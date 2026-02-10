@extends('layouts.public')

@section('head')

    <!-- SEO e Meta Tags -->
    <title>{{ $curso->titulo }} - {{ $curso->headline }}</title>
    <link rel="icon" href="{{ asset('/img/logo/logo-je-sm.png') }}" type="image/x-icon">
    <meta name="description" content="{{ $curso->headline }}">
    <meta name="keywords" content="{{ $curso->titulo }}, Curso, Certificado, Online, Profissionalização, Emprego, Educação, Carreira, Certificação, Portal Jovem Empreendedor, Programa Jovem Empreendedor, qualificação profissional, mercado de trabalho, capacitação profissional, curso com certificado, curso profissionalizante.">
    <meta name="author" content="Portal Jovem Empreendedor">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="{{ $curso->titulo . ' ' . $curso->headline }}">
    <meta property="og:description" content="{{ $curso->headline }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url($curso->url) }}">
    <meta property="og:image" content="{{ asset('/storage/' . $curso->capa_quadrada) }}">
    <meta property="og:image:alt" content="{{ $curso->titulo }}">
    <meta property="og:site_name" content="Portal Jovem Empreendedor">
    

@endsection

@section('content')
  <!-- Hero Section -->
<section class="hero">
    <div class="container">
        <img alt='Portal Jovem Empreendedor' style="max-width: 200px; margin-bottom: 20px;" src="{{asset('img/home_page/logowhite.webp')}}" />

        <h1>{{ $curso->titulo }}</h1> 
        <p class="hero-subtitle">{{ $curso->headline }}</p>
        <a href="#planos" class="cta-button btn-inscricao">
                <i class="fas fa-graduation-cap"></i> Garantir Minha Vaga Agora
            </a>
            <br>
            <button class="cta-button preview-button" data-bs-toggle="modal" data-bs-target="#aulasDemonstrativasModal">
                <i class="fas fa-play-circle"></i> Assista uma prévia
            </button>
    </div>
</section>

<!-- Seção "O que você vai aprender" (Substituição) -->
<section class="section" id="aprendizado" style="background-color: var(--fundo-branco);">
    <div class="container">
        <h2 class="section-title">O que você vai aprender</h2>
        <p class="section-subtitle">Um currículo completo para te levar do básico ao profissional, passo a passo.</p>
        
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

<!-- Seção BÔNUS (Estrutura Nova e Moderna) -->
<section id="bonus" class="section">
    <div class="container">
        <h2 class="section-title">Receba +4 Bônus Exclusivos</h2>
        <p class="section-subtitle">
            Ao garantir sua vaga no plano completo, você leva todos estes presentes que vão acelerar sua entrada no mercado de trabalho. <strong>Sem pagar nada a mais por isso.</strong>
        </p>

        @if(!$desconto_banner) 
            <div class="bonus-notice">
                <i class="fas fa-star"></i> Bônus válidos apenas para o <strong>Plano Completo</strong>.
            </div>
        @endif

        <div class="bonus-grid">
            
            <!-- Bônus 1: Carta de Estágio -->
            <div class="bonus-card">
                <div class="bonus-image">
                    <img loading="lazy" alt="Bônus Carta de Estágio" class="img-fluid" src="{{asset('img/home_page/cartaestagio.webp')}}">
                </div>
                <div class="bonus-details">
                    <h4>Carta de Estágio</h4>
                    <p>Uma ferramenta poderosa para abrir portas, oferecendo um grande diferencial em seu currículo.</p>
                    <div class="bonus-price">
                        <span>De <s>R$ 197,00</s></span>
                        <span class="free-tag">GRÁTIS HOJE</span>
                    </div>
                </div>
            </div>

            <!-- Bônus 2: Preparatório Jovem Aprendiz -->
            <div class="bonus-card">
                <div class="bonus-image">
                    <img loading="lazy" alt="Bônus Preparatório Jovem Aprendiz" class="img-fluid" src="{{asset('img/home_page/jovemaprendiz.webp')}}">
                </div>
                <div class="bonus-details">
                    <h4>Preparatório Jovem Aprendiz</h4>
                    <p>Um treinamento completo para você se destacar nos processos seletivos do programa.</p>
                    <div class="bonus-price">
                        <span>De <s>R$ 297,00</s></span>
                        <span class="free-tag">GRÁTIS HOJE</span>
                    </div>
                </div>
            </div>

            <!-- Outros Bônus (da lista) -->
            @if($curso->conteudo_bonus)
                @foreach($curso->conteudo_bonus as $conteudo)
                <div class="bonus-card">
                    <div class="bonus-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div class="bonus-details">
                        <h4>{{$conteudo['title']}}</h4>
                        {{-- Se houver descrição, você pode adicioná-la aqui --}}
                        {{-- <p>{{ $conteudo['description'] ?? 'Um presente exclusivo para complementar seus estudos.' }}</p> --}}
                        <div class="bonus-price">
                           <span class="free-tag">INCLUSO</span>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
        
        <div class="text-center mt-5">
            <p class="lead">Não perca a chance de garantir uma formação completa e aumentar suas chances no mercado de trabalho.</p>
            <a href="#planos" class="cta-button">Ver Planos e Garantir Bônus</a>
        </div>
    </div>
</section>

<!-- Seção de Investimento (Plano Único) -->
<section class="section" id="investimento" style="background-color: var(--fundo-claro);">
    <div class="container">
        <h2 class="section-title">Acesso Completo e Imediato</h2>
        <p class="section-subtitle">
            Faça um investimento único na sua carreira e tenha acesso a todo o material necessário para se tornar um profissional qualificado.
        </p>

        <div class="investment-box">
            <!-- Coluna da Esquerda: O que está incluso -->
            <div class="investment-includes">
                <h4>Seu Acesso Completo Inclui:</h4>
                <ul>
                    <li><i class="fas fa-check"></i> Curso Completo de <strong>{{ $curso->titulo }}</strong> ({{ $curso->horas_completo }}h)</li>
                    <li><i class="fas fa-check"></i> Certificado Digital Válido em Todo o Brasil</li>
                    <li><i class="fas fa-check"></i> Suporte Direto com o Professor</li>
                    <li><i class="fas fa-check"></i> Acesso Vitalício ao Conteúdo</li>
                    <li>
                        <i class="fas fa-gift"></i> <strong>Pacote de Bônus Exclusivos:</strong>
                        <ul class="bonus-list-included">
                            <li>Carta de Estágio</li>
                            <li>Preparatório Jovem Aprendiz</li>
                            @if($curso->conteudo_bonus)
                                @foreach($curso->conteudo_bonus as $conteudo)
                                    <li>{{$conteudo['title']}}</li>
                                @endforeach
                            @endif
                        </ul>
                    </li>
                </ul>
            </div>

            <!-- Coluna da Direita: Preço e Ação -->
            <div class="investment-action">
                <h5>INVESTIMENTO ÚNICO</h5>
                
                <div class="old-price">
                    De <s>R$ 497,00</s> por apenas:
                </div>

                <div class="main-price">
                    {{ $curso->preco_parcelado_completo }}
                    <small>ou {{ $curso->preco_cheio_completo }} à vista</small>
                </div>
                
                <a href="{{ $curso->link_checkout_completo }}" class="cta-button btn-inscricao w-100">
                    <i class="fas fa-lock"></i> Garantir Minha Vaga Agora
                </a>

                <p class="guarantee-text">
                    <i class="fas fa-shield-alt"></i> Risco Zero! Você tem 7 dias de garantia incondicional.
                </p>

                <div class="payment-methods">
                    <img src="{{asset('img/icons/pagamentos.webp')}}" alt="Formas de Pagamento">
                </div>
            </div>
        </div>
    </div>
</section>

   @if($desconto_banner ?? false)
    <div id="promo-footer" class="promo-footer">
        <div><strong>OFERTA EXPIRANDO!</strong> Não perca seu desconto.</div>
        <a href="{{ $curso->link_checkout_completo }}" class="cta-button btn-inscricao">Aproveitar Desconto</a>
    </div>
    @endif

    <!-- Modal: Vídeos Demonstrativos -->
    <div class="modal fade" id="aulasDemonstrativasModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Prévia do Curso: {{ $curso->titulo }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-8 mb-3 mb-lg-0"><div id="video-player-container"></div></div>
                        <div class="col-lg-4">
                            <h6>Aulas de Amostra:</h6>
                            <ul class="list-group list-group-flush">
                                @foreach($curso->aulas_demonstrativas as $aula)
                                <li class="list-group-item list-group-item-action" style="cursor: pointer;" onclick="changeVideo('{{ $aula['aula_id_youtube'] }}')"><i class="fas fa-play me-2 text-primary"></i> {{ $aula['aula_titulo'] }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal: Inscrição/Captura de Lead -->
    <div class="modal fade" id="inscricaoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Só mais um passo!</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body">
                    <p>Preencha seus dados para garantir a oferta. Seu nome será usado no certificado.</p>
                    <form id="inscricaoForm" action="{{ route('lead_whatsapp') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $curso->user_id ?? 13 }}">
                        <input type="hidden" name="curso_id" value="{{ $curso->id }}">
                        <input type="hidden" name="origem" value="{{ $curso->origem ?? 'organico' }}">
                        <div class="mb-3"><input type="text" class="form-control" name="buyer_name" placeholder="Seu nome completo" required></div>
                        <div class="mb-3"><input type="tel" class="form-control input_telefone" name="buyer_checkout_phone" placeholder="Seu WhatsApp com DDD" required></div>
                        <button type="submit" class="cta-button w-100">Finalizar Inscrição</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>

    <script>
        // Função para carregar vídeos dos depoimentos
        function loadVideo(element) {
            if (element.querySelector('iframe')) return;
            const videoId = element.getAttribute('data-video-id');
            element.innerHTML = `<iframe src="https://www.youtube.com/embed/${videoId}?autoplay=1&modestbranding=1&rel=0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="position:absolute; top:0; left:0; width:100%; height:100%;"></iframe>`;
        }

        let player; // Variável global para o player do modal
        // Função para trocar o vídeo no modal
        function changeVideo(videoId) {
            if (player) player.destroy();
            const container = document.getElementById('video-player-container');
            container.innerHTML = `<div class="plyr__video-embed"><iframe src="https://www.youtube.com/embed/${videoId}?autoplay=1&origin=${window.location.origin}" allowfullscreen allow="autoplay"></iframe></div>`;
            player = new Plyr(container.querySelector('.plyr__video-embed'), {
                // Opções do Plyr, se necessário
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Professor Bio Toggle
            const toggleBtn = document.getElementById('toggle-bio-btn');
            if(toggleBtn) {
                const bioWrapper = document.getElementById('professor-bio-wrapper');
                toggleBtn.addEventListener('click', () => {
                    bioWrapper.classList.toggle('show-full');
                    toggleBtn.textContent = bioWrapper.classList.contains('show-full') ? 'Mostrar menos' : 'Saiba mais';
                });
            }

            // FAQ Accordion
            const faqQuestions = document.querySelectorAll('.faq-question');
            faqQuestions.forEach(btn => {
                btn.addEventListener('click', () => {
                    const answer = btn.nextElementSibling;
                    const isActive = btn.classList.contains('active');
                    
                    // Fecha todos os outros
                    faqQuestions.forEach(otherBtn => {
                        otherBtn.classList.remove('active');
                        otherBtn.nextElementSibling.style.maxHeight = '0px';
                        otherBtn.nextElementSibling.style.padding = '0 20px';
                    });

                    // Abre o clicado (se não estava ativo)
                    if(!isActive) {
                        btn.classList.add('active');
                        answer.style.maxHeight = answer.scrollHeight + "px";
                        answer.style.padding = '0 20px 20px 20px';
                    }
                });
            });

            // Lógica do Modal de Vídeos
            const videoModalEl = document.getElementById('aulasDemonstrativasModal');
            if(videoModalEl) {
                videoModalEl.addEventListener('shown.bs.modal', function () {
                    const firstVideoId = '{{ $curso->aulas_demonstrativas[0]['aula_id_youtube'] ?? '' }}';
                    if (firstVideoId) changeVideo(firstVideoId);
                });
                videoModalEl.addEventListener('hidden.bs.modal', function () {
                    if (player) player.destroy();
                    document.getElementById('video-player-container').innerHTML = '';
                });
            }

            // Máscara de Telefone
            $('.input_telefone').inputmask({ mask: ["(99) 9999-9999", "(99) 99999-9999"], keepStatic: true });

            // Lógica do Modal de Inscrição
            let lastClickedButtonHref = '';
            const inscricaoModal = new bootstrap.Modal(document.getElementById('inscricaoModal'));
            document.querySelectorAll('.btn-inscricao').forEach(link => {
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    lastClickedButtonHref = this.getAttribute('href');
                    inscricaoModal.show();
                });
            });
            
            // Submissão do Formulário de Inscrição
            $('#inscricaoForm').on('submit', function(event) {
                event.preventDefault();
                // Desativa o botão para evitar cliques duplos
                $(this).find('button[type="submit"]').prop('disabled', true).text('Processando...');

                const form = $(this);
                const tel = form.find('.input_telefone').val().replace(/\D/g, '');
                const ddd = tel.substring(0, 2);
                const numero = tel.substring(2);
                const nome = form.find('input[name="buyer_name"]').val();
                
                const checkoutUrl = new URL(lastClickedButtonHref);
                checkoutUrl.searchParams.set('name', nome);
                checkoutUrl.searchParams.set('phoneac', ddd);
                checkoutUrl.searchParams.set('phonenumber', numero);
                
                // Dispara os pixels antes do AJAX
                // fbq('track', 'Lead');
                // fbq('track', 'AddToCart', { content_ids: ['{{$curso->id}}'], content_type: 'product' });

                // **CORREÇÃO CRÍTICA AQUI**
                // O redirecionamento agora ocorre APENAS APÓS a tentativa de salvar o lead.
                $.ajax({
                    url: form.attr('action'),
                    method: form.attr('method'),
                    data: form.serialize(),
                    success: function(response) {
                        console.log('Lead salvo com sucesso!');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Falha ao salvar o lead:', textStatus, errorThrown);
                    },
                    complete: function() {
                        // Redireciona para o checkout independentemente do resultado (sucesso ou erro)
                        // para não prejudicar a experiência do usuário.
                        window.location.href = checkoutUrl.href;
                    }
                });
            });
        });
    </script>

@endsection