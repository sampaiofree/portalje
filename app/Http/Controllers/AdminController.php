<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Curso;
use App\Models\PurchaseEvent;
use App\Services\AdminAffiliateHealthService;

class AdminController extends Controller
{

    public function dashboard(Request $request, AdminAffiliateHealthService $healthService)
    {
        $filters = $this->parseDashboardFilters($request);
        $baseQuery = $this->buildDashboardUsersQuery($filters);
        $metrics = $this->calculateDashboardMetrics($baseQuery);
        $healthSnapshot = $healthService->buildSnapshot($baseQuery, $filters);

        $usuarios = (clone $baseQuery)
            ->select('users.*')
            ->with(['whatsappAtendimentos' => function ($query) {
                $query->orderByDesc('is_active')->orderByDesc('updated_at');
            }])
            ->orderByDesc('users.created_at')
            ->paginate(50)
            ->appends($request->query());

        $queueSetupRows = collect($healthSnapshot['queue_setup_rows'] ?? []);
        $queueLeadRows = collect($healthSnapshot['queue_lead_rows'] ?? []);

        $queueSetupPaginator = $this->paginateCollection(
            $queueSetupRows,
            20,
            $request,
            'setup_page'
        );

        $queueLeadPaginator = $this->paginateCollection(
            $queueLeadRows,
            20,
            $request,
            'lead_page'
        );

        $series = $this->affiliateMonthlyRegistrationsLastFiveMonths();
        $meses = [];
        $totalCadastros = [];
        $totalComDominio = [];

        foreach ($series as $row) {
            $meses[] = sprintf('%02d/%04d', (int) $row['month'], (int) $row['year']);
            $totalCadastros[] = (int) $row['total'];
            $totalComDominio[] = (int) $row['total_with_dominio'];
        }

        $dailySeries = $this->affiliateDailyRegistrationsForDateRange(
            (string) ($filters['date_start'] ?? ''),
            (string) ($filters['date_end'] ?? '')
        );

        $dailyLabels = [];
        $dailyCadastros = [];
        foreach ($dailySeries as $row) {
            $dailyLabels[] = (string) $row['date'];
            $dailyCadastros[] = (int) $row['total'];
        }

        $isPeriodAll = ($filters['period_scope'] ?? 'last_90_days') === 'all';
        $showDailyChart = !$isPeriodAll && ($filters['date_start'] ?? '') !== '' && ($filters['date_end'] ?? '') !== '';
        $dailyChartMessage = $isPeriodAll
            ? 'Selecione um intervalo de datas para visualizar os cadastros por dia.'
            : 'Sem dados de cadastros para o intervalo selecionado.';

        return view('adm.dashboard_adm', [
            'filters' => $filters,
            'metrics' => $metrics,
            'usuarios' => $usuarios,
            'healthMetrics' => $healthSnapshot['health_metrics'] ?? [],
            'queueSetupRows' => $queueSetupPaginator,
            'queueLeadRows' => $queueLeadPaginator,
            'queueCounts' => [
                'setup_sem_lead' => $queueSetupRows->count(),
                'lead_sem_venda' => $queueLeadRows->count(),
            ],
            'meses' => $meses,
            'totalCadastros' => $totalCadastros,
            'totalComDominio' => $totalComDominio,
            'dailyLabels' => $dailyLabels,
            'dailyCadastros' => $dailyCadastros,
            'showDailyChart' => $showDailyChart,
            'dailyChartMessage' => $dailyChartMessage,
        ]);
    }

    public function dashboard_export_csv(Request $request, AdminAffiliateHealthService $healthService)
    {
        $filters = $this->parseDashboardFilters($request);
        $baseQuery = $this->buildDashboardUsersQuery($filters);
        $healthSnapshot = $healthService->buildSnapshot($baseQuery, $filters);

        $exportRows = $this->buildDashboardExportRows($healthSnapshot);

        $fileName = 'dashboard_usuarios_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($exportRows) {
            $output = fopen('php://output', 'w');
            if ($output === false) {
                return;
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, [
                'Nome',
                'Primeiro nome',
                'Sobrenome',
                'Telefone de contato',
                'Email',
                'Telefone de atendimento',
                'Data cadastro',
                'Dias desde cadastro',
                'Total leads',
                'Total vendas',
                'Fila',
            ], ';');

            foreach ($exportRows as $row) {
                fputcsv($output, [
                    (string) ($row['name'] ?? ''),
                    (string) ($row['primeiro_nome'] ?? ''),
                    (string) ($row['sobrenome'] ?? ''),
                    (string) ($row['telefone_contato'] ?? ''),
                    (string) ($row['email'] ?? ''),
                    (string) ($row['telefone_atendimento'] ?? ''),
                    (string) ($row['data_cadastro'] ?? ''),
                    $this->normalizeCsvNumericValue($row['dias_desde_cadastro'] ?? null),
                    $this->normalizeCsvNumericValue($row['total_leads'] ?? 0),
                    $this->normalizeCsvNumericValue($row['total_vendas'] ?? 0),
                    (string) ($row['fila'] ?? ''),
                ], ';');
            }

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    private function dashboardFilterDefaults(): array
    {
        return [
            'period_scope' => 'last_90_days',
            'date_start' => '',
            'date_end' => '',
            'tem_dominio' => 'all',
            'tem_lead' => 'all',
            'tem_venda' => 'all',
            'tem_produto' => 'all',
            'tem_whatsapp' => 'all',
            'dias_sem_lead' => 7,
            'dias_sem_venda' => 7,
            'fila' => 'all',
        ];
    }

    private function parseDashboardFilters(Request $request): array
    {
        $filters = $this->dashboardFilterDefaults();

        $periodScope = strtolower(trim((string) $request->query('period_scope', 'last_90_days')));
        if (!in_array($periodScope, ['last_90_days', 'all'], true)) {
            $periodScope = 'last_90_days';
        }
        $filters['period_scope'] = $periodScope;

        $dateStart = trim((string) $request->query('date_start', ''));
        $dateEnd = trim((string) $request->query('date_end', ''));

        if ($periodScope === 'all') {
            $filters['date_start'] = '';
            $filters['date_end'] = '';
        } else {
            $parsedDateStart = $this->isValidDate($dateStart) ? $dateStart : '';
            $parsedDateEnd = $this->isValidDate($dateEnd) ? $dateEnd : '';

            if ($parsedDateStart === '' && $parsedDateEnd === '') {
                $filters['date_start'] = now()->subDays(90)->toDateString();
                $filters['date_end'] = now()->toDateString();
            } else {
                $filters['date_start'] = $parsedDateStart;
                $filters['date_end'] = $parsedDateEnd;
            }

            if ($filters['date_start'] !== '' && $filters['date_end'] !== '' && $filters['date_start'] > $filters['date_end']) {
                $tmp = $filters['date_start'];
                $filters['date_start'] = $filters['date_end'];
                $filters['date_end'] = $tmp;
            }
        }

        foreach (['tem_dominio', 'tem_lead', 'tem_venda', 'tem_produto', 'tem_whatsapp'] as $field) {
            $value = strtolower(trim((string) $request->query($field, 'all')));
            $filters[$field] = in_array($value, ['all', 'yes', 'no'], true) ? $value : 'all';
        }

        $filters['dias_sem_lead'] = $this->sanitizeDaysFilter($request->query('dias_sem_lead', 7));
        $filters['dias_sem_venda'] = $this->sanitizeDaysFilter($request->query('dias_sem_venda', 7));

        $fila = strtolower(trim((string) $request->query('fila', 'all')));
        $filters['fila'] = in_array($fila, ['all', 'setup_sem_lead', 'lead_sem_venda'], true) ? $fila : 'all';

        return $filters;
    }

    private function isValidDate(string $value): bool
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return false;
        }

        $date = \DateTime::createFromFormat('Y-m-d', $value);
        return $date instanceof \DateTime && $date->format('Y-m-d') === $value;
    }

    private function sanitizeDaysFilter($value): int
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

    private function paginateCollection(Collection $rows, int $perPage, Request $request, string $pageName): LengthAwarePaginator
    {
        $page = max((int) $request->query($pageName, 1), 1);
        $offset = ($page - 1) * $perPage;
        $items = $rows->slice($offset, $perPage)->values();

        return new LengthAwarePaginator(
            $items,
            $rows->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => $pageName,
                'query' => $request->query(),
            ]
        );
    }

    private function buildDashboardExportRows(array $healthSnapshot): Collection
    {
        $queueMembershipByUserId = [];

        foreach (collect($healthSnapshot['queue_setup_rows'] ?? []) as $row) {
            $userId = (int) ($row['id'] ?? 0);
            if ($userId <= 0) {
                continue;
            }

            $queueMembershipByUserId[$userId][] = 'setup_sem_lead';
        }

        foreach (collect($healthSnapshot['queue_lead_rows'] ?? []) as $row) {
            $userId = (int) ($row['id'] ?? 0);
            if ($userId <= 0) {
                continue;
            }

            $queueMembershipByUserId[$userId][] = 'lead_sem_venda';
        }

        return collect($healthSnapshot['users_rows'] ?? [])
            ->map(function ($row) use ($queueMembershipByUserId) {
                $userId = (int) ($row['id'] ?? 0);
                [$primeiroNome, $sobrenome] = $this->splitDashboardUserName((string) ($row['name'] ?? ''));

                $row['primeiro_nome'] = $primeiroNome;
                $row['sobrenome'] = $sobrenome;
                $row['fila'] = implode(',', $queueMembershipByUserId[$userId] ?? []);

                return $row;
            })
            ->values();
    }

    private function splitDashboardUserName(string $name): array
    {
        $name = trim($name);
        if ($name === '') {
            return ['', ''];
        }

        $parts = preg_split('/\s+/', $name) ?: [];
        if ($parts === []) {
            return ['', ''];
        }

        $primeiroNome = (string) array_shift($parts);

        return [$primeiroNome, implode(' ', $parts)];
    }

    private function normalizeCsvNumericValue($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return (string) ((int) $value);
    }

    private function buildDashboardUsersQuery(array $filters): Builder
    {
        $query = User::query()
            ->where('users.nivel_acesso', User::NIVEL_ACESSO_USER);

        if (($filters['date_start'] ?? '') !== '') {
            $query->whereDate('users.created_at', '>=', $filters['date_start']);
        }

        if (($filters['date_end'] ?? '') !== '') {
            $query->whereDate('users.created_at', '<=', $filters['date_end']);
        }

        $this->applyTriStateFilter(
            $query,
            $filters['tem_dominio'] ?? 'all',
            fn (Builder $q) => $this->applyHasDomainCondition($q, true),
            fn (Builder $q) => $this->applyHasDomainCondition($q, false)
        );

        $this->applyTriStateFilter(
            $query,
            $filters['tem_lead'] ?? 'all',
            fn (Builder $q) => $this->applyHasLeadCondition($q, true),
            fn (Builder $q) => $this->applyHasLeadCondition($q, false)
        );

        $this->applyTriStateFilter(
            $query,
            $filters['tem_venda'] ?? 'all',
            fn (Builder $q) => $this->applyHasSaleLeadCondition($q, true),
            fn (Builder $q) => $this->applyHasSaleLeadCondition($q, false)
        );

        $this->applyTriStateFilter(
            $query,
            $filters['tem_produto'] ?? 'all',
            fn (Builder $q) => $this->applyHasProductCondition($q, true),
            fn (Builder $q) => $this->applyHasProductCondition($q, false)
        );

        $this->applyTriStateFilter(
            $query,
            $filters['tem_whatsapp'] ?? 'all',
            fn (Builder $q) => $this->applyHasWhatsappCondition($q, true),
            fn (Builder $q) => $this->applyHasWhatsappCondition($q, false)
        );

        return $query;
    }

    private function applyTriStateFilter(Builder $query, string $value, callable $yesCallback, callable $noCallback): void
    {
        if ($value === 'yes') {
            $yesCallback($query);
            return;
        }

        if ($value === 'no') {
            $noCallback($query);
        }
    }

    private function calculateDashboardMetrics(Builder $baseQuery): array
    {
        $hasCodigoRef = Schema::hasTable('codigo_ref');
        $hasPurchaseEvents = Schema::hasTable('purchase_events');
        $hasWhatsappTable = Schema::hasTable('whatsapp_atendimento');

        $domainCondition = "((users.dominio IS NOT NULL AND TRIM(users.dominio) <> '') OR (users.dominio_externo IS NOT NULL AND TRIM(users.dominio_externo) <> ''))";

        $productExistsCondition = $hasCodigoRef
            ? "EXISTS (SELECT 1 FROM codigo_ref cr WHERE cr.user_id = users.id)"
            : '0 = 1';

        $leadExistsCondition = ($hasCodigoRef && $hasPurchaseEvents)
            ? "EXISTS (SELECT 1 FROM codigo_ref cr JOIN purchase_events pe ON pe.affiliate_code = cr.codigo_ref WHERE cr.user_id = users.id AND cr.codigo_ref IS NOT NULL AND TRIM(cr.codigo_ref) <> '')"
            : '0 = 1';

        $saleLeadExistsCondition = ($hasCodigoRef && $hasPurchaseEvents)
            ? "EXISTS (SELECT 1 FROM codigo_ref cr JOIN purchase_events pe ON pe.affiliate_code = cr.codigo_ref WHERE cr.user_id = users.id AND cr.codigo_ref IS NOT NULL AND TRIM(cr.codigo_ref) <> '' AND UPPER(TRIM(COALESCE(pe.purchase_status, ''))) IN ('APPROVED', 'COMPLETED'))"
            : '0 = 1';

        $whatsappCondition = $hasWhatsappTable
            ? "((users.whatsapp_atendimento IS NOT NULL AND TRIM(users.whatsapp_atendimento) <> '') OR EXISTS (SELECT 1 FROM whatsapp_atendimento wa WHERE wa.user_id = users.id))"
            : "(users.whatsapp_atendimento IS NOT NULL AND TRIM(users.whatsapp_atendimento) <> '')";

        $row = (clone $baseQuery)
            ->selectRaw('COUNT(users.id) as total_cadastros')
            ->selectRaw("SUM(CASE WHEN {$domainCondition} THEN 1 ELSE 0 END) as com_dominio")
            ->selectRaw("SUM(CASE WHEN {$leadExistsCondition} THEN 1 ELSE 0 END) as com_lead")
            ->selectRaw("SUM(CASE WHEN {$saleLeadExistsCondition} THEN 1 ELSE 0 END) as com_lead_venda")
            ->selectRaw("SUM(CASE WHEN {$productExistsCondition} THEN 1 ELSE 0 END) as com_produto")
            ->selectRaw("SUM(CASE WHEN {$whatsappCondition} THEN 1 ELSE 0 END) as com_whatsapp")
            ->first();

        return [
            'total_cadastros' => (int) ($row->total_cadastros ?? 0),
            'com_dominio' => (int) ($row->com_dominio ?? 0),
            'com_lead' => (int) ($row->com_lead ?? 0),
            'com_lead_venda' => (int) ($row->com_lead_venda ?? 0),
            'com_produto' => (int) ($row->com_produto ?? 0),
            'com_whatsapp' => (int) ($row->com_whatsapp ?? 0),
        ];
    }

    private function applyHasDomainCondition(Builder $query, bool $has): void
    {
        if ($has) {
            $query->where(function (Builder $subQuery) {
                $subQuery
                    ->where(function (Builder $domainQuery) {
                        $domainQuery
                            ->whereNotNull('users.dominio')
                            ->whereRaw("TRIM(users.dominio) <> ''");
                    })
                    ->orWhere(function (Builder $externalQuery) {
                        $externalQuery
                            ->whereNotNull('users.dominio_externo')
                            ->whereRaw("TRIM(users.dominio_externo) <> ''");
                    });
            });

            return;
        }

        $query->where(function (Builder $subQuery) {
            $subQuery
                ->where(function (Builder $domainQuery) {
                    $domainQuery
                        ->whereNull('users.dominio')
                        ->orWhereRaw("TRIM(users.dominio) = ''");
                })
                ->where(function (Builder $externalQuery) {
                    $externalQuery
                        ->whereNull('users.dominio_externo')
                        ->orWhereRaw("TRIM(users.dominio_externo) = ''");
                });
        });
    }

    private function applyHasProductCondition(Builder $query, bool $has): void
    {
        if (!Schema::hasTable('codigo_ref')) {
            if ($has) {
                $query->whereRaw('1 = 0');
            }
            return;
        }

        $method = $has ? 'whereExists' : 'whereNotExists';
        $query->{$method}(function ($subQuery) {
            $subQuery
                ->selectRaw('1')
                ->from('codigo_ref as cr')
                ->whereColumn('cr.user_id', 'users.id');
        });
    }

    private function applyHasLeadCondition(Builder $query, bool $has): void
    {
        if (!Schema::hasTable('codigo_ref') || !Schema::hasTable('purchase_events')) {
            if ($has) {
                $query->whereRaw('1 = 0');
            }
            return;
        }

        $method = $has ? 'whereExists' : 'whereNotExists';
        $query->{$method}(function ($subQuery) {
            $subQuery
                ->selectRaw('1')
                ->from('codigo_ref as cr')
                ->join('purchase_events as pe', 'pe.affiliate_code', '=', 'cr.codigo_ref')
                ->whereColumn('cr.user_id', 'users.id')
                ->whereNotNull('cr.codigo_ref')
                ->whereRaw("TRIM(cr.codigo_ref) <> ''");
        });
    }

    private function applyHasSaleLeadCondition(Builder $query, bool $has): void
    {
        if (!Schema::hasTable('codigo_ref') || !Schema::hasTable('purchase_events')) {
            if ($has) {
                $query->whereRaw('1 = 0');
            }
            return;
        }

        $method = $has ? 'whereExists' : 'whereNotExists';
        $query->{$method}(function ($subQuery) {
            $subQuery
                ->selectRaw('1')
                ->from('codigo_ref as cr')
                ->join('purchase_events as pe', 'pe.affiliate_code', '=', 'cr.codigo_ref')
                ->whereColumn('cr.user_id', 'users.id')
                ->whereNotNull('cr.codigo_ref')
                ->whereRaw("TRIM(cr.codigo_ref) <> ''")
                ->whereRaw("UPPER(TRIM(COALESCE(pe.purchase_status, ''))) IN ('APPROVED', 'COMPLETED')");
        });
    }

    private function applyHasWhatsappCondition(Builder $query, bool $has): void
    {
        $hasWhatsappTable = Schema::hasTable('whatsapp_atendimento');

        if ($has) {
            $query->where(function (Builder $subQuery) use ($hasWhatsappTable) {
                $subQuery->where(function (Builder $legacyQuery) {
                    $legacyQuery
                        ->whereNotNull('users.whatsapp_atendimento')
                        ->whereRaw("TRIM(users.whatsapp_atendimento) <> ''");
                });

                if ($hasWhatsappTable) {
                    $subQuery->orWhereExists(function ($existsQuery) {
                        $existsQuery
                            ->selectRaw('1')
                            ->from('whatsapp_atendimento as wa')
                            ->whereColumn('wa.user_id', 'users.id');
                    });
                }
            });

            return;
        }

        $query->where(function (Builder $subQuery) use ($hasWhatsappTable) {
            $subQuery->where(function (Builder $legacyQuery) {
                $legacyQuery
                    ->whereNull('users.whatsapp_atendimento')
                    ->orWhereRaw("TRIM(users.whatsapp_atendimento) = ''");
            });

            if ($hasWhatsappTable) {
                $subQuery->whereNotExists(function ($existsQuery) {
                    $existsQuery
                        ->selectRaw('1')
                        ->from('whatsapp_atendimento as wa')
                        ->whereColumn('wa.user_id', 'users.id');
                });
            }
        });
    }

    private function resolveContatoTelefone(User $user): string
    {
        $telefone = trim((string) ($user->telefone_pessoal_1 ?? ''));
        if ($telefone !== '') {
            return $telefone;
        }

        $fallback = trim((string) ($user->telefone_pessoal_2 ?? ''));
        return $fallback;
    }

    private function resolveAtendimentoTelefone(User $user): string
    {
        $legacy = trim((string) ($user->whatsapp_atendimento ?? ''));
        if ($legacy !== '') {
            return $legacy;
        }

        if (!$user->relationLoaded('whatsappAtendimentos')) {
            return '';
        }

        $registro = $user->whatsappAtendimentos->first();
        if (!$registro) {
            return '';
        }

        return trim((string) ($registro->whatsapp ?? ''));
    }

    private function affiliateMonthlyRegistrationsLastFiveMonths(): array
    {
        $now = now();
        $startDate = $now->copy()->subMonths(4)->startOfMonth();
        $endDate = $now->copy()->endOfMonth();

        $driver = DB::connection()->getDriverName();
        $yearExpression = $driver === 'sqlite'
            ? "CAST(strftime('%Y', users.created_at) AS INTEGER)"
            : "YEAR(users.created_at)";
        $monthExpression = $driver === 'sqlite'
            ? "CAST(strftime('%m', users.created_at) AS INTEGER)"
            : "MONTH(users.created_at)";

        return User::query()
            ->where('users.nivel_acesso', User::NIVEL_ACESSO_USER)
            ->whereBetween('users.created_at', [$startDate, $endDate])
            ->selectRaw("$yearExpression as year")
            ->selectRaw("$monthExpression as month")
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("COUNT(CASE WHEN ((users.dominio IS NOT NULL AND TRIM(users.dominio) <> '') OR (users.dominio_externo IS NOT NULL AND TRIM(users.dominio_externo) <> '')) THEN 1 END) as total_with_dominio")
            ->groupByRaw("$yearExpression, $monthExpression")
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($row) {
                return [
                    'year' => (int) $row->year,
                    'month' => (int) $row->month,
                    'total' => (int) $row->total,
                    'total_with_dominio' => (int) $row->total_with_dominio,
                ];
            })
            ->toArray();
    }

    private function affiliateDailyRegistrationsForDateRange(string $dateStart, string $dateEnd): array
    {
        if ($dateStart === '' || $dateEnd === '') {
            return [];
        }

        try {
            $start = Carbon::createFromFormat('Y-m-d', $dateStart)->startOfDay();
            $end = Carbon::createFromFormat('Y-m-d', $dateEnd)->endOfDay();
        } catch (\Throwable $e) {
            return [];
        }

        if ($start->greaterThan($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        $driver = DB::connection()->getDriverName();
        $dateExpression = $driver === 'sqlite'
            ? "strftime('%Y-%m-%d', users.created_at)"
            : "DATE(users.created_at)";

        $rows = User::query()
            ->where('users.nivel_acesso', User::NIVEL_ACESSO_USER)
            ->whereBetween('users.created_at', [$start, $end])
            ->selectRaw("$dateExpression as day_date")
            ->selectRaw('COUNT(*) as total')
            ->groupByRaw($dateExpression)
            ->orderBy('day_date')
            ->get();

        $totalsByDate = [];
        foreach ($rows as $row) {
            $dayDate = trim((string) ($row->day_date ?? ''));
            if ($dayDate === '') {
                continue;
            }
            $totalsByDate[$dayDate] = (int) ($row->total ?? 0);
        }

        $series = [];
        $cursor = $start->copy()->startOfDay();
        $lastDay = $end->copy()->startOfDay();

        while ($cursor->lessThanOrEqualTo($lastDay)) {
            $date = $cursor->format('Y-m-d');
            $series[] = [
                'date' => $date,
                'total' => (int) ($totalsByDate[$date] ?? 0),
            ];
            $cursor->addDay();
        }

        return $series;
    }

    public function logsIndex()
    {
        $logDirectory = storage_path('logs');
        $logFiles = [];

        if (is_dir($logDirectory)) {
            foreach (File::files($logDirectory) as $file) {
                if (!$file->isFile()) {
                    continue;
                }

                $sizeBytes = $file->getSize();
                $modifiedTimestamp = $file->getMTime();
                $logFiles[] = [
                    'name' => $file->getFilename(),
                    'size_bytes' => $sizeBytes,
                    'size_human' => $this->formatBytes($sizeBytes),
                    'modified_timestamp' => $modifiedTimestamp,
                    'modified_at' => date('d/m/Y H:i:s', $modifiedTimestamp),
                ];
            }
        }

        usort($logFiles, static function (array $a, array $b): int {
            return $b['modified_timestamp'] <=> $a['modified_timestamp'];
        });

        return view('adm.logs.index', compact('logFiles'));
    }

    public function logsView(string $logFile)
    {
        $path = $this->resolveLogFilePath($logFile);
        if (!$path) {
            abort(404);
        }

        $maxBytes = 500 * 1024;
        $fileSize = filesize($path) ?: 0;
        $isPartial = $fileSize > $maxBytes;
        $previewContent = $this->readLastBytesFromFile($path, $maxBytes);

        if ($isPartial) {
            $previewContent = "[Conteúdo parcial: exibindo os últimos 500KB]\n\n" . $previewContent;
        }

        return view('adm.logs.view', [
            'logFile' => basename($path),
            'previewContent' => $previewContent,
            'maxBytes' => $maxBytes,
            'fileSize' => $fileSize,
        ]);
    }

    public function logsDownload(string $logFile)
    {
        $path = $this->resolveLogFilePath($logFile);
        if (!$path) {
            abort(404);
        }

        return response()->download($path, basename($path));
    }

    public function adm_cursos_lista() 
    {
        $cursos = Curso::orderBy('ordem')->orderBy('id')->get();
        return view('adm.cursos.cursos_lista', compact('cursos'));
    }
    

    public function adm_editar_curso($id) {
        $curso = Curso::where('id', $id)->first();
        if($curso){
            return view('adm.cursos.editar_curso', compact('curso'));
        }
    }

    public function create()
    {
        $timestamp = now()->format('YmdHis');
        $tempUrl = 'curso-temp-' . $timestamp . '-' . substr((string) microtime(true), -6);

        $curso = Curso::create([
            'titulo' => 'Novo curso',
            'url' => $tempUrl,
        ]);

        // Obtém o ID do registro recém-criado
        $newCursoId = $curso->id;
        return redirect()->route('adm_editar_curso', ['id' => $newCursoId]);
    }

    public function adm_cursos_lista_editar(Request $request, $id=null){

        $dados = $request->except('_token', '_method');

        //echo json_encode($id);
        //exit;
        
        if(!$id){$id=$dados['id'];}

        // Encontre o curso pelo ID e atualize os dados
        $curso = Curso::find($id);

        if($dados['publicado']==='true'){$dados['publicado']=1;}else{$dados['publicado']=null;}
        if($dados['permitir_afiliacao']==='true'){$dados['permitir_afiliacao']=1;}else{$dados['permitir_afiliacao']=null;}
        if($dados['mostrar_na_pagina']==='true'){$dados['mostrar_na_pagina']=1;}else{$dados['mostrar_na_pagina']=null;}
        if($dados['gratuito']==='true'){$dados['gratuito']=1;}else{$dados['gratuito']=null;}

        if($curso->update($dados)) {
            return response()->json(['message' => json_encode($dados['gratuito'])]);
            //return response()->json(['message' => 'Curso atualizado com sucesso!']);
        } else {
            return response()->json(['message' => 'Erro na atualização! Contate o Dev'], 500);
        }

    }

    public function adm_editar_curso_post(Request $request, $id){

        $dados = $request->except('_token', '_method');

        // Encontrar o curso pelo ID e atualizar os dados
        $curso = Curso::find($id);

        if($curso){

            if ($request->file('capa_quadrada')) {
                $capa_quadrada = $request->file('capa_quadrada')->store('uploads/capa_cursos', 'public');
                $dados['capa_quadrada'] = $capa_quadrada;
                $this->deleteOldImage($curso->capa_quadrada);
            }
            if ($request->file('capa_vertical')) {
                
                $capa_vertical = $request->file('capa_vertical')->store('uploads/capa_cursos', 'public');
                $dados['capa_vertical'] = $capa_vertical;
                
                $this->deleteOldImage($curso->capa_vertical);
            }
            if ($request->file('capa_horizontal')) {
                $capa_horizontal = $request->file('capa_horizontal')->store('uploads/capa_cursos', 'public');
                $dados['capa_horizontal'] = $capa_horizontal;
                $this->deleteOldImage($curso->capa_horizontal);
            }

            if ($request->file('professor_foto')) {
                $professor_foto = $request->file('professor_foto')->store('uploads/capa_cursos', 'public');
                $dados['professor_foto'] = $professor_foto;
                if($curso->professor_foto){$this->deleteOldImage($curso->professor_foto);}
                
            }

            if(
                !$curso['url'] ||
                str_starts_with($curso['url'], 'curso-temp-')
            ){
                $dados['url'] = $this->createSlug($dados['titulo']);
                if(!$dados['url']){return redirect()->back()->with('error', 'Curso com mesmo nome já cadastrado');}
            }

            if(!isset($dados['publicado'])){$dados['publicado']=false;}
            if(!isset($dados['permitir_afiliacao'])){$dados['permitir_afiliacao']=false;}
            if(!isset($dados['mostrar_na_pagina'])){$dados['mostrar_na_pagina']=false;}
            if(!isset($dados['gratuito'])){$dados['gratuito']=false;}
            
            
            if ($curso->update($dados)) {
                return redirect()->back()->with('success', json_encode($dados['gratuito']));
                //return redirect()->back()->with('success', 'Curso atualizado com sucesso!');
            } else {
                return redirect()->back()->with('error', 'Erro na atualização! Contate o Dev');
            }

        }else{
            return redirect()->back()->with('error', 'Erro na atualização! Contate o Dev');
        }
    }

    private function deleteOldImage($imagePath)
    {
        if ($imagePath) {
            $fullImagePath = storage_path('app/public/' . $imagePath);
            if (is_file($fullImagePath) && file_exists($fullImagePath)) {
                unlink($fullImagePath);
            }
        }
    }


    public function createSlug($string) {
        // Converter para minúsculas
        $string = strtolower($string);

        // Substituir caracteres acentuados por suas versões não acentuadas
        $unwanted_array = array(
            'á'=>'a','à'=>'a','â'=>'a','ã'=>'a','ä'=>'a',
            'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
            'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i',
            'ó'=>'o','ò'=>'o','ô'=>'o','õ'=>'o','ö'=>'o',
            'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u',
            'ç'=>'c','ñ'=>'n',
            'Á'=>'a','À'=>'a','Â'=>'a','Ã'=>'a','Ä'=>'a',
            'É'=>'e','È'=>'e','Ê'=>'e','Ë'=>'e',
            'Í'=>'i','Ì'=>'i','Î'=>'i','Ï'=>'i',
            'Ó'=>'o','Ò'=>'o','Ô'=>'o','Õ'=>'o','Ö'=>'o',
            'Ú'=>'u','Ù'=>'u','Û'=>'u','Ü'=>'u',
            'Ç'=>'c','Ñ'=>'n'
        );
        $string = strtr($string, $unwanted_array);

        // Substituir qualquer caractere que não seja letra ou número por um espaço
        $string = preg_replace('/[^a-z0-9\s]/', '', $string);

        // Substituir múltiplos espaços por um único espaço
        $string = preg_replace('/\s+/', ' ', $string);

        // Substituir espaços por hífens
        $string = str_replace(' ', '-', $string);

        $curso = Curso::where('url', $string)->first();

        if($curso){
            return false;
        }else{
            return $string;
        }

        
    }

    public function leads_hotmart()
    {      
        // Pagina os resultados e mantém os parâmetros da query string
        $data = PurchaseEvent::orderBy('created_at', 'desc')->paginate(150);

        $hotmart_leads = $data;
        $titulo_pagina = "Todos os leads";
        // Retorna a view com os leads paginados
        
        return view('adm.leads.leads', compact('hotmart_leads', 'titulo_pagina'));
    }

    private function resolveLogFilePath(string $logFile): ?string
    {
        $safeName = basename($logFile);
        if ($safeName === '' || $safeName !== $logFile) {
            return null;
        }

        $path = storage_path('logs/' . $safeName);

        if (!is_file($path)) {
            return null;
        }

        return $path;
    }

    private function readLastBytesFromFile(string $path, int $maxBytes): string
    {
        $size = filesize($path);
        if ($size === false || $size <= 0) {
            return '';
        }

        $start = max(0, $size - $maxBytes);
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return '';
        }

        fseek($handle, $start);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $content !== false ? $content : '';
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        $units = ['KB', 'MB', 'GB', 'TB'];
        $size = $bytes / 1024;
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return number_format($size, 2, ',', '.') . ' ' . $units[$unitIndex];
    }

    public function purchase_events(Request $request)
    {
        $tagFieldLabels = $this->purchaseEventTagFieldLabels();
        $filters = $this->parsePurchaseEventFilters($request, $tagFieldLabels, true);

        $filters['per_page'] = min(max($filters['per_page'], 10), 200);

        if ($filters['date_start'] !== '' && $filters['date_end'] !== '' && $filters['date_start'] > $filters['date_end']) {
            $swap = $filters['date_start'];
            $filters['date_start'] = $filters['date_end'];
            $filters['date_end'] = $swap;
        }

        $statusOptions = [
            'WAITING_PAYMENT',
            'BILLET_PRINTED',
            'APPROVED',
            'COMPLETED',
            'CANCELED',
            'CANCELLED',
            'EXPIRED',
            'DELAYED',
        ];

        if (!Schema::hasTable('purchase_events')) {
            return view('dashboard.admin.purchase-events', [
                'purchaseEventsGrouped' => null,
                'filters' => $filters,
                'statusOptions' => $statusOptions,
                'tagFieldLabels' => $tagFieldLabels,
                'warningMessage' => 'A tabela purchase_events não existe neste ambiente.',
                'totalGroupedRecords' => 0,
            ]);
        }

        $baseQuery = PurchaseEvent::query()
            ->whereNotNull('buyer_checkout_phone')
            ->where('buyer_checkout_phone', '<>', '');

        $this->applyPurchaseEventFilters($baseQuery, $filters, $tagFieldLabels);

        $totalGroupedRecords = (clone $baseQuery)
            ->distinct('buyer_checkout_phone')
            ->count('buyer_checkout_phone');

        $purchaseEventsGrouped = (clone $baseQuery)
            ->selectRaw('MAX(buyer_name) as buyer_name')
            ->selectRaw('buyer_checkout_phone')
            ->selectRaw('COUNT(*) as total_events')
            ->selectRaw('COUNT(DISTINCT `transaction`) as total_transactions')
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(COALESCE(purchase_status, ''))) IN ('APPROVED', 'COMPLETED') THEN 1 ELSE 0 END) as total_approved")
            ->selectRaw('MAX(created_at) as last_event_at')
            ->groupBy('buyer_checkout_phone')
            ->orderByRaw('MAX(created_at) DESC')
            ->simplePaginate($filters['per_page'])
            ->appends($request->query());

        return view('dashboard.admin.purchase-events', [
            'purchaseEventsGrouped' => $purchaseEventsGrouped,
            'filters' => $filters,
            'statusOptions' => $statusOptions,
            'tagFieldLabels' => $tagFieldLabels,
            'warningMessage' => null,
            'totalGroupedRecords' => $totalGroupedRecords,
        ]);
    }

    public function purchase_events_suggestions(Request $request)
    {
        $tagFieldLabels = $this->purchaseEventTagFieldLabels();
        $field = (string) $request->query('field', '');
        $queryText = trim((string) $request->query('q', ''));

        if (!array_key_exists($field, $tagFieldLabels)) {
            return response()->json([]);
        }

        if (mb_strlen($queryText) < 2 || !Schema::hasTable('purchase_events')) {
            return response()->json([]);
        }
        $filters = $this->parsePurchaseEventFilters($request, $tagFieldLabels, false);

        $baseQuery = PurchaseEvent::query()
            ->whereNotNull('buyer_checkout_phone')
            ->where('buyer_checkout_phone', '<>', '');

        $this->applyPurchaseEventFilters($baseQuery, $filters, $tagFieldLabels, $field);

        $query = (clone $baseQuery)
            ->selectRaw($field)
            ->whereNotNull($field)
            ->where($field, '<>', '');

        if (in_array($field, ['event', 'purchase_status', 'purchase_payment_type'], true)) {
            $query->whereRaw("UPPER(COALESCE({$field}, '')) LIKE ?", ['%' . Str::upper($queryText) . '%']);
        } elseif ($field === 'purchase_full_price_value') {
            $query->whereRaw("CAST({$field} AS TEXT) LIKE ?", ['%' . $queryText . '%']);
        } else {
            $query->where($field, 'like', '%' . $queryText . '%');
        }

        $results = $query
            ->distinct()
            ->orderBy($field)
            ->limit(12)
            ->pluck($field)
            ->filter()
            ->values()
            ->all();

        return response()->json($results);
    }

    public function purchase_events_related(Request $request)
    {
        $phone = preg_replace('/\D/', '', (string) $request->query('phone', ''));

        if ($phone === '' || !Schema::hasTable('purchase_events')) {
            return response()->json(['records' => []]);
        }

        $records = PurchaseEvent::query()
            ->where('buyer_checkout_phone', $phone)
            ->orderByDesc('created_at')
            ->get([
                'created_at',
                'buyer_name',
                'product_name',
                'event',
                'purchase_full_price_value',
                'purchase_status',
                'purchase_payment_type',
                'affiliate_name',
                'affiliate_code',
                'transaction',
            ])
            ->map(function ($record) {
                return [
                    'created_at' => optional($record->created_at)->format('Y-m-d H:i:s'),
                    'buyer_name' => $record->buyer_name,
                    'product_name' => $record->product_name,
                    'event' => $record->event,
                    'purchase_full_price_value' => $record->purchase_full_price_value,
                    'purchase_status' => $record->purchase_status,
                    'purchase_payment_type' => $record->purchase_payment_type,
                    'affiliate_name' => $record->affiliate_name,
                    'affiliate_code' => $record->affiliate_code,
                    'transaction' => $record->transaction,
                ];
            })
            ->values();

        return response()->json(['records' => $records]);
    }

    public function purchase_events_export_csv(Request $request)
    {
        $tagFieldLabels = $this->purchaseEventTagFieldLabels();
        $filters = $this->parsePurchaseEventFilters($request, $tagFieldLabels, true);

        if ($filters['date_start'] !== '' && $filters['date_end'] !== '' && $filters['date_start'] > $filters['date_end']) {
            $swap = $filters['date_start'];
            $filters['date_start'] = $filters['date_end'];
            $filters['date_end'] = $swap;
        }

        if (!Schema::hasTable('purchase_events')) {
            return redirect()
                ->route('admin.purchase_events')
                ->with('error', 'A tabela purchase_events não existe neste ambiente.');
        }

        $baseQuery = PurchaseEvent::query()
            ->whereNotNull('buyer_checkout_phone')
            ->where('buyer_checkout_phone', '<>', '');

        $this->applyPurchaseEventFilters($baseQuery, $filters, $tagFieldLabels);

        $exportQuery = (clone $baseQuery)
            ->selectRaw('MAX(buyer_name) as buyer_name')
            ->selectRaw('buyer_checkout_phone')
            ->groupBy('buyer_checkout_phone')
            ->orderBy('buyer_checkout_phone');

        $fileName = 'purchase_events_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($exportQuery) {
            $output = fopen('php://output', 'w');
            if ($output === false) {
                return;
            }

            // BOM para compatibilidade de acentuação no Excel.
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Nome', 'Telefone'], ';');

            foreach ($exportQuery->cursor() as $row) {
                fputcsv($output, [
                    (string) ($row->buyer_name ?? ''),
                    (string) ($row->buyer_checkout_phone ?? ''),
                ], ';');
            }

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    private function purchaseEventTagFieldLabels(): array
    {
        return [
            'product_name' => 'Produto',
            'event' => 'Evento',
            'purchase_full_price_value' => 'Valor compra',
            'purchase_status' => 'Status',
            'purchase_payment_type' => 'Forma pagamento',
            'affiliate_name' => 'Afiliado nome',
            'affiliate_code' => 'Afiliado código',
        ];
    }

    private function parsePurchaseEventFilters(Request $request, array $tagFieldLabels, bool $useDefaultDates): array
    {
        $filters = [
            'date_start' => (string) $request->query('date_start', $useDefaultDates ? now()->subDays(90)->toDateString() : ''),
            'date_end' => (string) $request->query('date_end', $useDefaultDates ? now()->toDateString() : ''),
            'phone' => preg_replace('/\D/', '', (string) $request->query('phone', '')),
            'free_search' => trim((string) $request->query('free_search', '')),
            'per_page' => (int) $request->query('per_page', 50),
        ];

        foreach (array_keys($tagFieldLabels) as $field) {
            $includeRaw = $request->query($field, []);
            if (!is_array($includeRaw)) {
                $includeRaw = trim((string) $includeRaw) !== '' ? [$includeRaw] : [];
            }

            $excludeRaw = $request->query($field . '_exclude', []);
            if (!is_array($excludeRaw)) {
                $excludeRaw = trim((string) $excludeRaw) !== '' ? [$excludeRaw] : [];
            }

            $include = $this->normalizeTagValues($field, $includeRaw);
            $exclude = $this->normalizeTagValues($field, $excludeRaw);

            // Não permitir conflito do mesmo valor entre inclusão e exclusão.
            $exclude = array_values(array_diff($exclude, $include));

            $filters[$field] = [
                'include' => $include,
                'exclude' => $exclude,
            ];
        }

        return $filters;
    }

    private function normalizeTagValues(string $field, array $rawValues): array
    {
        $values = collect($rawValues)
            ->map(fn ($value) => trim((string) $value))
            ->filter(fn ($value) => $value !== '')
            ->values()
            ->unique()
            ->all();

        if (in_array($field, ['event', 'purchase_status', 'purchase_payment_type'], true)) {
            $values = array_values(array_unique(array_map(static fn ($v) => strtoupper((string) $v), $values)));
        }

        if ($field === 'purchase_full_price_value') {
            $values = collect($values)
                ->map(fn ($value) => $this->normalizeMoneyFilterValue($value))
                ->filter(fn ($value) => $value !== null)
                ->values()
                ->all();
        }

        return $values;
    }

    private function normalizeMoneyFilterValue(string $value): ?string
    {
        $raw = preg_replace('/[^0-9,.-]/', '', $value);
        if ($raw === null || $raw === '') {
            return null;
        }

        if (str_contains($raw, ',') && str_contains($raw, '.')) {
            $raw = str_replace('.', '', $raw);
        }

        $raw = str_replace(',', '.', $raw);

        return is_numeric($raw) ? (string) $raw : null;
    }

    private function purchaseEventFiltersWithoutExcludes(array $filters, array $tagFieldLabels): array
    {
        $cloned = $filters;

        foreach (array_keys($tagFieldLabels) as $field) {
            $include = $cloned[$field]['include'] ?? [];
            $cloned[$field] = [
                'include' => is_array($include) ? $include : [],
                'exclude' => [],
            ];
        }

        return $cloned;
    }

    private function applyPurchaseEventFilters($query, array $filters, array $tagFieldLabels, ?string $excludeTagField = null): void
    {
        if (($filters['date_start'] ?? '') !== '') {
            $query->whereDate('created_at', '>=', $filters['date_start']);
        }

        if (($filters['date_end'] ?? '') !== '') {
            $query->whereDate('created_at', '<=', $filters['date_end']);
        }

        if (($filters['phone'] ?? '') !== '') {
            $query->where('buyer_checkout_phone', 'like', '%' . $filters['phone'] . '%');
        }

        if (($filters['free_search'] ?? '') !== '') {
            $freeSearch = trim((string) $filters['free_search']);
            $freeSearchDigits = preg_replace('/\D/', '', $freeSearch);

            $query->where(function ($subQuery) use ($freeSearch, $freeSearchDigits) {
                $subQuery
                    ->where('buyer_name', 'like', '%' . $freeSearch . '%')
                    ->orWhere('buyer_email', 'like', '%' . $freeSearch . '%')
                    ->orWhere('buyer_document', 'like', '%' . $freeSearch . '%')
                    ->orWhere('buyer_checkout_phone', 'like', '%' . $freeSearch . '%');

                if ($freeSearchDigits !== '' && $freeSearchDigits !== $freeSearch) {
                    $subQuery
                        ->orWhere('buyer_document', 'like', '%' . $freeSearchDigits . '%')
                        ->orWhere('buyer_checkout_phone', 'like', '%' . $freeSearchDigits . '%');
                }
            });
        }

        $filtersWithoutExcludes = $this->purchaseEventFiltersWithoutExcludes($filters, $tagFieldLabels);

        foreach (array_keys($tagFieldLabels) as $field) {
            if ($excludeTagField === $field) {
                continue;
            }

            $includeValues = $filters[$field]['include'] ?? [];
            $excludeValues = $filters[$field]['exclude'] ?? [];

            if (empty($includeValues) && empty($excludeValues)) {
                continue;
            }

            if ($field === 'purchase_full_price_value') {
                if (!empty($includeValues)) {
                    $query->whereIn($field, array_map('floatval', $includeValues));
                }

                if (!empty($excludeValues)) {
                    $query->whereNotIn($field, array_map('floatval', $excludeValues));
                }
            } elseif (in_array($field, ['event', 'purchase_status', 'purchase_payment_type'], true)) {
                if (!empty($includeValues)) {
                    $placeholders = implode(',', array_fill(0, count($includeValues), '?'));
                    $query->whereRaw("UPPER(TRIM(COALESCE({$field}, ''))) IN ({$placeholders})", $includeValues);
                }

                if (!empty($excludeValues)) {
                    $placeholders = implode(',', array_fill(0, count($excludeValues), '?'));
                    $query->whereRaw("UPPER(TRIM(COALESCE({$field}, ''))) NOT IN ({$placeholders})", $excludeValues);
                }
            } else {
                if (!empty($includeValues)) {
                    $query->whereIn($field, $includeValues);
                }

                if (!empty($excludeValues)) {
                    $query->whereNotIn($field, $excludeValues);
                }
            }

            if (!empty($excludeValues)) {
                $query->whereNotIn('buyer_checkout_phone', function ($subQuery) use ($filtersWithoutExcludes, $tagFieldLabels, $excludeTagField, $field, $excludeValues) {
                    $subQuery
                        ->from('purchase_events as pe_ex')
                        ->select('pe_ex.buyer_checkout_phone')
                        ->whereNotNull('pe_ex.buyer_checkout_phone')
                        ->where('pe_ex.buyer_checkout_phone', '<>', '');

                    $this->applyPurchaseEventFilters($subQuery, $filtersWithoutExcludes, $tagFieldLabels, $excludeTagField);

                    if ($field === 'purchase_full_price_value') {
                        $subQuery->whereIn($field, array_map('floatval', $excludeValues));
                        return;
                    }

                    if (in_array($field, ['event', 'purchase_status', 'purchase_payment_type'], true)) {
                        $placeholders = implode(',', array_fill(0, count($excludeValues), '?'));
                        $subQuery->whereRaw("UPPER(TRIM(COALESCE({$field}, ''))) IN ({$placeholders})", $excludeValues);
                        return;
                    }

                    $subQuery->whereIn($field, $excludeValues);
                });
            }
        }
    }
  
}
