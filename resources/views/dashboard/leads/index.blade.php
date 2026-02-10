@php use Illuminate\Support\Str; @endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Meus Leads') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div 
                x-data="leadsManager()"
                class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
            >
                <!-- Card de Filtros -->
                <div class="p-6 border-b border-gray-200 bg-gray-50 rounded-md">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Filtrar Leads</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 gap-y-6">
                        <!-- Origem -->
                        <div>
                            <x-input-label value="Origem" />
                            <x-dropdown align="left" width="100">
                                <x-slot name="trigger">
                                    <x-secondary-button class="w-full justify-between">
                                        {{ $btn_leads_portal_hotmart }}
                                        <i class="ri-arrow-down-s-line ml-2"></i>
                                    </x-secondary-button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('hotmart_leads', ['version' => null])">Leads do Portal</x-dropdown-link>
                                    <x-dropdown-link :href="route('hotmart_leads', ['version' => '2.0.0'])">Leads da Hotmart</x-dropdown-link>
                                    <x-dropdown-link :href="route('hotmart_leads', ['version' => 'Grupo_WhatsApp'])">Leads de Grupos</x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>

                        <!-- Status -->
                        <div>
                            <x-input-label value="Status da Compra" />
                            <x-dropdown align="left" width="100">
                                <x-slot name="trigger">
                                    <x-secondary-button class="w-full justify-between">
                                        {{ $btn_status ?? 'Status' }}
                                        <i class="ri-arrow-down-s-line ml-2"></i>
                                    </x-secondary-button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('hotmart_leads', array_merge(request()->query(), ['purchase_status' => 'WAITING_PAYMENT']))">Aguardando Pagamento</x-dropdown-link>
                                    <x-dropdown-link :href="route('hotmart_leads', array_merge(request()->query(), ['purchase_status' => 'CANCELLED']))">Cartão Recusado</x-dropdown-link>
                                    <x-dropdown-link :href="route('hotmart_leads', array_merge(request()->query(), ['purchase_status' => 'EXPIRED']))">Pagamentos Expirados</x-dropdown-link>
                                    <x-dropdown-link :href="route('hotmart_leads', array_merge(request()->query(), ['purchase_status' => 'APPROVED']))">Vendas</x-dropdown-link>
                                    <x-dropdown-link :href="route('hotmart_leads', array_merge(request()->query(), ['purchase_status' => '']))">Mostrar Todos</x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>

                        <!-- Atendimento -->
                        <div>
                            <x-input-label value="Etapa de Atendimento" />
                            <x-dropdown align="left" width="100">
                                <x-slot name="trigger">
                                    <x-secondary-button class="w-full justify-between">
                                        {{ $btn_atendimento }}
                                        <i class="ri-arrow-down-s-line ml-2"></i>
                                    </x-secondary-button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('hotmart_leads', array_merge(request()->query(), ['atendimento' => 'aguardando']))">Aguardando Atendimento</x-dropdown-link>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <x-dropdown-link :href="route('hotmart_leads', array_merge(request()->query(), ['atendimento' => $i]))">{{ $i }}º Atendimento</x-dropdown-link>
                                    @endfor
                                    <div class="border-t border-gray-200 my-1"></div>
                                    <x-dropdown-link :href="route('hotmart_leads', array_merge(request()->query(), ['atendimento' => 'arquivado']))">Arquivados</x-dropdown-link>
                                    <x-dropdown-link :href="route('hotmart_leads', array_merge(request()->query(), ['atendimento' => '']))">Mostrar Todos</x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>

                        <!-- Data início -->
                        <div>
                            <x-input-label for="date_start" value="Data inicial" />
                            <x-text-input id="date_start" type="date" name="date_start" class="mt-1 block w-full py-2 px-3" :value="$btn_data" onchange="filterByDate(this, 'created_at')" />
                        </div>

                        <!-- Data fim -->
                        <div>
                            <x-input-label for="date_end" value="Data final" />
                            <x-text-input id="date_end" type="date" name="date_end" class="mt-1 block w-full py-2 px-3" :value="$btn_fim" onchange="filterByDate(this, 'date_fim')" />
                        </div>
                    </div>
                </div>


                <!-- Card de Ações em Massa -->
                <div class="p-6">
                    <div class="flex flex-wrap items-center gap-4">
                        <!--<x-primary-button @click.prevent="$dispatch('open-modal', 'whatsapp-bulk-modal')">
                            <i class="ri-whatsapp-line mr-1"></i> Enviar WhatsApp
                        </x-primary-button>-->
                       <form 
                            @submit.prevent="submitBulkAction('{{ route('alterar_atendimento') }}')" 
                            class="flex items-center gap-2"
                        >
                            <select x-model="bulkActionStatus" name="select_atendimento" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm py-2 px-3">
                                <option value="1">Marcar como 1º Atendimento</option>
                                <option value="2">Marcar como 2º Atendimento</option>
                                <option value="3">Marcar como 3º Atendimento</option>
                                <option value="4">Marcar como 4º Atendimento</option>
                                <option value="5">Marcar como 5º Atendimento</option>
                                <option value="arquivado">Arquivar Selecionados</option>
                            </select>

                            <x-secondary-button type="submit">
                                Aplicar
                            </x-secondary-button>
                        </form>

                        <a href="{{ route('hotmart_leads', array_merge(request()->query(), ['excel' => '1'])) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150">
                            <i class="ri-file-excel-2-line mr-2"></i>
                            Exportar para Excel
                        </a>
                    </div>
                </div>

                <!-- Tabela de Leads -->
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" @change="toggleSelectAll($event.target.checked)" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Atend.</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Curso</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Origem</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($hotmart_leads as $hotmart_lead)
                                {{-- Sua lógica de filtro de "Arquivados" foi mantida --}}
                                @if(($btn_atendimento != 'Arquivados' && $hotmart_lead['atendimento'] != 'arquivado') || ($btn_atendimento == 'Arquivados' && $hotmart_lead['atendimento'] == 'arquivado'))
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" :value="'{{ $hotmart_lead['id'] }}'" @change="handleCheckboxChange ($event)">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($hotmart_lead['atendimento'] && $hotmart_lead['atendimento'] != 'arquivado')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $hotmart_lead['atendimento'] }}º Atend.</span>
                                            @elseif($hotmart_lead['atendimento'] == 'arquivado')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Arquivado</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Aguardando</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex flex-col gap-1">
                                                <div>
                                                    {{ explode(" ", $hotmart_lead['buyer_name'])[0] }}
                                                </div>
                                                {{-- Telefone --}}
                                                <div>
                                                    <span class="px-2 inline-flex items-center gap-1 text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        <i class="ri-whatsapp-line text-base"></i>
                                                        {{ $hotmart_lead['buyer_checkout_phone'] }}
                                                    </span>
                                                </div>

                                                @if($hotmart_lead['buyer_email'])
                                                <div>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        <i class="ri-mail-line text-base"></i>
                                                        {{ $hotmart_lead['buyer_email'] }}
                                                    </span>
                                                </div>
                                                @endif
                                            </div>
                                        </td>


                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs">
                                            <div class="flex flex-col gap-1">
                                                {{-- Nome do curso com truncamento --}}
                                                <div class="truncate" title="{{ $hotmart_lead['product_name'] }}">
                                                    {{ $hotmart_lead['product_name'] }}
                                                </div>

                                                {{-- Transação (se contiver "HP") --}}
                                                @if(Str::contains($hotmart_lead['transaction'], 'HP'))
                                                    <div>
                                                        <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                            {{ $hotmart_lead['transaction'] }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex flex-col gap-1">
                                                {{-- Status do pagamento --}}
                                                <div>
                                                    @php
                                                        $status = strtoupper($hotmart_lead['purchase_status']);
                                                        if ($status == 'WAITING_PAYMENT' || $status == 'BILLET_PRINTED') { echo 'Aguard. Pag.'; }
                                                        elseif ($status == 'APPROVED' || $status == 'COMPLETED') { echo 'Venda'; }
                                                        elseif ($status == 'CANCELED' || $status == 'DELAYED') { echo 'Cancelado'; }
                                                        elseif ($status == 'EXPIRED') { echo 'Vencido'; }
                                                        else { echo $hotmart_lead['purchase_status']; }
                                                    @endphp
                                                </div>

                                                {{-- Link abaixo (PIX, Boleto ou Tipo de pagamento) --}}
                                                <div class="text-xs text-indigo-600">
                                                    @if(in_array($hotmart_lead['purchase_status'], ['WAITING_PAYMENT', 'BILLET_PRINTED']))                        
                                                        @if($hotmart_lead['purchase_payment_billet_url']) 
                                                            <a class="hover:underline" target="_blank" href="{{ $hotmart_lead['purchase_payment_billet_url'] }}">Boleto</a> 
                                                        @elseif($hotmart_lead['purchase_payment_pix_qrcode']) 
                                                            <a class="hover:underline" target="_blank" href="https://pay.hotmart.com/thanks?transactionReference={{ $hotmart_lead['transaction'] }}">PIX</a>
                                                        @else
                                                            {{ $hotmart_lead['purchase_payment_type'] }}
                                                        @endif
                                                    @else
                                                        {{ $hotmart_lead['purchase_payment_type'] }}    
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($hotmart_lead['created_at'])->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-12 text-gray-500">
                                        Nenhum lead encontrado com os filtros atuais.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginação -->
                <div class="p-6">
                    {{ $hotmart_leads->appends(request()->query())->links('pagination::tailwind') }}
                </div>

            </div>
        </div>
    </div>

    <script>
        function filterByDate(element, paramName) {
            const url = new URL(window.location.href);
            url.searchParams.set(paramName, element.value);
            window.location.href = url.toString();
        }

        function leadsManager() {
            return {
                selectedLeads: [],
                bulkActionStatus: '1',
                
                toggleSelectAll(checked) {
                    const allLeadIds = Array.from(document.querySelectorAll('tbody input[type="checkbox"]')).map(el => el.value);
                    this.selectedLeads = checked ? allLeadIds.map(id => ({ id })) : [];
                },
                handleCheckboxChange(e) {
                    const id = e.target.value;
                    if (e.target.checked) {
                        this.selectedLeads.push({ id });
                    } else {
                        this.selectedLeads = this.selectedLeads.filter(lead => lead.id !== id);
                    }
                },
                submitBulkAction(actionUrl) {
                    if (this.selectedLeads.length === 0) {
                        alert('Por favor, selecione pelo menos um lead.');
                        return;
                    }
                    
                    const form = event.target;
                    const formData = new FormData(form);
                    formData.append('selectedLeads', JSON.stringify(this.selectedLeads));
                    if (form.querySelector('[name="select_atendimento"]')) {
                         formData.append('select_atendimento', this.bulkActionStatus);
                    }

                    fetch(actionUrl, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        // Lógica de sucesso/erro
                        alert(data.message || 'Ação concluída!');
                        location.reload();
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Ocorreu um erro.');
                    });
                }
            }
        }
    </script>
</x-app-layout>