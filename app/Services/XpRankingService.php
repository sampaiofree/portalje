<?php

namespace App\Services;

use App\Models\JourneyStep;
use App\Models\User;
use App\Models\XpRankingSnapshot;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class XpRankingService
{
    public const MAX_ROWS = 100;

    public const PAGE_SIZE = 10;

    private const CHUNK_SIZE = 500;

    public function __construct(private JourneyStepEvaluator $evaluator) {}

    public function publishedDataset(): array
    {
        $snapshot = XpRankingSnapshot::query()->find(1);

        return [
            'rows' => $snapshot?->rows ?? [],
            'totals' => $snapshot?->totals ?? ['participants' => 0, 'xp_total' => 0, 'xp_average' => 0],
            'generated_at' => $snapshot?->generated_at?->toIso8601String(),
        ];
    }

    public function rebuild(callable $assertLockOwned): array
    {
        $startedAt = now();
        foreach (['users', 'codigo_ref', 'purchase_events', 'whatsapp_atendimento', 'journey_steps', 'xp_ranking_snapshots'] as $table) {
            if (! Schema::hasTable($table)) {
                throw new RuntimeException("Tabela obrigatória ausente: {$table}");
            }
        }

        $steps = JourneyStep::query()->where('is_active', true)
            ->get(['id', 'completion_rule', 'goal_value', 'xp']);
        $rows = [];
        $participants = $xpTotal = $processed = 0;

        User::query()->select(['id', 'name', 'apelido', 'dominio', 'dominio_externo', 'whatsapp_atendimento'])
            ->chunkById(self::CHUNK_SIZE, function (Collection $users) use ($steps, $assertLockOwned, &$rows, &$participants, &$xpTotal, &$processed) {
                $assertLockOwned();
                $ids = $users->modelKeys();
                $products = DB::table('codigo_ref')->whereIn('user_id', $ids)->where('mostrar_curso', true)
                    ->groupBy('user_id')->pluck('user_id')->flip();
                $whatsapps = DB::table('whatsapp_atendimento')->whereIn('user_id', $ids)->where('is_active', true)
                    ->groupBy('user_id')->pluck('user_id')->flip();
                $leads = DB::table('codigo_ref')->whereIn('user_id', $ids)
                    ->whereExists(function ($query) {
                        $query->selectRaw('1')->from('purchase_events')
                            ->whereColumn('purchase_events.affiliate_code', 'codigo_ref.codigo_ref');
                    })->get(['user_id', 'codigo_ref'])
                    ->filter(fn ($ref) => filled($ref->codigo_ref))->pluck('user_id')->flip();

                // Match User::purchaseEvents(), including the existing join semantics
                // when more than one course uses the same affiliate code.
                $sales = DB::table('codigo_ref')
                    ->join('purchase_events', 'purchase_events.affiliate_code', '=', 'codigo_ref.codigo_ref')
                    ->whereIn('codigo_ref.user_id', $ids)
                    ->whereIn('purchase_events.purchase_status', ['APPROVED', 'COMPLETED'])
                    ->groupBy('codigo_ref.user_id')
                    ->selectRaw('codigo_ref.user_id, COUNT(*) AS sales_count, SUM(purchase_events.purchase_original_offer_price_value) AS sales_total')
                    ->get()->keyBy('user_id');

                foreach ($users as $user) {
                    $processed++;
                    $count = (int) ($sales[$user->id]->sales_count ?? 0);
                    $metrics = [
                        JourneyStep::COMPLETION_RULE_DOMAIN_CONFIGURED => (bool) ($user->dominio || $user->dominio_externo),
                        JourneyStep::COMPLETION_RULE_WHATSAPP_CONFIGURED => $whatsapps->has($user->id) || trim((string) $user->whatsapp_atendimento) !== '',
                        JourneyStep::COMPLETION_RULE_PRODUCT_ACTIVATED => $products->has($user->id),
                        JourneyStep::COMPLETION_RULE_FIRST_LEAD => $leads->has($user->id),
                        JourneyStep::COMPLETION_RULE_FIRST_SALE => $count >= 1,
                        JourneyStep::COMPLETION_RULE_SALES_COUNT_AT_LEAST => $count,
                        JourneyStep::COMPLETION_RULE_SALES_TOTAL_AT_LEAST => (float) ($sales[$user->id]->sales_total ?? 0),
                    ];
                    $xp = 0;
                    foreach ($steps as $step) {
                        if ($this->evaluator->isComplete($step, $metrics)) {
                            $xp += (int) $step->xp;
                        }
                    }
                    if ($xp <= 0) {
                        continue;
                    }

                    $participants++;
                    $xpTotal += $xp;
                    $candidate = ['user_id' => $user->id, 'display_name' => $this->displayName($user), 'xp_total' => $xp];
                    // Keep a sorted, bounded top 100 rather than all participants.
                    $index = 0;
                    while ($index < count($rows) && $this->compare($rows[$index], $candidate) <= 0) {
                        $index++;
                    }
                    if ($index < self::MAX_ROWS) {
                        array_splice($rows, $index, 0, [$candidate]);
                        if (count($rows) > self::MAX_ROWS) {
                            array_pop($rows);
                        }
                    }
                }
            });

        foreach ($rows as $index => &$row) {
            $row['position'] = $index + 1;
        }
        unset($row);
        $totals = [
            'participants' => $participants,
            'xp_total' => $xpTotal,
            'xp_average' => $participants > 0 ? (int) round($xpTotal / $participants) : 0,
        ];

        $generatedAt = DB::transaction(function () use ($rows, $totals, $startedAt, $assertLockOwned) {
            $snapshot = XpRankingSnapshot::query()->lockForUpdate()->findOrFail(1);
            // Check after obtaining the DB row lock, so an expired Redis lease
            // cannot let an older process overwrite a newer publication.
            $assertLockOwned();
            $snapshot->forceFill([
                'rows' => $rows, 'totals' => $totals,
                'started_at' => $startedAt, 'generated_at' => now(),
            ])->save();

            return $snapshot->generated_at->toIso8601String();
        });

        return ['users_processed' => $processed, 'generated_at' => $generatedAt] + $totals;
    }

    private function compare(array $a, array $b): int
    {
        return ($b['xp_total'] <=> $a['xp_total'])
            ?: strcasecmp($a['display_name'], $b['display_name'])
            ?: ($a['user_id'] <=> $b['user_id']);
    }

    private function displayName(User $user): string
    {
        $nickname = trim((string) $user->apelido);
        if ($nickname !== '') {
            return $nickname;
        }
        $name = trim((string) $user->name);

        return $name === '' ? 'Sem nome' : preg_split('/\s+/', $name)[0];
    }
}
