<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Jovem Empreendedor</title>
    <link rel="icon" href="{{asset('/img/logo/logo-je-sm.png')}}" type="image/x-icon">
    <meta name="google-adsense-account" content="ca-pub-9796869151117705">
    @yield('head')

    <style>
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

        
    
    /**APENAS MOBILE**/
    @media (max-width: 768px) {

    
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
    </style>
  </head>
  <body>
    @yield('body_head')
    <script async>
        //INICIAR PLRY
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

        document.addEventListener("DOMContentLoaded", function () {
            
        });


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

            // CARREGAR DIVS COM LAZY LOAD
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

           
        };   
    </script>
    
    
    <!-- Rodapé -->
    <footer id="rodape" class="text-white py-5 bg-dark" >
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-4 offset-lg-4">
                <div class="row">
                    <!-- Logo -->
                    <div class="col-sm-4 mb-2 text-center text-md-start lazy-load">
                        <img data-src="{{asset('img/home_page/logowhite.png')}}" alt="Logo" class=" img-fluid  mb-2 " style="width: 150px; height: 36.36px ">
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
     @yield('end_body')
  </body>
</html>