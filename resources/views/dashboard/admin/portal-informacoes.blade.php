<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Informações do Portal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                    {{ session('error') }}
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

            <form method="POST" action="{{ route('adm_portal_informacoes_update') }}" class="space-y-6">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Contatos e Dados Institucionais</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="telefone_suporte_alunos" value="Telefone do Suporte aos Alunos" />
                                <x-text-input
                                    id="telefone_suporte_alunos"
                                    name="telefone_suporte_alunos"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="old('telefone_suporte_alunos', $dadosPortal->telefone_suporte_alunos ?? '')"
                                    required
                                />
                            </div>

                            <div>
                                <x-input-label for="telefone_suporte_afiliados" value="Telefone do Suporte aos Afiliados" />
                                <x-text-input
                                    id="telefone_suporte_afiliados"
                                    name="telefone_suporte_afiliados"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="old('telefone_suporte_afiliados', $dadosPortal->telefone_suporte_afiliados ?? '')"
                                />
                            </div>

                            <div>
                                <x-input-label for="cnpj" value="CNPJ" />
                                <x-text-input
                                    id="cnpj"
                                    name="cnpj"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="old('cnpj', $dadosPortal->cnpj ?? '')"
                                />
                            </div>

                            <div>
                                <x-input-label for="endereco" value="Endereço" />
                                <x-text-input
                                    id="endereco"
                                    name="endereco"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="old('endereco', $dadosPortal->endereco ?? '')"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Comportamento de Página</h3>

                        <div class="space-y-4">
                            <div>
                                <x-input-label for="whatsapp_atendimento_tempo" value="Tempo para exibir WhatsApp (segundos)" />
                                <x-text-input
                                    id="whatsapp_atendimento_tempo"
                                    name="whatsapp_atendimento_tempo"
                                    type="text"
                                    class="mt-1 block w-full md:w-80"
                                    :value="old('whatsapp_atendimento_tempo', $dadosPortal->whatsapp_atendimento_tempo ?? '0')"
                                />
                            </div>

                            <div class="flex items-center gap-3">
                                <input
                                    id="formulario_whatsapp"
                                    name="formulario_whatsapp"
                                    type="checkbox"
                                    value="1"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    @checked((int) old('formulario_whatsapp', isset($dadosPortal) ? (int) $dadosPortal->formulario_whatsapp : 1) === 1)
                                >
                                <label for="formulario_whatsapp" class="text-sm text-gray-700">
                                    Mostrar formulário WhatsApp
                                </label>
                            </div>

                            <div class="flex items-center gap-3">
                                <input
                                    id="formulario_pre_checkout"
                                    name="formulario_pre_checkout"
                                    type="checkbox"
                                    value="1"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    @checked((int) old('formulario_pre_checkout', isset($dadosPortal) ? (int) $dadosPortal->formulario_pre_checkout : 1) === 1)
                                >
                                <label for="formulario_pre_checkout" class="text-sm text-gray-700">
                                    Mostrar formulário Pre Checkout
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <x-primary-button>
                        {{ __('Salvar alterações') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
