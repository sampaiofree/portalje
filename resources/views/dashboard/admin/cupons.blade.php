<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cupons de Desconto') }}
        </h2>
    </x-slot>

    <div
        class="py-12"
        x-data="{
            isOpen: false,
            editing: false,
            currentCupom: { id: null, codigo: '', desconto: '' },
            formAction: @js(route('admin.cupons.store')),
            openCreate() {
                this.editing = false;
                this.currentCupom = { id: null, codigo: '', desconto: '' };
                this.formAction = @js(route('admin.cupons.store'));
                this.isOpen = true;
            },
            openEdit(cupom) {
                this.editing = true;
                this.currentCupom = { ...cupom };
                this.formAction = `/administrador/cupons/${cupom.id}`;
                this.isOpen = true;
            },
            closeModal() {
                this.isOpen = false;
            }
        }"
    >
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Registros de Cupons</h3>
                        <button
                            type="button"
                            @click="openCreate"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        >
                            Novo
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Desconto</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($cupons as $cupom)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $cupom->codigo }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $cupom->desconto }}%</td>
                                        <td class="px-4 py-3 text-sm text-right">
                                            <div class="inline-flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    @click='openEdit(@json(["id" => $cupom->id, "codigo" => $cupom->codigo, "desconto" => $cupom->desconto]))'
                                                    class="inline-flex items-center px-3 py-1.5 bg-gray-100 border border-gray-300 rounded-md text-xs text-gray-700 hover:bg-gray-200"
                                                >
                                                    Editar
                                                </button>

                                                <form method="POST" action="{{ route('admin.cupons.destroy', $cupom) }}" onsubmit="return confirm('Deseja realmente excluir este cupom?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-50 border border-red-300 rounded-md text-xs text-red-700 hover:bg-red-100"
                                                    >
                                                        Excluir
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-6 text-sm text-gray-500 text-center">
                                            Nenhum cupom cadastrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div
            x-show="isOpen"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
            @keydown.escape.window="closeModal"
        >
            <div
                class="bg-white rounded-lg shadow-xl w-full max-w-lg"
                @click.away="closeModal"
            >
                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900" x-text="editing ? 'Editar Cupom' : 'Novo Cupom'"></h4>
                    <button type="button" @click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <span class="sr-only">Fechar</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form method="POST" :action="formAction" class="p-4 space-y-4">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" :disabled="!editing">

                    <div>
                        <x-input-label for="cupom_codigo" value="Código" />
                        <x-text-input
                            id="cupom_codigo"
                            name="codigo"
                            type="text"
                            class="mt-1 block w-full"
                            maxlength="30"
                            x-model="currentCupom.codigo"
                            required
                        />
                    </div>

                    <div>
                        <x-input-label for="cupom_desconto" value="Desconto (%)" />
                        <x-text-input
                            id="cupom_desconto"
                            name="desconto"
                            type="number"
                            step="0.01"
                            min="1"
                            max="100"
                            class="mt-1 block w-full"
                            x-model="currentCupom.desconto"
                            required
                        />
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="closeModal" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
