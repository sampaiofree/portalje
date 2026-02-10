@extends('adm.html_base')

@section('content')

<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="header-title">Cadastros de Afiliados</h4>
                <div>Últimos 5 meses</div>
            </div>
    
            <div class="card-body pt-0">
                <div class="chart-content-bg">
                    <div class="row text-center">
                        <div class="col">
                            <p class="text-muted mb-0 mt-3">Total de Afiliados</p>
                            <h2 class="fw-normal mb-3">
                                <span>{{$afiliados['tot_cadastros']}}</span>
                            </h2>
                        </div>
                        <div class="col">
                            <p class="text-muted mb-0 mt-3">Total com domínio cadastrado</p>
                            <h2 class="fw-normal mb-3">
                                <span>{{$afiliados['tot_dominio']}}</span>
                            </h2>
                        </div>
                        <div class="col">
                            <p class="text-muted mb-0 mt-3">Aproveitamento</p>
                            <h2 class="fw-normal mb-3">
                                <span>{{$afiliados['aproveitamento']}}%</span>
                            </h2>
                        </div>
                    </div>
                </div>
    
                <div dir="ltr">
                    <div id="dash-revenue-chart" class="apex-charts" data-colors="#0acf97,#fa5c7c"></div>
                </div>
    
            </div>
            <!-- end card body-->
        </div>
        <!-- end card -->
    </div>
    <!-- end col-->
        
    <!--<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>-->
 
    
</div>
    
@endsection

@section('scripts')
<!-- Apex  Charts js -->
<script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/apexcharts/apexcharts.min.js')}}"></script>
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
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: @json($meses),
        },
        yaxis: {
            title: {
                text: 'Número de Cadastros'
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val
                }
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#dash-revenue-chart"), options);
    chart.render();
</script>
@endsection
