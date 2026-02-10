<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $statusArquivado ? 'Leads Arquivados' : 'Novos Leads - Aulas Gratuitas' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Card de Filtros e Visualização -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="md:flex md:items-center md:justify-between">
                    <!-- Seletores de Visualização (Novos Leads / Arquivados) -->
                    <div>
                        <span class="text-sm font-medium text-gray-700 mr-3">Visualizar:</span>
                        <div class="inline-flex rounded-md shadow-sm" role="group">
                            <a href="{{ route('novos_leads', ['status' => 0] + request()->except('status', 'page')) }}" 
                               class="px-4 py-2 text-sm font-medium {{ !$statusArquivado ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border border-gray-300 rounded-l-lg">
                                <i class="ri-inbox-fill mr-1"></i> Novos Leads
                            </a>
                            <a href="{{ route('novos_leads', ['status' => 1] + request()->except('status', 'page')) }}" 
                               class="px-4 py-2 text-sm font-medium {{ $statusArquivado ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border-t border-b border-r border-gray-300 rounded-r-lg">
                                <i class="ri-archive-fill mr-1"></i> Arquivados
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Filtros de Data -->
                <form method="GET" action="{{ route('novos_leads') }}" class="mt-4 pt-4 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <x-input-label for="data_inicial" value="Data Inicial" />
                            <x-text-input type="date" id="data_inicial" name="data_inicial" class="mt-1 block w-full" value="{{ request('data_inicial') }}" />
                        </div>
                        <div>
                            <x-input-label for="data_final" value="Data Final" />
                            <x-text-input type="date" id="data_final" name="data_final" class="mt-1 block w-full" value="{{ request('data_final') }}" />
                        </div>
                        <div>
                            <input type="hidden" name="status" value="{{ $statusArquivado }}">
                            <x-primary-button class="w-full justify-center">
                                <i class="ri-filter-3-line mr-1"></i> Aplicar Filtros
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabela de Leads -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $statusArquivado ? 'Data de Criação' : 'Data de Cadastro' }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">WhatsApp</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Curso</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($leads as $lead)
                                <tr x-data="{ openModal: false }">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($lead->created_at)->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $lead->nome }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $lead->whatsapp }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 max-w-xs truncate" title="{{ $lead->curso }}">{{ $lead->curso }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        <x-secondary-button @click.prevent="openModal = true">
                                            <i class="ri-eye-line mr-1"></i> Detalhes
                                        </x-secondary-button>
                                        
                                        @if ($statusArquivado)
                                            <form action="{{ route('leads.desarquivar', $lead->id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja restaurar este lead?');">
                                                @csrf
                                                <x-secondary-button type="submit" class="text-green-600 border-green-300 hover:bg-green-50">
                                                    <i class="ri-inbox-unarchive-line"></i>
                                                </x-secondary-button>
                                            </form>
                                        @else
                                            <form action="{{ route('leads.arquivar', $lead->id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja arquivar este lead?');">
                                                @csrf
                                                <x-danger-button type="submit">
                                                    <i class="ri-archive-line"></i>
                                                </x-danger-button>
                                            </form>
                                        @endif

                                        <!-- Modal de Detalhes (específico para esta linha) -->
                                        <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
                                            <div x-show="openModal" x-transition.opacity class="fixed inset-0 bg-black/50" @click="openModal = false"></div>
                                            <div x-show="openModal" x-transition class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl">
                                                <div class="p-6">
                                                    <h3 class="text-lg font-bold">Detalhes de: {{ $lead->nome }}</h3>
                                                    {{-- Conteúdo do Modal aqui --}}
                                                    <div class="mt-4 space-y-4 text-sm max-h-96 overflow-y-auto">
                                                        {{-- Seus detalhes do lead aqui, convertidos para Tailwind --}}
                                                    </div>
                                                </div>
                                                <div class="px-6 py-4 bg-gray-50 text-right">
                                                    <x-secondary-button @click="openModal = false">Fechar</x-secondary-button>
                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-16 text-gray-500">Nenhum lead encontrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="p-6">
                    {{ $leads->appends(request()->query())->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>