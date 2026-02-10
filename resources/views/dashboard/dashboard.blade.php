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
            @elseif(!Auth::user()->whatsapp_atendimento)
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 p-4 text-center rounded-md" role="alert">
                    <h4 class="font-bold text-lg flex items-center justify-center"><i class="ri-whatsapp-line mr-2"></i>Quase lá!</h4>
                    <p>Seu site está no ar, mas você precisa cadastrar seu WhatsApp de atendimento para não perder vendas.</p>
                    <hr class="my-3 border-yellow-300">
                    <x-primary-button x-data @click.prevent="$dispatch('open-modal', 'whatsapp-form-modal')"  class="bg-yellow-500 hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700">
                        <i class="ri-edit-2-line mr-2"></i>Cadastrar Meu WhatsApp
                    </x-primary-button>
                </div>
            @endif

            <!-- SEÇÕES GUIADAS -->
            <div class="space-y-8">
                <!-- PASSO 1: CONFIGURAÇÃO E APRENDIZADO -->
                <div class="bg-white bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold mb-4 flex items-center text-gray-900 text-gray-100"><span class="bg-blue-500 text-white rounded-full h-8 w-8 flex items-center justify-center mr-3 text-sm">1</span>Comece Por Aqui: Treinamento</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="#" onclick="video_de_ajuda('kJrqK9ZlrA0','Regras do Programa','')" class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 px-4 py-3 bg-gray-100 focus:bg-white transition"><i class="ri-file-shield-2-line text-2xl text-blue-500"></i> <span class="font-medium text-gray-700 text-gray-300">Regras do Programa</span></a>
                        <a href="https://www.youtube.com/playlist?list=PL8UPaaNJEdSDFGX9Pj20RBn7QCn7aSbaU" target="_blank" class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 px-4 py-3 bg-gray-100 focus:bg-white transition"><i class="ri-graduation-cap-line text-2xl text-blue-500"></i> <span class="font-medium text-gray-700 text-gray-300">Curso Método Carvalho</span></a>
                        <a href="https://www.youtube.com/playlist?list=PL8UPaaNJEdSBFoe9Pd1TBlklRrZUZt2lU" target="_blank" class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 px-4 py-3 bg-gray-100 focus:bg-white transition"><i class="ri-customer-service-2-line text-2xl text-red-500"></i> <span class="font-medium text-gray-700 text-gray-300">Curso de Atendimento</span></a>
                        <a href="https://chat.whatsapp.com/Lb7F6OSFUvx1kmyp4h7kgT" target="_blank" class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 px-4 py-3 bg-gray-100 focus:bg-white transition"><i class="ri-whatsapp-line text-2xl text-green-500"></i> <span class="font-medium text-gray-700 text-gray-300">Comunidade de Afiliados</span></a>
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
                        
                        <!-- Bloco de Ação do WhatsApp -->
                        <div class="p-4 border rounded-lg flex flex-col items-start">
                            <div class="flex items-center">
                                <i class="ri-whatsapp-line text-2xl text-green-500 mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-gray-800">WhatsApp de Atendimento</h4>
                                    <p class="text-xs text-gray-500">O número que seus clientes verão.</p>
                                </div>
                            </div>
                            
                            @if (Auth::user()->whatsapp_atendimento)
                                <p class="mt-3 text-sm text-gray-700 bg-green-50 p-2 rounded-md w-full">
                                    Cadastrado: <span class="font-medium">{{ Auth::user()->whatsapp_atendimento }}</span>
                                </p>
                            @else
                                <p class="mt-3 text-sm text-yellow-800 bg-yellow-50 p-2 rounded-md w-full">
                                    <i class="ri-error-warning-line mr-1"></i>
                                    Status: <span class="font-medium">Pendente</span>
                                </p>
                            @endif

                            {{-- ESTE BOTÃO IRÁ ABRIR O MODAL --}}
                            <x-secondary-button x-data @click.prevent="$dispatch('open-modal', 'whatsapp-form-modal')" class="mt-4">
                                {{ Auth::user()->whatsapp_atendimento ? 'Alterar WhatsApp' : 'Cadastrar WhatsApp' }}
                            </x-secondary-button>
                        </div>

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

                    </div>
                </div>

                <!-- PASSO 3: FERRAMENTAS DO DIA A DIA -->
                <div class="bg-white bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold mb-4 flex items-center text-gray-900 text-gray-100"><span class="bg-blue-500 text-white rounded-full h-8 w-8 flex items-center justify-center mr-3 text-sm">3</span>Ferramentas do Dia a Dia</h3>
                   @if(Auth::user()->dominio || Auth::user()->dominio_externo)
                        @php $dominio = Auth::user()->dominio_externo ?? Auth::user()->dominio; @endphp

                        <!-- NOVO GERADOR DE LINKS DA HOME PAGE -->
                        <div 
                            x-data="{
                                destination: 'standard',
                                coupon: '',
                                city: '',
                                alternateWhatsApp: '',
                                copied: false,
                                baseUrl: 'https://{{ $dominio }}',
                                
                                get finalUrl() {
                                    let url = this.baseUrl;
                                    if (this.destination === 'whatsapp') {
                                        url += '/w';
                                    }
                                    
                                    const params = new URLSearchParams();
                                    if (this.coupon) params.set('d', this.coupon);
                                    if (this.city) params.set('c', this.city);
                                    if (this.alternateWhatsApp) params.set('t', this.alternateWhatsApp);
                                    
                                    const queryString = params.toString();
                                    return queryString ? `${url}?${queryString}` : url;
                                },

                                copyToClipboard() {
                                    if (!this.finalUrl) return;
                                    navigator.clipboard.writeText(this.finalUrl).then(() => {
                                        this.copied = true;
                                        setTimeout(() => this.copied = false, 2000);
                                    });
                                }
                            }"
                            class="bg-gray-100 p-4 rounded-lg border mb-6"
                        >
                            <h5 class="font-semibold text-gray-800 flex items-center gap-2"><i class="ri-links-line"></i>Gerador de Link da Página Principal</h5>
                            <p class="text-sm text-gray-600">Personalize o link principal do seu site para suas campanhas.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <!-- Coluna de Opções -->
                                <div class="space-y-4">
                                    <div>
                                        <x-input-label value="Destino do Link" class="mb-2" />
                                        <div class="flex gap-4">
                                            <label class="flex items-center"><input type="radio" x-model="destination" value="standard" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"><span class="ml-2 text-sm">Página Padrão</span></label>
                                            <label class="flex items-center"><input type="radio" x-model="destination" value="whatsapp" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"><span class="ml-2 text-sm">Leva ao WhatsApp</span></label>
                                        </div>
                                    </div>

                                    <div>
                                        <x-input-label for="coupon_select" value="Cupom de Desconto" />
                                        <select x-model="coupon" id="coupon_select" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" style="padding: 15px;">
                                            <option value="">Sem cupom</option>
                                            <option value="o10">10% OFF</option>
                                            <option value="o20">20% OFF</option>
                                            <option value="o30">30% OFF</option>
                                            <option value="o40">40% OFF</option>
                                            <option value="o50">50% OFF</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Coluna de Campos Opcionais -->
                                <div class="space-y-4">
                                    <div>
                                        <x-input-label for="city_input" value="Nome da Cidade (opcional)" />
                                        <x-text-input x-model.debounce.300ms="city" id="city_input" type="text" class="mt-1 block w-full text-sm" placeholder="Ex: salvador" style="padding: 15px;" />
                                    </div>
                                    <div>
                                        <x-input-label for="whatsapp_input" value="WhatsApp Alternativo (opcional)" />
                                        <x-text-input x-model.debounce.300ms="alternateWhatsApp" id="whatsapp_input" type="number" class="mt-1 block w-full text-sm" placeholder="556299999999" style="padding: 15px;" />
                                    </div>
                                </div>
                            </div>

                            <!-- Saída do Link Final -->
                            <div class="mt-4">
                                <x-input-label value="Seu Link Personalizado" />
                                <div class="flex items-stretch mt-1">
                                    <div x-text="finalUrl" class="flex items-center w-full border border-r-0 border-gray-300 rounded-l-md shadow-sm text-sm bg-gray-200 font-mono px-3 py-2 break-all"></div>
                                    <button @click="copyToClipboard()" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-r-md hover:bg-indigo-500 text-sm transition-colors flex items-center w-32 justify-center">
                                        <span x-show="!copied" class="flex items-center"><i class="ri-file-copy-line mr-1"></i> Copiar</span>
                                        <span x-show="copied" x-transition class="flex items-center text-lime-300"><i class="ri-check-line mr-1"></i> Copiado!</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="https://drive.google.com/drive/folders/1H1Obc3ozkNDUkcimhDwHD1olZYvHBL2f?usp=sharing" target="_blank" class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 px-4 py-3 bg-gray-100 focus:bg-white transition"><i class="ri-folder-zip-line text-2xl text-blue-500"></i> <span class="font-medium text-gray-700 text-gray-300">Materiais de Apoio</span></a>
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

            <h2 class="text-lg font-medium text-gray-900">
                Atualizar WhatsApp de Atendimento
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Digite o número completo, incluindo o código do país (Ex: 55) e o DDD, sem espaços ou símbolos.
            </p>

            <div class="mt-6">
                <x-input-label for="whatsapp_atendimento" value="Número do WhatsApp" />
                <x-text-input
                    id="whatsapp_atendimento"
                    name="whatsapp_atendimento"
                    type="number"
                    class="mt-1 block w-full py-3 px-4"
                    placeholder="5562999998888"
                    :value="old('whatsapp_atendimento', Auth::user()->whatsapp_atendimento)"
                    required
                />
            </div>
            
            <div class="mt-4">
                <x-input-label for="whatsapp_atendimento_tempo" value="Quando mostrar o botão de WhatsApp no seu site?" />
                <select name="whatsapp_atendimento_tempo" id="whatsapp_atendimento_tempo" class="py-3 px-4 mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    @php $currentTime = Auth::user()->whatsapp_atendimento_tempo; @endphp
                    <option value="0" @selected($currentTime == 0)>Imediatamente</option>
                    <option value="60" @selected($currentTime == 60)>Após 1 minuto</option>
                    <option value="120" @selected($currentTime == 120)>Após 2 minutos</option>
                    <option value="180" @selected($currentTime == 180)>Após 3 minutos</option>
                    <option value="300" @selected($currentTime == 300)>Após 5 minutos</option>
                </select>
            </div>

            <div class="modal_feedback mt-4 text-sm"></div>


            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Salvar WhatsApp') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Modal do Formulário de Domínio -->
    <x-modal name="dominio-form-modal" :show="$errors->any()" focusable>
        <form 
            x-data="{ subdominio: '{{ str_replace('.portalje.org', '', Auth::user()->dominio) }}' }"
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
                Seu site será: <strong class="font-semibold text-indigo-600" x-text="subdominio ? subdominio + '.portalje.org' : ''"></strong>
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
                        setTimeout(() => location.reload(), 1500);
                    }
                })
                .catch(errorData => {
                    if (feedbackDiv && errorData.errors) {
                        let errorHtml = '<ul class="text-red-600 list-disc list-inside">';
                        
                        // CORREÇÃO: Verifica os possíveis erros de ambos os formulários
                        if (errorData.errors.whatsapp_atendimento) {
                            errorHtml += `<li>${errorData.errors.whatsapp_atendimento[0]}</li>`;
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

    document.addEventListener('DOMContentLoaded', bindAjaxForms);
</script>
</x-app-layout>