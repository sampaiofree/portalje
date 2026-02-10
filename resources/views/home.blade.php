@extends('layouts.public')

@section('head')

  <title>Cursos Profissionalizantes Online - Mude sua Vida Hoje!</title>
  <meta name="description" content="Qualifique-se com cursos completos e certificados. Aprenda habilidades valorizadas pelo mercado e conte com suporte especializado para transformar sua carreira. Inscreva-se e conquiste oportunidades reais com a ajuda dos nossos professores experientes!">
    <meta name="keywords" content="Curso, Certificado, Online, Profissionalização, Emprego, Educação, Carreira, Certificação, Portal Jovem Empreendedor, Programa Jovem Empreendedor, qualificação profissional, mercado de trabalho, capacitação profissional, curso com certificado, curso profissionalizante.">
    <meta name="author" content="Portal Jovem Empreendedor">
    <meta name="robots" content="index, follow">
    <meta property="article:published_time" content="2024-10-24T08:00:00Z">
    <meta property="og:title" content="Portal Jovem Empreendedor">
    <meta property="og:description" content="Qualifique-se com cursos completos e certificados. Aprenda habilidades valorizadas pelo mercado e conte com suporte especializado para transformar sua carreira. Inscreva-se e conquiste oportunidades reais com a ajuda dos nossos professores experientes!">
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://jovemempreendedor.org">
    <meta property="og:image" content="{{asset('img/home_page/certificadoNovo2.webp')}}">
    <meta property="og:image:alt" content="Portal Jovem Empreendedor">
    <meta property="og:site_name" content="Portal Jovem Empreendedor">
    

@endsection

@section('content')
  <!-- Hero Section -->
<section class="hero">
    <div class="container">
        <img alt='Portal Jovem Empreendedor' style="max-width: 200px; margin-bottom: 20px;" src="{{asset('img/home_page/logowhite.webp')}}" />

        @if($info['cidade'])
            <h1 style="color: #fff;">Bolsas de Estudo para <span class="highlight text-uppercase">{{$info['cidade']}}</span></h1>
            <p class="subtitle">Qualifique-se em mais de 40 áreas com nossos cursos profissionalizantes e conquiste sua vaga no mercado de trabalho. Fale com um consultor!</p>
        @else
            <h1 style="color: #fff;">Transforme sua <span class="highlight">Carreira</span><br>com Nossos Cursos!</h1>
            <p class="subtitle">Cursos 100% online, certificados e reconhecidos. Aprenda no seu ritmo e conquiste o emprego dos seus sonhos!</p>
        @endif
    </div>
</section>

<!-- Botão Centralizado + Blocos de Benefícios -->
<section class="container" style="text-align: center; padding: 40px 0;">
    
    <a href="#sessao_cursos" class="btn btn-primary btn-scroll-curso" style="padding: 18px 40px; font-size: 1.2rem; max-width: 400px; margin: 20px auto; display: inline-block;">
        Escolher meu curso agora
    </a>

    <div class="hero-benefits" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin-top: 30px;">

        <div class="hero-benefit" style="flex: 1 1 200px; max-width: 250px;">
            <i class="fas fa-certificate"></i>
            <h3>Certificado Válido</h3>
            <p>Reconhecido em todo o Brasil</p>
        </div>

        <div class="hero-benefit" style="flex: 1 1 200px; max-width: 250px;">
            <i class="fas fa-users"></i>
            <h3>+120 Mil Alunos</h3>
            <p>Formados no Brasil e no exterior</p>
        </div>

        <div class="hero-benefit" style="flex: 1 1 200px; max-width: 250px;">
            <i class="fas fa-shield-alt"></i>
            <h3>Garantia de 7 Dias</h3>
            <p>Sua satisfação ou seu dinheiro de volta</p>
        </div>

        <div class="hero-benefit" style="flex: 1 1 200px; max-width: 250px;">
            <i class="fas fa-graduation-cap"></i>
            <h3>Autorizado pelo MEC</h3>
            <p>Cursos livres, Lei nº 9.394/96</p>
        </div>

    </div>
</section>

    <!-- Seção Problemas -->
    <section class="section lazy-load" style="background: white;">
        <div class="container">
            <h2 class="section-title">Cansado de Ficar Para Trás?</h2>
            <p class="section-subtitle">Sabemos como é frustrante procurar um bom emprego e não ter a qualificação que as empresas pedem.</p>
            
            <div class="problems-grid">
                <div class="problem-card">
                    <div class="problem-icon"><i class="fas fa-user-slash"></i></div>
                    <h3>Desemprego ou Subemprego</h3>
                    <p>A falta de qualificação te deixa fora das melhores vagas e preso em empregos com baixos salários.</p>
                </div>
                <div class="problem-card">
                    <div class="problem-icon"><i class="fas fa-sack-xmark"></i></div>
                    <h3>Salários Baixos</h3>
                    <p>Trabalhos sem especialização raramente oferecem o salário que você e sua família merecem.</p>
                </div>
                <div class="problem-card">
                    <div class="problem-icon"><i class="fas fa-door-closed"></i></div>
                    <h3>Falta de Oportunidades</h3>
                    <p>Sem um certificado de peso, você perde promoções e oportunidades de crescimento todos os dias.</p>
                </div>
            </div>
        </div>
    </section>



    <!-- Seção Cursos -->
    <section id="sessao_cursos" class="section lazy-load" style="background: var(--light-bg);">
        <div class="container">
            <h2 class="section-title">Escolha Sua Nova Profissão</h2>
            <p class="section-subtitle">Temos o curso perfeito para você começar uma carreira de sucesso. Todos com certificado reconhecido.</p>
            
            <div class="courses-grid">
                @foreach($cursos as $curso)
                    @php
                        // Lógica para links e dados (do seu código original)
                        /*if ($info['afiliado'] AND !request()->has('wd')) {
                            //$curso->link_checkout_completo = "https://go.hotmart.com/".$curso->codigo_ref.$info['parametros'];
                            $curso->link_checkout_completo = "/" . $curso->url . $info['parametros'];
                        } else {
                            $curso->link_checkout_completo = "https://" . request()->getHost() . "/" . $curso->url . $info['parametros'];
                            if ($curso->gratuito) {
                                $curso->link_checkout_completo = "https://" . request()->getHost() . "/" . $curso->url . $info['parametros'] . "&g=1";
                            }
                        }*/
                        $curso->link_checkout_completo = "https://" . request()->getHost() . "/" . $curso->url . $info['parametros'];
                        if ($curso->gratuito) {$curso->link_checkout_completo = "https://" . request()->getHost() . "/" . $curso->url . $info ['parametros'] . "&g=1"; }
                        if ($info['whatsapp']) {
                            if ($info['whatsapp'] != 1) {
                                $nome = $info['formulario'] ? "Olá meu nome é {nome}, " : null;
                                $curso->link_checkout_completo = "https://wa.me/" . $info['whatsapp_atendimento'] . "?text=$nome Quero tirar minhas dúvidas sobre o curso $curso->titulo";
                            }
                        }
                        if ($curso->numero_alunos < 1000) {
                            $curso->numero_alunos += rand(800, 1300);
                        }
                    @endphp

                    @if($curso['publicado'] AND $curso['mostrar_na_pagina'])
                    <div class="course-card lazy-load">
                        <div class="course-image-wrapper" style="height: 240px">
                            @if($curso->gratuito)
                            <div class="course-popular-tag">GRATUITO</div>
                            @endif
                            <img data-src="{{asset('/storage/'.$curso['capa_horizontal'])}}" alt="Capa do curso de {{$curso['titulo']}}" class="lazy-img">
                        </div>
                        
                        <div class="course-content">
                            <!--<h3 class="course-title">{{ $curso['titulo'] }}</h3>-->
                            <p class="course-description">{{ str_replace('"', '', $curso['headline']) }}</p>
                            
                            <ul class="course-features">
                                <li><i class="fas fa-check"></i> Certificado Reconhecido</li>
                                <li><i class="fas fa-clock"></i> Carga horária: {{ $curso->horas_completo }} horas</li>
                                <li><i class="fas fa-users"></i> Mais de {{ $curso->numero_alunos }} alunos</li>
                                <li><i class="fas fa-headset"></i> Suporte completo</li>
                            </ul>
                            
                            <a onclick="@if(!$info['whatsapp']) fbq('track', 'ViewContent') @elseif(!$info['formulario']) fbq('track', 'Lead') @endif" 
                               @if($info['whatsapp'])
                                 data-bs-toggle="modal" 
                                 data-bs-target="#inscricaoModal"
                               @endif
                               data-cursoid="{{$curso->id}}" 
                               href="{{$curso->link_checkout_completo}}" 
                               class="btn btn-primary btn-inscricao">
                                <i class="fas fa-play"></i> Saiba Mais
                            </a>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

@endsection