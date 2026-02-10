<x-guest-layout>
    <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-700">Faça seu login</h2>
        <p class="text-gray-600 mb-4">Digite seu e-mail e senha para acessar o portal dos parceiros.</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full p-2 bg-gray-100" type="email" name="email" :value="old('email')" required autofocus placeholder="Digite seu e-mail" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <div class="flex justify-between items-center">
                <x-input-label for="password" value="Senha" />
                <a class="underline text-sm text-red-600 hover:text-gray-900 rounded-md" href="{{ route('password.request') }}">
                    <small>Clique AQUI se esqueceu sua senha?</small>
                </a>
            </div>
            <x-text-input id="password" class="block mt-1 w-full p-2 bg-gray-100" type="password" name="password" required placeholder="Digite sua senha" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input id="remember" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <span class="ms-2 text-sm text-gray-600">Manter logado</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Login') }}
            </x-primary-button>
        </div>
    </form>

    <div class="text-center mt-4">
        <a class="text-sm text-gray-600 hover:text-gray-900" href="{{ route('register') }}">
            Ainda não tem uma conta? <span class="font-bold text-blue-500">Clique AQUI</span> e faça o seu cadastro!
        </a>
    </div>

    <!-- Botão de Instalação PWA -->
    <div class="text-center mt-6">
        <button id="btn-install" style="display: none;"
          class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700">
            Adicionar à tela inicial
        </button>
    </div>
</x-guest-layout>

@push('scripts')
<script>
    if (window.location.pathname === '/login' && 'serviceWorker' in navigator) {
      navigator.serviceWorker
        .register('/sw.js')
        .then(reg => console.log('✅ SW registrado:', reg.scope))
        .catch(err => console.error('❌ Falha SW:', err));
    
      let deferredPrompt;
      const btn = document.getElementById('btn-install');
    
      window.addEventListener('beforeinstallprompt', e => {
        e.preventDefault();
        deferredPrompt = e;
        if (btn) {
            btn.style.display = 'inline-flex';
        }
      });
    
      if (btn) {
          btn.addEventListener('click', () => {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(choice => {
              if (choice.outcome === 'accepted') {
                console.log('Usuário aceitou a instalação');
              } else {
                console.log('Usuário rejeitou');
              }
              deferredPrompt = null;
              btn.style.display = 'none';
            });
          });
      }
    }
</script>
@endpush