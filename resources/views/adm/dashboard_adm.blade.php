<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard ADM') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="p-4 bg-gray-50 rounded-lg border">
                            <p class="text-sm text-gray-500">Total de Afiliados</p>
                            <p class="text-2xl font-semibold">{{ $afiliados['tot_cadastros'] }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg border">
                            <p class="text-sm text-gray-500">Com domínio cadastrado</p>
                            <p class="text-2xl font-semibold">{{ $afiliados['tot_dominio'] }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg border">
                            <p class="text-sm text-gray-500">Aproveitamento</p>
                            <p class="text-2xl font-semibold">{{ $afiliados['aproveitamento'] }}%</p>
                        </div>
                    </div>

                    <div class="mb-2 text-sm text-gray-500">Últimos 5 meses</div>
                    <div id="dash-revenue-chart" class="w-full" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
        <script>
            var options = {
                series: [{
                    name: "Total de Cadastros",
                    data: @json($totalCadastros)
                }, {
                    name: "Cadastros com Domínio",
                    data: @json($totalComDominio)
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
                dataLabels: { enabled: false },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: { categories: @json($meses) },
                yaxis: { title: { text: 'Número de Cadastros' } },
                fill: { opacity: 1 },
                tooltip: {
                    y: { formatter: function (val) { return val; } }
                }
            };

            var chart = new ApexCharts(document.querySelector("#dash-revenue-chart"), options);
            chart.render();
        </script>
    @endpush
</x-app-layout>
