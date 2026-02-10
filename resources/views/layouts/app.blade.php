<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Portal dos Afiliados</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Ícones (Vamos manter os que você já usa) -->
        <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />

        {{-- Scripts e Estilos (Todos os seus antigos CSS e JS são substituídos por esta única linha do Vite) --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Espaço para estilos específicos da página, se necessário --}}
        @yield('head')
        @stack('head')
    </head>
    <body class="font-sans antialiased">
        <!-- Bloco de Notificação Global para Mensagens da Sessão Laravel -->
        @if (session('success') || session('error') || $errors->any())
                    <div
                    x-data="{
                    show: true,
                    type: '{{ session('success') ? 'success' : 'error' }}',
                    message: '{{ session('success') ?? session('error') }}'
                    }"
                    x-init="setTimeout(() => show = false, 5000)"
                    x-show="show"
                    x-transition:enter="transform ease-out duration-300 transition"
                    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    style="display: none;"
                    class="fixed top-20 right-5 z-50 w-full max-w-sm bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"
                    >
                    <div class="p-4" :class="{ 'border-l-4 border-green-500': type === 'success', 'border-l-4 border-red-500': type === 'error' }">
                    <div class="flex items-start">
                    <div class="flex-shrink-0">
                    <i x-show="type === 'success'" class="ri-checkbox-circle-fill text-2xl text-green-500"></i>
                    <i x-show="type === 'error'" class="ri-close-circle-fill text-2xl text-red-500"></i>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-semibold text-gray-900" x-text="type === 'success' ? 'Sucesso!' : 'Erro!'"></p>
                                {{-- Exibe a mensagem de sucesso/erro ou a lista de erros de validação --}}
                        @if ($errors->any())
                            <ul class="mt-1 text-sm text-gray-600 list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mt-1 text-sm text-gray-600" x-text="message"></p>
                        @endif

                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="show = false" class="inline-flex text-gray-400 hover:text-gray-500">
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="min-h-screen bg-gray-100">
            {{-- Inclui a barra de navegação principal, que adaptaremos --}}
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{-- O @yield('content') antigo agora é o {{ $slot }} --}}
                {{ $slot }}
            </main>
        </div>

        {{-- Aqui colocaremos seus modais e scripts customizados --}}
        @include('layouts.partials.modals')
        @yield('scripts')
        @stack('scripts')
        <script>
            /**
             * Dispara um evento para abrir o modal de vídeo com Alpine.js.
             * @param {string} link - O ID do vídeo do YouTube.
             * @param {string} titulo - O título para o cabeçalho do modal.
             * @param {string} texto - O HTML para a descrição abaixo do vídeo.
             */
            window.video_de_ajuda = (link, titulo, texto) => {
                const event = new CustomEvent('open-video-modal', {
                    detail: { link, title: titulo, text: texto }
                });
                window.dispatchEvent(event);
            };
        </script>
    </body>
</html>