<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Configurações do Site e Integrações') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
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
                                <x-text-input id="meta_pixel_id" name="meta_pixel_id" type="text" class="mt-1 px-4 py-3 bg-gray-100 focus:bg-white block w-full" :value="old('meta_pixel_id', Auth::user()->meta_pixel_id)" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Botão Flutuante do WhatsApp -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h2 class="text-lg font-medium text-gray-900">Botão flutuante do WhatsApp</h2>
                        <p class="mt-1 text-sm text-gray-600">Configuração aplicada à home (/) e /cursos, além da W3 (/w3 e /w3/{cidade}).</p>

                        <div class="mt-6 space-y-4">
                            <div>
                                <x-input-label for="w3_whatsapp_float_enabled" value="Exibir botão flutuante" />
                                <select
                                    id="w3_whatsapp_float_enabled"
                                    name="w3_whatsapp_float_enabled"
                                    class="mt-1 px-4 py-3 bg-gray-100 focus:bg-white block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option value="1" @selected((int) old('w3_whatsapp_float_enabled', (int) (Auth::user()->w3_whatsapp_float_enabled ?? 1)) === 1)>Sim</option>
                                    <option value="0" @selected((int) old('w3_whatsapp_float_enabled', (int) (Auth::user()->w3_whatsapp_float_enabled ?? 1)) === 0)>Não</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="w3_whatsapp_float_delay_seconds" value="Tempo para aparecer" />
                                <select
                                    id="w3_whatsapp_float_delay_seconds"
                                    name="w3_whatsapp_float_delay_seconds"
                                    class="mt-1 px-4 py-3 bg-gray-100 focus:bg-white block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    @php
                                        $w3DelayAtual = (int) old('w3_whatsapp_float_delay_seconds', (int) (Auth::user()->w3_whatsapp_float_delay_seconds ?? 0));
                                        $w3DelayOptions = [0, 5, 10, 20, 30, 45, 60, 120];
                                    @endphp
                                    @foreach($w3DelayOptions as $delay)
                                        <option value="{{ $delay }}" @selected($w3DelayAtual === $delay)>
                                            {{ $delay === 0 ? 'Imediatamente' : $delay . ' segundos' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
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
            });
        </script>
    @endpush
</x-app-layout>
