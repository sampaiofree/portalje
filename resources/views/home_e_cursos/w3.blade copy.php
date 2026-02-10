@extends('home_e_cursos.html_base')

@section('slide')
<div class="row d-md-none">
    <div class="col-12 px-0">
        <div id="carousel_home" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner" role="listbox">
                <div class="carousel-item active">
                    <img class="d-block img-fluid" src="{{asset('img/home_page/bolsas-de-estudo-mobile2.webp')}}" alt="First slide">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row mt-3">
    <div class="col-12 text-center">
        <h2 class="text-info">
            Conheça os treinamentos que podem te ajudar a entrar no mercado de trabalho mais rápido
        </h2>
        <h5>Escolha um dos cursos abaixo e clique no botão "Saiba Mais" para fazer a sua inscrição.</h5>
    </div>
</div>
<div class="row mt-3">
    <div class="col-12 col-lg-8 mx-auto">
        <div class="row">
            @foreach ($cursos as $curso)
                @if($curso->publicado AND $curso->mostrar_na_pagina)
                    <div class="col-12 col-md-4 col-lg-4">
                        {!!$curso->tag_a!!}
                    </div>
                @endif 
            @endforeach
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
@endsection

@section('head')
    <style>
        /*body{font-family: 'Open Sans', sans-serif;}*/
        .topicos{
            font-size: 1.8rem;
            font-weight: 900;
            color: #212529;
            line-height: 1.1;
            font-family: 'Tahoma', sans-serif;
        }
    </style>

@endsection