<x-guest-layout>
    @if (session('status') === 'verification-code-sent')
        <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700">
            Enviamos um novo codigo de verificacao para {{ Auth::user()->email }}.
        </div>
    @endif

    @if (session('status') === 'email-verified')
        <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700">
            E-mail confirmado com sucesso.
        </div>
    @endif

    <div class="mb-4">
        <h1 class="text-lg font-semibold text-gray-900">Confirme seu e-mail</h1>
        <p class="mt-2 text-sm text-gray-600">
            Digite o codigo de 6 digitos enviado para <strong>{{ Auth::user()->email }}</strong>.
        </p>
    </div>

    <form method="POST" action="{{ route('verification.code') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="code" value="Codigo de verificacao" />
            <x-text-input
                id="code"
                name="code"
                type="text"
                inputmode="numeric"
                pattern="[0-9]{6}"
                maxlength="6"
                class="mt-1 block w-full bg-gray-100 p-3 tracking-[0.35em] text-center text-lg"
                :value="old('code')"
                required
                autofocus
            />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">
            Confirmar e-mail
        </x-primary-button>
    </form>

    <div class="mt-6 flex items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-secondary-button>
                Reenviar codigo
            </x-secondary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-600 underline hover:text-gray-900">
                Sair
            </button>
        </form>
    </div>
</x-guest-layout>
