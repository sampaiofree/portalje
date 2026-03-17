<x-guest-layout>
    @if (session('status') === 'phone-code-sent')
        <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700">
            Seu codigo foi preparado. Agora clique no botao abaixo, envie a mensagem pronta e depois digite o codigo recebido.
        </div>
    @endif

    <div class="mb-4">
        <h1 class="text-lg font-semibold text-gray-900">Verifique seu WhatsApp</h1>
        <p class="mt-2 text-sm text-gray-600">
            Informe seu numero pessoal do WhatsApp com DDI. Esse numero nao sera exibido como atendimento e sera usado apenas para contato com o nosso suporte.
        </p>
    </div>

    <form id="phone-verification-send-form" method="POST" action="{{ route('phone.verification.send') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="telefone_pessoal_1" value="WhatsApp com DDI" />
            <x-text-input
                id="telefone_pessoal_1"
                name="telefone_pessoal_1"
                type="text"
                inputmode="numeric"
                pattern="[0-9]{12,14}"
                maxlength="14"
                class="mt-1 block w-full bg-gray-100 p-3"
                :value="old('telefone_pessoal_1', $pendingPhone)"
                placeholder="Ex: 5562999998888"
                required
            />
            <x-input-error :messages="$errors->get('telefone_pessoal_1')" class="mt-2" />
        </div>

        <div
            id="ddi-warning-card"
            tabindex="-1"
            class="hidden rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-300"
        >
            <p class="font-semibold">Confira o DDI do seu WhatsApp</p>
            <p class="mt-1">
                Para numeros do Brasil, comece com 55 (DDI). Exemplo: 5562999998888.
            </p>
            <button
                id="ddi-continue-button"
                type="button"
                class="mt-3 inline-flex items-center rounded-md bg-amber-600 px-3 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-300"
            >
                Continuar mesmo assim
            </button>
        </div>

        <x-primary-button id="phone-verification-submit-button" class="w-full justify-center">
            Gerar codigo de verificacao
        </x-primary-button>
    </form>

    @if($pendingPhone)
        <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
            <p class="text-sm font-medium text-gray-900">Numero em verificacao: {{ $pendingPhone }}</p>
            <p class="mt-1 text-sm text-gray-600">
                Envie a mensagem pronta no WhatsApp para receber o seu codigo.
            </p>

            @if($requestPhoneUrl)
                <a
                    href="{{ $requestPhoneUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mt-4 inline-flex w-full items-center justify-center rounded-md bg-green-600 px-4 py-3 text-sm font-semibold text-white hover:bg-green-500"
                >
                    Solicitar meu codigo no WhatsApp
                </a>
            @endif
        </div>

        <form method="POST" action="{{ route('phone.verification.confirm') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="code" value="Codigo recebido no WhatsApp" />
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
                />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </div>

            <x-primary-button class="w-full justify-center">
                Confirmar WhatsApp
            </x-primary-button>
        </form>
    @endif

    <div class="mt-6 flex items-center justify-end">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-600 underline hover:text-gray-900">
                Sair
            </button>
        </form>
    </div>
</x-guest-layout>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('phone-verification-send-form');
            const phoneInput = document.getElementById('telefone_pessoal_1');
            const warningCard = document.getElementById('ddi-warning-card');
            const continueButton = document.getElementById('ddi-continue-button');

            if (!form || !phoneInput || !warningCard || !continueButton) {
                return;
            }

            let confirmedNonBrazilPhone = null;

            const digitsOnly = (value) => value.replace(/\D/g, '');
            const hideWarning = () => warningCard.classList.add('hidden');
            const showWarning = () => {
                warningCard.classList.remove('hidden');
                warningCard.focus();
            };

            phoneInput.addEventListener('input', function () {
                const sanitized = digitsOnly(phoneInput.value);

                if (phoneInput.value !== sanitized) {
                    phoneInput.value = sanitized;
                }

                if (sanitized.startsWith('55')) {
                    confirmedNonBrazilPhone = null;
                    hideWarning();
                    return;
                }

                if (confirmedNonBrazilPhone !== null && sanitized !== confirmedNonBrazilPhone) {
                    confirmedNonBrazilPhone = null;
                }
            });

            form.addEventListener('submit', function (event) {
                const sanitized = digitsOnly(phoneInput.value);
                phoneInput.value = sanitized;

                if (sanitized === '' || sanitized.startsWith('55') || sanitized === confirmedNonBrazilPhone) {
                    return;
                }

                event.preventDefault();
                showWarning();
            });

            continueButton.addEventListener('click', function () {
                confirmedNonBrazilPhone = digitsOnly(phoneInput.value);
                hideWarning();
                form.requestSubmit();
            });
        });
    </script>
@endpush
