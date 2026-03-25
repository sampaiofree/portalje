<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Usuários
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Listagem exclusiva para administração e edição de contas.
                </p>
            </div>

            <a
                href="{{ route('dashboard_adm') }}"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Voltar ao dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900">Filtros</h3>

                <form method="GET" action="{{ route('admin.users.index') }}" class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
                    <div class="xl:col-span-2">
                        <x-input-label for="search" value="Buscar" />
                        <x-text-input
                            id="search"
                            name="search"
                            type="text"
                            class="mt-1 block w-full"
                            :value="$filters['search'] ?? ''"
                            placeholder="Nome, email, telefone, domínio..."
                        />
                    </div>

                    <div>
                        <x-input-label for="nivel_acesso" value="Nível de acesso" />
                        <select
                            id="nivel_acesso"
                            name="nivel_acesso"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="all" @selected(($filters['nivel_acesso'] ?? 'all') === 'all')>Todos</option>
                            <option value="user" @selected(($filters['nivel_acesso'] ?? 'all') === 'user')>Usuários</option>
                            <option value="admin" @selected(($filters['nivel_acesso'] ?? 'all') === 'admin')>Administradores</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="email_verificado" value="Email verificado" />
                        <select
                            id="email_verificado"
                            name="email_verificado"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="all" @selected(($filters['email_verificado'] ?? 'all') === 'all')>Todos</option>
                            <option value="yes" @selected(($filters['email_verificado'] ?? 'all') === 'yes')>Sim</option>
                            <option value="no" @selected(($filters['email_verificado'] ?? 'all') === 'no')>Não</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="telefone_verificado" value="Telefone verificado" />
                        <select
                            id="telefone_verificado"
                            name="telefone_verificado"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="all" @selected(($filters['telefone_verificado'] ?? 'all') === 'all')>Todos</option>
                            <option value="yes" @selected(($filters['telefone_verificado'] ?? 'all') === 'yes')>Sim</option>
                            <option value="no" @selected(($filters['telefone_verificado'] ?? 'all') === 'no')>Não</option>
                        </select>
                    </div>

                    <div class="md:col-span-2 xl:col-span-5 flex flex-wrap gap-2">
                        <x-primary-button>Filtrar</x-primary-button>
                        <a
                            href="{{ route('admin.users.index') }}"
                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Limpar filtros
                        </a>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Total filtrado</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format((int) ($summary['total'] ?? 0), 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Administradores</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format((int) ($summary['total_admins'] ?? 0), 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Usuários</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format((int) ($summary['total_usuarios'] ?? 0), 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Emails verificados</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format((int) ($summary['emails_verificados'] ?? 0), 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Telefones verificados</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format((int) ($summary['telefones_verificados'] ?? 0), 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Todos os usuários ({{ number_format((int) $usuarios->total(), 0, ',', '.') }})
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nome</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nível</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Contato</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">WhatsApp</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Domínio</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Cadastro</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Ações</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($usuarios as $usuario)
                                @php
                                    $telefoneContato = trim((string) ($usuario->telefone_pessoal_1 ?? ''));
                                    if ($telefoneContato === '') {
                                        $telefoneContato = trim((string) ($usuario->telefone_pessoal_2 ?? ''));
                                    }

                                    $telefoneAtendimento = trim((string) ($usuario->whatsapp_atendimento ?? ''));
                                    if ($telefoneAtendimento === '' && isset($usuario->whatsappAtendimentos) && $usuario->whatsappAtendimentos->isNotEmpty()) {
                                        $telefoneAtendimento = trim((string) ($usuario->whatsappAtendimentos->first()->whatsapp ?? ''));
                                    }

                                    $dominioPrincipal = trim((string) ($usuario->dominio ?? ''));
                                    if ($dominioPrincipal === '') {
                                        $dominioPrincipal = trim((string) ($usuario->dominio_externo ?? ''));
                                    }
                                @endphp

                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <div class="font-medium">{{ $usuario->name }}</div>
                                        <div class="text-xs text-gray-500">#{{ $usuario->id }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $usuario->email }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $usuario->nivel_acesso === 'admin' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-700' }}">
                                            {{ $usuario->nivel_acesso === 'admin' ? 'Administrador' : 'Usuário' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $telefoneContato !== '' ? $telefoneContato : '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $telefoneAtendimento !== '' ? $telefoneAtendimento : '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $dominioPrincipal !== '' ? $dominioPrincipal : '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <div class="flex flex-col gap-1">
                                            <span class="{{ $usuario->email_verified_at ? 'text-emerald-700' : 'text-amber-700' }}">
                                                Email: {{ $usuario->email_verified_at ? 'verificado' : 'pendente' }}
                                            </span>
                                            <span class="{{ $usuario->telefone_pessoal_1_verified_at ? 'text-emerald-700' : 'text-amber-700' }}">
                                                Telefone: {{ $usuario->telefone_pessoal_1_verified_at ? 'verificado' : 'pendente' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ optional($usuario->created_at)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <a
                                            href="{{ route('admin.users.edit', $usuario) }}"
                                            class="inline-flex items-center rounded-md border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-100"
                                        >
                                            Editar
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-sm text-gray-500">
                                        Nenhum usuário encontrado para os filtros informados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 p-4">
                    {{ $usuarios->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
