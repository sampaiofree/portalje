@extends('adm.html_base')
    
@section('content')
<div id="alert-container" class="container"></div>
<div id="spinner" class="d-none container">
    <div class="d-flex justify-content-center">
        <div class="spinner-border" role="status"></div>
    </div>
    <div class="text-center">
        <h3>Criando Anúncios</h4>
        <h5>Este processo pode demorar até 15 minutos</h5>
        <p><strong>Aguarde aqui para ver o resultado de cada anuncio.</strong></p>
    </div>
    
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div>
                    <h4 class="d-flex align-items-center">Anúncios Automáticos <a class=" ms-1" href="https://youtu.be/-NLST-aH0e8" target="_blank"><i class="ri-question-fill font-22 text-primary"></i></a></h4>
                    <p>Não sabe como usar esta ferramenta? Clique <a href="https://youtu.be/-NLST-aH0e8" target="_blank">AQUI</a> para descobrir.</p>
                </div>
            </div>
        </div>
    </div>
    
</div>

@if(
        Auth::user()->meta_conta_anuncios_id AND 
        Auth::user()->meta_pagina_id AND 
        Auth::user()->meta_instagram_id AND 
        Auth::user()->meta_app_id 
        AND !Session::get('accessToken')
    )
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class=" text-center">
                        <a href="#" id="fb-login-button" onclick="initiateFBLogin();"  class="d-block">
                            <div id="divLogin" class="card border cta-box bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-start align-items-center">
                                        <div class="w-100 overflow-hidden">
                                            <h3 id="h3Login" >Faça seu login no Facebook!</h3>
                                            <h4 id="h4Login" class="m-0 fw-normal cta-box-title text-reset" style="line-height: 1.1;font-size: medium;">Clique <strong>AQUI</strong> para fazer seu login.</h4>
                                        </div>
                                        <img id="imgLogin" class="ms-3" src="{{asset('/img/auto_ads/meta.webp')}}" style="max-width:120px" alt="Meta ads">
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    
    @elseif(
        !Auth::user()->meta_conta_anuncios_id OR 
        !Auth::user()->meta_pagina_id OR 
        !Auth::user()->meta_instagram_id OR 
        !Auth::user()->meta_app_id 
    )
    <div class="row">
        <div class="col-sm-4 mx-auto">
            <div class="card cta-box text-bg-primary">
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="m-0 fw-normal text-reset cta-box-title">Você ainda <b>não cadastrou o seu APP</b></h3>

                        <img class="my-3" src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/images/svg/report.svg')}}" width="180" alt="Generic placeholder image">

                        <br/>

                        <a href="{{route('afiliado_configurar_site')}}" class="btn btn-sm btn-light rounded-pill">Cadastrar AGORA <i class="mdi mdi-arrow-right"></i></a>
                    </div>
                </div>
                <!-- end card-body -->
            </div>
        </div>
    </div>
    @else
    <div id="div_form_auto_ads" class="row">
        <div class="col-12 col-sm-7">
            <div class="card">
                <div class="card-body">
                    <form id="form_auto_ads" enctype="multipart/form-data">
                        @csrf
                        <div class="my-3">
                            <label for="campanhaObjetivo">Objetivo da Campanha</label>
                            <select class="form-select" id="campanhaObjetivo" name="campanhaObjetivo" aria-label="Floating label select example">
                                <option value="OUTCOME_LEADS" selected>Cadastros</option>
                                <option value="OUTCOME_SALES">Vendas</option>
                            </select>

                        </div>
                        <div class="my-3">
                            <label for="imagemAnuncio">Adicione sua imagem</label>
                            <p class="my-0 py-0" style="font-size: xx-small">Ela precisa ser quadrada no mínimo 600x600</p>
                            <input class="form-control" type="file" id="imagemAnuncio" name="imagemAnuncio" accept="image/*" >
                        </div>
                        <div class="my-3">
                            <label for="url">Selecione a URL de destino</label>
                            <select id="url" name="url" class="form-select mb-3">
                                @php
                                    /*if(Auth::user()->email == 'sampaio.free@gmail.com' OR Auth::user()->email == 'jovemempreendedor4@gmail.com'){
                                        $dominio = 'jovemempreendedor.org';
                                    }else{
                                        $dominio = Auth::user()->dominio;
                                    }*/

                                    $dominio = Auth::user()->dominio_externo ?? Auth::user()->dominio;
                                @endphp
                                
                                <option value="https://{{$dominio}}/w?c={cidade}">Carvalho WhatsApp <small>({{$dominio}}/w?c={cidade})</small></option>
                                <option value="https://{{$dominio}}?wd=1&c={cidade}">Carvalho WhatsApp Direto <small>({{$dominio}}?wd=1&c={cidade})</small></option>
                                <option value="https://{{$dominio}}?c={cidade}">Home Page <small>({{$dominio}}?c={cidade})</small></option>
                               
                                @foreach($cursos as $curso)
                                    @if($curso->publicado AND $curso->permitir_afiliacao AND $curso->codigo_ref != null AND Auth::user()->email != 'sampaio.free@gmail.com' AND Auth::user()->email != 'jovemempreendedor4@gmail.com')
                                        <option value="https://{{$dominio}}/cursos/{{$curso->url}}/w/?c={cidade}">{{$curso->titulo}}</option>
                                    @elseif($curso->publicado AND (Auth::user()->email == 'sampaio.free@gmail.com' OR Auth::user()->email == 'jovemempreendedor4@gmail.com'))
                                        <option value="https://{{$dominio}}/cursos/{{$curso->url}}/w/?c={cidade}">{{$dominio}}/cursos/{{$curso->url}}/w/?c={cidade}</option>
                                    @endif
                                @endforeach

                                <option value="https://{{$dominio}}?w=1&test=1&c={cidade}"> (Teste w) <small>({{$dominio}}?wd=1&test=1&c={cidade})</small></option>
                                <option value="https://{{$dominio}}?wd=1&test=1&c={cidade}"> (Teste wd) <small>({{$dominio}}?wd=1&test=1&c={cidade})</small></option>
                            </select>
                        </div>

                        <div class="my-3">
                            <label for="datetime">Selecione a data e hora para começar a campanha</label>
                            @php date_default_timezone_set('America/Sao_Paulo'); @endphp
                            <input type="datetime-local" class="form-control" id="datetime" name="datetime" value="{{date("Y-m-d\TH:i")}}">
                        </div>
                        <div class="my-3">
                            <label for="cidades">Cidades: </label>
                            <select class="form-control" id="cidades" name="cidades[]" multiple="multiple" style="width: 100%;"></select>
                        </div>

                        <div class="my-3">
                            <label for="estado">Estado:</label>
                            <select class="form-control" id="estado" name="estado" style="width: 100%;">
                                <option value="">Selecione um estado</option>
                                <option value="Acre">Acre</option>
                                <option value="Alagoas">Alagoas</option>
                                <option value="Amapá">Amapá</option>
                                <option value="Amazonas">Amazonas</option>
                                <option value="Bahia">Bahia</option>
                                <option value="Ceará">Ceará</option>
                                <option value="Distrito Federal">Distrito Federal</option>
                                <option value="Espírito Santo">Espírito Santo</option>
                                <option value="Goiás">Goiás</option>
                                <option value="Maranhão">Maranhão</option>
                                <option value="Mato Grosso">Mato Grosso</option>
                                <option value="Mato Grosso do Sul">Mato Grosso do Sul</option>
                                <option value="Minas Gerais">Minas Gerais</option>
                                <option value="Pará">Pará</option>
                                <option value="Paraíba">Paraíba</option>
                                <option value="Paraná">Paraná</option>
                                <option value="Pernambuco">Pernambuco</option>
                                <option value="Piauí">Piauí</option>
                                <option value="Rio de Janeiro">Rio de Janeiro</option>
                                <option value="Rio Grande do Norte">Rio Grande do Norte</option>
                                <option value="Rio Grande do Sul">Rio Grande do Sul</option>
                                <option value="Rondônia">Rondônia</option>
                                <option value="Roraima">Roraima</option>
                                <option value="Santa Catarina">Santa Catarina</option>
                                <option value="São Paulo">São Paulo</option>
                                <option value="Sergipe">Sergipe</option>
                                <option value="Tocantins">Tocantins</option>
                            </select>
                            <small class="form-text text-muted">Ao selecionar um estado, todas as cidades desse estado serão carregadas.</small>
                        </div>
                        
                        

                        <div class="my-3">
                            <label for="adtitulo" class="form-label">Escreva seu título</label>
                            <input type="text" id="adtitulo" name="adtitulo" class="form-control" value="{cidade} RECEBE +40 CURSOS PROFISSIONALIZANTES">
                        </div>
                        <div class="my-3">
                            <label for="adtexto" class="form-label">Escreva a descrição</label>
                            <textarea class="form-control" id="adtexto" name="adtexto" rows="5">🔥Foi autorizado para a {cidade} o Programa Jovem Empreendedor! 🔥

Sem mensalidades. Sem custos de material.

Apenas uma taxa única de inscrição para garantir acesso a mais de 40 cursos profissionalizantes.

Certificado válido em todo o Brasil.

Clique em "Saiba Mais" e garanta sua vaga enquanto ainda temos inscrições abertas!
</textarea>
                        </div>
                        <div class="my-3">
                            <button type="submit" class="btn btn-primary">Criar campanha</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-sm-5">
            <div class="card border">
                <div class="card-header">
                    <h3 class="">Prévia do criativo</h3>
                    <a href="https://www.canva.com/design/DAGKu3fd28U/DTTIJd5g69caHC3TT0aTZQ/view?utm_content=DAGKu3fd28U&utm_campaign=designshare&utm_medium=link&utm_source=publishsharelink&mode=preview" target="_blank">Cliqe AQUI para baixar um modelo de imagem</a>
                </div>
                <div class="card-body">
                    <div class="card border border-2 rounded-3">
                        <div class="card-body">
                            <h6 id="previaTexto" class="card-subtitle text-muted"></h6>
                        </div>
                        <div class="image-container">                                
                            <img id="previewImagem" src="{{asset('img/auto_ads/selcione-a-imagem.png')}}" class="img-fluid" alt="Imagem">
                            <div class="image-text">NOME DA CIDADE</div>
                        </div>
                        <div class="card-body">
                            <h4 id="previaTitulo" class="card-title"></h4>
                        </div> <!-- end card-body-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('head')
    <link href="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .select2-selection__choice__display{color: black;}

        .image-container {
            position: relative;
            display: inline-block;
        }
        .image-container img {
            display: block;
        }
        .image-text {
            font-family: 'Helvetica', Arial, sans-serif;
            font-weight: bolder;
            position: absolute;
            top: 0px;
            left: 50%;
            transform: translateX(-50%);
            /*background: rgba(0, 0, 0, 0.5); !* Fundo semitransparente *!*/
            color: white;
            padding: 10px;
            font-size: 2em;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
        }
    </style>
@endsection

@section('scripts')

<script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/select2/js/select2.min.js')}}"></script>

<script>
    //MOSTRAR A IMAGEM NA PREVIA
    document.addEventListener('DOMContentLoaded', (event) => {
        const inputFile = document.getElementById('imagemAnuncio');

        inputFile.addEventListener('change', function() {
            const file = this.files[0];

            if (file) {
                const img = new Image();

                img.onload = function() {
                    if (img.width > 1080 || img.height > 1080) {
                        alert('A imagem deve ter no máximo 1080 x 1080 pixels.');
                        inputFile.value = '';
                    } else {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById('previewImagem').src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                };

                img.onerror = function() {
                    alert('Arquivo inválido. Por favor, selecione uma imagem.');
                    inputFile.value = '';
                };

                img.src = URL.createObjectURL(file);
            }
        });
    });

    /** ATUALIZAR EM TEMPO REAL A PREVIA **/
    $(document).ready(function() {
        // Função para atualizar o título em tempo real
        $('#adtitulo').on('input', function() {
            var valoradtitulo = $(this).val();
            $('#previaTitulo').text(valoradtitulo);
        });

        $('#adtexto').on('input', function() {
            var valoradTexto = $(this).val();
            $('#previaTexto').text(valoradTexto);
        });

        // Atualiza o título ao carregar a página
        $('#adtitulo').trigger('input');
        $('#adtexto').trigger('input');
    });


    @if(
        Auth::user()->meta_conta_anuncios_id AND 
        Auth::user()->meta_pagina_id AND 
        Auth::user()->meta_instagram_id AND 
        Auth::user()->meta_app_id 
        AND !Session::get('accessToken')  
    )

        
            window.fbAsyncInit = function() {
            FB.init({
                appId      : '{{Auth::user()->meta_app_id}}',
                cookie     : true,
                xfbml      : true,
                version    : 'v20.0'
            });

            FB.AppEvents.logPageView();

            checkLoginState();

            // Adiciona um listener para o evento de clique no botão após a inicialização do SDK
            document.getElementById('fb-login-button').addEventListener('click', checkLoginState);
        };

        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        function checkLoginState() {
            FB.getLoginStatus(function(response) {
                statusChangeCallback(response);
            });
        }

        function initiateFBLogin() {
            FB.login(function(response) {
                statusChangeCallback(response);
            }, {scope: 'public_profile,email,ads_management,ads_read,business_management'});
        }

        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                var accessToken = response.authResponse.accessToken;
                console.log('User is logged in.');
                console.log(accessToken);           
                sendTokenToBackend(accessToken);
                
            } else if (response.status === 'not_authorized') {
                showAlert('Existe algum erro nos dados do seu APP! Revise as configurações do site', 'warning');
            }else {
                //showAlert('Existe algum erro nos dados do seu APP! Revise as configurações do site', 'warning');
            }
        }

        function sendTokenToBackend(token) {
                fetch('{{route('meta_accessToken')}}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ accessToken: token })
                })
                .then(response => response.json())
                .then(data => {
                    //showAlert('Login feito com sucesso!', 'success');
                    window.location.reload();
                })
                .catch(error => {
                    showAlert('Você está desconectado ou existe algum erro nos dados do seu APP! Laça seu Login ou Revise as configurações do site', 'warning');
                });
        }

        
    @endif
    function showAlert(messages, type) {
        var alertContainer = document.getElementById('alert-container');
        alertContainer.innerHTML = ''; // Limpa alertas anteriores

        var alertBox = document.createElement('div');
        alertBox.className = `alert alert-${type}`;
        
        if (Array.isArray(messages)) {
            var list = document.createElement('ul');
            messages.forEach(function(msg) {
                var listItem = document.createElement('li');
                listItem.textContent = msg;
                list.appendChild(listItem);
            });
            alertBox.appendChild(list);
        } else {
            alertBox.textContent = messages;
        }

        alertContainer.appendChild(alertBox);
        
    }

    $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#cidades').select2({
                ajax: {
                    url: '{{ route('cidades.buscar') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            query: params.term // termo de busca
                        };
                    },
                    processResults: function (data) {
                        if (data.error) {
                            console.error(data.error);
                            return { results: [] };
                        }

                        if (data.message) {
                            console.warn(data.message);
                            return { results: [] };
                        }

                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.cidade+" - "+item.estado
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                placeholder: 'Selecione as cidades',
                allowClear: true
            });
           
            $('#form_auto_ads').submit(function(event) {
                event.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: '{{ route('cidades.processar') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        // Mostrar o spinner
                        document.getElementById('spinner').classList.remove('d-none');
                        document.getElementById('div_form_auto_ads').classList.add('d-none');
                    },
                    success: function(response) {
                        var messages = response.message;
                        showAlert(messages, response.status === 'success' ? 'success' : 'warning');
                    },
                    error: function(xhr, status, error) {
                        var response = JSON.parse(xhr.responseText);
                        var errorMessage = response.message;
                        if (response.errors) {
                            errorMessage += '<ul>';
                            $.each(response.errors, function(key, value) {
                                errorMessage += '<li>' + value.join(', ') + '</li>';
                            });
                            errorMessage += '</ul>';
                        }
                        showAlert(errorMessage, 'warning');
                    },
                    complete: function() {
                        // Ocultar o spinner
                        document.getElementById('spinner').classList.add('d-none');
                        document.getElementById('div_form_auto_ads').classList.remove('d-none');
                    }

                });
            });
    });
</script>
@endsection