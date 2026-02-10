<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>{{$curso->titulo." ".$curso->headline}}</title>
  <link rel="icon" href="{{asset('/img/logo/logo-je-sm.png')}}" type="image/x-icon">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Meta description -->
  <meta name="description" content="{{$curso->descricao_curta}}">
  <!-- Meta keywords -->
  <meta name="keywords" content="{{$curso->titulo}}, Curso, Certificado, Online, Profissionalização, Emprego, Educação, Carreira, Certificação, Portal Jovem Empreendedor, Programa Jovem Empreendedor, qualificação profissional, mercado de trabalho, capacitação profissional, curso com certificado, curso profissionalizante.">
  <!-- Meta author -->
  <meta name="author" content="Portal Jovem Empreendedor">
  <meta name="robots" content="index, follow">
  <meta property="article:published_time" content="2024-10-24T08:00:00Z">
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9796869151117705" crossorigin="anonymous"></script>
  <meta name="google-adsense-account" content="ca-pub-9796869151117705">

  <!-- Open Graph -->
  <meta property="og:title" content="{{$curso->titulo." ".$curso->headline}}">
  <meta property="og:description" content="{{$curso->descricao_curta}}">
  <meta property="og:type" content="article">
  <meta property="og:url" content="https://jovemempreendedor.org/{{$curso->url}}">
  <meta property="og:image" content="{{asset('/storage/'.$curso->capa_quadrada)}}">
  <meta property="og:image:alt" content="{{$curso->titulo}}">
  <meta property="og:site_name" content="Portal Jovem Empreendedor">
  <meta property="og:locale" content="pt_BR">

  <!-- === CSS EXTERNO (MAIS OTIMIZADO) === -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />

  <style>
    .plyr__video-wrapper iframe{
        width: 600% !important; 
        margin-left: -250% !important;
    }

    .form-control::placeholder {
        color: white;
    }


    .video-overlay-top {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 15%;
        z-index: 2;
    }


    .ytp-title-link{
        display: none;
    }
    
    :root {
      --bg-dark: #181b1e;
      --bg-darker: #141414;
      --text-light: #f5f5f5;
      --text-muted: #a0a0a0;
      --brand-green: #28a745;
      --border-color: #303030;
    }
    body {
      background-color: var(--bg-dark);
      color: var(--text-light);
      font-family: 'Poppins', sans-serif;
    }
    .netflix-header {
      background-color: var(--bg-darker);
      padding: 1rem 1.5rem;
      border-bottom: 1px solid var(--border-color);
      position: sticky;
      top: 0;
      z-index: 1030;
    }
    .netflix-header .logo { max-height: 40px; }
    main { padding: 2rem 0; }
    .video-container {
      position: relative;
      background-color: #000;
      border-radius: 8px;
      overflow: hidden;
      aspect-ratio: 16 / 9;
    }
    .video-facade {
        cursor: pointer; position: relative; width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
    }
    .video-facade img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease; }
    .video-facade:hover img { transform: scale(1.05); }
    .play-button-overlay {
        position: absolute; width: 80px; height: 80px; background-color: rgba(0, 0, 0, 0.6);
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        border: 2px solid white; transition: background-color 0.3s ease;
    }
    .play-button-overlay .bi-play-fill { font-size: 40px; color: white; margin-left: 5px; }
    .video-facade:hover .play-button-overlay { background-color: rgba(229, 9, 20, 0.8); }
    .playlist-container {
      background-color: var(--bg-darker); border-radius: 8px;
      padding: 1rem; max-height: 70vh; overflow-y: auto;
    }
    .playlist-container::-webkit-scrollbar { width: 8px; }
    .playlist-container::-webkit-scrollbar-thumb { background: #555; border-radius: 4px; }
    .playlist-header { border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; margin-bottom: 0.75rem; }
    .playlist-item {
        display: flex; align-items: center; gap: 1rem; padding: 0.75rem;
        border-radius: 6px; text-decoration: none; color: var(--text-light);
        transition: background-color 0.2s ease; border: 1px solid transparent;
    }
    .playlist-item:not(:last-child) { margin-bottom: 0.5rem; }
    .playlist-item:hover { background-color: #2a2a2a; }
    .playlist-item.active { background-color: rgba(255, 255, 255, 0.1); border: 1px solid var(--brand-green); }
    .playlist-item .thumbnail { width: 100px; height: 56px; object-fit: cover; border-radius: 4px; flex-shrink: 0; }
    .playlist-item .item-info { flex-grow: 1; overflow: hidden; }
    .playlist-item .item-title { font-size: 0.9rem; font-weight: 500; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .playlist-item .now-playing { color: var(--brand-green); font-size: 0.8rem; font-weight: bold; }
    .certificate-section {
      background-color: var(--bg-darker); border-radius: 8px;
      padding: 1.5rem; margin-top: 1.5rem; text-align: center;
    }
    .btn-certificate {
        background: linear-gradient(45deg, var(--brand-green), #34d058); border: none; padding: 12px 25px;
        font-weight: bold; color: white; border-radius: 5px; text-transform: uppercase;
        transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        display: block;
    }
    .btn-certificate:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4); display: block;}
    footer { border-top: 1px solid var(--border-color); padding: 2rem 0; font-size: 0.9rem; }
    footer a { color: var(--text-muted); text-decoration: none; transition: color 0.2s; }
    footer a:hover { color: var(--text-light); }
    #loginModal .modal-content { background-color: var(--bg-dark); }
    #loginModal label { color: var(--text-muted); }
    #loginModal .form-control { background-color: #333; border: 1px solid #555; color: white; }
    #loginModal .form-control:focus { background-color: #333; border-color: var(--brand-green); box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5); color: white; }
    .plyr { border-radius: 8px;min-width: 100%; }
  </style>
</head>
<body> 

    @php
        // MUDANÇA 1: LÓGICA PARA DETERMINAR O VÍDEO ATIVO
        $aulas = $curso->aulas_demonstrativas;
        $primeiraAulaId = !$aulas->isEmpty() ? str_replace("https://youtu.be/", "", $aulas->first()['aula_id_youtube']) : '';

        
        // Pega o ID da aula da URL, ou usa o ID da primeira aula como padrão
        $activeVideoId = request()->query('aula', $primeiraAulaId);
        
        // Define a thumbnail e o número de aulas a serem exibidas
        $activeVideoThumbnail = "https://img.youtube.com/vi/{$activeVideoId}/hqdefault.jpg";
        $n_aulas = request()->query('n_aulas', 25);
    @endphp
    
    <div id="alertaTelefone" class="alert alert-danger alert-dismissible fade position-fixed top-0 start-50 translate-middle-x mt-3 w-75" role="alert" style="z-index: 99999999; display: none;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> Por favor, digite um WhatsApp válido com DDD.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>

    <header class="netflix-header">
        <img src="{{asset('img/home_page/logowhite.png')}}" alt="Logo Portal Jovem Empreendedor" class="logo">
    </header>

    <main class="container-fluid">
        <div class="row gx-4">
            <!-- Coluna do Vídeo -->
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="video-container shadow-lg">
                    <!-- MUDANÇA 2: O player agora usa as variáveis do vídeo ativo -->
                    <div id="video-player-area" class="video-facade" data-video-id="{{ $activeVideoId }}" onclick="loadVideo(this)">
                        <img loading="lazy" src="{{ $activeVideoThumbnail }}" alt="{{ $curso->titulo }}" class="img-fluid">
                        <div class="play-button-overlay">
                            <i class="bi bi-play-fill"></i>
                        </div>
                    </div>
                    <div class="video-overlay-top"></div>
                </div>
            </div>

            <!-- Coluna da Playlist de Aulas -->
            <div class="col-lg-4">
                <div class="playlist-container shadow-lg">
                    <div class="playlist-header">
                        <h5 class="fw-bold mb-1">{{ $curso->titulo }}</h5>
                        <p class="small mb-0" style="color">Selecione uma aula para começar</p>
                    </div>
                    
                    <div id="playlist-list">
                        @if(isset($aulas) && !$aulas->isEmpty())
                            @foreach($aulas->take($n_aulas) as $aula)
                                @php
                                    $aula_id_youtube = str_replace("https://youtu.be/", "", $aula['aula_id_youtube']); 
                                    // MUDANÇA 3: Lógica para marcar a aula ativa e definir o link de recarregamento
                                    $is_active = ($aula_id_youtube == $activeVideoId);
                                    $aula_link = request()->url() . '?ga=1&aula=' . $aula_id_youtube;
                                @endphp
                                <a href="{{ $aula_link }}" class="playlist-item @if($is_active) active @endif">
                                    <img src="https://img.youtube.com/vi/{{$aula_id_youtube}}/mqdefault.jpg" alt="Miniatura da Aula" class="thumbnail">
                                    <div class="item-info">
                                        <p class="item-title">{{ $aula['aula_titulo'] }}</p>
                                        @if($is_active)
                                            <span class="now-playing">Tocando agora...</span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>

                @php
                    if($curso->gratuito){
                        $p = "Após assistir as aulas, emita seu certificado.";
                        $a = "<button data-bs-toggle='modal' data-bs-target='#certModal' class='btn-certificate'>
                                    <i class='bi bi-patch-check-fill me-2'></i>Emitir meu Certificado
                                </button> ";
                    }else{

                        $a = "<a href='{$curso->link_checkout_completo}' class='btn-certificate'>Quero Me Inscrever Agora</a>";
                        $p = "Inscreva-se agora mesmo <br>para liberar todas as aulas.";

                    };
                @endphp

                <div class="certificate-section">
                    {!!$p!!}
                    {!!$a!!}
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Certificado -->
    <div class="modal fade" id="certModal" tabindex="-1" aria-labelledby="certModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-light border-secondary">
                <div class="modal-header border-bottom-secondary">
                    <h5 class="modal-title" id="certModalLabel">Por que cobramos pelo certificado?</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p>Oferecemos o curso 100% gratuito, mas a emissão de um certificado válido, com registro e segurança, tem um custo simbólico.</p>
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-center mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Garante autenticidade com seu nome e data</li>
                        <li class="d-flex align-items-center mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Carga horária reconhecida</li>
                        <li class="d-flex align-items-center mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Arquivo em PDF disponível imediatamente</li>
                        <li class="d-flex align-items-center"><i class="bi bi-check-circle-fill text-success me-2"></i> Suporte e validação digital</li>
                    </ul>
                    <p class="mt-3"><strong>Resultado:</strong> um certificado profissional, pronto para seu currículo e processos seletivos.</p>
                </div>
                <div class="modal-footer border-top-secondary justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <a href="{{$curso->link_checkout_completo}}" class="btn btn-success fw-bold">Quero meu Certificado Agora</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Fullscreen LOGIN -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content d-flex justify-content-center align-items-center text-center p-4">
                <form id="form-login-whatsapp" style="max-width: 400px; width: 100%;">
                    @csrf
                    <div class="mb-3">
                        <img src="{{asset('/storage/'.$curso->capa_quadrada)}}" alt="{{$curso->titulo}}" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <h4 class="mb-2 fw-bolder">{{$curso->titulo}}</h4>
                    <p class="text-white mb-4">Preencha os dados abaixo para começar seu curso gratuito.</p>

                    <div class="mb-3 text-start">
                        <label for="nome" class="form-label text-white">Nome Completo (para o certificado)</label>
                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Digite seu Nome" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="whatsapp" class="form-label text-white">Seu WhatsApp com DDD</label>
                        <input type="text" class="form-control" id="whatsapp" name="whatsapp" placeholder="(99) 99999-9999" required>
                        <small id="erro-whatsapp" class="text-danger mt-1" style="display: none;">WhatsApp inválido.</small>
                    </div>
                    
                    <button type="submit" class="btn btn-success fw-bolder w-100 py-2 mt-3">ACESSAR O CURSO</button>
                    <p class="text-white small mt-2">Ao acessar, você concorda em receber avisos sobre o curso via WhatsApp.</p>
                </form>
            </div>
        </div>
    </div>
 
    <!-- Rodapé -->
    <footer class="bg-dark text-muted pt-5 pb-4">
        <div class="container text-center text-md-start">
            <div class="row">
                <div class="col-md-4 col-lg-4 col-xl-4 mx-auto mb-4">
                    <img loading="lazy" src="{{asset('img/home_page/logowhite.png')}}" alt="Logo" class="img-lazy mb-3" style="max-width: 180px;">
                    <p style="font-size: small">Cnpj: 21.798.932/0001-00</p>
                </div>
                <div class="col-md-4 col-lg-2 col-xl-2 mx-auto mb-4">
                    <h6 class="text-uppercase fw-bold mb-4 text-light">Links Úteis</h6>
                    <p><a target="_blank" href="https://jovemempreendedor.org/afiliados/politica">Política de Privacidade</a></p>
                    <p><a target="_blank" href="https://jovemempreendedor.org/afiliados/termos">Termos de Uso</a></p>
                    <p><a target="_blank" href="https://jovemempreendedor.org/afiliados/termos">Uso de Cookies</a></p>
                </div>
                <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                    <h6 class="text-uppercase fw-bold mb-4 text-light">Siga-nos</h6>
                    <div>
                        <a href="https://www.instagram.com/jovemempreendedororg" target="_blank" class="me-3" aria-label="Instagram"><i class="bi bi-instagram fs-4"></i></a>
                        <a href="https://www.youtube.com/@PortalJovemEmpreendedor" target="_blank" class="me-3" aria-label="Youtube"><i class="bi bi-youtube fs-4"></i></a>
                        <a href="https://www.facebook.com/portaljovemempreendedoroficial" target="_blank" aria-label="Facebook"><i class="bi bi-facebook fs-4"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>
    <script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>

    <script>
    // MUDANÇA 4: SCRIPT SIMPLIFICADO
    let player;

    // Esta função agora só carrega o player na primeira vez que o usuário clica.
    function loadVideo(element) {
    if (element.querySelector('iframe')) return;

    var videoId = element.getAttribute('data-video-id');
    var playerHTML = `
        <div class="plyr__video-embed" style="position: relative;">
            <iframe
                src="https://www.youtube.com/embed/${videoId}?autoplay=1&origin=${window.location.origin}&modestbranding=1&rel=0&iv_load_policy=3&playsinline=1&controls=1"
                allow="autoplay; fullscreen"
                allowfullscreen
                frameborder="0"
            ></iframe>
            <div class="video-overlay-top"></div>
        </div>`;
    
    element.innerHTML = playerHTML;

    player = new Plyr('.plyr__video-embed', {
        controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
    });
}


    // A função changeVideo() foi REMOVIDA.

    // Restante do seu JavaScript (modal, formulário, pixel) permanece igual...
    document.addEventListener('DOMContentLoaded', function () {
        if (!localStorage.getItem('loginModalShown')) {
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        }

        $('#whatsapp').inputmask('(99) 99999-9999');
        $('#whatsapp').on('input', function () {
            $('#erro-whatsapp').toggle(!$(this).inputmask("isComplete"));
        });

        $('#form-login-whatsapp').on('submit', function (e) {
            e.preventDefault();
            if (!$('#whatsapp').inputmask("isComplete")) {
                $('#alertaTelefone').fadeIn().delay(3000).fadeOut();
                return;
            }
            localStorage.setItem('loginModalShown', 'true');
            bootstrap.Modal.getInstance(document.getElementById('loginModal')).hide();
            
            const urlParams = new URLSearchParams(window.location.search);
            const origem = Array.from(urlParams.entries())
                .map(([key, value]) => `${key}=${value}`)
                .join('&');
            
            $.ajax({
                url: "{{ route('curso_gratuito_lead') }}", 
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    nome: $('#nome').val(),
                    whatsapp: $('#whatsapp').val(),
                    curso_id: "{{$curso->id}}",
                    curso: "{{$curso->titulo}}",
                    evento_portal: "CURSO GRATUITO",
                    origem: origem,
                },
                success: function(response) {
                    console.log('Lead salvo com sucesso:', response);
                    if (typeof fbq === 'function') fbq('track', 'Lead');
                },
                error: function(xhr) {
                    console.error('Erro ao salvar lead:', xhr.responseJSON || xhr.responseText || 'Erro desconhecido');
                }

            });
        });
        
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
        @if($curso->meta_pixel_id && $curso->meta_pixel_id != "948808649224691")
            fbq('init', '{{$curso->meta_pixel_id}}'); 
        @endif
        fbq('track', 'PageView');
    });
    </script>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Course",
      "name": "{{$curso->titulo}}",
      "description": "{{$curso->descricao_curta}}",
      "provider": {
        "@type": "Organization",
        "name": "Portal Jovem Empreendedor",
        "sameAs": "https://jovemempreendedor.org"
      }
    }
    </script>
</body>
</html>