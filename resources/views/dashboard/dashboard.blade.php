<x-app-layout>
    {{-- O Título da Página agora vai neste slot --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 text-gray-200 leading-tight">
            {{ __('Painel do Afiliado') }}
        </h2>
    </x-slot>

    {{-- O conteúdo principal da página --}}
    @php
        $whatsappAtendimentos = $whatsappAtendimentos ?? collect();
        $temWhatsappAtendimento = $whatsappAtendimentos->isNotEmpty();
        $whatsappAtivos = $whatsappAtendimentos->where('is_active', true)->values();
        $homePageLayoutAtual = in_array((string) Auth::user()->home_page_layout, ['padrao', 'w3'], true)
            ? (string) Auth::user()->home_page_layout
            : 'padrao';
        $homePageDestinationAtual = in_array((string) Auth::user()->home_page_destination, ['curso', 'whatsapp'], true)
            ? (string) Auth::user()->home_page_destination
            : 'curso';
        $homePageWhatsappFlowAtual = in_array((string) Auth::user()->home_page_whatsapp_flow, ['formulario', 'direto'], true)
            ? (string) Auth::user()->home_page_whatsapp_flow
            : 'formulario';
        $dashboardJornada = array_values($dashboard_jornada ?? []);
        $dashboardJornadaSummary = array_merge([
            'completed_count' => 0,
            'progress_percent' => 0,
            'xp_total' => 0,
            'xp_max' => 0,
            'level_label' => 'Nível 1 · Em ativação',
            'reward_unlocked' => false,
        ], $dashboard_jornada_summary ?? []);
    @endphp
    <div class="py-12" x-data="dashboardJourney(@js($dashboardJornada))">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- SEÇÃO 1: BOAS-VINDAS E PRIMEIROS PASSOS -->
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-800 text-gray-200">Bem-vindo(a), {{ Auth::user()->name }}!</h1>
                <p class="mt-2 text-lg text-gray-600 text-gray-400">Sua jornada para o sucesso como afiliado começa agora. Siga os passos abaixo.</p>
            </div>

            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <section class="bg-gradient-to-r from-indigo-900 to-blue-900 rounded-xl p-6 shadow-xl border border-indigo-500/30 mb-8">
                <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between mb-6">
                    <div class="max-w-2xl">
                        <h3 class="text-2xl font-bold text-white flex items-center gap-2">
                            <i class="ri-medal-line text-yellow-400"></i>
                            Sua Jornada de Sucesso
                        </h3>
                        <p class="mt-2 text-sm text-indigo-200">
                            Complete as missões para ativar sua estrutura de forma progressiva.
                        </p>
                    </div>

                    <div class="md:min-w-[280px] md:text-right">
                        <div class="mt-3 flex md:justify-end">
                            <div class="inline-flex items-center gap-3 rounded-2xl border border-amber-300/30 bg-slate-950/35 px-4 py-3 shadow-[0_0_26px_rgba(251,191,36,0.2)]">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-amber-300/20 text-amber-200">
                                    <i class="ri-flashlight-line text-xl"></i>
                                </span>
                                <div class="leading-tight text-left md:text-right">
                                    <div class="text-2xl font-black text-amber-100">{{ $dashboardJornadaSummary['xp_total'] }} XP</div>
                                    <div class="text-[11px] uppercase tracking-[0.14em] text-amber-200/80">Experiência acumulada</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-3">
                    <button
                        type="button"
                        @click="prevCarousel()"
                        :disabled="carouselStart === 0"
                        x-show="steps.length > cardsPerView"
                        x-cloak
                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white transition"
                        :class="carouselStart === 0 ? 'cursor-not-allowed opacity-40' : 'hover:bg-white/20 hover:shadow-[0_0_14px_rgba(148,163,184,0.35)]'"
                    >
                        <i class="ri-arrow-left-s-line text-xl"></i>
                    </button>

                    <div class="min-w-0 flex-1 overflow-hidden">
                        <div
                            class="flex flex-nowrap transition-transform duration-300 ease-out will-change-transform"
                            :style="trackStyle()"
                        >
                            @forelse ($dashboardJornada as $step)
                        @php
                            $stepIsCompleted = (bool) ($step['concluido'] ?? false);
                            $stepIsFocused = (bool) ($step['em_foco'] ?? false);
                            $stepIsBlocked = (bool) ($step['bloqueado'] ?? false);
                            $stepCanOpen = (bool) ($step['pode_abrir'] ?? false);
                            $stepCardClasses = $stepIsCompleted
                                ? 'bg-emerald-400/10 border-emerald-300/30'
                                : ($stepIsFocused
                                    ? 'bg-gradient-to-br from-amber-300/35 via-yellow-300/30 to-amber-500/25 border-amber-200/80 shadow-[0_0_34px_rgba(251,191,36,0.38)] ring-1 ring-amber-100/35'
                                    : ($stepIsBlocked ? 'bg-slate-950/25 border-slate-400/20 opacity-80' : 'bg-white/5 border-white/10'));
                            $stepTitleClasses = $stepIsCompleted
                                ? 'text-emerald-50'
                                : ($stepIsFocused ? 'text-amber-50' : ($stepIsBlocked ? 'text-slate-100' : 'text-white'));
                            $stepStatusClasses = $stepIsCompleted
                                ? 'text-emerald-200'
                                : ($stepIsFocused ? 'text-amber-100' : ($stepIsBlocked ? 'text-slate-300' : 'text-indigo-200'));
                            $stepIconClasses = $stepIsCompleted
                                ? 'ri-checkbox-circle-fill text-emerald-300'
                                : ($stepIsFocused ? 'ri-sparkling-2-fill text-amber-300' : ($stepIsBlocked ? 'ri-lock-2-fill text-slate-300' : 'ri-time-line text-indigo-300 opacity-70'));
                            $stepStatusText = (string) ($step['status_label'] ?? 'Pendente');
                            $stepGoalLabel = trim((string) ($step['goal_label'] ?? ''));
                            $stepDescription = $stepIsCompleted
                                ? 'O sistema já confirmou essa meta.'
                                : ($stepIsFocused
                                    ? 'Conclua esta missão para liberar a próxima etapa da jornada.'
                                    : ($stepIsBlocked
                                        ? 'Essa missão será liberada quando você concluir a etapa atual.'
                                        : 'A etapa segue disponível com aulas e status automático.'));
                            $stepButtonClasses = $stepIsBlocked
                                ? 'bg-slate-700/40 text-slate-300 cursor-not-allowed'
                                : ($stepIsFocused
                                    ? 'bg-amber-300 text-slate-950 hover:bg-amber-200 shadow-[0_0_18px_rgba(251,191,36,0.25)]'
                                    : 'bg-white/10 hover:bg-white/20 text-white');
                            $stepButtonLabel = $stepIsBlocked ? 'Bloqueada' : 'Instruções';
                        @endphp
                            <div class="w-full shrink-0 px-2 sm:w-1/2 lg:w-1/3">
                                <div class="relative h-full border rounded-xl p-4 transition-all duration-200 {{ $stepCanOpen ? 'hover:scale-[1.02]' : '' }} {{ $stepCardClasses }}">
                                    <span class="absolute top-2 right-2 bg-yellow-500 text-black text-[10px] font-black px-2 py-0.5 rounded-full shadow-lg">
                                        +{{ $step['xp'] }} XP
                                    </span>

                                    <div class="flex flex-col h-full justify-between min-h-[210px]">
                                        <div>
                                            @if ($stepIsCompleted)
                                                <div class="mb-2 flex items-center justify-start">
                                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-300/40 bg-emerald-400/20 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.16em] text-emerald-100">
                                                        <i class="ri-checkbox-circle-fill text-sm"></i>
                                                        Meta concluída
                                                    </span>
                                                </div>
                                            @elseif ($stepIsFocused)
                                                <div class="mb-2 flex items-center justify-start">
                                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-200/55 bg-amber-300/25 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.16em] text-amber-50">
                                                        <i class="ri-sparkling-2-fill text-sm"></i>
                                                        {{ $stepStatusText }}
                                                    </span>
                                                </div>
                                            @elseif ($stepIsBlocked)
                                                <div class="mb-2 flex items-center justify-start">
                                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-300/30 bg-slate-400/15 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.16em] text-slate-100">
                                                        <i class="ri-lock-2-fill text-sm"></i>
                                                        {{ $stepStatusText }}
                                                    </span>
                                                </div>
                                            @else
                                                <div class="mb-2 flex items-center justify-between gap-2">
                                                    <i class="{{ $stepIconClasses }} text-2xl"></i>
                                                    <span class="text-[11px] font-semibold uppercase tracking-[0.16em] {{ $stepStatusClasses }}">
                                                        {{ $stepStatusText }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div class="text-xs font-semibold uppercase tracking-wider text-indigo-300/80">
                                                Etapa {{ $step['numero'] ?? $step['id'] }}
                                            </div>
                                            @if ($stepIsFocused)
                                                <span class="mt-3 inline-flex items-center gap-2 rounded-full bg-amber-300 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-slate-950 shadow-[0_0_16px_rgba(251,191,36,0.28)]">
                                                    <i class="ri-focus-3-line"></i>
                                                    Etapa atual
                                                </span>
                                            @endif
                                            <h4 class="mt-2 font-bold text-sm leading-tight {{ $stepTitleClasses }}">
                                                {{ $step['titulo'] }}
                                            </h4>
                                            @if ($stepGoalLabel !== '')
                                                <p class="mt-2 text-[11px] font-semibold uppercase tracking-[0.14em] {{ $stepStatusClasses }}">
                                                    {{ $stepGoalLabel }}
                                                </p>
                                            @endif
                                            <p class="mt-3 text-xs leading-5 {{ $stepStatusClasses }}">
                                                {{ $stepDescription }}
                                            </p>
                                        </div>

                                        <button
                                            type="button"
                                            @click="openStepAction({{ $step['id'] }})"
                                            @disabled($stepIsBlocked)
                                            class="mt-4 text-[11px] font-bold uppercase tracking-wider py-2 rounded transition {{ $stepButtonClasses }}"
                                        >
                                            {{ $stepButtonLabel }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @empty
                                <div class="w-full shrink-0 px-2">
                                    <div class="rounded-xl border border-dashed border-white/15 bg-white/5 p-5 text-sm leading-6 text-indigo-100/85">
                                        Nenhuma missão ativa foi configurada para a sua jornada no momento.
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="nextCarousel()"
                        :disabled="carouselStart >= maxCarouselStart()"
                        x-show="steps.length > cardsPerView"
                        x-cloak
                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white transition"
                        :class="carouselStart >= maxCarouselStart() ? 'cursor-not-allowed opacity-40' : 'hover:bg-white/20 hover:shadow-[0_0_14px_rgba(148,163,184,0.35)]'"
                    >
                        <i class="ri-arrow-right-s-line text-xl"></i>
                    </button>
                </div>

                <p class="mt-4 text-sm text-indigo-100/80">
                    Clique em instruções para saber como concluir a etapa.
                </p>
            </section>

            <!-- SEÇÕES GUIADAS -->
            <div class="space-y-8">
                <!-- PASSO 1: CONFIGURAÇÃO E APRENDIZADO -->
                <div class="bg-white bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold mb-4 flex items-center text-gray-900 text-gray-100"><span class="bg-blue-500 text-white rounded-full h-8 w-8 flex items-center justify-center mr-3 text-sm">1</span>Comece Por Aqui: Treinamento</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <a href="#" onclick="video_de_ajuda('kJrqK9ZlrA0','Regras do Programa','')" class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 px-4 py-3 bg-gray-100 focus:bg-white transition"><i class="ri-file-shield-2-line text-2xl text-blue-500"></i> <span class="font-medium text-gray-700 text-gray-300">Regras do Programa</span></a>
                        <a href="https://www.youtube.com/playlist?list=PL8UPaaNJEdSDFGX9Pj20RBn7QCn7aSbaU" target="_blank" class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 px-4 py-3 bg-gray-100 focus:bg-white transition"><i class="ri-graduation-cap-line text-2xl text-blue-500"></i> <span class="font-medium text-gray-700 text-gray-300">Curso Método Carvalho</span></a>
                        <a href="https://www.youtube.com/playlist?list=PL8UPaaNJEdSBFoe9Pd1TBlklRrZUZt2lU" target="_blank" class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 px-4 py-3 bg-gray-100 focus:bg-white transition"><i class="ri-customer-service-2-line text-2xl text-red-500"></i> <span class="font-medium text-gray-700 text-gray-300">Curso de Atendimento</span></a>
                        <a href="https://chat.whatsapp.com/Lb7F6OSFUvx1kmyp4h7kgT" target="_blank" class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 px-4 py-3 bg-gray-100 focus:bg-white transition"><i class="ri-whatsapp-line text-2xl text-green-500"></i> <span class="font-medium text-gray-700 text-gray-300">Comunidade de Afiliados</span></a>
                        <a href="https://drive.google.com/drive/folders/1GjFHzFfwI_yEjtMwliMU5F34y581xmKN?usp=sharing" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 px-4 py-3 bg-gray-100 focus:bg-white transition"><i class="ri-file-list-3-line text-2xl text-blue-500"></i> <span class="font-medium text-gray-700 text-gray-300">Script de atendimento</span></a>
                    </div>
                </div>

                <!-- PASSO 2: Card de Configurações Essenciais -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold mb-2 flex items-center text-gray-900">
                        <span class="bg-blue-500 text-white rounded-full h-8 w-8 flex items-center justify-center mr-3 text-sm">2</span>
                        Configurações Essenciais
                    </h3>
                    <p class="text-sm text-gray-600 mb-6">Complete estes passos para ativar todas as funcionalidades da sua conta.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-6">
                            <!-- Bloco de Ação do Domínio (Condicional) -->
                            @if (!Auth::user()->dominio_externo)
                                <div class="p-4 border rounded-lg flex flex-col items-start">
                                    <div class="flex items-center">
                                        <i class="ri-global-line text-2xl text-blue-500 mr-3"></i>
                                        <div>
                                            <h4 class="font-semibold text-gray-800">Nome do Seu Site</h4>
                                            <p class="text-xs text-gray-500">O endereço principal da sua estrutura.</p>
                                        </div>
                                    </div>
                                    @if (Auth::user()->dominio)
                                        <p class="mt-3 text-sm text-gray-700 bg-green-50 p-2 rounded-md w-full break-all">
                                            Configurado: <span class="font-medium">{{ Auth::user()->dominio }}</span>
                                        </p>
                                    @else
                                        <p class="mt-3 text-sm text-yellow-800 bg-yellow-50 p-2 rounded-md w-full">
                                            <i class="ri-error-warning-line mr-1"></i>
                                            Status: <span class="font-medium">Pendente</span>
                                        </p>
                                    @endif
                                    <x-secondary-button x-data @click.prevent="$dispatch('open-modal', 'dominio-form-modal')" class="mt-4">
                                         {{ Auth::user()->dominio ? 'Alterar Nome do Site' : 'Configurar Site' }}
                                    </x-secondary-button>
                                </div>
                            @endif

                        <!-- Bloco de Ação do WhatsApp -->
                        <div class="p-4 border rounded-lg flex flex-col items-start">
                            <div class="flex items-center">
                                <i class="ri-whatsapp-line text-2xl text-green-500 mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-gray-800">WhatsApp de Atendimento</h4>
                                    <p class="text-xs text-gray-500">O número que seus clientes verão.</p>
                                </div>
                            </div>
                            
                            @if ($temWhatsappAtendimento)
                                <div class="mt-3 w-full space-y-2">
                                    @foreach ($whatsappAtendimentos as $item)
                                        <div class="w-full rounded-md border p-3">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="break-all">
                                                    <p class="font-medium text-gray-800">{{ $item->whatsapp }}</p>
                                                    <p class="text-xs {{ $item->is_active ? 'text-green-700' : 'text-gray-500' }}">
                                                        {{ $item->is_active ? 'Ativo' : 'Inativo' }}
                                                    </p>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button
                                                        type="button"
                                                        class="px-3 py-2 text-xs font-semibold text-indigo-700 bg-indigo-50 rounded-md hover:bg-indigo-100"
                                                        onclick="openWhatsappModal({ id: {{ $item->id }}, whatsapp: '{{ $item->whatsapp }}', isActive: {{ $item->is_active ? 1 : 0 }} })"
                                                    >
                                                        Editar
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="px-3 py-2 text-xs font-semibold text-red-700 bg-red-50 rounded-md hover:bg-red-100"
                                                        data-delete-whatsapp
                                                        data-delete-url="{{ route('excluir_whatsapp_atendimento', $item->id) }}"
                                                    >
                                                        Excluir
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="mt-3 text-sm text-yellow-800 bg-yellow-50 p-2 rounded-md w-full">
                                    <i class="ri-error-warning-line mr-1"></i>
                                    Status: <span class="font-medium">Pendente</span>
                                </p>
                            @endif

                            <x-secondary-button type="button" onclick="openWhatsappModal()" class="mt-4">
                                {{ $temWhatsappAtendimento ? 'Adicionar WhatsApp' : 'Cadastrar WhatsApp' }}
                            </x-secondary-button>
                        </div>
                        </div>

                        <div>
                            <div
                                x-data="configureHomeSettings($el)"
                                data-home-model="{{ $homePageLayoutAtual }}"
                                data-home-destination="{{ $homePageDestinationAtual }}"
                                data-home-whatsapp-flow="{{ $homePageWhatsappFlowAtual }}"
                                data-layout-endpoint="{{ route('alterar_home_page_layout') }}"
                                class="p-4 border rounded-lg flex flex-col items-start"
                            >
                                <div class="flex items-center">
                                    <i class="ri-home-gear-line text-2xl text-indigo-500 mr-3"></i>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Configure sua home</h4>
                                        <p class="text-xs text-gray-500">Defina o modelo e o destino padrão da sua página inicial.</p>
                                    </div>
                                </div>

                                <div class="mt-4 w-full space-y-4">
                                    <div>
                                        <x-input-label for="home_settings_model_select" value="Escolha o modelo da sua home" />
                                        <select
                                            id="home_settings_model_select"
                                            x-model="homeModel"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                            style="padding: 15px;"
                                        >
                                            <option value="padrao">Modelo 1</option>
                                            <option value="w3">Modelo 2</option>
                                        </select>
                                    </div>

                                    <div>
                                        <x-input-label for="home_settings_destination_select" value="Escolha o destino da home" />
                                        <select
                                            id="home_settings_destination_select"
                                            x-model="homeDestination"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                            style="padding: 15px;"
                                        >
                                            <option value="curso">Página do curso</option>
                                            <option value="whatsapp">WhatsApp</option>
                                        </select>
                                    </div>

                                    <div x-show="homeDestination === 'whatsapp'" x-cloak>
                                        <x-input-label for="home_settings_whatsapp_flow_select" value="Fluxo do WhatsApp na home" />
                                        <select
                                            id="home_settings_whatsapp_flow_select"
                                            x-model="homeWhatsappFlow"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                            style="padding: 15px;"
                                        >
                                            <option value="formulario">Mostrar formulário</option>
                                            <option value="direto">Ir direto para o WhatsApp</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center gap-3">
                                    <x-primary-button type="button" @click="saveHomeSettings()" x-bind:disabled="isSaving">
                                        <span x-show="!isSaving">Salvar</span>
                                        <span x-show="isSaving" x-cloak>Salvando...</span>
                                    </x-primary-button>
                                    <p x-show="saveSuccess" x-cloak class="text-sm text-green-700">Salvo</p>
                                    <p x-show="saveError" x-text="saveError" x-cloak class="text-sm text-red-600"></p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- PASSO 3: GERADOR DE LINKS -->
                <div class="bg-white bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold mb-4 flex items-center text-gray-900 text-gray-100"><span class="bg-blue-500 text-white rounded-full h-8 w-8 flex items-center justify-center mr-3 text-sm">3</span>Gerador de links</h3>
                   @if(Auth::user()->dominio || Auth::user()->dominio_externo)
                        @php $dominio = Auth::user()->dominio_externo ?? Auth::user()->dominio; @endphp

                        <!-- NOVO GERADOR DE LINKS DA HOME PAGE -->
                        <div
                            x-data="configureHomeBuilder($el)"
                            data-base-url="https://{{ $dominio }}"
                            data-home-model="{{ $homePageLayoutAtual }}"
                            data-home-destination="{{ $homePageDestinationAtual }}"
                            class="bg-gray-100 p-4 rounded-lg border mb-6"
                        >
                            <h5 class="font-semibold text-gray-800 flex items-center gap-2"><i class="ri-links-line"></i>Gerador de links da home</h5>
                            <p class="text-sm text-gray-600">Ajuste as opções para gerar o link na hora. Esta seção não salva configurações.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <x-input-label for="home_model_select" value="Escolha o modelo da sua home" />
                                    <select
                                        id="home_model_select"
                                        x-model="homeModel"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                        style="padding: 15px;"
                                    >
                                        <option value="padrao">Modelo 1</option>
                                        <option value="w3">Modelo 2</option>
                                    </select>
                                </div>

                                <div>
                                    <x-input-label for="home_destination_select" value="Escolha o destino da home" />
                                    <select
                                        id="home_destination_select"
                                        x-model="destination"
                                        @change="updateWhatsappDestination()"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                        style="padding: 15px;"
                                    >
                                        <option value="curso">Página do curso</option>
                                        <option value="whatsapp">WhatsApp</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4" x-show="destination === 'whatsapp'" x-cloak>
                                <x-input-label for="whatsapp_channel_select" value="Canal de WhatsApp" />
                                <select
                                    x-model="whatsappChannel"
                                    id="whatsapp_channel_select"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                    style="padding: 15px;"
                                >
                                    <option value="rodizio">Rodízio (automático)</option>
                                    @foreach($whatsappAtivos as $whatsappAtivo)
                                        <option value="{{ $whatsappAtivo->whatsapp }}">{{ $whatsappAtivo->whatsapp }}</option>
                                    @endforeach
                                </select>
                                @if($whatsappAtivos->isEmpty())
                                    <p class="mt-1 text-xs text-yellow-700">Nenhum WhatsApp ativo encontrado. O link usará rodízio.</p>
                                @endif
                            </div>

                            <div class="mt-4">
                                <x-input-label value="Mostrar nome da cidade?" />
                                <div class="mt-2 inline-flex rounded-md shadow-sm border border-gray-300 overflow-hidden">
                                    <button
                                        type="button"
                                        @click="showCity = 'sim'; updateCityVisibility()"
                                        :class="showCity === 'sim' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'"
                                        class="px-4 py-2 text-sm font-medium transition-colors"
                                    >
                                        Sim
                                    </button>
                                    <button
                                        type="button"
                                        @click="showCity = 'nao'; updateCityVisibility()"
                                        :class="showCity === 'nao' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'"
                                        class="px-4 py-2 text-sm font-medium border-l border-gray-300 transition-colors"
                                    >
                                        Não
                                    </button>
                                </div>
                            </div>

                            <div class="mt-4" x-show="showCity === 'sim'" x-cloak>
                                <x-input-label for="city_input" value="Digite o nome da cidade" />
                                <x-text-input x-model.debounce.300ms="city" id="city_input" type="text" class="mt-1 block w-full text-sm" placeholder="Ex: salvador" style="padding: 15px;" />
                            </div>

                            <div class="mt-4">
                                <x-input-label value="Seu Link Personalizado" />
                                <div class="flex items-stretch mt-1">
                                    <div x-text="finalUrl" class="flex items-center w-full border border-r-0 border-gray-300 rounded-l-md shadow-sm text-sm bg-gray-200 font-mono px-3 py-2 break-all"></div>
                                    <button type="button" @click.prevent="copyToClipboard()" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-r-md hover:bg-indigo-500 text-sm transition-colors flex items-center w-32 justify-center">
                                        <span x-show="!copied" class="flex items-center"><i class="ri-file-copy-line mr-1"></i> Copiar</span>
                                        <span x-show="copied" x-transition class="flex items-center text-lime-300"><i class="ri-check-line mr-1"></i> Copiado!</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="https://drive.google.com/drive/folders/1H1Obc3ozkNDUkcimhDwHD1olZYvHBL2f?usp=sharing" target="_blank" class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 px-4 py-3 bg-gray-100 focus:bg-white transition"><i class="ri-folder-zip-line text-2xl text-blue-500"></i> <span class="font-medium text-gray-700 text-gray-300">Materiais de Apoio</span></a>
                        <a href="https://drive.google.com/drive/folders/1GjFHzFfwI_yEjtMwliMU5F34y581xmKN?usp=sharing" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 px-4 py-3 bg-gray-100 focus:bg-white transition"><i class="ri-file-list-3-line text-2xl text-blue-500"></i> <span class="font-medium text-gray-700 text-gray-300">Script de atendimento</span></a>
                        <a href="{{ route('hotmart_leads', ['version' => null]) }}" class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 px-4 py-3 bg-gray-100 focus:bg-white transition"><i class="ri-user-search-line text-2xl text-blue-500"></i> <span class="font-medium text-gray-700 text-gray-300">Meus Leads</span></a>
                    </div>
                </div>

                <!-- PASSO 4: ANÁLISE E OTIMIZAÇÃO -->
                <div class="bg-white bg-gray-800 p-6 rounded-lg shadow-sm">
                    @php
                        $quantidadeVendas = $quantidade_vendas ?? 0;
                        $totalSum = $total_sum ?? 0;
                        $vencidosResumo = $vencidos ?? ['n' => 0, 'soma' => 0];
                        $dashboardResumo = array_merge([
                            'conversao_vendas' => null,
                            'totalLeads' => 0,
                        ], $dashboard ?? []);
                    @endphp
                    <h3 class="text-xl font-semibold mb-4 flex items-center text-gray-900 text-gray-100"><span class="bg-blue-500 text-white rounded-full h-8 w-8 flex items-center justify-center mr-3 text-sm">4</span>Análise de Resultados</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @if($quantidadeVendas)
                            <div class="text-center p-4 border border-gray-700 rounded-lg">
                                <div class="text-sm font-medium text-gray-500 text-gray-400 uppercase">Faturamento Bruto</div>
                                <div class="text-4xl font-bold text-gray-800 text-gray-200 mt-2">R${{ number_format($totalSum, 2, ',', '.') }}</div>
                                <div class="text-sm text-gray-500 text-gray-400 mt-1">{{ $quantidadeVendas }} Vendas Totais</div>
                            </div>
                        @endif
                         @if($vencidosResumo['n'] > 0)
                            <div class="text-center p-4 bg-yellow-50  border border-yellow-300 border-yellow-700 rounded-lg">
                                <div class="text-sm font-medium text-yellow-600 text-yellow-400 uppercase">A Recuperar</div>
                                <div class="text-4xl font-bold text-red-600 text-red-400 mt-2">R${{ number_format($vencidosResumo['soma'], 2, ',', '.') }}</div>
                                <div class="text-sm text-yellow-600 text-yellow-400 mt-1">{{ $vencidosResumo['n'] }} vendas expiradas</div>
                            </div>
                        @endif
                        @if($dashboardResumo['conversao_vendas'])
                             <div class="text-center p-4 border border-gray-700 rounded-lg">
                                <div class="text-sm font-medium text-gray-500 text-gray-400 uppercase">Taxa de Conversão</div>
                                <div class="text-4xl font-bold mt-2 {{ $dashboardResumo['conversao_vendas'] < 5 ? 'text-red-500' : 'text-green-500' }}">{{ $dashboardResumo['conversao_vendas'] }}%</div>
                                <div class="text-sm text-gray-500 text-gray-400 mt-1">{{ $dashboardResumo['totalLeads'] }} leads → {{ $quantidadeVendas }} vendas</div>
                            </div>
                        @endif
                    </div>
                    <!--<div class="text-center mt-6">
                         <a href="{{ route('ranking') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="ri-trophy-line mr-2"></i>Ver Ranking Completo de Afiliados
                         </a>
                    </div>-->
                </div>
            </div>
        </div>

        <div
            x-show="isStepVideosOpen"
            x-cloak
            class="fixed inset-0 z-50"
            style="display: none;"
            x-on:keydown.escape.window="closeStepVideos()"
        >
            <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm" @click="closeStepVideos()"></div>
            <div class="absolute inset-y-0 right-0 flex w-full justify-end">
                <div
                    x-show="isStepVideosOpen"
                    x-transition:enter="transform transition ease-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in duration-200"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="relative flex h-full w-full max-w-xl flex-col overflow-y-auto border-l border-white/10 bg-slate-950 text-white shadow-2xl"
                >
                    <div class="border-b border-white/10 px-6 py-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-cyan-200/80">Aulas da missão</div>
                                <h3 class="mt-2 text-2xl font-semibold" x-text="selectedStepTitle()"></h3>
                                <p class="mt-2 text-sm leading-6 text-slate-300">
                                    Assista às aulas desta etapa e depois conclua a ação usando os blocos do dashboard.
                                </p>
                            </div>
                            <button type="button" class="rounded-full border border-white/10 bg-white/5 p-2 text-slate-300 transition hover:bg-white/10 hover:text-white" @click="closeStepVideos()">
                                <i class="ri-close-line text-xl"></i>
                            </button>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-slate-300">
                            <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1">
                                <i class="ri-flashlight-line text-amber-200"></i>
                                <span x-text="selectedStepXp()"></span>
                            </span>
                            <span
                                class="inline-flex items-center gap-2 rounded-full border px-3 py-1"
                                :class="selectedStepStatusClass()"
                            >
                                <i :class="selectedStepStatusIcon()"></i>
                                <span x-text="selectedStepStatusLabel()"></span>
                            </span>
                        </div>
                    </div>

                    <div class="flex-1 space-y-3 px-6 py-5">
                        <template x-if="selectedStep && selectedStep.videos && selectedStep.videos.length">
                            <template x-for="(video, index) in selectedStep.videos" :key="`${selectedStep.id}-${index}`">
                                <button
                                    type="button"
                                    class="flex w-full items-start gap-4 rounded-2xl border border-white/10 bg-white/5 px-4 py-4 text-left transition hover:border-cyan-300/25 hover:bg-cyan-400/10"
                                    @click="openJourneyVideo(video)"
                                >
                                    <span class="mt-1 inline-flex h-11 w-11 flex-none items-center justify-center rounded-2xl bg-cyan-400/10 text-cyan-200">
                                        <i class="ri-youtube-line text-2xl"></i>
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block text-sm font-semibold text-white" x-text="video.titulo"></span>
                                        <span class="mt-1 block text-sm leading-6 text-slate-300" x-text="video.texto ? 'Abrir aula e materiais dessa etapa.' : 'Abrir aula em vídeo no modal de ajuda.'"></span>
                                    </span>
                                    <span class="pt-1 text-slate-400">
                                        <i class="ri-arrow-right-up-line text-lg"></i>
                                    </span>
                                </button>
                            </template>
                        </template>

                        <template x-if="!selectedStep || !selectedStep.videos || !selectedStep.videos.length">
                            <div class="rounded-2xl border border-dashed border-white/10 bg-white/5 px-4 py-6 text-sm text-slate-300">
                                Nenhuma aula cadastrada para esta missão.
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal do Formulário de WhatsApp -->
    <x-modal name="whatsapp-form-modal" :show="$errors->any()" focusable>
        <form id="form_whatsapp_atendimento" method="post" action="{{ route('alterar_whatsapp_atendimento') }}" class="p-6 ajax-form">

            @csrf
            <input type="hidden" name="whatsapp_id" id="whatsapp_id" value="">

            <h2 class="text-lg font-medium text-gray-900" id="whatsapp-modal-title">
                Cadastrar WhatsApp de Atendimento
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Digite o número completo, incluindo o código do país (Ex: 55) e o DDD, sem espaços ou símbolos.
            </p>

            <div class="mt-6">
                <x-input-label for="whatsapp" value="Número do WhatsApp" />
                <x-text-input
                    id="whatsapp"
                    name="whatsapp"
                    type="text"
                    class="mt-1 block w-full py-3 px-4"
                    placeholder="5562999998888"
                    inputmode="numeric"
                    pattern="[0-9]{12,14}"
                    required
                />
            </div>
            
            <div class="mt-4">
                <x-input-label for="is_active" value="Status" />
                <select name="is_active" id="is_active" class="py-3 px-4 mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="1">Ativo</option>
                    <option value="0">Inativo</option>
                </select>
            </div>

            <div class="modal_feedback mt-4 text-sm"></div>


            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-primary-button class="ms-3" id="whatsapp-modal-submit">
                    {{ __('Salvar WhatsApp') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Modal do Formulário de Domínio -->
    <x-modal name="dominio-form-modal" :show="$errors->any()" focusable>
        @php
            $baseDomain = parse_url(config('app.url'), PHP_URL_HOST) ?: request()->getHost();
            $baseDomain = preg_replace('/^www\./', '', strtolower($baseDomain));

            $currentSubdomain = '';
            $userDomain = strtolower((string) (Auth::user()->dominio ?? ''));
            if ($userDomain !== '') {
                $baseSuffix = '.' . $baseDomain;
                if (str_ends_with($userDomain, $baseSuffix)) {
                    $currentSubdomain = substr($userDomain, 0, -strlen($baseSuffix));
                } else {
                    $currentSubdomain = explode('.', $userDomain)[0] ?? '';
                }
                $currentSubdomain = preg_replace('/[^a-z0-9]/', '', $currentSubdomain);
            }
        @endphp
        <form 
            x-data="{ subdominio: @js($currentSubdomain), dominioBase: @js($baseDomain) }"
            method="post" 
            action="{{ route('alterar_dominio') }}" 
            class="p-6 ajax-form"
        >
            @csrf
            <h2 class="text-lg font-medium text-gray-900">
                Escolha o nome do seu site
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Este será seu endereço principal. Use apenas letras e números, sem espaços.
            </p>

            <div class="mt-6">
                <x-input-label for="dominio_digitar" value="Nome do site" />
                <x-text-input
                    id="dominio_digitar"
                    name="dominio"
                    class="mt-1 block w-full py-3 px-4"
                    x-model="subdominio"
                    @input="subdominio = $event.target.value.toLowerCase().replace(/[^a-z0-9]/g, '')"
                />
            </div>

            <div class="mt-2 text-sm text-gray-600">
                Seu site será: <strong class="font-semibold text-indigo-600" x-text="subdominio ? subdominio + '.' + dominioBase : ''"></strong>
            </div>
            
            <div class="modal_feedback mt-4 text-sm"></div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancelar') }}
                </x-secondary-button>
                <x-primary-button class="ms-3">
                    {{ __('Salvar Site') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
<script>
    function dashboardJourney(steps = []) {
        return {
            steps: Array.isArray(steps) ? steps : [],
            carouselStart: 0,
            cardsPerView: 1,
            resizeHandler: null,
            isStepVideosOpen: false,
            selectedStep: null,

            init() {
                this.cardsPerView = this.resolveCardsPerView();
                this.carouselStart = this.initialCarouselStart();
                this.resizeHandler = () => {
                    this.cardsPerView = this.resolveCardsPerView();
                    this.carouselStart = Math.min(this.carouselStart, this.maxCarouselStart());
                };
                window.addEventListener('resize', this.resizeHandler);
            },

            resolveCardsPerView() {
                const width = window.innerWidth || 0;
                if (width >= 1024) {
                    return 3;
                }

                if (width >= 640) {
                    return 2;
                }

                return 1;
            },

            maxCarouselStart() {
                return Math.max(0, this.steps.length - this.cardsPerView);
            },

            initialCarouselStart() {
                const totalSteps = this.steps.length;
                if (totalSteps <= this.cardsPerView) {
                    return 0;
                }

                const focusedIndex = this.steps.findIndex(step => Boolean(step && step.em_foco));
                if (focusedIndex < 0) {
                    return 0;
                }

                if (focusedIndex === 0) {
                    return 0;
                }

                if (focusedIndex === totalSteps - 1) {
                    return this.maxCarouselStart();
                }

                const centeredStart = focusedIndex - Math.floor(this.cardsPerView / 2);
                return Math.max(0, Math.min(centeredStart, this.maxCarouselStart()));
            },

            trackStyle() {
                if (this.steps.length <= this.cardsPerView) {
                    return 'transform: translateX(0%);';
                }

                const translatePercent = (100 / this.cardsPerView) * this.carouselStart;
                return `transform: translateX(-${translatePercent}%);`;
            },

            prevCarousel() {
                if (this.carouselStart <= 0) {
                    return;
                }

                this.carouselStart -= 1;
            },

            nextCarousel() {
                const maxStart = this.maxCarouselStart();
                if (this.carouselStart >= maxStart) {
                    return;
                }

                this.carouselStart += 1;
            },

            openStepVideos(stepId) {
                const selected = this.steps.find(step => Number(step.id) === Number(stepId));
                if (!selected || selected.pode_abrir === false) {
                    return;
                }

                this.selectedStep = selected;
                this.isStepVideosOpen = true;
            },

            openStepAction(stepId) {
                const selected = this.steps.find(step => Number(step.id) === Number(stepId));
                if (!selected || selected.pode_abrir === false) {
                    return;
                }

                const videos = Array.isArray(selected.videos) ? selected.videos : [];
                if (videos.length === 1) {
                    this.openJourneyVideo(videos[0]);
                    return;
                }

                this.openStepVideos(stepId);
            },

            closeStepVideos() {
                this.isStepVideosOpen = false;
                this.selectedStep = null;
            },

            openJourneyVideo(video) {
                if (!video || !video.link) {
                    return;
                }

                const title = typeof video.titulo === 'string' ? video.titulo : '';
                const text = typeof video.texto === 'string' ? video.texto : '';

                this.closeStepVideos();
                window.video_de_ajuda(video.link, title, text);
            },

            selectedStepTitle() {
                return this.selectedStep && this.selectedStep.titulo
                    ? this.selectedStep.titulo
                    : 'Missão';
            },

            selectedStepXp() {
                const xp = this.selectedStep && this.selectedStep.xp
                    ? Number(this.selectedStep.xp)
                    : 0;

                return `${xp} XP`;
            },

            selectedStepStatusClass() {
                if (this.selectedStep && this.selectedStep.concluido) {
                    return 'border-emerald-300/20 bg-emerald-400/10 text-emerald-100';
                }

                if (this.selectedStep && this.selectedStep.em_foco) {
                    return 'border-amber-300/30 bg-amber-300/15 text-amber-100';
                }

                if (this.selectedStep && this.selectedStep.bloqueado) {
                    return 'border-slate-400/20 bg-slate-400/10 text-slate-200';
                }

                return 'border-white/10 bg-white/5 text-slate-200';
            },

            selectedStepStatusIcon() {
                if (this.selectedStep && this.selectedStep.concluido) {
                    return 'ri-checkbox-circle-fill';
                }

                if (this.selectedStep && this.selectedStep.em_foco) {
                    return 'ri-sparkling-2-fill';
                }

                if (this.selectedStep && this.selectedStep.bloqueado) {
                    return 'ri-lock-2-fill';
                }

                return 'ri-time-line';
            },

            selectedStepStatusLabel() {
                return this.selectedStep && this.selectedStep.status_label
                    ? this.selectedStep.status_label
                    : 'Pendente';
            }
        };
    }

    function openWhatsappModal(payload = null) {
        const form = document.getElementById('form_whatsapp_atendimento');
        if (!form) return;

        const idInput = document.getElementById('whatsapp_id');
        const whatsappInput = document.getElementById('whatsapp');
        const isActiveSelect = document.getElementById('is_active');
        const feedbackDiv = form.querySelector('.modal_feedback');
        const title = document.getElementById('whatsapp-modal-title');
        const submitButton = document.getElementById('whatsapp-modal-submit');

        if (idInput) idInput.value = payload?.id ? String(payload.id) : '';
        if (whatsappInput) whatsappInput.value = payload?.whatsapp ? String(payload.whatsapp).replace(/[^0-9]/g, '') : '';
        if (isActiveSelect) isActiveSelect.value = payload?.isActive === 0 ? '0' : '1';
        if (feedbackDiv) feedbackDiv.innerHTML = '';

        if (title) {
            title.textContent = payload?.id ? 'Editar WhatsApp de Atendimento' : 'Cadastrar WhatsApp de Atendimento';
        }
        if (submitButton) {
            submitButton.textContent = payload?.id ? 'Salvar Alterações' : 'Salvar WhatsApp';
        }

        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'whatsapp-form-modal' }));
    }

    function bindWhatsappDeleteButtons() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        document.querySelectorAll('[data-delete-whatsapp]').forEach(button => {
            if (button.dataset.bound === '1') return;
            button.dataset.bound = '1';

            button.addEventListener('click', function () {
                const deleteUrl = this.dataset.deleteUrl;
                if (!deleteUrl) return;

                if (!confirm('Deseja excluir este WhatsApp?')) return;

                this.disabled = true;
                fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                })
                .then(response => {
                    if (!response.ok) return response.json().then(err => { throw err; });
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(() => {
                    this.disabled = false;
                    alert('Não foi possível excluir o WhatsApp.');
                });
            });
        });
    }

    function bindAjaxForms() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        document.querySelectorAll('.ajax-form').forEach(form => {
            // Evita adicionar o listener múltiplas vezes
            if (form.classList.contains('ajax-bound')) return;
            form.classList.add('ajax-bound');

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                const feedbackDiv = form.querySelector('.modal_feedback'); // ATENÇÃO: Corrigido para '.modal_feedback'
                const formData = new FormData(form);

                if (feedbackDiv) feedbackDiv.innerHTML = '<p class="text-gray-500">Salvando...</p>';

                fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) return response.json().then(err => { throw err; });
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        if (feedbackDiv) feedbackDiv.innerHTML = '<p class="text-green-600">Salvo com sucesso!</p>';
                        setTimeout(() => location.reload(), 800);
                    }
                })
                .catch(errorData => {
                    if (feedbackDiv && errorData.errors) {
                        let errorHtml = '<ul class="text-red-600 list-disc list-inside">';
                        
                        // CORREÇÃO: Verifica os possíveis erros de ambos os formulários
                        if (errorData.errors.whatsapp) {
                            errorHtml += `<li>${errorData.errors.whatsapp[0]}</li>`;
                        }
                        if (errorData.errors.whatsapp_atendimento) {
                            errorHtml += `<li>${errorData.errors.whatsapp_atendimento[0]}</li>`;
                        }
                        if (errorData.errors.is_active) {
                            errorHtml += `<li>${errorData.errors.is_active[0]}</li>`;
                        }
                        if (errorData.errors.dominio) {
                            errorHtml += `<li>${errorData.errors.dominio[0]}</li>`;
                        }
                        
                        errorHtml += '</ul>';
                        feedbackDiv.innerHTML = errorHtml;
                    } else {
                        if (feedbackDiv) feedbackDiv.innerHTML = '<p class="text-red-600">Ocorreu um erro inesperado.</p>';
                    }
                });
            });
        });
    }

    function configureHomeSettings(element) {
        const initialHomeModel = element?.dataset?.homeModel || 'padrao';
        const initialHomeDestination = element?.dataset?.homeDestination || 'curso';
        const initialHomeWhatsappFlow = element?.dataset?.homeWhatsappFlow || 'formulario';
        const layoutEndpoint = element?.dataset?.layoutEndpoint || '';

        return {
            homeModel: ['padrao', 'w3'].includes(initialHomeModel) ? initialHomeModel : 'padrao',
            homeDestination: ['curso', 'whatsapp'].includes(initialHomeDestination) ? initialHomeDestination : 'curso',
            homeWhatsappFlow: ['formulario', 'direto'].includes(initialHomeWhatsappFlow) ? initialHomeWhatsappFlow : 'formulario',
            isSaving: false,
            saveSuccess: false,
            saveError: '',

            saveHomeSettings() {
                if (!layoutEndpoint || this.isSaving) {
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    this.saveError = 'Não foi possível salvar agora.';
                    return;
                }

                this.isSaving = true;
                this.saveSuccess = false;
                this.saveError = '';

                const formData = new FormData();
                formData.append('home_page_layout', this.homeModel);
                formData.append('home_page_destination', this.homeDestination);
                formData.append('home_page_whatsapp_flow', this.homeWhatsappFlow);

                fetch(layoutEndpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(async (response) => {
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw data;
                    }
                    return data;
                })
                .then((data) => {
                    this.homeModel = ['padrao', 'w3'].includes(data.home_page_layout) ? data.home_page_layout : this.homeModel;
                    this.homeDestination = ['curso', 'whatsapp'].includes(data.home_page_destination) ? data.home_page_destination : this.homeDestination;
                    this.homeWhatsappFlow = ['formulario', 'direto'].includes(data.home_page_whatsapp_flow) ? data.home_page_whatsapp_flow : this.homeWhatsappFlow;
                    this.saveSuccess = true;
                    window.setTimeout(() => {
                        this.saveSuccess = false;
                    }, 2500);
                })
                .catch((errorData) => {
                    if (errorData?.errors?.home_page_layout?.[0]) {
                        this.saveError = errorData.errors.home_page_layout[0];
                        return;
                    }

                    if (errorData?.errors?.home_page_destination?.[0]) {
                        this.saveError = errorData.errors.home_page_destination[0];
                        return;
                    }

                    if (errorData?.errors?.home_page_whatsapp_flow?.[0]) {
                        this.saveError = errorData.errors.home_page_whatsapp_flow[0];
                        return;
                    }

                    this.saveError = 'Não foi possível salvar agora.';
                })
                .finally(() => {
                    this.isSaving = false;
                });
            }
        };
    }

    function configureHomeBuilder(element) {
        const baseUrl = element?.dataset?.baseUrl || '';
        const initialHomeModel = element?.dataset?.homeModel || 'padrao';
        const initialHomeDestination = element?.dataset?.homeDestination || 'curso';

        return {
            homeModel: ['padrao', 'w3'].includes(initialHomeModel) ? initialHomeModel : 'padrao',
            destination: ['curso', 'whatsapp'].includes(initialHomeDestination) ? initialHomeDestination : 'curso',
            showCity: 'nao',
            city: '',
            whatsappChannel: 'rodizio',
            copied: false,
            baseUrl,

            get finalUrl() {
                let url = this.baseUrl;
                const params = new URLSearchParams();

                if (this.homeModel === 'w3') {
                    url += '/w3';
                    if (this.destination === 'curso') {
                        params.set('destination', 'curso');
                    }
                } else {
                    params.set('layout', 'padrao');
                }

                if (this.destination === 'whatsapp') {
                    params.set('w', '1');
                    if (this.whatsappChannel && this.whatsappChannel !== 'rodizio') {
                        params.set('t', this.whatsappChannel);
                    }
                }

                if (this.showCity === 'sim' && this.city.trim() !== '') {
                    params.set('c', this.city.trim());
                }

                const queryString = params.toString();
                return queryString ? `${url}?${queryString}` : url;
            },

            updateWhatsappDestination() {
                if (this.destination !== 'whatsapp') {
                    this.whatsappChannel = 'rodizio';
                }
            },

            updateCityVisibility() {
                if (this.showCity !== 'sim') {
                    this.city = '';
                }
            },

            copyToClipboard() {
                if (!this.finalUrl) return;

                const onCopySuccess = () => {
                    this.copied = true;
                    setTimeout(() => {
                        this.copied = false;
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
                    navigator.clipboard.writeText(this.finalUrl)
                        .then(() => onCopySuccess())
                        .catch(() => {
                            if (fallbackCopy(this.finalUrl)) {
                                onCopySuccess();
                                return;
                            }
                            window.prompt('Copie manualmente o link:', this.finalUrl);
                        });
                    return;
                }

                if (fallbackCopy(this.finalUrl)) {
                    onCopySuccess();
                    return;
                }

                window.prompt('Copie manualmente o link:', this.finalUrl);
            }
        };
    }

    function bindWhatsappInputMask() {
        const whatsappInput = document.getElementById('whatsapp');
        if (!whatsappInput || whatsappInput.dataset.bound === '1') return;
        whatsappInput.dataset.bound = '1';

        whatsappInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        bindAjaxForms();
        bindWhatsappDeleteButtons();
        bindWhatsappInputMask();
    });
</script>
</x-app-layout>
