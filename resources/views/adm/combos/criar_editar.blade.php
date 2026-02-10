@extends('adm.html_base') 


@section('content')
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h4>{{$combo->titulo}}</h4>
                <a target="_black" href="{{asset($combo->url)}}" class="badge badge-lg d-inline bg-info">URL: {{$combo->url}}</a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('combo.editar') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $combo->id }}">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Nome do Combo</label>
                        <input type="text" id="titulo" name="titulo" class="form-control" value="{{$combo->titulo}}">
                    </div>

                    <div class="mb-3">
                        <label for="url" class="form-label">URL</label>
                        <input type="text" id="url" name="url" class="form-control" value="{{$combo->url}}">
                    </div>

                    <div class="mb-3">
                        <label for="headline" class="form-label">Headline</label>
                        <input type="text" id="headline" name="headline" class="form-control" value="{{$combo->headline}}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="editor_descricao_curta" class="form-label">Descrição Curta</label>
                        <input type="text" id="descricao_curta" name="descricao_curta" class="form-control" value="{{$combo->descricao_curta}}">
                    </div>

                    
                    <div class="mb-3">
                        <label for="link_checkout" class="form-label">Link do checkout </label>
                        <input type="text" id="link_checkout" name="link_checkout" class="form-control" value="{{$combo->link_checkout}}">
                    </div>

            

                   
                    <div class="mb-3">
                        <label for="preco_parcelado" class="form-label">Preço parcelado </label>
                        <input type="text" id="preco_parcelado" name="preco_parcelado" class="form-control" value="{{$combo->preco_parcelado}}">
                    </div>

                    

                    <div class="mb-3">
                        <label for="preco" class="form-label">Preço </label>
                        <input type="text" id="preco" name="preco" class="form-control" value="{{$combo->preco}}">
                    </div>

                   

                    <div class="mb-3">
                        <button type="submit" class="btn btn-secondary">Editar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
     
</div>
@endsection

@section('head')
    
@endsection

@section('scripts')


@endsection