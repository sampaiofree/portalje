<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  </head>
  <body>
    <div class="container">
        <div class="row">
            <div class="col-12">

            
        @foreach ($cursos as $curso)
            @if($curso['publicado'] AND $curso['mostrar_na_pagina'])
                <div class="mb-5">
                    <p class="mb-0">#Curso {{$curso['titulo']}}</p>
                    <p class="mb-0">- Link da Página: https://jovemempreendedor.org/{{$curso['url']}}</p>
                    @if($curso['video_dentro_do_curso'])
                    <p class="mb-0">- Vídeo por dentro do curso: https://www.youtube.com/watch?v={{$curso['video_dentro_do_curso']}}</p>
                    @endif
                    @if($curso['video_apresentacao'])
                    <p class="mb-0">- Vídeo de Apresentação: https://www.youtube.com/watch?v={{$curso['video_apresentacao']}}</p>
                    @endif
                    <p class="mb-0">- Link do Checkout: {{$curso['link_checkout_completo']}}</p>
                    <p class="mb-0">- Preço: {{$curso['preco_cheio_completo']}}</p>
                    <p class="mb-0">- Preço parcelado no cartão: {{$curso['preco_parcelado_completo']}}</p>
                    <p class="mb-0">- Carga Horaria: {{$curso['horas_completo']}}</p>
                    <p class="mb-0">- Link da área de membros: {{$curso['link_area_membros']}}</p>

                    
                    <p class="mt-5 mb-0">## Conteúdo</p>
                    @php
                        $html = $curso['conteudo_principal'];
                        preg_match_all('/<li(?! class="ql-indent-1")[^>]*>(.*?)<\/li>/', $html, $topicos);
                    @endphp

                    @foreach ($topicos[1] as $topico)
                            <p class="mb-0">- {{ strip_tags($topico) }}</p>
                    @endforeach

                    <p class="mt-5 mb-0">## Bônus</p>
                    @php
                        $html = $curso['conteudo_bonus'];
                        preg_match_all('/<li(?! class="ql-indent-1")[^>]*>(.*?)<\/li>/', $html, $topicos);
                    @endphp

                    @foreach ($topicos[1] as $topico)
                            <p class="mb-0">- {{ strip_tags($topico) }}</p>
                    @endforeach

                    
                </div>
            @endif
        @endforeach
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
  </body>
</html>