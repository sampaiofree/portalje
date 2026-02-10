<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Meus Cursos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div x-data="{ 
                search: '',
                showSuccessMessage: false,
                successMessage: '',
                showTutorial: false
            }"
            @show-success.window="showSuccessMessage = true; successMessage = $event.detail; setTimeout(() => showSuccessMessage = false, 3000)">
                <!-- Card de Cabeçalho com o campo de busca -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
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
                        <div class="mt-4">
                            <div class="relative max-w-md">
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

                <!-- Grid com os Cards dos Cursos -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($cursos as $curso)
                        @if($curso->publicado && $curso->permitir_afiliacao)
                            <div class="lista_cursos" x-show="search === '' || '{{ strtolower($curso->titulo) }}'.includes(search.toLowerCase())" x-transition>
                                
                                <!-- Início do Alpine Component para cada card -->
                                <div
                                    x-data="{
                                        // Estado expandido/colapsado
                                        expanded: false,

                                        resourcesExpanded: false,

                                        hasCodigoRef: {{ $curso->codigo_ref ? 'true' : 'false' }},
                                        codigoRefId: {{ $curso->codigo_ref_id ? (int) $curso->codigo_ref_id : 'null' }},
                                        codigoRef: @js($curso->codigo_ref),
                                        mostrarCurso: {{ $curso->mostrar_curso ? 'true' : 'false' }},

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
                                            navigator.clipboard.writeText(text).then(() => {
                                                if (type === 'sales') this.copiedSales = true;
                                                if (type === 'checkout') this.copiedCheckout = true;

                                                // Reseta o estado do botão após 2 segundos
                                                setTimeout(() => {
                                                    if (type === 'sales') this.copiedSales = false;
                                                    if (type === 'checkout') this.copiedCheckout = false;
                                                }, 2000);
                                            }).catch(err => {
                                                console.error('Erro ao copiar: ', err);
                                            });
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
                                        }
                                    }"
                                    @ref-saved.window="
                                        if (Number($event.detail.cursoId) === {{ $curso->id }}) {
                                            hasCodigoRef = true;
                                            if ($event.detail.codigoRefId) codigoRefId = $event.detail.codigoRefId;
                                            if (typeof $event.detail.codigoRef !== 'undefined') codigoRef = $event.detail.codigoRef;
                                            if (typeof $event.detail.mostrarCurso !== 'undefined') mostrarCurso = !!$event.detail.mostrarCurso;
                                            if (typeof $event.detail.baseCheckoutUrl !== 'undefined') baseCheckoutUrl = $event.detail.baseCheckoutUrl || '';
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
                                            action="{{route('cadastrar_codigo_ref')}}" 
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
                                                        class="rounded-r-none flex-1"
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
                                                    <button @click="copyLink(finalSalesUrl, 'sales')" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-r-md hover:bg-blue-500 text-sm transition-colors flex items-center w-28 justify-center">
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
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        <i class="ri-coupon-line mr-1"></i>
                                                        Cupom de Desconto
                                                    </label>
                                                    <select x-model="checkoutCupom" class="block w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm text-sm" style="padding: 15px;">
                                                        <option value="">Sem cupom</option>
                                                        <option value="10OFF">10% OFF</option>
                                                        <option value="20OFF">20% OFF</option>
                                                        <option value="30OFF">30% OFF</option>
                                                        <option value="40OFF">40% OFF</option>
                                                        <option value="50OFF">50% OFF</option>
                                                        <option value="80OFF">80% OFF</option>
                                                    </select>
                                                </div>

                                                <div class="flex">
                                                    <div x-text="finalCheckoutUrl" class="flex items-center w-full border border-r-0 border-gray-300 rounded-l-md shadow-sm text-sm bg-gray-100 font-mono px-3 py-2 break-all"></div>
                                                    <button @click="copyLink(finalCheckoutUrl, 'checkout')" class="px-4 py-2 bg-green-600 text-white font-semibold rounded-r-md hover:bg-green-500 text-sm transition-colors flex items-center w-28 justify-center">
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
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const refForms = document.querySelectorAll('.ref-form');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            refForms.forEach(form => {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();

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

                    feedbackDiv.innerHTML = ''; 

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
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
                        if (data.success) {
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
                                    baseCheckoutUrl: data.base_checkout_url ?? ''
                                }
                            }));

                            feedbackDiv.innerHTML = '';
                            const successParagraph = document.createElement('p');
                            successParagraph.className = 'text-green-600';
                            successParagraph.textContent = data.success;
                            feedbackDiv.appendChild(successParagraph);
                            window.dispatchEvent(new CustomEvent('show-success', { detail: data.success }));
                        }
                    })
                    .catch(errorData => {
                        if (errorData && errorData.errors) {
                            // Erros de validação
                            let errorHtml = '<ul class="text-red-600 list-disc list-inside">';
                            Object.values(errorData.errors).forEach(error => {
                                errorHtml += `<li>${error[0]}</li>`;
                            });
                            errorHtml += '</ul>';
                            feedbackDiv.innerHTML = errorHtml;
                        } else {
                            // Outros erros
                            console.error('Erro:', errorData);
                            const errorMessage = (errorData && errorData.message)
                                ? errorData.message
                                : 'Ocorreu um erro inesperado. Tente novamente.';
                            feedbackDiv.innerHTML = '';
                            const paragraph = document.createElement('p');
                            paragraph.className = 'text-red-600';
                            paragraph.textContent = errorMessage;
                            feedbackDiv.appendChild(paragraph);
                        }
                    });
                });
            });
        });
    </script>
@endpush
</x-app-layout>
