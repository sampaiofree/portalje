<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Encurtar Novo Link') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 mx-auto">
                    
                    <h2 class="text-2xl font-bold mb-2">Encurtador de Links</h2>
                    <p class="text-sm text-gray-600 mb-6">Cole uma URL longa abaixo para criar um link curto e fácil de compartilhar.</p>

                    <!-- Formulário com AJAX -->
                    <form id="form_link_encurtado" action="{{ route('encurtar_link') }}" method="POST">
                        @csrf
                        <div>
                            <x-input-label for="url_longa" :value="__('URL de Destino')" />
                            <x-text-input 
                                id="url_longa" 
                                name="url_longa" 
                                type="url" 
                                class="mt-1 block w-full py-3 px-4" 
                                placeholder="https://www.exemplo.com/pagina-longa-e-complexa" 
                                required 
                            />
                        </div>

                        <!-- Botão de submissão -->
                        <div class="mt-6 flex items-center">
                            <x-primary-button>
                                <i class="ri-links-line mr-2"></i>
                                {{ __('Encurtar URL') }}
                            </x-primary-button>
                        </div>
                    </form>
                    
                    <!-- Seção para exibir a resposta do AJAX -->
                    <div id="alert_link" class="mt-6">
                        {{-- Mensagens da sessão (se a página for recarregada) --}}
                        @if(session('link_encurtado'))
                            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-sm text-green-800">Seu link encurtado é:</p>
                                <p class="mt-2 font-semibold text-green-900">
                                    <a href="{{ preg_match('/^https?:\/\//i', session('link_encurtado')) ? session('link_encurtado') : 'https://' . session('link_encurtado') }}" target="_blank" class="underline hover:text-green-700">{{ preg_match('/^https?:\/\//i', session('link_encurtado')) ? session('link_encurtado') : 'https://' . session('link_encurtado') }}</a>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('form_link_encurtado');
                const alertDiv = document.getElementById('alert_link');
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    
                    const formData = new FormData(form);
                    alertDiv.innerHTML = '<p class="text-sm text-gray-500">Encurtando...</p>';

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        const rawShortUrl = (data.link_encurtado || '').trim();
                        const shortUrl = /^https?:\/\//i.test(rawShortUrl) ? rawShortUrl : `https://${rawShortUrl}`;
                        const successHtml = `
                            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-sm text-green-800">Seu link encurtado é:</p>
                                <p class="mt-2 font-semibold text-green-900">
                                    <a href="${shortUrl}" target="_blank" class="underline hover:text-green-700">${shortUrl}</a>
                                </p>
                            </div>`;
                        alertDiv.innerHTML = successHtml;
                        form.reset(); // Limpa o campo do formulário após o sucesso
                    })
                    .catch(errorData => {
                        const errorMessage = errorData.message || "Ocorreu um erro desconhecido.";
                        const errorHtml = `
                            <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-sm text-red-800">${errorMessage}</p>
                            </div>`;
                        alertDiv.innerHTML = errorHtml;
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
