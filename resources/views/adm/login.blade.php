<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>Log In | Portal dos parceiros Jovem Empreendedor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <link rel="manifest" href="/manifest.json">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    
    <link rel="shortcut icon" href="{{ asset('Hyper_v5.4/icons/logoje192.png')}}">

    <!-- Theme Config Js -->
    <script src="{{ asset('Hyper_v5.4/Admin/dist/saas/assets/js/hyper-config.js') }}"></script>

    <!-- App css -->
    <link href="{{ asset('Hyper_v5.4/Admin/dist/saas/assets/css/app-saas.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons css -->
    <link href="{{ asset('Hyper_v5.4/Admin/dist/saas/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        /* animação pulsar */
    @keyframes pulse {
      0%   { transform: scale(1); box-shadow: 0 0 0 rgba(0,102,204, 0.7); }
      50%  { transform: scale(1.05); box-shadow: 0 0 15px rgba(0,102,204, 0.7); }
      100% { transform: scale(1); box-shadow: 0 0 0 rgba(0,102,204, 0.7); }
    }
    .btn-pulse {
      animation: pulse 2s infinite;
      border-radius: 50px;
      padding: 0.75rem 1.5rem;
    }
    </style>
</head>

<body class="authentication-bg position-relative">
<div class="position-absolute start-0 end-0 start-0 bottom-0 w-100 h-100">
    <svg xmlns='http://www.w3.org/2000/svg' width='100%' height='100%' viewBox='0 0 800 800'>
        <g fill-opacity='0.22'>
            <circle style="fill: rgba(var(--ct-primary-rgb), 0.1);" cx='400' cy='400' r='600' />
            <circle style="fill: rgba(var(--ct-primary-rgb), 0.2);" cx='400' cy='400' r='500' />
            <circle style="fill: rgba(var(--ct-primary-rgb), 0.3);" cx='400' cy='400' r='300' />
            <circle style="fill: rgba(var(--ct-primary-rgb), 0.4);" cx='400' cy='400' r='200' />
            <circle style="fill: rgba(var(--ct-primary-rgb), 0.5);" cx='400' cy='400' r='100' />
        </g>
    </svg>
</div>
<div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5 position-relative">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-4 col-lg-5">
                <div class="card">

                    <!-- Logo -->
                    <div class="card-header py-4 text-center bg-primary">
                        <a href="index.html">
                            <span><img src="{{ asset('img/logo/logo-je-dark.png') }}" alt="logo" max-height="40"></span>
                        </a>
                    </div>

                    <div class="card-body p-4">
                        @yield('content')
                    </div> <!-- end card-body -->
                </div>
            </div> <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
</div>
<!-- end page -->

<footer class="footer footer-alt"><script>document.write(new Date().getFullYear())</script> © jempreendedor.org</footer>
<!-- Vendor js -->
<script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/js/vendor.min.js')}}"></script>

<!-- App js -->
<script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/js/app.min.js')}}"></script>

</body>

</html>