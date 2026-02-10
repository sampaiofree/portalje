@php
//$dominioseco = str_replace('.portalje.org', '', Auth::user()->dominio);
@endphp
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <title>@yield('titulo_pagina')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="@yield('descricao_pagina')" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('Hyper_v5.4/Admin/dist/saas/assets/images/favicon.ico')}}" >

    <!-- Daterangepicker css -->
    <link href="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/daterangepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css">

    <!-- Vector Map css -->
    <link href="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/jsvectormap/css/jsvectormap.min.css')}}"  rel="stylesheet" type="text/css">

    <!-- Theme Config Js -->
    <script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/js/hyper-config.js')}}" ></script>

    <!-- App css -->
    <link href="{{asset('Hyper_v5.4/Admin/dist/saas/assets/css/app-saas.min.css')}}"  rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons css -->
    <link href="{{asset('Hyper_v5.4/Admin/dist/saas/assets/css/icons.min.css')}}"  rel="stylesheet" type="text/css" />

    <style>
        .btn-expand {
            transition: transform 0.3s ease-in-out;
        }

        .btn-expand:hover {
            transform: scale(1.1); /* Aumenta o botão em 10% */
        }

    </style>

    @yield('head')
</head>

<body>
<!-- Begin page -->
<div class="wrapper">

    <div class="pt-3">
        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <h4 class="page-title">@yield('titulo_pagina')</h4>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <a class="btn btn-primary" href="{{route('nova_expedicao')}}"><i class="ri-file-edit-fill "></i> Nova Expedição</a>
                                <a class="btn btn-primary" href="{{route('listar_exedicoes')}}"><i class="ri-list-check "></i> Listar Expedições</a>
                                <a class="btn btn-primary" href="{{route('listar_interessados')}}"><i class="ri-user-3-line  "></i> Listar Interessados</a>
                            </div>
                            <div class="card-body">
                                <div id="alert-container"></div>
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>
            <!-- container $('#minha-jornada').modal('show')-->
        </div>
        <!-- content -->

<!-- VIDEO DE AJUDA -->

<!-- END wrapper -->

<!-- Vendor js -->
<script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/js/vendor.min.js')}}"></script>

<!-- Daterangepicker js -->
<script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/daterangepicker/daterangepicker.js')}}"></script>

<!-- App js -->
<script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/js/app.min.js')}}"></script>
<script>
 //MENSGEM DE ALETRA
 function showAlert(message, type = 'success') {
    // Seleciona o contêiner de alertas
    const alertContainer = document.getElementById('alert-container');

    // Remove qualquer alerta existente
    alertContainer.innerHTML = '';

    // Cria o elemento de alerta
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.role = 'alert';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    // Adiciona o alerta ao contêiner de alertas
    alertContainer.appendChild(alert);

    // Remove o alerta automaticamente após 5 segundos
    setTimeout(() => {
        alert.classList.remove('show');
        alert.classList.add('fade');
        setTimeout(() => alert.remove(), 150);
    }, 5000);
}

</script>
@yield('scripts')
</body>

</html>