@extends('adm.html_base')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="d-flex align-items-center">Lista dos cursos do Portal JE <a class=" ms-1" href="https://www.youtube.com/watch?v=G3_UjvwizSc" target="_blank"><i class="ri-question-fill font-22 text-primary"></i></a></h4>
                    <p>Está Perdido e Confuso? Clique <a href="https://www.youtube.com/watch?v=G3_UjvwizSc" target="_blank">AQUI</a> para saber como usar esta página.</p>
                    <div class="row">
                        <div class="col-lg-4">
                            <input class="form-control" type="text" id="campoPesquisa" placeholder="Digite AQUI o curso que você procura" >
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @foreach($cursos as $curso)
            @if($curso->publicado AND $curso->permitir_afiliacao)
                        <div class="lista_cursos col-sm-6 col-md-4 col-lg-3">
                            <div class="card d-block">
                                <div class="card-body">
                                    <h5 class="mb-0"><strong>{{$curso->titulo." ".$curso->codigo_id_hotmart}}</strong></h5>
                                    <p class="mt-0 text-primary">preço: <span class="fw-bold">{{$curso->preco_cheio_completo}}</span></p>
                                    <p class="card-text">{{$curso->headline}}</p>
                                    <p class="card-text" style="font-size: medium;">
                                        <span class="badge @if($curso->codigo_ref) bg-success @else bg-warning @endif">
                                            @if($curso->codigo_ref) Cadastrado @else Não cadastrado @endif
                                        </span>
                                    </p>
                                    
                                    <form id='form_{{$curso->id}}' method="POST" action="{{route('cadastrar_codigo_ref')}}">
                                        @csrf
                                        <div class="row gy-2 gx-2 mb-2">
                                            <div class="col-auto">
                                                <div class="input-group d-flex align-items-center">
                                                    <div class="input-group-text">Código REF <i class="ms-2 ri-arrow-right-line"></i></div>
                                                    <!--<input type="text" class="form-control" name="codigo_ref" placeholder="Código REF" value="{{$curso->codigo_ref}}" required>-->
                                                    <input 
                                                    type="text" 
                                                    class="form-control" 
                                                    name="codigo_ref" 
                                                    placeholder="Código REF" 
                                                    value="{{$curso->codigo_ref}}" 
                                                    maxlength="15" 
                                                    pattern="[^./:]*" 
                                                    title="Não é permitido o uso dos caracteres '.', '/' ou ':'." 
                                                    required
                                                    >

                                                    
                                                    <input type="hidden" name="curso_id" value="{{$curso->id}}">
                                                    <input type="hidden" name="titulo" value="{{$curso->titulo}}">
                                                    <input type="hidden" name="id" value="{{$curso->codigo_ref_id}}">
                                                    <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                @if($curso->codigo_ref) <button type="submit" class="btn btn-secondary">Editar codigo REF </button> @else <button type="submit" class="btn btn-primary">Cadastrar código REF</button> <p style="font-size: x-small" class="mt-1 text-danger">Não sabe conde encontrar o código REF? Clique <a href="https://www.youtube.com/watch?v=G3_UjvwizSc" target="_blank">AQUI</a> e descubra.</p> @endif
                                                
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <h5>Mostrar curso no site?</h5>
                                            <input type="checkbox" id="input_mostrar_curso_{{$curso->id}}" name="mostrar_curso" @if($curso->mostrar_curso) checked @endif data-switch="primary" onchange="submitForm('form_{{$curso->id}}')" />
                                            <label for="input_mostrar_curso_{{$curso->id}}" data-on-label="Sim" data-off-label="Não"></label>
                                        </div>
                                        
                                    </form>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item py-1"><a target="_blank" href="{{$curso->link_afiliacao}}" class="card-link text-custom d-flex align-items-center">Afiliar-se <i class="ms-1 ri-external-link-line "></i></a></li>
                                            <li class="list-group-item py-1"><a target="_blank" href="{{$curso->link_materiais}}" class="card-link text-custom d-flex align-items-center">Materiais de divulgação<i class="ms-1 ri-external-link-line "></i></a></li>
                                        @if($curso->codigo_ref)
                                            <li class="list-group-item py-1"><a target="_blank" href="{{ route('redirectWithUrl', ['url' => $curso->link_area_membros, 'iframe' => $curso->link_checkout_completo]) }}" class="card-link text-custom d-flex align-items-center">Área de membros<i class="ms-1 ri-external-link-line "></i></a></li>
                                            
                                            <li class="list-group-item dropdown py-1">
                                                <a href="javascript:void(0);" class="dropdown-toggle card-link text-custom d-flex align-items-center" id='drop{{$curso->id}}' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                                    Links da página
                                                </a>
                                                @php
                                                 if(Auth::user()->dominio_externo){$dominio = Auth::user()->dominio_externo;}else{$dominio = Auth::user()->dominio;}   
                                                @endphp
                                                <div class="dropdown-menu" aria-labelledby='drop{{$curso->id}}' style="max-width: 300px;">
                                                    <a href="https://{{$dominio}}/{{$curso->url}}" target="_blank" class="dropdown-item">Página Padrão</a>
                                                    <a href="https://{{$dominio}}/{{$curso->url}}/w" target="_blank" class="dropdown-item">Página WhatsApp</a>
                                                    <a href="https://{{$dominio}}/{{$curso->url}}/o10" target="_blank" class="dropdown-item">Deconto 10%</a>
                                                    <a href="https://{{$dominio}}/{{$curso->url}}/o20" target="_blank" class="dropdown-item">Deconto 20%</a>
                                                    <a href="https://{{$dominio}}/{{$curso->url}}/o30" target="_blank" class="dropdown-item">Deconto 30%</a>
                                                    <a href="https://{{$dominio}}/{{$curso->url}}/o40" target="_blank" class="dropdown-item">Deconto 40%</a>
                                                    <a href="https://{{$dominio}}/{{$curso->url}}/o50" target="_blank" class="dropdown-item">Deconto 50%</a>
                                                    <!--<a href="https://{{$dominio}}/{{$curso->url}}/o60" target="_blank" class="dropdown-item">Deconto 60%</a>
                                                    <a href="https://{{$dominio}}/{{$curso->url}}/o70" target="_blank" class="dropdown-item">Deconto 70%</a>-->
                                                </div>
                                            </li>
                                            <li class="list-group-item dropdown py-1">
                                                <a href="javascript:void(0);" class="dropdown-toggle card-link text-custom d-flex align-items-center" id='dropg{{$curso->id}}' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                                    Aulas Gratuitas
                                                </a>
                                                @php
                                                 if(Auth::user()->dominio_externo){$dominio = Auth::user()->dominio_externo;}else{$dominio = Auth::user()->dominio;}   
                                                @endphp
                                                <div class="dropdown-menu" aria-labelledby='dropg{{$curso->id}}' style="max-width: 300px;">
                                                    <a href="https://{{$dominio}}/{{$curso->url}}?g=1" target="_blank" class="dropdown-item">Com link para checkout</a>
                                                    <a href="https://{{$dominio}}/{{$curso->url}}/w?g=1" target="_blank" class="dropdown-item">Com link para o WhatsApp</a>
                                                </div>
                                            </li>
                                            <li class="list-group-item dropdown py-1">
                                                <a href="javascript:void(0);" class="dropdown-toggle card-link text-custom d-flex align-items-center" id='check{{$curso->id}}' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                                    Links do checkout
                                                </a>
                                                <div class="dropdown-menu" aria-labelledby='check{{$curso->id}}' style="max-width: 300px;">
                                                    @if($curso->link_checkout_basico)
                                                    <div class="dropdown">
                                                        <a href="javascript:void(0);" class="dropdown-item dropdown-toggle text-secondary" id='check_b{{$curso->id}}' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                                            Checkout Básico
                                                        </a>
                                                        <div class="dropdown-menu" aria-labelledby='check_b{{$curso->id}}' style="max-width: 300px;">
                                                            
                                                            <div class="dropdown">
                                                                <a href="javascript:void(0);" class="dropdown-item dropdown-toggle text-secondary" id='check_b_sb{{$curso->id}}' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                                                    Com boleto
                                                                </a>
                                                                <div class="dropdown-menu" aria-labelledby='check_b_sb{{$curso->id}}' style="max-width: 300px;">
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_basico}}&hideBillet=0" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Checkout Padrão</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_basico}}&hideBillet=0&offDiscount=10OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 10%</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_basico}}&hideBillet=0&offDiscount=20OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 20%</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_basico}}&hideBillet=0&offDiscount=30OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 30%</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_basico}}&hideBillet=0&offDiscount=40OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 40%</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_basico}}&hideBillet=0&offDiscount=50OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 50%</a>
                                                                    <!--<a style='cursor: pointer;' data-href="{{$curso->link_checkout_basico}}&hideBillet=0&offDiscount=60OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 60%</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_basico}}&hideBillet=0&offDiscount=70OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 70%</a>-->
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_basico}}&hideBillet=0&offDiscount=80OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 80%</a>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="dropdown">
                                                                <a href="javascript:void(0);" class="dropdown-item dropdown-toggle text-secondary" id='check_b_sb{{$curso->id}}' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                                                   
                                                                    Sem boleto
                                                                </a>
                                                                <div class="dropdown-menu" aria-labelledby='check_b_sb{{$curso->id}}' style="max-width: 300px;">
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_basico}}" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Checkout Padrão</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_basico}}&offDiscount=10OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 10%</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_basico}}&offDiscount=20OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 20%</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_basico}}&offDiscount=30OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 30%</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_basico}}&offDiscount=40OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 40%</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_basico}}&offDiscount=50OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 50%</a>
                                                                    <!--<a style='cursor: pointer;' data-href="{{$curso->link_checkout_basico}}&offDiscount=60OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 60%</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_basico}}&offDiscount=70OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 70%</a>-->
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_basico}}&offDiscount=80OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 80%</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    <div class="dropdown">
                                                        <a href="javascript:void(0);" class="dropdown-item dropdown-toggle text-secondary" id='check_b{{$curso->id}}' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                                            Checkout Completo
                                                        </a>
                                                        <div class="dropdown-menu" aria-labelledby='check_b{{$curso->id}}' style="max-width: 300px;">
                                                            <div class="dropdown">
                                                                <a href="javascript:void(0);" class="dropdown-item dropdown-toggle text-secondary" id='check_b_sb{{$curso->id}}' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                                                    Com boleto
                                                                </a>
                                                                <div class="dropdown-menu" aria-labelledby='check_b_sb{{$curso->id}}' style="max-width: 300px;">
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_completo}}&hideBillet=0" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Checkout Padrão</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_completo}}&hideBillet=0&offDiscount=10OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 10%</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_completo}}&hideBillet=0&offDiscount=20OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 20%</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_completo}}&hideBillet=0&offDiscount=30OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 30%</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_completo}}&hideBillet=0&offDiscount=40OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 40%</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_completo}}&hideBillet=0&offDiscount=50OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 50%</a>
                                                                    <!--<a style='cursor: pointer;' data-href="{{$curso->link_checkout_completo}}&hideBillet=0&offDiscount=60OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 60%</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_completo}}&hideBillet=0&offDiscount=70OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 70%</a>-->
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_completo}}&hideBillet=0&offDiscount=80OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 80%</a>
                                                                </div>
                                                            </div>
                                                            <div class="dropdown">
                                                                <a href="javascript:void(0);" class="dropdown-item dropdown-toggle text-secondary" id='check_b_sb{{$curso->id}}' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                                                    Sem boleto
                                                                </a>
                                                                <div class="dropdown-menu" aria-labelledby='check_b_sb{{$curso->id}}' style="max-width: 300px;">
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_completo}}" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Checkout Padrão</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_completo}}&offDiscount=10OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 10%</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_completo}}&offDiscount=20OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 20%</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_completo}}&offDiscount=30OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 30%</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_completo}}&offDiscount=40OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 40%</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_completo}}&offDiscount=50OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 50%</a>
                                                                    <!--<a style='cursor: pointer;' data-href="{{$curso->link_checkout_completo}}&offDiscount=60OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 60%</a>
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_completo}}&offDiscount=70OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 70%</a>-->
                                                                    <a style='cursor: pointer;' data-href="{{$curso->link_checkout_completo}}&offDiscount=80OFF" target="_blank" onclick="copyDataHref(this)" class="dropdown-item">Desconto de 80%</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            @if($curso->video_dentro_do_curso OR $curso->video_apresentacao)
                                                <li class="list-group-item dropdown py-1">
                                                    <a href="javascript:void(0);" class="dropdown-toggle card-link text-custom d-flex align-items-center" id='check{{$curso->id}}' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                                        Vídeos
                                                    </a>
                                                    <div class="dropdown-menu" aria-labelledby='check{{$curso->id}}' style="max-width: 300px;">
                                                        @if($curso->video_dentro_do_curso)
                                                        <a href="https://{{$dominio}}/redirect?iframe={{$curso->link_area_membros}}&url=https://www.youtube.com/embed/{{$curso->video_dentro_do_curso}}" target="_blank" class="dropdown-item">Vídeo dentro do curso</a>
                                                        @endif
                                                        @if($curso->video_apresentacao)
                                                        <a href="https://{{$dominio}}/redirect?iframe={{$curso->link_area_membros}}&url=https://www.youtube.com/embed/{{$curso->video_apresentacao}}" target="_blank" class="dropdown-item">Vídeo de Apresentação</a>
                                                        @endif
                                                    </div>
                                                </li>
                                            @endif
                                        @endif
                                        </ul>
                                </div> 
                            </div> 
                        </div>
            @endif
         @endforeach      
    </div>
@endsection

@section('header')
    
@endsection

@section('scripts')
    <script>
        //CAMPO PARA PROCURAR CURSO
          $(document).ready(function() {
        // Quando o usuário digita no campo de pesquisa
            $('#campoPesquisa').on('keyup', function() {
                var valor = $(this).val().toLowerCase(); // Pega o valor digitado, transforma em minúsculas

                // Verifica cada item do acordeão
                $('.lista_cursos').filter(function() {
                    // Alterna a visibilidade do item do acordeão com base na correspondência da pesquisa
                    $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1);
                });
            });
        });



        function submitForm(id) {
            document.getElementById(id).submit();
        }

        function copyDataHref(element) {
            // Obtém o valor do atributo data-href
            const link = element.getAttribute("data-href");

            // Cria um elemento de input temporário
            const tempInput = document.createElement("input");
            tempInput.style.position = "absolute";
            tempInput.style.left = "-9999px";
            tempInput.value = link;
            document.body.appendChild(tempInput);

            // Seleciona o conteúdo do input temporário
            tempInput.select();
            tempInput.setSelectionRange(0, 99999); // Para dispositivos móveis

            // Executa o comando de cópia
            document.execCommand("copy");

            // Remove o input temporário
            document.body.removeChild(tempInput);

            // Exibe uma mensagem de confirmação (opcional)
            showAlert('Link copiado com sucesso!');
        }

    </script>
@endsection