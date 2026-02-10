<x-app-layout>
    {{-- O cabeçalho da página, que geralmente vai na barra de navegação superior --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verifique seu Endereço de E-mail') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Bloco de mensagem de sucesso ao reenviar o e-mail --}}
                    @if (session('status') == 'verification-link-sent')
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ __('Um novo link de verificação foi enviado para o seu endereço de e-mail.') }}
                        </div>
                    @endif

                    <div class="mb-4 text-sm">
                        {{ __('Antes de continuar, por favor, verifique seu e-mail para encontrar o link de verificação.') }}
                        {{ __('Se você não recebeu o e-mail, clique no botão abaixo para solicitar um novo.') }}
                    </div>

                    {{-- Botões de Ação --}}
                    <div class="mt-4 flex items-center justify-between">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <x-primary-button>
                                {{ __('Reenviar E-mail de Verificação') }}
                            </x-primary-button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md">
                                {{ __('Sair') }}
                            </button>
                        </form>
                    </div>

                    {{-- Sua mensagem customizada --}}
                    <div class="border-t pt-4 mt-6 text-center text-sm text-gray-500">
                        {{ __('Se você estiver com dificuldades para ativar sua conta, entre em contato pelo WhatsApp') }}
                        <a href="https://wa.me/5562995772922" target="_blank" class="underline font-medium">(62) 99577-2922</a>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
