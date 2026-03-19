<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Ranking dos Afiliados') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Acompanhe o desempenho de vendas por período</p>
            </div>
            <div class="hidden md:flex items-center space-x-2 text-sm text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012-2m-6 0h6"></path>
                </svg>
                <span>Atualizado em tempo real</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @php
                $activeTab = in_array(($activeTab ?? 'xp'), ['sales', 'xp'], true) ? $activeTab : 'xp';
                $salesTabUrl = route('ranking', array_filter(['tab' => 'sales', 'mes' => $mes]));
                $xpTabUrl = route('ranking', ['tab' => 'xp']);
                $xpRows = $xpRows ?? [];
                $xpTotals = array_merge([
                    'participants' => 0,
                    'xp_total' => 0,
                    'xp_average' => 0,
                ], $xpTotals ?? []);
                $xpMeta = array_merge([
                    'page_size' => 10,
                    'next_offset' => count($xpRows),
                    'has_more' => false,
                    'max' => 100,
                    'max_visible' => count($xpRows),
                    'load_more_url' => route('ranking.xp.load_more'),
                ], $xpMeta ?? []);
            @endphp

            <div class="mb-6 inline-flex rounded-xl bg-gray-100 p-1 shadow-sm">
                <a
                    href="{{ $salesTabUrl }}"
                    class="rounded-lg px-4 py-2 text-sm font-semibold transition {{ $activeTab === 'sales' ? 'bg-white text-indigo-700 shadow' : 'text-gray-600 hover:text-gray-800' }}"
                >
                    Ranking por venda
                </a>
                <a
                    href="{{ $xpTabUrl }}"
                    class="rounded-lg px-4 py-2 text-sm font-semibold transition {{ $activeTab === 'xp' ? 'bg-white text-indigo-700 shadow' : 'text-gray-600 hover:text-gray-800' }}"
                >
                    Ranking por XP
                </a>
            </div>

            @if ($activeTab === 'sales')
                <!-- Cards de Resumo -->
                @php
                    $totalAfiliados = 0;
                    $totalVendas = 0;
                    $totalFaturamento = 0;
                    $ignoreList = ["Portal Jovem Empreendedor", "BRUNO SAMPAIO GONÇALVES", "SIDNEY DE ARAUJO ALMEIDA 05757969601"];

                    foreach ($d as $afiliado => $dados) {
                        if ($afiliado && !in_array($afiliado, $ignoreList) && $afiliado != "") {
                            $totalAfiliados++;
                            $totalVendas += count($dados);
                            $totalFaturamento += array_sum(array_column($dados, 'purchase_price_value'));
                        }
                    }
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 overflow-hidden shadow-lg rounded-xl">
                        <div class="p-6 text-white">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-100">Total de Afiliados</p>
                                    <p class="text-2xl font-bold">{{ number_format($totalAfiliados) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-green-500 to-green-600 overflow-hidden shadow-lg rounded-xl">
                        <div class="p-6 text-white">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11h8"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-100">Total de Vendas</p>
                                    <p class="text-2xl font-bold">{{ number_format($totalVendas) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 overflow-hidden shadow-lg rounded-xl">
                        <div class="p-6 text-white">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-purple-100">Faturamento Total</p>
                                    <p class="text-2xl font-bold">R$ {{ number_format($totalFaturamento, 2, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela Principal -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">Ranking de Performance</h3>
                            </div>

                            <!-- Filtro de Mês Melhorado -->
                            <div class="mt-4 sm:mt-0">
                                <div class="relative">
                                    <x-input-label for="mesSelect" value="📅 Período:" class="text-sm font-medium text-gray-700 mb-2" />
                                    <select id="mesSelect" name="mes"
                                            class="mt-1 block w-full sm:w-auto pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 rounded-lg shadow-sm bg-white"
                                            onchange="window.location.href = `{{ route('ranking') }}?tab=sales&mes=${this.value}`">
                                        <option value="">📊 Total de {{ date('Y') }}</option>
                                        @php
                                            $dataInicio = \Carbon\Carbon::create(2024, 8, 1);
                                            $dataFim = \Carbon\Carbon::now();
                                            $meses = [];
                                            while ($dataInicio <= $dataFim) {
                                                $meses[] = [
                                                    'value' => $dataInicio->format('Y-m'),
                                                    'label' => ucfirst($dataInicio->locale('pt_BR')->translatedFormat('F \d\e Y'))
                                                ];
                                                $dataInicio->addMonth();
                                            }
                                        @endphp
                                        @foreach (array_reverse($meses) as $mesOption)
                                            <option value="{{ $mesOption['value'] }}" @selected($mesOption['value'] == $mes)>
                                                📅 {{ $mesOption['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <span class="mr-2">🏆</span>
                                                Posição
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <span class="mr-2">👤</span>
                                                Afiliado
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            <div class="flex items-center justify-center">
                                                <span class="mr-2">📈</span>
                                                Vendas
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            <div class="flex items-center justify-end">
                                                <span class="mr-2">💰</span>
                                                Faturamento
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php
                                        // Lógica de ordenação mantida
                                        $dadosOrdenados = [];
                                        foreach ($d as $afiliado => $dados) {
                                            if ($afiliado) {
                                                $dadosOrdenados[] = [
                                                    'afiliado' => $afiliado,
                                                    'total_vendas' => count($dados),
                                                    'total' => array_sum(array_column($dados, 'purchase_price_value'))
                                                ];
                                            }
                                        }
                                        usort($dadosOrdenados, fn($a, $b) => $b['total'] <=> $a['total']);
                                        $contador = 1;
                                        $ignoreList = ["Portal Jovem Empreendedor", "BRUNO SAMPAIO GONÇALVES", "SIDNEY DE ARAUJO ALMEIDA 05757969601"];
                                    @endphp

                                    @forelse ($dadosOrdenados as $item)
                                        @if (!in_array($item['afiliado'], $ignoreList) && $item['afiliado'] != "")
                                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        @if($contador == 1)
                                                            <span class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 text-yellow-800 text-sm font-bold rounded-full">🥇</span>
                                                        @elseif($contador == 2)
                                                            <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 text-gray-800 text-sm font-bold rounded-full">🥈</span>
                                                        @elseif($contador == 3)
                                                            <span class="inline-flex items-center justify-center w-8 h-8 bg-orange-100 text-orange-800 text-sm font-bold rounded-full">🥉</span>
                                                        @else
                                                            <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 text-gray-600 text-sm font-medium rounded-full">{{ $contador }}</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                                                                <span class="text-sm font-medium text-white uppercase">
                                                                    {{ substr(preg_match('/^[0-9.]+$/', trim(explode(' ', $item['afiliado'])[0])) ? explode(' ', $item['afiliado'])[1] : explode(' ', $item['afiliado'])[0], 0, 2) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900 uppercase">
                                                                {{ preg_match('/^[0-9.]+$/', trim(explode(' ', $item['afiliado'])[0])) ? explode(' ', $item['afiliado'])[1] : explode(' ', $item['afiliado'])[0] }}
                                                            </div>
                                                            @if($contador <= 3)
                                                                <div class="text-xs text-gray-500">Top Performer</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                                        @if($item['total_vendas'] >= 10) bg-green-100 text-green-800
                                                        @elseif($item['total_vendas'] >= 5) bg-yellow-100 text-yellow-800
                                                        @else bg-gray-100 text-gray-800 @endif">
                                                        {{ $item['total_vendas'] }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    <div class="text-sm font-bold text-gray-900">
                                                        R$ {{ number_format($item['total'], 2, ',', '.') }}
                                                    </div>
                                                    @if($contador <= 3)
                                                        <div class="text-xs text-green-600 font-medium">
                                                            ⬆️ Destaque
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                            @php $contador++; @endphp
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-16">
                                                <div class="flex flex-col items-center justify-center">
                                                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012-2m-6 0h6"></path>
                                                    </svg>
                                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum dado encontrado</h3>
                                                    <p class="text-gray-500">Não há dados de ranking para o período selecionado.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Footer da tabela com informações adicionais -->
                    <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span>Exibindo {{ count(array_filter($dadosOrdenados, fn($item) => !in_array($item['afiliado'], $ignoreList) && $item['afiliado'] != "")) }} afiliados</span>
                            <span>Última atualização: {{ now()->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            @else
                @php
                    $xpParticipants = (int) ($xpTotals['participants'] ?? 0);
                    $xpAverage = (int) ($xpTotals['xp_average'] ?? 0);
                @endphp

                <div
                    x-data="xpRankingList({
                        initialRows: @js($xpRows),
                        nextOffset: {{ (int) ($xpMeta['next_offset'] ?? count($xpRows)) }},
                        hasMore: @js((bool) ($xpMeta['has_more'] ?? false)),
                        maxVisible: {{ (int) ($xpMeta['max_visible'] ?? count($xpRows)) }},
                        pageSize: {{ (int) ($xpMeta['page_size'] ?? 10) }},
                        loadMoreUrl: @js($xpMeta['load_more_url'] ?? route('ranking.xp.load_more')),
                    })"
                >
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 overflow-hidden shadow-lg rounded-xl">
                            <div class="p-6 text-white">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-indigo-100">Afiliados com XP</p>
                                        <p class="text-2xl font-bold">{{ number_format($xpParticipants) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 overflow-hidden shadow-lg rounded-xl">
                            <div class="p-6 text-white">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-emerald-100">Média de XP</p>
                                        <p class="text-2xl font-bold">{{ number_format($xpAverage) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h8m0 0l-3-3m3 3l-3 3M5 3v18"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">Ranking por XP</h3>
                            </div>
                        </div>

                        <div class="overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                                <div class="flex items-center">
                                                    <span class="mr-2">🏆</span>
                                                    Posição
                                                </div>
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                                <div class="flex items-center">
                                                    <span class="mr-2">👤</span>
                                                    Afiliado
                                                </div>
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                                <div class="flex items-center justify-end">
                                                    <span class="mr-2">⚡</span>
                                                    XP
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <template x-if="rows.length === 0">
                                            <tr>
                                                <td colspan="3" class="text-center py-16">
                                                    <div class="flex flex-col items-center justify-center">
                                                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                                        </svg>
                                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum afiliado com XP</h3>
                                                        <p class="text-gray-500">Ainda não há afiliados com XP acumulado para exibir no ranking.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>

                                        <template x-for="row in rows" :key="row.position">
                                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <template x-if="row.position === 1">
                                                            <span class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 text-yellow-800 text-sm font-bold rounded-full">🥇</span>
                                                        </template>
                                                        <template x-if="row.position === 2">
                                                            <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 text-gray-800 text-sm font-bold rounded-full">🥈</span>
                                                        </template>
                                                        <template x-if="row.position === 3">
                                                            <span class="inline-flex items-center justify-center w-8 h-8 bg-orange-100 text-orange-800 text-sm font-bold rounded-full">🥉</span>
                                                        </template>
                                                        <template x-if="row.position > 3">
                                                            <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 text-gray-600 text-sm font-medium rounded-full" x-text="row.position"></span>
                                                        </template>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                                                                <span class="text-sm font-medium text-white uppercase" x-text="initials(row.display_name)"></span>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900 uppercase" x-text="row.display_name || 'Sem nome'"></div>
                                                            <template x-if="row.position <= 3">
                                                                <div class="text-xs text-gray-500">Top XP</div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-amber-100 text-amber-800">
                                                        <span x-text="formatNumber(row.xp_total)"></span> XP
                                                    </span>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="px-6 py-4 border-t border-gray-200 bg-white" x-show="hasMore || loading">
                            <div class="flex flex-col items-center gap-2">
                                <button
                                    type="button"
                                    class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed"
                                    :disabled="loading || !hasMore"
                                    @click="loadMore"
                                >
                                    <span x-show="!loading">Mostrar mais (+10)</span>
                                    <span x-show="loading">Carregando...</span>
                                </button>
                                <p class="text-sm text-red-600" x-show="errorMessage" x-text="errorMessage"></p>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span x-text="`Exibindo ${formatNumber(rows.length)} de ${formatNumber(maxVisible)} afiliados`"></span>
                                <span>Última atualização: {{ now()->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function xpRankingList(config) {
            return {
                rows: Array.isArray(config.initialRows) ? config.initialRows : [],
                nextOffset: Number(config.nextOffset || 0),
                hasMore: Boolean(config.hasMore),
                maxVisible: Number(config.maxVisible || 0),
                pageSize: Number(config.pageSize || 10),
                loadMoreUrl: config.loadMoreUrl || '',
                loading: false,
                errorMessage: '',
                formatNumber(value) {
                    return Number(value || 0).toLocaleString('pt-BR');
                },
                initials(name) {
                    const text = String(name || 'Sem nome').trim();
                    return text.slice(0, 2).toUpperCase();
                },
                async loadMore() {
                    if (this.loading || !this.hasMore || !this.loadMoreUrl) {
                        return;
                    }

                    this.loading = true;
                    this.errorMessage = '';

                    try {
                        const url = new URL(this.loadMoreUrl, window.location.origin);
                        url.searchParams.set('offset', String(this.nextOffset));
                        url.searchParams.set('limit', String(this.pageSize));

                        const response = await fetch(url.toString(), {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        if (!response.ok) {
                            throw new Error('Falha ao carregar mais itens.');
                        }

                        const data = await response.json();
                        const newRows = Array.isArray(data.rows) ? data.rows : [];

                        this.rows.push(...newRows);
                        this.nextOffset = Number(data.next_offset || this.nextOffset + newRows.length);
                        this.hasMore = Boolean(data.has_more);
                    } catch (error) {
                        this.errorMessage = 'Não foi possível carregar mais agora. Tente novamente.';
                    } finally {
                        this.loading = false;
                    }
                },
            };
        }
    </script>
</x-app-layout>
