@extends('adm.html_base')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body ">
                   <div class="row">
                        <div class="d-flex flex-wrap align-items-center col-12">
                            <div class="me-1 mt-1" style="width: 230px">
                                <div class="input-group flex-nowrap">
                                    <span class="input-group-text bg-secondary text-white" id="basic-addon1">Do dia: </span>
                                    {{-- <input type="text" class="form-control" placeholder="Username" aria-label="Username"> --}}
                                    <input class="form-control border-secondary" id="example-date" type="date" name="date" style="width: 150px"  aria-describedby="basic-addon1" value="">
                                </div>
                            </div>
                            <div class="me-2 mt-1" style="width: 230px">
                                <div class="input-group flex-nowrap">
                                    <span class="input-group-text bg-secondary text-white" id="basic-addon2">Até o dia: </span>
                                    {{-- <input type="text" class="form-control" placeholder="Username" aria-label="Username"> --}}
                                    <input class="form-control border-secondary" id="date_fim" type="date" name="date_fim" style="width: 150px"  aria-describedby="basic-addon2" value="">
                                </div>
                            </div>
                            @if(request()->query('created_at') OR request()->query('buscar') OR request()->query('arquivar'))
                            <a class="btn btn-secondary text-white me-2 mt-1 " href="{{ route('painel_leads_afiliado') }}">Mostrar Todos os Leads</a>
                            @endif
                            @if(!request()->query('arquivar') )
                            <a class="btn btn-danger text-white me-2 mt-1 " href="{{ route('painel_leads_afiliado', ['arquivar=1']) }}">Mostrar Arquivados</a>
                            @endif
                        </div>
                        <div class="col-12 mt-2">
                            <form method="GET" class="mb-3 d-flex" action="{{ route('painel_leads_afiliado') }}">
                                <input type="text" name="buscar" class="form-control me-2" placeholder="Buscar por nome ou WhatsApp" value="{{ request('buscar') }}">
                                <button type="submit" class="btn btn-primary">Buscar</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
            <div id="alert-container-topo"></div>
            <!-- Paginação -->
                <div class="d-flex justify-content-center">
                    
                </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>WhatsApp</th>
                        <th>Idade</th>
                        <th>Cidade</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach($leads as $lead)
                        <tr>
                            <td>{{ $lead->nome }}</td>
                            <td>{{ $lead->whatsapp }}</td>
                            <td>{{ $lead->idade }}</td>
                            <td>{{ $lead->cidade }}</td>
                            <td>{{ $lead->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <!-- Botão Mostrar -->
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalLead{{ $lead->id }}">
                                    Mostrar
                                </button>
                                
                                @if(!request()->query('arquivar') )
                                <!-- Botão Arquivar -->
                                <a href="{{ route('leads.arquivar', $lead->id) }}" class="btn btn-sm btn-danger">
                                    <i class="bi bi-archive"></i> Arquivar
                                </a>
                                @endif
                            </td>
                        </tr>

                        <!-- Modal -->
                        <div class="modal fade" id="modalLead{{ $lead->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $lead->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalLabel{{ $lead->id }}">Dados completos de {{ $lead->nome }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Escolaridade:</strong> {{ $lead->escolaridade }}</p>
                                        <p><strong>Cursos de Interesse:</strong> {{ $lead->cursos_interesse }}</p>
                                        <p><strong>Cargos de Interesse:</strong> {{ $lead->cargos_interesse }}</p>
                                        <p><strong>Estuda Online:</strong> {{ $lead->aceita_estudar_online ? 'Sim' : 'Não' }}</p>
                                        <p><strong>Pode pagar inscrição:</strong> {{ $lead->pode_pagar_inscricao ? 'Sim' : 'Não' }}</p>
                                        <p><strong>Perdeu vaga:</strong> {{ $lead->perdeu_vaga }}</p>
                                        <p><strong>Motivação:</strong> {{ $lead->motivacao }}</p>
                                        <p><strong>Compartilhar dados:</strong> {{ $lead->compartilhar_dados ? 'Sim' : 'Não' }}</p>
                                        <p><strong>Melhor horário:</strong> {{ $lead->melhor_horario }}</p>
                                        <p><strong>Preferência de contato:</strong> {{ $lead->preferencia_contato }}</p>
                                        <p><strong>Origem:</strong> {{ $lead->origem }}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>

            
        </div>
    </div>
   
                                                

@endsection

@section('head')

  
@endsection

@section('scripts')
    
  
    
    <script>

            //ESCOLHER A DATA INÍCIO
            document.getElementById('example-date').addEventListener('change', function() {
                let selectedDate = this.value;

                // Capturar a URL base
                let baseUrl = "{{ route('painel_leads_afiliado') }}";
                
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
                let baseUrl = "{{ route('painel_leads_afiliado') }}";
                
                // Obter a query string atual
                let queryParams = new URLSearchParams(window.location.search);

                // Atualizar ou adicionar o parâmetro created_at com a data selecionada
                queryParams.set('date_fim', selectedDate);

                // Construir a nova URL com os parâmetros atualizados
                let newUrl = `${baseUrl}?${queryParams.toString()}`;

                // Redirecionar para a nova URL
                window.location.href = newUrl;
            });

    </script>
@endsection