<!-- Menu Dropdown "Minha Estrutura" -->
<div class="hidden sm:flex sm:items-center sm:ms-6">
    <x-dropdown align="left" width="48">
        <x-slot name="trigger">
            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                <div>Minha Estrutura</div>
                <div class="ms-1"><svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
            </button>
        </x-slot>
        <x-slot name="content">
            
            <x-dropdown-link :href="route('cadastrar_cursos')">Cursos</x-dropdown-link>
            <x-dropdown-link :href="route('afiliado_configurar_site')">Configurações do site</x-dropdown-link>
        </x-slot>
    </x-dropdown>
</div>

<!-- Menu Dropdown "Ferramentas" -->
<div class="hidden sm:flex sm:items-center sm:ms-6">
    <x-dropdown align="left" width="48">
        <x-slot name="trigger">
            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                <div>Ferramentas</div>
                <div class="ms-1"><svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
            </button>
        </x-slot>
        <x-slot name="content">
            <x-dropdown-link :href="route('encurtar_link_lista')">Encurtador de links</x-dropdown-link>
            <x-dropdown-link :href="route('afiliado.catalogo')">Catálogo Meta Ads</x-dropdown-link>
        </x-slot>
    </x-dropdown>
</div>

<!-- Menu Dropdown "Leads" -->
<div class="hidden sm:flex sm:items-center sm:ms-6">
    <x-dropdown align="left" width="48">
        <x-slot name="trigger">
            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                <div>Leads</div>
                <div class="ms-1">
                    <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
            </button>
        </x-slot>
        <x-slot name="content">
            <x-dropdown-link :href="route('hotmart_leads', ['version' => null])">
                Leads (Vendas)
            </x-dropdown-link>
            <x-dropdown-link :href="route('novos_leads')">
                Leads (Aulas Gratuitas)
            </x-dropdown-link>
        </x-slot>
    </x-dropdown>
</div>
<x-nav-link :href="route('ranking')" :active="request()->routeIs('ranking')">Ranking</x-nav-link>

<!-- Menu de Administração -->
@if (Auth::user()->nivel_acesso == App\Models\User::NIVEL_ACESSO_ADMIN)
    <div class="hidden sm:flex sm:items-center sm:ms-6">
        <x-dropdown align="left" width="48">
            <x-slot name="trigger">
                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-500 bg-red-100 hover:text-red-700 focus:outline-none transition ease-in-out duration-150">
                    <div>Administração</div>
                    <div class="ms-1"><svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
                </button>
            </x-slot>
            <x-slot name="content">
                <x-dropdown-link :href="route('dashboard_adm')">Dashboard ADM</x-dropdown-link>
                <x-dropdown-link :href="route('adm_portal_informacoes')">Informações do Portal</x-dropdown-link>
                <x-dropdown-link :href="route('cursos.novo')">Novo Curso</x-dropdown-link>
                <x-dropdown-link :href="route('adm_cursos_lista')">Lista de Cursos</x-dropdown-link>
                <x-dropdown-link :href="route('aulas_gratuitas_index')">Aulas Gratuitas</x-dropdown-link>
                <x-dropdown-link :href="route('combo.index')">Combos</x-dropdown-link>
            </x-slot>
        </x-dropdown>
    </div>
@endif
