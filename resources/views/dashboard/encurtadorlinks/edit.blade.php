<x-app-layout>
    <x-slot name="header">
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Link Encurtado') }}
        </h2>
        <a href="{{ route('encurtar_link_lista') }}">
            <i class="ri-arrow-left-line mr-1"></i>
            Voltar para a Lista
        </a>
    </div>
</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 mx-auto">
                    
                    <h2 class="text-2xl font-bold mb-2">Editar Link</h2>
                    <p class="text-sm text-gray-600 mb-6">Altere a URL de destino ou personalize o "slug" do seu link encurtado.</p>

                    <!-- Formulário com AJAX via Alpine.js -->
                   <form 
                        x-data="{ isSubmitting: false }"
                        @submit.prevent="
                            isSubmitting = true;
                            submitForm($el)
                                .finally(() => isSubmitting = false);
                        "
                        id="form_editar_link_encurtado"
                        action="{{ route('encurtar_link_editar_alterar', ['id' => $link->id]) }}"
                        method="POST"
                    >
                        @csrf
                        <div class="space-y-6">
                            <!-- Campo de URL longa -->
                            <div>
                                <x-input-label for="url_longa" :value="__('URL de Destino')" />
                                <x-text-input id="url_longa" name="url_longa" type="url" class="mt-1 block w-full py-3 px-4" :value="old('url_longa', $link->url_longa)" required />
                            </div>

                            <!-- Campo de Slug -->
                           <div>
                                <x-input-label for="slug" :value="__('Personalizar Link Encurtado (Slug)')" />
                                <div class="flex items-center mt-1">
                                    <span class="px-4 inline-flex items-center min-w-[180px] px-3 py-3 text-sm text-gray-500 bg-gray-100 border border-r-0 border-gray-300 rounded-l-md whitespace-nowrap overflow-hidden text-ellipsis">
                                        {{ Auth::user()->dominio_externo ?? Auth::user()->dominio }}/e/
                                    </span>
                                    <x-text-input id="slug" name="slug" type="text" class="block w-full rounded-l-none py-3 px-4" :value="old('slug', $link->slug)" />
                                </div>
                            </div>

                        </div>

                        <!-- Botão de submissão com estado dinâmico -->
                        <!-- Botão de submissão com estado dinâmico (CORRIGIDO) -->
                        <div class="mt-6 flex items-center">
    <x-primary-button x-bind:disabled="isSubmitting">
        <template x-if="!isSubmitting">
            <span class="flex items-center">
                <i class="ri-save-line mr-1"></i>
                Alterar Link
            </span>
        </template>
        <template x-if="isSubmitting">
            <span>Salvando...</span>
        </template>
    </x-primary-button>
</div>

                    </form>
                    
                    <!-- Seção para exibir a resposta do AJAX -->
                    <div id="alert_link" class="mt-6"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Agora esta é uma função reutilizável
        async function submitForm(form) {
            const alertDiv = document.getElementById('alert_link');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const formData = new FormData(form);
            alertDiv.innerHTML = '<p class="text-sm text-gray-500">Salvando...</p>';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw data; // Joga para o catch
                }

                // Resposta de sucesso
                const rawShortUrl = (data.link_encurtado || '').trim();
                const shortUrl = /^https?:\/\//i.test(rawShortUrl) ? rawShortUrl : `https://${rawShortUrl}`;
                const successHtml = `
                    <div class="p-4 bg-green-50 border border-green-200 rounded-lg" x-data="{ copied: false }">
                        <p class="text-sm text-green-800">Seu link foi alterado com sucesso!</p>
                        <div class="mt-2 flex items-stretch">
                            <a href="${shortUrl}" target="_blank" class="flex items-center w-full border border-r-0 border-gray-300 rounded-l-md text-sm bg-white font-mono px-3 py-2 break-all text-indigo-600 underline">${shortUrl}</a>
                            <button @click="navigator.clipboard.writeText('${shortUrl}'); copied = true; setTimeout(() => copied = false, 2000)" class="px-4 py-2 bg-green-600 text-white font-semibold rounded-r-md hover:bg-green-500 text-sm transition-colors flex items-center w-32 justify-center">
                                <span x-show="!copied"><i class="ri-file-copy-line mr-1"></i> Copiar</span>
                                <span x-show="copied" x-transition><i class="ri-check-line mr-1"></i> Copiado!</span>
                            </button>
                        </div>
                    </div>`;
                alertDiv.innerHTML = successHtml;

            } catch (errorData) {
                // Resposta de erro
                const errorMessage = errorData.message || "Ocorreu um erro desconhecido.";
                const errorHtml = `...`; // (seu HTML de erro aqui)
                alertDiv.innerHTML = errorHtml;
            }
        }
    </script>
</x-app-layout>
