<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Combo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-1">{{ $combo->titulo }}</h3>
                    <a target="_blank" href="{{ asset($combo->url) }}" class="text-indigo-600 text-sm hover:underline">
                        URL: {{ $combo->url }}
                    </a>

                    <form method="POST" action="{{ route('combo.editar') }}" class="mt-6 space-y-4">
                        @csrf
                        <input type="hidden" name="id" value="{{ $combo->id }}">

                        <div>
                            <x-input-label for="titulo" value="Nome do Combo" />
                            <x-text-input id="titulo" name="titulo" type="text" class="mt-1 block w-full" :value="$combo->titulo" />
                        </div>

                        <div>
                            <x-input-label for="url" value="URL" />
                            <x-text-input id="url" name="url" type="text" class="mt-1 block w-full" :value="$combo->url" />
                        </div>

                        <div>
                            <x-input-label for="headline" value="Headline" />
                            <x-text-input id="headline" name="headline" type="text" class="mt-1 block w-full" :value="$combo->headline" />
                        </div>

                        <div>
                            <x-input-label for="descricao_curta" value="Descrição Curta" />
                            <x-text-input id="descricao_curta" name="descricao_curta" type="text" class="mt-1 block w-full" :value="$combo->descricao_curta" />
                        </div>

                        <div>
                            <x-input-label for="link_checkout" value="Link do checkout" />
                            <x-text-input id="link_checkout" name="link_checkout" type="text" class="mt-1 block w-full" :value="$combo->link_checkout" />
                        </div>

                        <div>
                            <x-input-label for="preco_parcelado" value="Preço parcelado" />
                            <x-text-input id="preco_parcelado" name="preco_parcelado" type="text" class="mt-1 block w-full" :value="$combo->preco_parcelado" />
                        </div>

                        <div>
                            <x-input-label for="preco" value="Preço" />
                            <x-text-input id="preco" name="preco" type="text" class="mt-1 block w-full" :value="$combo->preco" />
                        </div>

                        <x-primary-button>Editar</x-primary-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
