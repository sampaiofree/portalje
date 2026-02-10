@php
$dominioseco = str_replace('.portalje.org', '', Auth::user()->dominio);
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

   
    <!-- ========== Topbar Start ========== -->
    <div class="navbar-custom">
        <div class="topbar container-fluid">
            <div class="d-flex align-items-center gap-lg-2 gap-1">

                <!-- Topbar Brand Logo -->
                <div class="logo-topbar">
                    <!-- Logo light -->
                    <a href="{{route('dashboard')}}" class="logo-light">
                            <span class="logo-lg">
                                <img src="{{asset('img/logo/logo-je-dark.png')}}" alt="logo">
                            </span>
                        <span class="logo-sm">
                                <img src="{{asset('img/logo/logo-dark-je-sm.png')}}" alt="small logo">
                            </span>
                    </a>

                    <!-- Logo Dark -->
                    <a href="{{route('dashboard')}}" class="logo-dark">
                            <span class="logo-lg">
                                <img src="{{asset('img/logo/logo-je-dark.png')}}" alt="dark logo">
                            </span>
                        <span class="logo-sm">
                                <img src="{{asset('img/logo/logo-je-sm.png')}}" alt="small logo">
                            </span>
                    </a>
                </div>

                <!-- Sidebar Menu Toggle Button -->
                <button class="button-toggle-menu">
                    <i class="mdi mdi-menu"></i>
                </button>

                <!-- Horizontal Menu Toggle Button -->
                <button class="navbar-toggle" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                    <div class="lines">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>

                <!-- Topbar Search Form -->
                <div class="app-search dropdown d-none d-lg-block">
                    <!--<form>
                        <div class="input-group">
                            <input type="search" class="form-control dropdown-toggle" placeholder="Search..." id="top-search">
                            <span class="mdi mdi-magnify search-icon"></span>
                            <button class="input-group-text btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>-->

                    <div class="dropdown-menu dropdown-menu-animated dropdown-lg" id="search-dropdown">
                        <!-- item-->
                        <div class="dropdown-header noti-title">
                            <h5 class="text-overflow mb-2">Found <span class="text-danger">17</span> results</h5>
                        </div>

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="uil-notes font-16 me-1"></i>
                            <span>Analytics Report</span>
                        </a>

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="uil-life-ring font-16 me-1"></i>
                            <span>How can I help you?</span>
                        </a>

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="uil-cog font-16 me-1"></i>
                            <span>User profile settings</span>
                        </a>

                        <!-- item-->
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow mb-2 text-uppercase">Users</h6>
                        </div>

                        <!--<div class="notification-list">

                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <div class="d-flex">
                                    <img class="d-flex me-2 rounded-circle" src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/images/users/avatar-2.jpg')}}" alt="Generic placeholder image" height="32">
                                    <div class="w-100">
                                        <h5 class="m-0 font-14">Erwin Brown</h5>
                                        <span class="font-12 mb-0">UI Designer</span>
                                    </div>
                                </div>
                            </a>


                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <div class="d-flex">
                                    <img class="d-flex me-2 rounded-circle" src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/images/users/avatar-5.jpg')}}" alt="Generic placeholder image" height="32">
                                    <div class="w-100">
                                        <h5 class="m-0 font-14">Jacob Deo</h5>
                                        <span class="font-12 mb-0">Developer</span>
                                    </div>
                                </div>
                            </a>
                        </div>-->
                    </div>
                </div>
            </div>

            <ul class="topbar-menu d-flex align-items-center gap-3">
                <li class="dropdown d-lg-none">
                    <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ri-search-line font-22"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-animated dropdown-lg p-0">
                        <form class="p-3">
                            <input type="search" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                        </form>
                    </div>
                </li>
                <!--<li class="dropdown notification-list">
                    <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ri-notification-3-line font-22"></i>
                        <span class="noti-icon-badge"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg py-0">
                        <div class="p-2 border-top-0 border-start-0 border-end-0 border-dashed border">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 font-16 fw-semibold"> Notification</h6>
                                </div>
                                <div class="col-auto">
                                    <a href="javascript: void(0);" class="text-dark text-decoration-underline">
                                        <small>Clear All</small>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="px-2" style="max-height: 300px;" data-simplebar>

                            <h5 class="text-muted font-13 fw-normal mt-2">Today</h5>
                            

                            <a href="javascript:void(0);" class="dropdown-item p-0 notify-item card unread-noti shadow-none mb-2">
                                <div class="card-body">
                                    <span class="float-end noti-close-btn text-muted"><i class="mdi mdi-close"></i></span>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="notify-icon bg-primary">
                                                <i class="mdi mdi-comment-account-outline"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 text-truncate ms-2">
                                            <h5 class="noti-item-title fw-semibold font-14">Datacorp <small class="fw-normal text-muted ms-1">1 min ago</small></h5>
                                            <small class="noti-item-subtitle text-muted">Caleb Flakelar commented on Admin</small>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            
                            <a href="javascript:void(0);" class="dropdown-item p-0 notify-item card read-noti shadow-none mb-2">
                                <div class="card-body">
                                    <span class="float-end noti-close-btn text-muted"><i class="mdi mdi-close"></i></span>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="notify-icon bg-info">
                                                <i class="mdi mdi-account-plus"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 text-truncate ms-2">
                                            <h5 class="noti-item-title fw-semibold font-14">Admin <small class="fw-normal text-muted ms-1">1 hours ago</small></h5>
                                            <small class="noti-item-subtitle text-muted">New user registered</small>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <h5 class="text-muted font-13 fw-normal mt-0">Yesterday</h5>

                            
                            <a href="javascript:void(0);" class="dropdown-item p-0 notify-item card read-noti shadow-none mb-2">
                                <div class="card-body">
                                    <span class="float-end noti-close-btn text-muted"><i class="mdi mdi-close"></i></span>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="notify-icon">
                                                <img src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/images/users/avatar-2.jpg')}}" class="img-fluid rounded-circle" alt="" />
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 text-truncate ms-2">
                                            <h5 class="noti-item-title fw-semibold font-14">Cristina Pride <small class="fw-normal text-muted ms-1">1 day ago</small></h5>
                                            <small class="noti-item-subtitle text-muted">Hi, How are you? What about our next meeting</small>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <h5 class="text-muted font-13 fw-normal mt-0">30 Dec 2021</h5>

                            
                            <a href="javascript:void(0);" class="dropdown-item p-0 notify-item card read-noti shadow-none mb-2">
                                <div class="card-body">
                                    <span class="float-end noti-close-btn text-muted"><i class="mdi mdi-close"></i></span>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="notify-icon bg-primary">
                                                <i class="mdi mdi-comment-account-outline"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 text-truncate ms-2">
                                            <h5 class="noti-item-title fw-semibold font-14">Datacorp</h5>
                                            <small class="noti-item-subtitle text-muted">Caleb Flakelar commented on Admin</small>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            
                            <a href="javascript:void(0);" class="dropdown-item p-0 notify-item card read-noti shadow-none mb-2">
                                <div class="card-body">
                                    <span class="float-end noti-close-btn text-muted"><i class="mdi mdi-close"></i></span>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="notify-icon">
                                                <img src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/images/users/avatar-4.jpg')}}" class="img-fluid rounded-circle" alt="" />
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 text-truncate ms-2">
                                            <h5 class="noti-item-title fw-semibold font-14">Karen Robinson</h5>
                                            <small class="noti-item-subtitle text-muted">Wow ! this admin looks good and awesome design</small>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <div class="text-center">
                                <i class="mdi mdi-dots-circle mdi-spin text-muted h3 mt-0"></i>
                            </div>
                        </div>

                        
                        <a href="javascript:void(0);" class="dropdown-item text-center text-primary notify-item border-top py-2">
                            View All
                        </a>

                    </div>
                </li>-->

                <!--<li class="dropdown d-none d-sm-inline-block">
                    <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ri-apps-2-line font-22"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg p-0">

                        <div class="p-2">
                            <div class="row g-0">
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#">
                                        <img src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/images/brands/slack.png')}}" alt="slack">
                                        <span>Slack</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#">
                                        <img src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/images/brands/github.png')}}" alt="Github">
                                        <span>GitHub</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#">
                                        <img src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/images/brands/dribbble.png')}}" alt="dribbble">
                                        <span>Dribbble</span>
                                    </a>
                                </div>
                            </div>

                            <div class="row g-0">
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#">
                                        <img src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/images/brands/bitbucket.png')}}" alt="bitbucket">
                                        <span>Bitbucket</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#">
                                        <img src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/images/brands/dropbox.png')}}" alt="dropbox">
                                        <span>Dropbox</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#">
                                        <img src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/images/brands/g-suite.png')}}" alt="G Suite">
                                        <span>G Suite</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </li>-->

                <li class="d-none d-sm-inline-block">
                    <a class="nav-link" data-bs-toggle="offcanvas" href="#theme-settings-offcanvas">
                        <i class="ri-settings-3-line font-22"></i>
                    </a>
                </li>

                <li class="d-none d-sm-inline-block">
                    <div class="nav-link" id="light-dark-mode" data-bs-toggle="tooltip" data-bs-placement="left" title="Theme Mode">
                        <i class="ri-moon-line font-22"></i>
                    </div>
                </li>


                <li class="d-none d-md-inline-block">
                    <a class="nav-link" href="" data-toggle="fullscreen">
                        <i class="ri-fullscreen-line font-22"></i>
                    </a>
                </li>

                <li class="dropdown">
                    <a class="nav-link dropdown-toggle arrow-none nav-user px-2" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <span class="account-user-avatar">
                                <!--<img src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/images/users/avatar-1.jpg')}}" alt="user-image" width="32" class="rounded-circle">-->
                            </span>
                        <span class="d-lg-flex flex-column gap-1 d-none">
                                <h5 class="my-0">{{ Auth::user()->name }}</h5>
                                <!--<h6 class="my-0 fw-normal"> 1</h6>-->
                            </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                        <!-- item-->
                        <!--<div class=" dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Welcome !</h6>
                        </div>

                        
                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="mdi mdi-account-circle me-1"></i>
                            <span>My Account</span>
                        </a>

                        
                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="mdi mdi-account-edit me-1"></i>
                            <span>Settings</span>
                        </a>

                        
                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="mdi mdi-lifebuoy me-1"></i>
                            <span>Support</span>
                        </a>

                        
                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="mdi mdi-lock-outline me-1"></i>
                            <span>Lock Screen</span>
                        </a>-->

                        
                        <a onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="dropdown-item" style=" cursor: pointer;">
                            <i class="mdi mdi-logout me-1"></i>
                            <span>Logout</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout2') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <!-- ========== Topbar End ========== -->

    <!-- ========== Left Sidebar Start ========== -->
    <div class="leftside-menu">

        <!-- Brand Logo Light -->
        <a href="{{route('dashboard')}}" class="logo logo-light">
                <span class="logo-lg">
                    <img src="{{asset('img/logo/logo-je-dark.png')}}" alt="logo">
                </span>
            <span class="logo-sm">
                    <img src="{{asset('img/logo/logo-dark-je-sm.png')}}" alt="small logo">
                </span>
        </a>

        <!-- Brand Logo Dark -->
        <a href="{{route('dashboard')}}" class="logo logo-dark">
                <span class="logo-lg">
                    <img src="{{asset('img/logo/logo-je-dark.png')}}" alt="dark logo">
                </span>
            <span class="logo-sm">
                    <img src="{{asset('img/logo/logo-dark-je-sm.png')}}" alt="small logo">
                </span>
        </a>

        <!-- Sidebar Hover Menu Toggle Button -->
        <div class="button-sm-hover" data-bs-toggle="tooltip" data-bs-placement="right" title="Show Full Sidebar">
            <i class="ri-checkbox-blank-circle-line align-middle"></i>
        </div>

        <!-- Full Sidebar Menu Close Button -->
        <div class="button-close-fullsidebar">
            <i class="ri-close-fill align-middle"></i>
        </div>

        <!-- Sidebar -->
        <div class="h-100" id="leftside-menu-container" data-simplebar>
            <!-- Leftbar User -->
            <div class="leftbar-user">
                <a href="pages-profile.html">
                    <img src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/images/users/avatar-1.jpg')}}" alt="user-image" height="42" class="rounded-circle shadow-sm">
                    <span class="leftbar-user-name mt-2">Dominic Keller</span>
                </a>
            </div>

            <!--- Sidemenu -->
            <ul class="side-nav">
                <li class="side-nav-item">
                    <a href="{{route('dashboard')}}" class="side-nav-link">
                        <i class="uil-dashboard  "></i>
                        <span> Dashboard </span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#estrutura" aria-expanded="false" aria-controls="sidebarDashboards" class="side-nav-link">
                        <i class="uil-store"></i>
                        <span> Minha estrutura </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="estrutura">
                        <ul class="side-nav-second-level">
                            <li>
                                <a 
                                @if(!Auth::user()->dominio_externo) 
                                    href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modal_form_dominio" 
                                @else
                                    href="{{route('afiliado_configurar_site', '#section_dominio_externo')}}"
                                @endif
                                >
                                    <i class="uil-home-alt "></i>
                                    <span> Meu site / domínio </span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modal_form_user_whatsapp_atendimento">
                                    <i class="ri-whatsapp-line "></i>
                                    <span> WhatsApp de atendimento </span>
                                </a>
                            </li>
                            <li>
                                <a href="{{route('cadastrar_cursos')}}">
                                    <i class=" ri-book-3-line "></i>
                                    <span> Cursos </span>
                                </a>
                            </li>
                            <li>
                                <a href="{{route('afiliado_configurar_site')}}">
                                    <i class="  ri-tools-fill "></i>
                                    <span> Configurações do site </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#ferramentas" aria-expanded="false" aria-controls="sidebarDashboards" class="side-nav-link">
                        <i class=" ri-tools-line "></i>
                        <span> Ferramentas </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="ferramentas">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="{{route('auto_ads')}}">
                                    <i class="ri-facebook-box-fill  "></i>
                                    <span> Anúncios automáticos </span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('instance.index') }}">
                                    <i class="ri-whatsapp-line "></i>
                                    <span> Zap Automático </span>
                                </a>
                               
                            </li>
                            <li>
                                <a href="{{ route('encurtar_link_lista') }}">
                                    <i class="ri-links-line  "></i>
                                    <span> Encurtador de links </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="side-nav-item">
                    <a href="{{ route('hotmart_leads', ['version' => null]) }}" class="side-nav-link">
                        <i class="ri-user-follow-fill"></i>
                        <span> Leads </span>
                    </a>
                </li>

                <li class="side-nav-item">
                    <a href="{{ route('novos_leads', ['version' => null]) }}" class="side-nav-link">
                        <i class="ri-user-follow-fill"></i>
                        <span> Leads (das aulas gratuitas) </span>
                    </a>
                </li>

                <li class="side-nav-item">
                    <a href="{{route('ranking')}}" class="side-nav-link">
                        <i class="ri-trophy-line"></i>
                        <span> Ranking </span>
                    </a>
                </li>


                @if (Auth::check() && Auth::user()->nivel_acesso == App\Models\User::NIVEL_ACESSO_ADMIN)
                <li class="side-nav-title">Administração</li>
                <li class="side-nav-item">
                    <a href="{{route('dashboard_adm')}}" class="side-nav-link">
                        <i class="uil-dashboard"></i>
                        <span> Dashboard </span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarPages" aria-expanded="true" aria-controls="sidebarPages" class="side-nav-link">
                        <i class="ri-slideshow-3-line  "></i>
                        <span> Cursos </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarPages">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="{{route('cursos.novo')}}">Novo curso</a>
                            </li>
                            <li>
                                <a href="{{route('adm_cursos_lista')}}">Lista dos cursos</a>
                            </li>
                            <li>
                                <a href="{{route('aulas_gratuitas_index')}}">Aulas Gratuitas</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a href="{{ route('combo.index') }}" class="side-nav-link">
                        <i class="ri-stack-line"></i>
                        <span> Combos </span>
                    </a>
                </li>
                
                
                @endif
            </ul>
            <!--- End Sidemenu -->

            <div class="clearfix"></div>
        </div>
    </div>
    <!-- ========== Left Sidebar End ========== -->

    <!-- ============================================================== -->
    <!-- Start Page Content Here -->
    <!-- ============================================================== -->

    <div class="content-page pt-3">
        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <div class="page-title-right">
                                <!--<form class="d-flex">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-light" id="dash-daterange">
                                        <span class="input-group-text bg-primary border-primary text-white">
                                                <i class="mdi mdi-calendar-range font-13"></i>
                                            </span>
                                    </div>
                                    <a href="javascript: void(0);" class="btn btn-primary ms-2">
                                        <i class="mdi mdi-autorenew"></i>
                                    </a>
                                    <a href="javascript: void(0);" class="btn btn-primary ms-1">
                                        <i class="mdi mdi-filter-variant"></i>
                                    </a>
                                </form>-->
                            </div>
                            <h4 class="page-title">@yield('titulo_pagina')</h4>
                        </div>
                    </div>
                </div>
                @if (session('success'))
                    <div class="alert alert-info alert-dismissible text-bg-info border-0 fade show" role="alert">
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        <strong>{{session('success')}}</strong>
                    </div>
                @elseif(session('error'))
                    <div class="alert alert-danger alert-dismissible text-bg-danger border-0 fade show" role="alert">
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        <strong>{{session('error')}}</strong>
                    </div>
                @elseif($errors->any())
                    <div class="alert alert-danger alert-dismissible text-bg-danger border-0 fade show" role="alert">
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div id="aviso"></div>
                @yield('content')
            </div>
            <!-- container $('#minha-jornada').modal('show')-->
            <a href="#" type="button" class="btn btn-primary text-white d-flex align-items-center rounded-pill btn-expand" data-bs-toggle="modal" data-bs-target="#minha-jornada" style="position: fixed; bottom: 25px; right: 15px; z-index:9;">
                <i class="me-1 ri-list-check" style="font-size: large;"></i>
                <span style="font-size: medium;"><strong>Minha Jornada</strong></span>
            </a>
            <div id="minha-jornada" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 99999999;">
                <div class="modal-dialog modal-sm modal-right" style="max-width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                        </div>
                        <div class="modal-body" style="padding-top: 350px;">
                            <div class="list-group list-group-flush">
                                @isset($minha_jornada)
                                    @foreach($minha_jornada as $tarefa)
                                        @php
                                            $link = $tarefa['link'];
                                            $titulo = $tarefa['titulo'];
                                            $texto = addslashes($tarefa['texto']);
                                            if($link){ $a = "onclick=\"video_de_ajuda('$link','$titulo', '$texto')\" data-bs-dismiss=\"modal\""; }else{$a='';}
                                            $icone = $tarefa['concluido'] ? "<i class=\"me-1 ri-checkbox-circle-fill text-success\"></i>" : "<i class=\"me-1  ri-youtube-fill  text-danger\"></i>";
                                            $text = $tarefa['concluido'] ? "text-secondary-emphasis" : "text-dark";
                                            $status = $tarefa['concluido'] ? "<span style='font-size: x-small' class=\"badge bg-success rounded-pill\">concluído</span>" : "<span style='font-size: x-small' class=\"badge bg-danger rounded-pill\">pendente</span>";
                                            //if($tarefa['aulas']){$icone='';}
                                        @endphp

                                            <div href="javascript:void(0);" class="py-2 border-bottom" aria-current="true" > 
                                                {!!$status!!}
                                                <a href="javascript:void(0);" {!!$a!!} class="{{$text}} mt-0 d-flex align-items-center" style="font-size: medium">{!!$icone!!}<span>{{$tarefa['titulo']}}</span></a>
                                                    @if($tarefa['aulas'])
                                                        @foreach ($tarefa['aulas'] as $dados_aula)
                                                            
                                                            @php
                                                                $link2 = $dados_aula['link'];
                                                                $titulo2 = $dados_aula['titulo'];
                                                                $texto2 = addslashes($dados_aula['texto']);
                                                                $text_cor = $tarefa['concluido'] ? "text-secondary-emphasis" : "text-danger";
                                                                $aula = "<a href=\"javascript:void(0);\" onclick=\"video_de_ajuda('$link2','$titulo2', '$texto2')\" data-bs-dismiss=\"modal\" class='$text my-2 ms-3 d-block' style='font-size: small;'><i class=\"me-1  ri-youtube-fill  $text_cor\"></i>$titulo2</a>";
                                                            @endphp
                                                    
                                                            {!!$aula!!}
                                                        @endforeach
                                                    @endif
                                            </div>
                                        
                                        @if(!$tarefa['concluido']) @php break; @endphp @endif
                                    @endforeach
                                @endisset
                                
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div>
        </div>
        <!-- content -->

<!-- VIDEO DE AJUDA -->

<div class="modal fade" id="modal_video_ajuda" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="videoModalLabel">Vídeo de Ajuda</h3>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="ratio ratio-16x9">
            <iframe id="videoFrame" src="" ></iframe>
          </div>
        </div>
        <div class="modal-body">
            <div id="videoText"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>
  
  

        <!-- Footer Start -->
        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <script>document.write(new Date().getFullYear())</script> © Portal Jovem Empreendedor
                    </div>
                    <div class="col-md-6">
                        <div class="text-md-end footer-links d-none d-md-block">
                            <a href="{{route('afiliados_termos')}}" target="_blank">Termos de Uso</a>
                            <a href="{{route('afiliados_politica')}}" target="_blank">Política de Privacidade</a>
                            <a href="#">Usuário nº {{ Auth::user()->id }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->

    </div>
    <!-- ============================================================== -->
    <!-- End Page content -->
    <!-- ============================================================== -->
    <div class="modal fade" id="modal_form_dominio" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" >Escolha no nome do seu site</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <form id="dominioForm" method="post" action="{{ route('alterar_dominio') }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Digite AQUI o nome do seu site</label>
                            <input type="text" id="dominio_digitar" name="dominio" class="form-control" value="{{ $dominioseco }}">
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-info" type="submit">Cadastrar meu site</button>
                        </div>
                    </form>
                    <div id="modal_form_dominio_feedback" class="mt-3"></div>
                    <div class="mb-3">
                        <h5>Veja como vai ficar o nome do seu site:</h5>
                        <p class="badge bg-info" style="font-size: medium" id="view_dominio">{{ Auth::user()->dominio }}</p>
                    </div>

                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="modal_form_user_whatsapp_atendimento" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" >Cadastre o seu WhatsApp de atendimento</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <form id="form_whatsapp_atendimento" method="post" action="{{ route('alterar_whatsapp_atendimento') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Digite o número do seu WhatsApp com o código do país.</label>
                            <input type="number" minlength="12" maxlength="14" required name="whatsapp_atendimento" class="form-control" value="{{ Auth::user()->whatsapp_atendimento }}">
                        </div>
                        <div class="mb-3">
                            <label for="select_whatsapp_atendimento_tempo" class="form-label">Quando seu WhatsApp vai aparecer na página?</label>
                            <select class="form-select" id="select_whatsapp_atendimento_tempo" name="whatsapp_atendimento_tempo" required>
                                <option value="0" @if(Auth::user()->whatsapp_atendimento_tempo == "0") selected @endif>Mostrar imediatamente</option>
                                <option value="60" @if(Auth::user()->whatsapp_atendimento_tempo == "60") selected @endif>Depois de 1 minutos</option>
                                <option value="120" @if(Auth::user()->whatsapp_atendimento_tempo == "120") selected @endif>Depois de 2 minutos</option>
                                <option value="180" @if(Auth::user()->whatsapp_atendimento_tempo == "180") selected @endif>Depois de 3 minutos</option>
                                <option value="240" @if(Auth::user()->whatsapp_atendimento_tempo == "240") selected @endif>Depois de 4 minutos</option>
                                <option value="300" @if(Auth::user()->whatsapp_atendimento_tempo == "300") selected @endif>Depois de 5 minutos</option>
                                <option value="600" @if(Auth::user()->whatsapp_atendimento_tempo == "600") selected @endif>Depois de 10 minutos</option>
                            </select>

                        </div>
                        <div class="mb-3">
                            <button class="btn btn-info" type="submit">Cadastrar / Editar</button>
                        </div>
                    </form>
                    <div class="modal_feedback mt-3"></div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <div class="alert alert-dismissible fade show position-fixed bottom-0 start-0 m-3 " role="alert" id="copyAlert" style="display: none; z-index: 99999; background-color: rgb(255, 96, 0);
  color: white;">
        <span id="alertMessage">Link copiado para a área de transferência!</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
<!-- END wrapper -->

<!-- Theme Settings -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="theme-settings-offcanvas">
    <div class="d-flex align-items-center bg-primary p-3 offcanvas-header">
        <h5 class="text-white m-0">Theme Settings</h5>
        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-0">
        <div data-simplebar class="h-100">
            <div class="card mb-0 p-3">

                <h5 class="my-3 font-16 fw-bold">Color Scheme</h5>

                <div class="colorscheme-cardradio">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-check card-radio">
                                <input class="form-check-input" type="radio" name="data-bs-theme" id="layout-color-light" value="light">
                                <label class="form-check-label p-0 avatar-md w-100" for="layout-color-light">
                                    <div id="sidebar-size">
                                            <span class="d-flex h-100">
                                                <span class="flex-shrink-0">
                                                    <span class="bg-light d-flex h-100 border-end flex-column p-1 px-2">
                                                        <span class="d-block p-1 bg-dark-lighten rounded mb-1"></span>
                                                        <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                        <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                        <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                        <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    </span>
                                                </span>
                                                <span class="flex-grow-1">
                                                    <span class="d-flex h-100 flex-column bg-white rounded-2">
                                                        <span class="bg-light d-block p-1"></span>
                                                    </span>
                                                </span>
                                            </span>
                                    </div>

                                    <div id="topnav-color" class="bg-white rounded-2 h-100">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="bg-light d-flex p-1 align-items-center border-bottom border-secondary border-opacity-25">
                                                    <span class="d-block p-1 bg-dark-lighten rounded me-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-auto"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-1"></span>
                                                </span>
                                                <span class="d-flex h-100 flex-column bg-white rounded-2">
                                                    <span class="bg-light d-block p-1"></span>
                                                </span>
                                            </span>
                                    </div>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Light</h5>
                        </div>

                        <div class="col-4">
                            <div class="form-check card-radio">
                                <input class="form-check-input" type="radio" name="data-bs-theme" id="layout-color-dark" value="dark">
                                <label class="form-check-label p-0 avatar-md w-100 bg-black" for="layout-color-dark">
                                    <div id="sidebar-size">
                                            <span class="d-flex h-100">
                                                <span class="flex-shrink-0">
                                                    <span class="bg-light d-flex h-100 flex-column p-1 px-2">
                                                        <span class="d-block p-1 bg-dark-lighten rounded mb-1"></span>
                                                        <span class="d-block border border-secondary border-opacity-25 border-3 rounded w-100 mb-1"></span>
                                                        <span class="d-block border border-secondary border-opacity-25 border-3 rounded w-100 mb-1"></span>
                                                        <span class="d-block border border-secondary border-opacity-25 border-3 rounded w-100 mb-1"></span>
                                                        <span class="d-block border border-secondary border-opacity-25 border-3 rounded w-100 mb-1"></span>
                                                    </span>
                                                </span>
                                                <span class="flex-grow-1">
                                                    <span class="d-flex h-100 flex-column">
                                                        <span class="bg-light d-block p-1"></span>
                                                    </span>
                                                </span>
                                            </span>
                                    </div>

                                    <div id="topnav-color">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="bg-light-lighten d-flex p-1 align-items-center border-bottom border-opacity-25 border-primary border-opacity-25">
                                                    <span class="d-block p-1 bg-dark-lighten rounded me-1"></span>
                                                    <span class="d-block border border-primary border-opacity-25 border-3 rounded ms-auto"></span>
                                                    <span class="d-block border border-primary border-opacity-25 border-3 rounded ms-1"></span>
                                                    <span class="d-block border border-primary border-opacity-25 border-3 rounded ms-1"></span>
                                                    <span class="d-block border border-primary border-opacity-25 border-3 rounded ms-1"></span>
                                                </span>
                                                <span class="bg-light-lighten d-block p-1"></span>
                                            </span>
                                    </div>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Dark</h5>
                        </div>
                    </div>
                </div>

                <div id="layout-width">
                    <h5 class="my-3 font-16 fw-bold">Layout Mode</h5>

                    <div class="row">
                        <div class="col-4">
                            <div class="form-check card-radio">
                                <input class="form-check-input" type="radio" name="data-layout-mode" id="layout-mode-fluid" value="fluid">
                                <label class="form-check-label p-0 avatar-md w-100" for="layout-mode-fluid">
                                    <div id="sidebar-size">
                                            <span class="d-flex h-100">
                                                <span class="flex-shrink-0">
                                                    <span class="bg-light d-flex h-100 border-end flex-column p-1 px-2">
                                                        <span class="d-block p-1 bg-dark-lighten rounded mb-1"></span>
                                                        <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                        <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                        <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                        <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    </span>
                                                </span>
                                                <span class="flex-grow-1">
                                                    <span class="d-flex h-100 flex-column rounded-2">
                                                        <span class="bg-light d-block p-1"></span>
                                                    </span>
                                                </span>
                                            </span>
                                    </div>

                                    <div id="topnav-color">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="bg-light d-flex p-1 align-items-center border-bottom border-secondary border-opacity-25">
                                                    <span class="d-block p-1 bg-dark-lighten rounded me-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-auto"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-1"></span>
                                                </span>
                                                <span class="bg-light d-block p-1"></span>
                                            </span>
                                    </div>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Fluid</h5>
                        </div>
                        <div class="col-4" id="layout-boxed">
                            <div class="form-check card-radio">
                                <input class="form-check-input" type="radio" name="data-layout-mode" id="layout-mode-boxed" value="boxed">
                                <label class="form-check-label p-0 avatar-md w-100 px-2" for="layout-mode-boxed">
                                    <div id="sidebar-size" class="border-start border-end">
                                            <span class="d-flex h-100">
                                                <span class="flex-shrink-0">
                                                    <span class="bg-light d-flex h-100 border-end flex-column p-1 px-2">
                                                        <span class="d-block p-1 bg-dark-lighten rounded mb-1"></span>
                                                        <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                        <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                        <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                        <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    </span>
                                                </span>
                                                <span class="flex-grow-1">
                                                    <span class="d-flex h-100 flex-column rounded-2">
                                                        <span class="bg-light d-block p-1"></span>
                                                    </span>
                                                </span>
                                            </span>
                                    </div>

                                    <div id="topnav-color" class="border-start border-end h-100">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="bg-light d-flex p-1 align-items-center border-bottom border-secondary border-opacity-25">
                                                    <span class="d-block p-1 bg-dark-lighten rounded me-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-auto"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-1"></span>
                                                </span>
                                                <span class="bg-light d-block p-1"></span>
                                            </span>
                                    </div>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Boxed</h5>
                        </div>

                        <div class="col-4" id="layout-detached">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-layout-mode" id="data-layout-detached" value="detached">
                                <label class="form-check-label p-0 avatar-md w-100" for="data-layout-detached">
                                        <span class="d-flex h-100 flex-column">
                                            <span class="bg-light d-flex p-1 align-items-center border-bottom ">
                                                <span class="d-block p-1 bg-dark-lighten rounded me-1"></span>
                                                <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-auto"></span>
                                                <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-1"></span>
                                                <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-1"></span>
                                                <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-1"></span>
                                            </span>
                                            <span class="d-flex h-100 p-1 px-2">
                                                <span class="flex-shrink-0">
                                                    <span class="bg-light d-flex h-100 flex-column p-1 px-2">
                                                        <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                        <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                        <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100"></span>
                                                    </span>
                                                </span>
                                            </span>
                                            <span class="bg-light d-block p-1 mt-auto px-2"></span>
                                        </span>

                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Detached</h5>
                        </div>
                    </div>
                </div>

                <h5 class="my-3 font-16 fw-bold">Topbar Color</h5>

                <div class="row">
                    <div class="col-4">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-topbar-color" id="topbar-color-light" value="light">
                            <label class="form-check-label p-0 avatar-md w-100" for="topbar-color-light">
                                <div id="sidebar-size">
                                        <span class="d-flex h-100">
                                            <span class="flex-shrink-0">
                                                <span class="bg-light d-flex h-100 border-end  flex-column p-1 px-2">
                                                    <span class="d-block p-1 bg-dark-lighten rounded mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-light d-block p-1"></span>
                                                </span>
                                            </span>
                                        </span>
                                </div>

                                <div id="topnav-color">
                                        <span class="d-flex h-100 flex-column">
                                            <span class="bg-light d-flex p-1 align-items-center border-bottom border-secondary border-opacity-25">
                                                <span class="d-block p-1 bg-dark-lighten rounded me-1"></span>
                                                <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-auto"></span>
                                                <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-1"></span>
                                                <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-1"></span>
                                                <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-1"></span>
                                            </span>
                                            <span class="bg-light d-block p-1"></span>
                                        </span>
                                </div>
                            </label>
                        </div>
                        <h5 class="font-14 text-center text-muted mt-2">Light</h5>
                    </div>

                    <div class="col-4" style="--ct-dark-rgb: 64,73,84;">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-topbar-color" id="topbar-color-dark" value="dark">
                            <label class="form-check-label p-0 avatar-md w-100" for="topbar-color-dark">
                                <div id="sidebar-size">
                                        <span class="d-flex h-100">
                                            <span class="flex-shrink-0">
                                                <span class="bg-light d-flex h-100 border-end  flex-column p-1 px-2">
                                                    <span class="d-block p-1 bg-primary-lighten rounded mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-dark d-block p-1"></span>
                                                </span>
                                            </span>
                                        </span>
                                </div>

                                <div id="topnav-color">
                                        <span class="d-flex h-100 flex-column">
                                            <span class="bg-dark d-flex p-1 align-items-center border-bottom border-secondary border-opacity-25">
                                                <span class="d-block p-1 bg-primary-lighten rounded me-1"></span>
                                                <span class="d-block border border-primary border-opacity-25 border-3 rounded ms-auto"></span>
                                                <span class="d-block border border-primary border-opacity-25 border-3 rounded ms-1"></span>
                                                <span class="d-block border border-primary border-opacity-25 border-3 rounded ms-1"></span>
                                                <span class="d-block border border-primary border-opacity-25 border-3 rounded ms-1"></span>
                                            </span>
                                            <span class="bg-light d-block p-1"></span>
                                        </span>
                                </div>
                            </label>
                        </div>
                        <h5 class="font-14 text-center text-muted mt-2">Dark</h5>
                    </div>

                    <div class="col-4">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-topbar-color" id="topbar-color-brand" value="brand">
                            <label class="form-check-label p-0 avatar-md w-100" for="topbar-color-brand">
                                <div id="sidebar-size">
                                        <span class="d-flex h-100">
                                            <span class="flex-shrink-0">
                                                <span class="bg-light d-flex h-100 border-end  flex-column p-1 px-2">
                                                    <span class="d-block p-1 bg-dark-lighten rounded mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-primary bg-gradient d-block p-1"></span>
                                                </span>
                                            </span>
                                        </span>
                                </div>

                                <div id="topnav-color">
                                        <span class="d-flex h-100 flex-column">
                                            <span class="bg-primary bg-gradient d-flex p-1 align-items-center border-bottom border-secondary border-opacity-25">
                                                <span class="d-block p-1 bg-light opacity-25 rounded me-1"></span>
                                                <span class="d-block border border-3 border opacity-25 rounded ms-auto"></span>
                                                <span class="d-block border border-3 border opacity-25 rounded ms-1"></span>
                                                <span class="d-block border border-3 border opacity-25 rounded ms-1"></span>
                                                <span class="d-block border border-3 border opacity-25 rounded ms-1"></span>
                                            </span>
                                            <span class="bg-light d-block p-1"></span>
                                        </span>
                                </div>
                            </label>
                        </div>
                        <h5 class="font-14 text-center text-muted mt-2">Brand</h5>
                    </div>
                </div>

                <div>
                    <h5 class="my-3 font-16 fw-bold">Menu Color</h5>

                    <div class="row">
                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-menu-color" id="leftbar-color-light" value="light">
                                <label class="form-check-label p-0 avatar-md w-100" for="leftbar-color-light">
                                    <div id="sidebar-size">
                                            <span class="d-flex h-100">
                                                <span class="flex-shrink-0">
                                                    <span class="bg-light d-flex h-100 border-end  flex-column p-1 px-2">
                                                        <span class="d-block p-1 bg-dark-lighten rounded mb-1"></span>
                                                        <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                        <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                        <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                        <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    </span>
                                                </span>
                                                <span class="flex-grow-1">
                                                    <span class="d-flex h-100 flex-column">
                                                        <span class="bg-light d-block p-1"></span>
                                                    </span>
                                                </span>
                                            </span>
                                    </div>

                                    <div id="topnav-color">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="bg-light d-flex p-1 align-items-center border-bottom border-secondary border-opacity-25">
                                                    <span class="d-block p-1 bg-dark-lighten rounded me-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-auto"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-1"></span>
                                                </span>
                                                <span class="bg-light d-block p-1"></span>
                                            </span>
                                    </div>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Light</h5>
                        </div>

                        <div class="col-4" style="--ct-dark-rgb: 64,73,84;">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-menu-color" id="leftbar-color-dark" value="dark">
                                <label class="form-check-label p-0 avatar-md w-100" for="leftbar-color-dark">
                                    <div id="sidebar-size">
                                            <span class="d-flex h-100">
                                                <span class="flex-shrink-0">
                                                    <span class="bg-dark d-flex h-100 flex-column p-1 px-2">
                                                        <span class="d-block p-1 bg-dark-lighten rounded mb-1"></span>
                                                        <span class="d-block border border-secondary rounded border-opacity-25 border-3 w-100 mb-1"></span>
                                                        <span class="d-block border border-secondary rounded border-opacity-25 border-3 w-100 mb-1"></span>
                                                        <span class="d-block border border-secondary rounded border-opacity-25 border-3 w-100 mb-1"></span>
                                                        <span class="d-block border border-secondary rounded border-opacity-25 border-3 w-100 mb-1"></span>
                                                    </span>
                                                </span>
                                                <span class="flex-grow-1">
                                                    <span class="d-flex h-100 flex-column">
                                                        <span class="bg-light d-block p-1"></span>
                                                    </span>
                                                </span>
                                            </span>
                                    </div>

                                    <div id="topnav-color">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="bg-light d-flex p-1 align-items-center border-bottom border-secondary border-primary border-opacity-25">
                                                    <span class="d-block p-1 bg-primary-lighten rounded me-1"></span>
                                                    <span class="d-block border border-secondary rounded border-opacity-25 border-3 ms-auto"></span>
                                                    <span class="d-block border border-secondary rounded border-opacity-25 border-3 ms-1"></span>
                                                    <span class="d-block border border-secondary rounded border-opacity-25 border-3 ms-1"></span>
                                                    <span class="d-block border border-secondary rounded border-opacity-25 border-3 ms-1"></span>
                                                </span>
                                                <span class="bg-dark d-block p-1"></span>
                                            </span>
                                    </div>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Dark</h5>
                        </div>
                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-menu-color" id="leftbar-color-brand" value="brand">
                                <label class="form-check-label p-0 avatar-md w-100" for="leftbar-color-brand">
                                    <div id="sidebar-size">
                                            <span class="d-flex h-100">
                                                <span class="flex-shrink-0">
                                                    <span class="bg-primary bg-gradient d-flex h-100 flex-column p-1 px-2">
                                                        <span class="d-block p-1 bg-light-lighten rounded mb-1"></span>
                                                        <span class="d-block border opacity-25 rounded border-3 w-100 mb-1"></span>
                                                        <span class="d-block border opacity-25 rounded border-3 w-100 mb-1"></span>
                                                        <span class="d-block border opacity-25 rounded border-3 w-100 mb-1"></span>
                                                        <span class="d-block border opacity-25 rounded border-3 w-100 mb-1"></span>
                                                    </span>
                                                </span>
                                                <span class="flex-grow-1">
                                                    <span class="d-flex h-100 flex-column">
                                                        <span class="bg-light d-block p-1"></span>
                                                    </span>
                                                </span>
                                            </span>
                                    </div>

                                    <div id="topnav-color">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="bg-light d-flex p-1 align-items-center border-bottom border-secondary">
                                                    <span class="d-block p-1 bg-dark-lighten rounded me-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-auto"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded ms-1"></span>
                                                </span>
                                                <span class="bg-primary bg-gradient d-block p-1"></span>
                                            </span>
                                    </div>

                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Brand</h5>
                        </div>
                    </div>
                </div>

                <div id="sidebar-size">
                    <h5 class="my-3 font-16 fw-bold">Sidebar Size</h5>

                    <div class="row">
                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidenav-size" id="leftbar-size-default" value="default">
                                <label class="form-check-label p-0 avatar-md w-100" for="leftbar-size-default">
                                        <span class="d-flex h-100">
                                            <span class="flex-shrink-0">
                                                <span class="bg-light d-flex h-100 border-end  flex-column p-1 px-2">
                                                    <span class="d-block p-1 bg-dark-lighten rounded mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-light d-block p-1"></span>
                                                </span>
                                            </span>
                                        </span>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Default</h5>
                        </div>

                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidenav-size" id="leftbar-size-compact" value="compact">
                                <label class="form-check-label p-0 avatar-md w-100" for="leftbar-size-compact">
                                        <span class="d-flex h-100">
                                            <span class="flex-shrink-0">
                                                <span class="bg-light d-flex h-100 border-end  flex-column p-1">
                                                    <span class="d-block p-1 bg-dark-lighten rounded mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-light d-block p-1"></span>
                                                </span>
                                            </span>
                                        </span>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Compact</h5>
                        </div>

                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidenav-size" id="leftbar-size-small" value="condensed">
                                <label class="form-check-label p-0 avatar-md w-100" for="leftbar-size-small">
                                        <span class="d-flex h-100">
                                            <span class="flex-shrink-0">
                                                <span class="bg-light d-flex h-100 border-end flex-column" style="padding: 2px;">
                                                    <span class="d-block p-1 bg-dark-lighten rounded mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-light d-block p-1"></span>
                                                </span>
                                            </span>
                                        </span>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Condensed</h5>
                        </div>

                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidenav-size" id="leftbar-size-small-hover" value="sm-hover">
                                <label class="form-check-label p-0 avatar-md w-100" for="leftbar-size-small-hover">
                                        <span class="d-flex h-100">
                                            <span class="flex-shrink-0">
                                                <span class="bg-light d-flex h-100 border-end flex-column" style="padding: 2px;">
                                                    <span class="d-block p-1 bg-dark-lighten rounded mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                    <span class="d-block border border-3 border-secondary border-opacity-25 rounded w-100 mb-1"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-light d-block p-1"></span>
                                                </span>
                                            </span>
                                        </span>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Hover View</h5>
                        </div>

                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidenav-size" id="leftbar-size-full" value="full">
                                <label class="form-check-label p-0 avatar-md w-100" for="leftbar-size-full">
                                        <span class="d-flex h-100">
                                            <span class="flex-shrink-0">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="d-block p-1 bg-dark-lighten mb-1"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-light d-block p-1"></span>
                                                </span>
                                            </span>
                                        </span>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Full Layout</h5>
                        </div>

                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidenav-size" id="leftbar-size-fullscreen" value="fullscreen">
                                <label class="form-check-label p-0 avatar-md w-100" for="leftbar-size-fullscreen">
                                        <span class="d-flex h-100">
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-light d-block p-1"></span>
                                                </span>
                                            </span>
                                        </span>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Fullscreen Layout</h5>
                        </div>
                    </div>
                </div>

                <div id="layout-position">
                    <h5 class="my-3 font-16 fw-bold">Layout Position</h5>

                    <div class="btn-group radio" role="group">
                        <input type="radio" class="btn-check" name="data-layout-position" id="layout-position-fixed" value="fixed">
                        <label class="btn btn-soft-primary w-sm" for="layout-position-fixed">Fixed</label>

                        <input type="radio" class="btn-check" name="data-layout-position" id="layout-position-scrollable" value="scrollable">
                        <label class="btn btn-soft-primary w-sm ms-0" for="layout-position-scrollable">Scrollable</label>
                    </div>
                </div>

                <div id="sidebar-user">
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <label class="font-16 fw-bold m-0" for="sidebaruser-check">Sidebar User Info</label>
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" name="sidebar-user" id="sidebaruser-check">
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- Vendor js -->
<script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/js/vendor.min.js')}}"></script>

<!-- Daterangepicker js -->
<script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/daterangepicker/daterangepicker.js')}}"></script>

<!-- App js -->
<script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/js/app.min.js')}}"></script>
<script>
    @if(!Auth::user()->dominio)

        document.addEventListener('DOMContentLoaded', function() {
            video_de_ajuda('8w186DSRuLE', 'Assista o vídeo completo', 'Assista o vídeo completo! Não tema comigo.');
        });
        
    
       // message('Você não têm cursos ativos!')
    @endif


/**vídeo de ajuda":**/
function video_de_ajuda(link, titulo, texto) {
    // Define o título do modal
    document.getElementById('videoModalLabel').textContent = titulo;
    
    // Define o vídeo no iframe
    document.getElementById('videoFrame').src = "https://www.youtube.com/embed/"+link;
    
    // Define o texto de descrição
    document.getElementById('videoText').innerHTML = texto;
    
    // Abre o modal
    $('#modal_video_ajuda').modal('show');
}
    /**para o vídeo de ajuda quando fechar o modal**/
    document.getElementById('modal_video_ajuda').addEventListener('hidden.bs.modal', function () {
        document.getElementById('videoFrame').src = '';
    });

    /**Ativando o Tooltip em todos os elementos com data-bs-toggle="tooltip":**/
    document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
    });

     /**ALERT":**/
    function showAlert(message) {
        // Define o texto do alerta
        var alertMessageElement = document.getElementById('alertMessage');
        alertMessageElement.textContent = message;

        // Mostra o alerta
        var alertElement = document.getElementById('copyAlert');
        alertElement.style.display = 'block';

        // Esconde o alerta após 3 segundos
        setTimeout(function() {
        alertElement.style.display = 'none';
        }, 3000);
    }

    function aviso(message) {
        var alertHtml = '<div class="alert alert-info alert-dismissible fade show" role="alert">' + message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        $('#aviso').html(alertHtml);
    }

    /**MODAL FOR ALTERAR O DOMINIO**/
    document.addEventListener('DOMContentLoaded', function() {
        var dominio_digitar = document.getElementById('dominio_digitar');
        var viewDominio = document.getElementById('view_dominio'); // Certifique-se de ter um elemento para exibir o domínio

        dominio_digitar.addEventListener('input', function() {
            var value = dominio_digitar.value;

            // Remover caracteres não permitidos
            var sanitizedValue = value.replace(/[^a-z0-9]/g, '');

            if (value !== sanitizedValue) {
                $('#modal_form_dominio_feedback').html('<div class="alert alert-danger"><strong>Caracteres não permitidos!</strong> Digite tudo minusculo sem espaço, sem pontuação ou caracteres especiais.</div>');
            } else {
                $('#modal_form_dominio_feedback').html('');
            }

            // Atualizar o valor do campo de entrada
            dominio_digitar.value = sanitizedValue;
            viewDominio.textContent = sanitizedValue + '.portalje.org';
        });
    });

    $(document).ready(function() {
        $('#dominioForm').on('submit', function(e) {
            e.preventDefault(); // Evita o envio normal do formulário
            var novo_dominio = $('#view_dominio').text();
            var formData = $(this).serialize(); // Serializa os dados do formulário

            $.ajax({
                url: $(this).attr('action'), // Usa a URL do atributo 'action' do formulário
                type: 'POST',
                data: formData,
                success: function(response) {
                    location.reload();
                    // Exibe mensagem de sucesso
                    //$('#modal_form_dominio_feedback').html('<div class="alert alert-success my-0">'+ response.success +'</div>' );
                    //$('.dominio_atual').text(novo_dominio);
                    //$('.href_dominio').attr('href', 'https://'+novo_dominio);
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    var errorHtml = '<div class="alert alert-danger">';

                    // Exibe os erros de validação
                    $.each(errors, function(key, value) {
                        errorHtml += '<p class="my-0">' + value[0] + '</p>';
                    });
                    errorHtml += '</div>';
                    $('#modal_form_dominio_feedback').html(errorHtml);
                }
            });
        });


        $('input[name="whatsapp_atendimento"]').on('input', function() {
            var value = $(this).val();

            // Remove todos os caracteres que não são números
            var sanitizedValue = value.replace(/\D/g, '');

            // Atualiza o campo com o valor sanitizado
            $(this).val(sanitizedValue);

            // Verifica o comprimento do valor sanitizado
            if (sanitizedValue.length < 12 || sanitizedValue.length > 14) {
                $(this)[0].setCustomValidity('O número deve ter entre 12 e 14 dígitos.');
            } else {
                $(this)[0].setCustomValidity('');
            }
        });


        $('#form_whatsapp_atendimento').on('submit', function(e) {
            e.preventDefault(); // Evita o envio normal do formulário
            var formData = $(this).serialize();
            $.ajax({
                url: $(this).attr('action'), // Usa a URL do atributo 'action' do formulário
                type: 'POST',
                data: formData,
                success: function(response) {
                    // Exibe mensagem de sucesso
                   // $('.modal_feedback').html('<div class="alert alert-success my-0">'+ response.success +'</div>');
                   location.reload();
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    var errorHtml = '<div class="alert alert-danger">';

                    // Exibe os erros de validação
                    $.each(errors, function(key, value) {
                        errorHtml += '<p class="my-0">' + value[0] + '</p>';
                    });
                    errorHtml += '</div>';
                    $('.modal_feedback').html(errorHtml);
                }
            });
        });
    });

    //TypeBoot para suporte aos Afiliados
    /*const typebotInitScript = document.createElement("script");
    typebotInitScript.type = "module";
    typebotInitScript.innerHTML = `import Typebot from 'https://cdn.jsdelivr.net/npm/@typebot.io/js@0.3.4/dist/web.js'

    Typebot.initBubble({
    typebot: "bruna-suporte",
    apiHost: "https://app.jovemempreendedorportal.com.br",
    theme: { button: { backgroundColor: "#0042DA" } },
    });
    `;
    document.body.append(typebotInitScript);
    //TypeBoot para suporte aos Afiliados*/

    //ATIVAR POPOVERS
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    })

    
    //FACEBOOK PIXEL APÓS O CARREGAMENTO COMPLETO DA PÁGINA
    window.onload = function() {
        // Carregar o Pixel do Facebook
        !function(f,b,e,v,n,t,s) {
            if(f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if(!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s);
        }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');

        // Inicializar o Pixel com seu ID
        fbq('init', '728470837780073');
        
        // Rastrear visualização de página
        fbq('track', 'PageView');
    };

    // Configuração básica para RTCPeerConnection
    const configuration = {
    iceServers: [
        { urls: 'stun:stun.l.google.com:19302' } // STUN do Google
    ]
    };

    // Criando uma nova conexão Peer-to-Peer
    const peerConnection = new RTCPeerConnection(configuration);

    // Adicionando event listeners para debugging
    peerConnection.onicecandidate = event => {
    if (event.candidate) {
        console.log('New ICE candidate: ', event.candidate);
    } else {
        console.log('All ICE candidates have been sent');
    }
    };

    peerConnection.onconnectionstatechange = () => {
    console.log('Connection state: ', peerConnection.connectionState);
    };

</script>

<noscript>
    <img height="1" width="1" style="display:none"
         src="https://www.facebook.com/tr?id=728470837780073&ev=PageView&noscript=1" />
</noscript>

@yield('scripts')
</body>

</html>