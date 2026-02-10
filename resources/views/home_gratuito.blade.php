<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Jovem Empreendedor</title>
    <link rel="icon" href="{{asset('/img/logo/logo-je-sm.png')}}" type="image/x-icon">
    <meta name="google-adsense-account" content="ca-pub-9796869151117705">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Meta description (descrição da página) -->
    <meta name="description" content="Qualifique-se com cursos completos e certificados. Aprenda habilidades valorizadas pelo mercado e conte com suporte especializado para transformar sua carreira. Inscreva-se e conquiste oportunidades reais com a ajuda dos nossos professores experientes!">
    <!-- Meta keywords (palavras-chave relacionadas ao conteúdo) -->
    <meta name="keywords" content="Curso, Certificado, Online, Profissionalização, Emprego, Educação, Carreira, Certificação, Portal Jovem Empreendedor, Programa Jovem Empreendedor, qualificação profissional, mercado de trabalho, capacitação profissional, curso com certificado, curso profissionalizante.">
    <!-- Meta author (autor do conteúdo) -->
    <meta name="author" content="Portal Jovem Empreendedor">
    <!-- Robots meta tag (controla indexação e rastreamento pelos bots de pesquisa) -->
    <meta name="robots" content="index, follow">
    <!-- Data de publicação -->
    <meta property="article:published_time" content="2024-10-24T08:00:00Z"> 

    <!-- Open Graph meta tags for Facebook and Instagram -->
    <meta property="og:title" content="Portal Jovem Empreendedor">
    <meta property="og:description" content="Qualifique-se com cursos completos e certificados. Aprenda habilidades valorizadas pelo mercado e conte com suporte especializado para transformar sua carreira. Inscreva-se e conquiste oportunidades reais com a ajuda dos nossos professores experientes!">
    <meta property="og:type" content="article"> <!-- Pode ser 'article', 'video', etc. -->
    <meta property="og:url" content="https://jovemempreendedor.org"> <!-- URL canônica -->
    <meta property="og:image" content="{{asset('img/home_page/certificadoNovo2.webp')}}"> <!-- URL da imagem de pré-visualização -->
    <meta property="og:image:alt" content="Portal Jovem Empreendedor">
    <meta property="og:site_name" content="Portal Jovem Empreendedor">
    <meta property="og:locale" content="pt_BR">

    <style>
        /* PADRÃO DO SITE */
        .color1{color: #505a64;}
        .color2{color: #007bff;}
        .color3{color: #28a745;}
        .color4{color: #fdfd88;}

        .bg-portal{background:  linear-gradient(150deg, #0056b3, #181b1e);}
        .bg-portal2{background: #002147;}
        .bg-portal3{background:  #f2f2f2;}

        /* SESSAO HERO */
        #selos_primeira_dobra img{
            max-height: 65px;
            
        }

        #selos_primeira_dobra, #selos_primeira_dobra .row, #selos_primeira_dobra .col-4{
            box-sizing: border-box;
        }
        .col-4 {
        flex: 0 0 auto;
        width: 33.33333333%;
        }

        .fw-bold {
        font-weight: 700 !important;
        }

        .mx-auto {
        margin-right: auto !important;
        margin-left: auto !important;
        }

        .mb-4 {
        margin-bottom: 1.5rem !important;
        }

        .text-uppercase {
        text-transform: uppercase !important;
        }

        .mt-3 {
        margin-top: 1rem !important;
        }

        .text-white {
        --bs-text-opacity: 1;
        color: rgba(var(--bs-white-rgb),var(--bs-text-opacity)) !important;
        }

        .hero {
        background-image: url('{{asset('img/padrao/fundo_certificado3.webp')}}');
        background-size: 500px; /* Tamanho original da imagem */
        background-repeat: repeat; /* Repetir tanto horizontalmente quanto verticalmente */
        background-position: center;
        position: relative;
        text-align: center;
        color: var(--accent-color);
        padding: 60px 20px;
        }

        .hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.9));
        z-index: 1;
        }

        .hero-content {
        position: relative;
        z-index: 2;
        }

        .hero h1 {
        font-size: 2rem;
        margin-bottom: 20px;
        line-height: 1.1;
        }

        .hero h2 {
        font-size: 1.5rem;
        margin-bottom: 20px;
        color: var(--secondary-color);
        line-height: 1.1;
        }

        .hero p {
        font-size: 1.1rem;
        margin-bottom: 20px;
        line-height: 1.3;
        }

        /* Estilizando o botão CTA */
        .btn-cta {
        display: inline-block;
        padding: 15px 30px;
        font-size: 1.2rem;
        font-weight: bold;
        color: #fff;
        text-decoration: none;
        background: linear-gradient(45deg, #007bff, #28a745);
        border: none;
        border-radius: 5px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        cursor: pointer;
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.5);
        }

        .btn-cta:hover {
        background: linear-gradient(45deg, #28a745, #34d058);
        }

        .top-banner {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #1338df;
            color: white;
            text-align: center;
            padding: 10px 0;
            font-size: 1rem;
            font-weight: bold;
            z-index: 9999; /* Mantém o banner acima de outros elementos */
            /*white-space: nowrap; /* Garante que o texto fique em uma linha */
            overflow: hidden; /* Evita overflow caso o texto seja maior que o contêiner */
        }
    
    /**APENAS MOBILE**/
    @media (max-width: 768px) {

    /**CARROSSEL EMPRESAS**/
    .carousel-container {
        overflow: hidden; /* Esconde qualquer coisa fora da área de visualização */
        width: 100%; /* Ocupa 100% da largura do container */
    }

    .carousel-logos {
        display: flex;
        justify-content: space-around;
        align-items: center;
        width: calc(80px * 7); /* Ajuste a largura total para incluir o número de logos duplicadas */
        animation: scroll 20s linear infinite; /* Tempo da animação ajustado para a nova largura */
    }

    .carousel-logos img {
        /*max-width: 80px; /* Defina o tamanho máximo das logos */
        /*height: auto;*/
        margin: 0 15px;
        opacity: 0.8;
        transition: transform 0.2s ease, opacity 0.2s ease;
    }

    .carousel-logos img:hover {
        transform: scale(1.1); /* Aumenta um pouco ao passar o mouse */
        opacity: 1;
    }

    /* Animação de rolagem contínua */
    @keyframes scroll {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(-50%); /* Mova metade da largura total */
        }
    }
}

/**APENAS DESKTOP**/
@media (min-width: 769px) {
    #headline{
        max-width: 50%;
        margin-right: auto !important;
        margin-left: auto !important;}

    

     /**CARROSSEL DESKTOP**/
    .carousel-logos img {
            max-width: 80px; /* Defina o tamanho máximo das logos */
            height: auto;
            margin: 0 15px;
            opacity: 0.8;
            transition: transform 0.2s ease, opacity 0.2s ease;
    }
}
    </style>
  </head>
  <body style="box-sizing: border-box;">
    @if($info['cidade'])
    <!-- Elemento fixo no topo -->
    <!--<div class="top-banner">
        <p class="mt-0" style="font-size: 1rem; margin-bottom: -7px;">Autorizado para <u class="text-uppercase">{{$info['cidade']}}.</u></p>-->
        <!--<p class="" style="font-size: 1rem;margin-bottom: -4px;">{{$info['v']}}</p>-->
        <!--<p class="mt-1 mb-0 color4" style="font-size: 0.8rem;">Inscrições terminam em <span class="countdown" style="font-size: 0.8rem;"></span></p>-->
        <!--<p class="mt-1 mb-0 color4" style="font-size: 0.8rem;">Inscrições terminam em <span class="countdown" style="font-size: 0.8rem;"></span></p>
    </div>-->
    @elseif($desconto_banner)
    <!-- Elemento fixo no topo -->
    <div class="top-banner" style="position: fixed;top: 0;left: 0;width: 100%;background-color: rgb(0, 0, 0);color: white;text-align: center;padding: 10px 0;font-size: 1rem;font-weight: bold;z-index: 9999;overflow: hidden;">
        <p class="my-0" style="font-size: 1.5rem;">Desconto especial de {{$desconto_banner}}%</p>
        <p class="my-0" style="font-size: 0.8rem;">Termina em <span class="color4 countdown"></span></p>
    </div>
    @endif 
    <!-- Seção Hero -->
    <section class="hero" style="box-sizing: border-box; justify-content: center !important; align-items: center !important; display: flex !important;@if($info['cidade'] OR $desconto_banner) padding-top: 100px; @endif">
        <div class="hero-content" style="text-align: center !important;">
        <img alt='Portal Jovem Empreendedor' style= "width: 150px; height: 36.3833px;" src="{{asset('img/home_page/logowhite.webp')}}" />
        @if($info['cidade'])
            <p class="mt-4 mb-0 text-uppercase" style="color: white; font-weight: 700 !important; text-shadow: 1px 1px 10px rgb(0, 0, 0);line-height: 1.1;font-size: 1.4rem;" class="mt-0 fs-4">27 Bolsas de Estudo liberadas para</p>
            <h1 class="mt-0 fw-bold" style="color: white; font-weight: 700 !important; text-shadow: 1px 1px 10px rgb(0, 0, 0);"><span class="text-uppercase" style="color: #fdfd88;font-size:2rem;">{{$info['cidade']}}</span></h1>
            
            <h2 id="headline" class="fw-bold mb-0 color4 mx-auto" style="color: #ffffff; text-shadow: 1px 1px 10px rgb(0, 0, 0);font-size: 1.3rem;"><strong>CURSOS GRATUITOS</strong> com opção de certificado.</h2>
            <p class="mt-1 mb-4" style="color: white; text-shadow: 1px 1px 10px rgb(0, 0, 0);line-height: 1.2;font-size: 1rem;" class="mt-0 fs-4">Escolha seu curso agora e comece a estudar de graça</p>
        @else
            <h2 id="headline" class="fw-bold mb-0 mt-4 color4 mx-auto" style="color: #ffffff; text-shadow: 1px 1px 10px rgb(0, 0, 0);font-size: 1.3rem;"><strong>CURSOS GRATUITOS</strong> com opção de certificado.</h2>
            <p class="mt-1 mb-4" style="color: white; text-shadow: 1px 1px 10px rgb(0, 0, 0);line-height: 1.2;font-size: 1rem;" class="mt-0 fs-4">Escolha seu curso agora e comece a estudar de graça!</p>
        @endif  
        <a href="#sessao_cursos" class="btn-cta text-uppercase" style="text-align: center !important;font-size: 1rem;">Escolher meu curso AGORA</a>
        
        <!-- Selos -->
        <div id="selos_primeira_dobra" class="mt-3 text-white" style="">
            <div class="row text-center d-flex align-items-center">
                <div class="col-4 col-md-2 offset-md-3">
                    <img src="{{asset('img/icons/mec2.webp')}}" style="width: 65px; height: 65px" class="img-fluid " alt="Imagem 1">
                    <!--<h5 class="mt-3">Título 1</h5>-->
                    
                </div>
                <div class="col-4 col-md-2">
                    <img src="{{asset('img/icons/certificado.png')}}" style="width: 65px; height: 65x" class="img-fluid " alt="Imagem 2">
                    
                   
                </div>
                <div class="col-4 col-md-2">
                    <img src="{{asset('img/icons/112mil.png')}}" style="width: 65px; height: 65x" class="img-fluid " alt="Imagem 3">                 
                    
                </div>
                
            </div>
            <div class="row mt-1">
                <div class="col-4 col-md-2 offset-md-3">
                    
                    <p style="font-size: xx-small; color: white;">Cursos autorizados pelo MEC ministrados por profissionais.</p>
                </div>
                <div class="col-4 col-md-2">
                    
                    
                    <p style="font-size: xx-small; color: white;">Certificação válido e reconhecido em todo o território nacional.</p>
                </div>
                <div class="col-4 col-md-2">
                    
                    
                    <p style="font-size: xx-small; : white;">112 mil alunos formados no Brasil e em +14 países.</p>
                </div>
            </div>
        </div>
        
        
        </div>
    </section>
    <!-- Empresas LOGOS -->
    <!--<section style="text-align: center !important; background-color: white; padding-top: 3rem !important;padding-bottom: 3rem !important;">
        <div class="container-fluid color1">
       
        <h3 class="text-center fw-bold my-0" style="font-weight: 700 !important;box-sizing: border-box;font-size: calc(1.3rem + .6vw);">
            Mais de <span style="font-weight: bolder;" class="color2">112 mil formados</span> em todo o Brasil e em +14 países.
        </h3>
        <p class="my-0 text-center fw-bold" style="font-size: small;">
            Nossos alunos já trabalham em empresas como:
        </p>
        
        
        <div class="carousel-container mt-4">
            <div class="carousel-logos">
                @php
                    $logo_empresas = [
                        [asset('img/empresas/nestle.webp'), 22.7167],
                        [asset('img/empresas/coca.webp'),25.9],
                        [asset('img/empresas/ambev.webp'),45],
                        [asset('img/empresas/natura.webp'),60.9],
                        [asset('img/empresas/pao.webp'),64.533],
                        [asset('img/empresas/rener.webp'),25.45],
                        [asset('img/empresas/unilever.webp'),80]
                    ];
                    
                @endphp
                @foreach ($logo_empresas as $logo_empresa)
                    
                    <img src="{{$logo_empresa[0]}}" alt="Empresas onde nossos alunos trabalham" class="logos" style="width: 80px; height: {{$logo_empresa[1]}}px;">
                @endforeach
            </div>
        </div>
        </div>
    </section>-->
    <!-- Por que escolher nossos cursos? -->
    <section id="beneficios" class="bg-portal3 color1 lazy-load" style="padding-bottom: 50px; padding-top: 50px;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h3 id="beneficios_h3" class="fw-bold">Como funciona?</h3>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-4 offset-lg-4">
                
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-transparent my-1">
                    <p class="mb-0"><i class="bi bi-mortarboard-fill me-2"></i> Você escolhe um curso gratuito</p>
                    </li>
                    <li class="list-group-item bg-transparent my-1">
                    <p class="mb-0"><i class="bi bi-clock-history me-2"></i> Faz as aulas no seu tempo</p>
                    </li>
                    <li class="list-group-item bg-transparent my-1">
                    <p class="mb-0"><i class="bi bi-patch-check-fill me-2"></i> Ao concluir, você tem a opção de solicitar seu certificado</p>
                    </li>

                </ul>
                @if($info['cidade'])
                <p class="mt-3 lead text-center">Válido apenas para os candidatos residentes em <strong>{{$info['cidade']}} ou região,</strong>.</p>
                @endif
                </div>
            </div>
            
        </div>
    </section>
    <!--<section id="beneficios" class="bg-portal3 color1 lazy-load" style="padding-bottom: 50px; padding-top: 50px;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 text-center">
                
                    <h3 id="beneficios_h3" class="fw-bold">Por que escolher nossos cursos?</h3>
                    <p class="lead text-center">Transforme seu futuro com uma profissão sólida e procurada no mercado!</p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-4 offset-lg-4">
                
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-transparent my-1">
                    <p class=" mb-0 fw-bold">
                        <i class="bi_icone bi bi-clipboard-check"></i> Habilidades mais buscadas
                    </p>
                    <p class="mb-0 lead"><strong>Aprenda as competências que as empresas mais valorizam.</strong> Ganhe habilidades práticas e atuais, projetadas para atender às demandas reais do mercado.</p>
                    </li>
                    <li class="list-group-item bg-transparent my-1">
                    <p class="mb-0 fw-bold">
                        <i class="bi_icone bi bi-award "></i> Certificado reconhecido
                    </p>
                    <p class="mb-0 lead"><strong>Com o nosso certificado, conquiste oportunidades reais.</strong> Tenha um certificado válido e reconhecido em todo o Brasil, valorizado por empresas em diversas áreas.

                    </p>
                    </li>
                    <li class="list-group-item bg-transparent my-1">
                    <p class="mb-0 fw-bold">
                        <i class="bi_icone bi bi-person-check "></i> Professores experientes
                    </p>
                    <p class="mb-0 lead"><strong>Ensino simples, com qualidade garantida.</strong> Aprenda com especialistas que dominam o assunto e explicam de forma prática, ideal mesmo para quem está começando.

                    </p>
                    </li>
                    <li class="list-group-item bg-transparent my-1">
                    <p class="mb-0 fw-bold">
                        <i class="bi_icone bi bi-headset "></i> Suporte completo
                    </p>
                    <p class="mb-0 lead"><strong>Acompanhamento e dicas práticas para seu sucesso.</strong> Durante o curso, você terá total apoio, com orientações práticas para se destacar em entrevistas e no ambiente de trabalho.</p>
                    </li>
                </ul>
                    
                </div>
            </div>
            
        </div>
    </section>-->
    <!-- Nossos Cursos Disponíveis para Você se Qualificar -->
    <section id="sessao_cursos" class="lazy-load py-5 color1">
        <div class="container-fluid">
            <h3 id="beneficios_h3" class="fw-bold text-center">Lista de cursos disponíveis</h3>
            <p class="lead text-center">Escolha o curso ideal para começar sua jornada profissional</p>

            <div id="lista_cursos" class="row mx-auto mt-3" style="max-width: 1100px;">
                @php
                    $notas = [
                        "4.9",
                        "5",
                        "5.0",
                        "4.8",
                        "4.8/5",
                        "5.5",
                        "4,8",
                        "4,7",

                    ];

                    

                    
                @endphp
                @foreach ($cursos as $curso)
                    @php

                        $min = 4.49;
                        $max = 4.97;
                        // Gera um número aleatório entre $min e $max com duas casas decimais
                        $randomFloat = $min + mt_rand() / mt_getrandmax() * ($max - $min);

                        // Formata o número para duas casas decimais
                        $randomFloat = number_format($randomFloat, 2, '.', '');

                        /**DEFINIR NÚMERO DE ALUNOS**/
                        if ($curso->numero_alunos<1000) {
                            $curso->numero_alunos += 1058;
                        }

                        /**LINKS DOS CHECKOUTS**/
                        $curso->link_checkout_completo = "https://".request()->getHost()."/".$curso->url.$info['parametros']."&ga=1";

                        /**DEFINIR SE PÁGINA SERÁ MOSTRAR OU NÃO**/
                        if (isset($curso->mostrar_curso) AND !$curso->mostrar_curso) {
                            $curso['mostrar_na_pagina'] = false; 
                        }

                        /*if(isset($curso->codigo_ref) AND $curso->codigo_ref){
                            $curso->link_checkout_completo = "https://go.hotmart.com/".$curso->codigo_ref;
                        }*/

                        /**DEFINIR SALÁRIO**/
                        if($curso->salario_maximo<2000){
                            $curso->salario_maximo+=837.53;
                        }
                                
                    @endphp
                    @if($curso['gratuito'])
                    <div class="lazy-load col-12 col-sm-6 col-lg-4 mb-2 mt-1 py-1 pb-3" style="border-bottom: 10px solid #cccccc5e;">
                        <div class="row row_curso_individual align-items-center d-flex">
                            <!-- Coluna da Imagem -->
                            <div class="col-6 position-relative">
                                <div class="img-overlay">
                                    <img  data-src="{{asset('/storage/'.$curso['capa_vertical'])}}"  class=" img-fluid w-100"alt="curso de {{$curso['titulo']}}" style="">
                                    <!-- Degradê e Título no rodapé da Imagem -->
                                    <div class="img-gradient-overlay">
                                        <div class="img-title text-white">
                                            <p class="m-0 text-center" style="font-size: 0.8rem; font-weight: bold;line-height: 1.1;">{{$curso['titulo']}}</p>
                                            <!--<p class="mt-1 mb-0 d-flex justify-content-center align-items-center color4" style="font-size: x-small">
                                                <i class="bi bi-star-fill  me-1"></i>
                                                <span>Nota {{$randomFloat}} de 5</span>
                                            </p>-->
                                            <p class="my-0 d-flex justify-content-center align-items-center color4" style="font-size: x-small">
                                                <i class="bi bi-people-fill  me-1"></i>
                                                <span>{{ $curso->numero_alunos }} alunos</span>
                                            </p>
                                        </div>
                                        
                                    </div>
                                    
                                </div>
                                <span class='position-absolute d-flex  align-items-center translate-middle badge bg-danger' style='left: 70% !important;top: 6% !important;font-size: x-small;'>
                                    <i class='bi bi-gift-fill me-1'></i>CURSO GRATUITO
                                    <span class='visually-hidden'>Carga Horária</span>
                                </span>
                            </div>

                            <!-- Coluna de Texto e Botão -->
                            <div class="col-6">
                                <p class="mb-2" style="font-size: 0.8rem;line-height: 1.3;font-weight: 700">{{$curso->titulo}}</p>
                                <p class="mb-2" style="font-size: 0.8rem;line-height: 1.3;font-weight: 400">{{ str_replace('"', '', $curso['headline']) }} <!--Salário pode chegar a R${{$curso->salario_maximo}}--></p>
                                <a onclick="@if(!$info['whatsapp']) fbq('track', 'ViewContent') @elseif(!$info['formulario']) fbq('track', 'Lead') @endif" 
                                data-cursoid="{{$curso->id}}" 
                                href="{{$curso->link_checkout_completo}}" class="mt-1 btn-cta btn-inscricao text-center px-1" style="font-size: small; padding-bottom: 10px; padding-top: 10px;">ACESSAR CURSO GRÁTIS</a> 
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
    <!--Depoimentos-->
    <section id="depoimentos" class="lazy-load py-5 bg-portal text-white">
        <div class="container-fluid">
            <div class="row">
                <div class="text-center col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-4 offset-lg-4">
                    <!-- Título da sessão -->
                    <h3 class="fw-bold ">Histórias de quem já participou</h3>
                    <p class="lead">Nada melhor do que ouvir diretamente de quem já passou pela mesma jornada e conquistaram novas oportunidades.</p>
                </div>
                <div class="col-sm-10 offset-sm-1 mx-auto" style="max-width: 1100px;">
                    @php
                        $depoimentos = [
                            'rejxwJ2lX-Q',
                            '1hekoAyPVRs',
                            'Mnn2yIAlhZk',
                            '9mmtunKAnMY',
                            'uQ5lB9r8ZlI',
                            'dMIxLKj35aU',
                            'gIV1MGief-0',
                            'X1IJZkVXgBw',
                            '1qWXa9F0qBw'
                        ];
                    @endphp
                    
                    <div id="video-depoimentos" class="mt-4 row flex-nowrap overflow-auto">
                        <!-- Vídeos de Depoimentos -->
                        @foreach($depoimentos as $depoimento)
                        <div class="col-sm-4 col-videos mb-4 lazy-load">
                            <div class="video-facade" data-video-id="{{ $depoimento }}" onclick="loadVideo(this)">
                                <img data-src="https://img.youtube.com/vi/{{ $depoimento }}/hqdefault.jpg" alt="Thumbnail {{ $depoimento }}" class=" img-fluid ">
                                <div class="play-button">
                                    <i class="bi bi-play-fill"></i> <!-- Ícone do Bootstrap -->
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="text-center col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-4 offset-lg-4">
                    <!-- Título da sessão -->
                    <p>Agora que você viu como nossos alunos transformaram suas vidas, está na hora de <strong>você dar o próximo passo.</strong></p>
                </div>
            </div>
            <div class="row">
                <div class="text-center col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-4 offset-lg-4">
                    <p class="lead">Faça parte de um grupo de alunos que já estão conquistando suas vagas e crescendo na carreira.</p>
                </div>
            </div>
            <div class="row d-flex align-items-center lazy-load">
                <div class="col-3 col-sm-1 offset-sm-4">
                    <img alt='Portal Jovem Empreendedor' class=" img-fluid " data-src="{{asset('img/home_page/garantia-7-dias.png')}}" style="width: 83px; height:83px">
                </div>
                <div class="col-9 col-sm-3">
                    <p>Tem dúvidas? Não se preocupe, o curso oferece uma <strong>garantia de 7 dias.</strong> Se não estiver satisfeito, devolvemos o seu dinheiro sem complicações.</p>
                </div>
                
            </div>
            <!--<div class="row mt-4">
                <div class="col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-4 offset-lg-4 text-center">
                    <p class="fw-bold mb-2" style="line-height: 1.3">Inscreva-se agora mesmo e comece agora sua jornada rumo ao sucesso!</p>
                    <a href="#sessao_cursos" class="btn-cta text-center text-uppercase btn-inscricao">Quero Me Inscrever Agora</a>
                    <p class="my-1 fw-bold color4" >Esta oferta acaba em <span class="countdown"></span></p>
                    <p class="mt-2 w-75 mx-auto fw-bold " style="font-size: x-small; color:#e9ecef;">Garanta seu acesso agora com 7 dias de garantia. Se não estiver satisfeito, devolvemos 100% do seu dinheiro!</p>
                </div>
            </div>-->
            
        </div>
    </section>
     <!-- Seção Certificado -->
     <section id="certificado" class="lazy-load container-fluid bg-white py-5 color1">
        <div class="row">
            <div class="text-center col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-4 offset-lg-4">
                <h3 class="fw-bold ">Certificado <strong >reconhecido e registrado</strong></h3>
                <p class='lead'>Você receberá um <strong> certificado de conclusão com validade em todo o Brasil.</strong> </p>
                        <p>Nosso certificado oferece uma série de benefícios para garantir sua credibilidade no mercado de trabalho.</p>
            </div>
            <div class="col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-4 offset-lg-4 lazy-load">
                <div class="card border-0">
                    <img class="rounded  img-fluid mx-auto" alt="Certificado" data-src="{{asset('img/home_page/certificadoNovo2.webp')}}" style="width: 404px; height: 285.633">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="mt-4 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-4 offset-lg-4">
                
                <div class="my-4">
                    <p class="mb-0 d-flex align-items-center">
                        <i class="bi_icone bi bi-patch-check-fill  me-2"></i>
                        <strong>Registro digital com QR Code:</strong>
                    </p>
                    <p class="mt-0 lead" style="font-size: small">O certificado possui assinatura digital e um QR code exclusivo, o que garante sua autenticidade e facilita a verificação online por recrutadores e empresas.</p>
                </div>
                
                <div class="mb-4">
                    <p class="mb-0 d-flex align-items-center">
                        <i class="bi_icone bi bi-bookmark-star-fill  me-2"></i>
                        <strong>Mesma validade de um curso presencial:</strong>
                    </p>
                    <p class="mt-0 lead" style="font-size: small">O certificado tem a mesma validade legal de cursos presenciais, reconhecido por empresas e instituições de ensino.</p>
                </div>
                
                <div class="">
                    <p class=" mb-0 d-flex align-items-center">
                        <i class="bi_icone bi bi-award-fill  me-2"></i>
                        <strong>Extensão universitária e concursos públicos:</strong>
                    </p>
                    <p class="mt-0 lead" style="font-size: small">Nosso certificado pode ser utilizado como extensão universitária e é aceito em concursos públicos, desde que esteja em conformidade com os requisitos do edital.</p>
                </div>
                
            </div>
        </div>
        <div class="row mt-4">
            <div class="text-center col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-4 offset-lg-4">
                <p class="lead ">Assim que você concluir o curso, terá acesso ao certificado 100% digital. Ele pode ser baixado e impresso ou anexado diretamente ao seu currículo e perfis profissionais.</p>
                <p><strong>Comprove sua qualificação e dê o próximo passo em sua carreira.</strong> 

                </p>
                <p>Garanta seu certificado ao final do curso e abra as portas para novas oportunidades.</p>
            </div>
        </div>
    </section>
    <!-- Seção Chame no WhatsApp -->
    <!--<section class="lazy-load py-5 text-center bg-portal3">
        <div class="container-fluid color1">

        <div class="row">
            <div class="col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-4 offset-lg-4">
                    
                    <h3 class="fw-bold ">Tem dúvidas? Fale com a gente!</h2>
                        
                    
                    <p class="lead text-center">
                        Ainda com dúvidas sobre o curso? <br>
                        Nosso time está pronto para te ajudar! Tire todas as suas dúvidas agora mesmo, é só chamar no WhatsApp.
                    </p>
                    
                    
                    <p class="mb-4">
                        Estamos aqui para te oferecer todo o suporte que você precisa. Seja para saber mais sobre o curso, formas de pagamento ou qualquer outra questão, fale diretamente com a nossa equipe.
                    </p>
                    
                    
                    <p class="fw-bold mb-2">
                        Responda suas dúvidas na hora e tenha a certeza de que está fazendo a melhor escolha para sua carreira!
                    </p>
                    
                    
                    <a href="https://wa.me/{{$info['whatsapp_atendimento']}}?text=Quero saber mais sobre os cursos do portal Jovem Empreendedor" target="_blank" class="btn-cta fw-bold ">
                        <i class="bi bi-whatsapp"></i> Fale com a gente no WhatsApp
                    </a>
                    <p class="mt-1 fw-bold" style="font-size: small">Clique no botão para conversar diretamente com a gente!</p>
            </div>
        </div>
        
        </div>
    </section>-->
    
    <!-- Seção Perguntas e respostas -->
    <section class="lazy-load container-fluid bg-white py-5">
        <div id="perguntas_e_respotas" class="row">
            <div class="col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-4 offset-lg-4 color1">
                <h3 class="fw-bold text-center">Perguntas e Respostas</h3>
                <p class="text-center lead">Ficou alguma dúvida? Clique nas perguntas abaixo.</p>
                <div class="mt-3 accordion accordion-flush" id="accordionFlushExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                Como funciona o curso
                            </button>
                        </h2>
                        <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">Após a confirmação do pagamento, você receberá por e-mail o acesso ao seu curso. O curso é totalmente online, composto por vídeo aulas acessíveis 24 horas por dia. Assim, você tem a liberdade de estudar quando e onde desejar.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                Quando começo a fazer o meu curso
                            </button>
                        </h2>
                        <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">O acesso ao curso é liberado após a confirmação do pagamento. Se o pagamento for feito por cartão de crédito, a liberação ocorre imediatamente. Você receberá um e-mail da HOTMART no próximo dia útil após o pagamento. Este e-mail pode estar na caixa de spam ou lixo eletrônico. Ao encontrá-lo, clique em "ACESSAR MEU PRODUTO" e crie uma senha para acessar o curso usando seu e-mail e a senha criada. Quando o pagamento for feito via PIX é necessário aguardar a confirmação do banco, que pode ser em alguns minutos ou em até 3 dias.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                                Quando posso fazer minha inscrição
                            </button>
                        </h2>
                        <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">Este valor promocional é limitado, e as inscrições podem ser encerradas a qualquer momento. Recomendamos que você faça sua inscrição o quanto antes para garantir sua vaga.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-heading04">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapse04" aria-expanded="false" aria-controls="flush-collapse04">
                                O certificado é reconhecido em todo o Brasil?
                            </button>
                        </h2>
                        <div id="flush-collapse04" class="accordion-collapse collapse" aria-labelledby="flush-heading04"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">SIM! Nossos cursos profissionalizantes, enquadrados como cursos livres, são autorizados a emitir certificados com base no Decreto N° 5.154, de 23 de Julho de 2004, Art. 1° e 3°, e de acordo com as normas do MEC pela Resolução CNE nº 04/99, Art 11º. Válidos em todo o território nacional, nossos certificados podem ser utilizados para enriquecer seu currículo e contar como horas extracurriculares em faculdades.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-heading05">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapse05" aria-expanded="false" aria-controls="flush-collapse05">
                                Como vou receber o certificado
                            </button>
                        </h2>
                        <div id="flush-collapse05" class="accordion-collapse collapse" aria-labelledby="flush-heading05"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">O certificado, em formato PDF, será disponibilizado ao final do curso para download e impressão. A parte frontal do certificado, contendo seu nome, é liberada após a conclusão da última aula. Exceção feita ao curso de Operador de Caixa, cujo certificado pode ser solicitado via WhatsApp, conforme informado no curso.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-heading06">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapse06" aria-expanded="false" aria-controls="flush-collapse06">
                                Este site é seguro?
                            </button>
                        </h2>
                        <div id="flush-collapse06" class="accordion-collapse collapse" aria-labelledby="flush-heading06"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">Sim! Nosso site é protegido por certificado de segurança, como indicado na URL. Além disso, o processamento de pagamentos é realizado por uma empresa especializada que garante a segurança do valor pago por 7 dias, permitindo o reembolso em caso de problemas com o curso. Nossa empresa, reconhecida e ativa há vários anos, tem uma forte presença nas redes sociais, evidenciando a realização de diversos projetos em todo o Brasil.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-heading07">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapse07" aria-expanded="false" aria-controls="flush-collapse07">
                                Há testes ou provas?
                            </button>
                        </h2>
                        <div id="flush-collapse07" class="accordion-collapse collapse" aria-labelledby="flush-heading07"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">Sim! Existem avaliações de recapitulação ao longo do curso. Não se preocupe, pois é possível refazer as avaliações mais de uma vez, se necessário.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-heading08">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapse08" aria-expanded="false" aria-controls="flush-collapse08">
                                Quais os requisitos para fazer o curso?
                            </button>
                        </h2>
                        <div id="flush-collapse08" class="accordion-collapse collapse" aria-labelledby="flush-heading08"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body"> Os cursos do Portal Jovem Empreendedor são acessíveis a pessoas de todas as idades e níveis de escolaridade. Mesmo para cursos em profissões que exigem ensino médio completo, é possível se matricular e iniciar o aprendizado enquanto conclui seus estudos.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
   

    <!-- Rodapé -->
    <footer id="rodape" class="text-white py-5 bg-dark" >
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-4 offset-lg-4">
                <div class="row">
                    <!-- Logo -->
                    <div class="col-sm-4 mb-2 text-center text-md-start lazy-load">
                        <img data-src="{{asset('img/home_page/logowhite.png')}}" alt="Logo" class=" img-fluid  mb-2 " style="width: 150px; height: 36.36px ">
                        <!--<p style="font-size: small">Cnpj: 21.798.932/0001-00</p>-->
                    </div>

                    <!-- Redes Sociais -->
                    <div class="col-sm-4 mb-3 text-center">
                        <a href="https://www.instagram.com/jovemempreendedororg" target="_blank" class="text-white me-3" style="text-decoration: none;" aria-label="Siga-nos no Instagram">
                        <i class="bi bi-instagram" style="font-size: 1.5rem;"></i>
                        </a>
                        <a href="https://www.youtube.com/@PortalJovemEmpreendedor" target="_blank" class="text-white me-3"  style="text-decoration: none;" aria-label="Siga-nos no Youtube">
                        <i class="bi bi-youtube" style="font-size: 1.5rem;"></i>
                        </a>
                        <a href="https://www.facebook.com/portaljovemempreendedoroficial" target="_blank" class="text-white"  style="text-decoration: none;" aria-label="Siga-nos no Facebook">
                        <i class="bi bi-facebook" style="font-size: 1.5rem;"></i>
                        </a>
                    </div>

                    <!-- Links de Política -->
                    <div class="col-sm-4 text-center text-md-end">
                        <div class="text-center text-md-end">
                            <small>
                                <div class="row">
                                    <a target="_blanck" href="https://jovemempreendedor.org/afiliados/politica" class="politicas text-white me-3 col-sm-12">Política de Privacidade</a>
                                    <a  target="_blanck" href="https://jovemempreendedor.org/afiliados/termos" class="politicas text-white me-3 col-sm-12">Termos de Uso</a>
                                    <a  target="_blanck" href="https://jovemempreendedor.org/afiliados/termos" class="text-white col-sm-12">Uso de Cookies</a>
                                </div>
                                
                            </small>
                            </div>
                            
                        
                    </div>
                </div>
            </div>
            
          </div>
        </div>
    </footer>
    <!-- Modal com Formulário -->
    <div class="modal fade" id="inscricaoModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
            <div class="modal-header">
                        @if ($info['whatsapp'])
                        <h5 class="text-uppercase text-secondary modal-title fs-4 fw-bolder" id="modalLabel" style="line-height: 1.3;">Descubra se você é elegível para receber uma bolsa parcial!</h5>
                        @else
                        <h5 class="modal-title" id="modalLabel">Complete sua Inscrição e Comece Sua Jornada de Sucesso!</h5>
                        @endif
                
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p style="line-height: 1.3;font-weight: 400;">Antes de prosseguir, precisamos de algumas informações básicas.</p>
                
                <!-- Formulário -->
                <form id="inscricaoForm" action="{{ route('lead_whatsapp') }}" method="POST">
                    @csrf
                    <div class="mb-3">                    
                        <input type="text" class="form-control" id="input_lead_nome" name="nome" placeholder="Digite seu nome completo" style="height: 50px;font-size: large;" required>
                        <div class="form-text mt-0 ms-2" style="font-size: 0.7rem;">O nome que será impresso no certificado.</div>
                    </div>
                    
                    <!--Campos ocultos-->
                    <input id="input_lead_user_id" type="hidden" name="user_id" value="{{$info['user_id']}}">
                    <input id="input_lead_curso_id" type="hidden" name="curso_id" value="">
                    <input id="input_lead_href" type="hidden" name="link" value="">
                    <input id="input_lead_origem" type="hidden" name="origem" value="whatsapp">
                    <input id="input_lead_cidade" type="hidden" name="cidade" value="@if($info['cidade']) {{$info['cidade']}} @endif">

                    <div class="mb-3">    
                        <input minlength="13" id="input_lead_telefone" type="tel" name="telefone" class="form-control input_telefone" placeholder="Digite seu WhatsApp" title="Digite seu número de telefone aqui" style="height: 50px;font-size: large;" required>
                        <div class="form-text mt-0 ms-2" style="font-size: 0.7rem;">Receba suporte e atualizações diretamente no WhatsApp.</div>
                    </div>
                    
                    <button type="submit" class="btn-cta w-100">
                        @if ($info['whatsapp'])
                        <i class="bi bi-whatsapp"></i> Saiba mais no WhatsApp
                        @else
                            Concluir Inscrição e Garantir Minha Vaga
                        @endif
                    </button>
                </form>
            </div>
            <div class="modal-footer">
                <p class="text-center my-0" style="font-size: small;line-height: 1.3;">Não se preocupe! Suas informações estão seguras conosco. Vamos entrar em contato em breve para confirmar sua inscrição e fornecer os detalhes do curso.</p>
            </div>
            </div>
        </div>
    </div>
    <div id="iframe-container" style="display: none; visibility: hidden;">
        <!-- O iframe será adicionado aqui -->
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modal_overlay" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="fw-bolder fs-2 modal-body text-center d-flex justify-content-center align-items-center" style="height: 100vh;">
                Aguarde, estamos transferindo para um de nossos consultores...
            </div>
        </div>
        </div>
    </div>
    <script async>
 
     

        /**CRONOMETRO**/
        // Obter a data atual
        var today = new Date();

        // Ajustar a hora para 23:59:59 do dia atual
        today.setHours(23, 59, 59, 999);

        // Obter o timestamp da data ajustada
        var countDownDate = today.getTime();

        var countdownFunction = setInterval(function() {
            var now = new Date().getTime();
            var distance = countDownDate - now;

            // Cálculos para horas, minutos e segundos
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Seleciona todos os elementos com a classe 'countdown'
            var countdownElements = document.querySelectorAll('.countdown');

            // Itera sobre cada elemento e atualiza o conteúdo
            countdownElements.forEach(function(element) {
            element.innerHTML = hours + "<span style='font-size: small'>horas</span> " 
                                + minutes + "<span style='font-size: small'>min</span> " 
                                + seconds + "<span style='font-size: small'>seg</span>";
            });


            // Se a contagem terminar
            if (distance < 0) {
                clearInterval(countdownFunction);
                countdownElements.forEach(function(element) {
                element.innerHTML = "PROMOÇÃO ENCERRADA";
                });
            }
        }, 1000);

        /**ALTERAR LAYOUT DOS VIDEOS LADO A LADO**/
        function adjustClasses() {
            var videodepoimentos = document.getElementById('video-depoimentos');
            //var videodentro = document.getElementById('video_dentro_curso');
            if (window.innerWidth >= 768) { // Desktop breakpoint
                videodepoimentos.classList.remove('flex-nowrap', 'overflow-auto');
                //videodentro.classList.remove('flex-nowrap', 'overflow-auto');
            } else {
                videodepoimentos.classList.add('flex-nowrap', 'overflow-auto');
                //videodentro.classList.add('flex-nowrap', 'overflow-auto');
            }
        }

        // Run on page load
        window.addEventListener('load', adjustClasses);

        // Run on window resize
        window.addEventListener('resize', adjustClasses);
        
        
        function loadVideo(element) {
                if (element.querySelector('iframe')) {
                    return; // Se o iframe já existe, não faz nada
                }

            var videoId = element.getAttribute('data-video-id');

            // Substitui a facade pela incorporação do vídeo do YouTube com Plyr
            var playerHTML = `
                <div class="plyr__video-embed player">
                    <iframe
                        src="https://www.youtube.com/embed/${videoId}?autoplay=1&origin=${window.location.origin}&modestbranding=1&rel=0&iv_load_policy=3&playsinline=1&controls=1"
                        allow="autoplay; fullscreen"
                        allowfullscreen
                        frameborder="0">
                    </iframe>
                </div>
            `;

            element.innerHTML = playerHTML;

            // Inicializa o Plyr para o vídeo carregado
            const player = new Plyr('.player', {
                controls: ['play', 'progress', 'current-time', 'mute', 'volume',]
            });

            element.removeEventListener('click', loadVideo);
        }


        /* ####################################
         * APÓS CARREGAMENTO PARCIAL DA PÁGINA
         * ##################################*/ 
         document.addEventListener('DOMContentLoaded', function() {

            // Carregar Bootstrap CSS
            var bootstrapCSS = document.createElement('link');
            bootstrapCSS.rel = 'stylesheet';
            bootstrapCSS.href = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css';
            bootstrapCSS.integrity = 'sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH';
            bootstrapCSS.crossOrigin = 'anonymous';
            document.head.appendChild(bootstrapCSS);
            
            // Carregar jquery
            var jquery = document.createElement('script');
            jquery.src = 'https://code.jquery.com/jquery-3.7.1.min.js';
            jquery.crossOrigin = 'anonymous';
            jquery.async = true; 
            document.head.appendChild(jquery);

            // CARREGAR DIVS DE CURSOS COM LAZY LOAD
            const lazyDivs = document.querySelectorAll(".lazy-load");

            // Configuração do Intersection Observer para monitorar as divs
            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("fade-in"); // Adiciona o efeito de fade
                        const img = entry.target.querySelector("img"); // Seleciona a imagem dentro da div

                        // Carrega a imagem se tiver data-src
                        if (img && img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute("data-src");
                        }
                        
                        observer.unobserve(entry.target); // Para de observar após exibir
                    }
                });
            }, { 
                rootMargin: "0px 0px 5% 0px" // Exibe quando 30% da div está visível
            });

            // Observa cada <div> com a classe 'lazy-load'
            lazyDivs.forEach((div) => observer.observe(div));

            // IFRAME DO COOKIE
            @if(isset($curso->link_checkout_completo))
            const iframe = document.createElement('iframe');
            iframe.src = '{{$curso->link_checkout_completo}}'; // URL do iframe
            iframe.width = '600'; // Largura do iframe
            iframe.height = '400'; // Altura do iframe
            iframe.style.border = '0'; // Estilo opcional
            
            // Adiciona o iframe ao contêiner
            document.getElementById('iframe-container').appendChild(iframe);
            @endif
        });

        /* ####################################
        * APÓS CARREGAMENTO COMPLETO DA PÁGINA
        * ##################################*/ 
        window.onload = function() {

            // ICONES DO BOOTSTRAP
            var bootstrapIconsCSS = document.createElement('link');
            bootstrapIconsCSS.rel = 'stylesheet';
            bootstrapIconsCSS.href = 'https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css';
            document.head.appendChild(bootstrapIconsCSS);

            // Carregar Bootstrap JS
            var bootstrapJS = document.createElement('script');
            bootstrapJS.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js';
            bootstrapJS.integrity = 'sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz';
            bootstrapJS.crossOrigin = 'anonymous';
            bootstrapJS.async = true; 
            document.head.appendChild(bootstrapJS);

            // Carregar Plyr CSS
            var plyrCSS = document.createElement('link');
            plyrCSS.rel = 'stylesheet';
            plyrCSS.href = 'https://cdn.plyr.io/3.7.8/plyr.css';
            document.head.appendChild(plyrCSS);

            // Carregar Plyr JS
            var plyrJS = document.createElement('script');
            plyrJS.src = 'https://cdn.plyr.io/3.7.8/plyr.js';
            plyrJS.async = true; 
            document.head.appendChild(plyrJS);

            // Carregar inputmask
            var inputmask = document.createElement('script');
            inputmask.src = 'https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js';
            inputmask.async = true; 
            document.head.appendChild(inputmask);

            // Máscara do formulario para o WhatsApp
            inputmask.onload = function() {            
                var telefoneInputs = document.querySelectorAll('.input_telefone');            
                // Aplica a máscara a cada campo de entrada
                telefoneInputs.forEach(function(input) {
                    Inputmask({
                        mask: ["(99) 9999-9999", "(99) 99999-9999"],
                        keepStatic: true
                    }).mask(input);
                });
            };  
            //MODAL FORMULÁRIO WHATSAPP
            
            @if($info['formulario'])
            // Quando o Bootstrap JS for carregado, executa a função que usa o modal
            bootstrapJS.onload = function() {
                // Selecione todos os links com a classe btn-inscricao
                const links = document.querySelectorAll('.btn-inscricao');
                window.modal = new bootstrap.Modal(document.getElementById('inscricaoModal'));

                links.forEach(link => {
                    link.addEventListener('click', function (event) {
                        // Previne o comportamento padrão (abrir o link)
                        event.preventDefault();

                        // PEGAR O CURSO ID E COLOCAR NO FORMULÁRIO
                        window.datacursoidGlobal = link.getAttribute('data-cursoid');
                        $('#input_lead_curso_id').val(window.datacursoidGlobal);

                        //SETAR O LINK, SEJA DO WHATSAPP OU DO CHECKOUT
                        window.hrefGlobal = link.getAttribute('href');
                        $('#input_lead_href').val(window.hrefGlobal);


                        // Abra o modal
                        window.modal.show();
                    });
                });
            };
            @endif

            document.getElementById("inscricaoForm").addEventListener("submit", function(e) {
                e.preventDefault(); // Impede o envio normal do formulário
                
                var form = e.target;
                var formData = new FormData(form);
                
                // Captura o valor do campo 'nome' e o valor do campo 'href' 
                var nome = document.getElementById("input_lead_nome").value;
                var redirectLink = document.getElementById("input_lead_href").value;
                
                // Substitui a expressão {nome} pelo valor do campo de nome
                redirectLink = redirectLink.replace("{nome}", encodeURIComponent(nome));
                window.location.href = redirectLink;
                
                fbq('track', 'Lead');
                
                // Envia o formulário via fetch (pode ser com ou sem resposta)
                fetch(form.action, {
                    method: "POST",
                    body: formData
                })
                .then(response => {
                    // Após o envio bem-sucedido, redireciona para o link alterado
                    //window.location.href = redirectLink;
                })
                .catch(error => {
                    console.error('Erro no envio:', error);
                    // Em caso de erro, você ainda pode redirecionar
                    //window.location.href = redirectLink;
                });
            });

            

            //REDIRECIONAMENTO PAR AO LINK
            /*document.getElementById('botaoEnviar').addEventListener('click', function() {
                // Mostrar o overlay de espera
                window.modal_carregando = new bootstrap.Modal(document.getElementById('modal_overlay'));
                window.modal_carregando.show();
            });*/

            

            /* Código base para dois Pixels do Facebook*/
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');

            // Inicializa o primeiro pixel
            fbq('init', '419961365827965'); // Substitua PIXEL_ID_1 pelo ID do primeiro Pixel
            // Inicializa o segundo pixel
            fbq('init', '948808649224691'); // Substitua PIXEL_ID_2 pelo ID do segundo Pixel

            @if($info['meta_pixel_id'] AND $info['meta_pixel_id']!='948808649224691')
                fbq('init', '{{$info['meta_pixel_id']}}'); 
            @endif

            // Rastreia a visualização de página para ambos os pixels
            fbq('track', 'PageView');
        };   
    </script>
    <!-- Código base para dois Pixels do Facebook -->
    <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=419961365827965&ev=PageView&noscript=1"
        /></noscript>
        <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=948808649224691&ev=PageView&noscript=1"
        /></noscript>
        @if($info['meta_pixel_id'] AND $info['meta_pixel_id']!='948808649224691')
        <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id={{$info['meta_pixel_id']}}&ev=PageView&noscript=1"
        /></noscript>
        @endif
        <!-- Fim do Código base para dois Pixels do Facebook -->
    <style>
        .img-overlay {
            right: -7px;
            position: relative;
            overflow: hidden;
            width: 100%;
            height: 100%px;
            border-radius: 4px; /* Bordas arredondadas */
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3); /* Sombra na imagem */
        }

        .img-gradient-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.1));
            display: flex;
            align-items: flex-end;
            padding: 15px;
            box-sizing: border-box;
            border-radius: 4px; /* Bordas arredondadas no degradê */
        }

        .img-title {
            text-align: left;
            font-size: 1.2rem;
            color: white;
            width: 100%;
            word-wrap: break-word;
            margin: 0;
        }

        .items_curso{
            font-size: x-small;
            border-radius: 4px;
            padding-bottom: 4px;
            padding-top: 4px;
            padding-left: 8px;
        }

       
        #fundo_certificado {
            background-image: url("{{asset('img/padrao/fundo_certificado2.webp')}}"); /* Coloque a URL da imagem desejada */
            background-size: 500px; /* Tamanho original da imagem */
            background-repeat: repeat; /* Repetir tanto horizontalmente quanto verticalmente */
            background-position: center;
            position: relative;
        }

        
        #fundo_certificado .bg-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);  
            z-index: 0;
        }
       

        #rodape a{
            text-decoration: none;
        }

        #professor p{
            margin-top: 0;
            margin-bottom: 0;
        }

        ul {
        list-style-type: none; /* Remove os marcadores da lista */
        padding: 0;
        margin: 0;
        }

        ul li {
            padding: 8px 0; /* Espaçamento vertical entre os itens */
            border-bottom: 1px solid #e0e0e0; /* Linha fina abaixo de cada item */
        }

        ul li:last-child {
            border-bottom: none; /* Remove a linha do último item */
        }

        .promo-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .old-price {
            text-decoration: line-through;        
            color: #ff6666;
        }

        .new-price {
            font-size: 24px;
            font-weight: bold;
            color: #fff;
            margin-top: -10px;
            margin-bottom: 0;
        }

        .countdown {
            font-size: 18px;
            font-weight: bold;
            margin-top: -7px;
            margin-bottom: 0;
        }

        .video-facade {
            position: relative;
            cursor: pointer;
            overflow: hidden; /* Garante que o conteúdo extra seja cortado */
            height: 300px;
        }

        .video-facade img {
            width: 100%;
            height: 85%;
            object-fit: cover; /* Faz o crop da imagem nas extremidades verticais */
        }

        .play-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .play-button i {
            font-size: 30px;
            color: white;
        }

        .modal{
            z-index: 99999;
        }

        #beneficios p{
            color: #505a64;
        }

        #beneficios .lead{
            font-size: 1rem;
        }

        .bi_icone{
            background-color: #808080c4;
            color: #fff;
            font-size: 0.9rem;
            padding: 7px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .lazy-load {
            opacity: 0;
            transition: opacity 0.5s ease-in; /* Animação de fade-in */
        }

        .fade-in {
            opacity: 1; /* Torna o elemento visível com o efeito */
        }

        /**APENAS MOBILE**/
        @media (max-width: 768px) {

            /**TAMANHO DA FOTO DE CAPA DOS VÍDEOS**/
            .video-facade {
                position: relative;
                cursor: pointer;
                overflow: hidden; /* Garante que o conteúdo extra seja cortado */
                height: 195px;
            }

            .col-videos {
                width: 85%;
            }

            

            .politicas{
                margin-bottom: 7px
            }

            .promo-footer.show {
                opacity: 1;
                visibility: visible;  
            }

            .img-overlay img{
                width: 164px; 
                height: 237.117px;
            }
        }
        /**APENAS DESKTOP**/
        @media (min-width: 769px) {
            #lista_cursos{
                margin-right: auto !important;
                margin-left: auto !important;
                width: 80%;
            }

            #row_curso_individual{
                padding-left: 15px;
                padding-right: 15px;
            }
        }
    </style>
  </body>
</html>