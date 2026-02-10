@extends('adm.html_base')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"> {{$btn_leads_portal_hotmart}} <span class="caret"></span> </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('hotmart_leads', ['version' => null]) }}">Leads do Portal</a>
                        <a class="dropdown-item" href="{{ route('hotmart_leads', ['version' => '2.0.0']) }}">Leads da Hotmart</a>
                    </div>
                </div>
                @if($btn_leads_portal_hotmart=='Leads da Hotmart')
                <div class="btn-group ms-2">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"> {{$btn_status}} <span class="caret"></span> </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['purchase_status' => 'WAITING_PAYMENT'])) }}">Aguardando Pagamento PIX e BOLETO</a>
                        <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['purchase_status' => 'CANCELLED'])) }}">Cartão Recusado</a>
                        <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['purchase_status' => 'EXPIRED'])) }}">Pagamentos Expirados</a>
                        <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['purchase_status' => 'APPROVED'])) }}">Vendas</a>
                        <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['purchase_status' => ''])) }}">Mostrar TODOS</a>
                    </div>
                </div>
                @endif
                <div class="btn-group ms-2">
                    <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"> {{$btn_atendimento}} <span class="caret"></span> </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['atendimento' => 'aguardando'])) }}">Aguardando Atendimento</a>
                        <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['atendimento' => '1'])) }}">1º Atendimento</a>
                        <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['atendimento' => '2'])) }}">2º Atendimento</a>
                        <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['atendimento' => '3'])) }}">3º Atendimento</a>
                        <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['atendimento' => '4'])) }}">4º Atendimento</a>
                        <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['atendimento' => '5'])) }}">5º Atendimento</a>
                        <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['atendimento' => 'arquivado'])) }}">Arquivados</a>
                        <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['atendimento' => ''])) }}">Mostrar todos</a>
                    </div>
                </div>
                <div class="ms-2" style="width: 230px">
                    <div class="input-group flex-nowrap">
                        <span class="input-group-text bg-secondary text-white" id="basic-addon1">Do dia: </span>
                        {{-- <input type="text" class="form-control" placeholder="Username" aria-label="Username"> --}}
                        <input class="form-control border-secondary" id="example-date" type="date" name="date" style="width: 150px"  aria-describedby="basic-addon1" value="{{ $btn_data }}">
                    </div>
                </div>
                <div class="ms-1" style="width: 230px">
                    <div class="input-group flex-nowrap">
                        <span class="input-group-text bg-secondary text-white" id="basic-addon2">Até o dia: </span>
                        {{-- <input type="text" class="form-control" placeholder="Username" aria-label="Username"> --}}
                        <input class="form-control border-secondary" id="date_fim" type="date" name="date_fim" style="width: 150px"  aria-describedby="basic-addon2" value="{{ $btn_fim }}">
                    </div>
                </div>
                @if($btn_data)
                <a class="btn btn-secondary text-white ms-1 " href="{{ route('hotmart_leads', array_merge(request()->query(), ['created_at' => 'todos', 'date_fim' => ''])) }}">Mostrar Todos os Leads</a>
                @endif
            </div>
            <div class="card-body ">
                <h4 class="d-flex align-items-center">Selecione os contatos para enviar mensagens ou alterar o atendimento. <a class=" ms-1" href="https://youtu.be/QzSuI41UYfE" target="_blank"><i class="ri-question-fill font-22 text-primary"></i></a></h4>
                <div class="d-flex">
                    <button type="button" class="btn btn-success" onclick="getSelectedRows()"><i class="ri-whatsapp-line  me-1"></i> <span>Enviar WhatsApp</span> </button>
                    <a class=" ms-1" href="https://youtu.be/xVXgTvO19bc" target="_blank"><i class="ri-question-fill font-22 text-primary"></i></a>
                    <form id="form_atendimento" class="">
                        @csrf
                        <div class="d-flex">
                            <select class="ms-3 form-select form-control border-info" name="select_atendimento" id="select_atendimento" style="width: 120px">
                                <option value="1">1º Atend.</option>
                                <option value="2">2º Atend.</option>
                                <option value="3">3º Atend.</option>
                                <option value="4">4º Atend.</option>
                                <option value="5">5º Atend.</option>
                                <option value="arquivado">Arquivar</option>
                            </select>
                            <button type="submit" class="btn btn-info" style="width: 160px">Alterar atendimento</button>
                        </div>
                    </form>
                </div>
                
            </div>
        </div>
        <div id="alert-container-topo"></div>
       <!-- Paginação do Laravel -->
        <div class="d-flex justify-content-center">
            {{ $hotmart_leads->links('pagination::bootstrap-4') }}
        </div>
        <table id="table_leads" class="table table-striped dt-responsive nowrap w-100">            
            <thead>
                <tr>
                    
                   
                    <th>Cadastro</th>
                    <th class="hidden">id</th>
                    <th class="td_check"><input type="checkbox" class="me-1 form-check-input" id="select_all"> Marcar Todos</th>                   
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>Curso</th>
                    <th>Origem</th>
                    <th>Forma de pagamento</th>
                    <th>Email</th>
                    <th>CPF</th>
                    <th>Codigo HP</th>
                    <th>Atualizado</th>
                    
                    
                </tr>
            </thead>
        
        
            <tbody>
                @forEach($hotmart_leads as $hotmart_lead)
                @if(($btn_atendimento!='Arquivados' AND $hotmart_lead['atendimento']!='arquivado') OR ($btn_atendimento=='Arquivados' AND $hotmart_lead['atendimento']=='arquivado'))
                <tr>
                    
                    
                    <td>{{ \Carbon\Carbon::parse($hotmart_lead['created_at'])->format('d/m/Y H:i') }}</td>
                    <td class="hidden">{{$hotmart_lead['id']}}</td>
                    <td class="td_check">
                        <input type="checkbox" class="me-2 form-check-input check_item" id="check_{{$hotmart_lead['id']}}">
                        {{$hotmart_lead['atendimento']}}@if($hotmart_lead['atendimento'] AND $hotmart_lead['atendimento']!='arquivado') º Atend. @elseif(!$hotmart_lead['atendimento']) Aguardando @endif
                    </td>
                    @php
                        $nome = explode(" ", $hotmart_lead['buyer_name']);
                    @endphp
                    <td>{{$nome[0]}}</td>
                    <td class="list-telefone">{{$hotmart_lead['buyer_checkout_phone']}}</td>
                    <td>{{$hotmart_lead['product_name']}}</td>
                    <td>@php
                        if(strtoupper($hotmart_lead['purchase_status'])== 'WAITING_PAYMENT'){$purchase_status = 'Aguardando';}
                        elseif(strtoupper($hotmart_lead['purchase_status'])== 'APPROVED'){$purchase_status = 'Venda';}
                        elseif(strtoupper($hotmart_lead['purchase_status'])== 'COMPLETE'){$purchase_status = 'Venda';}
                        elseif(strtoupper($hotmart_lead['purchase_status']) == 'CANCELLED' OR strtoupper($hotmart_lead['purchase_status']) == 'CANCELED'){$purchase_status = 'Cancelado';}
                        elseif(strtoupper($hotmart_lead['purchase_status'])== 'EXPIRED'){$purchase_status = 'Vencido';}
                        elseif(strtoupper($hotmart_lead['purchase_status'])== 'EXPIRED'){$purchase_status = 'Vencido';}
                        else{$purchase_status = $hotmart_lead['purchase_status'];}
                        @endphp
                        {{$purchase_status}}
                    </td> 
                    <td>
                        
                            @if($hotmart_lead['purchase_payment_billet_url']) 
                                <a target="_blank" href="{{$hotmart_lead['purchase_payment_billet_url']}}">Baixar Boleto</a> 
                            @elseif($hotmart_lead['purchase_payment_pix_qrcode']) 
                                <a class="btn btn-sm btn-info" target="_blank" href="{{$hotmart_lead['purchase_payment_pix_qrcode']}}">Pix QRCODE</a>

                                <button type="button" class="me-2 btn btn-sm btn-primary" data-bs-container="body" data-bs-toggle="popover" data-bs-placement="top" data-bs-content="{{$hotmart_lead['purchase_payment_pix_code']}}" title="">
                                    Pix Copia e Cola
                                </button>
                            @else
                                {{$hotmart_lead['purchase_payment_type']}}
                            @endif
                            
                        
                    </td>   
                    <td class="list-email">{{$hotmart_lead['buyer_email']}}</td>
                    <td>{{$hotmart_lead['buyer_document']}}</td>
                    <td>{{$hotmart_lead['transaction']}}</td>
                    <td>{{$hotmart_lead['updated_at']}}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        <!-- Paginação do Laravel -->
        <div class="d-flex justify-content-center">
            {{ $hotmart_leads->links('pagination::bootstrap-4') }}
        </div>
        
    </div>
</div>
<div id="modal_whatsapp" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title d-inline">Enviar mensagem pelo Zap Automático</h4>
                
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <p>Certifique-se que seu WhatsApp está conectado. Veja no Menu > Ferramentas > Zap Automático</p>
                <h5>Use <span class="bg-secondary text-white rounded-3" style="font-size: padding: 0.5%;">{nome}</span> para se referir ao nome do lead e <span class="bg-secondary text-white rounded-3" style="font-size: padding: 0.5%;">{curso}</span> para se referir ao nome do curso.</p>
                <form id="form_envio_mensagem" enctype="multipart/form-data">
                    @csrf
                    <div id="alert-container"></div>
                    <div class="mb-3">
                        <label for="texto_padrao" class="form-label">Texto com até 500 caracteres</label>
                        <textarea name="texto_padrao" class="form-control" id="texto_padrao" rows="5"></textarea>
                    </div>
                    <div class="mb-1">
                        <label for="imagem" class="form-label">Imagem de até 750kb</label>
                        <input type="file" id="imagem" name="imagem" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="imagem_legenda" class="form-label">Legenda da Imagem</label>
                        <textarea name="imagem_legenda" class="form-control" id="imagem_legenda" rows="5"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="audio" class="form-label">Áudio - Mp3 de até 750kb</label>
                        <input type="file" id="audio" name="audio" class="form-control">
                    </div>
                    <input type="hidden" id="selectedRowsData" name="selectedRowsData"> <!--PEGAR OS DADOS DAS LINHAS // ID, NOME , TELEFONE E CURSO-->
                    <div class="mb-3">
                        <label>Intervalo entre mensagens</label>
                        <select name="intervalo" id="intervalo" class="form-select mb-3">
                            <option value="30">30 segundos</option>
                            <option value="40">40 segundos</option>
                            <option value="50">50 segundos</option>
                            <option value="60">60 segundos</option>
                            <option value="70">70 segundos</option>
                            <option value="80">80 segundos</option>
                            <option value="90">90 segundos</option>
                            <option value="120">120 segundos</option>
                        </select> 
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
                                                

@endsection

@section('head')

    <link href="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/datatables.net-select-bs5/css/select.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />

    <style>
        .hidden{display: none; visibility: hidden;}
        .badge{font-size: large; margin-left: 5px}
        /*.td_check{font-size: 1%;}*/
        /* .sorting{max-width: 200px;} */
    </style>
  
@endsection

@section('scripts')
    
    <script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js')}}"></script>
    <script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js')}}"></script>
    
    <script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/datatables.net-select/js/dataTables.select.min.js')}}"></script>
  
    
    <script>
            $('#table_leads').DataTable( {
                paging: false,
                //order: [[2, 'asc']],
                order: false,
                columnDefs: [
                    { orderable: false, targets: 0 } // Desativa a ordenação na coluna 0
                ]
            } );

            //SELECIONAR TODAS AS LINHAS
            document.getElementById('select_all').addEventListener('change', function() {
                // Verifica o estado do checkbox "Selecionar Todos"
                let isChecked = this.checked;
                
                // Seleciona todos os checkboxes com a classe 'check_item'
                let checkboxes = document.querySelectorAll('.check_item');

                // Altera o estado de todos os checkboxes
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = isChecked;
                });
            });

            //ESCOLHER A DATA INÍCIO
            document.getElementById('example-date').addEventListener('change', function() {
                let selectedDate = this.value;

                // Capturar a URL base
                let baseUrl = "{{ route('hotmart_leads') }}";
                
                // Obter a query string atual
                let queryParams = new URLSearchParams(window.location.search);

                // Atualizar ou adicionar o parâmetro created_at com a data selecionada
                queryParams.set('created_at', selectedDate);

                // Construir a nova URL com os parâmetros atualizados
                let newUrl = `${baseUrl}?${queryParams.toString()}`;

                // Redirecionar para a nova URL
                window.location.href = newUrl;
            });

            //ESCOLHER A DATA INÍCIO
            document.getElementById('date_fim').addEventListener('change', function() {
                let selectedDate = this.value;

                // Capturar a URL base
                let baseUrl = "{{ route('hotmart_leads') }}";
                
                // Obter a query string atual
                let queryParams = new URLSearchParams(window.location.search);

                // Atualizar ou adicionar o parâmetro created_at com a data selecionada
                queryParams.set('date_fim', selectedDate);

                // Construir a nova URL com os parâmetros atualizados
                let newUrl = `${baseUrl}?${queryParams.toString()}`;

                // Redirecionar para a nova URL
                window.location.href = newUrl;
            });

            function dados_select(){
                var table = document.getElementById("table_leads");
                var checkboxes = table.querySelectorAll(".form-check-input:checked");
                var selectedData = [];

                checkboxes.forEach(function(checkbox) {
                    var row = checkbox.closest("tr");
                    var rowData = {
                        id: row.cells[1].innerText.trim(),
                        nome: row.cells[3].innerText.trim(),
                        telefone: row.cells[4].innerText.trim(),
                        curso: row.cells[5].innerText.trim(),
                    };
                    selectedData.push(rowData);
                });

                // Armazene os dados selecionados no modal usando um campo oculto
                document.getElementById('selectedRowsData').value = JSON.stringify(selectedData);

                if (!selectedData || selectedData.length === 0){
                    var alertHtml = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                Selecione pelo meno um contato pela segunda coluna
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        // Insere o alerta no contêiner de alertas
                        $('#alert-container-topo').html(alertHtml);
                        return false;
                }else{
                    return selectedData; 
                }

                
            }

            function getSelectedRows() {
                
                var selectedData = dados_select();

                if(!selectedData){return; }

                $('#modal_whatsapp').modal('show');
                console.log(selectedData);
            }

            $(document).ready(function() {
                $('#form_atendimento').on('submit', function(e) {
                    e.preventDefault(); // Evita o envio padrão do formulário

                    var formData = new FormData(this);

                    // Adiciona as linhas selecionadas ao FormData
                    var selectedData = dados_select();
                    if(!selectedData){return;}

                    //var selectedRowsData = JSON.parse($('#selectedRowsData').val());

                    formData.append('selectedRowsData', JSON.stringify(selectedData));

                    // Para depuração, exibe o conteúdo do FormData
                    for (var pair of formData.entries()) {
                        console.log(pair[0]+ ': ' + pair[1]);
                    }

                    $.ajax({
                        url: "{{ route('alterar_atendimento') }}", // Substitua pela rota correta
                        type: 'POST',
                        data: formData,
                        processData: false, // Evita que o jQuery processe os dados
                        contentType: false, // Evita que o jQuery defina o cabeçalho Content-Type
                        success: function(response) {
                            console.log('Resposta do servidor:', response);
                            window.location.reload();
                        },
                        error: function(xhr, status, error) {
                            console.error('Erro ao enviar dados: ', error);
                            console.error('Status: ', status);
                            console.error('Resposta do servidor: ', xhr.responseText);
                            // Aqui você pode manipular o erro, exibir uma mensagem de erro, etc.
                        }
                    });
                });
            });


            $(document).ready(function() {
                $('#form_envio_mensagem').on('submit', function(e) {
                    e.preventDefault(); // Evita o envio padrão do formulário

                    var imageInput = document.getElementById('imagem');
                    var file = imageInput.files[0];
                    var maxSize = 750 * 1024; // 750 KB em bytes

                    if (file && file.size > maxSize) {
                        e.preventDefault(); // Evita o envio do formulário

                        // Cria o alerta Bootstrap
                        var alertHtml = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                A imagem é muito grande. O tamanho máximo permitido é 750 KB.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        // Insere o alerta no contêiner de alertas
                        $('#alert-container').html(alertHtml);

                        return false; // Impede o envio do formulário
                    }

                    // Verifica o tipo e tamanho do arquivo de áudio
                    var audioInput = document.getElementById('audio');
                    var audio = audioInput.files[0];
                    var maxSizeaudio = 750 * 1024; // 750 KB em bytes

                    if (audio) {
                        // Verifica se o tipo de arquivo é mp3
                        if (audio.type !== 'audio/mp3' && audio.type !== 'audio/mpeg') {
                            e.preventDefault(); // Evita o envio do formulário

                            // Cria o alerta Bootstrap
                            var alertHtml = `
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    O arquivo de áudio deve ser do tipo MP3.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            `;
                            $('#alert-container').html(alertHtml);
                            return false; // Impede o envio do formulário
                        }

                        if (audio.size > maxSizeaudio) {
                            e.preventDefault(); // Evita o envio do formulário

                            // Cria o alerta Bootstrap
                            var alertHtml = `
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    O áudio é muito grande. O tamanho máximo permitido é 750 KB.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            `;
                            $('#alert-container').html(alertHtml);
                            return false; // Impede o envio do formulário
                        }
                    }


                    var textoPadrao = $('#texto_padrao').val(); //NÃO PERMITIR MAIS DE 500 CARACTERES NO TEXTO
                    if (textoPadrao.length > 500) {
                        e.preventDefault(); // Impede o envio do formulário

                        // Cria o alerta Bootstrap
                        var alertHtml = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                O texto não pode ultrapassar 500 caracteres. Atualmente você está com ${textoPadrao.length} caracteres.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        // Insere o alerta no contêiner de alertas
                        $('#alert-container').html(alertHtml);

                        return false; // Impede o envio do formulário
                    }

                    var imagemLegenda = $('#imagem_legenda').val(); //NÃO PERMITIR MAIS DE 500 CARACTERES NO TEXTO
                    if (imagemLegenda.length > 500) {
                        e.preventDefault(); // Impede o envio do formulário

                        // Cria o alerta Bootstrap
                        var alertHtml = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                O texto da legenda da imagem não pode ultrapassar 500 caracteres. Atualmente você está com ${imagemLegenda.length} caracteres.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        // Insere o alerta no contêiner de alertas
                        $('#alert-container').html(alertHtml);

                        return false; // Impede o envio do formulário
                    }

                    // Cria um objeto FormData
                    var formData = new FormData(this);

                    // Adiciona as linhas selecionadas ao FormData
                    var selectedRowsData = JSON.parse($('#selectedRowsData').val());
                    formData.append('selectedRowsData', JSON.stringify(selectedRowsData));

                    // Cria o alerta Bootstrap
                        var alertHtml = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                Mensagens enviadas com sucesso. O tempo total para enviar todas as mensagens pode variar dependendo da quatidade de contatos.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        // Insere o alerta no contêiner de alertas
                        $('#alert-container-topo').html(alertHtml);
                        $('#modal_whatsapp').modal('hide');

                    // Envia os dados via AJAX
                    $.ajax({
                        url: "{{ route('zap_enviar_mensagem') }}", // Substitua pela rota correta
                        type: 'POST',
                        data: formData,
                        processData: false, // Evita que o jQuery processe os dados
                        contentType: false, // Evita que o jQuery defina o cabeçalho Content-Type
                        success: function(response) {
                            console.log('Resposta do servidor:', response);
                            // Aqui você pode manipular a resposta, exibir uma mensagem de sucesso, etc.
                        },
                        error: function(xhr, status, error) {
                            console.error('Erro ao enviar dados:', error);
                            // Aqui você pode manipular o erro, exibir uma mensagem de erro, etc.
                        }
                    });
                });
            });




    </script>
@endsection