<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Configurações do Site e Integrações') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <form method="POST" action="{{ route('afiliado_configurar_site_post') }}" class="space-y-6" enctype="multipart/form-data">
                @csrf

                <!-- Card: Dados do Parceiro -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h2 class="text-lg font-medium text-gray-900">Dados do Parceiro</h2>
                        <p class="mt-1 text-sm text-gray-600">Informações que serão usadas no ranking e para contato.</p>
                        
                        <div class="mt-6 space-y-4">
                            <div>
                                <x-input-label for="telefone_pessoal_1" value="Telefone Pessoal 1" />
                                <x-text-input
                                    id="telefone_pessoal_1"
                                    name="telefone_pessoal_1"
                                    type="text"
                                    inputmode="numeric"
                                    pattern="[0-9]{11,14}"
                                    class="px-4 py-3 bg-gray-100 focus:bg-white mt-1 block w-full"
                                    :value="old('telefone_pessoal_1', Auth::user()->telefone_pessoal_1_pending ?: Auth::user()->telefone_pessoal_1)"
                                    placeholder="Ex: 5562999998888"
                                />
                                @if(Auth::user()->telefone_pessoal_1_verified_at)
                                    <p class="mt-1 text-xs text-green-700">
                                        Numero validado em {{ Auth::user()->telefone_pessoal_1_verified_at->format('d/m/Y H:i') }}.
                                    </p>
                                @endif
                                @if(Auth::user()->telefone_pessoal_1_pending)
                                    <p class="mt-1 text-xs text-amber-700">
                                        Novo numero pendente de confirmacao. <a href="{{ route('phone.verification.notice') }}" class="underline">Concluir verificacao</a>.
                                    </p>
                                @endif
                            </div>
                            <div>
                                <x-input-label for="telefone_pessoal_2" value="Telefone Pessoal 2" />
                                <x-text-input id="telefone_pessoal_2" name="telefone_pessoal_2" type="number" class="px-4 py-3 bg-gray-100 focus:bg-white mt-1 block w-full" :value="old('telefone_pessoal_2', Auth::user()->telefone_pessoal_2)" />
                            </div>
                            <div>
                                <x-input-label for="apelido" value="Apelido no Ranking" />
                                <x-text-input id="apelido" name="apelido" type="text" class="px-4 py-3 bg-gray-100 focus:bg-white mt-1 block w-full" :value="old('apelido', Auth::user()->apelido)" />
                            </div>
                            <div>
                                <x-input-label for="nome_empresa" value="Nome da empresa" />
                                <x-text-input id="nome_empresa" name="nome_empresa" type="text" class="px-4 py-3 bg-gray-100 focus:bg-white mt-1 block w-full" :value="old('nome_empresa', Auth::user()->nome_empresa)" placeholder="Programa Jovem Empreendedor" />
                                @error('nome_empresa')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Fallback automático: Programa Jovem Empreendedor.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Identidade visual -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    @php
                        $hasLogoPadraoCustom = !empty(Auth::user()->logo_padrao_path);
                        $hasLogoDarkCustom = !empty(Auth::user()->logo_dark_path);
                        $logoPadraoAtual = Auth::user()->logo_padrao_path ? asset('storage/' . Auth::user()->logo_padrao_path) : asset('img/home_page/logojecolor.webp');
                        $logoDarkAtual = Auth::user()->logo_dark_path ? asset('storage/' . Auth::user()->logo_dark_path) : asset('img/home_page/logowhite.png');
                        $removeLogoPadraoOld = (int) old('remove_logo_padrao', 0);
                        $removeLogoDarkOld = (int) old('remove_logo_dark', 0);
                    @endphp
                    <div class="max-w-2xl">
                        <h2 class="text-lg font-medium text-gray-900">Logos da Home e dos Cursos</h2>
                        <p class="mt-1 text-sm text-gray-600">Envie sua logo padrão e sua logo dark. Limite por arquivo: 1MB (PNG, JPG, JPEG ou WEBP). Se não enviar, usamos as logos atuais como fallback.</p>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <input type="hidden" id="remove_logo_padrao" name="remove_logo_padrao" value="{{ $removeLogoPadraoOld ? '1' : '0' }}">
                                <x-input-label for="logo_padrao" value="Logo padrão" />
                                <input
                                    id="logo_padrao"
                                    name="logo_padrao"
                                    type="file"
                                    accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp"
                                    class="mt-1 block w-full text-sm text-gray-700 file:mr-3 file:rounded-md file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-500"
                                >
                                @error('logo_padrao')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <div class="mt-3 rounded-md border border-gray-200 p-3 bg-white">
                                    <div class="flex items-center justify-between gap-3 mb-2">
                                        <p class="text-xs text-gray-500">Logo atual</p>
                                        @if($hasLogoPadraoCustom)
                                            <button
                                                type="button"
                                                id="toggle_remove_logo_padrao"
                                                data-remove-target="remove_logo_padrao"
                                                data-status-target="remove_logo_padrao_status"
                                                data-default-class="text-red-600"
                                                data-active-class="text-amber-600"
                                                data-file-input="logo_padrao"
                                                class="inline-flex items-center gap-1 text-xs font-medium text-red-600 hover:text-red-700"
                                            >
                                                <i class="ri-delete-bin-line"></i>
                                                <span>Excluir</span>
                                            </button>
                                        @endif
                                    </div>
                                    <img src="{{ $logoPadraoAtual }}" alt="Logo padrão atual" class="h-12 w-auto object-contain">
                                    <p
                                        id="remove_logo_padrao_status"
                                        class="mt-2 text-xs font-medium text-amber-700 {{ $removeLogoPadraoOld ? '' : 'hidden' }}"
                                    >
                                        Logo será removida ao salvar.
                                    </p>
                                </div>
                            </div>

                            <div>
                                <input type="hidden" id="remove_logo_dark" name="remove_logo_dark" value="{{ $removeLogoDarkOld ? '1' : '0' }}">
                                <x-input-label for="logo_dark" value="Logo dark" />
                                <input
                                    id="logo_dark"
                                    name="logo_dark"
                                    type="file"
                                    accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp"
                                    class="mt-1 block w-full text-sm text-gray-700 file:mr-3 file:rounded-md file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-500"
                                >
                                @error('logo_dark')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <div class="mt-3 rounded-md border border-gray-700 p-3 bg-gray-900">
                                    <div class="flex items-center justify-between gap-3 mb-2">
                                        <p class="text-xs text-gray-300">Logo atual</p>
                                        @if($hasLogoDarkCustom)
                                            <button
                                                type="button"
                                                id="toggle_remove_logo_dark"
                                                data-remove-target="remove_logo_dark"
                                                data-status-target="remove_logo_dark_status"
                                                data-default-class="text-red-300"
                                                data-active-class="text-amber-300"
                                                data-file-input="logo_dark"
                                                class="inline-flex items-center gap-1 text-xs font-medium text-red-300 hover:text-red-200"
                                            >
                                                <i class="ri-delete-bin-line"></i>
                                                <span>Excluir</span>
                                            </button>
                                        @endif
                                    </div>
                                    <img src="{{ $logoDarkAtual }}" alt="Logo dark atual" class="h-12 w-auto object-contain">
                                    <p
                                        id="remove_logo_dark_status"
                                        class="mt-2 text-xs font-medium text-amber-300 {{ $removeLogoDarkOld ? '' : 'hidden' }}"
                                    >
                                        Logo será removida ao salvar.
                                    </p>
                                </div>
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

                <!-- Card: Canal de atendimento -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h2 class="text-lg font-medium text-gray-900">Canal de atendimento do site</h2>
                        <p class="mt-1 text-sm text-gray-600">Escolha qual canal será salvo como preferência de atendimento.</p>

                        <div class="mt-6">
                            @php
                                $siteContactProviderAtual = old('site_contact_provider', Auth::user()->site_contact_provider ?? 'whatsapp');
                                $siteContactProviderAtual = in_array((string) $siteContactProviderAtual, ['whatsapp', 'typebot'], true)
                                    ? (string) $siteContactProviderAtual
                                    : 'whatsapp';
                            @endphp
                            <x-input-label for="site_contact_provider" value="Canal de atendimento" />
                            <select
                                id="site_contact_provider"
                                name="site_contact_provider"
                                class="mt-1 px-4 py-3 bg-gray-100 focus:bg-white block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="whatsapp" @selected($siteContactProviderAtual === 'whatsapp')>WhatsApp</option>
                                <option value="typebot" @selected($siteContactProviderAtual === 'typebot')>Typebot</option>
                            </select>
                            <x-input-error :messages="$errors->get('site_contact_provider')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Card: Botão Flutuante do WhatsApp -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h2 class="text-lg font-medium text-gray-900">Botão flutuante do WhatsApp</h2>
                        <p class="mt-1 text-sm text-gray-600">Configuração aplicada à home (/) e /cursos, além da W3 (/w3 e /w3/{cidade}).</p>

                        <div class="mt-6 space-y-4">
                            @php
                                $w3FloatWhatsappOptions = Auth::user()->whatsappAtendimentos()
                                    ->where('is_active', true)
                                    ->orderByDesc('updated_at')
                                    ->orderBy('id')
                                    ->get();
                                $w3FloatWhatsappPadrao = $w3FloatWhatsappOptions->contains('id', Auth::user()->w3_whatsapp_float_whatsapp_atendimento_id)
                                    ? (string) Auth::user()->w3_whatsapp_float_whatsapp_atendimento_id
                                    : (string) optional($w3FloatWhatsappOptions->first())->id;
                                $w3FloatChannelAtual = old(
                                    'w3_whatsapp_float_channel',
                                    $w3FloatWhatsappPadrao
                                );
                            @endphp
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
                            <div>
                                <x-input-label for="w3_whatsapp_float_channel" value="Escolha o WhatsApp" />
                                <select
                                    id="w3_whatsapp_float_channel"
                                    name="w3_whatsapp_float_channel"
                                    class="mt-1 px-4 py-3 bg-gray-100 focus:bg-white block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    @if($w3FloatWhatsappOptions->isEmpty())
                                        <option value="">Cadastre um WhatsApp ativo</option>
                                    @endif
                                    @foreach($w3FloatWhatsappOptions as $w3FloatWhatsappOption)
                                        <option value="{{ $w3FloatWhatsappOption->id }}" @selected($w3FloatChannelAtual === (string) $w3FloatWhatsappOption->id)>
                                            {{ $w3FloatWhatsappOption->whatsapp }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($w3FloatWhatsappOptions->isEmpty())
                                    <p class="mt-1 text-xs text-yellow-700">Nenhum WhatsApp ativo encontrado. Cadastre um número de atendimento para usar no botão flutuante.</p>
                                @else
                                    <p class="mt-1 text-xs text-gray-500">Escolha qual número será usado no botão flutuante.</p>
                                @endif
                                <x-input-error :messages="$errors->get('w3_whatsapp_float_channel')" class="mt-2" />
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

                function bindLogoRemoveToggle(buttonId) {
                    const button = document.getElementById(buttonId);
                    if (!button) return;

                    const removeInputId = button.getAttribute('data-remove-target');
                    const statusId = button.getAttribute('data-status-target');
                    const defaultClass = button.getAttribute('data-default-class');
                    const activeClass = button.getAttribute('data-active-class');
                    const fileInputId = button.getAttribute('data-file-input');
                    const removeInput = removeInputId ? document.getElementById(removeInputId) : null;
                    const status = statusId ? document.getElementById(statusId) : null;
                    const fileInput = fileInputId ? document.getElementById(fileInputId) : null;
                    const label = button.querySelector('span');

                    if (!removeInput || !status || !label) return;

                    const applyState = (isRemoving) => {
                        removeInput.value = isRemoving ? '1' : '0';
                        status.classList.toggle('hidden', !isRemoving);
                        label.textContent = isRemoving ? 'Cancelar remoção' : 'Excluir';
                        if (defaultClass) {
                            button.classList.toggle(defaultClass, !isRemoving);
                        }
                        if (activeClass) {
                            button.classList.toggle(activeClass, isRemoving);
                        }
                    };

                    applyState(removeInput.value === '1');

                    button.addEventListener('click', function () {
                        const nextRemoving = removeInput.value !== '1';
                        applyState(nextRemoving);
                    });

                    if (fileInput) {
                        fileInput.addEventListener('change', function () {
                            if (fileInput.files && fileInput.files.length > 0) {
                                applyState(false);
                            }
                        });
                    }
                }

                bindLogoRemoveToggle('toggle_remove_logo_padrao');
                bindLogoRemoveToggle('toggle_remove_logo_dark');
            });
        </script>
    @endpush
</x-app-layout>
