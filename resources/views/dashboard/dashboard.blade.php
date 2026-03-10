<x-app-layout>
    {{-- O Título da Página agora vai neste slot --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 text-gray-200 leading-tight">
            {{ __('Painel do Afiliado') }}
        </h2>
    </x-slot>

    {{-- O conteúdo principal da página --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- SEÇÃO 1: BOAS-VINDAS E PRIMEIROS PASSOS -->
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-800 text-gray-200">Bem-vindo(a), {{ Auth::user()->name }}!</h1>
                <p class="mt-2 text-lg text-gray-600 text-gray-400">Sua jornada para o sucesso como afiliado começa agora. Siga os passos abaixo.</p>
            </div>

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
            @endphp

            <!-- BOX DE ALERTA PRINCIPAL -->
            @if(!Auth::user()->dominio && !Auth::user()->dominio_externo)
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 text-center rounded-md" role="alert">
                    <h4 class="font-bold text-lg flex items-center justify-center"><i class="ri-error-warning-line mr-2"></i>Ação Necessária!</h4>
                    <p>Você ainda não configurou seu site. Este é o passo mais importante para começar a vender.</p>
                    <hr class="my-3 border-red-300">
                    <x-danger-button x-data @click.prevent="$dispatch('open-modal', 'dominio-form-modal')">
                        <i class="ri-global-line mr-2"></i>Configurar Meu Site Agora
                    </x-danger-button>
                </div>
            @elseif(!$temWhatsappAtendimento)
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 p-4 text-center rounded-md" role="alert">
                    <h4 class="font-bold text-lg flex items-center justify-center"><i class="ri-whatsapp-line mr-2"></i>Quase lá!</h4>
                    <p>Seu site está no ar, mas você precisa cadastrar seu WhatsApp de atendimento para não perder vendas.</p>
                    <hr class="my-3 border-yellow-300">
                    <x-primary-button type="button" onclick="openWhatsappModal()" class="bg-yellow-500 hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700">
                        <i class="ri-edit-2-line mr-2"></i>Cadastrar Meu WhatsApp
                    </x-primary-button>
                </div>
            @endif

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
                    <h3 class="text-xl font-semibold mb-4 flex items-center text-gray-900 text-gray-100"><span class="bg-blue-500 text-white rounded-full h-8 w-8 flex items-center justify-center mr-3 text-sm">4</span>Análise de Resultados</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @if($quantidade_vendas)
                            <div class="text-center p-4 border border-gray-700 rounded-lg">
                                <div class="text-sm font-medium text-gray-500 text-gray-400 uppercase">Faturamento Bruto</div>
                                <div class="text-4xl font-bold text-gray-800 text-gray-200 mt-2">R${{ number_format($total_sum, 2, ',', '.') }}</div>
                                <div class="text-sm text-gray-500 text-gray-400 mt-1">{{ $quantidade_vendas}} Vendas Totais</div>
                            </div>
                        @endif
                         @if($vencidos && $vencidos['n'] > 0)
                            <div class="text-center p-4 bg-yellow-50  border border-yellow-300 border-yellow-700 rounded-lg">
                                <div class="text-sm font-medium text-yellow-600 text-yellow-400 uppercase">A Recuperar</div>
                                <div class="text-4xl font-bold text-red-600 text-red-400 mt-2">R${{ number_format($vencidos['soma'], 2, ',', '.') }}</div>
                                <div class="text-sm text-yellow-600 text-yellow-400 mt-1">{{ $vencidos['n']}} vendas expiradas</div>
                            </div>
                        @endif
                        @if($dashboard['conversao_vendas'])
                             <div class="text-center p-4 border border-gray-700 rounded-lg">
                                <div class="text-sm font-medium text-gray-500 text-gray-400 uppercase">Taxa de Conversão</div>
                                <div class="text-4xl font-bold mt-2 {{ $dashboard['conversao_vendas']<5 ? 'text-red-500' : 'text-green-500' }}">{{$dashboard['conversao_vendas']}}%</div>
                                <div class="text-sm text-gray-500 text-gray-400 mt-1">{{$dashboard['totalLeads']}} leads → {{$quantidade_vendas}} vendas</div>
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
                if (this.homeModel === 'w3') {
                    url += '/w3';
                }

                const params = new URLSearchParams();

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
