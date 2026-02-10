<x-guest-layout>
    {{-- Bloco de mensagem de sucesso ao reenviar o e-mail --}}
    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            Um novo link de verificação foi enviado para o seu endereço de e-mail.
        </div>
    @endif

    <div class="mb-4 text-sm text-gray-600">
        Antes de continuar, por favor, verifique seu e-mail para encontrar o link de verificação. Se você não recebeu o e-mail, clique no botão abaixo para solicitar um novo.
    </div>

    {{-- O Breeze geralmente coloca os botões de Reenviar e Sair lado a lado --}}
    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-primary-button>
                Reenviar e-mail
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Sair
            </button>
        </form>
    </div>

    {{-- Sua mensagem customizada, estilizada com Tailwind --}}
    <div class="border-t pt-4 mt-4 text-center text-sm text-gray-500">
        Se você estiver com dificuldades para ativar sua conta, entre em contato pelo WhatsApp <a href="https://wa.me/5562995772922" target="_blank" class="underline">(62) 99577-2922</a>.
    </div>
</x-guest-layout>