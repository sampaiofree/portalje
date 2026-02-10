@extends('adm.html_base')

@section('titulo_pagina', 'Gerenciador de Leads')

@section('head')
    {{-- DataTables CSS --}}
    <link href="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <style>
        .info-card { background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: none; }
        .form-control-sm { height: calc(1.5em + .5rem + 2px); padding: .25rem .5rem; font-size: .875rem; }
        .btn-sm { padding: .25rem .5rem; font-size: .875rem; }
        .table-hover tbody tr:hover { background-color: #f8f9fa; cursor: pointer; }
        .table thead th { text-transform: uppercase; font-size: 0.8rem; color: #6c757d; }
        .status-badge { padding: 0.3em 0.6em; font-size: 0.75rem; border-radius: 0.25rem; }
        .actions-bar { display: none; }
        .actions-bar.visible { display: flex; }
        /* Estilo para a linha do modal */
        .detail-row { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f1f3f5; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #6c757d; }
        .detail-value { font-weight: 500; color: #343a40; }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    

    <div class="row">
        <div class="col-12">
            <div class="info-card">
                <!-- CABEÇALHO COM FILTROS E BUSCA -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div class="d-flex flex-wrap gap-2">
                        {{-- Filtro de Origem --}}
                        <div class="btn-group">
                            <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-filter-3-line me-1"></i> Origem: {{$btn_leads_portal_hotmart}}</button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('hotmart_leads', ['version' => null]) }}">Leads do Portal</a>
                                <a class="dropdown-item" href="{{ route('hotmart_leads', ['version' => '2.0.0']) }}">Leads da Hotmart</a>
                                <a class="dropdown-item" href="{{ route('hotmart_leads', ['version' => 'Grupo_WhatsApp']) }}">Leads de Grupos</a>
                            </div>
                        </div>
                        {{-- Filtro de Status --}}
                        @if($btn_leads_portal_hotmart=='Leads da Hotmart')
                        <div class="btn-group">
                            <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Status: {{$btn_status}}</button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['purchase_status' => 'WAITING_PAYMENT'])) }}">Aguardando Pagamento</a>
                                <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['purchase_status' => 'CANCELLED'])) }}">Cartão Recusado</a>
                                <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['purchase_status' => 'EXPIRED'])) }}">Pagamentos Expirados</a>
                                <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['purchase_status' => 'APPROVED'])) }}">Vendas</a>
                                <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['purchase_status' => ''])) }}">Todos</a>
                            </div>
                        </div>
                        @endif
                        {{-- Filtro de Atendimento --}}
                        <div class="btn-group">
                            <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Atendimento: {{$btn_atendimento}}</button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['atendimento' => 'aguardando'])) }}">Aguardando</a>
                                <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['atendimento' => '1'])) }}">1º Atend.</a>
                                <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['atendimento' => '2'])) }}">2º Atend.</a>
                                <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['atendimento' => '3'])) }}">3º Atend.</a>
                                <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['atendimento' => 'arquivado'])) }}">Arquivados</a>
                                <a class="dropdown-item" href="{{ route('hotmart_leads', array_merge(request()->query(), ['atendimento' => ''])) }}">Todos</a>
                            </div>
                        </div>
                        {{-- Filtro de Data --}}
                        <div class="input-group input-group-sm" style="width: 280px;">
                            <input class="form-control" id="date_range_picker" type="text" name="daterange">
                            <button class="btn btn-light" id="clear_date_filter" title="Limpar Filtro de Data"><i class="ri-close-line"></i></button>
                        </div>
                    </div>
                    {{-- Campo de Busca Rápida --}}
                    <div class="mt-2 mt-md-0">
                        <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Buscar por nome, email ou telefone..." style="width: 250px;">
                    </div>
                </div>

                <!-- BARRA DE AÇÕES EM LOTE -->
                <div id="actionsBar" class="actions-bar align-items-center justify-content-between p-2 mb-3 rounded" style="background-color: #eef2f7;">
                    <div>
                        <span id="selectionCount" class="fw-bold me-3">0 selecionados</span>
                        <a id="btnWhatsapp" href="#" class="btn btn-success btn-sm"><i class="ri-whatsapp-line me-1"></i> Abrir no WhatsApp</a>
                    </div>
                    <form id="form_atendimento" class="d-flex align-items-center gap-2">
                        @csrf
                        <select class="form-select form-select-sm" name="select_atendimento" id="select_atendimento" style="width: 150px;">
                            <option value="1">Marcar 1º Atend.</option>
                            <option value="2">Marcar 2º Atend.</option>
                            <option value="3">Marcar 3º Atend.</option>
                            <option value="arquivado">Arquivar</option>
                        </select>
                        <button type="submit" class="btn btn-info btn-sm">Aplicar</button>
                    </form>
                </div>

                <!-- TABELA DE LEADS -->
                <div class="table-responsive">
                    <table id="table_leads" class="table table-hover dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th style="width: 20px;"><input type="checkbox" class="form-check-input" id="select_all"></th>
                                <th>Lead</th>
                                <th>Contato</th>
                                <th>Origem / Status</th>
                                <th>Atendimento</th>
                                <th class="text-center">Ações Rápidas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hotmart_leads as $lead)
                                <tr class="lead-row" data-bs-toggle="modal" data-bs-target="#leadDetailModal" data-lead-details="{{ json_encode($lead) }}">
                                    <td><input type="checkbox" class="form-check-input check_item" data-id="{{ $lead->id }}" onclick="event.stopPropagation();"></td>
                                    <td>
                                        <strong>{{ $lead->buyer_name }}</strong><br>
                                        <small class="text-muted">{{ $lead->buyer_email }}</small>
                                    </td>
                                    <td>{{ $lead->buyer_checkout_phone }}</td>
                                    {{-- Trecho corrigido da coluna "Origem / Status" dentro do @foreach --}}
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $lead->product_name }}</span><br>
                                        @php
                                            $status = strtoupper($lead->purchase_status);
                                            if ($status == 'WAITING_PAYMENT' || $status == 'BILLET_PRINTED') {
                                                $badgeClass = 'bg-warning-lighten text-warning-darken';
                                                $text = 'Aguard. Pag.';
                                            } elseif ($status == 'APPROVED' || $status == 'COMPLETED') {
                                                $badgeClass = 'bg-success-lighten text-success-darken';
                                                $text = 'Venda';
                                            } elseif ($status == 'EXPIRED') {
                                                $badgeClass = 'bg-danger-lighten text-danger-darken';
                                                $text = 'Expirado';
                                            } else {
                                                $badgeClass = 'bg-secondary-lighten text-secondary-darken';
                                                $text = $status ?: 'N/A';
                                            }
                                        @endphp
                                        <span class="status-badge {{ $badgeClass }}">{{ $text }}</span>
                                    </td>
                                    <td>
                                        @if($lead->atendimento == 'arquivado')
                                            <span class="badge bg-secondary">Arquivado</span>
                                        @elseif($lead->atendimento)
                                            <span class="badge bg-info">{{ $lead->atendimento }}º Atend.</span><br>
                                            <small class="text-muted" title="{{ \Carbon\Carbon::parse($lead->updated_at)->format('d/m/Y H:i:s') }}">
                                                {{ \Carbon\Carbon::parse($lead->updated_at)->diffForHumans() }}
                                            </small>
                                        @else
                                            <span class="badge bg-light text-dark">Aguardando</span>
                                            <small class="text-muted" title="{{ \Carbon\Carbon::parse($lead->updated_at)->format('d/m/Y H:i:s') }}">
                                                {{ \Carbon\Carbon::parse($lead->updated_at)->diffForHumans() }}
                                            </small>
                                            <small class="text-muted" title="{{ \Carbon\Carbon::parse($lead->updated_at)->format('d/m/Y H:i:s') }}">
                                              | {{ \Carbon\Carbon::parse($lead->updated_at)->format('d/m/Y H:i') }}
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-center" onclick="event.stopPropagation();">
                                        @php
                                            // Adiciona "55" se não começar com ele e remove não-números
                                            $phone = preg_replace('/\D/', '', $lead->buyer_checkout_phone);
                                            if (substr($phone, 0, 2) !== '55') {
                                                $phone = '55' . $phone;
                                            }
                                        @endphp
                                        <a href="https://wa.me/{{ $phone }}" target="_blank" class="btn btn-success btn-sm" title="Conversar no WhatsApp"><i class="ri-whatsapp-line"></i></a>
                                        @if($lead->purchase_status == 'WAITING_PAYMENT' && $lead->purchase_payment_billet_url)
                                            <a href="{{ $lead->purchase_payment_billet_url }}" target="_blank" class="btn btn-primary btn-sm" title="Ver Boleto"><i class="ri-barcode-line"></i></a>
                                        @elseif($lead->purchase_status == 'WAITING_PAYMENT' && $lead->purchase_payment_pix_qrcode)
                                            <a href="https://pay.hotmart.com/thanks?transactionReference={{$lead->transaction}}" target="_blank" class="btn btn-primary btn-sm" title="Ver PIX"><i class="ri-qr-code-line"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $hotmart_leads->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ### INÍCIO DO NOVO CÓDIGO ### -->

<!-- Modal de Detalhes do Lead -->
<div class="modal fade" id="leadDetailModal" tabindex="-1" aria-labelledby="leadDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leadDetailModalLabel">Detalhes do Lead</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-uppercase text-muted">Informações Pessoais</h6>
                        <div class="detail-row"><span class="detail-label">Nome</span> <span class="detail-value" id="modal_buyer_name"></span></div>
                        <div class="detail-row"><span class="detail-label">E-mail</span> <span class="detail-value" id="modal_buyer_email"></span></div>
                        <div class="detail-row"><span class="detail-label">Telefone</span> <span class="detail-value" id="modal_buyer_checkout_phone"></span></div>
                        <div class="detail-row"><span class="detail-label">Documento (CPF)</span> <span class="detail-value" id="modal_buyer_document"></span></div>
                    </div>
                    <div class="col-md-6 mt-4 mt-md-0">
                        <h6 class="text-uppercase text-muted">Informações da Compra</h6>
                        <div class="detail-row"><span class="detail-label">Produto</span> <span class="detail-value" id="modal_product_name"></span></div>
                        <div class="detail-row"><span class="detail-label">Status</span> <span class="detail-value" id="modal_purchase_status"></span></div>
                        <div class="detail-row"><span class="detail-label">Forma de Pagamento</span> <span class="detail-value" id="modal_purchase_payment_type"></span></div>
                        <div class="detail-row"><span class="detail-label">Código (HP)</span> <span class="detail-value" id="modal_transaction"></span></div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                         <h6 class="text-uppercase text-muted">Histórico</h6>
                         <div class="detail-row"><span class="detail-label">Data de Criação</span> <span class="detail-value" id="modal_created_at"></span></div>
                         <div class="detail-row"><span class="detail-label">Última Atualização</span> <span class="detail-value" id="modal_updated_at"></span></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<!-- ### FIM DO NOVO CÓDIGO ### -->

@endsection

@section('scripts')
    {{-- DataTables JS --}}
    <script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js')}}"></script>
    <script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
    
    {{-- DateRangePicker JS --}}
    <script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/daterangepicker/moment.min.js')}}"></script>
    <script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/daterangepicker/daterangepicker.js')}}"></script>

    <script>
        $(document).ready(function() {
            // Inicializa a DataTable sem os controles padrão
            var table = $('#table_leads').DataTable({
                paging: false,
                searching: false, // Desativa a busca padrão do DataTable
                info: false,
                ordering: true,
                order: [[0, 'desc']], // Ordena pela primeira coluna (data de cadastro implícita)
                responsive: true,
                language: { url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Portuguese-Brasil.json' }
            });

            // Lógica da Busca Rápida
            $('#searchInput').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Lógica do DateRangePicker
            let startDate = "{{ $btn_data ?: '' }}";
            let endDate = "{{ $btn_fim ?: '' }}";

            $('#date_range_picker').daterangepicker({
                startDate: startDate ? moment(startDate) : moment().subtract(29, 'days'),
                endDate: endDate ? moment(endDate) : moment(),
                ranges: {
                   'Hoje': [moment(), moment()],
                   'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                   'Últimos 7 Dias': [moment().subtract(6, 'days'), moment()],
                   'Últimos 30 Dias': [moment().subtract(29, 'days'), moment()],
                   'Este Mês': [moment().startOf('month'), moment().endOf('month')],
                   'Mês Passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                locale: { "format": "DD/MM/YYYY", "separator": " - ", /* ... outras traduções ... */ }
            }, function(start, end) {
                let queryParams = new URLSearchParams(window.location.search);
                queryParams.set('created_at', start.format('YYYY-MM-DD'));
                queryParams.set('date_fim', end.format('YYYY-MM-DD'));
                window.location.href = "{{ route('hotmart_leads') }}?" + queryParams.toString();
            });
            
            // Se as datas não estiverem definidas, limpa o campo
            if (!startDate && !endDate) {
                 $('#date_range_picker').val('');
            }
            
            // Limpar filtro de data
            $('#clear_date_filter').on('click', function() {
                 let queryParams = new URLSearchParams(window.location.search);
                 queryParams.delete('created_at');
                 queryParams.delete('date_fim');
                 window.location.href = "{{ route('hotmart_leads') }}?" + queryParams.toString();
            });

            // Lógica de Seleção de Linhas
            const actionsBar = $('#actionsBar');
            const selectionCount = $('#selectionCount');
            const btnWhatsapp = $('#btnWhatsapp');

            function updateActionsBar() {
                const checkedCount = $('.check_item:checked').length;
                selectionCount.text(checkedCount + ' selecionado' + (checkedCount !== 1 ? 's' : ''));
                if (checkedCount > 0) {
                    actionsBar.addClass('visible');
                } else {
                    actionsBar.removeClass('visible');
                }
                
                // Atualiza link do WhatsApp (para abrir múltiplos contatos)
                if (checkedCount > 0) {
                    let numbers = [];
                    $('.check_item:checked').each(function() {
                        const phone = $(this).closest('tr').find('td:nth-child(3)').text().replace(/\D/g, '');
                        if(phone) numbers.push(phone);
                    });
                    // Gerar links individuais, já que o WhatsApp Web não abre múltiplos contatos de uma vez de forma nativa
                    let whatsappUrl = `https://wa.me/${numbers[0]}`; // Pega o primeiro número
                    btnWhatsapp.attr('href', whatsappUrl);
                    if(numbers.length > 1) {
                        btnWhatsapp.attr('title', 'Abre o WhatsApp para o primeiro contato selecionado. Para os demais, use os botões individuais.');
                    } else {
                         btnWhatsapp.attr('title', 'Abrir conversa no WhatsApp');
                    }
                }
            }

            $('#select_all').on('change', function() {
                $('.check_item').prop('checked', $(this).is(':checked'));
                updateActionsBar();
            });

            $('#table_leads tbody').on('change', '.check_item', function() {
                updateActionsBar();
            });
            
            // Lógica para alterar atendimento em lote
            $('#form_atendimento').on('submit', function(e) {
                e.preventDefault();
                let selectedIds = [];
                $('.check_item:checked').each(function() {
                    selectedIds.push($(this).data('id'));
                });

                if (selectedIds.length === 0) {
                    alert('Por favor, selecione ao menos um lead.');
                    return;
                }
                
                let formData = $(this).serialize() + '&selected_ids=' + JSON.stringify(selectedIds);

                $.ajax({
                    url: "{{ route('alterar_atendimento') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        //alert('Atendimento alterado com sucesso!');
                        window.location.reload();
                    },
                    error: function() {
                        alert('Ocorreu um erro ao alterar o atendimento.');
                    }
                });
            });
        });
    </script>
    
    {{-- ### INÍCIO DO NOVO CÓDIGO JS ### --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const leadDetailModal = document.getElementById('leadDetailModal');
            
            leadDetailModal.addEventListener('show.bs.modal', function (event) {
                const row = event.relatedTarget; // A linha <tr> que acionou o modal
                const leadDataString = row.getAttribute('data-lead-details');
                
                if (!leadDataString) return;
                
                const leadData = JSON.parse(leadDataString);

                // Função para formatar data
                const formatDate = (dateString) => {
                    if (!dateString) return 'N/A';
                    return new Date(dateString).toLocaleString('pt-BR', {
                        day: '2-digit', month: '2-digit', year: 'numeric',
                        hour: '2-digit', minute: '2-digit'
                    });
                };
                
                // Preenche os campos do modal
                document.getElementById('modal_buyer_name').textContent = leadData.buyer_name || 'N/A';
                document.getElementById('modal_buyer_email').textContent = leadData.buyer_email || 'N/A';
                document.getElementById('modal_buyer_checkout_phone').textContent = leadData.buyer_checkout_phone || 'N/A';
                document.getElementById('modal_buyer_document').textContent = leadData.buyer_document || 'N/A';
                document.getElementById('modal_product_name').textContent = leadData.product_name || 'N/A';
                document.getElementById('modal_purchase_status').textContent = leadData.purchase_status || 'N/A';
                document.getElementById('modal_purchase_payment_type').textContent = leadData.purchase_payment_type || 'N/A';
                document.getElementById('modal_transaction').textContent = leadData.transaction || 'N/A';
                document.getElementById('modal_created_at').textContent = formatDate(leadData.created_at);
                document.getElementById('modal_updated_at').textContent = formatDate(leadData.updated_at);
            });

            // Adiciona evento de clique para os checkboxes, parando a propagação
            document.querySelectorAll('.check_item').forEach(checkbox => {
                checkbox.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
            });
            document.querySelectorAll('.btn').forEach(btn => {
                btn.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
            });
        });
    </script>
    {{-- ### FIM DO NOVO CÓDIGO JS ### --}}
@endsection