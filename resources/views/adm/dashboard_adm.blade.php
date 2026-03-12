<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard ADM') }}
        </h2>
    </x-slot>

    @php
        $filters = $filters ?? [
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

        $metrics = $metrics ?? [
            'total_cadastros' => 0,
            'com_dominio' => 0,
            'com_lead' => 0,
            'com_lead_venda' => 0,
            'com_produto' => 0,
            'com_whatsapp' => 0,
        ];

        $healthMetrics = $healthMetrics ?? [
            'afiliados_filtrados' => 0,
            'setup_completo' => 0,
            'com_lead' => 0,
            'com_venda' => 0,
            'taxa_setup' => 0.0,
            'taxa_lead' => 0.0,
            'taxa_venda' => 0.0,
            'taxa_lead_para_venda' => 0.0,
            'mediana_dias_primeiro_lead' => null,
            'mediana_dias_primeira_venda' => null,
        ];

        $dailyLabels = $dailyLabels ?? [];
        $dailyCadastros = $dailyCadastros ?? [];
        $showDailyChart = $showDailyChart ?? false;
        $dailyChartMessage = $dailyChartMessage ?? 'Selecione um intervalo de datas para visualizar os cadastros por dia.';

        $queueCounts = $queueCounts ?? [
            'setup_sem_lead' => 0,
            'lead_sem_venda' => 0,
        ];

        $triStateOptions = [
            'all' => 'Todos',
            'yes' => 'Sim',
            'no' => 'Não',
        ];

        $filaOptions = [
            'all' => 'Todas as filas',
            'setup_sem_lead' => 'Setup sem lead',
            'lead_sem_venda' => 'Lead sem venda',
        ];

        $activeQueueTab = ($filters['fila'] ?? 'all') === 'lead_sem_venda' ? 'lead_sem_venda' : 'setup_sem_lead';
        if (($filters['fila'] ?? 'all') === 'all') {
            $tabFromQuery = (string) request()->query('aba_fila', 'setup_sem_lead');
            if (in_array($tabFromQuery, ['setup_sem_lead', 'lead_sem_venda'], true)) {
                $activeQueueTab = $tabFromQuery;
            }
        }

        $formatPercent = static fn ($value) => number_format((float) $value, 2, ',', '.') . '%';
        $formatMedianDays = static function ($value) {
            if ($value === null) {
                return '-';
            }

            $floatValue = (float) $value;
            if ((int) $floatValue === $floatValue) {
                return number_format($floatValue, 0, ',', '.') . ' dias';
            }

            return number_format($floatValue, 1, ',', '.') . ' dias';
        };
    @endphp

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtros</h3>

                <form method="GET" action="{{ route('dashboard_adm') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="period_scope" class="block text-sm text-gray-600 mb-1">Período</label>
                            <select
                                id="period_scope"
                                name="period_scope"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="last_90_days" @selected(($filters['period_scope'] ?? 'last_90_days') === 'last_90_days')>Últimos 90 dias (padrão)</option>
                                <option value="all" @selected(($filters['period_scope'] ?? 'last_90_days') === 'all')>Todo período</option>
                            </select>
                        </div>

                        <div>
                            <label for="date_start" class="block text-sm text-gray-600 mb-1">Data inicial</label>
                            <input
                                id="date_start"
                                name="date_start"
                                type="date"
                                value="{{ $filters['date_start'] }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                        <div>
                            <label for="date_end" class="block text-sm text-gray-600 mb-1">Data final</label>
                            <input
                                id="date_end"
                                name="date_end"
                                type="date"
                                value="{{ $filters['date_end'] }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            <p class="mt-1 text-xs text-gray-500">As datas são aplicadas quando o período está em "Últimos 90 dias".</p>
                        </div>

                        <div>
                            <label for="tem_dominio" class="block text-sm text-gray-600 mb-1">Tem domínio</label>
                            <select
                                id="tem_dominio"
                                name="tem_dominio"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                @foreach ($triStateOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['tem_dominio'] ?? 'all') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="tem_lead" class="block text-sm text-gray-600 mb-1">Tem lead</label>
                            <select
                                id="tem_lead"
                                name="tem_lead"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                @foreach ($triStateOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['tem_lead'] ?? 'all') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="tem_venda" class="block text-sm text-gray-600 mb-1">Tem lead de venda</label>
                            <select
                                id="tem_venda"
                                name="tem_venda"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                @foreach ($triStateOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['tem_venda'] ?? 'all') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="tem_produto" class="block text-sm text-gray-600 mb-1">Tem produto</label>
                            <select
                                id="tem_produto"
                                name="tem_produto"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                @foreach ($triStateOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['tem_produto'] ?? 'all') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="tem_whatsapp" class="block text-sm text-gray-600 mb-1">Tem WhatsApp</label>
                            <select
                                id="tem_whatsapp"
                                name="tem_whatsapp"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                @foreach ($triStateOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['tem_whatsapp'] ?? 'all') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="dias_sem_lead" class="block text-sm text-gray-600 mb-1">Setup sem lead (dias)</label>
                            <input
                                id="dias_sem_lead"
                                name="dias_sem_lead"
                                type="number"
                                min="0"
                                value="{{ $filters['dias_sem_lead'] }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                        <div>
                            <label for="dias_sem_venda" class="block text-sm text-gray-600 mb-1">Lead sem venda (dias)</label>
                            <input
                                id="dias_sem_venda"
                                name="dias_sem_venda"
                                type="number"
                                min="0"
                                value="{{ $filters['dias_sem_venda'] }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                        <div>
                            <label for="fila" class="block text-sm text-gray-600 mb-1">Fila para exportação</label>
                            <select
                                id="fila"
                                name="fila"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                @foreach ($filaOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['fila'] ?? 'all') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-indigo-700 rounded-md text-sm text-white hover:bg-indigo-700">
                            Filtrar
                        </button>
                        <a href="{{ route('dashboard_adm') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-200">
                            Limpar filtros
                        </a>
                        <a href="{{ route('dashboard_adm_export_csv', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-emerald-700 rounded-md text-sm text-white hover:bg-emerald-700">
                            Exportar fila filtrada (CSV)
                        </a>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="p-4 bg-white rounded-lg border shadow-sm">
                    <p class="text-sm text-gray-500">Total de cadastros</p>
                    <p class="text-2xl font-semibold" data-kpi="total_cadastros">{{ number_format((int) $metrics['total_cadastros'], 0, ',', '.') }}</p>
                </div>
                <div class="p-4 bg-white rounded-lg border shadow-sm">
                    <p class="text-sm text-gray-500">Com domínio cadastrado</p>
                    <p class="text-2xl font-semibold" data-kpi="com_dominio">{{ number_format((int) $metrics['com_dominio'], 0, ',', '.') }}</p>
                </div>
                <div class="p-4 bg-white rounded-lg border shadow-sm">
                    <p class="text-sm text-gray-500">Com pelo menos 1 lead</p>
                    <p class="text-2xl font-semibold" data-kpi="com_lead">{{ number_format((int) $metrics['com_lead'], 0, ',', '.') }}</p>
                </div>
                <div class="p-4 bg-white rounded-lg border shadow-sm">
                    <p class="text-sm text-gray-500">Com lead de venda</p>
                    <p class="text-2xl font-semibold" data-kpi="com_lead_venda">{{ number_format((int) $metrics['com_lead_venda'], 0, ',', '.') }}</p>
                </div>
                <div class="p-4 bg-white rounded-lg border shadow-sm">
                    <p class="text-sm text-gray-500">Com pelo menos 1 produto</p>
                    <p class="text-2xl font-semibold" data-kpi="com_produto">{{ number_format((int) $metrics['com_produto'], 0, ',', '.') }}</p>
                </div>
                <div class="p-4 bg-white rounded-lg border shadow-sm">
                    <p class="text-sm text-gray-500">Com WhatsApp de atendimento</p>
                    <p class="text-2xl font-semibold" data-kpi="com_whatsapp">{{ number_format((int) $metrics['com_whatsapp'], 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Saúde Comercial</h3>
                    <span class="text-sm text-gray-500">Afiliados filtrados: {{ number_format((int) $healthMetrics['afiliados_filtrados'], 0, ',', '.') }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg border">
                        <p class="text-sm text-gray-500">Taxa de setup completo</p>
                        <p class="text-2xl font-semibold" data-health-kpi="taxa_setup">{{ $formatPercent($healthMetrics['taxa_setup'] ?? 0) }}</p>
                        <p class="text-xs text-gray-500">Setup completo: {{ number_format((int) ($healthMetrics['setup_completo'] ?? 0), 0, ',', '.') }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg border">
                        <p class="text-sm text-gray-500">Taxa de lead</p>
                        <p class="text-2xl font-semibold" data-health-kpi="taxa_lead">{{ $formatPercent($healthMetrics['taxa_lead'] ?? 0) }}</p>
                        <p class="text-xs text-gray-500">Com lead: {{ number_format((int) ($healthMetrics['com_lead'] ?? 0), 0, ',', '.') }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg border">
                        <p class="text-sm text-gray-500">Taxa de venda</p>
                        <p class="text-2xl font-semibold" data-health-kpi="taxa_venda">{{ $formatPercent($healthMetrics['taxa_venda'] ?? 0) }}</p>
                        <p class="text-xs text-gray-500">Com venda: {{ number_format((int) ($healthMetrics['com_venda'] ?? 0), 0, ',', '.') }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg border">
                        <p class="text-sm text-gray-500">Taxa lead - venda</p>
                        <p class="text-2xl font-semibold" data-health-kpi="taxa_lead_para_venda">{{ $formatPercent($healthMetrics['taxa_lead_para_venda'] ?? 0) }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg border">
                        <p class="text-sm text-gray-500">Mediana até 1º lead</p>
                        <p class="text-2xl font-semibold" data-health-kpi="mediana_dias_primeiro_lead">{{ $formatMedianDays($healthMetrics['mediana_dias_primeiro_lead'] ?? null) }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg border">
                        <p class="text-sm text-gray-500">Mediana até 1ª venda</p>
                        <p class="text-2xl font-semibold" data-health-kpi="mediana_dias_primeira_venda">{{ $formatMedianDays($healthMetrics['mediana_dias_primeira_venda'] ?? null) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6" id="operacional-filas">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Filas Operacionais</h3>

                <div class="flex flex-wrap gap-2 mb-4" data-tab-root data-active-tab="{{ $activeQueueTab }}">
                    <button
                        type="button"
                        class="queue-tab-button inline-flex items-center px-4 py-2 rounded-md border text-sm bg-indigo-600 text-white border-indigo-700"
                        data-tab-button="setup_sem_lead"
                        aria-controls="panel-setup-sem-lead"
                    >
                        Setup sem lead ({{ $filters['dias_sem_lead'] }}d+) - {{ number_format((int) ($queueCounts['setup_sem_lead'] ?? 0), 0, ',', '.') }}
                    </button>
                    <button
                        type="button"
                        class="queue-tab-button inline-flex items-center px-4 py-2 rounded-md border text-sm bg-gray-100 text-gray-700 border-gray-300"
                        data-tab-button="lead_sem_venda"
                        aria-controls="panel-lead-sem-venda"
                    >
                        Lead sem venda ({{ $filters['dias_sem_venda'] }}d+) - {{ number_format((int) ($queueCounts['lead_sem_venda'] ?? 0), 0, ',', '.') }}
                    </button>
                </div>

                <div id="panel-setup-sem-lead" data-tab-panel="setup_sem_lead" class="queue-tab-panel overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" data-queue="setup_sem_lead">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone contato</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone atendimento</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data cadastro</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dias cadastro</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leads</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendas</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($queueSetupRows as $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-3 text-sm text-gray-900">{{ $row['name'] }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-700">{{ $row['email'] !== '' ? $row['email'] : '-' }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-700">{{ $row['telefone_contato'] !== '' ? $row['telefone_contato'] : '-' }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-700">{{ $row['telefone_atendimento'] !== '' ? $row['telefone_atendimento'] : '-' }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-700">{{ $row['data_cadastro'] !== '' ? \Carbon\Carbon::parse($row['data_cadastro'])->format('d/m/Y') : '-' }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-700">{{ $row['dias_desde_cadastro'] ?? '-' }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-700">{{ $row['total_leads'] ?? 0 }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-700">{{ $row['total_vendas'] ?? 0 }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-8 text-center text-sm text-gray-500">Nenhum afiliado nesta fila para o filtro atual.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="pt-4">
                        {{ $queueSetupRows->appends(array_merge(request()->query(), ['aba_fila' => 'setup_sem_lead']))->links('pagination::tailwind') }}
                    </div>
                </div>

                <div id="panel-lead-sem-venda" data-tab-panel="lead_sem_venda" class="queue-tab-panel overflow-x-auto hidden">
                    <table class="min-w-full divide-y divide-gray-200" data-queue="lead_sem_venda">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone contato</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone atendimento</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data cadastro</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dias cadastro</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leads</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendas</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($queueLeadRows as $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-3 text-sm text-gray-900">{{ $row['name'] }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-700">{{ $row['email'] !== '' ? $row['email'] : '-' }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-700">{{ $row['telefone_contato'] !== '' ? $row['telefone_contato'] : '-' }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-700">{{ $row['telefone_atendimento'] !== '' ? $row['telefone_atendimento'] : '-' }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-700">{{ $row['data_cadastro'] !== '' ? \Carbon\Carbon::parse($row['data_cadastro'])->format('d/m/Y') : '-' }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-700">{{ $row['dias_desde_cadastro'] ?? '-' }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-700">{{ $row['total_leads'] ?? 0 }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-700">{{ $row['total_vendas'] ?? 0 }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-8 text-center text-sm text-gray-500">Nenhum afiliado nesta fila para o filtro atual.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="pt-4">
                        {{ $queueLeadRows->appends(array_merge(request()->query(), ['aba_fila' => 'lead_sem_venda']))->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Usuários filtrados ({{ number_format((int) $usuarios->total(), 0, ',', '.') }})</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone de contato</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone de atendimento</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data de cadastro</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($usuarios as $usuario)
                                @php
                                    $telefoneContato = trim((string) ($usuario->telefone_pessoal_1 ?? ''));
                                    if ($telefoneContato === '') {
                                        $telefoneContato = trim((string) ($usuario->telefone_pessoal_2 ?? ''));
                                    }

                                    $telefoneAtendimento = trim((string) ($usuario->whatsapp_atendimento ?? ''));
                                    if ($telefoneAtendimento === '' && isset($usuario->whatsappAtendimentos) && $usuario->whatsappAtendimentos->isNotEmpty()) {
                                        $telefoneAtendimento = trim((string) ($usuario->whatsappAtendimentos->first()->whatsapp ?? ''));
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $usuario->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $telefoneContato !== '' ? $telefoneContato : '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $usuario->email }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $telefoneAtendimento !== '' ? $telefoneAtendimento : '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ optional($usuario->created_at)->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">Nenhum usuário encontrado para os filtros informados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-100">
                    {{ $usuarios->links('pagination::tailwind') }}
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-2 text-sm text-gray-500">Cadastros por dia (período filtrado)</div>
                @if ($showDailyChart)
                    <div id="dash-daily-chart" class="w-full" style="min-height: 320px;"></div>
                @else
                    <div class="rounded-md border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600">
                        {{ $dailyChartMessage }}
                    </div>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-2 text-sm text-gray-500">Cadastros mensais (últimos 5 meses)</div>
                <div id="dash-revenue-chart" class="w-full" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            (function () {
                const hasApex = typeof ApexCharts !== 'undefined';
                const chartElement = document.querySelector('#dash-revenue-chart');
                if (chartElement) {
                    const monthlyLabels = @json($meses);
                    const monthlyCadastros = @json($totalCadastros);
                    const monthlyComDominio = @json($totalComDominio);

                    if (!hasApex) {
                        chartElement.innerHTML = '<p class="text-sm text-gray-500">Não foi possível carregar a biblioteca de gráfico.</p>';
                    } else if (monthlyLabels.length === 0) {
                        chartElement.innerHTML = '<p class="text-sm text-gray-500">Sem dados de cadastros para os últimos 5 meses.</p>';
                    } else {
                        const options = {
                            series: [{
                                name: 'Total de Cadastros',
                                data: monthlyCadastros
                            }, {
                                name: 'Cadastros com Domínio',
                                data: monthlyComDominio
                            }],
                            chart: {
                                type: 'bar',
                                height: 350
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '45%',
                                    endingShape: 'rounded'
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                show: true,
                                width: 2,
                                colors: ['transparent']
                            },
                            xaxis: {
                                categories: monthlyLabels
                            },
                            yaxis: {
                                title: {
                                    text: 'Número de Cadastros'
                                }
                            },
                            fill: {
                                opacity: 1
                            }
                        };

                        const chart = new ApexCharts(chartElement, options);
                        chart.render();
                    }
                }

                const dailyChartElement = document.querySelector('#dash-daily-chart');
                if (dailyChartElement) {
                    const dailyLabels = @json($dailyLabels);
                    const dailyCadastros = @json($dailyCadastros);

                    if (!hasApex) {
                        dailyChartElement.innerHTML = '<p class="text-sm text-gray-500">Não foi possível carregar a biblioteca de gráfico.</p>';
                    } else if (dailyLabels.length === 0) {
                        dailyChartElement.innerHTML = '<p class="text-sm text-gray-500">Sem dados de cadastros para o período selecionado.</p>';
                    } else {
                        const dailyOptions = {
                            series: [{
                                name: 'Cadastros por dia',
                                data: dailyCadastros
                            }],
                            chart: {
                                type: 'line',
                                height: 320,
                                toolbar: {
                                    show: false
                                }
                            },
                            stroke: {
                                curve: 'smooth',
                                width: 3
                            },
                            dataLabels: {
                                enabled: false
                            },
                            markers: {
                                size: 3
                            },
                            xaxis: {
                                categories: dailyLabels,
                                labels: {
                                    rotate: -45
                                }
                            },
                            yaxis: {
                                title: {
                                    text: 'Cadastros'
                                },
                                min: 0,
                                forceNiceScale: true
                            },
                            tooltip: {
                                y: {
                                    formatter: function (value) {
                                        return String(value);
                                    }
                                }
                            }
                        };

                        const dailyChart = new ApexCharts(dailyChartElement, dailyOptions);
                        dailyChart.render();
                    }
                }

                const tabRoot = document.querySelector('[data-tab-root]');
                if (!tabRoot) {
                    return;
                }

                const activeTabFromServer = tabRoot.getAttribute('data-active-tab') || 'setup_sem_lead';
                const tabButtons = Array.from(tabRoot.querySelectorAll('[data-tab-button]'));
                const panels = Array.from(document.querySelectorAll('[data-tab-panel]'));

                const activateTab = function (tabName) {
                    tabButtons.forEach(function (button) {
                        const isActive = button.getAttribute('data-tab-button') === tabName;
                        button.classList.toggle('bg-indigo-600', isActive);
                        button.classList.toggle('text-white', isActive);
                        button.classList.toggle('border-indigo-700', isActive);
                        button.classList.toggle('bg-gray-100', !isActive);
                        button.classList.toggle('text-gray-700', !isActive);
                        button.classList.toggle('border-gray-300', !isActive);
                    });

                    panels.forEach(function (panel) {
                        const isActive = panel.getAttribute('data-tab-panel') === tabName;
                        panel.classList.toggle('hidden', !isActive);
                    });
                };

                tabButtons.forEach(function (button) {
                    button.addEventListener('click', function () {
                        activateTab(button.getAttribute('data-tab-button'));
                    });
                });

                activateTab(activeTabFromServer);
            })();
        </script>
    @endpush
</x-app-layout>
