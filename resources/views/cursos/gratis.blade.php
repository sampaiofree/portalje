@extends('layouts.public')

{{-- SEO e Meta Tags Específicas --}}
@section('head')
    <title>{{$curso->titulo." ".$curso->headline}}</title>
    <meta name="description" content="{{$curso->headline}}">
    
    <meta property="og:title" content="{{$curso->titulo." ".$curso->headline}}">
    <meta property="og:description" content="{{$curso->headline}}">
    <meta property="og:type" content="article"> <!-- Pode ser 'article', 'video', etc. -->
    <meta property="og:url" content="https://jovemempreendedor.org/{{$curso->url}}"> <!-- URL canônica -->
    <meta property="og:image" content="{{asset('/storage/'.$curso->capa_quadrada)}}"> <!-- URL da imagem de pré-visualização -->
    <meta property="og:image:alt" content="{{$curso->titulo}}">
    
    
    <style>
        /* CSS específico para os componentes desta página */
        /* (Pode ser movido para main.css se preferir) */
        
        /* BOTÃO DE DESTAQUE PARA COMEÇAR GRÁTIS */
        .btn-start-free {
            background: #ff4757; /* Cor de destaque para o CTA principal */
            color: white;
            padding: 18px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 8px 25px rgba(255, 71, 87, 0.4);
            display: inline-block;
        }
        .btn-start-free:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(255, 71, 87, 0.5); 
        }
        .btn-start-free i { margin-right: 10px; }

        /* SEÇÃO DO PROFESSOR */
        .professor-image { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; box-shadow: 0 10px 30px rgba(0,0,0,0.15); border: 4px solid white; }
        #professor-bio-wrapper { max-height: 100px; overflow: hidden; position: relative; transition: max-height 0.5s ease-out; text-align: left; color: var(--light-text); margin-top: 20px; }
        #professor-bio-wrapper.show-full { max-height: 1000px; }
        #toggle-bio-btn { background: none; border: 1px solid #ddd; border-radius: 20px; padding: 8px 16px; margin-top: 15px; cursor: pointer; transition: all 0.2s; }
        
        /* SEÇÃO DE GARANTIA/CTA FINAL */
        .final-cta-card { background: var(--light-bg); border-top: 5px solid var(--primary-color); border-radius: 15px; padding: 40px; }
        
        /* GRADE DE MÓDULOS */
        .modules-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px; }
        .module-card { background: #fff; border: 1px solid #e9ecef; border-radius: 15px; padding: 30px; transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .module-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08); }
        .module-header { display: flex; align-items: center; gap: 20px; margin-bottom: 20px; }
        .module-number { flex-shrink: 0; width: 50px; height: 50px; background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); color: white; font-size: 1.2rem; font-weight: bold; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
        .module-header h3 { color: var(--dark-text); font-size: 1.3rem; margin: 0; }
        .module-topics { list-style: none; padding-left: 0; }
        .module-topics li { color: var(--light-text); padding: 8px 0 8px 30px; position: relative; border-bottom: 1px solid #e9ecef; }
        .module-topics li:last-child { border-bottom: none; }
        .module-topics li::before { content: '\f00c'; font-family: 'Font Awesome 6 Free'; font-weight: 900; color: var(--success-color); position: absolute; left: 0; top: 10px; }

        /* GRADE DE BÔNUS */
        .bonus-grid { display: grid; grid-template-columns: 1fr; gap: 25px; max-width: 800px; margin: 0 auto; }
        .bonus-card { background: white; border: 1px solid #e9ecef; border-radius: 15px; padding: 25px; display: flex; align-items: center; gap: 25px; box-shadow: 0 8px 25px rgba(0,0,0,0.05); }
        .bonus-image, .bonus-icon { flex-shrink: 0; width: 90px; height: 90px; border-radius: 50%; overflow: hidden; }
        .bonus-image img { width: 100%; height: 100%; object-fit: cover; }
        .bonus-icon { display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); color: white; font-size: 2.5rem; }
        .bonus-details h4 { font-size: 1.2rem; font-weight: bold; margin-bottom: 8px; }
        .bonus-details p { color: var(--light-text); margin-bottom: 15px; line-height: 1.5; }
        .bonus-price span { color: var(--light-text); }
        .free-tag { background: var(--success-color); color: white !important; padding: 4px 10px; border-radius: 5px; font-weight: bold; font-size: 0.9rem; margin-left: 10px; }
        @media (max-width: 576px) { .bonus-card { flex-direction: column; text-align: center; } }
    </style>
@endsection


@section('content')

    <!-- HERO SECTION: Título, Subtítulo, Descrição, Botão e Vídeo em um Card de Destaque -->
    <header class="hero" style="padding-bottom: 50px;">
        <div class="container text-center" style="margin-top: -50px; position: relative; z-index: 2;">
            <div style="background-color: #fff; border-radius: 15px; padding: 40px 30px; box-shadow: 0 15px 40px rgba(0,0,0,0.1); max-width: 900px; margin: 0 auto;">
                <img alt='Portal Jovem Empreendedor' style="max-width: 180px; margin-bottom: 15px;" src="{{asset('img/home_page/logoPortal.webp')}}" />
                <h1 class="fw-bold display-6" style="color: var(--dark-text); margin-bottom: 10px;">Curso {{$curso->titulo}}</h1>
                <p style="font-size: 1.5rem; color: #28a745; font-weight: bold; margin-bottom: 15px;">De <s style="color: var(--light-text); font-size: 1.5rem;">{{$curso->preco_cheio_completo}}</s> → GRÁTIS por tempo limitado</p>
                <p style="font-size: 1.1rem; color: var(--light-text); line-height: 1.4;">O curso online que já ajudou milhares de alunos a conquistarem um emprego.
                </p>
                <p style="font-size: 1.3rem; color: var(--light-text); line-height: 1.4;">Em poucas semanas, você aprenderá tudo para trabalhar em
                    <span style="font-weight: bolder; color: #ff9100">
                        @foreach($curso->areas_de_atuacao as $area)
                            {{ $area }}@if(!$loop->last), @else. @endif
                        @endforeach
                    </span>
                </p>
                <p style="font-size: 1.1rem; color: var(--dark-text); font-weight: bold; margin-bottom: 25px;">Comece ainda hoje, sem risco e sem complicação.</p>
                
                <!-- Botão para Mobile - Acima do vídeo -->
                <div class="d-block d-md-none mb-4">
                    <a href="?g=1" style="background: #ff4757; color: white; padding: 15px 30px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 1.1rem; transition: all 0.3s ease; border: none; box-shadow: 0 8px 25px rgba(255, 71, 87, 0.4); display: inline-block; width: 100%; max-width: 350px;">
                        Inscreva-se grátis agora →
                    </a>
                </div>

                <!-- Campo de Vídeo -->
                <!--<div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; background: #000; border-radius: 10px; margin-bottom: 20px;">
                    <iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border:0;" src="https://www.youtube.com/embed/your_video_id" allowfullscreen></iframe>
                </div>-->

                <!-- Botão para Desktop - Abaixo do vídeo (ou ao lado, dependendo do layout) -->
                <div class="d-none d-md-block">
                    <a href="?g=1" style="background: #ff4757; color: white; padding: 18px 40px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 1.2rem; transition: all 0.3s ease; border: none; box-shadow: 0 8px 25px rgba(255, 71, 87, 0.4); display: inline-block;">
                        Inscreva-se grátis agora →
                    </a>
                </div>
                
                <p class="mt-3" style="color: var(--light-text); font-size: 0.9rem;">Oferta válida por tempo limitado.</p>
            </div>
        </div>
    </header>

    <!-- SEÇÃO "O QUE VOCÊ VAI APRENDER": Mostra o valor do curso completo -->
    <section class="section lazy-load" style="background-color: #fff; padding-top: 60px;">
        <div class="container">
            <h2 class="section-title">O Que Você Vai Aprender</h2>
            <p class="section-subtitle" style="margin-bottom: 15px;">Um currículo completo para te levar do básico ao profissional, passo a passo.</p>
            <p style="font-size: 0.9rem; text-align: center;">Alguns módulos estão disponíveis apenas na versão completa do curso.</p>
            <div class="modules-grid">
                @if($curso->conteudo_principal_acordion)
                    @foreach($curso->conteudo_principal_acordion as $indice => $topico)
                    <div class="module-card">
                        <div class="module-header">
                            <div class="module-number">{{ str_pad($indice + 1, 2, '0', STR_PAD_LEFT) }}</div>
                            <h3>{{ $topico['title'] }}</h3>
                        </div>
                        <ul class="module-topics">
                            @foreach($topico['topics'] as $conteudo)
                                <li>{{ $conteudo }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    <!-- SEÇÃO DE BÔNUS: Aumenta a percepção de valor -->
    <!--<section id="bonus" class="section lazy-load">
        <div class="container">
            <h2 class="section-title">Tudo o Que Você Receberá</h2>
            <p class="section-subtitle">
                Ao decidir continuar após o período de teste, você terá acesso a todo este material.
            </p>
            include('partials._bonus-grid')
        </div>
    </section>-->

    <!-- SEÇÃO FINAL DE CTA (Call to Action) -->
    <section class="section lazy-load" style="background-color: #fff;">
        <div class="container">
            <div class="final-cta-card text-center">
                <i class="fas fa-rocket fa-3x mb-3" style="color: var(--primary-color);"></i>
                <h3 class="fw-bold">Pronto para Dar o Próximo Passo?</h3>
                <p class="lead mb-4">Milhares de alunos já transformaram suas carreiras. Você é o próximo.</p>
                <a href="?g=1" class="btn-start-free" style="max-width: 400px; width: 100%;">
                    <i class="fas fa-play-circle"></i> Iniciar Curso Sem Compromisso
                </a>
                <p class="mt-3 text-muted">Acesso imediato às primeiras aulas.</p>
            </div>
        </div>
    </section>

    <!-- SEÇÃO DO PROFESSOR: Reposicionada para o final -->
    @if($curso->professor_foto)
    <section class="section lazy-load">
        <div class="container text-center" style="max-width: 800px;">
            <h2 class="section-title">Conheça seu Professor</h2>
            <img src="{{ asset('/storage/' . $curso->professor_foto) }}" alt="Foto do professor {{ $curso->professor_nome }}" class="professor-image">
            <h3 class="mt-4">{{ $curso->professor_nome }}</h3>
            <p class="text-muted">{{ $curso->professor_especialidade ?? 'Especialista na Área' }}</p>
            <div id="professor-bio-wrapper">{!! $curso->professor_biografia !!}</div>
            <button id="toggle-bio-btn">Saiba mais</button>
        </div>
    </section>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
    
            // ... (outras funções de setup como startCountdown, setupLazyLoading, etc.)

            function setupProfessorBio() {
                const toggleBtn = document.getElementById('toggle-bio-btn');
                const bioWrapper = document.getElementById('professor-bio-wrapper');
                if (!toggleBtn || !bioWrapper) return; // Se não encontrar os elementos, ele para.

                toggleBtn.addEventListener('click', () => {
                    bioWrapper.classList.toggle('show-full');
                    toggleBtn.textContent = bioWrapper.classList.contains('show-full') ? 'Mostrar menos' : 'Saiba mais';
                });
            }

            // --- INICIALIZAÇÃO ---
            // ... (outras chamadas como startCountdown(), setupLazyLoading(), etc.)
            
            setupProfessorBio(); // << ESSA LINHA PRECISA ESTAR AQUI!
        });
    </script>

@endsection