@extends('home_page.home_base')

@section('content')
<div class="row py-5 bg-dark px-2">
    <div class="col-12 col-sm-6 offset-sm-3 text-white px-2">
        <p class="mb-0"><strong>Sobre o curso</strong></p>
        <p class="h2 mt-1"><strong>{{$curso->titulo}}</strong></p>
    </div>
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
                            <img alt='Portal Jovem Empreendedor' loading='lazy'  class='lazy img-fluid'  src='{{$curso->professor_foto}}' width='67' height='67'>
                        </div>
                        <div class="col-8">
                            <h4><small>Conheça nosso professor</small> {{$curso->professor_nome}}</h3>
                        </div>
                    </div>
                    <div id="professor_bio_curta">
                        {!! Str::limit($curso->professor_biografia, 400) !!}
                        <a href="javascript:void(0)" onclick="mostrar_mais('professor_bio')">Saiba mais</a>
                    </div>
                    <div id="professor_bio_completa" class="content-hidden">
                        {!! $curso->professor_biografia !!}
                        <a href="javascript:void(0)" onclick="mostrar_menos('professor_bio')">Mostrar menos</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row ">
    <div class="col-12 py-5">
        <h1 class="h2 mb-0 text-center"><strong>Inscreva-se agora!</strong></h1>
        <p class="mt-0 text-center">Dê o primeiro passo para uma carreira de sucesso!</p>

        <div id="planos" class="row flex-nowrap overflow-auto p-2">
            <!--PLANO COMPLETO-->
            <div  class="col-11 col-sm-5 col-lg-3 offset-lg-3 offset-sm-1 px-1">
                <div class="card border-success border-2 h-100">
                    <div class="card-body text-center p-4">
                        <span class="badge rounded-pill bg-success position-absolute start-50 translate-middle text-uppercase">MAIS RECOMENDADO</span>
                        <p class="h6 text-uppercase text-muted card-subtitle">Plano Completo</p>
                        <p class="my-0 text-danger" style="font-size: small;"><strong>De <span style="text-decoration: line-through;">R${{$pagina['preco_cheio_sem_desconto_completo']}}</span> por </strong></p>
                        <p class=' my-0 fw-bold card-title'>{{ $curso->preco_parcelado_completo }}</p><p class='mt-0 mb-0' style='font-size: medium;'>ou {{ $curso->preco_cheio_completo }} à vista</p>
                        
                    </div>
                    <div class="card-body p-4">
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

                            @if($pagina['bonus_lista'])
                                @forEach($pagina['bonus_lista'] as $bonus_lista)
                                        <li class="d-flex mb-2">
                                            <i class="ri-checkbox-circle-fill me-1 text-success"></i>
                                            <span>{{$bonus_lista}}</span>
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
                        <a class="btn btn-success d-block w-100" role="button" href="#" data-bs-toggle="modal" data-bs-target="#modal-check-plano-completo">Garantir Minha Vaga</a>
                    </div>
                </div>
            </div>
            <!--PLANO BÁSICO-->
            <div id="cardPlanoBasico" class="col-11 col-sm-5 col-lg-3 px-1" style="{{$pagina['plano_basico_hidden']}}">
                <div class="card border border-info h-100" >
                    <div class="card-body text-center p-4">
                        <p class="h6 text-uppercase text-muted card-subtitle">Plano Básico</p>
                        <p class="my-0 text-danger" style="font-size: small;"><strong>De <span style="text-decoration: line-through;">R${{$pagina['preco_cheio_sem_desconto_basico']}}</span> por </strong></p>
                        <p class=' my-0  fw-bold card-title'>{{ $curso->preco_parcelado_basico }}</p><p class='mt-0 mb-0' style='font-size: medium;'>ou {{ $curso->preco_cheio_basico }} à vista</p>
                        
                    </div>
                    <div class="card-body p-4">
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
                            @if($pagina['bonus_lista'])
                                @forEach($pagina['bonus_lista'] as $bonus_lista)
                                        <li class="d-flex mb-2">
                                            <i class=" ri-close-circle-fill me-1 "></i>
                                            <span>{{$bonus_lista}} não incluso </span>
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
                        <a class="btn btn-info d-block w-100" role="button" href="#" data-bs-toggle="modal" data-bs-target="#modal-check-plano-basico">Garantir Minha Vaga</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    
</div>

@endsection

@section('head')
    <style>
        #carousel_home img{width: 100%;}
        .content-hidden {display: none;}
        #planos .card-title{font-size: xx-large; color: black;}
        .ri-close-circle-fill {font-size: large; color: red;}
        .ri-checkbox-circle-fill {font-size: large;}
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