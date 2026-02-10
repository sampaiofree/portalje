@extends('home_e_cursos.html_base')

@section('slide')
    {{-- SLIDE --}}
    <div class="row d-md-none">
        <div class="col-12 px-0">
            <div id="carousel_home" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner" role="listbox">
                    <div class="carousel-item active">
                        <img class="d-block img-fluid" src="{{asset('img/home_page/slide1.webp')}}" alt="First slide">
                    </div>
                    <div class="carousel-item">
                        <img class="d-block img-fluid" src="{{asset('img/home_page/slide2.webp')}}" alt="Second slide">
                    </div>
                    <div class="carousel-item">
                        <img class="d-block img-fluid" src="{{asset('img/home_page/slide3.webp')}}" alt="Third slide">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    {{-- LISTA DOS CURSOS --}}
    <div id="cursos_lista" class="row bg-dark text-white py-5">
        <div class="col-12 col-lg-8 offset-lg-2">
            <h2 class="h3 text-center">Conheça os treinamentos que podem te ajudar a entrar no mercado de trabalho mais rápido</h2>
            <h5 class="text-center">Escolha um dos cursos abaixo e clique no botão "Saiba Mais" para fazer a sua inscrição.</h5>
            <div class="row mt-3 px-2">
                @php
                    $redirecionar = true;    
                @endphp
                @foreach ($cursos as $curso)
                    @if($curso->publicado AND $curso->mostrar_na_pagina)
                        @php
                            $redirecionar = false;    
                        @endphp
                        <div class="col-6 col-md-4 col-lg-2 p-1">
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
@endsection

@section('head')
    <style>
        #carousel_home img{width: 100%;}
    </style>
@endsection