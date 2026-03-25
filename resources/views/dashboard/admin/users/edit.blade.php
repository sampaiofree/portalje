<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Editar Usuário
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Ajuste dados operacionais do afiliado sem precisar acessar a conta dele.
                </p>
            </div>

            <a
                href="{{ route('admin.users.index') }}"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Voltar para usuários
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    Revise os campos destacados e tente novamente.
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Resumo</h3>

                    <dl class="mt-4 space-y-3 text-sm">
                        <div>
                            <dt class="text-gray-500">ID</dt>
                            <dd class="font-medium text-gray-900">{{ $editingUser->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Cadastro</dt>
                            <dd class="font-medium text-gray-900">{{ optional($editingUser->created_at)->format('d/m/Y H:i') ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Última atualização</dt>
                            <dd class="font-medium text-gray-900">{{ optional($editingUser->updated_at)->format('d/m/Y H:i') ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Email verificado</dt>
                            <dd class="font-medium text-gray-900">{{ optional($editingUser->email_verified_at)->format('d/m/Y H:i') ?: 'Não' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Telefone 1 verificado</dt>
                            <dd class="font-medium text-gray-900">{{ optional($editingUser->telefone_pessoal_1_verified_at)->format('d/m/Y H:i') ?: 'Não' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Telefone pendente</dt>
                            <dd class="font-medium text-gray-900">{{ $editingUser->telefone_pessoal_1_pending ?: '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="lg:col-span-2 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <form method="POST" action="{{ route('admin.users.update', $editingUser) }}" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <section class="space-y-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Dados principais</h3>
                                <p class="text-sm text-gray-500">Campos básicos de conta e identificação.</p>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <x-input-label for="name" value="Nome" />
                                    <x-text-input
                                        id="name"
                                        name="name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        :value="old('name', $editingUser->name)"
                                        required
                                    />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="email" value="Email" />
                                    <x-text-input
                                        id="email"
                                        name="email"
                                        type="email"
                                        class="mt-1 block w-full"
                                        :value="old('email', $editingUser->email)"
                                        required
                                    />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="nivel_acesso" value="Nível de acesso" />
                                    <select
                                        id="nivel_acesso"
                                        name="nivel_acesso"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="user" @selected(old('nivel_acesso', $editingUser->nivel_acesso) === 'user')>Usuário</option>
                                        <option value="admin" @selected(old('nivel_acesso', $editingUser->nivel_acesso) === 'admin')>Administrador</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('nivel_acesso')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="apelido" value="Apelido no ranking" />
                                    <x-text-input
                                        id="apelido"
                                        name="apelido"
                                        type="text"
                                        class="mt-1 block w-full"
                                        :value="old('apelido', $editingUser->apelido)"
                                    />
                                    <x-input-error :messages="$errors->get('apelido')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="nome_empresa" value="Nome da empresa" />
                                    <x-text-input
                                        id="nome_empresa"
                                        name="nome_empresa"
                                        type="text"
                                        class="mt-1 block w-full"
                                        :value="old('nome_empresa', $editingUser->nome_empresa)"
                                    />
                                    <x-input-error :messages="$errors->get('nome_empresa')" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-4">
                                    <input type="hidden" name="email_verified" value="0">
                                    <input
                                        type="checkbox"
                                        name="email_verified"
                                        value="1"
                                        class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                        @checked(old('email_verified', $editingUser->email_verified_at ? '1' : '0') === '1')
                                    >
                                    <span>
                                        <span class="block text-sm font-medium text-gray-900">Marcar email como verificado</span>
                                        <span class="block text-sm text-gray-500">Se desmarcar, o usuário volta a exigir verificação de email.</span>
                                    </span>
                                </label>

                                <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-4">
                                    <input type="hidden" name="telefone_pessoal_1_verified" value="0">
                                    <input
                                        type="checkbox"
                                        name="telefone_pessoal_1_verified"
                                        value="1"
                                        class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                        @checked(old('telefone_pessoal_1_verified', $editingUser->telefone_pessoal_1_verified_at ? '1' : '0') === '1')
                                    >
                                    <span>
                                        <span class="block text-sm font-medium text-gray-900">Marcar telefone 1 como verificado</span>
                                        <span class="block text-sm text-gray-500">Se o telefone mudar, o status de verificação é recalculado com base nessa opção.</span>
                                    </span>
                                </label>
                            </div>
                        </section>

                        <section class="space-y-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Contato e rastreamento</h3>
                                <p class="text-sm text-gray-500">Telefones principais e dados de mídia.</p>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <x-input-label for="telefone_pessoal_1" value="Telefone pessoal 1" />
                                    <x-text-input
                                        id="telefone_pessoal_1"
                                        name="telefone_pessoal_1"
                                        type="text"
                                        inputmode="numeric"
                                        class="mt-1 block w-full"
                                        :value="old('telefone_pessoal_1', $editingUser->telefone_pessoal_1)"
                                        placeholder="Ex: 5562999998888"
                                    />
                                    <x-input-error :messages="$errors->get('telefone_pessoal_1')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="telefone_pessoal_2" value="Telefone pessoal 2" />
                                    <x-text-input
                                        id="telefone_pessoal_2"
                                        name="telefone_pessoal_2"
                                        type="text"
                                        inputmode="numeric"
                                        class="mt-1 block w-full"
                                        :value="old('telefone_pessoal_2', $editingUser->telefone_pessoal_2)"
                                        placeholder="Ex: 5562999997777"
                                    />
                                    <x-input-error :messages="$errors->get('telefone_pessoal_2')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="whatsapp_atendimento" value="WhatsApp de atendimento" />
                                    <x-text-input
                                        id="whatsapp_atendimento"
                                        name="whatsapp_atendimento"
                                        type="text"
                                        inputmode="numeric"
                                        class="mt-1 block w-full"
                                        :value="old('whatsapp_atendimento', $editingUser->whatsapp_atendimento)"
                                        placeholder="Ex: 5511999991111"
                                    />
                                    <x-input-error :messages="$errors->get('whatsapp_atendimento')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="meta_pixel_id" value="Meta Pixel ID" />
                                    <x-text-input
                                        id="meta_pixel_id"
                                        name="meta_pixel_id"
                                        type="text"
                                        class="mt-1 block w-full"
                                        :value="old('meta_pixel_id', $editingUser->meta_pixel_id)"
                                    />
                                    <x-input-error :messages="$errors->get('meta_pixel_id')" class="mt-2" />
                                </div>
                            </div>
                        </section>

                        <section class="space-y-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Domínios</h3>
                                <p class="text-sm text-gray-500">Você pode limpar os campos deixando-os vazios.</p>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <x-input-label for="dominio" value="Subdomínio interno" />
                                    <x-text-input
                                        id="dominio"
                                        name="dominio"
                                        type="text"
                                        class="mt-1 block w-full"
                                        :value="old('dominio', $editingUser->dominio)"
                                        placeholder="Ex: joaosilva ou joaosilva.{{ $baseDomain }}"
                                    />
                                    <p class="mt-1 text-xs text-gray-500">Aceita só o subdomínio ou o host completo do portal base `{{ $baseDomain }}`.</p>
                                    <x-input-error :messages="$errors->get('dominio')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="dominio_externo" value="Domínio externo" />
                                    <x-text-input
                                        id="dominio_externo"
                                        name="dominio_externo"
                                        type="text"
                                        class="mt-1 block w-full"
                                        :value="old('dominio_externo', $editingUser->dominio_externo)"
                                        placeholder="Ex: parceiro.com.br"
                                    />
                                    <p class="mt-1 text-xs text-gray-500">Pode colar com `https://` ou `www`, o sistema normaliza o host.</p>
                                    <x-input-error :messages="$errors->get('dominio_externo')" class="mt-2" />
                                </div>
                            </div>
                        </section>

                        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-6">
                            <p class="text-sm text-gray-500">
                                Alterações são salvas imediatamente e passam a valer no próximo acesso do usuário.
                            </p>

                            <x-primary-button>
                                Salvar alterações
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
