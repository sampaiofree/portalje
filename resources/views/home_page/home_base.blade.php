<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <title>@yield('titulo_pagina')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="@yield('descricao_pagina')" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('Hyper_v5.4/Admin/dist/saas/assets/images/favicon.ico')}}" >
    
    <!-- Theme Config Js -->
    <script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/js/hyper-config.js')}}" ></script>

    <!-- App css -->
    <link href="{{asset('Hyper_v5.4/Admin/dist/saas/assets/css/app-saas.min.css')}}"  rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons css -->
    <link href="{{asset('Hyper_v5.4/Admin/dist/saas/assets/css/icons.min.css')}}"  rel="stylesheet" type="text/css" />
    @yield('head')
    <style>
        #por_que_se_qualificar h3{line-height: 1;}
        #por_que_se_qualificar p{font-size: small;}
        .ri-check-fill{font-size: large; color: green;}
        #alunos{background-image:url('https://jovemempreendedor.org/themes/theme_certificado/assets/img/1500alunos.webp');}
        #alunos iframe{background-color: black;}
        .bg-primary{background-color: #004D99 !important;}
        #certificado p{font-size: large;}
    </style>
</head>

<body>
<!-- Begin page -->
<div class="wrapper">
  <section class="container-fluid">
    @yield('slide')

    {{-- LOGO HEADLINE --}}
    <div class="row py-5 bg-primary text-white">
        <div class="col-12  text-center px-0">
            <img loading="lazy" alt="Portal Jovem Empreendedor" src="https://jovemempreendedor.org/themes/theme_certificado/assets/img/logojecolor.webp" class="img-fluid " width="200" height="52">
            <h1 class="mt-2 h2 mb-0 W-75 mx-auto" style="font-size: x-large;"><strong>{{$pagina['headline']}}</strong></h1>
            <h2 class="w-75 mt-1 mx-auto" style="font-size: 18px;">{{$pagina['headline_sub']}}</h2>
            <a href="#content" class="btn btn-light">{{$pagina['headline_botao']}}</a>
        </div>
    </div>

    {{-- POR QUE SE QUALIFICAR --}}
    <div class="row py-5 px-2">
        <div class="col-12 text-center">
            <h2 class="h3">4 motivos para você fazer um curso profissionalizante</h2>
        </div>
        <div id="por_que_se_qualificar" class="row flex-nowrap overflow-auto p-2">
            <div class="col-lg-2 offset-lg-2 col-11 p-1">
                <div class="card cta-box border h-100">
                    <div class="card-body p-2">
                        <div class="text-center">
                            <img class="img-fluid rounded-circle" src="https://jovemempreendedor.org/themes/wc_portal/imageswebp/qualificacao.webp" width="120" alt="Generic placeholder image">
                        </div>
                        <h3 class="mt-3 fw-normal cta-box-title">Cansado de perder oportunidades por <b>falta de qualificação?</b></h3>
                        <p class="mt-2">Destaque-se no mercado de trabalho com qualificação de excelência! Nossos cursos são a chave para abrir as portas de um emprego dos sonhos. Invista em você e aumente suas chances de uma carreira de sucesso.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-11 p-1">
                <div class="card cta-box border h-100">
                    <div class="card-body p-2">
                        <div class="text-center">
                            <img class="img-fluid rounded-circle" src="https://jovemempreendedor.org/themes/wc_portal/imageswebp/semexperiencia.webp" width="120">
                        </div>
                        <h3 class="mt-3 fw-normal cta-box-title">Não te contratam por que <b>não tem experiência?</b></h3>
                        <p class="mt-2">Prepare-se para o mercado com os melhores professores! Nossos cursos proporcionam uma base teórica sólida, complementada por um carta de estágio. Assim, você terá a oportunidade de buscar experiência prática na sua área de atuação, aumentando sua empregabilidade.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-11 p-1">
                <div class="card cta-box border h-100">
                    <div class="card-body p-2">
                        <div class="text-center">
                            <img class="img-fluid rounded-circle" src="https://jovemempreendedor.org/themes/wc_portal/imageswebp/primeiroemprego.webp" width="120" alt="Generic placeholder image">
                        </div>
                        <h3 class="mt-3 fw-normal cta-box-title">Está buscando o seu <b>primeiro emprego?</b></h3>
                        <p class="mt-2">Dê o primeiro passo na sua carreira com confiança! Nosso programa de capacitação é desenhado para garantir que você entre no mercado de trabalho pronto para impressionar desde o primeiro dia.

                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-11 p-1">
                <div class="card cta-box border h-100">
                    <div class="card-body p-2">
                        <div class="text-center">
                            <img class="img-fluid rounded-circle" src="https://jovemempreendedor.org/themes/wc_portal/imageswebp/empregomelhor.webp" width="120" alt="Generic placeholder image">
                        </div>
                        <h3 class="mt-3 fw-normal cta-box-title">Você quer um <b>falta de salário melhor?
                        </b></h3>
                        <p class="mt-2">Impulsione seu potencial de ganhos! Com nossa formação, você se qualifica para posições mais elevadas e salários competitivos. Seja um profissional requisitado e valorizado no mercado.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="content"></div>
    @yield('content')

    <div id="beneficios" class="row py-5 " style="background-color:#0159A8;">
        <div class="col-12 col-sm-6 offset-sm-3 col-lg-4 offset-lg-4">
            <p class="h3 text-white"><strong>Benefícios de você entrar no Programa</strong></p>
            <ul class="text-white">
                <li>Aumente suas chances de contratação imediatamente</li>
                <li>Aprenda técnicas modernas e eficazes que os empregadores procuram</li>
                <li>Obtenha habilidades práticas que você pode aplicar desde o primeiro dia no emprego</li>
                <li>Curso 100% online: Estude de onde estiver e quando puder</li>
            </ul>
        </div>
        <div class="col-12 col-sm-6 offset-sm-3 col-lg-4 offset-lg-4 mt-1">
            <p class="h3 text-white"><strong>Diferenciais</strong></p>
            <ul class="text-white">
                <li>Certificado reconhecido em todo o Brasil</li>
                <li>Instrutores renomados com vasta experiência</li>
                <li>O curso é tão rápido que você pode começar a se candidatar para as vagas já na próxima semana!</li>
                <li>Acesso vitalício</li>
            </ul>
        </div>
    </div>

    <div id="certificado" class="row py-5 px-0" >
        <div class="col-12 col-sm-6 offset-sm-3">
            <h2><strong>Conheça o curso que vai fazer você entrar no mercado de trabalho mais rápido, <span style="text-decoration: underline;">mesmo sem experiência.</span></strong></p>
            <p> Com vídeo aulas fáceis de assistir, você termina rápido, em menos de duas semanas, e recebe um <strong>certificado que vale em todo o Brasil.</strong></p>
            
        </div>
        <div class="row" style="background: linear-gradient(to bottom, #9DADBF, #97AABC);">
            <div class="text-center">
                <img loading="lazy" class="lazyload-imagem img-fluid" alt="Portal Jovem Empreendedor" src="https://jovemempreendedor.org/themes/theme_certificado/assets/img/certificadoNovo2.webp">
            </div>
        </div>
        <div class="col-12 col-sm-6 offset-sm-3 col-lg-4 offset-lg-4 mt-3">
                <p class="d-flex align-items-center"><i class=" ri-check-fill "></i>Certificado Reconhecido em Todo o Brasil</p>
                <p class="d-flex align-items-center"><i class=" ri-check-fill "></i>Tem a Mesma validade de um Curso Presencial</p>
                <p class="d-flex align-items-center"><i class=" ri-check-fill "></i>Assinatura digital</p>
                <p class="d-flex align-items-center"><i class=" ri-check-fill "></i>Válido como Extensão universitária</p>
                <p class="d-flex align-items-center"><i class=" ri-check-fill "></i>Concursos públicos (mediante verificação do edital)</p>
        </div>   
    </div>

    <div id="alunos" class="row px-0 py-5 ">
        <div class="col-12 col-lg-6 offset-lg-3 col-sm-10 offset-sm-1 text-center" style="z-index: 2">
            <h1 class="h2 mb-0 text-white sombraTexto"><strong>São mais de 120 mil alunos no Brasil e em 14 países</strong></h1>
            <p class="w-lg-50 mt-0 text-white sombraTexto"> Veja o eles estão dizendo sobre nossos cursos?</p>
            <div class="row flex-nowrap overflow-auto">
                <div class="col-11 col-lg-4">
                    <div class="ratio rounded-3 border border-dark ratio-16x9">
                        <iframe class="lazyload-iframe" width="100%" data-src="https://www.youtube.com/embed/rejxwJ2lX-Q" title="YouTube video" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="col-11 col-lg-4   ">
                    <div class="ratio rounded-3 border border-dark ratio-16x9">
                        <iframe class="lazyload-iframe" width="100%" data-src="https://www.youtube.com/embed/1hekoAyPVRs" title="YouTube video" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="col-11 col-lg-4   ">
                    <div class="ratio rounded-3 border border-dark ratio-16x9">
                        <iframe class="lazyload-iframe" width="100%" data-src="https://www.youtube.com/embed/Mnn2yIAlhZk" title="YouTube video" allowfullscreen></iframe>
                    </div>
                </div>
    
                <div class="col-11 col-lg-4   ">
                    <div class="ratio rounded-3 border border-dark ratio-16x9">
                        <iframe class="lazyload-iframe" width="100%" data-src="https://www.youtube.com/embed/9mmtunKAnMY" title="YouTube video" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="col-11 col-lg-4   ">
                    <div class="ratio rounded-3 border border-dark ratio-16x9">
                        <iframe class="lazyload-iframe" width="100%" data-src="https://www.youtube.com/embed/uQ5lB9r8ZlI" title="YouTube video" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="col-11 col-lg-4   ">
                    <div class="ratio rounded-3 border border-dark ratio-16x9">
                        <iframe class="lazyload-iframe" width="100%" data-src="https://www.youtube.com/embed/dMIxLKj35aU" title="YouTube video" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="col-11 col-lg-4   ">
                    <div class="ratio rounded-3 border border-dark ratio-16x9">
                        <iframe class="lazyload-iframe" width="100%" data-src="https://www.youtube.com/embed/gIV1MGief-0" title="YouTube video" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="col-11 col-lg-4   ">
                    <div class="ratio rounded-3 border border-dark ratio-16x9">
                        <iframe class="lazyload-iframe" width="100%" data-src="https://www.youtube.com/embed/X1IJZkVXgBw" title="YouTube video" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="col-11 col-lg-4   ">
                    <div class="ratio rounded-3 border border-dark ratio-16x9">
                        <iframe class="lazyload-iframe" width="100%" data-src="https://www.youtube.com/embed/1qWXa9F0qBw" title="YouTube video" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <div id="bonus" class="row py-5">
        <div class="col-12 col-lg-6 offset-lg-3 col-sm-10 offset-sm-1 ">
            <h2 class="h2 mb-1 text-center">Ao se inscrever agora</h2>
            <p class="mb-0 text-center">Você poderá ganhar todos esses bônus abaixo gratuitamente.</p>
            <p style="font-size: small" class="text-center">Estes bônus não são vendidos separadamente.</p>
            
            <div class="row  my-2 d-flex align-items-center">
                <div class="col-3 col-sm-2 offset-sm-3">
                    <img loading="lazy" alt='Portal Jovem Empreendedor'  class="lazy lazyload-imagem rounded-circle img-fluid" src="https://jovemempreendedor.org/themes/theme_certificado/assets/img/cartaestagio.webp">
                </div>
                <div class="col-9 col-sm-4" >
                    <div class="row d-flex align-items-center justify-content-center" style="min-height: 100px;">
                        <p class="col-12 h6 feature-title"><strong>Carta de Estágio</strong></p>
                        <p class="col-12 feature-description">Uma ferramenta poderosa para abrir portas no competitivo mercado de trabalho, oferecendo um grande diferencial em seu currículo.</p>
                        <p class="col-12">DE <s>R$ 197,00</s> <span class="text-laranja">por R$ 0,00</span></p>
                    </div>
                </div>
            </div>
            <div class="row d-flex align-items-center ">
                <div class="col-3 col-sm-2 offset-sm-3">
                    <img loading="lazy" alt='Portal Jovem Empreendedor'   class="lazy lazyload-imagem rounded-circle img-fluid" src="https://jovemempreendedor.org/themes/theme_certificado/assets/img/jovemaprendiz.webp">
                </div>
                <div class="col-9 col-sm-4">
                    <div class="row d-flex align-items-center justify-content-center" style="min-height: 100px;">
                        <p class="col-12 h6 feature-title"><strong>Preparatório para Jovem Aprendiz</strong></p>
                        <p class="col-12 feature-description">Treinamento em vídeo aulas para ajudar você a ser aprovado neste programa altamente competitivo.</p>
                        <p class="col-12">DE <s>R$ 297,00</s> <span class="text-laranja">por R$ 0,00</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="garantia" class="row py-5 bg-primary text-white ">
        <div class="col text-center">
            <img loading="lazy" alt='Portal Jovem Empreendedor' class="lazyload-imagem img-fluid" src="https://jovemempreendedor.org/themes/theme_certificado/assets/img/garantia-7-dias.png" width="150" height="150">
            <p class="text-center">Satisfação garantida ou seu dinheiro de volta! Você tem 7 dias para experimentar o curso sem riscos</p>
            <p class="text-center">Não deixe para depois o que você pode fazer hoje para mudar sua vida. As vagas são limitadas e a demanda é alta. Inscreva-se agora!</p>
        </div>
    </div>

    <div id="perguntas_e_respotas" class="row py-5">
        <div class="col-12 col-sm-6 offset-sm-3">
            <h2 class="h2 text-center mb-1">Perguntas e Respostas</h2>
            <p class="text-center">Ficou alguma dúvida? Clique nas perguntas abaixo.</p>
            <div class="accordion accordion-flush" id="accordionFlushExample">
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

  <footer class="py-5 px-2 bg-dark">
    <div class="container-fluid">
        <div class="row   text-white">
            <div class="col-sm-3 offset-sm-3 mt-3">
                <img loading="lazy" class="lazyload-imagem" alt='Portal Jovem Empreendedor' width="200" height="49" src="https://jovemempreendedor.org/themes/theme_certificado/assets/images/logo/logowhite.png" >
                <p class="description">Nossa missão é especializar jovens, equipando-os com as habilidades e conhecimentos essenciais para se destacarem em seu primeiro emprego.</p>
            </div>
            <div class="col-sm-3 mt-3">
                <p class="h5 widget-title">Informações para Contato</p>
                <p class="text-white d-flex align-items-center mb-0"><i class=" ri-building-2-fill me-2"></i>Sede Nacional: Rua Tristão Monteiro nº 1520, Centro, Taquara - RS.</p>
                {{-- <p class="text-white d-flex align-items-center mb-0"><span class="material-symbols-outlined me-2">call</span><a class="text-white" href="https://wa.me/<?php echo $telefoneContato?>">+<?php echo $telefoneContato?></a></p> --}}
                <p class="text-white d-flex align-items-center mb-0"><i class="  ri-mail-line  me-2 "></i><a class="text-white" target="_blank" href="mailto:atendimento@jovemempreendedor.org">atendimento@jovemempreendedor.org</a></p>
            </div>
        </div>
        <div class="row mt-3   text-white">
            <div class="col-12 col-sm-6 offset-sm-3">
                {{-- <p>Programa Jovem Empreendedor - Copyright <?php
                    $anoAtual = date("Y");
                    echo $anoAtual; // Isso exibirá o ano atual, por exemplo, 2023
                    ?> Todos os Direitos Reservados -
                    <a class="text-white" target="_blank" href="https://<?php echo $_SERVER['HTTP_HOST'];?>/termos-de-uso">Termos de Uso</a> -
                    <a class="text-white" target="_blank" href="https://<?php echo $_SERVER['HTTP_HOST'];?>/politica-de-privacidade">Política de Privacidade</a>
                </p> --}}
            </div>
        </div>
    </div>
</footer>
</div>
{{-- <footer class="footer">
  <div class="container-fluid">
      <div class="row">
          <div class="col-md-6">
              <script>document.write(new Date().getFullYear())</script> © portalje.org
          </div>
          <div class="col-md-6">
              <div class="text-md-end footer-links d-none d-md-block">
                  <a href="javascript: void(0);">About</a>
                  <a href="javascript: void(0);">Support</a>
                  <a href="javascript: void(0);">Contact Us</a>
              </div>
          </div> 
      </div>
  </div>
</footer> --}}
<!-- Vendor js -->
<script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/js/vendor.min.js')}}"></script>

<!-- App js -->
<script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/js/app.min.js')}}"></script>

<script>
    //MOSTRAR OS IFRAMES
    document.addEventListener("DOMContentLoaded", function() {
        var observador = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var iframe = entry.target;
                    iframe.src = iframe.getAttribute('data-src');
                    iframe.onload = function() {
                        iframe.style.opacity = 1;
                    };
                    observador.unobserve(iframe); // Para de observar o iframe depois que ele é carregado
                }
            });
        }, {
            rootMargin: '0px',
            threshold: 0.1 // Ajuste conforme necessário para quando o iframe deve começar a carregar
        });

        // Adiciona todos os iframes que devem ser observados
        document.querySelectorAll('iframe.lazyload-iframe').forEach(function(iframe) {
            observador.observe(iframe);
        });
    });

    //MOSTRAR A IMAGEM DOS CURSO DE FORMA FORÇADA CASO LAZY NÃO FUNCIONE
    document.addEventListener("DOMContentLoaded", function() {
        let observador = new IntersectionObserver((entradas) => {
            entradas.forEach(entrada => {
                if (entrada.isIntersecting) {
                    const img = entrada.target;
                    // Se você optar por substituir a fonte da imagem,
                    // certifique-se de que o atributo 'data-src' contém o caminho correto da imagem
                    // img.src = img.getAttribute('data-src');
                    img.removeAttribute('loading'); // Força o carregamento imediato
                    observador.unobserve(img); // Deixa de observar a imagem para melhorar a performance
                }
            });
        }, {
            rootMargin: "100px 0px", // Ajuste isso para carregar imagens antes de chegarem à viewport
            threshold: 0.01
        });

        const imagens = document.querySelectorAll('img.lazyload-imagem');
        imagens.forEach(img => {
            observador.observe(img);
        });
    });
</script>

@yield('scripts')
</body>

</html>