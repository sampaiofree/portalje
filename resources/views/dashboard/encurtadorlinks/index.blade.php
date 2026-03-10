<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Encurtador de Links') }}
            </h2>
            <a href="{{ route('encurtar_link') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="ri-add-line mr-1"></i>
            Novo Link
        </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                            <p class="text-sm font-medium">{{ session('success') }}</p>
                            @if (session('link_encurtado'))
                                @php
                                    $sessionLink = (string) session('link_encurtado');
                                    $sessionLinkNormalized = preg_match('/^https?:\/\//i', $sessionLink)
                                        ? $sessionLink
                                        : 'https://' . $sessionLink;
                                @endphp
                                <div class="mt-2 flex items-stretch">
                                    <a
                                        href="{{ $sessionLinkNormalized }}"
                                        target="_blank"
                                        class="flex items-center w-full border border-r-0 border-gray-300 rounded-l-md text-sm bg-white font-mono px-3 py-2 break-all text-indigo-600 underline"
                                    >
                                        {{ $sessionLinkNormalized }}
                                    </a>
                                    <button
                                        type="button"
                                        data-copy-link="{{ $sessionLinkNormalized }}"
                                        class="px-4 py-2 bg-green-600 text-white font-semibold rounded-r-md hover:bg-green-500 text-sm transition-colors flex items-center"
                                    >
                                        <i class="ri-file-copy-line mr-1"></i> Copiar
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                            <p class="text-sm font-medium">{{ session('error') }}</p>
                        </div>
                    @endif
                    
                    <!-- Container da Tabela com Alpine.js para a busca -->
                    <div x-data="{ search: '' }">
                        <div class="mb-4">
                            <x-input-label for="search_links" value="Buscar Links" />
                            <x-text-input x-model.debounce.300ms="search" id="search_links" class="mt-1 block w-full md:w-1/3" type="text" placeholder="Digite a URL ou o slug..." />
                        </div>

                        <!-- Tabela Responsiva -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Link Encurtado</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Link de Destino</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliques</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($links as $link)
                                        <tr x-show="search === '' || '{{ strtolower($link->dominio . $link->url_longa) }}'.includes(search.toLowerCase())" x-transition>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ $link->dominio }}" target="_blank" class="text-indigo-600 hover:underline break-all">{{ $link->dominio }}</a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ $link->url_longa }}" target="_blank" class="text-gray-600 hover:underline truncate max-w-xs block" title="{{$link->url_longa}}">{{ $link->url_longa }}</a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $link->click_count }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center space-x-4">
                                                    <a href="{{ route('encurtar_link_editar_mostrar', ['id'=> $link->id]) }}" class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                                        <i class="ri-pencil-fill text-lg"></i>
                                                    </a>
                                                    {{-- Para a exclusão, o ideal é um formulário com método DELETE --}}
                                                    <form method="POST" action="{{ route('encurtar_link_excluir', ['id'=> $link->id]) }}" onsubmit="return confirm('Tem certeza que deseja excluir este link?');">
                                                        @csrf
                                                        @method('DELETE') {{-- Supondo que sua rota de exclusão use o método DELETE --}}
                                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Excluir">
                                                            <i class="ri-delete-bin-fill text-lg"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                                Nenhum link encurtado encontrado.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Paginação do Laravel -->
                    <div class="mt-6">
                        {{-- Se você usa paginação no controller, descomente a linha abaixo --}}
                        {{-- {{ $links->links() }} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.addEventListener('click', function (event) {
                    const button = event.target.closest('[data-copy-link]');
                    if (!button) {
                        return;
                    }

                    const link = button.getAttribute('data-copy-link') || '';
                    if (!link) {
                        return;
                    }

                    const originalLabel = button.innerHTML;
                    const showCopiedState = () => {
                        button.innerHTML = '<i class="ri-check-line mr-1"></i> Copiado!';
                        setTimeout(() => {
                            button.innerHTML = originalLabel;
                        }, 1600);
                    };

                    const fallbackCopy = (value) => {
                        const textArea = document.createElement('textarea');
                        textArea.value = value;
                        textArea.setAttribute('readonly', '');
                        textArea.style.position = 'fixed';
                        textArea.style.opacity = '0';
                        document.body.appendChild(textArea);
                        textArea.select();
                        textArea.setSelectionRange(0, value.length);

                        let copied = false;
                        try {
                            copied = document.execCommand('copy');
                        } catch (err) {
                            copied = false;
                        }
                        document.body.removeChild(textArea);
                        return copied;
                    };

                    if (navigator.clipboard && window.isSecureContext) {
                        navigator.clipboard.writeText(link)
                            .then(showCopiedState)
                            .catch(() => {
                                if (fallbackCopy(link)) {
                                    showCopiedState();
                                    return;
                                }
                                window.prompt('Copie manualmente o link:', link);
                            });
                        return;
                    }

                    if (fallbackCopy(link)) {
                        showCopiedState();
                        return;
                    }

                    window.prompt('Copie manualmente o link:', link);
                });
            });
        </script>
    @endpush
</x-app-layout>
