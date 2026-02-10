@extends('adm.html_base')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="mt-3">
                <div id="alert_teste_pixel"></div>
                <form method="POST" action="{{route('afiliado_configurar_site_post')}}">
                    @csrf
                    <div class="d-none card border mb-3">
                        <div class="card-header"><h4>Configuração de Formulários</h4></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h5>Mostrar formulário WhatsApp?</h5>
                                <input type="checkbox" id="formulario_whatsapp" name="formulario_whatsapp" @if(Auth::user()->formulario_whatsapp) checked @endif data-switch="bool" value="1"/>
                                <label for="formulario_whatsapp" data-on-label="On" data-off-label="Off"></label>
                            </div>
        
                            <div class="mb-3">
                                <h5>Mostrar formulário antes do checkout?</h5>
                                <input type="checkbox" id="formulario_pre_checkout" name="formulario_pre_checkout" @if(Auth::user()->formulario_pre_checkout) checked @endif data-switch="bool" value="1"/>
                                <label for="formulario_pre_checkout" data-on-label="On" data-off-label="Off"></label>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-secondary" type="submit">Alterar</button>
                            </div>
                        </div>
                    </div>
                    <div class="card border mb-3">
                        <div class="card-header"><h4>Dados do Parceiro</h4></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="telefone_pessoal_1" class="form-label">Telefone Pessoal 1</label>
                                <input type="number" id="telefone_pessoal_1" name="telefone_pessoal_1" class="form-control" placeholder="digite apenas número com o DDI do país" value="{{Auth::user()->telefone_pessoal_1}}">
                            </div>
                            <div class="mb-3">
                                <label for="telefone_pessoal_2" class="form-label">Telefone Pessoal 2</label>
                                <input type="number" id="telefone_pessoal_2" name="telefone_pessoal_2" class="form-control" placeholder="digite apenas número com o DDI do país" value="{{Auth::user()->telefone_pessoal_2}}">
                            </div>
                            <div class="mb-3">
                                <label for="apelido" class="form-label">Apelido</label>
                                <input type="Text" id="apelido" name="apelido" class="form-control" placeholder="O nome que aparecerá no Ranking" value="{{Auth::user()->apelido}}">
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-secondary" type="submit">Alterar</button>
                            </div>
                        </div>
                    </div>
                    <div id="section_dominio_externo"></div>
                    <div class="card border mb-3">
                        <div class="card-header"><h4>Domínio externo | Domínio próprio</h4><p>Somente para domínio pago, deixe em branco.</p></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="dominio_externo" class="form-label">Digite seu domínio sem https e sem www</label>
                                <input type="text" id="dominio_externo" name="dominio_externo" class="form-control" value="{{Auth::user()->dominio_externo}}">
                            </div>
                            <div id="alert_dominio_proprio_feedback"></div>
                            <div class="mb-3">
                                <button class="btn btn-secondary" type="submit">Alterar</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card border mb-3">
                        <div class="card-header">
                            <h4>Meta Ads (Facebook Ads)</h4>
                            <a class="ri-youtube-line" target="_blank" href="https://youtu.be/Ev-RMK0p62Y"> <i class=""></i>Como configurar o pixel na hotmart</a>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="meta_pixel_id" class="form-label">Meta Pixel ID - <i>Pixel do Facebook</i></label>
                                <input type="Text" id="meta_pixel_id" name="meta_pixel_id" class="form-control" placeholder="Pixel ID do Facebook" value="{{Auth::user()->meta_pixel_id}}">
                                <div class="mt-1 d-flex align-items-center">
                                    <a id="btn_testar_pixel" class="btn btn-secondary">Testar PIXEL</a>
                                    <a class=" ms-1" href="https://youtu.be/xN9_Y-xUvL0" target="_blank" ><i class="ri-question-fill font-22 text-primary"></i></a>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="meta_pixel_api" class="form-label">Meta Pixel Token - <i>Token da API (Opcional)</i></label>
                                <input type="Text" id="meta_pixel_api" name="meta_pixel_api" class="form-control" placeholder="Token API Facebook" value="{{Auth::user()->meta_pixel_api}}">
                            </div>
                            <div class="mb-3">
                                <label for="meta_pixel_eventcode" class="form-label">Meta Pixel Event (Opcional)</i></label>
                                <input type="Text" id="meta_pixel_eventcode" name="meta_pixel_eventcode" class="form-control" placeholder="Event Code" value="{{Auth::user()->meta_pixel_eventcode}}">
                            </div>
                            <div class="mb-3">
                                <label for="meta_conta_anuncios_id" class="form-label">Conta Anuncios ID</i> <small>Preencha somente se for usar a ferramenta "Anúncios Automáticos", caso contrario deixe em branco.</small></label>
                                <input type="Text" id="meta_conta_anuncios_id" name="meta_conta_anuncios_id" class="form-control" placeholder="Event Code" value="{{Auth::user()->meta_conta_anuncios_id}}">
                            </div>
                            <div class="mb-3">
                                <label for="meta_pagina_id" class="form-label">Página ID</i> <small>Preencha somente se for usar a ferramenta "Anúncios Automáticos", caso contrario deixe em branco.</small></label>
                                <input type="Text" id="meta_pagina_id" name="meta_pagina_id" class="form-control" placeholder="Event Code" value="{{Auth::user()->meta_pagina_id}}">
                            </div>
                            <div class="mb-3">
                                <label for="meta_instagram_id" class="form-label">Instagram ID</i> <small>Preencha somente se for usar a ferramenta "Anúncios Automáticos", caso contrario deixe em branco.</small></label>
                                <input type="Text" id="meta_instagram_id" name="meta_instagram_id" class="form-control" placeholder="Event Code" value="{{Auth::user()->meta_instagram_id}}">
                            </div>
                            <div class="mb-3">
                                <label for="meta_app_id" class="form-label">App ID</i> <small>Preencha somente se for usar a ferramenta "Anúncios Automáticos", caso contrario deixe em branco.</small></label>
                                <input type="Text" id="meta_app_id" name="meta_app_id" class="form-control" placeholder="Event Code" value="{{Auth::user()->meta_app_id}}">
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-secondary" type="submit">Alterar</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card border mb-3">
                        <div class="card-header"><h4>ManyChat</h4></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="many_api" class="form-label">API</label>
                                <input type="text" id="many_api" name="many_api" class="form-control" placeholder="digite aqui a API do manychat" value="{{Auth::user()->many_api}}">
                            </div>
                            <div class="mb-3">
                                <label for="many_cliente_telefone_id" class="form-label">ID do campo cliente_telefone_id</label>
                                <input type="text" id="many_cliente_telefone_id" name="many_cliente_telefone_id" class="form-control" placeholder="digite aqui a API do manychat" value="{{Auth::user()->many_cliente_telefone_id}}">
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-secondary" type="submit">Alterar</button>
                            </div>
                        </div>
                    </div>

                    <div class="card border mb-3">
                        <div class="card-header"><h4 class="d-flex align-items-center">
                            BotConversa
                            <a class=" ms-1" href="https://youtu.be/KD2bZ1miXCc" target="_blank" ><i class="ri-question-fill font-22 text-primary"></i></a>
                        </h4>
                        <a target="_blank" href="https://drive.google.com/drive/folders/11NSQkACBTocGxk6DclMnBOiP3s2Arc_T?usp=drive_link">Veja também os materiais e vídeo complementares AQUI</a>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="botconversa_webhook" class="form-label">Webhook</label>
                                <input type="text" id="botconversa_webhook" name="botconversa_webhook" class="form-control" placeholder="digite aqui a API do manychat" value="{{Auth::user()->botconversa_webhook}}">
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-secondary" type="submit">Alterar</button>
                            </div>
                        </div>
                    </div>
                    
                    
                </for>
            </div>
        </div>
                          
    </div>
@endsection

@section('scripts')
<script type="text/javascript">
    /**MODAL FOR ALTERAR O DOMINIO**/
    document.addEventListener('DOMContentLoaded', function() {
        var dominio_digitar = document.getElementById('dominio_externo');
        

        dominio_digitar.addEventListener('input', function() {
            var value = dominio_digitar.value;

            // Remover caracteres não permitidos
            var sanitizedValue = value.replace(/[^a-z0-9.]/g, '');

            if (value !== sanitizedValue) {
                $('#alert_dominio_proprio_feedback').html('<div class="alert alert-danger"><strong>Caracteres não permitidos!</strong> Digite tudo minusculo sem espaço, sem / ou caracteres especiais.</div>');
            } else {
                $('#alert_dominio_proprio_feedback').html('');
            }

            // Atualizar o valor do campo de entrada
            dominio_digitar.value = sanitizedValue;
        });
    });


    $(document).ready(function() {
        $('#btn_testar_pixel').on('click', function() {
            // Pegando os valores dos campos
            var metaPixelId = $('#meta_pixel_id').val();
            var metaPixelApi = $('#meta_pixel_api').val();
            var metaPixelEventCode = $('#meta_pixel_eventcode').val();

            // Exemplo de envio de uma requisição AJAX
            $.ajax({
                url: '{{route('afiliado_testar_pixel')}}', // Altere para a URL do seu backend que irá processar a requisição
                method: 'POST',
                data: {
                    meta_pixel_id: metaPixelId,
                    meta_pixel_api: metaPixelApi,
                    meta_pixel_eventcode: metaPixelEventCode,
                    _token: '{{ csrf_token() }}' // Inclua o token CSRF para segurança
                },
                success: function(response) {
                    // Se necessário, parse a resposta novamente
                    var parsedResponse = typeof response === 'string' ? JSON.parse(response) : response;

                    if (parsedResponse.original && parsedResponse.original.error) {
                        console.log(parsedResponse);
                        $("#alert_teste_pixel").html(
                            "<div class='alert alert-danger'><strong><h4>Resposta da META API.</h4>" + parsedResponse.original.error + "</strong> <br>Caso necessário, traduza a mensagem. </div>"
                        );
                    } else {
                        console.log(parsedResponse);
                        $("#alert_teste_pixel").html(
                            "<div class='alert alert-success'><strong>Pixel funcionando corretamente</strong></div>"
                        );
                    }
                },
                error: function(xhr) {
                    console.log('Ocorreu um erro: ' + xhr.responseText);
                }



            });
        });
    });
</script>

@endsection