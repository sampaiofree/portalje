<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminAffiliateHealthService
{
    public function buildSnapshot(Builder $baseQuery, array $filters): array
    {
        $diasSemLead = $this->sanitizeDays($filters['dias_sem_lead'] ?? 7);
        $diasSemVenda = $this->sanitizeDays($filters['dias_sem_venda'] ?? 7);

        $users = (clone $baseQuery)
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.telefone_pessoal_1',
                'users.telefone_pessoal_2',
                'users.whatsapp_atendimento',
                'users.dominio',
                'users.dominio_externo',
                'users.created_at',
            ])
            ->orderByDesc('users.created_at')
            ->get();

        $userIds = $users->pluck('id')->map(fn ($id) => (int) $id)->all();

        $codigoRefCounts = $this->codigoRefCountsByUser($userIds);
        $whatsappCounts = $this->whatsappCountsByUser($userIds);
        $whatsappPhones = $this->firstWhatsappPhoneByUser($userIds);
        $leadStats = $this->leadStatsByUser($userIds);

        $now = Carbon::now();
        $usersRows = collect();
        $queueSetupRows = collect();
        $queueLeadRows = collect();
        $leadDurations = [];
        $saleDurations = [];

        $setupCompleto = 0;
        $comLead = 0;
        $comVenda = 0;

        foreach ($users as $user) {
            $userId = (int) $user->id;
            $createdAt = $user->created_at ? Carbon::parse($user->created_at) : null;
            $leadStat = $leadStats[$userId] ?? [
                'total_leads' => 0,
                'total_vendas' => 0,
                'first_lead_at' => null,
                'first_sale_at' => null,
            ];

            $firstLeadAt = !empty($leadStat['first_lead_at']) ? Carbon::parse($leadStat['first_lead_at']) : null;
            $firstSaleAt = !empty($leadStat['first_sale_at']) ? Carbon::parse($leadStat['first_sale_at']) : null;

            $hasDomain = $this->filled($user->dominio) || $this->filled($user->dominio_externo);
            $hasProduct = (int) ($codigoRefCounts[$userId] ?? 0) > 0;
            $hasWhatsapp = $this->filled($user->whatsapp_atendimento) || (int) ($whatsappCounts[$userId] ?? 0) > 0;
            $hasLead = (int) ($leadStat['total_leads'] ?? 0) > 0;
            $hasVenda = (int) ($leadStat['total_vendas'] ?? 0) > 0;

            $isSetupCompleto = $hasDomain && $hasProduct && $hasWhatsapp;

            if ($isSetupCompleto) {
                $setupCompleto++;
            }
            if ($hasLead) {
                $comLead++;
            }
            if ($hasVenda) {
                $comVenda++;
            }

            $diasDesdeCadastro = $createdAt ? (int) floor($createdAt->diffInDays($now)) : null;
            $diasDesdePrimeiroLead = $firstLeadAt ? (int) floor($firstLeadAt->diffInDays($now)) : null;
            $diasAtePrimeiroLead = ($createdAt && $firstLeadAt) ? (int) floor($createdAt->diffInDays($firstLeadAt)) : null;
            $diasAtePrimeiraVenda = ($createdAt && $firstSaleAt) ? (int) floor($createdAt->diffInDays($firstSaleAt)) : null;

            if ($diasAtePrimeiroLead !== null) {
                $leadDurations[] = $diasAtePrimeiroLead;
            }

            if ($diasAtePrimeiraVenda !== null) {
                $saleDurations[] = $diasAtePrimeiraVenda;
            }

            $row = [
                'id' => $userId,
                'name' => (string) ($user->name ?? ''),
                'email' => (string) ($user->email ?? ''),
                'telefone_contato' => $this->resolveContatoTelefone((string) ($user->telefone_pessoal_1 ?? ''), (string) ($user->telefone_pessoal_2 ?? '')),
                'telefone_atendimento' => $this->resolveAtendimentoTelefone(
                    (string) ($user->whatsapp_atendimento ?? ''),
                    (string) ($whatsappPhones[$userId] ?? '')
                ),
                'data_cadastro' => $createdAt ? $createdAt->format('Y-m-d') : '',
                'dias_desde_cadastro' => $diasDesdeCadastro,
                'total_leads' => (int) ($leadStat['total_leads'] ?? 0),
                'total_vendas' => (int) ($leadStat['total_vendas'] ?? 0),
                'first_lead_at' => $firstLeadAt ? $firstLeadAt->format('Y-m-d H:i:s') : null,
                'first_sale_at' => $firstSaleAt ? $firstSaleAt->format('Y-m-d H:i:s') : null,
                'dias_desde_primeiro_lead' => $diasDesdePrimeiroLead,
                'has_setup_completo' => $isSetupCompleto,
                'has_lead' => $hasLead,
                'has_venda' => $hasVenda,
            ];

            $usersRows->push($row);

            if ($isSetupCompleto && !$hasLead && $diasDesdeCadastro !== null && $diasDesdeCadastro >= $diasSemLead) {
                $queueSetupRows->push(array_merge($row, ['fila' => 'setup_sem_lead']));
            }

            if ($hasLead && !$hasVenda && $diasDesdePrimeiroLead !== null && $diasDesdePrimeiroLead >= $diasSemVenda) {
                $queueLeadRows->push(array_merge($row, ['fila' => 'lead_sem_venda']));
            }
        }

        $afiliadosFiltrados = $usersRows->count();
        $healthMetrics = [
            'afiliados_filtrados' => $afiliadosFiltrados,
            'setup_completo' => $setupCompleto,
            'com_lead' => $comLead,
            'com_venda' => $comVenda,
            'taxa_setup' => $this->percent($setupCompleto, $afiliadosFiltrados),
            'taxa_lead' => $this->percent($comLead, $afiliadosFiltrados),
            'taxa_venda' => $this->percent($comVenda, $afiliadosFiltrados),
            'taxa_lead_para_venda' => $this->percent($comVenda, $comLead),
            'mediana_dias_primeiro_lead' => $this->median($leadDurations),
            'mediana_dias_primeira_venda' => $this->median($saleDurations),
        ];

        return [
            'health_metrics' => $healthMetrics,
            'users_rows' => $usersRows,
            'queue_setup_rows' => $queueSetupRows->sortByDesc('dias_desde_cadastro')->values(),
            'queue_lead_rows' => $queueLeadRows->sortByDesc('dias_desde_primeiro_lead')->values(),
        ];
    }

    private function codigoRefCountsByUser(array $userIds): array
    {
        if (empty($userIds) || !Schema::hasTable('codigo_ref')) {
            return [];
        }

        return DB::table('codigo_ref')
            ->whereIn('user_id', $userIds)
            ->whereNotNull('codigo_ref')
            ->whereRaw("TRIM(codigo_ref) <> ''")
            ->groupBy('user_id')
            ->selectRaw('user_id, COUNT(*) as total')
            ->pluck('total', 'user_id')
            ->map(fn ($value) => (int) $value)
            ->toArray();
    }

    private function whatsappCountsByUser(array $userIds): array
    {
        if (empty($userIds) || !Schema::hasTable('whatsapp_atendimento')) {
            return [];
        }

        return DB::table('whatsapp_atendimento')
            ->whereIn('user_id', $userIds)
            ->groupBy('user_id')
            ->selectRaw('user_id, COUNT(*) as total')
            ->pluck('total', 'user_id')
            ->map(fn ($value) => (int) $value)
            ->toArray();
    }

    private function firstWhatsappPhoneByUser(array $userIds): array
    {
        if (empty($userIds) || !Schema::hasTable('whatsapp_atendimento')) {
            return [];
        }

        $rows = DB::table('whatsapp_atendimento')
            ->whereIn('user_id', $userIds)
            ->orderBy('user_id')
            ->orderByDesc('is_active')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get(['user_id', 'whatsapp']);

        $phonesByUser = [];
        foreach ($rows as $row) {
            $userId = (int) ($row->user_id ?? 0);
            if ($userId <= 0 || array_key_exists($userId, $phonesByUser)) {
                continue;
            }

            $phone = trim((string) ($row->whatsapp ?? ''));
            $phonesByUser[$userId] = $phone;
        }

        return $phonesByUser;
    }

    private function leadStatsByUser(array $userIds): array
    {
        if (empty($userIds) || !Schema::hasTable('codigo_ref') || !Schema::hasTable('purchase_events')) {
            return [];
        }

        return DB::table('codigo_ref as cr')
            ->join('purchase_events as pe', 'pe.affiliate_code', '=', 'cr.codigo_ref')
            ->whereIn('cr.user_id', $userIds)
            ->whereNotNull('cr.codigo_ref')
            ->whereRaw("TRIM(cr.codigo_ref) <> ''")
            ->groupBy('cr.user_id')
            ->selectRaw('cr.user_id as user_id')
            ->selectRaw('COUNT(*) as total_leads')
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(pe.purchase_status, ''))) IN ('APPROVED', 'COMPLETED') THEN 1 ELSE 0 END) as total_vendas")
            ->selectRaw('MIN(pe.created_at) as first_lead_at')
            ->selectRaw("MIN(CASE WHEN UPPER(TRIM(COALESCE(pe.purchase_status, ''))) IN ('APPROVED', 'COMPLETED') THEN pe.created_at ELSE NULL END) as first_sale_at")
            ->get()
            ->mapWithKeys(function ($row) {
                return [
                    (int) $row->user_id => [
                        'total_leads' => (int) ($row->total_leads ?? 0),
                        'total_vendas' => (int) ($row->total_vendas ?? 0),
                        'first_lead_at' => $row->first_lead_at,
                        'first_sale_at' => $row->first_sale_at,
                    ],
                ];
            })
            ->toArray();
    }

    private function resolveContatoTelefone(string $telefone1, string $telefone2): string
    {
        $telefone1 = trim($telefone1);
        if ($telefone1 !== '') {
            return $telefone1;
        }

        return trim($telefone2);
    }

    private function resolveAtendimentoTelefone(string $legacy, ?string $fallback = null): string
    {
        $legacy = trim($legacy);
        if ($legacy !== '') {
            return $legacy;
        }

        return trim((string) $fallback);
    }

    private function sanitizeDays($value): int
    {
        $days = (int) $value;
        if ($days < 0) {
            return 0;
        }

        if ($days > 3650) {
            return 3650;
        }

        return $days;
    }

    private function filled($value): bool
    {
        return trim((string) $value) !== '';
    }

    private function percent(int $numerator, int $denominator): float
    {
        if ($denominator <= 0) {
            return 0.0;
        }

        return round(($numerator / $denominator) * 100, 2);
    }

    /**
     * @param array<int, int|float> $values
     */
    private function median(array $values): ?float
    {
        if (empty($values)) {
            return null;
        }

        sort($values);
        $count = count($values);
        $middle = (int) floor(($count - 1) / 2);

        if ($count % 2 !== 0) {
            return (float) $values[$middle];
        }

        return round((((float) $values[$middle]) + ((float) $values[$middle + 1])) / 2, 1);
    }
}
