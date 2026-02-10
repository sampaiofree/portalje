<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{asset('/img/logo/logo-je-sm.png')}}" type="image/x-icon">
    <meta name="author" content="Portal Jovem Empreendedor">
    <meta name="robots" content="index, follow">
    <meta name="keywords" content="{{$curso->titulo}}, Curso, Certificado, Online, Profissionalização, Emprego, Educação, Carreira, Certificação, Portal Jovem Empreendedor, Programa Jovem Empreendedor, qualificação profissional, mercado de trabalho, capacitação profissional, curso com certificado, curso profissionalizante.">

    <meta property="og:site_name" content="Portal Jovem Empreendedor">
    <meta property="og:locale" content="pt_BR">
    
    <!-- CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    
    @yield('head')
</head>
<body>

    <!-- Inclui a barra de urgência (se existir) -->
    @include('partials._urgency-bar')

    <!-- Conteúdo principal da página específica -->
    <main>
        @yield('content')
    </main>

    <!-- Seções comuns a todas as páginas -->
    @include('partials._testimonials')
    @include('partials._certificate')
    @include('partials._faq')

    <!-- Rodapé -->
    @include('partials._footer')

    <!-- Botão WhatsApp Flutuante -->
    @if($info['whatsapp_atendimento'] ?? false)
    <a href="https://wa.me/{{$info['whatsapp_atendimento']}}?text=Olá! Quero saber mais sobre os cursos!" class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>
    @endif

    <!-- Modal de Inscrição (Comum a todas as páginas) -->
    @include('partials._modal-inscricao')

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    
    <!-- Scripts de Tracking -->
    @include('partials._tracking-scripts')
</body>
</html>