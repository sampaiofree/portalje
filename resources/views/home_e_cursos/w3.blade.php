<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Jovem Empreendedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <!-- Icons css -->
    <link href="{{asset('Hyper_v5.4/Admin/dist/saas/assets/css/icons.min.css')}}"  rel="stylesheet" type="text/css" />
    <style>
         #por_que_se_qualificar h3{line-height: 1;}
        #por_que_se_qualificar p{font-size: medium;}
        .ri-check-fill{font-size: large; color: green;}
        /*#alunos{background-image:url('');}*/
        #alunos iframe{background-color: black;}
        .bg-primary{background-color: #004D99 !important;}
        #certificado p{font-size: large;}
        h4{font-size: 1.4rem;font-weight: 300;}
        h5{font-size: 1.2rem;font-weight: 300;}
        body {
            font-family: 'Open Sans', sans-serif;
            color: #303030;
        }

        h1, h2, h3, h4, h5, h6 {
            font-weight: bolder
        }

        strong {
            font-weight: bolder
        }

        .justify {
            text-align: justify
        }

        /*MOBILE*/
        @media (max-width: 767px) {
            .econderMobile {
                display: none;
                visibility: hidden
            }
        }

        /*DESKTOP*/
        @media (min-width: 768px) {
            .econderDesktop {
                display: none;
                visibility: hidden
            }
        }

        @media (max-width: 768px) {
            .frequently-questions {
                background-color: #f4f8fc;
                padding: 60px 0;
            }
            .frequently-questions h2 {
                font-size: 21px;
                font-weight: 700;
                color: #000;
                margin: 0 0 40px;
            }
            .frequently-questions .btn {
                
                margin: 0 0 10px;
                font-size: 16px;
                font-weight: 700;
                color: #000;
                padding: 10.5px 52px;
                display: block;
                width: 100%;
            }
        }
        @media (min-width: 767px) {
            .frequently-questions {
                background-color: #f4f8fc;
                padding: 80px 0;
            }
            .frequently-questions h2 {
                font-size: 34px;
                font-weight: 700;
                color: #000;
                margin: 0 0 68px;
            }
            .frequently-questions .btn {
                
                margin: 0 0 10px;
                font-size: 16px;
                font-weight: 700;
                color: #000;
                padding: 10.5px 52px;
                display: block;
                width: 100%;
            }
        }
        @media (min-width: 992px) {
            .frequently-questions {
                background-color: #f4f8fc;
                padding: 80px 0;
            }
            .frequently-questions h2 {
                font-size: 34px;
                font-weight: 700;
                color: #000;
                margin: 0 0 68px;
            }
            .frequently-questions .btn {
                
                margin: 0 0 10px;
                font-size: 16px;
                font-weight: 700;
                color: #000;
                padding: 10.5px 52px;
                display: block;
                width: 100%;

            }
        }
        @media (min-width: 1200px) {
            .frequently-questions {
                background-color: #f4f8fc;
                padding: 80px 0;
            }
            .frequently-questions h2 {
                font-size: 34px;
                font-weight: 700;
                color: #000;
                margin: 0 0 68px;
            }
            .frequently-questions .btn {
                
                margin: 0 0 10px;
                font-size: 16px;
                font-weight: 700;
                color: #000;
                padding: 10.5px 52px;
                display: block;
                width: 100%;
            }

            .ext-start{
                display: block !important;
            }
        }
        /* APLICA APENAS PARA MOBILE   */
        @media (max-width: 575px) {
            .ml18{margin-left: -18%;}
            .w18{width: 85%;}
        }

        h1, h2, .h2, h3{font-weight: bolder;}
        h3{color:#00a555;}
    </style>
  </head>
  <body>
    <section class=" container-fluid px-0">
        <div class=" row">
            <div class=" col-12">
                <img id="imgTOPO" src="{{asset('/img/home_page/bolsas-de-estudo3.webp')}}"
                     class="econderMobile w-100">
                <img id="imgTOPO" src="{{asset('/img/home_page/bolsas-de-estudo-mobile2.webp')}}" 
                     class="econderDesktop w-100">
            </div>
        </div>
    </section>
    <section class=" container my-5">
        <div class=" row">
            <div class=" col-12 text-center">
                <h1 class="topicos mb-0 mx-auto">{!!$pagina['headline']!!}</h1>
                <h5 class=" text-center text-secondary mt-4 pb-0">Escolha seu curso para falar com o nosso consultor pelo WhatsApp</h5>
                <a href="#lista-cursos" class="btn btn-lg btn-primary text-white mt-4">Escolher meu curso agora!</a>
            </div>
        </div>
    </section>
    <section class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="h2 text-center"><strong>4 motivos para você fazer um curso profissionalizante</strong></h1>
            </div>
        </div>
        <div class="row flex-nowrap overflow-auto mt-2">
            <div class="col-12 col-sm-4 pl-4 py-2">
                <div class="row d-flex align-items-top w18 border rounded-2">
                    <div class="col-12 text-center">
                        <img src="{{asset('img/home_page/qualificacao.webp')}}" class="rounded-circle img-fluid w-50 mb-4">
                    </div>
                    <div class="col-11 text-center">
                        <h5 class=" text-danger">Cansado de perder oportunidades por <span style="font-weight: 900">falta de qualificação?</span></h5>
                        <p class=" text-center"><small>Destaque-se no mercado de trabalho com qualificação de excelência! Nossos cursos são a chave para abrir as portas de um emprego dos sonhos. Invista em você e aumente suas chances de uma carreira de sucesso.</small></p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4 ml18 py-2">
                <div class="row d-flex align-items-top w18 border rounded-2 w18">
                    <div class="col-12 text-center">
                        <img src="{{asset('img/home_page/semexperiencia.webp')}}" class="rounded-circle img-fluid w-50 mb-4">
                    </div>
                    <div class="col-12">
                        <h5 class=" text-danger">Não te contratam por que <span
                                    style="font-weight: 900">não tem experiência?</span></h5>
                        <p class=" justify"><small>Prepare-se para o mercado com os melhores professores! Nossos cursos proporcionam uma base teórica sólida, complementada por um carta de estágio. Assim, você terá a oportunidade de buscar experiência prática na sua área de atuação, aumentando sua empregabilidade.</small></p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4 ml18 py-2">
                <div class="row d-flex align-items-top w18 border rounded-2 w18">
                    <div class="col-12 text-center">
                        <img src="{{asset('img/home_page/primeiroemprego.webp')}}" class="rounded-circle img-fluid w-50 mb-4">
                    </div>
                    <div class="col-12">
                        <h5 class=" text-danger">Está buscando o seu <span style="font-weight: 900">primeiro emprego?</span>
                        </h5>
                        <p class=" justify"><small>Dê o primeiro passo na sua carreira com confiança! Nosso programa de capacitação é desenhado para garantir que você entre no mercado de trabalho pronto para impressionar desde o primeiro dia.
                            </small></p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4 ml18 py-2">
                <div class="row d-flex align-items-top w18 border rounded-2 w18">
                    <div class="col-12 text-center">
                        <img src="{{asset('/img/home_page/empregomelhor.webp')}}" class="rounded-circle img-fluid w-50 mb-4">
                    </div>
                    <div class="col-12">
                        <h5 class=" text-danger">Você quer um <span style="font-weight: 900">salário melhor?</span></h5>
                        <p class=" justify"><small>Impulsione seu potencial de ganhos! Com nossa formação, você se qualifica para posições mais elevadas e salários competitivos. Seja um profissional requisitado e valorizado no mercado.</small></p>
                    </div>
                </div>
            </div>
        </div>
        <div id="lista-cursos" class="row mt-3">
            <div class="col-12 col-lg-8 mx-auto">
                <div class="row">
                    @php
                        $redirecionar = true;    
                    @endphp
                    @foreach ($cursos as $curso)
                        
                        @if($curso->publicado AND $curso->mostrar_na_pagina)
                            @php
                                $redirecionar = false;    
                            @endphp
                            <div class="col-12 col-md-4 col-lg-4">
                                {!!$curso->tag_a!!}
                            </div>
                        @endif 
                    @endforeach
                    @php
                        if($redirecionar){header('Location: https://jovemempreendedor.org/');exit();}    
                    @endphp
                </div>
            </div>
        </div>
        <div class="row mt-3 py-4 bg-white">
            <div class="col-12 col-lg-8 mx-auto">
                <div class="row">
                    <div class="col-12">
                        <h2 class="text-info text-center">
                            Por que estudar no Portal Jovem Empreendedor?
                        </h2>
                    </div>
                    <div class=" col-sm-6 col-12 align-self-center">
                        <img src="{{asset('img/home_page/portal-jovem-empreendedor.webp')}}" class=" img-fluid">
                    </div>
                    <div class=" col-sm-6 col-12 align-self-center">
                        <p class=" lead text-secondary">Os nossos treinamentos irão ajudar você conseguir um emprego de forma
                            rápida mesmo que você não tenha <strong>nenhuma experiência.</strong></p>
                        <p class=" lead text-secondary">Somos <strong>a maior escola de cursos profissionalizantes do
                                Brasil.</strong></p>
                    </div>
                </div>
            </div>
            
        </div>
        <div id="beneficios" class="row py-5 bg-primary" >
            <div class="col-lg-8 mx-auto">
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <p class="h3 text-white"><strong>Benefícios de você entrar no Programa</strong></p>
                        <ul class="text-white">
                            <li>Aumente suas chances de contratação imediatamente</li>
                            <li>Aprenda técnicas modernas e eficazes que os empregadores procuram</li>
                            <li>Obtenha habilidades práticas que você pode aplicar desde o primeiro dia no emprego</li>
                            <li>Curso 100% online: Estude de onde estiver e quando puder</li>
                        </ul>
                    </div>
                    <div class="col-12 col-lg-6">
                        <p class="h3 text-white"><strong>Diferenciais</strong></p>
                        <ul class="text-white">
                            <li>Certificado reconhecido em todo o Brasil</li>
                            <li>Instrutores renomados com vasta experiência</li>
                            <li>O curso é tão rápido que você pode começar a se candidatar para as vagas já na próxima semana!</li>
                            <li>Acesso vitalício</li>
                        </ul>
                    </div>
                </div>
            </div>
            
        </div>
    
        <div id="certificado" class="row py-5 px-0" >
            <div class="col-12 col-sm-6 col-lg-8 mx-auto">
                <h2><strong>Conheça o curso que vai fazer você entrar no mercado de trabalho mais rápido, <span style="text-decoration: underline;">mesmo sem experiência.</span></strong></p>
                <p> Com vídeo aulas fáceis de assistir, você termina rápido, em menos de duas semanas, e recebe um <strong>certificado que vale em todo o Brasil.</strong></p>
            </div>
            <div class="row mx-auto px-0" style="background: linear-gradient(to bottom, #9DADBF, #97AABC);">
                <div class="text-center">
                    <img loading="lazy" class="lazyload-imagem img-fluid" alt="Portal Jovem Empreendedor" src="{{asset('img/home_page/certificadoNovo2.webp')}}">
                </div>
            </div>
            <div class="mt-4 col-12 col-sm-6 col-lg-4 mx-auto">
                    <p class="d-flex align-items-center"><i class=" ri-check-fill "></i>Certificado Reconhecido em Todo o Brasil</p>
                    <p class="d-flex align-items-center"><i class=" ri-check-fill "></i>Tem a Mesma validade de um Curso Presencial</p>
                    <p class="d-flex align-items-center"><i class=" ri-check-fill "></i>Assinatura digital</p>
                    <p class="d-flex align-items-center"><i class=" ri-check-fill "></i>Válido como Extensão universitária</p>
                    <p class="d-flex align-items-center"><i class=" ri-check-fill "></i>Concursos públicos (mediante verificação do edital)</p>
            </div>   
        </div>
    
        <div id="alunos" class="row px-0 py-5 bg-dark">
            <div class="col-12 col-sm-10 col-lg-8 mx-auto text-center" style="z-index: 2">
                <h2 class="mb-0 text-white w-75 mx-auto"><strong>São mais de 120 mil alunos no Brasil e em 14 países</strong></h1>
                <p class="w-lg-50 mt-0 text-white "> Veja o eles estão dizendo sobre nossos cursos?</p>
                <div class="row flex-nowrap overflow-auto px-2">
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
    
        <div id="bonus_padrao" class="row py-5">
            <div class="col-12 col-sm-10 col-lg-8 mx-auto">
                <h2 class="h2 mb-1 text-center">Ao se inscrever agora</h2>
                <p class="mb-0 text-center">Você poderá ganhar todos esses bônus abaixo gratuitamente.</p>
                <p style="font-size: small" class="text-center">Estes bônus não são vendidos separadamente.</p>
                
                <div class="row my-2 d-flex align-items-center">
                    <div class="col-3 col-sm-2 offset-sm-3 col-lg-1">
                        <img loading="lazy" alt='Portal Jovem Empreendedor'  class="lazy lazyload-imagem rounded-circle img-fluid" src="{{asset('img/home_page/cartaestagio.webp')}}">
                    </div>
                    <div class="col-9 col-sm-4 col-lg-6" >
                        <div class="row d-flex align-items-center justify-content-center" style="min-height: 100px;">
                            <p class="col-12 h6 feature-title"><strong>Carta de Estágio</strong></p>
                            <p class="col-12 feature-description">Uma ferramenta poderosa para abrir portas no competitivo mercado de trabalho, oferecendo um grande diferencial em seu currículo.</p>
                            <p class="col-12">DE <s>R$ 197,00</s> <span class="text-laranja">por R$ 0,00</span></p>
                        </div>
                    </div>
                </div>
                <div class="row d-flex align-items-center ">
                    <div class="col-3 col-sm-2 offset-sm-3 col-lg-1">
                        <img loading="lazy" alt='Portal Jovem Empreendedor'   class="lazy lazyload-imagem rounded-circle img-fluid" src="{{asset('img/home_page/jovemaprendiz.webp')}}">
                    </div>
                    <div class="col-9 col-sm-4 col-lg-6">
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
                <img loading="lazy" alt='Portal Jovem Empreendedor' class="lazyload-imagem img-fluid" src="{{asset('img/home_page/garantia-7-dias.png')}}" width="150" height="150">
                <p class="mt-3 text-center">Satisfação garantida ou seu dinheiro de volta! Você tem 7 dias para experimentar o curso sem riscos</p>
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
        <div class="modal fade" id="modal_lead" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content py-3">                
                    <div class="modal-body">
                        <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-hidden="true"></button>
                        <div class="row">
                            <div class="col-7 mx-auto text-center">
                                <img class="img-fluid" src="{{asset('img/home_page/logoPortal.webp')}}">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <form id="modal_form_lead" action="{{ route('lead_whatsapp') }}" method="POST">
                                @csrf
                                <p class="h3 mb-1" style="font-size: large"><strong>{!!$pagina['form_lead_titulo']!!}</strong></p>
                                <div class="mt-2">
                                    <label >Digite seu nome completo</label>
                                    <input id="input_lead_nome" type="text" class="form-control" name="nome" placeholder="Digite seu nome completo" required>
                                    <input id="input_lead_link" type="hidden" name="link">
                                    <input id="input_lead_curso_id" type="hidden" name="curso_id">
                                    <input id="input_lead_user_id" type="hidden" name="user_id">
                                    <input id="input_lead_origem" type="hidden" name="origem">
                                </div>
                                <div class="my-3">
                                    <label >Digite seu WhatsApp com DDD</label>
                                    <input minlength="13" id="input_lead_telefone" type="tel" name="telefone" class="form-control input_telefone" placeholder="Digite apenas números" title="Digite apenas números. Digite seu telefone com DDD" required> 
                                </div>
                                <div class="text-center">
                                    <button class="btn btn-lg btn-success btn-block" type="submit">{!!$pagina['form_lead_botao']!!}</button>
                                </div>
                                <p class="form-text text-muted text-center" style="line-height: 1.2">Ao clicar no botão, você será redirecionado para o nosso Conselheiro de Carreiras no WhatsApp.</p>
                            </form>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
        
    </section>
    <footer class="py-5 px-2 bg-dark">
        <div class="container-fluid">
            <div class="row   text-white">
                <div class="col-sm-3 offset-sm-3 mt-3">
                    <img loading="lazy" class="lazyload-imagem" alt='Portal Jovem Empreendedor' width="200" height="49" src="{{asset('img/home_page/logowhite.png')}}/" >
                    <p class="description">Nossa missão é especializar jovens, equipando-os com as habilidades e conhecimentos essenciais para se destacarem em seu primeiro emprego.</p>
                </div>
                <div class="col-sm-3 mt-3">
                    <p class="h5 widget-title">Informações para Contato</p>
                    <!--<p class="text-white d-flex align-items-center mb-0"><i class=" ri-building-2-fill me-2"></i>Sede Nacional: Rua Tristão Monteiro nº 1520, Centro, Taquara - RS.</p>-->
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
    <!--CURSOS-->
    <div id="btn-escolher-curso"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <!-- Inputmask Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>
    <script>
        //MOSTRAR BOTÃO DO WHATSAPP DEPOIS DE ALGUNS SEGUNDOS
    @if($pagina['whatsapp_mostrar'])
          setTimeout(function() {
        var botao_whatsapp = document.getElementById('whatsapp_botao');
        botao_whatsapp.style.visibility = 'inherit'; // Usa 'flex' para manter a formatação do flexbox
    }, {{$pagina['whatsapp_atendimento_tempo']}}000);

    @endif
    

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

    //PREENCHER INPUTS DO FORMULÁRIO LEAD
    var modal_lead = document.getElementById('modal_lead');
          
    modal_lead.addEventListener('show.bs.modal', function (event) {
          
          // Button that triggered the modal
          var button = event.relatedTarget;
          // Extract info from data-bs-* attributes
          var link = button.getAttribute('data-link');
          var curso_id = button.getAttribute('data-curso');
          var user_id = button.getAttribute('data-user');
          var origem = button.getAttribute('data-origem');

          // Update the modal's content.
          var input_lead_link = modal_lead.querySelector('#input_lead_link');
          var input_lead_curso_id = modal_lead.querySelector('#input_lead_curso_id');
          var input_lead_user_id = modal_lead.querySelector('#input_lead_user_id');
          var input_lead_origem = modal_lead.querySelector('#input_lead_origem');

          input_lead_link.value = link;
          input_lead_curso_id.value = curso_id;
          input_lead_user_id.value = user_id;
          input_lead_origem.value = origem;

    });

    //FORMULÁRIO LEAD / CHECKOUT; WHATSAPP
    $(document).ready(function() {
        $('#modal_form_lead').on('submit', function(event) {
            event.preventDefault();

            @if($pagina['pidel_id'])
            fbq('track', 'Lead');
            @endif

            var input_lead_nome = $('#input_lead_nome').val();
            var input_lead_link = $('#input_lead_link').val();
            var input_lead_curso_id = $('#input_lead_curso_id').val();
            var input_lead_user_id = $('#input_lead_user_id').val();
            var input_lead_origem = $('#input_lead_origem').val();
            var input_lead_telefone = $('#input_lead_telefone').val();
            var telefoneApenasNumeros = input_lead_telefone.replace(/\D/g, '');

            if (telefoneApenasNumeros.length < 10) {
                alert('O telefone deve conter no mínimo 10 dígitos.');
                return;
            }

            var ddd = telefoneApenasNumeros.substring(0, 2);
            var telefone = telefoneApenasNumeros.substring(2);

            input_lead_link = input_lead_link.replace("{nome}", input_lead_nome); //COLOCAR O NOME DA PESSOA NA MENSAGEM
            window.location.href = input_lead_link+"&name="+input_lead_nome+"&phoneac="+ddd+"&phonenumber="+telefone;
            
            $.ajax({
                url: $(this).attr('action'),
                method: $(this).attr('method'),
                data: {
                    _token: $('input[name="_token"]').val(),  // Certifique-se de incluir o CSRF token se necessário
                    nome: input_lead_nome,
                    curso_id: input_lead_curso_id,
                    user_id: input_lead_user_id,
                    origem: input_lead_origem,
                    telefone: telefoneApenasNumeros,
                    origem: input_lead_origem
                },
                success: function(response) {
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    var errors = xhr.responseJSON.errors;
                    console.log(errors);
                }
            });
        });
    });

    //FORMULÁRIO ONDE TEM O CAMPO TELEFONE
    document.addEventListener('DOMContentLoaded', function() {
        // Seleciona todos os campos de entrada com a classe 'telefone'
        var telefoneInputs = document.querySelectorAll('.input_telefone');
        
        // Aplica a máscara a cada campo de entrada
        telefoneInputs.forEach(function(input) {
            Inputmask({
                mask: ["(99) 9999-9999", "(99) 99999-9999"],
                keepStatic: true
            }).mask(input);
        });
    });
        
    $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            $('.lead').on('click', function(event) {
                event.preventDefault(); // Previne o comportamento padrão do link

                // Ativa o evento de rastreamento do Facebook Pixel
                @if($pagina['pidel_id'])
                fbq('track', 'Lead');
                @endif
                console.log('Evento Lead rastreado pelo Facebook Pixel.');

                // Pega o valor do atributo data-href
                const dataHref = $(this).data('href');

                window.location.href = dataHref;

                // Faz uma chamada AJAX
                $.ajax({
                    url: "{{route('meta_api_direto')}}", // Substitua pelo URL para onde deseja enviar a requisição
                    method: 'POST', // Método HTTP que deseja usar
                    data: {
                        // Dados que você deseja enviar para o servidor
                        lead_id: 'id' // Substitua por dados reais se necessário
                    },
                    success: function(response) {
                        console.log('Requisição AJAX bem-sucedida:', response);
                       
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro na requisição AJAX:', error);
                    }
                });
            });
    });    

    $('.view_content').on('click', function(event) {
                // Previna o comportamento padrão de redirecionamento
                event.preventDefault();

                // Capture o href do botão
                var href = $(this).attr('href');

                // Redirecione o usuário imediatamente
                window.location.href = href;

                // Envie a requisição AJAX
                $.ajax({
                    url: '/meta_api/ViewContent', // Substitua pela sua rota POST
                    type: 'POST',
                    data: {
                        // Dados a serem enviados no POST
                        evento: 'ViewContent' // Exemplo de dado
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Inclua o token CSRF se necessário
                    },
                    success: function(response) {
                        console.log('Resposta da API: ', response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro na requisição AJAX: ', error);
                    }
                });

                
    });        
        
        
    </script>
     @if($pagina['pidel_id'])
        <!-- Facebook Pixel Code -->
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{(int)$pagina['pidel_id']}}'); // Substitua YOUR_PIXEL_ID pelo seu ID do Pixel
            fbq('track', 'PageView'); // Evento padrão de visualização de página
            //fbq('track', 'Lead'); // Evento específico de Lead
        </script>
        <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id={{(int)$pagina['pidel_id']}}&ev=PageView&noscript=1"
        /></noscript>
        <!-- End Facebook Pixel Code -->
     @endif
  </body>
</html>
