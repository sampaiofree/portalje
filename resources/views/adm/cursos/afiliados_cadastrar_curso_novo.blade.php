@extends('adm.html_base')

@section('titulo_pagina', 'Meus Cursos e Links')

@section('head')
    <style>
        .info-card { background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: none; }
        .curso-card {
            display: flex;
            flex-direction: column;
            height: 100%;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e9ecef;
            transition: all 0.2s ease;
        }
        .curso-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }
        .curso-card .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .curso-card .card-title {
            font-weight: 600;
        }
        .curso-card .card-footer {
            background-color: #f8f9fa;
        }
        .nav-pills .nav-link {
            border-radius: 0.5rem;
        }
        .nav-pills .nav-link.active {
            background-color: #007bff; /* Cor primária */
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <!--<div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Meus Cursos e Links de Divulgação</h4>
            </div>
        </div>
    </div>-->
    
    <!-- Filtro de Cursos -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="info-card">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <p class="mb-0 text-muted">Use o campo abaixo para encontrar um curso específico.</p>
                    <p>Está Perdido e Confuso? Clique <a href="https://www.youtube.com/watch?v=G3_UjvwizSc" target="_blank">AQUI</a> para saber como usar esta página.</p>
                    <div class="mt-2 mt-md-0" style="width: 300px;">
                        <input class="form-control" type="text" id="campoPesquisa" placeholder="Digite o nome do curso...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grade de Cursos -->
    <div class="row">
        @foreach($cursos as $curso)
            @if($curso->publicado AND $curso->permitir_afiliacao)
                <div class="lista_cursos col-md-6 col-lg-4 mb-4">
                    <div class="curso-card p-2 bg-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{$curso->titulo}}</h5>
                                <span class="badge @if($curso->codigo_ref) bg-success text-success-darken @else bg-warning text-warning-darken @endif">
                                    @if($curso->codigo_ref) Configurado @else Pendente @endif
                                </span>
                            </div>
                            <p class="text-primary fw-bold mb-2">Preço: R$ {{ $curso->preco_cheio_completo }}</p>
                            <p class="card-text text-muted small flex-grow-1">{{$curso->headline}}</p>
                            
                            {{-- Botão principal que abre o modal de configuração --}}
                            <button class="btn @if($curso->codigo_ref) btn-secondary @else btn-primary @endif w-100" data-bs-toggle="modal" data-bs-target="#modalCurso-{{$curso->id}}">
                                <i class="ri-settings-3-line me-1"></i>
                                @if($curso->codigo_ref) Gerenciar Links e Configurações @else Configurar Curso Agora @endif
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
 <!-- Modais dos Cursos -->
    @foreach($cursos as $curso)
        @if($curso->publicado AND $curso->permitir_afiliacao)
            <div class="modal fade" id="modalCurso-{{$curso->id}}" tabindex="-1" aria-labelledby="modalLabel-{{$curso->id}}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel-{{$curso->id}}">{{ $curso->titulo }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Abas de Navegação -->
                            <ul class="nav nav-pills bg-light rounded p-2 mb-3" id="pills-tab-{{$curso->id}}" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="pills-config-tab-{{$curso->id}}" data-bs-toggle="pill" data-bs-target="#pills-config-{{$curso->id}}" type="button" role="tab">1. Configuração</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link @if(!$curso->codigo_ref) disabled @endif" id="pills-links-tab-{{$curso->id}}" data-bs-toggle="pill" data-bs-target="#pills-links-{{$curso->id}}" type="button" role="tab">2. Links de Divulgação</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-materiais-tab-{{$curso->id}}" data-bs-toggle="pill" data-bs-target="#pills-materiais-{{$curso->id}}" type="button" role="tab">3. Materiais</button>
                                </li>
                            </ul>

                            <!-- Conteúdo das Abas -->
                            <div class="tab-content" id="pills-tabContent-{{$curso->id}}">
                                
                                <!-- Aba 1: Configuração -->
                                <div class="tab-pane fade show active" id="pills-config-{{$curso->id}}" role="tabpanel">
                                    <h4>Passo 1: Afilie-se ao produto e Cadastre seu Código REF</h4>
                                    <p class="text-muted">
                                        Seu código REF da Hotmart é essencial para rastrear suas vendas e garantir suas comissões. Sem ele, você não recebe pelas vendas.
                                    </p>
                                    <p class="text-muted">
                                        Clique <a href="https://www.youtube.com/watch?v=G3_UjvwizSc" target="_blank">AQUI</a> e assista o vídeo explicando como conseguir o código e se afiliar.
                                    </p>
                                    <a href="{{$curso->link_afiliacao}}" target="_blank" class="mb-5 btn btn-sm btn-primary">
                                        ➡️ Afilie-se Agora
                                    </a>

                                    <form id='form_{{$curso->id}}' method="POST" action="{{route('cadastrar_codigo_ref')}}">
                                        @csrf
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Código REF</span>
                                            <input type="text" class="form-control" name="codigo_ref" placeholder="Cole seu código aqui" value="{{$curso->codigo_ref}}" required pattern="[^./:]*" title="Não use '.', '/' ou ':'">
                                            <button type="submit" class="btn @if($curso->codigo_ref) btn-secondary @else btn-primary @endif">@if($curso->codigo_ref) Atualizar @else Salvar Código @endif</button>
                                        </div>
                                        <input type="hidden" name="curso_id" value="{{$curso->id}}">
                                        <input type="hidden" name="titulo" value="{{$curso->titulo}}">
                                                    <input type="hidden" name="id" value="{{$curso->codigo_ref_id}}">
                                                    <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
                                    </form>
                                    <a href="https://www.youtube.com/watch?v=G3_UjvwizSc" target="_blank">Não sabe onde encontrar o código REF? Clique aqui.</a>
                                    
                                    <hr>
                                    <h4>Mostrar este curso no seu site?</h4>
                                    <p class="text-muted">Selecione "Sim" para que este curso apareça na lista de produtos do seu site de afiliado.</p>
                                    <form id="form_mostrar_curso_{{$curso->id}}" method="POST" action="{{route('cadastrar_codigo_ref')}}">
                                        @csrf
                                        {{-- Replicando os mesmos hiddens para este formulário --}}
                                        <input type="hidden" name="codigo_ref" value="{{$curso->codigo_ref}}">
                                        <input type="hidden" name="curso_id" value="{{$curso->id}}">
                                        <input type="hidden" name="id" value="{{$curso->codigo_ref_id}}">
                                        <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
                                        <div class="form-check form-switch fs-4">
                                            <input class="form-check-input" type="checkbox" role="switch" id="input_mostrar_curso_{{$curso->id}}" name="mostrar_curso" @if($curso->mostrar_curso) checked @endif onchange="this.form.submit()">
                                            <label class="form-check-label" for="input_mostrar_curso_{{$curso->id}}">@if($curso->mostrar_curso) Sim, mostrar no site @else Não, ocultar do site @endif</label>
                                        </div>
                                    </form>
                                </div>

                                <!-- Aba 2: Links -->
                                <div class="tab-pane fade" id="pills-links-{{$curso->id}}" role="tabpanel">
                                    <h4>Seus Links Prontos para Divulgar</h4>
                                    <p class="text-muted">Copie os links abaixo para usar em suas campanhas. Eles já contêm seu código de afiliado.</p>
                                    @php $dominio = Auth::user()->dominio_externo ?? Auth::user()->dominio; @endphp
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">Página Padrão <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('https://{{$dominio}}/{{$curso->url}}')">Copiar</button></li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">Página WhatsApp <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('https://{{$dominio}}/{{$curso->url}}/w')">Copiar</button></li>
                                        @php
                                            $descontos = [10, 20, 30, 40, 50];
                                        @endphp

                                        @foreach ($descontos as $desconto)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Página Padrão - {{ $desconto }}%
                                                <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('https://{{ $dominio }}/{{ $curso->url }}/o{{ $desconto }}')">
                                                    Copiar
                                                </button>
                                            </li>
                                        @endforeach

                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Checkout sem Boleto
                                                <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('{{$curso->link_checkout_basico}}&hideBillet=1')">
                                                    Copiar
                                                </button>
                                        </li>

                                        @foreach ($descontos as $desconto)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Checkout sem Boleto - {{ $desconto }}%
                                                <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('{{$curso->link_checkout_basico}}&hideBillet=1&offDiscount={{ $desconto }}OFF')">
                                                    Copiar
                                                </button>
                                            </li>
                                        @endforeach

                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Checkout com Boleto
                                                <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('{{$curso->link_checkout_basico}}&hideBillet=0')">
                                                    Copiar
                                                </button>
                                        </li>

                                         @foreach ($descontos as $desconto)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Checkout com Boleto - {{ $desconto }}%
                                                <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('{{$curso->link_checkout_basico}}&hideBillet=0&offDiscount={{ $desconto }}OFF')">
                                                    Copiar
                                                </button>
                                            </li>
                                        @endforeach

                                        
                                    </ul>
                                </div>
                                
                                <!-- Aba 3: Materiais -->
                                <div class="tab-pane fade" id="pills-materiais-{{$curso->id}}" role="tabpanel">
                                    <h4>Recursos para te Ajudar a Vender</h4>
                                    <div class="list-group">
                                        <a href="{{$curso->link_afiliacao}}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">Afiliar-se na Hotmart <i class="ri-external-link-line"></i></a>
                                        <a href="{{$curso->link_materiais}}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">Materiais de Divulgação <i class="ri-external-link-line"></i></a>
                                        @if($curso->codigo_ref)
                                        <a href="{{ route('redirectWithUrl', ['url' => $curso->link_area_membros, 'iframe' => $curso->link_checkout_completo]) }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">Visualizar Área de Membros <i class="ri-external-link-line"></i></a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection


@section('scripts')
    <script>
        // Filtro de Cursos
        $(document).ready(function() {
            $('#campoPesquisa').on('keyup', function() {
                var valor = $(this).val().toLowerCase();
                $('.lista_cursos').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1);
                });
            });
        });

        // Função para copiar texto para a área de transferência
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // A função showAlert já existe no seu html_base, então podemos chamá-la.
                // Se não, você pode usar um simples alert('Link copiado!');
                alert('Link copiado com sucesso!'); 
            }, function(err) {
                alert('Erro ao copiar o link.');
            });
        }
    </script>
@endsection