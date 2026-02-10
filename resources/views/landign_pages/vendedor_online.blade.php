@extends('html_base')

@section('head')
<style>
    .fs-7{
        font-size: small;
    }
    .hero-section {
        position: relative;
        color: white;
        padding: 80px 20px;
        background-image: url('{{asset('img/fundos/fundo_4880206.webp')}}');
        background-size: 500px; /* Tamanho original da imagem */
        background-repeat: repeat;
        background-position: center;
        overflow: hidden;
    }

    /* Efeito de degradê transparente */
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(0, 4, 40, 0.8), rgba(0, 78, 146, 0.8)); /* Degradê com transparência */
        z-index: 1;
    }

    /* Conteúdo */
    .hero-section .container {
        position: relative;
        z-index: 2;
    }

    .cards_lista{
        background: linear-gradient(to top left, #d9d9d9, #fff);
    }

</style>
@endsection

@section('body_head')
    <!-- Primeira dobra -->
    <section class="hero-section d-flex flex-column justify-content-center align-items-center">
        <div class="container text-center">
            <h1 class="fw-bold">Quer saber o que <span class="text-bg-warning"> Realmente Funciona </span> para vender pela internet?</h1>
            <p class="mt-3 lead">Cansado das promessas de dinheiro fácil e dos atalhos que <span class="text-bg-secondary">nunca funcionam?</span></p>
            <p>Entre para aprender de graça o que realmente dá resultados — <span class="text-bg-secondary">sem truques.</span> </p>
            <a onclick="fbq('track', 'ViewContent');" href="#section_register" class="btn-cta mt-4 d-flex align-items-center justify-content-center">
                <i class=" ri-arrow-right-s-line me-2" style="font-size: 1.2rem;"></i> COMEÇAR AGORA!
            </a>
            <p class="mt-0" style="font-size: small;">Curso 100% gratuito, sem promessas milagrosas. Se não for pra você, pode <strong>sair a qualquer momento.</strong></p>
        </div>
    </section> 

    <!-- Segunda Dobra com Lista Simples -->
    <section class="py-5 bg-white text-secondary">
        <div class="container">
            <!-- Título Curto e Enfático -->
            <h2 class="fw-bold mb-0 text-center" style="font-size: 1.5rem;">Sem Fórmulas Mágicas.</h2>
            <p class="lead mb-4 mt-0 text-center">Apenas Realidade</p>

            <!-- Lista Simples -->
            <div class="row justify-content-center">
                <ul class="list-unstyled" style="max-width: 700px;">
                    <!-- Item 1 -->
                    <li class="mb-3">
                        <p class="mb-0">Curso gratuito e direto para quem quer entender como ganhar renda online de verdade.</p>
                    </li>
                    
                    <!-- Item 2 -->
                    <li class="mb-3">
                        <p class="mb-0">Nada de promessas fáceis. Vamos falar sobre técnicas reais de vendas, com total transparência.</p>
                    </li>
                    
                    <!-- Item 3 -->
                    <li class="mb-3">
                        <p class="mb-0">Se em algum momento achar que não vale a pena, pode sair do grupo a qualquer hora.</p>
                    </li>
                </ul>
            </div>

            <!-- Resumo Final em Destaque -->
            <p class="lead text-center mt-4" style="font-weight: 600;">
                Aqui você não perde nada, só aprende o que realmente funciona.
            </p>
        </div>
    </section>

    <!-- Seção "O Que Você Vai Aprender" -->
    <section class="py-5 hero-section">
        <div class="container">
            <!-- Título Principal -->
            <h2 class="fw-bold mb-5 text-center" style="font-size: 1.5rem;">
                O Que Você Vai Aprender - <span class="text-bg-warning"> Sem Enrolação </span>
            </h2>

            <!-- Lista de Tópicos com Ícones -->
            <div class="row">
                <!-- Tópico 1 -->
                <div class="col-md-6 mb-4 d-flex align-items-start">
                    <i class="bi bi-bullseye  me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <p class="mb-1 fw-bold">Identificar Produtos Lucrativos de Verdade</p>
                        <p class="mb-0">Como escolher produtos que vendem, evitando as armadilhas das promessas fáceis.</p>
                    </div>
                </div>

                <!-- Tópico 2 -->
                <div class="col-md-6 mb-4 d-flex align-items-start">
                    <i class="bi bi-phone-fill  me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <p class="mb-1 fw-bold">Transformar o WhatsApp em uma Máquina de Vendas</p>
                        <p class="mb-0">Passo a passo para criar conversas que vendem de verdade.</p>
                    </div>
                </div>

                <!-- Tópico 3 -->
                <div class="col-md-6 mb-4 d-flex align-items-start">
                    <i class="bi bi-hand-thumbs-up-fill  me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <p class="mb-1 fw-bold">Vender com Honestidade e Ganhar Confiança</p>
                        <p class="mb-0">Técnicas para vender sem truques e ganhar a confiança do cliente.</p>
                    </div>
                </div>

                <!-- Tópico 4 -->
                <div class="col-md-6 mb-4 d-flex align-items-start">
                    <i class="bi bi-bar-chart-line-fill  me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <p class="mb-1 fw-bold">Usar Meta Ads sem Jogar Dinheiro Fora</p>
                        <p class="mb-0">Como investir de forma estratégica em anúncios e entender o que realmente funciona.</p>
                    </div>
                </div>

                <!-- Tópico 5 -->
                <div class="col-md-6 mb-4 d-flex align-items-start">
                    <i class="bi bi-people-fill  me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <p class="mb-1 fw-bold">Atrair o Público Certo e Aumentar Suas Vendas</p>
                        <p class="mb-0">Como alcançar pessoas interessadas e aumentar a conversão.</p>
                    </div>
                </div>

                <!-- Tópico 6 -->
                <div class="col-md-6 mb-4 d-flex align-items-start">
                    <i class="bi bi-graph-up  me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <p class="mb-1 fw-bold">Melhorar Resultados com Ajustes Simples</p>
                        <p class="mb-0">Pequenas otimizações que aumentam o retorno das campanhas e das vendas.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Seção "Por que Tudo Isso é de Graça?" -->
    <section id="section_register" class="py-5 bg-white text-secondary">
        <div class="container">
            <h2 class="fw-bold text-center mb-4" style="font-size: 1.5rem; ">
                Cadastre-se na Nossa Plataforma e Comece a Vender Online Agora Mesmo!
            </h2>
            <div class="mx-auto" style="">
                <p class="mb-3 text-center lead">Na nossa plataforma, você encontra todas as ferramentas que precisa para começar a vender online de forma simples e eficiente. Cadastre-se agora e dê o primeiro passo para transformar o seu negócio!</p>
            </div>
            <form method="POST" action="{{ route('register') }}">
                @csrf
        
                <div class="mb-3">
                    <label for="name" class=" col-form-label ">Nome</label>
        
                    <div class="">
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" >
        
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                        @enderror
                    </div>
                </div>
        
                <div class="mb-3">
                    <label for="email" class=" col-form-label ">Email</label>
        
                    <div class="">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
        
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                        @enderror
                    </div>
                </div>
        
                <div class="mb-3">
                    <label for="password" class=" col-form-label ">Senha</label>
        
                    <div class="">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
        
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                        @enderror
                    </div>
                </div>
        
                <div class="mb-3">
                    <label for="password-confirm" class=" col-form-label ">Confirme sua senha</label>
        
                    <div class="">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                    </div>
                </div>
        
                <div class="mb-0">
                    <div class="">
                        <button type="submit" onclick="fbq('track', 'Lead');" class="btn btn-primary">
                            Fazer meu cadastro
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="container mt-5">
            <!-- Título Principal -->
            <h2 class="fw-bold mb-4 text-center" style="font-size: 1.5rem; ">
                Por que Tudo Isso é de Graça?
            </h2>

            <!-- Bloco de Explicação -->
            <div class="mx-auto text-center" style="">
                <p class="mb-3 lead">Sim, este curso é 100% gratuito, sem truques e sem pegadinhas.</p>
                <p class="mb-3">Você deve estar se perguntando: qual o motivo?</p>
                <p class="mb-3 lead">A resposta é simples: eu tenho uma forma legítima de ganhar dinheiro com o que vou te ensinar, mas ela não envolve vender nenhum curso para você.</p>
                <p class="mb-0">Vou te explicar exatamente como isso funciona dentro do grupo, com total transparência.</p>
            </div>
        </div>
    </section>
    <!-- Seção Perguntas e Respostas -->
    <section class="py-5 hero-section">
        <div class="container">
            <!-- Título Principal -->
            <h2 class="fw-bold mb-5 text-center" style="font-size: 1.5rem;">
                Perguntas e Respostas
            </h2>

            <!-- Blocos de Perguntas e Respostas -->
            <div class="accordion accordion-flush" id="faqAccordion" style="margin: auto;">
                <!-- Pergunta 1 -->
                <div class="accordion-item mb-3 border-0">
                    <h3 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed bg-white fw-bold" style="font-size: 1.1rem; " type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            <i class="bi bi-question-circle me-2 "></i> Eu vou ter que pagar algo?
                        </button>
                    </h3>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Não. O curso é 100% gratuito. Se achar que está sendo enrolado, pode sair do grupo a qualquer momento.
                        </div>
                    </div>
                </div>

                <!-- Pergunta 2 -->
                <div class="accordion-item mb-3 border-0">
                    <h3 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed bg-white fw-bold" style="font-size: 1.1rem; " type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            <i class="bi bi-clock-history me-2 "></i> Quanto tempo vou precisar dedicar?
                        </button>
                    </h3>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            O curso é prático e direto, mas exige que você estude e aplique o que aprende. Vou ser sincero: sem dedicação, os resultados não vêm.
                        </div>
                    </div>
                </div>

                <!-- Pergunta 3 -->
                <div class="accordion-item border-0">
                    <h3 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed bg-white fw-bold" style="font-size: 1.1rem; " type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            <i class="bi bi-shield-check me-2 "></i> Por que você oferece o curso de graça?
                        </button>
                    </h3>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Vou te explicar isso com transparência dentro do grupo, mas já adianto: não envolve vender cursos para você.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



@endsection

@section('end_body')
<style>
    .container{
        max-width: 700px;
    }
</style>
<script>

        /* ####################################
         * APÓS CARREGAMENTO PARCIAL DA PÁGINA
         * ##################################*/ 
        document.addEventListener('DOMContentLoaded', function() {

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


        });
</script>

<noscript>
    <img height="1" width="1" style="display:none"
         src="https://www.facebook.com/tr?id=728470837780073&ev=PageView&noscript=1" />
</noscript>

@endsection