@extends('adm.html_base')

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Estilos para melhorar a legibilidade */
        .table th {
            white-space: nowrap;
        }
        .table td {
            vertical-align: middle;
        }
        .filter-card {
            background-color: #f8f9fa;
        }
        .stats-card {
            background-color: #eef2f6;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        {{-- Título dinâmico --}}
        <h3 class="mt-4">
            @if ($statusArquivado)
                Leads Arquivados
            @else
                Novos Leads - Aulas Gratuitas
            @endif
        </h3>
        <p class="text-muted mb-4">
            @if ($statusArquivado)
                Lista de leads que foram movidos para o arquivo.
            @else
                Lista de leads capturados através da página de cursos gratuitos.
            @endif
        </p>

        <!-- Card de Filtros e Visualização -->
        <div class="card shadow-sm mb-4 filter-card">
            <div class="card-body">
                <form method="GET" action="{{ route('novos_leads') }}" class="row g-3 align-items-end">
                    
                    {{-- Seletores de Visualização --}}
                    <div class="col-12 mb-3">
                        <h5 class="card-title">Visualizar</h5>
                        <div class="btn-group" role="group">
                            <a href="{{ route('novos_leads', ['status' => 0] + request()->except('status', 'page')) }}" class="btn btn-sm {{ !$statusArquivado ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="bi bi-inbox-fill me-1"></i> Novos Leads
                            </a>
                            <a href="{{ route('novos_leads', ['status' => 1] + request()->except('status', 'page')) }}" class="btn btn-sm {{ $statusArquivado ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="bi bi-archive-fill me-1"></i> Arquivados
                            </a>
                        </div>
                    </div>

                    {{-- Filtros de Data --}}
                    <div class="col-12"><h5 class="card-title mb-2">Filtrar por Data</h5></div>
                    <div class="col-md-4">
                        <label for="data_inicial" class="form-label">Data Inicial</label>
                        <input type="date" class="form-control" id="data_inicial" name="data_inicial" value="{{ request('data_inicial') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="data_final" class="form-label">Data Final</label>
                        <input type="date" class="form-control" id="data_final" name="data_final" value="{{ request('data_final') }}">
                    </div>
                    <div class="col-md-4">
                        {{-- Campo oculto para manter o status ao filtrar --}}
                        <input type="hidden" name="status" value="{{ $statusArquivado }}">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-funnel-fill me-1"></i> Aplicar Filtros
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabela de Leads -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                {{-- Coluna de Data muda de nome dependendo da view --}}
                                <th>
                                    @if($statusArquivado)
                                        Data de Criação
                                    @else
                                        Data de Cadastro
                                    @endif
                                </th>
                                <th>Nome</th>
                                <th>WhatsApp</th>
                                <th class="text-center">Curso</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($leads as $lead)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($lead->created_at)->format('d/m/Y H:i') }}</td>
                                    <td class="fw-bold">{{ $lead->nome }}</td>
                                    <td>{{ $lead->whatsapp }}</td>
                                    <td class="text-center">{{ $lead->curso }}</td>
                                    <td class="text-center">
                                        <!--<button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#leadDetailsModal{{ $lead->id }}">
                                            <i class="bi bi-eye"></i> Detalhes
                                        </button>-->
                                        
                                        {{-- Botão de Ação Dinâmico --}}
                                        @if ($statusArquivado)
                                            {{-- Ação para DESARQUIVAR --}}
                                            <form action="{{ route('leads.desarquivar', $lead->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja restaurar este lead?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Restaurar
                                                </button>
                                            </form>
                                        @else
                                            {{-- Ação para ARQUIVAR --}}
                                            <form action="{{ route('leads.arquivar', $lead->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja arquivar este lead?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-archive"></i> Arquivar
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        @if ($statusArquivado)
                                            Nenhum lead arquivado encontrado.
                                        @else
                                            Nenhum lead novo encontrado com os filtros aplicados.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                 <!-- Paginação -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $leads->links() }}
                </div>
            </div>
        </div>
    </div>


    <!-- Modais de Detalhes (gerados dinamicamente) -->
    @foreach ($leads as $lead)
        <div class="modal fade" id="leadDetailsModal{{ $lead->id }}" tabindex="-1" aria-labelledby="leadDetailsModalLabel{{ $lead->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="leadDetailsModalLabel{{ $lead->id }}">Detalhes do Lead: {{ $lead->nome }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        
                        {{-- Seção de Contato --}}
                        <h6><i class="bi bi-person-lines-fill me-2"></i>Informações de Contato</h6>
                        <div class="border rounded p-3 mb-3">
                            <p><strong>WhatsApp:</strong> {{ $lead->whatsapp }}</p>
                            <p><strong>Melhor horário para contato:</strong> {{ $lead->melhor_horario ?? 'Não informado' }}</p>
                            <p class="mb-0"><strong>Preferência de contato:</strong> {{ $lead->preferencia_contato ?? 'Não informado' }}</p>
                        </div>

                        {{-- Seção de Perfil --}}
                        <h6><i class="bi bi-person-vcard me-2"></i>Perfil do Lead</h6>
                        <div class="border rounded p-3 mb-3">
                            <p><strong>Idade:</strong> {{ $lead->idade }}</p>
                            <p><strong>Cidade:</strong> {{ $lead->cidade }}</p>
                            <p class="mb-0"><strong>Escolaridade:</strong> {{ $lead->escolaridade }}</p>
                        </div>
                        
                        {{-- Seção de Interesses --}}
                        <h6><i class="bi bi-lightbulb me-2"></i>Interesses</h6>
                        <div class="border rounded p-3 mb-3">
                            <p class="mb-1"><strong>Cursos já realizados:</strong></p>
                            <ul class="ps-4 mb-2">
                                @foreach (explode(',', $lead->cursos_interesse) as $curso)
                                    <li>{{ trim($curso) }}</li>
                                @endforeach
                            </ul>

                            <p class="mb-1"><strong>Áreas de Interesse:</strong></p>
                            <ul class="ps-4 mb-0">
                                @foreach (explode(',', $lead->cargos_interesse) as $cargo)
                                    <li>{{ trim($cargo) }}</li>
                                @endforeach
                            </ul>
                        </div>
                        
                        {{-- Seção de Qualificação --}}
                        <h6><i class="bi bi-card-checklist me-2"></i>Qualificação</h6>
                         <div class="border rounded p-3 mb-3">
                            <p><strong>Pode pagar a inscrição:</strong> {!! $lead->pode_pagar_inscricao ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>' !!}</p>
                            <p><strong>Aceita estudar online:</strong> {!! $lead->aceita_estudar_online ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>' !!}</p>
                            <p><strong>Compartilhar dados:</strong> {!! $lead->compartilhar_dados ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>' !!}</p>
                            <p><strong>Já perdeu vaga por falta de curso:</strong> {{ $lead->perdeu_vaga ?? 'Não informado' }}</p>
                            <p class="mb-0"><strong>Motivação:</strong> {{ $lead->motivacao ?? 'Não informada' }}</p>
                        </div>

                        {{-- Seção de Origem (UTM) --}}
                        <h6><i class="bi bi-signpost-split me-2"></i>Origem (UTM)</h6>
                        @php parse_str($lead->origem, $utm); @endphp
                        <div class="border rounded p-3 bg-light">
                            <p class="mb-1 small"><strong>Source:</strong> {{ $utm['utm_source'] ?? '-' }}</p>
                            <p class="mb-1 small"><strong>Medium:</strong> {{ $utm['utm_medium'] ?? '-' }}</p>
                            <p class="mb-1 small"><strong>Campaign:</strong> {{ $utm['utm_campaign'] ?? '-' }}</p>
                            <p class="mb-1 small"><strong>Content:</strong> {{ $utm['utm_content'] ?? '-' }}</p>
                            <p class="mb-0 small"><strong>Term:</strong> {{ $utm['utm_term'] ?? '-' }}</p>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection

@section('scripts')
    {{-- Seus scripts JS, se houver --}}
@endsection