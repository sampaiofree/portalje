@extends('adm.html_base')

@section('titulo_pagina', 'Informações do Portal')
@section('descricao_pagina', 'Configuração dos dados globais usados nas páginas públicas')

@section('content')
<div class="row">
    <div class="col-12">
        <form method="POST" action="{{ route('adm_portal_informacoes_update') }}">
            @csrf

            <div class="card border mb-3">
                <div class="card-header">
                    <h4 class="mb-0">Contatos e Dados Institucionais</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefone_suporte_alunos" class="form-label">Telefone do Suporte aos Alunos</label>
                            <input
                                type="text"
                                id="telefone_suporte_alunos"
                                name="telefone_suporte_alunos"
                                class="form-control"
                                value="{{ old('telefone_suporte_alunos', $dadosPortal->telefone_suporte_alunos ?? '') }}"
                                placeholder="5511982671533"
                                required
                            >
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="telefone_suporte_afiliados" class="form-label">Telefone do Suporte aos Afiliados</label>
                            <input
                                type="text"
                                id="telefone_suporte_afiliados"
                                name="telefone_suporte_afiliados"
                                class="form-control"
                                value="{{ old('telefone_suporte_afiliados', $dadosPortal->telefone_suporte_afiliados ?? '') }}"
                                placeholder="55119..."
                            >
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cnpj" class="form-label">CNPJ</label>
                            <input
                                type="text"
                                id="cnpj"
                                name="cnpj"
                                class="form-control"
                                value="{{ old('cnpj', $dadosPortal->cnpj ?? '') }}"
                            >
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="endereco" class="form-label">Endereço</label>
                            <input
                                type="text"
                                id="endereco"
                                name="endereco"
                                class="form-control"
                                value="{{ old('endereco', $dadosPortal->endereco ?? '') }}"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border mb-3">
                <div class="card-header">
                    <h4 class="mb-0">Comportamento de Página</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="whatsapp_atendimento_tempo" class="form-label">Tempo para exibir WhatsApp (segundos)</label>
                        <input
                            type="text"
                            id="whatsapp_atendimento_tempo"
                            name="whatsapp_atendimento_tempo"
                            class="form-control"
                            value="{{ old('whatsapp_atendimento_tempo', $dadosPortal->whatsapp_atendimento_tempo ?? '0') }}"
                            placeholder="0"
                        >
                    </div>

                    <div class="mb-3">
                        @php
                            $checkedWhatsapp = old(
                                'formulario_whatsapp',
                                isset($dadosPortal) ? (int) $dadosPortal->formulario_whatsapp : 1
                            );
                        @endphp
                        <h5>Mostrar formulário WhatsApp?</h5>
                        <div class="form-check form-switch mt-2">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                role="switch"
                                id="formulario_whatsapp"
                                name="formulario_whatsapp"
                                value="1"
                                @checked((int) $checkedWhatsapp === 1)
                            >
                            <label class="form-check-label" for="formulario_whatsapp">Ativado</label>
                        </div>
                    </div>

                    <div class="mb-0">
                        @php
                            $checkedPreCheckout = old(
                                'formulario_pre_checkout',
                                isset($dadosPortal) ? (int) $dadosPortal->formulario_pre_checkout : 1
                            );
                        @endphp
                        <h5>Mostrar formulário Pre Checkout?</h5>
                        <div class="form-check form-switch mt-2">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                role="switch"
                                id="formulario_pre_checkout"
                                name="formulario_pre_checkout"
                                value="1"
                                @checked((int) $checkedPreCheckout === 1)
                            >
                            <label class="form-check-label" for="formulario_pre_checkout">Ativado</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-2">
                <button type="submit" class="btn btn-secondary">Salvar alterações</button>
            </div>
        </form>
    </div>
</div>
@endsection
