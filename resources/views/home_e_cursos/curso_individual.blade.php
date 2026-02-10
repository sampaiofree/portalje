@extends('home_e_cursos.html_base')

@section('content')
<div class="row py-5 bg-dark px-2">
    <div class="col-12 col-sm-6 offset-sm-3 text-white px-2">
        <p class="mb-0"><strong>Sobre o curso</strong></p>
        <p class="h2 mt-1"><strong>{{$curso->titulo}}</strong></p>
    </div>
    <div class="col-12">
        <div class="row flex-nowrap overflow-auto p-2 px-sm-5">
            <div class="col-11 col-sm-4 col-lg-2 offset-lg-3 px-1">
                <div class="card border h-100">
                    <div class="card-body pt-2 ">
                        <p class="h4">Dados do curso</p>
                        <p class="d-flex align-items-center">
                            <i class="me-2 ri-time-line"></i>
                            Carga horária de até {{$curso->horas_completo}} horas
                        </p>
                        <p class="d-flex align-items-center">
                            <i class="me-2 ri-group-line "></i>
                            {{$curso->numero_alunos}} alunos
                        </p>
                        <p class="d-flex align-items-center">
                            <i class="me-2  ri-star-s-fill  "></i>
                            {{$curso->nota_avaliacao}}/5
                        </p>
                        <p class="d-flex align-items-center">
                            <i class="me-2  ri-money-dollar-circle-fill "></i>
                            Salário de até {{ $curso->salario_maximo}}
                        </p>
                        <p class="d-flex align-items-center mb-0">
                            <i class="me-2   ri-briefcase-line  "></i>
                            Áreas que você pode atuar
                        </p>
                        <ul>
                        @forEach($curso->areas_de_atuacao as $areas)
                            <li>{{$areas}}</li>
                        @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-11 col-sm-4 col-lg-2 px-1 ">
                <div class="card border h-100">
                    <div class="card-body pt-2 ">
                        <p class="h3">O que você vai aprender?</p>
                        <div id="o_que_vc_aprender_curta">
                            {!! Str::limit($curso->descricao_curta, 400) !!}
                            <a href="javascript:void(0)" onclick="mostrar_mais('o_que_vc_aprender')">Saiba mais</a>
                        </div>
                        <div id="o_que_vc_aprender_completa" class="content-hidden">
                            {!! $curso->descricao_curta !!}
                            <a href="javascript:void(0)" onclick="mostrar_menos('o_que_vc_aprender')">Mostrar menos</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-11 col-sm-4 col-lg-2 px-1 ">
                <div class="card border h-100">
                    <div class="card-body pt-2 ">
                        <div class="row">
                            <div class="col-4">
                                <img alt='Portal Jovem Empreendedor' loading='lazy'  class='lazy img-fluid'  src='{{asset('/storage/'.$curso->professor_foto)}}' width='67' height='67'>
                            </div>
                            <div class="col-8">
                                <h4><small>Conheça nosso professor</small> {{$curso->professor_nome}}</h3>
                            </div>
                        </div>
                        @if($curso->professor_biografia)
                            <div id="professor_bio_curta">
                                {!! Str::limit($curso->professor_biografia, 400) !!}
                                <a href="javascript:void(0)" onclick="mostrar_mais('professor_bio')">Saiba mais</a>
                            </div>
                            <div id="professor_bio_completa" class="content-hidden">
                                {!! $curso->professor_biografia !!}
                                <a href="javascript:void(0)" onclick="mostrar_menos('professor_bio')">Mostrar menos</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
<div class="row my-5">
    <div class="col-sm-6 offset-sm-3">
        <div class="row  ">
            <div class="col-12">
                <h2 class="h2 text-center mb-1">Conheça o conteúdo do curso</h2>
                <p class="text-center">Aperte em cada item para conhecer as aulas de cada módulo.</p>
            </div>
        </div>
        <div class="row">
            <div class="accordion accordion-flush" id="accordionConteudo">
                @php $index = 0; @endphp
                @forEach($curso->conteudo_principal as $conteudo)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="flush-conteudo-{{$index}}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#flush-collapse-conteudo-{{$index}}" aria-expanded="false" aria-controls="flush-collapse-conteudo-{{$index}}">
                            {{$conteudo['title']}}
                        </button>
                    </h2>
                    <div id="flush-collapse-conteudo-{{$index}}" class="accordion-collapse collapse" aria-labelledby="flush-conteudo-{{$index}}"
                        data-bs-parent="#accordionConteudo">
                        <div class="accordion-body">
                            <ul class="list-group list-group-flush">
                                @forEach($conteudo['topics'] as $lista)
                                <li class="list-group-item"  style="font-size: small;">{{$lista}}</li>
                                @endforeach
                            </ul>
                            
                        </div>
                    </div>
                </div>
                @php $index++; @endphp
                @endforeach
            </div>
        </div>
    </div>
</div>

@if($curso->conteudo_bonus)
        <div class="row my-5">
            <div class="col-sm-6 offset-sm-3">
                <div class="row">
                    <div class="col-12 text-center">
                            <h2 class="h2 mb-1">Fazendo sua inscrição ainda <strong>hoje</strong> receba também de bônus!</h2>
                            <p>Bônus válidos somente no plano completo!</p>
                    </div>
                </div>
                <div class="row">
                    <div class="accordion accordion-flush" id="accordionbonus">
                        @php $index = 0; @endphp
                        @forEach($curso->conteudo_bonus as $conteudo)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-bonus-{{$index}}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapse-bonus-{{$index}}" aria-expanded="false" aria-controls="flush-collapse-bonus-{{$index}}">
                                    {{$conteudo['title']}}
                                </button>
                            </h2>
                            <div id="flush-collapse-bonus-{{$index}}" class="accordion-collapse collapse" aria-labelledby="flush-bonus-{{$index}}"
                                data-bs-parent="#accordionbonus">
                                <div class="accordion-body">
                                    <ul class="list-group list-group-flush">
                                        @forEach($conteudo['topics'] as $lista)
                                        <li class="list-group-item" style="font-size: small;">{{$lista}}</li>
                                        @endforeach
                                    </ul>
                                    
                                </div>
                            </div>
                        </div>
                        @php $index++; @endphp
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    @endif

@if(!$curso->botao_flutuante_whatsapp)    
<div id="planos" class="row py-5">
    <div class="col-12 ">
        <h1 class="h2 mb-0 text-center"><strong>Inscreva-se agora!</strong></h1>
        <p class="mt-0 text-center">Dê o primeiro passo para uma carreira de sucesso!</p>

        <div class="row flex-nowrap overflow-auto p-2">
            <!--PLANO COMPLETO-->
            @if(!empty($curso->preco_parcelado_completo))
            <div  class="col-11 col-sm-5 col-lg-3 offset-lg-3 offset-sm-1 px-1 @if(empty($curso->preco_parcelado_basico)) mx-auto @endif">
                <div class="card border-success border-2 h-100">
                    <div class="card-body p-4">
                        <span class="badge rounded-pill bg-success position-absolute start-50 translate-middle text-uppercase" style="top: 0;">MAIS RECOMENDADO</span>
                        <div class="text-center">
                            
                            <p class="h6 text-uppercase text-muted card-subtitle">Plano Completo</p>
                            <p class="my-0 text-danger" style="font-size: small;"><strong>De <span style="text-decoration: line-through;">R${{$curso->preco_cheio_sem_desconto_completo}}</span> por </strong></p>
                            <p class=' my-0 fw-bold card-title'>{{ $curso->preco_parcelado_completo }}</p>
                            @if($curso->preco_cheio_completo )<p class='mt-0 mb-0' style='font-size: medium;'>ou {{ $curso->preco_cheio_completo }} à vista</p>@endif
                            
                        </div>
                        <div class="mt-3">
                            <ul class="list-unstyled">
                                <li class="d-flex mb-2">
                                    <i class="ri-checkbox-circle-fill me-1 text-success"></i>
                                    <span>Curso {{$curso->titulo}} em vídeo aulas - todos os módulos</span>
                                </li>
                                <li class="d-flex mb-2">
                                    <i class="ri-checkbox-circle-fill me-1 text-success"></i>
                                    <span>Certificado de {{$curso->horas_completo}} horas incluso</span>
                                </li>
                                <li class="d-flex mb-2">
                                    <i class="ri-checkbox-circle-fill me-1 text-success"></i>
                                    <span>Acesso vitálício</span>
                                </li>
    
                                @if($curso->conteudo_bonus)
                                    @forEach($curso->conteudo_bonus as $conteudo)
                                            <li class="d-flex mb-2">
                                                <i class="ri-checkbox-circle-fill me-1 text-success"></i>
                                                <span>{{$conteudo['title']}}</span>
                                            </li>
                                    @endforeach
                                @endif
    
                                <li class="d-flex mb-2">
                                    <i class="ri-checkbox-circle-fill me-1 text-success"></i>
                                    <span>+ Carta de Estágio</span>
                                </li><li class="d-flex mb-2">
                                    <i class="ri-checkbox-circle-fill me-1 text-success"></i>
                                    <span>+ Preparatório para Jovem Aprendiz</span>
                                </li>
    
                            </ul>
                            {!!$curso->botao_checkout_completo!!}
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <!--PLANO BÁSICO-->
            @if(!empty($curso->preco_parcelado_basico))
                <div id="cardPlanoBasico" class="col-11 col-sm-5 col-lg-3 px-1" >
                    <div class="card border border-info h-100" >
                        <div class="card-body p-4">
                            <div class="text-center">
                                <p class="h6 text-uppercase text-muted card-subtitle">Plano Básico</p>
                                <p class="my-0 text-danger" style="font-size: small;"><strong>De <span style="text-decoration: line-through;">R${{$curso->preco_cheio_sem_desconto_basico}}</span> por </strong></p>
                                <p class=' my-0  fw-bold card-title'>{{ $curso->preco_parcelado_basico }}</p>
                                @if($curso->preco_cheio_completo )<p class='mt-0 mb-0' style='font-size: medium;'>ou {{ $curso->preco_cheio_basico }} à vista</p>@endif
                                
                            </div>
                            <div class="mt-3">
                                <ul class="list-unstyled">
                                    <li class="d-flex mb-2">
                                        <i class="ri-checkbox-circle-fill me-1 text-success"></i>
                                        <span>Curso {{ $curso->titulo }} em vídeo aulas - módulo básico</span>
                                    </li>
                                    <li class="d-flex mb-2">
                                        <i class="ri-checkbox-circle-fill me-1 text-success"></i>
                                        <span>Certificado de {{ $curso->horas_basico }} horas incluso</span>
                                    </li>
                                    <li class="d-flex mb-2">
                                        <i class="ri-checkbox-circle-fill me-1 text-success"></i>
                                        <span>Acesso vitálício</span>
                                    </li>
                                    @if($curso->conteudo_bonus)
                                    @forEach($curso->conteudo_bonus as $conteudo)
                                            <li class="d-flex mb-2">
                                                <i class="ri-close-circle-fill me-1"></i>
                                                <span>{{$conteudo['title']}}</span>
                                            </li>
                                    @endforeach
                                @endif
                                            <li class="d-flex mb-2">
                                                <i class=" ri-close-circle-fill me-1 "></i>
                                                <span>Carta de Estágio não incluso</span>
                                            </li>
                                            <li class="d-flex mb-2">
                                                <i class=" ri-close-circle-fill me-1 "></i>
                                                <span>Preparatório para Jovem Aprendiz não incluso</span>
                                            </li>
                                </ul>
                                {!!$curso->botao_checkout_basico!!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>
    
</div>
@else
{!!$curso->botao_flutuante_whatsapp!!}
@endif

@if(!empty($curso->video_dentro_do_curso) OR !empty($curso->video_apresentacao))

    <div class="row py-5 px-0 bg-dark">
        <div class="col-12 col-lg-8 offset-lg-2">
            <div class="text-center" style="z-index: 2">
                <h1 class="h2 mb-0 text-white ">Veja como é o curso por dentro</h1>
                <p class="w-lg-50 mt-0 text-white "> Assista o vídeo abaixo para saber como funciona o nosso treinamento.</p>
            </div>
            <div class="row flex-nowrap overflow-auto p-2">
                @if(!empty($curso->video_dentro_do_curso))
                    <div class="col-11 col-sm-6 bg-black mx-1">
                        <div class="ratio ratio-16x9">
                            <iframe class="lazyload-iframe" width="100%" data-src="https://www.youtube.com/embed/<?=$curso->video_dentro_do_curso;?>" title="YouTube video" allowfullscreen></iframe>
                        </div>
                    </div>
                @endif
                @if(!empty($curso->video_apresentacao))
                    <div class="col-11 col-sm-6 bg-black mx-1">
                        <div class="ratio ratio-16x9">
                            <iframe class="lazyload-iframe" width="100%" data-src="https://www.youtube.com/embed/<?=$curso->video_apresentacao;?>" title="YouTube video" allowfullscreen></iframe>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
    </section>
 @endif

@endsection

@section('head')
    <style>
        #carousel_home img{width: 100%;}
        .content-hidden {display: none;}
        #planos .card-title{font-size: xx-large; color: black;}
        .ri-close-circle-fill {font-size: large; color: red;}
        .ri-checkbox-circle-fill {font-size: large;}
        .jump {
            animation: jump 1s infinite;            
        }
        @keyframes jump {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
    </style>
@endsection

@section('scripts')
<script>
        

        function mostrar_mais(id){
            $('#'+id+"_curta").hide();
            $('#'+id+"_completa").show();
        }

        function mostrar_menos(id){
            $('#'+id+"_completa").hide();
            $('#'+id+"_curta").show();
        }
        
</script>
@endsection