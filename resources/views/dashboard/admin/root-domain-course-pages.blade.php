<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Páginas dos Domínios') }}
        </h2>
    </x-slot>

    @php
        $hasCuponsDisponiveis = ($cupons ?? collect())->isNotEmpty();
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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                x-data="{
                    search: '',
                    showSuccessMessage: false,
                    successMessage: '',
                    showTutorial: false,
                    rootDomainBulkPublicConfigModalOpen: false,
                    isBulkPublicConfigLoading: false,
                    rootDomainBulkPublicConfigError: '',
                    bulkPublicConfigFormularioPreCheckout: '1',
                    bulkPublicConfigModoPrecos: 'padrao',
                    bulkPublicConfigCupomPrincipalId: '',
                    bulkPublicConfigCupomSecundarioId: '',
                    bulkPublicConfigUsarContador: '0',
                    bulkPublicConfigContadorMinutos: '',
                    bulkPublicConfigContadorAcao: '',
                    bulkPublicConfigContadorDestinoOferta: '',
                    bulkCountdownDestinationOptions: @js($bulkCountdownDestinationOptions),
                    hasCuponsDisponiveis: {{ $hasCuponsDisponiveis ? 'true' : 'false' }},
                    bulkActionsUrl: @js(route('admin.root_domain_course_pages.bulk_actions', [], false)),
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
                    openRootDomainBulkPublicConfigModal() {
                        this.normalizeBulkPublicConfigSelections();
                        this.rootDomainBulkPublicConfigError = '';
                        this.rootDomainBulkPublicConfigModalOpen = true;
                    },
                    closeRootDomainBulkPublicConfigModal() {
                        if (this.isBulkPublicConfigLoading) return;
                        this.rootDomainBulkPublicConfigModalOpen = false;
                        this.rootDomainBulkPublicConfigError = '';
                    },
                    get bulkCountdownActionOptions() {
                        if (this.bulkPublicConfigModoPrecos === 'um_preco') {
                            return [
                                { value: 'nada', label: 'Nada' },
                                { value: 'alterar_preco', label: 'Alterar o preço para' },
                            ];
                        }

                        return [
                            { value: 'nada', label: 'Nada' },
                            { value: 'encerrar_basico', label: 'Encerrar o plano básico' },
                            { value: 'alterar_preco', label: 'Alterar o preço para' },
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
                    isBulkPublicConfigReady() {
                        if (this.bulkPublicConfigUsarContador !== '1') {
                            return true;
                        }

                        if (!['1', '5', '10', '20', '30', '50'].includes(String(this.bulkPublicConfigContadorMinutos || ''))) {
                            return false;
                        }

                        const actionValues = this.bulkCountdownActionOptions.map(option => option.value);
                        if (!actionValues.includes(this.bulkPublicConfigContadorAcao)) {
                            return false;
                        }

                        if (this.bulkPublicConfigContadorAcao === 'alterar_preco') {
                            return this.activeBulkCountdownDestinationOptions.some(
                                option => option.value === this.bulkPublicConfigContadorDestinoOferta
                            );
                        }

                        return true;
                    },
                    applyRootDomainBulkPublicPageConfig() {
                        if (this.isBulkPublicConfigLoading) return;

                        const csrfToken = this.getCsrfToken();
                        if (!csrfToken) {
                            this.rootDomainBulkPublicConfigError = 'Token de segurança não encontrado.';
                            return;
                        }

                        this.normalizeBulkPublicConfigSelections();
                        if (!this.isBulkPublicConfigReady()) {
                            this.rootDomainBulkPublicConfigError = 'Preencha corretamente a configuração do contador antes de aplicar em massa.';
                            return;
                        }

                        this.isBulkPublicConfigLoading = true;
                        this.rootDomainBulkPublicConfigError = '';

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

                        if (payload.usar_contador !== '1') {
                            payload.contador_minutos = null;
                            payload.contador_acao = null;
                            payload.contador_destino_oferta = null;
                        } else if (payload.contador_acao !== 'alterar_preco') {
                            payload.contador_destino_oferta = null;
                        }

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

                            window.dispatchEvent(new CustomEvent('root-domain-bulk-public-page-config-updated', {
                                detail: {
                                    updatedCourseIds: Array.isArray(data.updated_course_ids) ? data.updated_course_ids : [],
                                    configIdsByCourse: data.config_ids_by_course || {},
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

                            self.rootDomainBulkPublicConfigModalOpen = false;
                            window.dispatchEvent(new CustomEvent('show-success', {
                                detail: data.message || 'Configurações em massa aplicadas com sucesso.',
                            }));
                        })
                        .catch(function (error) {
                            self.rootDomainBulkPublicConfigError = self.parseBulkActionError(
                                error,
                                'Não foi possível aplicar as configurações em massa.'
                            );
                        })
                        .finally(function () {
                            self.isBulkPublicConfigLoading = false;
                        });
                    },
                }"
                @show-success.window="showSuccessMessage = true; successMessage = $event.detail; setTimeout(() => showSuccessMessage = false, 3000)"
                @keydown.escape.window="if (rootDomainBulkPublicConfigModalOpen && !isBulkPublicConfigLoading) closeRootDomainBulkPublicConfigModal()"
            >
                <div x-show="showSuccessMessage" x-transition class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    <span x-text="successMessage"></span>
                </div>

                @if (session('error'))
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="bg-white overflow-visible shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-start gap-4">
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold flex items-center">
                                    <i class="ri-global-line mr-2 text-blue-600"></i>
                                    Configuração compartilhada dos domínios raiz
                                    <button @click="showTutorial = !showTutorial" class="ml-2 text-blue-500 hover:text-blue-700 transition-colors">
                                        <i class="ri-question-fill text-xl"></i>
                                    </button>
                                </h4>
                                <p class="mt-1 text-sm text-gray-600">
                                    As configurações desta tela valem para {{ implode(' e ', $sharedDomains) }}.
                                </p>
                            </div>
                        </div>

                        <div x-show="showTutorial" x-transition class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <h5 class="font-semibold text-blue-800 mb-2">Como usar esta página:</h5>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• Controle quais cursos aparecem nos domínios raiz</li>
                                <li>• Configure formulário, preços, cupons e contador por curso</li>
                                <li>• As alterações abaixo valem ao mesmo tempo para portalje.org e jovemempreendedor.org</li>
                                <li>• Os aliases dns.portalje.org e dns.jovemempreendedor.org seguem a mesma configuração</li>
                            </ul>
                        </div>

                        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="relative max-w-md w-full">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="ri-search-line text-gray-400"></i>
                                </div>
                                <x-text-input
                                    x-model.debounce.300ms="search"
                                    id="adminRootDomainCourseSearch"
                                    class="block w-full pl-10"
                                    type="text"
                                    placeholder="Buscar curso..."
                                />
                                <div x-show="search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <button @click="search = ''" class="text-gray-400 hover:text-gray-600" type="button">
                                        <i class="ri-close-line"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 sm:flex-shrink-0">
                                <button
                                    type="button"
                                    @click="openRootDomainBulkPublicConfigModal()"
                                    :disabled="isBulkPublicConfigLoading"
                                    class="inline-flex items-center justify-center rounded-md border border-blue-300 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 shadow-sm transition hover:bg-blue-100 disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    <i class="ri-sliders-line mr-2"></i>
                                    <span x-text="isBulkPublicConfigLoading ? 'Aplicando...' : 'Configurar página pública em massa'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    x-show="rootDomainBulkPublicConfigModalOpen"
                    x-transition.opacity
                    class="fixed inset-0 z-40 bg-slate-900/50"
                    @click="closeRootDomainBulkPublicConfigModal()"
                ></div>

                <div
                    x-show="rootDomainBulkPublicConfigModalOpen"
                    x-transition
                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                >
                    <div @click.stop class="je-dark-surface w-full max-w-2xl overflow-hidden rounded-xl border shadow-xl">
                        <div class="je-dark-divider flex items-center justify-between border-b px-6 py-4">
                            <h3 class="text-lg font-semibold text-white">Configurações da página pública em massa</h3>
                            <button
                                type="button"
                                @click="closeRootDomainBulkPublicConfigModal()"
                                :disabled="isBulkPublicConfigLoading"
                                class="rounded-md p-1 text-slate-300 transition hover:bg-slate-700 hover:text-white disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <i class="ri-close-line text-xl"></i>
                            </button>
                        </div>

                        <div class="space-y-5 px-6 py-5">
                            <p class="je-dark-muted text-sm">
                                Esta configuração será aplicada a todos os cursos publicados dos domínios raiz.
                            </p>

                            <p
                                x-show="rootDomainBulkPublicConfigError"
                                x-text="rootDomainBulkPublicConfigError"
                                class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"
                            ></p>

                            <div>
                                <x-input-label for="root_bulk_formulario_pre_checkout" value="Formulário antes de continuar" class="je-dark-label text-sm font-medium" />
                                <div class="relative mt-1">
                                    <select
                                        id="root_bulk_formulario_pre_checkout"
                                        x-model="bulkPublicConfigFormularioPreCheckout"
                                        :disabled="isBulkPublicConfigLoading"
                                        class="je-dark-field block w-full appearance-none rounded-lg px-3 py-2.5 pr-10 text-sm shadow-sm transition"
                                    >
                                        <option value="1">Com formulário antes de continuar</option>
                                        <option value="0">Sem formulário (ir direto)</option>
                                    </select>
                                    <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                </div>
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
                                        <x-input-label for="root_bulk_modo_precos" value="Tipo de página" class="je-dark-label text-sm font-medium" />
                                        <div class="relative mt-1">
                                            <select
                                                id="root_bulk_modo_precos"
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
                                        <x-input-label for="root_bulk_cupom_principal_id_um_preco" value="Preço do plano" class="je-dark-label text-sm font-medium" />
                                        <div class="relative mt-1">
                                            <select
                                                id="root_bulk_cupom_principal_id_um_preco"
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
                                            <x-input-label for="root_bulk_cupom_principal_id_dois_precos" value="Preço do plano completo" class="je-dark-label text-sm font-medium" />
                                            <div class="relative mt-1">
                                                <select
                                                    id="root_bulk_cupom_principal_id_dois_precos"
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
                                            <x-input-label for="root_bulk_cupom_secundario_id_dois_precos" value="Preço do plano básico" class="je-dark-label text-sm font-medium" />
                                            <div class="relative mt-1">
                                                <select
                                                    id="root_bulk_cupom_secundario_id_dois_precos"
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

                            <div class="rounded-md border border-blue-100 bg-white/10 p-4 space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-white">Contador na página pública</p>
                                    <p class="je-dark-muted text-xs">O contador começa ao carregar a página e continua de onde parou no mesmo navegador.</p>
                                </div>

                                <div>
                                    <x-input-label for="root_bulk_usar_contador" value="Usar contador?" class="je-dark-label text-sm font-medium" />
                                    <div class="relative mt-1">
                                        <select
                                            id="root_bulk_usar_contador"
                                            x-model="bulkPublicConfigUsarContador"
                                            @change="normalizeBulkPublicConfigSelections()"
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
                                        <x-input-label for="root_bulk_contador_minutos" value="Minutos" class="je-dark-label text-sm font-medium" />
                                        <div class="relative mt-1">
                                            <select
                                                id="root_bulk_contador_minutos"
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
                                        <x-input-label for="root_bulk_contador_acao" value="Após o contador" class="je-dark-label text-sm font-medium" />
                                        <div class="relative mt-1">
                                            <select
                                                id="root_bulk_contador_acao"
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
                                        <x-input-label for="root_bulk_contador_destino_oferta" value="Alterar o preço para" class="je-dark-label text-sm font-medium" />
                                        <div class="relative mt-1">
                                            <select
                                                id="root_bulk_contador_destino_oferta"
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
                                @click="closeRootDomainBulkPublicConfigModal()"
                                :disabled="isBulkPublicConfigLoading"
                                class="inline-flex items-center justify-center rounded-md border border-slate-500 bg-slate-700 px-4 py-2 text-sm font-medium text-slate-100 transition hover:bg-slate-600 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                Cancelar
                            </button>
                            <button
                                type="button"
                                @click="applyRootDomainBulkPublicPageConfig()"
                                :disabled="isBulkPublicConfigLoading"
                                class="inline-flex items-center justify-center rounded-md border border-blue-600 bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <i class="ri-save-line mr-2"></i>
                                <span x-text="isBulkPublicConfigLoading ? 'Aplicando...' : 'Aplicar em todos'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($cursos as $curso)
                        @if($curso->publicado)
                            @php
                                $parseCourseMonetary = static function ($value): ?float {
                                    if ($value === null) {
                                        return null;
                                    }

                                    $normalized = preg_replace('/[^\d,.]/', '', (string) $value);
                                    if ($normalized === null || $normalized === '') {
                                        return null;
                                    }

                                    if (str_contains($normalized, ',') && str_contains($normalized, '.')) {
                                        $lastComma = strrpos($normalized, ',');
                                        $lastDot = strrpos($normalized, '.');

                                        if ($lastComma !== false && $lastDot !== false && $lastComma > $lastDot) {
                                            $normalized = str_replace('.', '', $normalized);
                                            $normalized = str_replace(',', '.', $normalized);
                                        } else {
                                            $normalized = str_replace(',', '', $normalized);
                                        }
                                    } elseif (str_contains($normalized, ',')) {
                                        $normalized = str_replace('.', '', $normalized);
                                        $normalized = str_replace(',', '.', $normalized);
                                    } elseif (substr_count($normalized, '.') > 1) {
                                        $parts = explode('.', $normalized);
                                        $decimal = array_pop($parts);
                                        $normalized = implode('', $parts) . '.' . $decimal;
                                    }

                                    return is_numeric($normalized) ? (float) $normalized : null;
                                };

                                $formatCourseCurrency = static function (?float $value): ?string {
                                    return $value !== null ? 'R$' . number_format($value, 2, ',', '.') : null;
                                };

                                $searchTitle = mb_strtolower($curso->titulo ?? '');
                                $hasCuponsDisponiveis = ($cupons ?? collect())->isNotEmpty();
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
                            <div
                                x-data="{
                                    configId: @js($curso->root_domain_course_config_id),
                                    mostrarCurso: {{ $curso->mostrar_curso ? 'true' : 'false' }},
                                    publicPageConfigExpanded: false,
                                    formularioPreCheckout: @js($curso->formulario_pre_checkout ? '1' : '0'),
                                    modoPrecos: @js($curso->modo_precos ?? 'padrao'),
                                    cupomPrincipalId: @js(!empty($curso->cupom_principal_id) ? (string) $curso->cupom_principal_id : ''),
                                    cupomSecundarioId: @js(!empty($curso->cupom_secundario_id) ? (string) $curso->cupom_secundario_id : ''),
                                    usarContador: @js(!empty($curso->usar_contador) ? '1' : '0'),
                                    contadorMinutos: @js(!empty($curso->contador_minutos) ? (string) $curso->contador_minutos : ''),
                                    contadorAcao: @js($curso->contador_acao ?? ''),
                                    contadorDestinoOferta: @js($curso->contador_destino_oferta ?? ''),
                                    countdownDestinationOptions: @js($countdownDestinationOptions),
                                    hasCuponsDisponiveis: {{ $hasCuponsDisponiveis ? 'true' : 'false' }},
                                    isSavingPricing: false,
                                    pricingSaveMessage: '',
                                    pricingSaveError: '',

                                    get countdownActionOptions() {
                                        if (this.modoPrecos === 'um_preco') {
                                            return [
                                                { value: 'nada', label: 'Nada' },
                                                { value: 'alterar_preco', label: 'Alterar o preço para' },
                                            ];
                                        }

                                        return [
                                            { value: 'nada', label: 'Nada' },
                                            { value: 'encerrar_basico', label: 'Encerrar o plano básico' },
                                            { value: 'alterar_preco', label: 'Alterar o preço para' },
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
                                            return this.activeCountdownDestinationOptions.some(option => option.value === this.contadorDestinoOferta);
                                        }

                                        return true;
                                    },

                                    triggerPricingSave(force = false) {
                                        if (this.isSavingPricing) return;
                                        this.normalizeCountdownSelections();
                                        if (!force && !this.isPricingConfigReady()) return;
                                        const form = this.$el.querySelector('.root-domain-config-form');
                                        if (!form) return;

                                        this.pricingSaveError = '';
                                        this.pricingSaveMessage = '';
                                        this.isSavingPricing = true;
                                        form.dataset.submitSource = 'pricing';
                                        form.requestSubmit();
                                    }
                                }"
                                x-init="normalizeCountdownSelections()"
                                @root-domain-config-saved.window="
                                    if (Number($event.detail.cursoId) === {{ $curso->id }}) {
                                        configId = $event.detail.configId ?? configId;
                                        mostrarCurso = !!$event.detail.mostrarCurso;
                                        formularioPreCheckout = $event.detail.formularioPreCheckout ? '1' : '0';
                                        modoPrecos = $event.detail.modoPrecos || 'padrao';
                                        cupomPrincipalId = String($event.detail.cupomPrincipalId || '');
                                        cupomSecundarioId = String($event.detail.cupomSecundarioId || '');
                                        usarContador = $event.detail.usarContador ? '1' : '0';
                                        contadorMinutos = String($event.detail.contadorMinutos || '');
                                        contadorAcao = $event.detail.contadorAcao || '';
                                        contadorDestinoOferta = $event.detail.contadorDestinoOferta || '';
                                        normalizeCountdownSelections();
                                    }
                                "
                                @root-domain-config-save-finished.window="
                                    if (Number($event.detail.cursoId) === {{ $curso->id }}) {
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
                                @root-domain-bulk-public-page-config-updated.window="
                                    const ids = Array.isArray($event.detail.updatedCourseIds) ? $event.detail.updatedCourseIds.map(Number) : [];
                                    if (ids.includes({{ $curso->id }})) {
                                        const configIdsByCourse = $event.detail.configIdsByCourse || {};
                                        if (typeof configIdsByCourse['{{ $curso->id }}'] !== 'undefined') {
                                            configId = configIdsByCourse['{{ $curso->id }}'];
                                        }
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
                                x-show="search === '' || @js($searchTitle).includes(search.toLowerCase())"
                                class="lista_cursos bg-white shadow-sm rounded-lg border hover:shadow-md transition-shadow duration-200 flex flex-col h-full"
                            >
                                <div class="p-6 flex-grow">
                                    <div class="flex justify-between items-start mb-3 gap-3">
                                        <h5 class="font-bold text-gray-900 text-lg leading-tight">{{ $curso->titulo }} ({{ $curso->codigo_id_hotmart }})</h5>
                                        <span x-show="configId" class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full whitespace-nowrap">
                                            Personalizado
                                        </span>
                                        <span x-show="!configId" class="bg-gray-100 text-gray-700 text-xs font-medium px-2.5 py-0.5 rounded-full whitespace-nowrap">
                                            Padrão do portal
                                        </span>
                                    </div>

                                    <div class="flex items-center mb-4">
                                        <i class="ri-price-tag-3-line text-blue-600 mr-2"></i>
                                        <span class="text-blue-600 font-semibold">{{ $curso->preco_cheio_completo }}</span>
                                    </div>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.root_domain_course_pages.save', [], false) }}"
                                        @submit.prevent
                                        class="space-y-4 root-domain-config-form"
                                    >
                                        @csrf

                                        <div>
                                            <label for="mostrar_curso_root_{{ $curso->id }}" class="flex items-center justify-between cursor-pointer">
                                                <span class="text-sm font-medium text-gray-700">Mostrar curso nos domínios raiz?</span>
                                                <div class="relative inline-flex items-center">
                                                    <input
                                                        type="checkbox"
                                                        id="mostrar_curso_root_{{ $curso->id }}"
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
                                            <p class="mt-1 text-xs text-gray-500">Vale para portalje.org e jovemempreendedor.org.</p>
                                        </div>

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
                                                        <p class="text-xs text-gray-600">Formulário, preços, cupons e contador compartilhados entre os domínios raiz.</p>
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
                                                    <x-input-label for="root_formulario_pre_checkout_{{ $curso->id }}" value="Formulário antes de continuar" class="text-sm font-medium" />
                                                    <div class="relative mt-1">
                                                        <select
                                                            id="root_formulario_pre_checkout_{{ $curso->id }}"
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
                                                            <x-input-label for="root_modo_precos_{{ $curso->id }}" value="Tipo de página" class="text-sm font-medium" />
                                                            <div class="relative mt-1">
                                                                <select
                                                                    id="root_modo_precos_{{ $curso->id }}"
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
                                                        </div>

                                                        <div x-show="modoPrecos === 'um_preco'" x-transition>
                                                            <x-input-label for="root_cupom_principal_id_{{ $curso->id }}" value="Preço do plano" class="text-sm font-medium" />
                                                            <div class="relative mt-1">
                                                                <select
                                                                    id="root_cupom_principal_id_{{ $curso->id }}"
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
                                                                <x-input-label for="root_cupom_principal_id_dois_{{ $curso->id }}" value="Preço do plano completo" class="text-sm font-medium" />
                                                                <div class="relative mt-1">
                                                                    <select
                                                                        id="root_cupom_principal_id_dois_{{ $curso->id }}"
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
                                                                <x-input-label for="root_cupom_secundario_id_{{ $curso->id }}" value="Preço do plano básico" class="text-sm font-medium" />
                                                                <div class="relative mt-1">
                                                                    <select
                                                                        id="root_cupom_secundario_id_{{ $curso->id }}"
                                                                        name="cupom_secundario_id"
                                                                        x-model="cupomSecundarioId"
                                                                        @change="normalizeCountdownSelections(); triggerPricingSave()"
                                                                        :disabled="isSavingPricing || modoPrecos !== 'dois_precos'"
                                                                        class="je-dark-field block w-full appearance-none rounded-lg px-3 py-2.5 pr-10 text-sm shadow-sm transition"
                                                                    >
                                                                        <option value="">Selecione um cupom</option>
                                                                        @foreach(($cupons ?? collect()) as $cupom)
                                                                            <option value="{{ $cupom->id }}" :disabled="cupomPrincipalId === '{{ $cupom->id }}'">
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
                                                        <x-input-label for="root_usar_contador_{{ $curso->id }}" value="Usar contador?" class="text-sm font-medium" />
                                                        <div class="relative mt-1">
                                                            <select
                                                                id="root_usar_contador_{{ $curso->id }}"
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
                                                            <x-input-label for="root_contador_minutos_{{ $curso->id }}" value="Minutos" class="text-sm font-medium" />
                                                            <div class="relative mt-1">
                                                                <select
                                                                    id="root_contador_minutos_{{ $curso->id }}"
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
                                                            <x-input-label for="root_contador_acao_{{ $curso->id }}" value="Após o contador" class="text-sm font-medium" />
                                                            <div class="relative mt-1">
                                                                <select
                                                                    id="root_contador_acao_{{ $curso->id }}"
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
                                                            <x-input-label for="root_contador_destino_oferta_{{ $curso->id }}" value="Alterar o preço para" class="text-sm font-medium" />
                                                            <div class="relative mt-1">
                                                                <select
                                                                    id="root_contador_destino_oferta_{{ $curso->id }}"
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

                                                <div class="flex items-center justify-between gap-3 pt-1">
                                                    <div id="root-domain-config-feedback-{{ $curso->id }}" class="text-sm"></div>
                                                    <x-secondary-button type="submit">
                                                        <i class="ri-save-line mr-1"></i>
                                                        Salvar configuração
                                                    </x-secondary-button>
                                                </div>
                                            </div>
                                        </div>

                                        <input type="hidden" name="curso_id" value="{{ $curso->id }}">
                                        <input type="hidden" name="id" :value="configId ?? ''">
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endforeach
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
                border-color: #2563eb !important;
                box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.25) !important;
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
                const forms = document.querySelectorAll('.root-domain-config-form');
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                forms.forEach(form => {
                    form.addEventListener('submit', function (event) {
                        event.preventDefault();

                        if (form.dataset.submitting === '1') {
                            return;
                        }

                        const submitSource = form.dataset.submitSource === 'pricing' ? 'pricing' : 'manual';
                        form.dataset.submitting = '1';

                        const cursoId = form.querySelector('input[name="curso_id"]').value;
                        const feedbackDiv = document.getElementById(`root-domain-config-feedback-${cursoId}`);
                        const formData = new FormData(form);

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
                            } else if (!['nada', 'encerrar_basico', 'alterar_preco'].includes(contadorAcao)) {
                                countdownValidationMessage = 'Selecione a ação do contador.';
                            } else if (contadorAcao === 'encerrar_basico' && modoPrecosAtual === 'um_preco') {
                                countdownValidationMessage = 'A ação de encerrar o plano básico só pode ser usada quando a página exibe o plano básico.';
                            } else if (contadorAcao === 'alterar_preco' && !contadorDestinoOferta) {
                                countdownValidationMessage = 'Selecione o preço que será mostrado após o contador.';
                            }
                        }

                        if (submitSource === 'manual' && feedbackDiv) {
                            feedbackDiv.innerHTML = '';
                        }

                        if (countdownValidationMessage) {
                            if (submitSource === 'manual' && feedbackDiv) {
                                const paragraph = document.createElement('p');
                                paragraph.className = 'text-red-600';
                                paragraph.textContent = countdownValidationMessage;
                                feedbackDiv.appendChild(paragraph);
                            }

                            window.dispatchEvent(new CustomEvent('root-domain-config-save-finished', {
                                detail: {
                                    cursoId: Number(cursoId),
                                    ok: false,
                                    message: countdownValidationMessage,
                                }
                            }));

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
                            const payload = isJson ? await response.json() : { message: 'Erro inesperado no servidor.' };

                            if (!response.ok) {
                                throw payload;
                            }

                            return payload;
                        })
                        .then(data => {
                            if (!data.success) {
                                throw { message: 'Não foi possível salvar as configurações.' };
                            }

                            const idInput = form.querySelector('input[name="id"]');
                            if (idInput && typeof data.root_domain_course_config_id !== 'undefined') {
                                idInput.value = data.root_domain_course_config_id ?? '';
                            }

                            window.dispatchEvent(new CustomEvent('root-domain-config-saved', {
                                detail: {
                                    cursoId: Number(cursoId),
                                    configId: data.root_domain_course_config_id ?? null,
                                    mostrarCurso: typeof data.mostrar_curso === 'boolean' ? data.mostrar_curso : formData.get('mostrar_curso') === '1',
                                    formularioPreCheckout: typeof data.formulario_pre_checkout === 'boolean' ? data.formulario_pre_checkout : formData.get('formulario_pre_checkout') !== '0',
                                    modoPrecos: data.modo_precos ?? formData.get('modo_precos') ?? 'padrao',
                                    cupomPrincipalId: data.cupom_principal_id ?? formData.get('cupom_principal_id') ?? '',
                                    cupomSecundarioId: data.cupom_secundario_id ?? formData.get('cupom_secundario_id') ?? '',
                                    usarContador: typeof data.usar_contador === 'boolean' ? data.usar_contador : formData.get('usar_contador') === '1',
                                    contadorMinutos: data.contador_minutos ?? formData.get('contador_minutos') ?? '',
                                    contadorAcao: data.contador_acao ?? formData.get('contador_acao') ?? '',
                                    contadorDestinoOferta: data.contador_destino_oferta ?? formData.get('contador_destino_oferta') ?? '',
                                }
                            }));

                            if (submitSource === 'pricing') {
                                if (feedbackDiv) {
                                    feedbackDiv.innerHTML = '';
                                }
                                window.dispatchEvent(new CustomEvent('root-domain-config-save-finished', {
                                    detail: {
                                        cursoId: Number(cursoId),
                                        ok: true,
                                        message: 'Salvo',
                                    }
                                }));
                                return;
                            }

                            if (feedbackDiv) {
                                feedbackDiv.innerHTML = '';
                                const successParagraph = document.createElement('p');
                                successParagraph.className = 'text-green-600';
                                successParagraph.textContent = data.success;
                                feedbackDiv.appendChild(successParagraph);
                            }
                            window.dispatchEvent(new CustomEvent('show-success', { detail: data.success }));
                        })
                        .catch(errorData => {
                            let compactError = 'Ocorreu um erro inesperado. Tente novamente.';

                            if (errorData && errorData.errors) {
                                const allErrors = Object.values(errorData.errors);
                                compactError = allErrors?.[0]?.[0] ?? 'Erro de validação.';

                                if (submitSource === 'manual' && feedbackDiv) {
                                    let errorHtml = '<ul class="text-red-600 list-disc list-inside">';
                                    allErrors.forEach(error => {
                                        errorHtml += `<li>${error[0]}</li>`;
                                    });
                                    errorHtml += '</ul>';
                                    feedbackDiv.innerHTML = errorHtml;
                                }
                            } else {
                                compactError = (errorData && errorData.message) ? errorData.message : compactError;

                                if (submitSource === 'manual' && feedbackDiv) {
                                    feedbackDiv.innerHTML = '';
                                    const paragraph = document.createElement('p');
                                    paragraph.className = 'text-red-600';
                                    paragraph.textContent = compactError;
                                    feedbackDiv.appendChild(paragraph);
                                }
                            }

                            window.dispatchEvent(new CustomEvent('root-domain-config-save-finished', {
                                detail: {
                                    cursoId: Number(cursoId),
                                    ok: false,
                                    message: compactError,
                                }
                            }));
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
