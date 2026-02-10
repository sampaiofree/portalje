@extends('adm.html_base')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="telefone_suporte_alunos" class="form-label">Telefone do Suporte aos Alunos</label>
                    <input type="number" id="telefone_suporte_alunos" name="telefone_suporte_alunos" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="telefone_suporte_afiliados" class="form-label">Telefone do Suporte aos Afiliados</label>
                    <input type="number" id="telefone_suporte_afiliados" name="telefone_suporte_afiliados" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="endereco" class="form-label">Endereço</label>
                    <input type="text" id="endereco" name="endereco" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="cnpj" class="form-label">Endereço</label>
                    <input type="number" id="cnpj" name="cnpj" class="form-control">
                </div>
                <div class="mb-3">
                    <h5>Mostrar formulário WhatsApp?</h5>
                    <input type="checkbox" id="formulario_whatsapp" name="formulario_whatsapp" checked data-switch="bool" value="1"/>
                    <label for="formulario_whatsapp" data-on-label="On" data-off-label="Off"></label>
                </div>
                <div class="mb-3">
                    <h5>Mostrar formulário Pre Checkout?</h5>
                    <input type="checkbox" id="formulario_pre_checkout" name="formulario_pre_checkout" checked data-switch="bool" value="1"/>
                    <label for="formulario_pre_checkout" data-on-label="On" data-off-label="Off"></label>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection