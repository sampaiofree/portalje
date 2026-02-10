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
                    <x-danger-button @click.prevent="$dispatch('open-dominio-modal')">
                        <i class="ri-global-line mr-2"></i>Configurar Meu Site Agora
                    </x-danger-button>
                </div>
            @elseif(!Auth::user()->whatsapp_atendimento)
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 p-4 text-center rounded-md" role="alert">
                    <h4 class="font-bold text-lg flex items-center justify-center"><i class="ri-whatsapp-line mr-2"></i>Quase lá!</h4>
                    <p>Seu site está no ar, mas você precisa cadastrar seu WhatsApp de atendimento para não perder vendas.</p>
                    <hr class="my-3 border-yellow-300">
                    <x-primary-button @click.prevent="$dispatch('open-whatsapp-modal')" class="bg-yellow-500 hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700">
                        <i class="ri-edit-2-line mr-2"></i>Cadastrar Meu WhatsApp
                    </x-primary-button>
                </div>
            @endif

            <!-- SEÇÕES GUIADAS -->
            <div class="space-y-8">
                <!-- PASSO 1: CONFIGURAÇÃO E APRENDIZADO -->
                <div class="bg-white bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold mb-4 flex items-center text-gray-900 text-gray-100"><span class="bg-blue-500 text-white rounded-full h-8 w-8 flex items-center justify-center mr-3 text-sm">1</span>Comece Por Aqui: Treinamento e Configuração</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="#" onclick="video_de_ajuda('kJrqK9ZlrA0','Regras do Programa','')" class="flex items-center gap-3 p-4 bg-gray-50 bg-gray-700/50 rounded-lg hover:bg-gray-100 hover:bg-gray-700 transition"><i class="ri-file-shield-2-line text-2xl text-blue-500"></i> <span class="font-medium text-gray-700 text-gray-300">Regras do Programa</span></a>
                        <a href="https://www.youtube.com/playlist?list=PL8UPaaNJEdSDFGX9Pj20RBn7QCn7aSbaU" target="_blank" class="flex items-center gap-3 p-4 bg-gray-50 bg-gray-700/50 rounded-lg hover:bg-gray-100 hover:bg-gray-700 transition"><i class="ri-graduation-cap-line text-2xl text-blue-500"></i> <span class="font-medium text-gray-700 text-gray-300">Curso Método Carvalho</span></a>
                        <a href="https://www.youtube.com/playlist?list=PL8UPaaNJEdSBFoe9Pd1TBlklRrZUZt2lU" target="_blank" class="flex items-center gap-3 p-4 bg-gray-50 bg-gray-700/50 rounded-lg hover:bg-gray-100 hover:bg-gray-700 transition"><i class="ri-customer-service-2-line text-2xl text-red-500"></i> <span class="font-medium text-gray-700 text-gray-300">Curso de Atendimento</span></a>
                        <a href="https://chat.whatsapp.com/Lb7F6OSFUvx1kmyp4h7kgT" target="_blank" class="flex items-center gap-3 p-4 bg-gray-50 bg-gray-700/50 rounded-lg hover:bg-gray-100 hover:bg-gray-700 transition"><i class="ri-whatsapp-line text-2xl text-green-500"></i> <span class="font-medium text-gray-700 text-gray-300">Comunidade de Afiliados</span></a>
                    </div>
                </div>

                <!-- PASSO 2: FERRAMENTAS DO DIA A DIA -->
                <div class="bg-white bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold mb-4 flex items-center text-gray-900 text-gray-100"><span class="bg-blue-500 text-white rounded-full h-8 w-8 flex items-center justify-center mr-3 text-sm">2</span>Ferramentas do Dia a Dia</h3>
                    @if(Auth::user()->dominio || Auth::user()->dominio_externo)
                        @php $dominio = Auth::user()->dominio_externo ?? Auth::user()->dominio; @endphp
                        <div class="bg-gray-100 bg-gray-900/50 p-4 rounded-lg border border-gray-700 mb-6" id="geradorLinks">
                            <h5 class="font-semibold text-gray-800 text-gray-200 flex items-center gap-2"><i class="ri-links-line"></i>Gerador de Links de Divulgação</h5>
                            <p class="text-sm text-gray-600 text-gray-400">Use esta ferramenta para criar seus links com descontos e outros parâmetros.</p>
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 mt-3">
                                <div class="md:col-span-4">
                                    <x-input-label for="selectPagina" value="Página" class="text-xs"/>
                                    <select id="selectPagina" class="mt-1 block w-full border-gray-300 border-gray-700 bg-gray-900 text-gray-300 focus:border-indigo-500 focus:border-indigo-600 focus:ring-indigo-500 focus:ring-indigo-600 rounded-md shadow-sm text-sm">
                                        <option value="https://{{$dominio}}">Home page</option>
                                        {{-- ... outras opções ... --}}
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="selectCupom" value="Cupom" class="text-xs"/>
                                    <select id="selectCupom" class="mt-1 block w-full border-gray-300 border-gray-700 bg-gray-900 text-gray-300 focus:border-indigo-500 focus:border-indigo-600 focus:ring-indigo-500 focus:ring-indigo-600 rounded-md shadow-sm text-sm">
                                        <option value="">Sem cupom</option>
                                        <option value="o10">10%</option>
                                        {{-- ... outras opções ... --}}
                                    </select>
                                </div>
                                <div class="md:col-span-3">
                                    <x-input-label for="input_cidade" value="Cidade (Opcional)" class="text-xs"/>
                                    <x-text-input type="text" id="input_cidade" class="mt-1 block w-full text-sm" placeholder="Ex: salvador"/>
                                </div>
                                <div class="md:col-span-3">
                                    <x-input-label for="input_whatsapp2" value="WhatsApp 2 (Opcional)" class="text-xs"/>
                                    <x-text-input type="number" id="input_whatsapp2" class="mt-1 block w-full text-sm" placeholder="55119..."/>
                                </div>
                            </div>
                            <div class="mt-2 flex">
                                <input type="text" id="linkResultado" class="block w-full border-gray-300 border-gray-700 bg-gray-900/50 text-gray-300 rounded-l-md shadow-sm text-sm" readonly>
                                <button id="copiarLink" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-r-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Copiar</button>
                            </div>
                        </div>
                    @endif
                     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="https://drive.google.com/drive/folders/1H1Obc3ozkNDUkcimhDwHD1olZYvHBL2f?usp=sharing" target="_blank" class="flex items-center gap-3 p-4 bg-gray-50 bg-gray-700/50 rounded-lg hover:bg-gray-100 hover:bg-gray-700 transition"><i class="ri-folder-zip-line text-2xl text-blue-500"></i> <span class="font-medium text-gray-700 text-gray-300">Materiais de Apoio</span></a>
                        <a href="{{ route('hotmart_leads', ['version' => null]) }}" class="flex items-center gap-3 p-4 bg-gray-50 bg-gray-700/50 rounded-lg hover:bg-gray-100 hover:bg-gray-700 transition"><i class="ri-user-search-line text-2xl text-blue-500"></i> <span class="font-medium text-gray-700 text-gray-300">Meus Leads</span></a>
                    </div>
                </div>

                <!-- PASSO 3: ANÁLISE E OTIMIZAÇÃO -->
                <div class="bg-white bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold mb-4 flex items-center text-gray-900 text-gray-100"><span class="bg-blue-500 text-white rounded-full h-8 w-8 flex items-center justify-center mr-3 text-sm">3</span>Análise de Resultados</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @if($quantidade_vendas)
                            <div class="text-center p-4 border border-gray-700 rounded-lg">
                                <div class="text-sm font-medium text-gray-500 text-gray-400 uppercase">Faturamento Bruto</div>
                                <div class="text-4xl font-bold text-gray-800 text-gray-200 mt-2">R${{ number_format($total_sum, 2, ',', '.') }}</div>
                                <div class="text-sm text-gray-500 text-gray-400 mt-1">{{ $quantidade_vendas}} Vendas Totais</div>
                            </div>
                        @endif
                         @if($vencidos && $vencidos['n'] > 0)
                            <div class="text-center p-4 bg-yellow-50 bg-yellow-900/50 border border-yellow-300 border-yellow-700 rounded-lg">
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
                    <div class="text-center mt-6">
                         <a href="{{ route('ranking') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="ri-trophy-line mr-2"></i>Ver Ranking Completo de Afiliados
                         </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- O @section('scripts') antigo vai aqui, dentro de um @push --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Função para gerar o link e mostrar no campo de resultado
                function gerarLink() {
                    const geradorLinksDiv = document.getElementById('geradorLinks');
                    if (!geradorLinksDiv) return;

                    const pagina = geradorLinksDiv.querySelector('#selectPagina').value;
                    const desconto = geradorLinksDiv.querySelector('#selectCupom').value;
                    const cidade = geradorLinksDiv.querySelector('#input_cidade').value;
                    const whatsapp2 = geradorLinksDiv.querySelector('#input_whatsapp2').value;
                    const resultadoInput = geradorLinksDiv.querySelector('#linkResultado');
                    
                    const params = new URLSearchParams();
                    if (desconto) params.set('d', desconto);
                    if (cidade) params.set('c', cidade);
                    if (whatsapp2) params.set('t', whatsapp2);

                    const queryString = params.toString();
                    resultadoInput.value = queryString ? `${pagina}?${queryString}` : pagina;
                }

                function copiarLink() {
                    const resultadoInput = document.getElementById('linkResultado');
                    if (!resultadoInput || !resultadoInput.value) return;

                    navigator.clipboard.writeText(resultadoInput.value).then(() => {
                        // Idealmente, usar um componente de notificação mais robusto
                        alert('Link copiado!'); 
                    }).catch(err => {
                        console.error('Erro ao copiar link: ', err);
                        alert('Falha ao copiar o link.');
                    });
                }
                
                const geradorLinksDiv = document.getElementById('geradorLinks');
                if (geradorLinksDiv) {
                    geradorLinksDiv.addEventListener('input', gerarLink);
                    geradorLinksDiv.addEventListener('change', gerarLink);
                    geradorLinksDiv.querySelector('#copiarLink').addEventListener('click', copiarLink);
                    gerarLink(); // Gera o link inicial
                }
            });
        </script>
    @endpush
</x-app-layout>