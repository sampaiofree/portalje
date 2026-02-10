<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link rel="icon" href="{{asset('/img/logo/logo-je-sm.png')}}" type="image/x-icon">
    <meta property="og:locale" content="pt_BR">

    <!-- Font Awesome (Necessário para o novo layout) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">


    <!-- Estilos do Novo Layout + Estilos Essenciais do Antigo -->
    <style>
        /* Estilos do Novo Layout */

        /* 2. CÓDIGO CSS (Cole dentro da sua tag <style>) */



@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}



@keyframes scaleUp {
    from { transform: scale(0.95); }
    to { transform: scale(1); }
}



.btn-unlock {
    display: inline-block;
    background: linear-gradient(45deg, #27ae60, #2ecc71); /* var(--success-color) */
    color: white;
    padding: 15px 30px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: bold;
    font-size: 1.2rem;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 8px 25px rgba(39, 174, 96, 0.3);
}

.btn-unlock:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(39, 174, 96, 0.4);
}

.btn-unlock i {
    margin-right: 10px;
}

        html {
            scroll-behavior: smooth;
        }

        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --accent-color: #f39c12;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --light-bg: #f8f9fa;
            --dark-text: #2c3e50;
            --light-text: #7f8c8d;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; background: #f8f9fa; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        h1, h2, h3 { color: var(--dark-text); }

        /* Barra de Urgência */
        .urgency-bar { background: linear-gradient(45deg, #e74c3c, #c0392b); color: white; text-align: center; padding: 10px; font-weight: bold; animation: pulse 2s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.8; } 100% { opacity: 1; } }

        /* Hero Section */
        .hero { background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); color: white; padding-top: 60px; text-align: center; position: relative; overflow: hidden; z-index: 1;}
        .hero::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,138.7C960,139,1056,117,1152,112C1248,107,1344,117,1392,122.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') bottom; background-size: cover; }
        .hero h1 { font-size: 2.8rem; margin-bottom: 20px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .hero .subtitle { font-size: 1.3rem; margin-bottom: 30px; font-weight: 300; max-width: 800px; margin: 0 auto 40px; }
        .highlight { background: linear-gradient(45deg, #f39c12, #e67e22); padding: 2px 8px; border-radius: 4px; font-weight: bold; }

        #sessao_cursos {
            position: relative;
            z-index: 0;
        }

        
        /* Benefícios Hero */
        .hero-benefits { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 40px; max-width: 900px; margin: 40px auto 0; }
        .hero-benefit { text-align: center; padding: 20px; background: #e8e8e8; border-radius: 15px; backdrop-filter: blur(10px); }
        .hero-benefit i { font-size: 2.5rem; margin-bottom: 15px; color: var(--accent-color); }
        .hero-benefit h3 { color: rgb(62, 62, 62); font-size: 1.1rem; }
        .hero-benefit p { font-size: 0.9rem; opacity: 0.9; }

        /* Seções Genéricas */
        .section { padding: 80px 0; }
        .section-title { text-align: center; font-size: 2.5rem; margin-bottom: 20px; color: var(--dark-text); }
        .section-subtitle { text-align: center; font-size: 1.2rem; color: var(--light-text); margin-bottom: 50px; max-width: 700px; margin: 0 auto 50px; }

        /* Seção Problemas */
        .problems-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 50px; }
        .problem-card { background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); text-align: center; border-left: 5px solid var(--danger-color); transition: transform 0.3s ease; }
        .problem-card:hover { transform: translateY(-5px); }
        .problem-icon { font-size: 3rem; margin-bottom: 20px; color: var(--danger-color); }

        /* Seção Cursos */
        .courses-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; margin-top: 50px; }
        .course-card { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; }
        .course-card:hover { transform: translateY(-10px); box-shadow: 0 25px 50px rgba(0,0,0,0.15); }
        .course-image-wrapper { height: 220px; width: 100%; overflow: hidden; position: relative; }
        .course-image-wrapper img { width: 100%; height: 100%; object-fit: cover; }
        .course-popular-tag { position: absolute; top: 15px; right: -50px; background: var(--danger-color); color: white; padding: 5px 60px; font-size: 0.8rem; font-weight: bold; transform: rotate(45deg); text-align: center; }
        .course-content { padding: 25px; flex-grow: 1; display: flex; flex-direction: column; }
        .course-title { font-size: 1.4rem; font-weight: bold; margin-bottom: 10px; color: var(--dark-text); }
        .course-description { color: var(--light-text); margin-bottom: 20px; line-height: 1.5; font-size: 0.95rem; }
        .course-features { list-style: none; margin-bottom: 25px; }
        .course-features li { padding: 4px 0; color: var(--dark-text); font-weight: 500; font-size: 0.9rem;}
        .course-features li i { margin-right: 10px; color: var(--success-color); }
        .course-card .btn { margin-top: auto; } /* Empurra o botão para baixo */

        /* Botão Principal */
        .btn { display: inline-block; padding: 15px 30px; font-size: 1rem; font-weight: bold; text-decoration: none; border-radius: 50px; transition: all 0.3s ease; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; border: none; box-shadow: 0 8px 25px rgba(0,0,0,0.15); width: 100%; text-align: center; }
        .btn-primary { background: linear-gradient(45deg, var(--success-color), #2ecc71); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(39, 174, 96, 0.4); }

        /* Depoimentos */
        .testimonials { background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); color: white; }
        .testimonials .section-title, .testimonials .section-subtitle { color: white; }
        .video-facade { position: relative; cursor: pointer; overflow: hidden; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .video-facade img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease; }
        .video-facade:hover img { transform: scale(1.05); }
        .play-button { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: rgba(0, 0, 0, 0.6); border-radius: 50%; width: 60px; height: 60px; display: flex; justify-content: center; align-items: center; transition: background-color 0.3s; }
        .play-button i { font-size: 30px; color: white; }
        .video-facade:hover .play-button { background-color: var(--danger-color); }
        #video-depoimentos { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; }
        
        /* Certificado */
        #certificado img { border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        #certificado .list-group-item { display: flex; align-items: flex-start; gap: 15px; border: none; padding-left: 0;}
        #certificado .list-group-item i { font-size: 1.2rem; color: var(--success-color); margin-top: 5px; }

        /* FAQ - Accordion */
        #perguntas_e_respotas .accordion-item { border-radius: 10px !important; margin-bottom: 10px; border: 1px solid #e0e0e0; overflow: hidden; }
        #perguntas_e_respotas .accordion-button { font-weight: bold; color: var(--dark-text); }
        #perguntas_e_respotas .accordion-button:not(.collapsed) { background-color: #f0f2ff; color: var(--primary-color); }

        /* Footer */
        .footer { background: #2c3e50; color: white; padding: 60px 0 20px; }
        .footer-content { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; margin-bottom: 40px; text-align: left; }
        .footer-section h3 { margin-bottom: 20px; color: var(--accent-color); }
        .footer-section p, .footer-section a { color: #bdc3c7; text-decoration: none; font-size: 0.95rem; }
        .footer-section a:hover { color: white; }
        .footer-socials a { font-size: 1.5rem; margin-right: 15px; }
        .footer-bottom { border-top: 1px solid #34495e; padding-top: 20px; text-align: center; font-size: 0.9rem; color: #bdc3c7; }
        
        /* Botão WhatsApp Flutuante */
        .whatsapp-float { position: fixed; bottom: 20px; right: 20px; background: #25d366; color: white; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 2.2rem; box-shadow: 0 8px 25px rgba(37, 211, 102, 0.4); z-index: 1000; animation: bounce 2s infinite; text-decoration: none; }
        @keyframes bounce { 0%, 20%, 50%, 80%, 100% { transform: translateY(0); } 40% { transform: translateY(-10px); } 60% { transform: translateY(-5px); } }

        /* Animações e Utilitários */
        .lazy-load { opacity: 0; transform: translateY(30px); transition: all 0.7s ease-out; }
        .lazy-load.visible { opacity: 1; transform: translateY(0); }
        .color4 { color: #fdfd88; }
        .fw-bold { font-weight: bold; }

        /* Responsividade */
        @media (max-width: 768px) {
            .hero h1 { font-size: 2.2rem; }
            .hero .subtitle { font-size: 1.1rem; }
            .section-title { font-size: 2rem; }
            .footer-content { text-align: center; }
        }
    </style>
    @yield('head')
</head>
<body>

    <!-- Barra de Urgência Dinâmica (do seu código original) -->
    @if($info['cidade'])
    <div class="urgency-bar">
        <p class="m-0">Bolsas de Estudo Liberadas para <strong class="text-uppercase">{{$info['cidade']}}</strong>! Vagas Limitadas!</p>
    </div>
    @elseif($desconto_banner)
    <div class="urgency-bar">
        <p class="m-0">🔥 Desconto Especial de {{$desconto_banner}}%! Termina em <span class="countdown"></span></p>
    </div>
    @endif 



    @yield('content')
   

    <!-- Depoimentos em Vídeo -->
    <section class="section testimonials lazy-load">
        <div class="container">
            <h2 class="section-title">O Que Nossos Alunos Dizem</h2>
            <p class="section-subtitle">Histórias reais de quem transformou a carreira com nossos cursos.</p>
            
            @php
                $depoimentos = ['rejxwJ2lX-Q', '1hekoAyPVRs', 'Mnn2yIAlhZk', '9mmtunKAnMY', 'uQ5lB9r8ZlI', 'dMIxLKj35aU'];
            @endphp
            
            <div id="video-depoimentos">
                @foreach($depoimentos as $depoimento)
                <div class="lazy-load">
                    <div class="video-facade" data-video-id="{{ $depoimento }}" onclick="loadVideo(this)">
                        <img data-src="https://img.youtube.com/vi/{{ $depoimento }}/hqdefault.jpg" alt="Thumbnail do depoimento" class="lazy-img">
                        <div class="play-button"><i class="fas fa-play"></i></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

     <!-- Seção Certificado -->
     <section id="certificado" class="section lazy-load bg-white">
        <div class="container">
             <h2 class="section-title">Certificado Válido e Reconhecido</h2>
             <p class="section-subtitle">Receba um certificado com validade em todo o Brasil, pronto para turbinar seu currículo.</p>
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img class="lazy-img img-fluid" alt="Modelo de Certificado do Portal Jovem Empreendedor" data-src="{{asset('img/home_page/certificadoNovo2.webp')}}" style="width: 100%; height: auto;">
                </div>
                <div class="col-lg-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <i class="fas fa-qrcode"></i>
                            <div>
                                <h4 class="fw-bold">Registro Digital com QR Code</h4>
                                <p>Garante a autenticidade do seu certificado, facilitando a verificação por empresas e recrutadores.</p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-building-columns"></i>
                            <div>
                                <h4 class="fw-bold">Mesma Validade de um Curso Presencial</h4>
                                <p>Nosso certificado segue a legislação de cursos livres (Decreto N° 5.154/04), sendo aceito em todo o território nacional.</p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-award"></i>
                            <div>
                                <h4 class="fw-bold">Ideal para seu Currículo</h4>
                                <p>Use para comprovar horas extracurriculares, em processos seletivos e concursos públicos (conforme edital).</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ (Perguntas e Respostas) -->
    <section class="section lazy-load">
        <div id="perguntas_e_respotas" class="container">
            <h2 class="section-title">Perguntas Frequentes</h2>
            <p class="section-subtitle">Ainda tem alguma dúvida? Encontre a resposta aqui.</p>
            <div class="accordion accordion-flush" id="accordionFlushExample" style="max-width: 800px; margin: auto;">
                <!-- Item 1 -->
                <div class="accordion-item">
                    <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne">Como funciona o curso?</button></h2>
                    <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample"><div class="accordion-body">Após a confirmação do pagamento, você receberá por e-mail o acesso à nossa plataforma. O curso é 100% online com videoaulas que você pode assistir quando e onde quiser, 24 horas por dia.</div></div>
                </div>
                <!-- Item 2 -->
                <div class="accordion-item">
                    <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo">Quando posso começar?</button></h2>
                    <div id="flush-collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample"><div class="accordion-body">O acesso é imediato após a confirmação do pagamento via Cartão de Crédito ou PIX. Para boletos, a liberação pode levar até 2 dias úteis. Você receberá tudo por e-mail.</div></div>
                </div>
                <!-- Item 3 -->
                <div class="accordion-item">
                    <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse04">O certificado é reconhecido pelo MEC?</button></h2>
                    <div id="flush-collapse04" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample"><div class="accordion-body">Sim! Nossos cursos são classificados como "Cursos Livres", autorizados pela Lei nº 9.394/96 e regulamentados pelo Decreto Presidencial nº 5.154/04. O certificado é válido em todo o Brasil para comprovação de qualificação profissional, horas complementares e enriquecimento do seu currículo.</div></div>
                </div>
                <!-- Item 4 -->
                 <div class="accordion-item">
                    <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse06">Este site é seguro?</button></h2>
                    <div id="flush-collapse06" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample"><div class="accordion-body">Totalmente seguro. Usamos a Hotmart, uma das maiores e mais seguras plataformas de pagamento do mundo. Seus dados estão 100% protegidos e você tem uma garantia de 7 dias.</div></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                     <img src="{{asset('img/home_page/logowhite.png')}}" alt="Logo Portal Jovem Empreendedor" style="max-width: 180px; margin-bottom: 15px;">
                    <p>Transformando vidas através da educação profissionalizante. Qualifique-se para o mercado de trabalho com cursos acessíveis e de alta qualidade.</p>
                </div>
                <div class="footer-section">
                    <h3>Navegação</h3>
                    <p><a href="#sessao_cursos">Cursos</a></p>
                    <p><a href="#depoimentos">Depoimentos</a></p>
                    <p><a href="#certificado">Certificado</a></p>
                    <p><a href="#perguntas_e_respotas">Dúvidas</a></p>
                </div>
                <div class="footer-section">
                    <h3>Legal</h3>
                    <p><a target="_blank" href="https://jovemempreendedor.org/afiliados/politica">Política de Privacidade</a></p>
                    <p><a target="_blank" href="https://jovemempreendedor.org/afiliados/termos">Termos de Uso</a></p>
                    <p><a target="_blank" href="https://jovemempreendedor.org/afiliados/termos">Uso de Cookies</a></p>
                </div>
                <div class="footer-section">
                    <h3>Siga-nos</h3>
                    <div class="footer-socials">
                        <a href="https://www.instagram.com/jovemempreendedororg" target="_blank" aria-label="Siga-nos no Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.youtube.com/@PortalJovemEmpreendedor" target="_blank" aria-label="Siga-nos no Youtube"><i class="fab fa-youtube"></i></a>
                        <a href="https://www.facebook.com/portaljovemempreendedoroficial" target="_blank" aria-label="Siga-nos no Facebook"><i class="fab fa-facebook"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© {{ date('Y') }} Portal Jovem Empreendedor - Todos os direitos reservados.</p>
                <p style="font-size: 0.8rem;">CNPJ: 21.798.932/0001-00</p>
            </div>
        </div>
    </footer>

    

    <!-- Botão WhatsApp Flutuante -->
    @if($info['whatsapp_atendimento'])
    <a href="https://wa.me/{{$info['whatsapp_atendimento']}}?text=Olá! Quero saber mais sobre os cursos do Portal Jovem Empreendedor!" class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>
    @endif

    <!-- Modal de Inscrição (Estrutura do seu código original) -->
    <div class="modal fade" id="inscricaoModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalLabel">@if ($info['whatsapp']) Complete seus dados para falar com um consultor @else Preencha para garantir sua vaga! @endif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Falta pouco! Preencha seus dados abaixo para prosseguir.</p>
                    <form id="inscricaoForm" action="{{ route('lead_whatsapp') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <input type="text" class="form-control" id="input_lead_nome" name="nome" placeholder="Seu nome completo" required>
                            <div class="form-text mt-1">Será usado no seu certificado.</div>
                        </div>
                        <div class="mb-3">
                            <input minlength="13" type="tel" name="telefone" class="form-control input_telefone" placeholder="Seu melhor WhatsApp" required>
                            <div class="form-text mt-1">Para suporte e informações importantes.</div>
                        </div>
                        <input id="input_lead_user_id" type="hidden" name="user_id" value="{{$info['user_id']}}">
                        <input id="input_lead_curso_id" type="hidden" name="curso_id" value="">
                        <input id="input_lead_href" type="hidden" name="link" value="">
                        <input id="input_lead_origem" type="hidden" name="origem" value="whatsapp">
                        <input id="input_lead_cidade" type="hidden" name="cidade" value="@if($info['cidade']) {{$info['cidade']}} @endif">
                        <button type="submit" class="btn btn-primary w-100">
                            @if ($info['whatsapp']) <i class="fab fa-whatsapp"></i> Falar com Consultor @else Concluir Inscrição @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Elementos ocultos para funcionalidades -->
    <div id="iframe-container" style="display: none; visibility: hidden;"></div>
    <div class="modal fade" id="modal_overlay" tabindex="-1"><div class="modal-dialog modal-fullscreen"><div class="modal-content"><div class="modal-body text-center d-flex justify-content-center align-items-center">Aguarde...</div></div></div></div>

    <!-- SCRIPTS (Lógica principal do seu código original) -->
    <script>
        /**CRONOMETRO**/
        var today = new Date();
        today.setHours(23, 59, 59, 999);
        var countDownDate = today.getTime();
        var countdownFunction = setInterval(function() {
            var now = new Date().getTime();
            var distance = countDownDate - now;
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            var countdownElements = document.querySelectorAll('.countdown');
            countdownElements.forEach(function(element) {
                element.innerHTML = hours + "h " + minutes + "m " + seconds + "s";
            });
            if (distance < 0) {
                clearInterval(countdownFunction);
                countdownElements.forEach(function(element) { element.innerHTML = "PROMOÇÃO ENCERRADA"; });
            }
        }, 1000);

        /** CARREGAMENTO DE VÍDEO **/
        function loadVideo(element) {
            if (element.querySelector('iframe')) return;
            var videoId = element.getAttribute('data-video-id');
            var playerHTML = `<div class="plyr__video-embed player"><iframe src="https://www.youtube.com/embed/${videoId}?autoplay=1&origin=${window.location.origin}&modestbranding=1&rel=0" allow="autoplay; fullscreen" allowfullscreen frameborder="0"></iframe></div>`;
            element.innerHTML = playerHTML;
            const player = new Plyr(element.querySelector('.player'));
            element.removeEventListener('click', loadVideo);
        }

        /* ####################################
         * APÓS CARREGAMENTO PARCIAL DA PÁGINA
         * ##################################*/ 
        document.addEventListener('DOMContentLoaded', function() {

           
            


            var bootstrapCSS = document.createElement('link');
            bootstrapCSS.rel = 'stylesheet';
            bootstrapCSS.href = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css';
            document.head.appendChild(bootstrapCSS);
            
            var jquery = document.createElement('script');
            jquery.src = 'https://code.jquery.com/jquery-3.7.1.min.js';
            jquery.async = true; 
            document.head.appendChild(jquery);

            const lazyElements = document.querySelectorAll(".lazy-load");
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("visible");
                        const imgs = entry.target.querySelectorAll("img.lazy-img");
                        imgs.forEach(img => {
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.removeAttribute("data-src");
                            }
                        });
                        observer.unobserve(entry.target);
                    }
                });
            }, { rootMargin: "0px 0px -100px 0px" });
            lazyElements.forEach((div) => observer.observe(div));

            @if(isset($curso->link_checkout_completo))
            const iframe = document.createElement('iframe');
            iframe.src = '{{$curso->link_checkout_completo}}';
            iframe.style.display = 'none';
            document.getElementById('iframe-container').appendChild(iframe);
            @endif
        });

        /* ####################################
        * APÓS CARREGAMENTO COMPLETO DA PÁGINA
        * ##################################*/ 
        window.onload = function() {
            var bootstrapJS = document.createElement('script');
            bootstrapJS.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js';
            bootstrapJS.async = true; 
            document.body.appendChild(bootstrapJS);

            var plyrCSS = document.createElement('link');
            plyrCSS.rel = 'stylesheet';
            plyrCSS.href = 'https://cdn.plyr.io/3.7.8/plyr.css';
            document.head.appendChild(plyrCSS);

            var plyrJS = document.createElement('script');
            plyrJS.src = 'https://cdn.plyr.io/3.7.8/plyr.js';
            plyrJS.async = true; 
            document.body.appendChild(plyrJS);

            var inputmask = document.createElement('script');
            inputmask.src = 'https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js';
            inputmask.async = true; 
            document.body.appendChild(inputmask);

            inputmask.onload = function() {            
                var telefoneInputs = document.querySelectorAll('.input_telefone');            
                telefoneInputs.forEach(function(input) {
                    Inputmask({ mask: ["(99) 9999-9999", "(99) 99999-9999"], keepStatic: true }).mask(input);
                });
            };  
            
            @if($info['formulario'])
            bootstrapJS.onload = function() {
                const links = document.querySelectorAll('.btn-inscricao');
                window.modal = new bootstrap.Modal(document.getElementById('inscricaoModal'));

                links.forEach(link => {
                    link.addEventListener('click', function (event) {
                        event.preventDefault();
                        $('#input_lead_curso_id').val(link.getAttribute('data-cursoid'));
                        $('#input_lead_href').val(link.getAttribute('href'));
                        window.modal.show();
                    });
                });
            };
            @endif

            document.getElementById("inscricaoForm").addEventListener("submit", function(e) {
                e.preventDefault();
                var form = e.target;
                var formData = new FormData(form);
                var nome = document.getElementById("input_lead_nome").value;
                var redirectLink = document.getElementById("input_lead_href").value.replace("{nome}", encodeURIComponent(nome));
                
                fbq('track', 'Lead');
                
                fetch(form.action, { method: "POST", body: formData })
                    .finally(() => {
                        window.location.href = redirectLink;
                    });
            });

            /* FACEBOOK PIXEL TRACKING */
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');

            fbq('init', '419961365827965');
            fbq('init', '948808649224691');
            @if($info['meta_pixel_id'] AND $info['meta_pixel_id']!='948808649224691')
                fbq('init', '{{$info['meta_pixel_id']}}'); 
            @endif
            fbq('track', 'PageView');
        };
        
        document.querySelector('.btn-scroll-curso').addEventListener('click', function(e) {
    e.preventDefault();
    
    const bloco = document.getElementById('sessao_cursos');
    if (bloco) {
        window.scrollTo({
            top: bloco.offsetTop - 50, // Ajusta se quiser parar um pouco antes
            behavior: 'smooth'
        });
    }
});

        
    </script>
    
    <!-- Noscript para Pixels -->
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=419961365827965&ev=PageView&noscript=1"/></noscript>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=948808649224691&ev=PageView&noscript=1"/></noscript>
    @if($info['meta_pixel_id'] AND $info['meta_pixel_id']!='948808649224691')
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{$info['meta_pixel_id']}}&ev=PageView&noscript=1"/></noscript>
    @endif
    
</body>
</html>