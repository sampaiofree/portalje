@extends('home_e_cursos.html_base')

@section('titulo_pagina', trim($combo->titulo) !== '' ? $combo->titulo . ' | Portal Jovem Empreendedor' : 'Combo de cursos | Portal Jovem Empreendedor')
@section('descricao_pagina', trim(strip_tags($combo->descricao_curta ?: $combo->headline ?: 'Conheca os cursos incluidos no combo e comece sua qualificacao hoje.')))

@section('content')
@php
    $descricaoCombo = trim((string) ($combo->descricao_curta ?: $combo->headline));
    $descricaoComboCurta = \Illuminate\Support\Str::limit(strip_tags($descricaoCombo), 280);
    $descricaoComboLonga = trim((string) $combo->descricao_curta);
    $mostrarDescricaoExpandida = mb_strlen(strip_tags($descricaoCombo)) > 280 && $descricaoComboLonga !== '';
    $labelCursos = $comboResumo['course_count'] === 1 ? 'curso incluido' : 'cursos incluidos';
    $labelCertificados = $comboResumo['course_count'] === 1 ? 'certificado de conclusao' : 'certificados de conclusao';
@endphp

<div class="row py-5 bg-dark px-2">
    <div class="col-12 col-sm-6 offset-sm-3 text-white px-2">
        <p class="mb-0"><strong>Sobre o combo</strong></p>
        <p class="h2 mt-1"><strong>{{ $combo->titulo }}</strong></p>
    </div>
    <div class="col-12">
        <div class="row flex-nowrap overflow-auto p-2 px-sm-5">
            <div class="col-11 col-sm-4 col-lg-2 offset-lg-3 px-1">
                <div class="card border h-100">
                    <div class="card-body pt-2">
                        <p class="h4">Dados do combo</p>
                        <p class="d-flex align-items-center">
                            <i class="me-2 ri-book-open-line"></i>
                            {{ $comboResumo['course_count'] }} {{ $labelCursos }}
                        </p>
                        @if($comboResumo['has_total_hours'])
                            <p class="d-flex align-items-center">
                                <i class="me-2 ri-time-line"></i>
                                Carga horaria total de ate {{ $comboResumo['total_hours'] }} horas
                            </p>
                        @endif
                        <p class="d-flex align-items-center">
                            <i class="me-2 ri-award-line"></i>
                            {{ $comboResumo['course_count'] }} {{ $labelCertificados }}
                        </p>
                        <p class="d-flex align-items-center mb-0">
                            <i class="me-2 ri-stack-line"></i>
                            Acesso completo a todo o combo
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-11 col-sm-4 col-lg-2 px-1">
                <div class="card border h-100">
                    <div class="card-body pt-2">
                        <p class="h4">O que voce vai aprender?</p>
                        @if($descricaoCombo !== '')
                            <div id="combo_descricao_curta">
                                <p class="mb-2">{{ $descricaoComboCurta }}</p>
                                @if($mostrarDescricaoExpandida)
                                    <a href="javascript:void(0)" onclick="mostrarMais('combo_descricao')">Quero minha vaga</a>
                                @endif
                            </div>
                            @if($mostrarDescricaoExpandida)
                                <div id="combo_descricao_completa" class="content-hidden">
                                    <div>{!! $descricaoComboLonga !!}</div>
                                    <a href="javascript:void(0)" onclick="mostrarMenos('combo_descricao')">Mostrar menos</a>
                                </div>
                            @endif
                        @else
                            <p class="mb-0">Conheca os cursos incluidos neste combo e escolha a melhor trilha para acelerar sua qualificacao.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-11 col-sm-4 col-lg-2 px-1">
                <div class="card border h-100">
                    <div class="card-body pt-2">
                        <p class="h4">Areas de atuacao</p>
                        @if(!empty($comboResumo['unique_areas']))
                            <ul class="mb-0 ps-3">
                                @foreach($comboResumo['unique_areas'] as $area)
                                    <li>{{ $area }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mb-0">Este combo reune cursos com aplicacao pratica em diferentes frentes do mercado de trabalho.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row my-5">
    <div class="col-sm-8 offset-sm-2">
        <div class="row">
            <div class="col-12">
                <h2 class="h2 text-center mb-1">Conheca o conteudo do combo</h2>
                <p class="text-center">Abra cada curso para ver os modulos e topicos incluidos.</p>
            </div>
        </div>
        <div class="row">
            @if($combo->cursos->isEmpty())
                <div class="col-12">
                    <div class="alert alert-light border text-center mb-0">
                        Nenhum curso foi vinculado a este combo ainda.
                    </div>
                </div>
            @else
                <div class="accordion accordion-flush lp-combo-accordion" id="accordionComboCursos">
                    @foreach($combo->cursos as $curso)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-curso-{{ $curso->id }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse-curso-{{ $curso->id }}" aria-expanded="false"
                                    aria-controls="collapse-curso-{{ $curso->id }}">
                                    <span class="d-flex flex-column flex-sm-row justify-content-between w-100 me-3 gap-1">
                                        <span class="fw-bold">{{ $curso->titulo }}</span>
                                        @if(!empty($curso->horas_completo))
                                            <span class="small text-muted">{{ $curso->horas_completo }} horas</span>
                                        @endif
                                    </span>
                                </button>
                            </h2>
                            <div id="collapse-curso-{{ $curso->id }}" class="accordion-collapse collapse"
                                aria-labelledby="heading-curso-{{ $curso->id }}" data-bs-parent="#accordionComboCursos">
                                <div class="accordion-body">
                                    @if(!empty($curso->headline))
                                        <p class="mb-3">{{ str_replace('"', '', $curso->headline) }}</p>
                                    @endif

                                    @if(!empty($curso->conteudo))
                                        <div class="row g-3">
                                            @foreach($curso->conteudo as $modulo)
                                                <div class="col-12">
                                                    <div class="border rounded-3 p-3 h-100">
                                                        <p class="fw-bold mb-2">{{ $modulo['title'] }}</p>
                                                        @if(!empty($modulo['topics']))
                                                            <ul class="mb-0 ps-3">
                                                                @foreach($modulo['topics'] as $topico)
                                                                    <li>{{ $topico }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <p class="text-muted mb-0">Conteudo detalhado nao informado.</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">Conteudo detalhado nao informado.</p>
                                    @endif

                                    @if(!empty($curso->areas_de_atuacao_lista))
                                        <div class="mt-3">
                                            <p class="fw-bold mb-2">Areas em que voce pode aplicar este curso</p>
                                            <ul class="mb-0 ps-3">
                                                @foreach($curso->areas_de_atuacao_lista as $area)
                                                    <li>{{ $area }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<div id="planos" class="row py-5">
    <div class="col-12">
        <h1 class="h2 mb-0 text-center"><strong>Inscreva-se agora!</strong></h1>
        <p class="mt-0 text-center">Tenha acesso ao combo completo com uma unica inscricao.</p>

        <div class="row">
            <div class="col-11 col-sm-8 col-lg-4 mx-auto px-1">
                <div class="card border-success border-2 h-100">
                    <div class="card-body p-4">
                        <span class="badge rounded-pill bg-success position-absolute start-50 translate-middle text-uppercase" style="top: 0;">Combo completo</span>
                        <div class="text-center">
                            <p class="h6 text-uppercase text-muted card-subtitle">Acesso total</p>
                            @if(!empty($combo->preco_parcelado))
                                <p class="my-0 fw-bold card-title">{{ $combo->preco_parcelado }}</p>
                            @endif
                            @if(!empty($combo->preco))
                                <p class="mt-0 mb-0" style="font-size: medium;">ou {{ $combo->preco }} a vista</p>
                            @endif
                        </div>
                        <div class="mt-3">
                            <ul class="list-unstyled">
                                <li class="d-flex mb-2">
                                    <i class="ri-checkbox-circle-fill me-1 text-success"></i>
                                    <span>Acesso completo aos {{ $comboResumo['course_count'] }} {{ $comboResumo['course_count'] === 1 ? 'curso do combo' : 'cursos do combo' }}</span>
                                </li>
                                <li class="d-flex mb-2">
                                    <i class="ri-checkbox-circle-fill me-1 text-success"></i>
                                    <span>{{ $comboResumo['course_count'] }} {{ $labelCertificados }}</span>
                                </li>
                                @if($comboResumo['has_total_hours'])
                                    <li class="d-flex mb-2">
                                        <i class="ri-checkbox-circle-fill me-1 text-success"></i>
                                        <span>Carga horaria total de ate {{ $comboResumo['total_hours'] }} horas</span>
                                    </li>
                                @endif
                                @foreach($comboResumo['course_names'] as $courseName)
                                    <li class="d-flex mb-2">
                                        <i class="ri-checkbox-circle-fill me-1 text-success"></i>
                                        <span>{{ $courseName }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <a class="btn view_content btn-success d-block w-100" href="{{ $comboCheckoutUrl }}">Garantir meu acesso</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('head')
    <style>
        .content-hidden { display: none; }
        #planos .card-title { font-size: xx-large; color: black; }
        .lp-combo-accordion .accordion-button { font-weight: 600; }
        .lp-combo-accordion .accordion-body { background: #fff; }
        .ri-checkbox-circle-fill { font-size: large; }
    </style>
@endsection

@section('scripts')
    <script>
        function mostrarMais(id) {
            $('#' + id + '_curta').hide();
            $('#' + id + '_completa').show();
        }

        function mostrarMenos(id) {
            $('#' + id + '_completa').hide();
            $('#' + id + '_curta').show();
        }
    </script>
@endsection
