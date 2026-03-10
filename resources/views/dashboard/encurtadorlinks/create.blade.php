<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Encurtar Novo Link') }}
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
                    <h2 class="text-2xl font-bold mb-2">Criar Link</h2>
                    <p class="text-sm text-gray-600 mb-6">Informe a URL de destino e, se quiser, personalize o slug agora.</p>

                    <form
                        x-data="{ isSubmitting: false }"
                        @submit.prevent="
                            isSubmitting = true;
                            submitCreateForm($el)
                                .finally(() => isSubmitting = false);
                        "
                        id="form_link_encurtado"
                        action="{{ route('encurtar_link') }}"
                        method="POST"
                    >
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="url_longa" :value="__('URL de Destino')" />
                                <x-text-input
                                    id="url_longa"
                                    name="url_longa"
                                    type="url"
                                    class="mt-1 block w-full py-3 px-4"
                                    :value="old('url_longa')"
                                    placeholder="https://www.exemplo.com/oferta"
                                    required
                                />
                            </div>

                            <div>
                                <x-input-label for="slug" :value="__('Personalizar Link Encurtado (Slug)')" />
                                <div class="flex items-center mt-1">
                                    <span class="inline-flex items-center min-w-[180px] px-3 py-3 text-sm text-gray-500 bg-gray-100 border border-r-0 border-gray-300 rounded-l-md whitespace-nowrap overflow-hidden text-ellipsis">
                                        {{ Auth::user()->dominio_externo ?? Auth::user()->dominio }}/e/
                                    </span>
                                    <x-text-input
                                        id="slug"
                                        name="slug"
                                        type="text"
                                        class="block w-full rounded-l-none py-3 px-4"
                                        :value="old('slug')"
                                        placeholder="opcional-exemplo"
                                    />
                                </div>
                                <p class="mt-2 text-xs text-gray-500">
                                    Campo opcional. Se deixar em branco, o sistema gera um slug automaticamente.
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center">
                            <x-primary-button x-bind:disabled="isSubmitting">
                                <template x-if="!isSubmitting">
                                    <span class="flex items-center">
                                        <i class="ri-links-line mr-1"></i>
                                        Criar Link
                                    </span>
                                </template>
                                <template x-if="isSubmitting">
                                    <span>Salvando...</span>
                                </template>
                            </x-primary-button>
                        </div>
                    </form>

                    <div id="alert_link" class="mt-6">
                        @if(session('link_encurtado'))
                            @php
                                $sessionLink = (string) session('link_encurtado');
                                $sessionLinkNormalized = preg_match('/^https?:\/\//i', $sessionLink)
                                    ? $sessionLink
                                    : 'https://' . $sessionLink;
                            @endphp
                            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-sm text-green-800">Seu link encurtado é:</p>
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
                                        class="px-4 py-2 bg-green-600 text-white font-semibold rounded-r-md hover:bg-green-500 text-sm transition-colors flex items-center w-32 justify-center"
                                    >
                                        <i class="ri-file-copy-line mr-1"></i> Copiar
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            async function submitCreateForm(form) {
                const alertDiv = document.getElementById('alert_link');
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const defaultRedirectUrl = @json(route('encurtar_link_lista'));
                if (!alertDiv || !csrfToken) {
                    return;
                }

                const formData = new FormData(form);
                alertDiv.innerHTML = '<p class="text-sm text-gray-500">Salvando...</p>';

                const buildErrorHtml = (data) => {
                    const messages = [];
                    if (data?.errors && typeof data.errors === 'object') {
                        Object.values(data.errors).forEach((fieldErrors) => {
                            if (Array.isArray(fieldErrors)) {
                                fieldErrors.forEach((message) => messages.push(String(message)));
                            }
                        });
                    }
                    if (messages.length === 0 && data?.message) {
                        messages.push(String(data.message));
                    }
                    if (messages.length === 0) {
                        messages.push('Não foi possível salvar o link agora.');
                    }

                    return `
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                            <ul class="text-sm text-red-800 list-disc pl-5 space-y-1">
                                ${messages.map((message) => `<li>${message}</li>`).join('')}
                            </ul>
                        </div>
                    `;
                };

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        alertDiv.innerHTML = buildErrorHtml(data);
                        return;
                    }

                    const rawShortUrl = (data.link_encurtado || '').trim();
                    const shortUrl = /^https?:\/\//i.test(rawShortUrl) ? rawShortUrl : `https://${rawShortUrl}`;
                    const redirectUrl = typeof data.redirect_to === 'string' && data.redirect_to !== ''
                        ? data.redirect_to
                        : defaultRedirectUrl;

                    alertDiv.innerHTML = `
                        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-sm text-green-800">Seu link foi criado com sucesso!</p>
                            <div class="mt-2 flex items-stretch">
                                <a href="${shortUrl}" target="_blank" class="flex items-center w-full border border-r-0 border-gray-300 rounded-l-md text-sm bg-white font-mono px-3 py-2 break-all text-indigo-600 underline">${shortUrl}</a>
                                <button type="button" data-copy-link="${shortUrl}" class="px-4 py-2 bg-green-600 text-white font-semibold rounded-r-md hover:bg-green-500 text-sm transition-colors flex items-center w-32 justify-center">
                                    <i class="ri-file-copy-line mr-1"></i> Copiar
                                </button>
                            </div>
                        </div>
                    `;

                    window.location.assign(redirectUrl);
                } catch (error) {
                    alertDiv.innerHTML = buildErrorHtml({ message: 'Ocorreu um erro inesperado.' });
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                const alertDiv = document.getElementById('alert_link');
                if (!alertDiv) {
                    return;
                }

                alertDiv.addEventListener('click', function (event) {
                    const button = event.target.closest('[data-copy-link]');
                    if (!button) {
                        return;
                    }

                    const link = button.getAttribute('data-copy-link') || '';
                    if (!link) {
                        return;
                    }

                    const applyCopiedState = () => {
                        button.innerHTML = '<i class="ri-check-line mr-1"></i> Copiado!';
                        setTimeout(() => {
                            button.innerHTML = '<i class="ri-file-copy-line mr-1"></i> Copiar';
                        }, 1600);
                    };

                    if (navigator.clipboard && window.isSecureContext) {
                        navigator.clipboard.writeText(link)
                            .then(applyCopiedState)
                            .catch(() => window.prompt('Copie manualmente o link:', link));
                        return;
                    }

                    window.prompt('Copie manualmente o link:', link);
                });
            });
        </script>
    @endpush
</x-app-layout>
