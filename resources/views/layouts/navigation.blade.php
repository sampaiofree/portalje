<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        {{-- Substitua 'img/logo.png' pelo caminho para a sua imagem --}}
                        <img src="{{ asset('img/logo/logo-je.png') }}" alt="Logo Jovem Empreendedor" class="block h-9 w-auto">
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    <!-- Desktop Dropdowns... -->
                    @include('layouts.partials.navigation-desktop-links')
                </div>
            </div>

            <!-- Settings Dropdown (Desktop) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1"><svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <!--<x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>-->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <!-- Seção Minha Estrutura -->
            <div class="pt-2 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">Minha Estrutura</div>
                </div>
                <div class="mt-2 space-y-1">
                    
                    <x-responsive-nav-link :href="route('cadastrar_cursos')">Cursos</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('afiliado_configurar_site')">Configurações do site</x-responsive-nav-link>
                </div>
            </div>

            <!-- Seção Ferramentas -->
            <div class="pt-2 pb-1 border-t border-gray-200">
                <div class="px-4"><div class="font-medium text-base text-gray-800">Ferramentas</div></div>
                <div class="mt-2 space-y-1">
                    <x-responsive-nav-link :href="route('encurtar_link_lista')">Encurtador de links</x-responsive-nav-link>
                </div>
            </div>

            <!-- Links Diretos -->
            <!-- Seção Leads -->
            <div class="pt-2 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">Leads</div>
                </div>
                <div class="mt-2 space-y-1">
                    <x-responsive-nav-link :href="route('hotmart_leads', ['version' => null])">
                        Leads (Vendas)
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('novos_leads')">
                        Leads (Aulas Gratuitas)
                    </x-responsive-nav-link>
                </div>
            </div>
            <x-responsive-nav-link :href="route('ranking')" :active="request()->routeIs('ranking')">Ranking</x-responsive-nav-link>

            <!-- Menu de Administração (Mobile) -->
            @if (Auth::user()->nivel_acesso == App\Models\User::NIVEL_ACESSO_ADMIN)
                <div class="pt-2 pb-1 border-t border-gray-200">
                    <div class="px-4"><div class="font-medium text-base text-red-600">Administração</div></div>
                    <div class="mt-2 space-y-1">
                        <x-responsive-nav-link :href="route('dashboard_adm')">Dashboard ADM</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('cursos.novo')">Novo Curso</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('adm_cursos_lista')">Lista de Cursos</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('aulas_gratuitas_index')">Aulas Gratuitas</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('combo.index')">Combos</x-responsive-nav-link>
                    </div>
                </div>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <!--<x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>-->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>