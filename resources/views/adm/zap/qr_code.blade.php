@extends('adm.html_base')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="d-flex align-items-center">Zap Automático <a class=" ms-1" href="https://youtu.be/xVXgTvO19bc" target="_blank"><i class="ri-question-fill font-22 text-primary"></i></a></h4>
                <h5>Não sabe com funciona esta ferramenta? Então clique <a href="https://youtu.be/xVXgTvO19bc" target="_blank">AQUI</a> para assistir o vídeo</h5>
            </div>
            <div class="card-body">
                @if(!$instacia)
                <form action="{{route('criar_instancia')}}" method="POST">
                    @csrf
                    <div class="card border mb-3 ">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="proxyHost" class="form-label">Proxy Host</label>
                                <input type="text" id="proxyHost" name="proxyHost" class="form-control" required>
                                <input type="hidden" id="instanceName" name="instanceName" class="form-control" value="{{$instanceName}}">
                            </div>
                            <div class="mb-3">
                                <label for="proxyPort" class="form-label">Proxy Port</label>
                                <input type="text" id="proxyPort" name="proxyPort" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="proxyUsername" class="form-label">Proxy Username</label>
                                <input type="text" id="proxyUsername" name="proxyUsername" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="proxyPassword" class="form-label">Proxy Password</label>
                                <input type="text" id="proxyPassword" name="proxyPassword" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-secondary" type="submit">Cadastrar</button>
                            </div>
                        </div>
                    </div>
                </form>
                @else
                <div class="row">
                    <div class="col-lg-6">
                        @if(!$status)
                            <div class="card cta-box bg-warning text-white">
                                <a href="#" id="conectar" class="text-white">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start align-items-center">
                                            <div class="w-100 overflow-hidden">
                                                <h4>Você está desconectado</h2>
                                                <h3 class="m-0 fw-normal cta-box-title text-reset">
                                                    <strong class="dominio_atual" style="font-size: x-large;">Clique AQUI para conectar o seu WhatsApp</strong>
                                                </h3>
                                            </div>
                                            <img class="ms-3" src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/images/svg/email-campaign.svg')}}" width="120" alt="Generic placeholder image">
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @else
                            <div class="card cta-box bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-start align-items-center">
                                        <div class="w-100 overflow-hidden">
                                            <h3 class="m-0 fw-normal cta-box-title text-reset">
                                                <strong class="dominio_atual" style="font-size: x-large;">Você está conectado!</strong>
                                            </h3>
                                        </div>
                                        <img class="ms-3" src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/images/svg/email-campaign.svg')}}" width="120" alt="Generic placeholder image">
                                    </div>
                                </div>
                            </div>

                            <div class="card border" style="">
                                <div class="card-body">
                                    <h3 class="m-0 fw-normal cta-box-title text-reset"><strong class="dominio_atual" style="font-size: x-large;">Extrator de Leads</strong></h3>
                                    <h5>Não sabe com funciona esta ferramenta? Então clique <a href="https://drive.google.com/file/d/1GZUeukUG4Ee2L_Ex4r3BfusaW3_9WRWK/view?usp=sharing" target="_blank">AQUI</a> para assistir o vídeo</h5>
                                    <form id="extrair" action="{{route('extrair_leads_grupos')}}" method="POST">
                                        @csrf
                                        <label for="grupo_id">Grupo ID</label>
                                        <div class="d-flex align-itens-center">
                                            <input type="text" class="form-control w-50" id="grupo_id" placeholder="9999999999999999@g.us">
                                            <button type="submit" class="ms-2 btn btn-secondary">Extrair</button>
                                        </div>
                                    </form>                
                                </div>
                            </div>
                         @endif
                    </div>
                    <div class="col-lg-6">
                        <div class="card cta-box bg-danger text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-start align-items-center">
                                    <div class="w-100 overflow-hidden">
                                        <h3 class="m-0 fw-normal cta-box-title text-reset">Caso tenha algum problema no envio ou de conexão, clique <a class="text-white" href="{{route('instance.reiniciar')}}" ><strong>AQUI</strong></a> para resetar a instância</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif    
            </div>
        </div>
    </div>
    
</div>
<div class="modal fade" id="modal_qr_code" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center" id="h4_modal_qr_code">Escanei o código QR pelo seu WhatsApp de atendimento</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <h3 id="h3_qr_code">Aguarde...</h3>
                <canvas id="qrCodeCanvas"></canvas>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
    
    
@endsection

@section('head')




@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>

 <script>

@if($status)
    $(document).ready(function() {
        $('#extrair').on('submit', function(e) {
            e.preventDefault(); // Impede a submissão normal do formulário

            // Obter o valor do campo grupo_id
            var grupoId = $('#grupo_id').val();

            aviso('Estamos extraindo os leads. Veja no menu lateral > Leads ');

            $.ajax({
                url: "{{ route('extrair_leads_grupos') }}", // URL da rota do Laravel
                method: "POST", // Método de envio
                data: {
                    _token: '{{ csrf_token() }}', // Token CSRF para segurança
                    grupo_id: grupoId
                },
                success: function(response) {
                    // Manipule a resposta de sucesso aqui
                    console.log(response);
                    //alert('Leads extraídos com sucesso!');
                    aviso('Leads extraídos com sucesso!');
                },
                error: function(xhr) {
                    // Manipule o erro aqui
                    console.error(xhr.responseText);
                    //alert('Ocorreu um erro ao tentar extrair os leads.');
                    aviso('Grupo não autorizado. Tente outro grupo.');
                    
                }
            });
        });
    });
@endif
    $(document).ready(function() {

    function fetchQRCode() {
        // Mostra o modal
        // Configura a repetição a cada 10 segundos (10000 ms)
        // Cria o intervalo e armazena o identificador
        
        
        $.ajax({
            url: "{{ route('instance.qrcode') }}", // ROTA PARA PEGAR CÓDIGO QR
            type: 'GET',
            success: function(data) {
                console.log('Resposta da API:', data);

                if(data.code){
                    var qr = new QRious({
                    element: document.getElementById('qrCodeCanvas'),
                    size: 300, // Tamanho do QR code
                    value: data.code // Valor que será codificado no QR code h3_qr_code
                    });
                    document.getElementById('h3_qr_code').style.display = 'none';
                }else{
                    
                    $('#modal_qr_code').modal('hide');
                    location.reload(true);
                }
            },
            error: function(error) {
                
                $('#modal_qr_code').modal('hide');
                location.reload(true);
            }
        });
    }

    // Executa a primeira busca ao clicar no botão "Conectar"
    $('#conectar').on('click', function(e) {
        e.preventDefault();
        $('#modal_qr_code').modal('show');
        fetchQRCode();
        const intervalId = setInterval(fetchQRCode, 10000);
    });
    
});


 </script>
@endsection