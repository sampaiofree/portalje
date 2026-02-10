<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Configurações do Site e Integrações') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div id="alert_container" class="mb-6"></div>

            <form method="POST" action="{{ route('afiliado_configurar_site_post') }}" class="space-y-6">
                @csrf

                <!-- Card: Dados do Parceiro -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h2 class="text-lg font-medium text-gray-900">Dados do Parceiro</h2>
                        <p class="mt-1 text-sm text-gray-600">Informações que serão usadas no ranking e para contato.</p>
                        
                        <div class="mt-6 space-y-4">
                            <div>
                                <x-input-label for="telefone_pessoal_1" value="Telefone Pessoal 1" />
                                <x-text-input id="telefone_pessoal_1" name="telefone_pessoal_1" type="number" class="px-4 py-3 bg-gray-100 focus:bg-white mt-1 block w-full" :value="old('telefone_pessoal_1', Auth::user()->telefone_pessoal_1)" placeholder="Ex: 5562999998888" />
                            </div>
                            <div>
                                <x-input-label for="telefone_pessoal_2" value="Telefone Pessoal 2" />
                                <x-text-input id="telefone_pessoal_2" name="telefone_pessoal_2" type="number" class="px-4 py-3 bg-gray-100 focus:bg-white mt-1 block w-full" :value="old('telefone_pessoal_2', Auth::user()->telefone_pessoal_2)" />
                            </div>
                            <div>
                                <x-input-label for="apelido" value="Apelido no Ranking" />
                                <x-text-input id="apelido" name="apelido" type="text" class="px-4 py-3 bg-gray-100 focus:bg-white mt-1 block w-full" :value="old('apelido', Auth::user()->apelido)" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Domínio Externo -->
                @if(Auth::user()->dominio_externo OR !Auth::user()->dominio)
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h2 class="text-lg font-medium text-gray-900">Domínio Próprio (Opcional)</h2>
                        <p class="mt-1 text-sm text-gray-600">Se você possui um domínio registrado, configure-o aqui. Caso contrário, deixe em branco.</p>
                        
                        <div class="mt-6">
                            <x-input-label for="dominio_externo" value="Seu domínio (sem https:// ou www)" />
                            <x-text-input id="dominio_externo" name="dominio_externo" type="text" class="px-4 py-3 bg-gray-100 focus:bg-white mt-1 block w-full" :value="old('dominio_externo', Auth::user()->dominio_externo)" placeholder="exemplo.com.br" />
                            <div id="dominio_feedback" class="mt-2 text-sm text-red-600"></div>
                        </div>
                    </div>
                </div>
                @endif
                <!-- Card: Meta Ads (Facebook) -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                         <h2 class="text-lg font-medium text-gray-900 flex items-center">Meta Ads (Facebook)</h2>
                         <p class="mt-1 text-sm text-gray-600">Configure suas credenciais do Meta para rastreamento de conversões.</p>

                        <div class="mt-6 space-y-4">
                            <div>
                                <x-input-label for="meta_pixel_id" value="Meta Pixel ID" />
                                <div class="flex items-center gap-2 mt-1">
                                    <x-text-input id="meta_pixel_id" name="meta_pixel_id" type="text" class="px-4 py-3 bg-gray-100 focus:bg-white block w-full" :value="old('meta_pixel_id', Auth::user()->meta_pixel_id)" />
                                    <x-secondary-button type="button" id="btn_testar_pixel">Testar</x-secondary-button>
                                </div>
                            </div>
                             <div>
                                <x-input-label for="meta_pixel_api" value="Token da API de Conversões (Opcional)" />
                                <x-text-input id="meta_pixel_api" name="meta_pixel_api" type="text" class="mt-1 px-4 py-3 bg-gray-100 focus:bg-white block w-full" :value="old('meta_pixel_api', Auth::user()->meta_pixel_api)" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: ManyChat -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h2 class="text-lg font-medium text-gray-900">Integração com ManyChat</h2>
                        <p class="mt-1 text-sm text-gray-600">Configure suas credenciais para automações com o ManyChat.</p>
                        
                        <div class="mt-6 space-y-4">
                            <div>
                                <x-input-label for="many_api" value="Token da API" />
                                <x-text-input id="many_api" name="many_api" type="text" class="mt-1 px-4 py-3 bg-gray-100 focus:bg-white block w-full" :value="old('many_api', Auth::user()->many_api)" placeholder="Cole sua chave de API aqui" />
                            </div>
                            <div>
                                <x-input-label for="many_cliente_telefone_id" value="ID do Campo 'cliente_telefone_id'" />
                                <x-text-input id="many_cliente_telefone_id" name="many_cliente_telefone_id" type="text" class="mt-1 px-4 py-3 bg-gray-100 focus:bg-white block w-full" :value="old('many_cliente_telefone_id', Auth::user()->many_cliente_telefone_id)" placeholder="Cole o ID do campo customizado" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: BotConversa -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h2 class="text-lg font-medium text-gray-900 flex items-center">
                            Integração com BotConversa
                            <a href="https://youtu.be/KD2bZ1miXCc" target="_blank" class="ml-2 text-blue-500 hover:text-blue-700">
                                <i class="ri-question-fill text-xl"></i>
                            </a>
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Configure o webhook para integração com o BotConversa. 
                            <a href="https://drive.google.com/drive/folders/11NSQkACBTocGxk6DclMnBOiP3s2Arc_T?usp=drive_link" target="_blank" class="underline text-blue-600 hover:text-blue-800">Veja os materiais e vídeo aqui</a>.
                        </p>
                        
                        <div class="mt-6">
                            <x-input-label for="botconversa_webhook" value="URL do Webhook" />
                            <x-text-input id="botconversa_webhook" name="botconversa_webhook" type="text" class="mt-1 px-4 py-3 bg-gray-100 focus:bg-white block w-full" :value="old('botconversa_webhook', Auth::user()->botconversa_webhook)" placeholder="https://seu.botconversa.com.br/webhook" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Salvar Todas as Alterações') }}</x-primary-button>
                </div>
            </form>

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Sanitização do input de Domínio Externo
                const dominioInput = document.getElementById('dominio_externo');
                if (dominioInput) {
                    dominioInput.addEventListener('input', function() {
                        const feedbackDiv = document.getElementById('dominio_feedback');
                        const sanitizedValue = this.value.toLowerCase().replace(/[^a-z0-9.-]/g, '');
                        if (this.value !== sanitizedValue) {
                            feedbackDiv.textContent = 'Caracteres inválidos removidos. Use apenas letras, números, pontos e hífens.';
                        } else {
                            feedbackDiv.textContent = '';
                        }
                        this.value = sanitizedValue;
                    });
                }
                
                // Lógica para o botão de Testar Pixel
                const btnTestarPixel = document.getElementById('btn_testar_pixel');
                if (btnTestarPixel) {
                    btnTestarPixel.addEventListener('click', function() {
                        const pixelId = document.getElementById('meta_pixel_id').value;
                        const pixelApi = document.getElementById('meta_pixel_api').value;
                        const alertContainer = document.getElementById('alert_container');
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        alertContainer.innerHTML = '<p class="text-sm text-gray-500">Testando...</p>';

                        fetch('{{ route('afiliado_testar_pixel') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                meta_pixel_id: pixelId,
                                meta_pixel_api: pixelApi
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            let alertClass, message;
                            if (data.original && data.original.error) {
                                alertClass = 'bg-red-100 border-red-500 text-red-700';
                                message = `<strong>Erro da API da Meta:</strong> ${data.original.error}`;
                            } else {
                                alertClass = 'bg-green-100 border-green-500 text-green-700';
                                message = '<strong>Sucesso!</strong> O evento de teste foi enviado corretamente.';
                            }
                            alertContainer.innerHTML = `<div class="${alertClass} border-l-4 p-4" role="alert">${message}</div>`;
                        })
                        .catch(error => {
                            console.error('Erro:', error);
                            alertContainer.innerHTML = `<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">Ocorreu um erro de comunicação ao testar o pixel.</div>`;
                        });
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>