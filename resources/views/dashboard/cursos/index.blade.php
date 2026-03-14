<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Meus Cursos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @php
                $bulkCountdownDestinationOptions = [
                    'padrao' => [
                        ['value' => 'completo_padrao', 'label' => 'Plano completo - Sem cupom'],
                        ['value' => 'basico_padrao', 'label' => 'Plano básico - Sem cupom'],
                    ],
                    'um_preco' => [
                        ['value' => 'completo_padrao', 'label' => 'Plano completo - Sem cupom'],
                    ],
                    'dois_precos' => [
                        ['value' => 'completo_padrao', 'label' => 'Plano completo - Sem cupom'],
                    ],
                ];

                foreach (($cupons ?? collect()) as $cupomDestino) {
                    $bulkCountdownDestinationOptions['um_preco'][] = [
                        'value' => 'completo_cupom:' . $cupomDestino->id,
                        'label' => 'Plano completo - ' . $cupomDestino->desconto . '% OFF (' . $cupomDestino->codigo . ')',
                    ];
                    $bulkCountdownDestinationOptions['dois_precos'][] = [
                        'value' => 'completo_cupom:' . $cupomDestino->id,
                        'label' => 'Plano completo - ' . $cupomDestino->desconto . '% OFF (' . $cupomDestino->codigo . ')',
                    ];
                    $bulkCountdownDestinationOptions['dois_precos'][] = [
                        'value' => 'basico_cupom:' . $cupomDestino->id,
                        'label' => 'Plano básico - ' . $cupomDestino->desconto . '% OFF (' . $cupomDestino->codigo . ')',
                    ];
                }
            @endphp

            <div x-data="{ 
                search: '',
                showSuccessMessage: false,
                successMessage: '',
                showTutorial: false,
                bulkActionDropdownOpen: false,
                isBulkActionLoading: false,
                bulkActionError: '',
                bulkPublicConfigModalOpen: false,
                isBulkPublicConfigLoading: false,
                bulkPublicConfigError: '',
                bulkPublicConfigFormularioPreCheckout: '1',
                bulkPublicConfigModoPrecos: 'padrao',
                bulkPublicConfigCupomPrincipalId: '',
                bulkPublicConfigCupomSecundarioId: '',
                bulkPublicConfigUsarContador: '0',
                bulkPublicConfigContadorMinutos: '',
                bulkPublicConfigContadorAcao: '',
                bulkPublicConfigContadorDestinoOferta: '',
                bulkCountdownDestinationOptions: @js($bulkCountdownDestinationOptions),
                hasCuponsDisponiveis: {{ ($cupons ?? collect())->isNotEmpty() ? 'true' : 'false' }},
                bulkActionsUrl: @js(route('cadastrar_cursos_bulk_actions', [], false)),
                getCsrfToken() {
                    const csrfTokenEl = document.querySelector('meta[name=csrf-token]');
                    return csrfTokenEl ? csrfTokenEl.getAttribute('content') : '';
                },
                parseBulkActionError(payload, fallbackMessage) {
                    if (payload && payload.errors) {
                        const errors = Object.values(payload.errors);
                        return errors?.[0]?.[0] ?? fallbackMessage;
                    }

                    if (payload && payload.message) {
                        return payload.message;
                    }

                    return fallbackMessage;
                },
                applyBulkAction(action) {
                    if (this.isBulkActionLoading) return;

                    const confirmMessage = action === 'ativar_todos'
                        ? 'Tem certeza que deseja ativar todos os cursos com Código REF preenchido?'
                        : 'Tem certeza que deseja desativar todos os cursos com Código REF preenchido?';

                    if (!window.confirm(confirmMessage)) {
                        this.bulkActionDropdownOpen = false;
                        return;
                    }

                    const csrfToken = this.getCsrfToken();
                    if (!csrfToken) {
                        this.bulkActionError = 'Token de segurança não encontrado.';
                        return;
                    }

                    this.isBulkActionLoading = true;
                    this.bulkActionError = '';
                    this.bulkActionDropdownOpen = false;

                    const self = this;
                    fetch(this.bulkActionsUrl, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ action }),
                        })
                        .then(async function (response) {
                            const contentType = response.headers.get('content-type') || '';
                            const data = contentType.includes('application/json')
                                ? await response.json()
                                : {};

                            if (!response.ok || !data.success) {
                                throw data;
                            }

                            window.dispatchEvent(new CustomEvent('bulk-mostrar-curso-updated', {
                                detail: {
                                    action: data.action,
                                    updatedCourseIds: Array.isArray(data.updated_course_ids) ? data.updated_course_ids : [],
                                }
                            }));

                            window.dispatchEvent(new CustomEvent('show-success', {
                                detail: data.message || 'Ação em massa aplicada com sucesso.',
                            }));
                        })
                        .catch(function (error) {
                            self.bulkActionError = self.parseBulkActionError(
                                error,
                                'Não foi possível concluir a ação em massa.'
                            );
                        })
                        .finally(function () {
                            self.isBulkActionLoading = false;
                        });
                },
                openBulkPublicConfigModal() {
                    this.normalizeBulkPublicConfigSelections();
                    this.bulkPublicConfigError = '';
                    this.bulkPublicConfigModalOpen = true;
                },
                closeBulkPublicConfigModal() {
                    if (this.isBulkPublicConfigLoading) return;
                    this.bulkPublicConfigModalOpen = false;
                    this.bulkPublicConfigError = '';
                },
                get bulkCountdownActionOptions() {
                    if (this.bulkPublicConfigModoPrecos === 'um_preco') {
                        return [
                            { value: 'nada', label: 'Nada' },
                            { value: 'alterar_preco', label: 'Alterar o preço para' },
                            { value: 'whatsapp', label: 'WhatsApp' },
                        ];
                    }

                    return [
                        { value: 'nada', label: 'Nada' },
                        { value: 'encerrar_basico', label: 'Encerrar o plano básico' },
                        { value: 'alterar_preco', label: 'Alterar o preço para' },
                        { value: 'whatsapp', label: 'WhatsApp' },
                    ];
                },
                get activeBulkCountdownDestinationOptions() {
                    return this.bulkCountdownDestinationOptions[this.bulkPublicConfigModoPrecos] || [];
                },
                normalizeBulkPublicConfigSelections() {
                    if (this.bulkPublicConfigUsarContador !== '1') {
                        this.bulkPublicConfigContadorMinutos = '';
                        this.bulkPublicConfigContadorAcao = '';
                        this.bulkPublicConfigContadorDestinoOferta = '';
                        return;
                    }

                    const actionValues = this.bulkCountdownActionOptions.map(option => option.value);
                    if (this.bulkPublicConfigContadorAcao && !actionValues.includes(this.bulkPublicConfigContadorAcao)) {
                        this.bulkPublicConfigContadorAcao = '';
                    }

                    if (this.bulkPublicConfigContadorAcao !== 'alterar_preco') {
                        this.bulkPublicConfigContadorDestinoOferta = '';
                        return;
                    }

                    const destinationValues = this.activeBulkCountdownDestinationOptions.map(option => option.value);
                    if (
                        this.bulkPublicConfigContadorDestinoOferta &&
                        !destinationValues.includes(this.bulkPublicConfigContadorDestinoOferta)
                    ) {
                        this.bulkPublicConfigContadorDestinoOferta = '';
                    }
                },
                applyBulkPublicPageConfig() {
                    if (this.isBulkPublicConfigLoading) return;

                    const csrfToken = this.getCsrfToken();
                    if (!csrfToken) {
                        this.bulkPublicConfigError = 'Token de segurança não encontrado.';
                        return;
                    }

                    this.bulkPublicConfigError = '';
                    this.normalizeBulkPublicConfigSelections();

                    const payload = {
                        action: 'configurar_pagina_publica_todos',
                        formulario_pre_checkout: this.bulkPublicConfigFormularioPreCheckout,
                        modo_precos: this.bulkPublicConfigModoPrecos,
                        cupom_principal_id: this.bulkPublicConfigCupomPrincipalId || null,
                        cupom_secundario_id: this.bulkPublicConfigCupomSecundarioId || null,
                        usar_contador: this.bulkPublicConfigUsarContador,
                        contador_minutos: this.bulkPublicConfigContadorMinutos || null,
                        contador_acao: this.bulkPublicConfigContadorAcao || null,
                        contador_destino_oferta: this.bulkPublicConfigContadorDestinoOferta || null,
                    };

                    if (!this.hasCuponsDisponiveis) {
                        payload.modo_precos = 'padrao';
                        payload.cupom_principal_id = null;
                        payload.cupom_secundario_id = null;
                    } else if (payload.modo_precos === 'padrao') {
                        payload.cupom_principal_id = null;
                        payload.cupom_secundario_id = null;
                    } else if (payload.modo_precos === 'um_preco') {
                        payload.cupom_secundario_id = null;
                    }

                    let countdownValidationMessage = '';
                    if (payload.usar_contador === '1') {
                        if (!['1', '5', '10', '20', '30', '50'].includes(String(payload.contador_minutos || ''))) {
                            countdownValidationMessage = 'Selecione os minutos do contador.';
                        } else if (!this.bulkCountdownActionOptions.some(option => option.value === payload.contador_acao)) {
                            countdownValidationMessage = 'Selecione a ação do contador.';
                        } else if (payload.contador_acao === 'encerrar_basico' && payload.modo_precos === 'um_preco') {
                            countdownValidationMessage = 'A ação de encerrar o plano básico só pode ser usada quando a página exibe o plano básico.';
                        } else if (
                            payload.contador_acao === 'alterar_preco' &&
                            !this.activeBulkCountdownDestinationOptions.some(option => option.value === payload.contador_destino_oferta)
                        ) {
                            countdownValidationMessage = 'Selecione o preço que será mostrado após o contador.';
                        }
                    }

                    if (countdownValidationMessage) {
                        this.bulkPublicConfigError = countdownValidationMessage;
                        return;
                    }

                    if (payload.usar_contador !== '1') {
                        payload.contador_minutos = null;
                        payload.contador_acao = null;
                        payload.contador_destino_oferta = null;
                    } else if (payload.contador_acao !== 'alterar_preco') {
                        payload.contador_destino_oferta = null;
                    }

                    this.isBulkPublicConfigLoading = true;

                    const self = this;
                    fetch(this.bulkActionsUrl, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(payload),
                        })
                        .then(async function (response) {
                            const contentType = response.headers.get('content-type') || '';
                            const data = contentType.includes('application/json')
                                ? await response.json()
                                : {};

                            if (!response.ok || !data.success) {
                                throw data;
                            }

                            window.dispatchEvent(new CustomEvent('bulk-public-page-config-updated', {
                                detail: {
                                    updatedCourseIds: Array.isArray(data.updated_course_ids) ? data.updated_course_ids : [],
                                    formularioPreCheckout: !!data.formulario_pre_checkout,
                                    modoPrecos: data.modo_precos || 'padrao',
                                    cupomPrincipalId: data.cupom_principal_id || '',
                                    cupomSecundarioId: data.cupom_secundario_id || '',
                                    usarContador: !!data.usar_contador,
                                    contadorMinutos: data.contador_minutos || '',
                                    contadorAcao: data.contador_acao || '',
                                    contadorDestinoOferta: data.contador_destino_oferta || '',
                                }
                            }));

                            self.bulkPublicConfigModalOpen = false;
                            window.dispatchEvent(new CustomEvent('show-success', {
                                detail: data.message || 'Configurações em massa aplicadas com sucesso.',
                            }));
                        })
                        .catch(function (error) {
                            self.bulkPublicConfigError = self.parseBulkActionError(
                                error,
                                'Não foi possível aplicar as configurações em massa.'
                            );
                        })
                        .finally(function () {
                            self.isBulkPublicConfigLoading = false;
                        });
                }
            }"
            @show-success.window="showSuccessMessage = true; successMessage = $event.detail; setTimeout(() => showSuccessMessage = false, 3000)"
            @keydown.escape.window="if (bulkPublicConfigModalOpen && !isBulkPublicConfigLoading) closeBulkPublicConfigModal()">
                <!-- Card de Cabeçalho com o campo de busca -->
                <div class="bg-white overflow-visible shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold flex items-center">
                                    <i class="ri-book-line mr-2 text-blue-600"></i>
                                    Lista dos cursos do Portal JE
                                    <button @click="showTutorial = !showTutorial" class="ml-2 text-blue-500 hover:text-blue-700 transition-colors">
                                        <i class="ri-question-fill text-xl"></i>
                                    </button>
                                </h4>
                                <p class="mt-1 text-sm text-gray-600">
                                    Configure seus códigos REF e gere links personalizados para cada curso
                                </p>
                            </div>
                        </div>

                        <!-- Tutorial Expansível -->
                        <div x-show="showTutorial" x-transition class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <h5 class="font-semibold text-blue-800 mb-2">Como usar esta página:</h5>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• Configure um código REF único para cada curso</li>
                                <li>• Use o gerador para criar links personalizados</li>
                                <li>• Escolha se o link vai para checkout ou WhatsApp</li>
                                <li>• Adicione cupons de desconto quando necessário</li>
                            </ul>
                            <a href="https://www.youtube.com/watch?v=G3_UjvwizSc" target="_blank" class="inline-flex items-center mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                <i class="ri-play-circle-line mr-1"></i>
                                Ver tutorial completo
                            </a>
                        </div>

                        <!-- Campo de Busca Melhorado -->
                        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="relative max-w-md w-full">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="ri-search-line text-gray-400"></i>
                                </div>
                                <x-text-input 
                                    x-model.debounce.300ms="search" 
                                    id="campoPesquisa" 
                                    class="block w-full pl-10" 
                                    type="text" 
                                    placeholder="Buscar curso..." 
                                />
                                <div x-show="search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <button @click="search = ''" class="text-gray-400 hover:text-gray-600">
                                        <i class="ri-close-line"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 sm:flex-shrink-0">
                                <button
                                    type="button"
                                    @click="openBulkPublicConfigModal()"
                                    :disabled="isBulkPublicConfigLoading"
                                    class="inline-flex items-center justify-center rounded-md border border-blue-300 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 shadow-sm transition hover:bg-blue-100 disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    <i class="ri-sliders-line mr-2"></i>
                                    <span x-text="isBulkPublicConfigLoading ? 'Aplicando...' : 'Configurar página pública em massa'"></span>
                                </button>

                                <div class="relative" data-bulk-actions-menu>
                                    <button
                                        type="button"
                                        @click="bulkActionDropdownOpen = !bulkActionDropdownOpen"
                                        :disabled="isBulkActionLoading"
                                        class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-60"
                                    >
                                        <i class="ri-settings-3-line mr-2"></i>
                                        <span x-text="isBulkActionLoading ? 'Processando...' : 'Ações'"></span>
                                        <i class="ri-arrow-down-s-line ml-2 transition-transform" :class="bulkActionDropdownOpen ? 'rotate-180' : ''"></i>
                                    </button>

                                    <div
                                        x-show="bulkActionDropdownOpen"
                                        x-transition.origin.top.right
                                        @click.outside="bulkActionDropdownOpen = false"
                                        class="absolute right-0 z-20 mt-2 w-56 overflow-hidden rounded-md border border-gray-200 bg-white shadow-lg"
                                    >
                                        <button
                                            type="button"
                                            @click="applyBulkAction('ativar_todos')"
                                            :disabled="isBulkActionLoading"
                                            class="flex w-full items-center px-4 py-2 text-left text-sm text-gray-700 transition hover:bg-green-50 hover:text-green-700 disabled:cursor-not-allowed disabled:opacity-60"
                                        >
                                            <i class="ri-checkbox-circle-line mr-2"></i>
                                            Ativar todos
                                        </button>
                                        <button
                                            type="button"
                                            @click="applyBulkAction('desativar_todos')"
                                            :disabled="isBulkActionLoading"
                                            class="flex w-full items-center px-4 py-2 text-left text-sm text-gray-700 transition hover:bg-red-50 hover:text-red-700 disabled:cursor-not-allowed disabled:opacity-60"
                                        >
                                            <i class="ri-close-circle-line mr-2"></i>
                                            Desativar todos
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mensagem de Sucesso -->
                <div x-show="showSuccessMessage" x-transition class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="ri-check-circle-line text-green-600 mr-2"></i>
                        <span class="text-green-800" x-text="successMessage"></span>
                    </div>
                </div>

                <div x-show="bulkActionError" x-transition class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="ri-error-warning-line text-red-600 mr-2"></i>
                        <span class="text-red-800" x-text="bulkActionError"></span>
                    </div>
                </div>

                <div
                    x-show="bulkPublicConfigModalOpen"
                    x-transition.opacity
                    class="fixed inset-0 z-40 bg-slate-900/50"
                    @click="closeBulkPublicConfigModal()"
                ></div>

                <div
                    x-show="bulkPublicConfigModalOpen"
                    x-transition
                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                >
                    <div @click.stop class="je-dark-surface w-full max-w-2xl overflow-hidden rounded-xl border shadow-xl">
                        <div class="je-dark-divider flex items-center justify-between border-b px-6 py-4">
                            <h3 class="text-lg font-semibold text-white">Configurações da página pública em massa</h3>
                            <button
                                type="button"
                                @click="closeBulkPublicConfigModal()"
                                :disabled="isBulkPublicConfigLoading"
                                class="rounded-md p-1 text-slate-300 transition hover:bg-slate-700 hover:text-white disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <i class="ri-close-line text-xl"></i>
                            </button>
                        </div>

                        <div class="space-y-5 px-6 py-5">
                            <p class="je-dark-muted text-sm">
                                Esta configuração será aplicada a todos os cursos com Código REF preenchido.
                            </p>

                            <p
                                x-show="bulkPublicConfigError"
                                x-text="bulkPublicConfigError"
                                class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"
                            ></p>

                            <div>
                                <x-input-label for="bulk_formulario_pre_checkout" value="Formulário antes de continuar" class="je-dark-label text-sm font-medium" />
                                <div class="relative mt-1">
                                    <select
                                        id="bulk_formulario_pre_checkout"
                                        x-model="bulkPublicConfigFormularioPreCheckout"
                                        :disabled="isBulkPublicConfigLoading"
                                        class="je-dark-field block w-full appearance-none rounded-lg px-3 py-2.5 pr-10 text-sm shadow-sm transition"
                                    >
                                        <option value="1">Com formulário antes de continuar</option>
                                        <option value="0">Sem formulário (ir direto)</option>
                                    </select>
                                    <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                </div>
                                <p class="je-dark-muted mt-1 text-xs">Esta opção vale para botões de checkout e WhatsApp da página pública.</p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-white">Preços na página pública</p>
                                <p class="je-dark-muted text-xs">Defina se as páginas terão o modelo padrão, 1 plano ou 2 planos com cupons.</p>
                            </div>

                            <template x-if="!hasCuponsDisponiveis">
                                <div class="rounded-md border border-yellow-200 bg-yellow-50 px-3 py-2 text-xs text-yellow-800">
                                    Nenhum cupom disponível. A configuração de preço será aplicada no modo padrão.
                                </div>
                            </template>

                            <template x-if="hasCuponsDisponiveis">
                                <div class="space-y-4">
                                    <div>
                                        <x-input-label for="bulk_modo_precos" value="Tipo de página" class="je-dark-label text-sm font-medium" />
                                        <div class="relative mt-1">
                                            <select
                                                id="bulk_modo_precos"
                                                x-model="bulkPublicConfigModoPrecos"
                                                @change="
                                                    if (bulkPublicConfigModoPrecos === 'padrao') {
                                                        bulkPublicConfigCupomPrincipalId = '';
                                                        bulkPublicConfigCupomSecundarioId = '';
                                                    } else if (bulkPublicConfigModoPrecos === 'um_preco') {
                                                        bulkPublicConfigCupomSecundarioId = '';
                                                    }
                                                    normalizeBulkPublicConfigSelections();
                                                "
                                                :disabled="isBulkPublicConfigLoading"
                                                class="je-dark-field block w-full appearance-none rounded-lg px-3 py-2.5 pr-10 text-sm shadow-sm transition"
                                            >
                                                <option value="padrao">Página padrão</option>
                                                <option value="um_preco">Página com um plano</option>
                                                <option value="dois_precos">Página com dois planos</option>
                                            </select>
                                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                        </div>
                                    </div>

                                    <div x-show="bulkPublicConfigModoPrecos === 'um_preco'" x-transition>
                                        <x-input-label for="bulk_cupom_principal_id_um_preco" value="Preço do plano" class="je-dark-label text-sm font-medium" />
                                        <div class="relative mt-1">
                                            <select
                                                id="bulk_cupom_principal_id_um_preco"
                                                x-model="bulkPublicConfigCupomPrincipalId"
                                                :disabled="isBulkPublicConfigLoading || bulkPublicConfigModoPrecos !== 'um_preco'"
                                                class="je-dark-field block w-full appearance-none rounded-lg px-3 py-2.5 pr-10 text-sm shadow-sm transition"
                                            >
                                                <option value="">Padrão (sem cupom)</option>
                                                @foreach(($cupons ?? collect()) as $cupom)
                                                    <option value="{{ $cupom->id }}">{{ $cupom->desconto }}% OFF ({{ $cupom->codigo }})</option>
                                                @endforeach
                                            </select>
                                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                        </div>
                                    </div>

                                    <div x-show="bulkPublicConfigModoPrecos === 'dois_precos'" x-transition class="space-y-3">
                                        <div>
                                            <x-input-label for="bulk_cupom_principal_id_dois_precos" value="Preço do plano completo" class="je-dark-label text-sm font-medium" />
                                            <div class="relative mt-1">
                                                <select
                                                    id="bulk_cupom_principal_id_dois_precos"
                                                    x-model="bulkPublicConfigCupomPrincipalId"
                                                    @change="if (bulkPublicConfigCupomSecundarioId && bulkPublicConfigCupomSecundarioId === bulkPublicConfigCupomPrincipalId) bulkPublicConfigCupomSecundarioId = '';"
                                                    :disabled="isBulkPublicConfigLoading || bulkPublicConfigModoPrecos !== 'dois_precos'"
                                                    class="je-dark-field block w-full appearance-none rounded-lg px-3 py-2.5 pr-10 text-sm shadow-sm transition"
                                                >
                                                    <option value="">Padrão (sem cupom)</option>
                                                    @foreach(($cupons ?? collect()) as $cupom)
                                                        <option value="{{ $cupom->id }}">{{ $cupom->desconto }}% OFF ({{ $cupom->codigo }})</option>
                                                    @endforeach
                                                </select>
                                                <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                            </div>
                                        </div>

                                        <div>
                                            <x-input-label for="bulk_cupom_secundario_id_dois_precos" value="Preço do plano básico" class="je-dark-label text-sm font-medium" />
                                            <div class="relative mt-1">
                                                <select
                                                    id="bulk_cupom_secundario_id_dois_precos"
                                                    x-model="bulkPublicConfigCupomSecundarioId"
                                                    :disabled="isBulkPublicConfigLoading || bulkPublicConfigModoPrecos !== 'dois_precos'"
                                                    class="je-dark-field block w-full appearance-none rounded-lg px-3 py-2.5 pr-10 text-sm shadow-sm transition"
                                                >
                                                    <option value="">Selecione um cupom</option>
                                                    @foreach(($cupons ?? collect()) as $cupom)
                                                        <option
                                                            value="{{ $cupom->id }}"
                                                            :disabled="bulkPublicConfigCupomPrincipalId === '{{ $cupom->id }}'"
                                                        >
                                                            {{ $cupom->desconto }}% OFF ({{ $cupom->codigo }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-white">Contador da página pública</p>
                                    <p class="je-dark-muted text-xs">O contador começa ao carregar a página e continua de onde parou no mesmo navegador.</p>
                                </div>

                                <div>
                                    <x-input-label for="bulk_usar_contador" value="Usar contador?" class="je-dark-label text-sm font-medium" />
                                    <div class="relative mt-1">
                                        <select
                                            id="bulk_usar_contador"
                                            x-model="bulkPublicConfigUsarContador"
                                            @change="
                                                if (bulkPublicConfigUsarContador !== '1') {
                                                    bulkPublicConfigContadorMinutos = '';
                                                    bulkPublicConfigContadorAcao = '';
                                                    bulkPublicConfigContadorDestinoOferta = '';
                                                }
                                            "
                                            :disabled="isBulkPublicConfigLoading"
                                            class="je-dark-field block w-full appearance-none rounded-lg px-3 py-2.5 pr-10 text-sm shadow-sm transition"
                                        >
                                            <option value="0">Não</option>
                                            <option value="1">Sim</option>
                                        </select>
                                        <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    </div>
                                </div>

                                <div x-show="bulkPublicConfigUsarContador === '1'" x-transition class="space-y-4">
                                    <div>
                                        <x-input-label for="bulk_contador_minutos" value="Minutos" class="je-dark-label text-sm font-medium" />
                                        <div class="relative mt-1">
                                            <select
                                                id="bulk_contador_minutos"
                                                x-model="bulkPublicConfigContadorMinutos"
                                                :disabled="isBulkPublicConfigLoading"
                                                class="je-dark-field block w-full appearance-none rounded-lg px-3 py-2.5 pr-10 text-sm shadow-sm transition"
                                            >
                                                <option value="">Selecione</option>
                                                <option value="1">1 minuto</option>
                                                <option value="5">5 minutos</option>
                                                <option value="10">10 minutos</option>
                                                <option value="20">20 minutos</option>
                                                <option value="30">30 minutos</option>
                                                <option value="50">50 minutos</option>
                                            </select>
                                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                        </div>
                                    </div>

                                    <div>
                                        <x-input-label for="bulk_contador_acao" value="Após o contador" class="je-dark-label text-sm font-medium" />
                                        <div class="relative mt-1">
                                            <select
                                                id="bulk_contador_acao"
                                                x-model="bulkPublicConfigContadorAcao"
                                                @change="normalizeBulkPublicConfigSelections()"
                                                :disabled="isBulkPublicConfigLoading"
                                                class="je-dark-field block w-full appearance-none rounded-lg px-3 py-2.5 pr-10 text-sm shadow-sm transition"
                                            >
                                                <option value="">Selecione</option>
                                                <template x-for="option in bulkCountdownActionOptions" :key="option.value">
                                                    <option :value="option.value" x-text="option.label"></option>
                                                </template>
                                            </select>
                                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                        </div>
                                    </div>

                                    <div x-show="bulkPublicConfigContadorAcao === 'alterar_preco'" x-transition>
                                        <x-input-label for="bulk_contador_destino_oferta" value="Alterar o preço para" class="je-dark-label text-sm font-medium" />
                                        <div class="relative mt-1">
                                            <select
                                                id="bulk_contador_destino_oferta"
                                                x-model="bulkPublicConfigContadorDestinoOferta"
                                                :disabled="isBulkPublicConfigLoading"
                                                class="je-dark-field block w-full appearance-none rounded-lg px-3 py-2.5 pr-10 text-sm shadow-sm transition"
                                            >
                                                <option value="">Selecione um preço</option>
                                                <template x-for="option in activeBulkCountdownDestinationOptions" :key="option.value">
                                                    <option :value="option.value" x-text="option.label"></option>
                                                </template>
                                            </select>
                                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="je-dark-divider flex items-center justify-end gap-3 border-t px-6 py-4">
                            <button
                                type="button"
                                @click="closeBulkPublicConfigModal()"
                                :disabled="isBulkPublicConfigLoading"
                                class="inline-flex items-center justify-center rounded-md border border-slate-500 bg-slate-700 px-4 py-2 text-sm font-medium text-slate-100 transition hover:bg-slate-600 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                Cancelar
                            </button>
                            <button
                                type="button"
                                @click="applyBulkPublicPageConfig()"
                                :disabled="isBulkPublicConfigLoading"
                                class="inline-flex items-center justify-center rounded-md border border-blue-600 bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <i class="ri-save-line mr-2"></i>
                                <span x-text="isBulkPublicConfigLoading ? 'Aplicando...' : 'Aplicar em todos'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Grid com os Cards dos Cursos -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($cursos as $curso)
                        @if($curso->publicado && $curso->permitir_afiliacao)
                            <div class="lista_cursos" x-show="search === '' || @js(mb_strtolower($curso->titulo, 'UTF-8')).includes(search.toLowerCase())" x-transition>
                                @php
                                    $parseCourseMonetary = static function ($valor): ?float {
                                        if (!is_scalar($valor) || $valor === '') {
                                            return null;
                                        }

                                        $normalizado = trim((string) $valor);
                                        $normalizado = preg_replace('/^\s*\d+\s*x(?:\s*de)?\s*/i', '', $normalizado) ?? $normalizado;
                                        $somenteNumeros = preg_replace('/[^\d,.]/', '', $normalizado);

                                        if (!$somenteNumeros) {
                                            return null;
                                        }

                                        if (str_contains($somenteNumeros, ',') && str_contains($somenteNumeros, '.')) {
                                            $ultimaVirgula = strrpos($somenteNumeros, ',');
                                            $ultimoPonto = strrpos($somenteNumeros, '.');

                                            if ($ultimaVirgula !== false && $ultimoPonto !== false && $ultimaVirgula > $ultimoPonto) {
                                                $somenteNumeros = str_replace('.', '', $somenteNumeros);
                                                $somenteNumeros = str_replace(',', '.', $somenteNumeros);
                                            } else {
                                                $somenteNumeros = str_replace(',', '', $somenteNumeros);
                                            }
                                        } elseif (str_contains($somenteNumeros, ',')) {
                                            $somenteNumeros = str_replace('.', '', $somenteNumeros);
                                            $somenteNumeros = str_replace(',', '.', $somenteNumeros);
                                        } elseif (substr_count($somenteNumeros, '.') > 1) {
                                            $partes = explode('.', $somenteNumeros);
                                            $decimal = array_pop($partes);
                                            $somenteNumeros = implode('', $partes) . '.' . $decimal;
                                        }

                                        return is_numeric($somenteNumeros) ? (float) $somenteNumeros : null;
                                    };

                                    $formatCourseCurrency = static function (?float $valor): ?string {
                                        return $valor !== null ? 'R$' . number_format($valor, 2, ',', '.') : null;
                                    };

                                    $precoCompletoBase = $parseCourseMonetary($curso->preco_cheio_completo ?? null);
                                    $precoBasicoPadrao = $precoCompletoBase !== null ? $precoCompletoBase * 0.5 : null;
                                    $countdownDestinationOptions = [
                                        'padrao' => [
                                            [
                                                'value' => 'completo_padrao',
                                                'label' => 'Plano completo - Sem cupom'
                                                    . ($precoCompletoBase !== null ? ' - ' . $formatCourseCurrency($precoCompletoBase) : ''),
                                            ],
                                            [
                                                'value' => 'basico_padrao',
                                                'label' => 'Plano básico - Sem cupom'
                                                    . ($precoBasicoPadrao !== null ? ' - ' . $formatCourseCurrency($precoBasicoPadrao) : ''),
                                            ],
                                        ],
                                        'um_preco' => [
                                            [
                                                'value' => 'completo_padrao',
                                                'label' => 'Plano completo - Sem cupom'
                                                    . ($precoCompletoBase !== null ? ' - ' . $formatCourseCurrency($precoCompletoBase) : ''),
                                            ],
                                        ],
                                        'dois_precos' => [
                                            [
                                                'value' => 'completo_padrao',
                                                'label' => 'Plano completo - Sem cupom'
                                                    . ($precoCompletoBase !== null ? ' - ' . $formatCourseCurrency($precoCompletoBase) : ''),
                                            ],
                                        ],
                                    ];

                                    foreach (($cupons ?? collect()) as $cupomDestino) {
                                        $precoCompletoCupom = $precoCompletoBase !== null
                                            ? $precoCompletoBase * (1 - ($cupomDestino->desconto / 100))
                                            : null;
                                        $countdownDestinationOptions['um_preco'][] = [
                                            'value' => 'completo_cupom:' . $cupomDestino->id,
                                            'label' => 'Plano completo - ' . $cupomDestino->desconto . '% OFF (' . $cupomDestino->codigo . ')'
                                                . ($precoCompletoCupom !== null ? ' - ' . $formatCourseCurrency($precoCompletoCupom) : ''),
                                        ];
                                        $countdownDestinationOptions['dois_precos'][] = [
                                            'value' => 'completo_cupom:' . $cupomDestino->id,
                                            'label' => 'Plano completo - ' . $cupomDestino->desconto . '% OFF (' . $cupomDestino->codigo . ')'
                                                . ($precoCompletoCupom !== null ? ' - ' . $formatCourseCurrency($precoCompletoCupom) : ''),
                                        ];

                                        $precoBasicoCupom = $precoCompletoBase !== null
                                            ? $precoCompletoBase * (1 - ($cupomDestino->desconto / 100))
                                            : null;
                                        $countdownDestinationOptions['dois_precos'][] = [
                                            'value' => 'basico_cupom:' . $cupomDestino->id,
                                            'label' => 'Plano básico - ' . $cupomDestino->desconto . '% OFF (' . $cupomDestino->codigo . ')'
                                                . ($precoBasicoCupom !== null ? ' - ' . $formatCourseCurrency($precoBasicoCupom) : ''),
                                        ];
                                    }
                                @endphp
                                
                                <!-- Início do Alpine Component para cada card -->
                                <div
                                    x-data="{
                                        // Estado expandido/colapsado
                                        expanded: false,

                                        resourcesExpanded: false,

                                        publicPageConfigExpanded: false,

                                        hasCodigoRef: {{ $curso->codigo_ref ? 'true' : 'false' }},
                                        codigoRefId: {{ $curso->codigo_ref_id ? (int) $curso->codigo_ref_id : 'null' }},
                                        codigoRef: @js($curso->codigo_ref),
                                        mostrarCurso: {{ $curso->mostrar_curso ? 'true' : 'false' }},
                                        formularioPreCheckout: @js((isset($curso->formulario_pre_checkout) ? (bool) $curso->formulario_pre_checkout : true) ? '1' : '0'),
                                        modoPrecos: @js(in_array(($curso->modo_precos ?? 'padrao'), ['padrao', 'um_preco', 'dois_precos'], true) ? $curso->modo_precos : 'padrao'),
                                        cupomPrincipalId: @js(!empty($curso->cupom_principal_id) ? (string) $curso->cupom_principal_id : ''),
                                        cupomSecundarioId: @js(!empty($curso->cupom_secundario_id) ? (string) $curso->cupom_secundario_id : ''),
                                        usarContador: @js(!empty($curso->usar_contador) ? '1' : '0'),
                                        contadorMinutos: @js(!empty($curso->contador_minutos) ? (string) $curso->contador_minutos : ''),
                                        contadorAcao: @js($curso->contador_acao ?? ''),
                                        contadorDestinoOferta: @js($curso->contador_destino_oferta ?? ''),
                                        countdownDestinationOptions: @js($countdownDestinationOptions),
                                        hasCuponsDisponiveis: {{ ($cupons ?? collect())->isNotEmpty() ? 'true' : 'false' }},
                                        isSavingPricing: false,
                                        pricingSaveMessage: '',
                                        pricingSaveError: '',

                                        // Estado para o Gerador da Página de Vendas
                                        salesPageTarget: 'checkout',
                                        salesPageGratuitas: false,

                                        // Estado para o Gerador do Checkout
                                        checkoutBoleto: 'com',
                                        checkoutCupom: '',

                                          // NOVO: Estado para feedback de cópia
                                        copiedSales: false,
                                        copiedCheckout: false,

                                        // URLs Base
                                        baseUrl: 'https://{{ Auth::user()->dominio_externo ?? Auth::user()->dominio }}/{{$curso->url}}',
                                        baseCheckoutUrl: @js($curso->link_checkout_completo),

                                        // Função para copiar com feedback melhorado
                                       copyLink(text, type) {
                                            if (!text) return;

                                            const onCopySuccess = () => {
                                                if (type === 'sales') this.copiedSales = true;
                                                if (type === 'checkout') this.copiedCheckout = true;

                                                // Reseta o estado do botão após 2 segundos
                                                setTimeout(() => {
                                                    if (type === 'sales') this.copiedSales = false;
                                                    if (type === 'checkout') this.copiedCheckout = false;
                                                }, 2000);
                                            };

                                            const fallbackCopy = (value) => {
                                                const textArea = document.createElement('textarea');
                                                textArea.value = value;
                                                textArea.setAttribute('readonly', '');
                                                textArea.style.position = 'fixed';
                                                textArea.style.opacity = '0';
                                                document.body.appendChild(textArea);
                                                textArea.select();
                                                textArea.setSelectionRange(0, textArea.value.length);

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
                                                navigator.clipboard.writeText(text)
                                                    .then(() => onCopySuccess())
                                                    .catch(() => {
                                                        if (fallbackCopy(text)) {
                                                            onCopySuccess();
                                                            return;
                                                        }

                                                        window.prompt('Copie manualmente o link:', text);
                                                    });
                                                return;
                                            }

                                            if (fallbackCopy(text)) {
                                                onCopySuccess();
                                                return;
                                            }

                                            window.prompt('Copie manualmente o link:', text);
                                        },

                                        // Propriedades Computadas
                                        get finalSalesUrl() {
                                            let url = this.baseUrl;
                                            if (this.salesPageTarget === 'whatsapp') url += '/w';
                                            if (this.salesPageGratuitas) url += '?g=1';
                                            return url;
                                        },

                                        get finalCheckoutUrl() {
                                            if (!this.baseCheckoutUrl) return '';
                                            let url = this.baseCheckoutUrl;
                                            url += this.checkoutBoleto === 'com' ? '&hideBillet=0' : '&hideBillet=1';
                                            if (this.checkoutCupom) url += '&offDiscount=' + this.checkoutCupom;
                                            return url;
                                        },

                                        get countdownActionOptions() {
                                            if (this.modoPrecos === 'um_preco') {
                                                return [
                                                    { value: 'nada', label: 'Nada' },
                                                    { value: 'alterar_preco', label: 'Alterar o preço para' },
                                                    { value: 'whatsapp', label: 'WhatsApp' },
                                                ];
                                            }

                                            return [
                                                { value: 'nada', label: 'Nada' },
                                                { value: 'encerrar_basico', label: 'Encerrar o plano básico' },
                                                { value: 'alterar_preco', label: 'Alterar o preço para' },
                                                { value: 'whatsapp', label: 'WhatsApp' },
                                            ];
                                        },

                                        get activeCountdownDestinationOptions() {
                                            return this.countdownDestinationOptions[this.modoPrecos] || [];
                                        },

                                        normalizeCountdownSelections() {
                                            if (this.usarContador !== '1') {
                                                this.contadorMinutos = '';
                                                this.contadorAcao = '';
                                                this.contadorDestinoOferta = '';
                                                return;
                                            }

                                            const actionValues = this.countdownActionOptions.map(option => option.value);
                                            if (this.contadorAcao && !actionValues.includes(this.contadorAcao)) {
                                                this.contadorAcao = '';
                                            }

                                            if (this.contadorAcao !== 'alterar_preco') {
                                                this.contadorDestinoOferta = '';
                                                return;
                                            }

                                            const destinationValues = this.activeCountdownDestinationOptions.map(option => option.value);
                                            if (this.contadorDestinoOferta && !destinationValues.includes(this.contadorDestinoOferta)) {
                                                this.contadorDestinoOferta = '';
                                            }
                                        },

                                        isPricingConfigReady() {
                                            if (this.usarContador !== '1') {
                                                return true;
                                            }

                                            if (!['1', '5', '10', '20', '30', '50'].includes(String(this.contadorMinutos || ''))) {
                                                return false;
                                            }

                                            const actionValues = this.countdownActionOptions.map(option => option.value);
                                            if (!actionValues.includes(this.contadorAcao)) {
                                                return false;
                                            }

                                            if (this.contadorAcao === 'alterar_preco') {
                                                return this.activeCountdownDestinationOptions.some(
                                                    option => option.value === this.contadorDestinoOferta
                                                );
                                            }

                                            return true;
                                        },

                                        triggerPricingSave(force = false) {
                                            if (!this.hasCodigoRef || this.isSavingPricing) return;
                                            this.normalizeCountdownSelections();
                                            if (!force && !this.isPricingConfigReady()) return;
                                            const form = this.$el.querySelector('.ref-form');
                                            if (!form) return;

                                            this.pricingSaveError = '';
                                            this.pricingSaveMessage = '';
                                            this.isSavingPricing = true;

                                            form.dataset.submitSource = 'pricing';
                                            form.requestSubmit();
                                        }
                                    }"
                                    x-init="normalizeCountdownSelections()"
                                    @ref-saved.window="
                                        if (Number($event.detail.cursoId) === {{ $curso->id }}) {
                                            hasCodigoRef = true;
                                            if ($event.detail.codigoRefId) codigoRefId = $event.detail.codigoRefId;
                                            if (typeof $event.detail.codigoRef !== 'undefined') codigoRef = $event.detail.codigoRef;
                                            if (typeof $event.detail.mostrarCurso !== 'undefined') mostrarCurso = !!$event.detail.mostrarCurso;
                                            if (typeof $event.detail.formularioPreCheckout !== 'undefined') formularioPreCheckout = $event.detail.formularioPreCheckout ? '1' : '0';
                                            if (typeof $event.detail.modoPrecos !== 'undefined') modoPrecos = $event.detail.modoPrecos;
                                            if (typeof $event.detail.cupomPrincipalId !== 'undefined') cupomPrincipalId = String($event.detail.cupomPrincipalId || '');
                                            if (typeof $event.detail.cupomSecundarioId !== 'undefined') cupomSecundarioId = String($event.detail.cupomSecundarioId || '');
                                            if (typeof $event.detail.usarContador !== 'undefined') usarContador = $event.detail.usarContador ? '1' : '0';
                                            if (typeof $event.detail.contadorMinutos !== 'undefined') contadorMinutos = String($event.detail.contadorMinutos || '');
                                            if (typeof $event.detail.contadorAcao !== 'undefined') contadorAcao = $event.detail.contadorAcao || '';
                                            if (typeof $event.detail.contadorDestinoOferta !== 'undefined') contadorDestinoOferta = $event.detail.contadorDestinoOferta || '';
                                            normalizeCountdownSelections();
                                            if (typeof $event.detail.baseCheckoutUrl !== 'undefined') baseCheckoutUrl = $event.detail.baseCheckoutUrl || '';
                                        }
                                    "
                                    @ref-save-finished.window="
                                        if (Number($event.detail.cursoId) === {{ $curso->id }} && $event.detail.source === 'pricing') {
                                            isSavingPricing = false;

                                            if ($event.detail.ok) {
                                                pricingSaveError = '';
                                                pricingSaveMessage = 'Salvo';
                                                setTimeout(() => { pricingSaveMessage = ''; }, 1400);
                                            } else {
                                                pricingSaveMessage = '';
                                                pricingSaveError = $event.detail.message || 'Não foi possível salvar.';
                                            }
                                        }
                                    "
                                    @bulk-mostrar-curso-updated.window="
                                        const ids = Array.isArray($event.detail.updatedCourseIds) ? $event.detail.updatedCourseIds.map(Number) : [];
                                        if (ids.includes({{ $curso->id }})) {
                                            mostrarCurso = $event.detail.action === 'ativar_todos';
                                        }
                                    "
                                    @bulk-public-page-config-updated.window="
                                        const ids = Array.isArray($event.detail.updatedCourseIds) ? $event.detail.updatedCourseIds.map(Number) : [];
                                        if (ids.includes({{ $curso->id }})) {
                                            formularioPreCheckout = $event.detail.formularioPreCheckout ? '1' : '0';
                                            modoPrecos = $event.detail.modoPrecos || 'padrao';
                                            cupomPrincipalId = String($event.detail.cupomPrincipalId || '');
                                            cupomSecundarioId = String($event.detail.cupomSecundarioId || '');
                                            usarContador = $event.detail.usarContador ? '1' : '0';
                                            contadorMinutos = String($event.detail.contadorMinutos || '');
                                            contadorAcao = $event.detail.contadorAcao || '';
                                            contadorDestinoOferta = $event.detail.contadorDestinoOferta || '';
                                            normalizeCountdownSelections();
                                            pricingSaveError = '';
                                            pricingSaveMessage = 'Salvo';
                                            setTimeout(() => { pricingSaveMessage = ''; }, 1400);
                                        }
                                    "
                                    class="bg-white shadow-sm rounded-lg border hover:shadow-md transition-shadow duration-200 flex flex-col h-full"
                                >
                                    <!-- Cabeçalho do Card -->
                                    <div class="p-6 flex-grow">
                                        <div class="flex justify-between items-start mb-3">
                                            <h5 class="font-bold text-gray-900 text-lg leading-tight">{{ $curso->titulo }} ({{ $curso->codigo_id_hotmart }})</h5>
                                            <span x-show="hasCodigoRef" class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                                Configurado
                                            </span>
                                            <span x-show="!hasCodigoRef" class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                                Pendente
                                            </span>
                                        </div>
                                        <div class="flex items-center mb-4">
                                            <i class="ri-price-tag-3-line text-blue-600 mr-2"></i>
                                            <span class="text-blue-600 font-semibold">{{ $curso->preco_cheio_completo }}</span>
                                        </div>


                                       
                                        
                                        
                                        <!-- Formulário do Código REF e Toggle (Unificado e CORRIGIDO) -->
                                        <form 
                                            id='form_{{$curso->id}}' 
                                            method="POST" 
                                            action="{{ route('cadastrar_codigo_ref', [], false) }}" 
                                            @submit.prevent
                                            class="space-y-4 ref-form"
                                        >
                                            @csrf
                                            <!-- Input do Código REF -->
                                            <div>
                                                <x-input-label for="codigo_ref_{{$curso->id}}" value="Código REF" class="text-sm font-medium" />
                                                <div class="flex mt-1">
                                                    <x-text-input 
                                                        id="codigo_ref_{{$curso->id}}" 
                                                        type="text" 
                                                        name="codigo_ref" 
                                                        placeholder="Ex: AFILIADO123" 
                                                        value="{{$curso->codigo_ref}}" 
                                                        class="je-dark-field rounded-r-none flex-1"
                                                        required 
                                                    />
                                                    <x-primary-button type="submit" class="rounded-l-none px-4 whitespace-nowrap">
                                                        <i class="ri-save-line mr-1"></i>
                                                        <span x-text="hasCodigoRef ? 'Atualizar' : 'Salvar'"></span>
                                                    </x-primary-button>
                                                </div>
                                            </div>

                                            <!-- Toggle Switch -->
                                            <div>
                                                <label for="mostrar_curso_{{$curso->id}}" class="flex items-center justify-between cursor-pointer">
                                                    <span class="text-sm font-medium text-gray-700">Mostrar curso no site?</span>
                                                    <div class="relative inline-flex items-center">
                                                        <input 
                                                            type="checkbox" 
                                                            id="mostrar_curso_{{$curso->id}}" 
                                                            name="mostrar_curso" 
                                                            class="sr-only peer"
                                                            value="1"
                                                            :checked="mostrarCurso"
                                                            @change="mostrarCurso = $event.target.checked; $nextTick(() => $el.form.requestSubmit())"
                                                        >
                                                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                                        <span x-text="mostrarCurso ? 'Sim' : 'Não'" class="ml-3 text-sm font-medium text-gray-900"></span>
                                                    </div>
                                                </label>
                                            </div>

                                            <template x-if="!hasCodigoRef">
                                                <div class="rounded-lg border border-yellow-200 bg-yellow-50 px-3 py-2 text-xs text-yellow-800">
                                                    Salve o Código REF para configurar os preços.
                                                </div>
                                            </template>

                                            <template x-if="hasCodigoRef">
                                                <div class="rounded-lg border border-blue-100 bg-blue-50/40 overflow-hidden">
                                                    <button
                                                        type="button"
                                                        @click="publicPageConfigExpanded = !publicPageConfigExpanded"
                                                        class="w-full px-4 py-3 text-left flex items-start justify-between gap-3 hover:bg-blue-100/50 transition-colors"
                                                    >
                                                        <div class="flex items-start gap-2">
                                                            <i class="ri-settings-3-line text-blue-600 mt-0.5"></i>
                                                            <div>
                                                                <p class="text-sm font-semibold text-gray-800">Configurações da página pública</p>
                                                                <p class="text-xs text-gray-600">Defina formulário pré-checkout e modelo de preços por curso.</p>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <span x-show="isSavingPricing" class="text-[11px] text-blue-600 font-medium">Salvando...</span>
                                                            <span x-show="!isSavingPricing && pricingSaveMessage" x-text="pricingSaveMessage" class="text-[11px] text-green-600 font-medium"></span>
                                                            <i class="ri-arrow-down-s-line text-gray-500 transition-transform" :class="publicPageConfigExpanded ? 'rotate-180' : ''"></i>
                                                        </div>
                                                    </button>

                                                    <div x-show="publicPageConfigExpanded" x-transition class="space-y-4 px-4 pb-4 border-t border-blue-100">
                                                        <p x-show="pricingSaveError" x-text="pricingSaveError" class="rounded-md border border-red-200 bg-red-50 px-2 py-1 text-xs text-red-700"></p>

                                                        <div>
                                                            <x-input-label for="formulario_pre_checkout_{{$curso->id}}" value="Formulário antes de continuar" class="text-sm font-medium" />
                                                            <div class="relative mt-1">
                                                                <select
                                                                    id="formulario_pre_checkout_{{$curso->id}}"
                                                                    name="formulario_pre_checkout"
                                                                    x-model="formularioPreCheckout"
                                                                    @change="triggerPricingSave()"
                                                                    :disabled="isSavingPricing"
                                                                    class="je-dark-field block w-full appearance-none rounded-lg px-3 py-2.5 pr-10 text-sm shadow-sm transition"
                                                                >
                                                                    <option value="1">Com formulário antes de continuar</option>
                                                                    <option value="0">Sem formulário (ir direto)</option>
                                                                </select>
                                                                <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                            </div>
                                                            <p class="mt-1 text-xs text-gray-500">Esta opção vale para botões de checkout e WhatsApp da página pública.</p>
                                                        </div>

                                                        <div>
                                                            <p class="text-sm font-medium text-gray-800">Preços na página pública</p>
                                                            <p class="text-xs text-gray-600">Defina se a página terá o modelo padrão, 1 plano ou 2 planos com cupons.</p>
                                                        </div>

                                                        <template x-if="!hasCuponsDisponiveis">
                                                            <div class="rounded-md border border-yellow-200 bg-yellow-50 px-3 py-2 text-xs text-yellow-800">
                                                                Nenhum cupom disponível. A página seguirá no modo padrão atual.
                                                            </div>
                                                        </template>

                                                        <template x-if="hasCuponsDisponiveis">
                                                            <div class="space-y-4">
                                                                <div>
                                                                    <x-input-label for="modo_precos_{{$curso->id}}" value="Tipo de página" class="text-sm font-medium" />
                                                                    <div class="relative mt-1">
                                                                        <select
                                                                            id="modo_precos_{{$curso->id}}"
                                                                            name="modo_precos"
                                                                            x-model="modoPrecos"
                                                                            @change="
                                                                                if (modoPrecos === 'padrao') {
                                                                                    cupomPrincipalId = '';
                                                                                    cupomSecundarioId = '';
                                                                                } else if (modoPrecos === 'um_preco') {
                                                                                    cupomSecundarioId = '';
                                                                                }
                                                                                normalizeCountdownSelections();
                                                                                triggerPricingSave();
                                                                            "
                                                                            :disabled="isSavingPricing"
                                                                            class="je-dark-field block w-full appearance-none rounded-lg px-3 py-2.5 pr-10 text-sm shadow-sm transition"
                                                                        >
                                                                            <option value="padrao">Página padrão</option>
                                                                            <option value="um_preco">Página com um plano</option>
                                                                            <option value="dois_precos">Página com dois planos</option>
                                                                        </select>
                                                                        <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                    </div>
                                                                    <p x-show="modoPrecos === 'padrao'" class="mt-1 text-xs text-gray-500">Usa o comportamento padrão da página.</p>
                                                                    <p x-show="modoPrecos === 'um_preco'" class="mt-1 text-xs text-gray-500">Exibe apenas o plano completo com o cupom escolhido.</p>
                                                                    <p x-show="modoPrecos === 'dois_precos'" class="mt-1 text-xs text-gray-500">Exibe plano completo e plano básico, cada um com seu cupom.</p>
                                                                </div>

                                                                <div x-show="modoPrecos === 'um_preco'" x-transition>
                                                                    <x-input-label for="cupom_principal_id_{{$curso->id}}" value="Preço do plano" class="text-sm font-medium" />
                                                                    <div class="relative mt-1">
                                                                        <select
                                                                            id="cupom_principal_id_{{$curso->id}}"
                                                                            name="cupom_principal_id"
                                                                            x-model="cupomPrincipalId"
                                                                            @change="normalizeCountdownSelections(); triggerPricingSave()"
                                                                            :disabled="isSavingPricing || modoPrecos !== 'um_preco'"
                                                                            class="je-dark-field block w-full appearance-none rounded-lg px-3 py-2.5 pr-10 text-sm shadow-sm transition"
                                                                        >
                                                                            <option value="">Padrão (sem cupom)</option>
                                                                            @foreach(($cupons ?? collect()) as $cupom)
                                                                                <option value="{{ $cupom->id }}">{{ $cupom->desconto }}% OFF ({{ $cupom->codigo }})</option>
                                                                            @endforeach
                                                                        </select>
                                                                        <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                    </div>
                                                                </div>

                                                                <div x-show="modoPrecos === 'dois_precos'" x-transition class="space-y-3">
                                                                    <div>
                                                                        <x-input-label for="cupom_principal_id_{{$curso->id}}" value="Preço do plano completo" class="text-sm font-medium" />
                                                                        <div class="relative mt-1">
                                                                            <select
                                                                                id="cupom_principal_id_{{$curso->id}}"
                                                                                name="cupom_principal_id"
                                                                                x-model="cupomPrincipalId"
                                                                                @change="
                                                                                    if (cupomSecundarioId && cupomSecundarioId === cupomPrincipalId) cupomSecundarioId = '';
                                                                                    normalizeCountdownSelections();
                                                                                    triggerPricingSave();
                                                                                "
                                                                                :disabled="isSavingPricing || modoPrecos !== 'dois_precos'"
                                                                                class="je-dark-field block w-full appearance-none rounded-lg px-3 py-2.5 pr-10 text-sm shadow-sm transition"
                                                                            >
                                                                                <option value="">Padrão (sem cupom)</option>
                                                                                @foreach(($cupons ?? collect()) as $cupom)
                                                                                    <option value="{{ $cupom->id }}">{{ $cupom->desconto }}% OFF ({{ $cupom->codigo }})</option>
                                                                                @endforeach
                                                                            </select>
                                                                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                        </div>
                                                                    </div>

                                                                    <div>
                                                                        <x-input-label for="cupom_secundario_id_{{$curso->id}}" value="Preço do plano básico" class="text-sm font-medium" />
                                                                        <div class="relative mt-1">
                                                                            <select
                                                                                id="cupom_secundario_id_{{$curso->id}}"
                                                                                name="cupom_secundario_id"
                                                                                x-model="cupomSecundarioId"
                                                                                @change="normalizeCountdownSelections(); triggerPricingSave()"
                                                                                :disabled="isSavingPricing || modoPrecos !== 'dois_precos'"
                                                                                class="je-dark-field block w-full appearance-none rounded-lg px-3 py-2.5 pr-10 text-sm shadow-sm transition"
                                                                            >
                                                                                <option value="">Selecione um cupom</option>
                                                                                @foreach(($cupons ?? collect()) as $cupom)
                                                                                    <option
                                                                                        value="{{ $cupom->id }}"
                                                                                        :disabled="cupomPrincipalId === '{{ $cupom->id }}'"
                                                                                    >
                                                                                        {{ $cupom->desconto }}% OFF ({{ $cupom->codigo }})
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>

                                                        <input x-show="!hasCuponsDisponiveis" :disabled="hasCuponsDisponiveis" type="hidden" name="modo_precos" value="padrao">
                                                        <input x-show="!hasCuponsDisponiveis" :disabled="hasCuponsDisponiveis" type="hidden" name="cupom_principal_id" value="">
                                                        <input x-show="!hasCuponsDisponiveis" :disabled="hasCuponsDisponiveis" type="hidden" name="cupom_secundario_id" value="">

                                                        <div class="rounded-md border border-blue-100 bg-white/70 p-4 space-y-4">
                                                            <div>
                                                                <p class="text-sm font-medium text-gray-800">Contador na página pública</p>
                                                                <p class="text-xs text-gray-600">O contador começa ao carregar a página e continua de onde parou no mesmo navegador.</p>
                                                            </div>

                                                            <div>
                                                                <x-input-label for="usar_contador_{{$curso->id}}" value="Usar contador?" class="text-sm font-medium" />
                                                                <div class="relative mt-1">
                                                                    <select
                                                                        id="usar_contador_{{$curso->id}}"
                                                                        name="usar_contador"
                                                                        x-model="usarContador"
                                                                        @change="
                                                                            if (usarContador !== '1') {
                                                                                contadorMinutos = '';
                                                                                contadorAcao = '';
                                                                                contadorDestinoOferta = '';
                                                                                triggerPricingSave(true);
                                                                            } else {
                                                                                normalizeCountdownSelections();
                                                                            }
                                                                        "
                                                                        :disabled="isSavingPricing"
                                                                        class="je-dark-field block w-full appearance-none rounded-lg px-3 py-2.5 pr-10 text-sm shadow-sm transition"
                                                                    >
                                                                        <option value="0">Não</option>
                                                                        <option value="1">Sim</option>
                                                                    </select>
                                                                    <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                </div>
                                                            </div>

                                                            <div x-show="usarContador === '1'" x-transition class="space-y-4">
                                                                <div>
                                                                    <x-input-label for="contador_minutos_{{$curso->id}}" value="Minutos" class="text-sm font-medium" />
                                                                    <div class="relative mt-1">
                                                                        <select
                                                                            id="contador_minutos_{{$curso->id}}"
                                                                            name="contador_minutos"
                                                                            x-model="contadorMinutos"
                                                                            @change="triggerPricingSave()"
                                                                            :disabled="isSavingPricing"
                                                                            class="je-dark-field block w-full appearance-none rounded-lg px-3 py-2.5 pr-10 text-sm shadow-sm transition"
                                                                        >
                                                                            <option value="">Selecione</option>
                                                                        <option value="1">1 minuto</option>
                                                                        <option value="5">5 minutos</option>
                                                                        <option value="10">10 minutos</option>
                                                                        <option value="20">20 minutos</option>
                                                                        <option value="30">30 minutos</option>
                                                                        <option value="50">50 minutos</option>
                                                                        </select>
                                                                        <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                    </div>
                                                                </div>

                                                                <div>
                                                                    <x-input-label for="contador_acao_{{$curso->id}}" value="Após o contador" class="text-sm font-medium" />
                                                                    <div class="relative mt-1">
                                                                        <select
                                                                            id="contador_acao_{{$curso->id}}"
                                                                            name="contador_acao"
                                                                            x-model="contadorAcao"
                                                                            @change="normalizeCountdownSelections(); triggerPricingSave()"
                                                                            :disabled="isSavingPricing"
                                                                            class="je-dark-field block w-full appearance-none rounded-lg px-3 py-2.5 pr-10 text-sm shadow-sm transition"
                                                                        >
                                                                            <option value="">Selecione</option>
                                                                            <template x-for="option in countdownActionOptions" :key="option.value">
                                                                                <option :value="option.value" x-text="option.label"></option>
                                                                            </template>
                                                                        </select>
                                                                        <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                    </div>
                                                                </div>

                                                                <div x-show="contadorAcao === 'alterar_preco'" x-transition>
                                                                    <x-input-label for="contador_destino_oferta_{{$curso->id}}" value="Alterar o preço para" class="text-sm font-medium" />
                                                                    <div class="relative mt-1">
                                                                        <select
                                                                            id="contador_destino_oferta_{{$curso->id}}"
                                                                            name="contador_destino_oferta"
                                                                            x-model="contadorDestinoOferta"
                                                                            @change="triggerPricingSave()"
                                                                            :disabled="isSavingPricing"
                                                                            class="je-dark-field block w-full appearance-none rounded-lg px-3 py-2.5 pr-10 text-sm shadow-sm transition"
                                                                        >
                                                                            <option value="">Selecione um preço</option>
                                                                            <template x-for="option in activeCountdownDestinationOptions" :key="option.value">
                                                                                <option :value="option.value" x-text="option.label"></option>
                                                                            </template>
                                                                        </select>
                                                                        <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- Inputs Ocultos (CORRIGIDOS) -->
                                            <div id="ref-form-feedback-{{$curso->id}}" class="mt-2 text-sm"></div>
                                            <input type="hidden" name="curso_id" value="{{$curso->id}}">
                                            <input type="hidden" name="id" :value="codigoRefId ?? ''">
                                            
                                            {{-- ADICIONADO: Input oculto para o título, necessário para a mensagem de sucesso --}}
                                            <input type="hidden" name="titulo" value="{{$curso->titulo}}">
                                            
                                            {{-- ADICIONADO: Input oculto para o user_id, necessário para a validação --}}
                                            <input type="hidden" name="user_id" value="{{Auth::user()->id}}">

                                        </form>
                                    </div>
                                    
                                    <!-- GERADOR DE LINKS DINÂMICOS -->
                                    <div x-show="hasCodigoRef" x-transition class="border-t border-gray-200 bg-gray-50">
                                        <!-- Botão para Expandir/Colapsar -->
                                        <button 
                                            @click="expanded = !expanded"
                                            class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-100 transition-colors"
                                        >
                                            <span class="font-semibold text-gray-800 flex items-center">
                                                <i class="ri-links-line mr-2 text-blue-600"></i>
                                                Gerador de Links
                                            </span>
                                            <i class="ri-arrow-down-s-line text-gray-500 transition-transform" :class="expanded ? 'rotate-180' : ''"></i>
                                        </button>

                                        <!-- Conteúdo Expansível -->
                                        <div x-show="expanded" x-transition class="px-6 pb-6">
                                            
                                            <!-- Gerador da Página de Vendas -->
                                            <div class="space-y-4 mb-6 p-4 bg-white rounded-lg border">
                                                <div class="flex items-center">
                                                    <i class="ri-pages-line text-blue-600 mr-2"></i>
                                                    <h6 class="font-semibold text-gray-800">Página de Vendas</h6>
                                                </div>
                                                
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50" :class="salesPageTarget === 'checkout' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                                        <input x-model="salesPageTarget" type="radio" value="checkout" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                                        <div class="ml-3">
                                                            <div class="text-sm font-medium text-gray-900">Checkout Direto</div>
                                                            <div class="text-xs text-gray-500">Leva direto para pagamento</div>
                                                        </div>
                                                    </label>
                                                    
                                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50" :class="salesPageTarget === 'whatsapp' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                                        <input x-model="salesPageTarget" type="radio" value="whatsapp" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                                        <div class="ml-3">
                                                            <div class="text-sm font-medium text-gray-900">WhatsApp</div>
                                                            <div class="text-xs text-gray-500">Contato via WhatsApp</div>
                                                        </div>
                                                    </label>
                                                </div>

                                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50" :class="salesPageGratuitas ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                                    <input x-model="salesPageGratuitas" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                    <div class="ml-3">
                                                        <div class="text-sm font-medium text-gray-900">Incluir Aulas Gratuitas</div>
                                                        <div class="text-xs text-gray-500">Mostra preview do conteúdo</div>
                                                    </div>
                                                </label>

                                               <div class="flex">
                                                    <div x-text="finalSalesUrl" class="flex items-center w-full border border-r-0 border-gray-300 rounded-l-md shadow-sm text-sm bg-gray-100 font-mono px-3 py-2 break-all"></div>
                                                    <button type="button" @click.prevent="copyLink(finalSalesUrl, 'sales')" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-r-md hover:bg-blue-500 text-sm transition-colors flex items-center w-28 justify-center">
                                                        <span x-show="!copiedSales" class="flex items-center"><i class="ri-file-copy-line mr-1"></i> Copiar</span>
                                                        <span x-show="copiedSales" x-transition class="flex items-center text-lime-300"><i class="ri-check-line mr-1"></i> Copiado!</span>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Gerador do Link de Checkout -->
                                            <div class="space-y-4 p-4 bg-white rounded-lg border">
                                                <div class="flex items-center">
                                                    <i class="ri-shopping-cart-line text-green-600 mr-2"></i>
                                                    <h6 class="font-semibold text-gray-800">Checkout - Página de Pagamento</h6>
                                                </div>
                                                
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50" :class="checkoutBoleto === 'com' ? 'border-green-500 bg-green-50' : 'border-gray-200'">
                                                        <input x-model="checkoutBoleto" type="radio" value="com" class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500">
                                                        <div class="ml-3">
                                                            <div class="text-sm font-medium text-gray-900">Com Boleto</div>
                                                            <div class="text-xs text-gray-500">Inclui opção de boleto</div>
                                                        </div>
                                                    </label>
                                                    
                                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50" :class="checkoutBoleto === 'sem' ? 'border-green-500 bg-green-50' : 'border-gray-200'">
                                                        <input x-model="checkoutBoleto" type="radio" value="sem" class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500">
                                                        <div class="ml-3">
                                                            <div class="text-sm font-medium text-gray-900">Sem Boleto</div>
                                                            <div class="text-xs text-gray-500">Apenas cartão/PIX</div>
                                                        </div>
                                                    </label>
                                                </div>

                                                <div>
                                                    @php
                                                        $precoBaseCurso = null;
                                                        $precoCheioRaw = (string) ($curso->preco_cheio_completo ?? '');
                                                        $precoNormalizado = preg_replace('/[^\d,.]/', '', $precoCheioRaw);

                                                        if (!empty($precoNormalizado)) {
                                                            if (str_contains($precoNormalizado, ',') && str_contains($precoNormalizado, '.')) {
                                                                $ultimaVirgula = strrpos($precoNormalizado, ',');
                                                                $ultimoPonto = strrpos($precoNormalizado, '.');

                                                                if ($ultimaVirgula !== false && $ultimoPonto !== false && $ultimaVirgula > $ultimoPonto) {
                                                                    $precoNormalizado = str_replace('.', '', $precoNormalizado);
                                                                    $precoNormalizado = str_replace(',', '.', $precoNormalizado);
                                                                } else {
                                                                    $precoNormalizado = str_replace(',', '', $precoNormalizado);
                                                                }
                                                            } elseif (str_contains($precoNormalizado, ',')) {
                                                                $precoNormalizado = str_replace('.', '', $precoNormalizado);
                                                                $precoNormalizado = str_replace(',', '.', $precoNormalizado);
                                                            } elseif (substr_count($precoNormalizado, '.') > 1) {
                                                                $partesPreco = explode('.', $precoNormalizado);
                                                                $decimalPreco = array_pop($partesPreco);
                                                                $precoNormalizado = implode('', $partesPreco) . '.' . $decimalPreco;
                                                            }

                                                            if (is_numeric($precoNormalizado)) {
                                                                $precoBaseCurso = (float) $precoNormalizado;
                                                            }
                                                        }
                                                    @endphp
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        <i class="ri-coupon-line mr-1"></i>
                                                        Cupom de Desconto
                                                    </label>
                                                    <select x-model="checkoutCupom" class="je-dark-field block w-full rounded-md shadow-sm text-sm" style="padding: 15px;">
                                                        <option value="">
                                                            @if($precoBaseCurso !== null)
                                                                Sem cupom - R${{ number_format($precoBaseCurso, 2, ',', '.') }}
                                                            @else
                                                                Sem cupom
                                                            @endif
                                                        </option>
                                                        @foreach(($cupons ?? collect()) as $cupom)
                                                            @if($precoBaseCurso !== null)
                                                                @php
                                                                    $precoComDesconto = $precoBaseCurso * (1 - ($cupom->desconto / 100));
                                                                @endphp
                                                                <option value="{{ $cupom->codigo }}">{{ $cupom->desconto }}% OFF ({{ $cupom->codigo }}) - R${{ number_format($precoComDesconto, 2, ',', '.') }}</option>
                                                            @else
                                                                <option value="{{ $cupom->codigo }}">{{ $cupom->desconto }}% OFF ({{ $cupom->codigo }})</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="flex">
                                                    <div x-text="finalCheckoutUrl" class="flex items-center w-full border border-r-0 border-gray-300 rounded-l-md shadow-sm text-sm bg-gray-100 font-mono px-3 py-2 break-all"></div>
                                                    <button type="button" @click.prevent="copyLink(finalCheckoutUrl, 'checkout')" class="px-4 py-2 bg-green-600 text-white font-semibold rounded-r-md hover:bg-green-500 text-sm transition-colors flex items-center w-28 justify-center">
                                                        <span x-show="!copiedCheckout" class="flex items-center"><i class="ri-file-copy-line mr-1"></i> Copiar</span>
                                                        <span x-show="copiedCheckout" x-transition class="flex items-center text-lime-300"><i class="ri-check-line mr-1"></i> Copiado!</span>
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div x-show="!hasCodigoRef" x-transition class="border-t border-gray-200 bg-yellow-50 p-4">
                                        <div class="flex items-center text-yellow-800">
                                            <i class="ri-information-line mr-2"></i>
                                            <span class="text-sm">Configure um código REF primeiro para gerar links personalizados</span>
                                        </div>
                                    </div>
                                    <!-- Bloco de Links Rápidos e Recursos -->
                                    <!-- NOVO: Acordeão de Recursos e Links Rápidos -->
                                    <div class="border-t border-gray-200 bg-gray-50">
                                        <!-- Botão para Expandir/Colapsar -->
                                        <button 
                                            @click="resourcesExpanded = !resourcesExpanded"
                                            class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-100 transition-colors"
                                        >
                                            <span class="font-semibold text-gray-800 flex items-center">
                                                <i class="ri-book-read-line mr-2 text-indigo-600"></i>
                                                Recursos e Links Rápidos
                                            </span>
                                            <i class="ri-arrow-down-s-line text-gray-500 transition-transform" :class="resourcesExpanded ? 'rotate-180' : ''"></i>
                                        </button>

                                        <!-- Conteúdo Expansível -->
                                        <div x-show="resourcesExpanded" x-transition class="px-6 pb-6 space-y-3">
                                            
                                            <!-- Link para Afiliar-se -->
                                            <a target="_blanck" href="{{ $curso->link_afiliacao }}" target="_blank" class="flex items-center text-sm text-indigo-600 hover:underline">
                                                <i class="ri-user-add-line w-5 mr-2"></i>
                                                <span>Afiliar-se a este curso</span>
                                                <i class="ri-external-link-line ml-1 text-gray-400"></i>
                                            </a>
                                            
                                            <!-- Link para Materiais de Divulgação -->
                                            <a target="_blanck" href="{{ $curso->link_materiais }}" target="_blank" class="flex items-center text-sm text-indigo-600 hover:underline">
                                                <i class="ri-folder-zip-line w-5 mr-2"></i>
                                                <span>Materiais de Divulgação</span>
                                                <i class="ri-external-link-line ml-1 text-gray-400"></i>
                                            </a>

                                            <!-- Link para Área de Membros (se o código REF estiver configurado) -->

                                                <a x-show="hasCodigoRef" target="_blanck" href="{{ $curso->link_area_membros }}" target="_blank" class="flex items-center text-sm text-indigo-600 hover:underline">
                                                    <i class="ri-shield-user-line w-5 mr-2"></i>
                                                    <span>Acessar Área de Membros</span>
                                                    <i class="ri-external-link-line ml-1 text-gray-400"></i>
                                                </a>


                                            @if($curso->video_dentro_do_curso)
                                                        <a target="_blanck" href="https://www.youtube.com/watch?v={{$curso->video_dentro_do_curso}}" class="flex items-center text-sm text-indigo-600 hover:underline">
                                                            <i class="ri-movie-2-line w-5 mr-2"></i>
                                                            <span>Ver por dentro do curso</span>
                                                        </a>
                                            @endif
                                            @if($curso->video_apresentacao)
                                                        <a target="_blanck" href="https://www.youtube.com/watch?v={{$curso->video_apresentacao}}" class="flex items-center text-sm text-indigo-600 hover:underline mt-2">
                                                            <i class="ri-slideshow-3-line w-5 mr-2"></i>
                                                            <span>Vídeo de Apresentação</span>
                                                        </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Mensagem quando não há resultados (Lógica Corrigida e Simplificada) -->
                <div 
                    x-show="search && $el.parentElement.querySelectorAll('.lista_cursos:not([style*=\'display: none\'])').length === 0" 
                    x-transition
                    class="text-center py-16"
                >
                    <i class="ri-search-eye-line text-gray-400 text-6xl"></i>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Nenhum curso encontrado</h3>
                    <p class="mt-1 text-sm text-gray-500">Não encontramos cursos com o termo "<span x-text="search" class="font-semibold"></span>".</p>
                </div>
            </div>
        </div>
    </div>
@push('styles')
    <style>
        .je-dark-surface {
            background-color: #181b1e !important;
            border-color: #303030 !important;
            color: #f5f5f5 !important;
        }

        .je-dark-divider {
            border-color: #303030 !important;
        }

        .je-dark-muted {
            color: #a0a0a0 !important;
        }

        .je-dark-label {
            color: #f5f5f5 !important;
        }

        .je-dark-field {
            background-color: #333 !important;
            border: 1px solid #555 !important;
            color: #fff !important;
        }

        .je-dark-field::placeholder {
            color: #fff !important;
            opacity: 1;
        }

        .je-dark-field:focus {
            background-color: #333 !important;
            border-color: #28a745 !important;
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5) !important;
            color: #fff !important;
            outline: none !important;
        }

        .je-dark-field:disabled {
            background-color: #2f2f2f !important;
            border-color: #4a4a4a !important;
            color: #bfbfbf !important;
            opacity: 1;
        }

        .je-dark-field option {
            background-color: #222;
            color: #fff;
        }
    </style>
@endpush
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const refForms = document.querySelectorAll('.ref-form');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            refForms.forEach(form => {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    if (form.dataset.submitting === '1') {
                        return;
                    }

                    const submitSource = form.dataset.submitSource === 'pricing' ? 'pricing' : 'manual';
                    form.dataset.submitting = '1';

                    const cursoId = form.querySelector('input[name="curso_id"]').value;
                    const feedbackDiv = document.getElementById(`ref-form-feedback-${cursoId}`);
                    
                    // CORREÇÃO: Criamos o FormData AQUI
                    const formData = new FormData(form);

                    // VERIFICAÇÃO DO CHECKBOX: O FormData só inclui checkboxes se estiverem marcados.
                    // Se não estiver marcado, o controller não receberá o campo 'mostrar_curso'.
                    // Para garantir que o controller sempre saiba o estado, adicionamos manualmente se estiver desmarcado.
                    if (!formData.has('mostrar_curso')) {
                        formData.append('mostrar_curso', '0');
                    }

                    const usarContador = formData.get('usar_contador') === '1';
                    const contadorMinutos = String(formData.get('contador_minutos') || '');
                    const contadorAcao = String(formData.get('contador_acao') || '');
                    const contadorDestinoOferta = String(formData.get('contador_destino_oferta') || '');
                    const modoPrecosAtual = String(formData.get('modo_precos') || 'padrao');

                    let countdownValidationMessage = '';
                    if (usarContador) {
                        if (!['1', '5', '10', '20', '30', '50'].includes(contadorMinutos)) {
                            countdownValidationMessage = 'Selecione os minutos do contador.';
                        } else if (!['nada', 'encerrar_basico', 'alterar_preco', 'whatsapp'].includes(contadorAcao)) {
                            countdownValidationMessage = 'Selecione a ação do contador.';
                        } else if (contadorAcao === 'encerrar_basico' && modoPrecosAtual === 'um_preco') {
                            countdownValidationMessage = 'A ação de encerrar o plano básico só pode ser usada quando a página exibe o plano básico.';
                        } else if (contadorAcao === 'alterar_preco' && !contadorDestinoOferta) {
                            countdownValidationMessage = 'Selecione o preço que será mostrado após o contador.';
                        }
                    }

                    if (submitSource === 'manual') {
                        feedbackDiv.innerHTML = '';
                    }

                    if (countdownValidationMessage) {
                        if (submitSource === 'manual') {
                            feedbackDiv.innerHTML = '';
                            const countdownParagraph = document.createElement('p');
                            countdownParagraph.className = 'text-red-600';
                            countdownParagraph.textContent = countdownValidationMessage;
                            feedbackDiv.appendChild(countdownParagraph);
                        }

                        if (submitSource === 'pricing') {
                            window.dispatchEvent(new CustomEvent('ref-save-finished', {
                                detail: {
                                    cursoId: Number(cursoId),
                                    source: 'pricing',
                                    ok: false,
                                    message: countdownValidationMessage
                                }
                            }));
                        }

                        form.dataset.submitting = '0';
                        form.dataset.submitSource = 'manual';
                        return;
                    }

                    fetch(form.action, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(async response => {
                        const contentType = response.headers.get('content-type') || '';
                        const isJson = contentType.includes('application/json');
                        const payload = isJson
                            ? await response.json()
                            : { message: 'Erro inesperado no servidor.' };

                        if (!response.ok) {
                            throw payload;
                        }

                        return payload;
                    })
                    .then(data => {
                        if (!data.success) {
                            throw { message: 'Não foi possível salvar as configurações.' };
                        }

                        if (typeof data.codigo_ref_id !== 'undefined') {
                            const idInput = form.querySelector('input[name="id"]');
                            if (idInput) {
                                idInput.value = data.codigo_ref_id ?? '';
                            }
                        }

                        window.dispatchEvent(new CustomEvent('ref-saved', {
                            detail: {
                                cursoId: Number(cursoId),
                                codigoRefId: data.codigo_ref_id ?? null,
                                codigoRef: data.codigo_ref ?? formData.get('codigo_ref'),
                                mostrarCurso: typeof data.mostrar_curso === 'boolean'
                                    ? data.mostrar_curso
                                    : formData.get('mostrar_curso') === '1',
                                formularioPreCheckout: typeof data.formulario_pre_checkout === 'boolean'
                                    ? data.formulario_pre_checkout
                                    : formData.get('formulario_pre_checkout') !== '0',
                                modoPrecos: data.modo_precos ?? formData.get('modo_precos') ?? 'padrao',
                                cupomPrincipalId: data.cupom_principal_id ?? formData.get('cupom_principal_id') ?? '',
                                cupomSecundarioId: data.cupom_secundario_id ?? formData.get('cupom_secundario_id') ?? '',
                                usarContador: typeof data.usar_contador === 'boolean'
                                    ? data.usar_contador
                                    : formData.get('usar_contador') === '1',
                                contadorMinutos: data.contador_minutos ?? formData.get('contador_minutos') ?? '',
                                contadorAcao: data.contador_acao ?? formData.get('contador_acao') ?? '',
                                contadorDestinoOferta: data.contador_destino_oferta ?? formData.get('contador_destino_oferta') ?? '',
                                baseCheckoutUrl: data.base_checkout_url ?? ''
                            }
                        }));

                        if (submitSource === 'pricing') {
                            feedbackDiv.innerHTML = '';
                            window.dispatchEvent(new CustomEvent('ref-save-finished', {
                                detail: {
                                    cursoId: Number(cursoId),
                                    source: 'pricing',
                                    ok: true,
                                    message: 'Salvo'
                                }
                            }));
                            return;
                        }

                        feedbackDiv.innerHTML = '';
                        const successParagraph = document.createElement('p');
                        successParagraph.className = 'text-green-600';
                        successParagraph.textContent = data.success;
                        feedbackDiv.appendChild(successParagraph);
                        window.dispatchEvent(new CustomEvent('show-success', { detail: data.success }));
                    })
                    .catch(errorData => {
                        let compactError = 'Ocorreu um erro inesperado. Tente novamente.';

                        if (errorData && errorData.errors) {
                            const allErrors = Object.values(errorData.errors);
                            compactError = allErrors?.[0]?.[0] ?? 'Erro de validação.';

                            if (submitSource === 'manual') {
                                let errorHtml = '<ul class="text-red-600 list-disc list-inside">';
                                allErrors.forEach(error => {
                                    errorHtml += `<li>${error[0]}</li>`;
                                });
                                errorHtml += '</ul>';
                                feedbackDiv.innerHTML = errorHtml;
                            }
                        } else {
                            console.error('Erro:', errorData);
                            compactError = (errorData && errorData.message)
                                ? errorData.message
                                : compactError;

                            if (submitSource === 'manual') {
                                feedbackDiv.innerHTML = '';
                                const paragraph = document.createElement('p');
                                paragraph.className = 'text-red-600';
                                paragraph.textContent = compactError;
                                feedbackDiv.appendChild(paragraph);
                            }
                        }

                        if (submitSource === 'pricing') {
                            window.dispatchEvent(new CustomEvent('ref-save-finished', {
                                detail: {
                                    cursoId: Number(cursoId),
                                    source: 'pricing',
                                    ok: false,
                                    message: compactError
                                }
                            }));
                        }
                    })
                    .finally(() => {
                        form.dataset.submitting = '0';
                        form.dataset.submitSource = 'manual';
                    });
                });
            });
        });
    </script>
@endpush
</x-app-layout>
