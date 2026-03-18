<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jornada do Dashboard') }}
        </h2>
    </x-slot>

    @php
        $journeySteps = $steps->map(function ($step) {
            return [
                'id' => $step->id,
                'title' => $step->title,
                'slug' => $step->slug,
                'completion_rule' => $step->completion_rule,
                'xp' => $step->xp,
                'sort_order' => $step->sort_order,
                'is_active' => (bool) $step->is_active,
                'is_fixed' => (bool) $step->is_fixed,
                'goal_value' => $step->goal_value !== null ? (float) $step->goal_value : null,
                'description' => (string) ($step->description ?? ''),
                'videos' => $step->videos->map(fn ($video) => [
                    'id' => $video->id,
                    'journey_step_id' => $video->journey_step_id,
                    'title' => $video->title,
                    'youtube_id' => $video->youtube_id,
                    'support_html' => (string) ($video->support_html ?? ''),
                    'sort_order' => $video->sort_order,
                    'is_primary' => (bool) $video->is_primary,
                    'is_active' => (bool) $video->is_active,
                ])->values()->all(),
            ];
        })->values();
    @endphp

    <div
        class="py-12"
        x-data="journeyAdminManager({
            steps: @js($journeySteps),
            completionRules: @js($completionRules),
            extraCompletionRules: @js($extraCompletionRules ?? []),
            openStepId: @js(session('journey_manage_step')),
            stepStoreUrl: @js(route('admin.journey.steps.store'))
        })"
    >
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (false)
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 p-6">
                        <h3 class="text-lg font-semibold text-slate-900">Prêmio final da jornada</h3>
                        <p class="mt-1 text-sm leading-6 text-slate-600">
                            Configure o selo textual, os textos de resgate e o botão que aparecem quando o afiliado conclui as 5 etapas.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('admin.journey.reward.update') }}" class="space-y-6 p-6">
                        @csrf
                        @method('PUT')

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <x-input-label for="journey_reward_badge_name" value="Nome do selo" />
                                <x-text-input
                                    id="journey_reward_badge_name"
                                    name="badge_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    value="{{ old('badge_name', $rewardSetting->badge_name) }}"
                                    required
                                />
                            </div>

                            <div>
                                <x-input-label for="journey_reward_badge_icon" value="Classe do ícone" />
                                <x-text-input
                                    id="journey_reward_badge_icon"
                                    name="badge_icon"
                                    type="text"
                                    class="mt-1 block w-full"
                                    value="{{ old('badge_icon', $rewardSetting->badge_icon) }}"
                                    required
                                />
                                <p class="mt-2 text-xs text-slate-500">Exemplo: <span class="font-mono">ri-shield-star-line</span></p>
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="journey_reward_pre_claim_text" value="Texto antes do resgate" />
                                <textarea
                                    id="journey_reward_pre_claim_text"
                                    name="pre_claim_text"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >{{ old('pre_claim_text', $rewardSetting->pre_claim_text) }}</textarea>
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="journey_reward_post_claim_text" value="Texto depois do resgate" />
                                <textarea
                                    id="journey_reward_post_claim_text"
                                    name="post_claim_text"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >{{ old('post_claim_text', $rewardSetting->post_claim_text) }}</textarea>
                            </div>

                            <div>
                                <x-input-label for="journey_reward_claim_button_label" value="Texto do botão" />
                                <x-text-input
                                    id="journey_reward_claim_button_label"
                                    name="claim_button_label"
                                    type="text"
                                    class="mt-1 block w-full"
                                    value="{{ old('claim_button_label', $rewardSetting->claim_button_label) }}"
                                    required
                                />
                            </div>
                        </div>

                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Prévia rápida</div>
                            <div class="mt-3 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-900">
                                <i class="{{ old('badge_icon', $rewardSetting->badge_icon) }}"></i>
                                {{ old('badge_name', $rewardSetting->badge_name) }}
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md border border-transparent bg-slate-900 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-slate-800"
                            >
                                Salvar prêmio
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-4 border-b border-slate-200 p-6 md:flex-row md:items-start md:justify-between">
                    <div class="max-w-3xl">
                        <h3 class="text-lg font-semibold text-slate-900">Etapas e aulas da jornada</h3>
                        <p class="mt-1 text-sm leading-6 text-slate-600">
                            As etapas 1 a 5 são base protegida. Novas etapas extras só podem usar metas de número de vendas ou total em vendas.
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="openCreateStep()"
                        class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-indigo-500"
                    >
                        Nova etapa extra
                    </button>
                </div>

                <div class="grid gap-4 p-6">
                    @forelse ($steps as $step)
                        <div class="overflow-hidden rounded-2xl border border-slate-200">
                            <div class="flex flex-col gap-4 bg-slate-50 px-5 py-4 lg:flex-row lg:items-start lg:justify-between">
                                <div class="space-y-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center rounded-full bg-slate-900 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-white">
                                            Etapa {{ $step->sort_order }}
                                        </span>
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $step->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                                            {{ $step->is_active ? 'Ativa' : 'Inativa' }}
                                        </span>
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $step->is_fixed ? 'bg-cyan-100 text-cyan-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ $step->is_fixed ? 'Base da jornada' : 'Etapa extra' }}
                                        </span>
                                        <span class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-1 text-[11px] font-semibold text-indigo-700">
                                            {{ $step->xp }} XP
                                        </span>
                                        <span class="inline-flex items-center rounded-full bg-slate-200 px-2.5 py-1 text-[11px] font-semibold text-slate-700">
                                            {{ $completionRules[$step->completion_rule] ?? $step->completion_rule }}
                                        </span>
                                        @if (!$step->is_fixed && $step->goal_value)
                                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-700">
                                                Meta: {{ in_array($step->completion_rule, ['sales_total_at_least'], true) ? 'R$ ' . number_format((float) $step->goal_value, 2, ',', '.') : (int) $step->goal_value }}
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="text-base font-semibold text-slate-900">{{ $step->title }}</h4>
                                        <p class="mt-1 text-sm leading-6 text-slate-600">
                                            {{ $step->description ?: 'Sem descrição complementar.' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <button
                                        type="button"
                                        @click='toggleVideos({{ $step->id }})'
                                        class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-widest text-slate-700 hover:bg-slate-100"
                                    >
                                        Gerenciar aulas
                                    </button>
                                    <button
                                        type="button"
                                        @click="openEditStepById({{ $step->id }})"
                                        class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-widest text-slate-700 hover:bg-slate-100"
                                    >
                                        Editar
                                    </button>
                                    @if (!$step->is_fixed)
                                        <form method="POST" action="{{ route('admin.journey.steps.destroy', $step) }}" onsubmit="return confirm('Deseja realmente excluir esta etapa e suas aulas?');">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="inline-flex items-center rounded-md border border-red-300 bg-red-50 px-3 py-2 text-xs font-semibold uppercase tracking-widest text-red-700 hover:bg-red-100"
                                            >
                                                Excluir
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center rounded-md border border-slate-300 bg-slate-100 px-3 py-2 text-xs font-semibold uppercase tracking-widest text-slate-500">
                                            Protegida
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div x-show="isVideosOpen({{ $step->id }})" x-cloak class="border-t border-slate-200 bg-white px-5 py-5">
                                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                    <div>
                                        <h5 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">Aulas da etapa</h5>
                                        <p class="mt-1 text-sm text-slate-600">
                                            O vídeo principal é usado como primeira aula da etapa e como abertura direta quando houver apenas 1 vídeo ativo.
                                        </p>
                                    </div>
                                    <button
                                        type="button"
                                        @click='openCreateVideo({{ $step->id }})'
                                        class="inline-flex items-center rounded-md border border-transparent bg-slate-900 px-3 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-slate-800"
                                    >
                                        Nova aula
                                    </button>
                                </div>

                                <div class="mt-4 overflow-x-auto">
                                    <table class="min-w-full divide-y divide-slate-200">
                                        <thead class="bg-slate-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Título</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">YouTube ID</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Ordem</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Status</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-200 bg-white">
                                            @forelse ($step->videos as $video)
                                                <tr>
                                                    <td class="px-4 py-3 text-sm text-slate-900">
                                                        <div class="font-medium">{{ $video->title }}</div>
                                                        @if ($video->support_html)
                                                            <div class="mt-1 text-xs text-slate-500">Com texto/material de apoio.</div>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $video->youtube_id }}</td>
                                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $video->sort_order }}</td>
                                                    <td class="px-4 py-3 text-sm text-slate-700">
                                                        <div class="flex flex-wrap gap-2">
                                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $video->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                                                                {{ $video->is_active ? 'Ativo' : 'Inativo' }}
                                                            </span>
                                                            @if ($video->is_primary)
                                                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-700">
                                                                    Principal
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3 text-right text-sm">
                                                        <div class="inline-flex flex-wrap gap-2">
                                                            <button
                                                                type="button"
                                                                @click="openEditVideoById({{ $video->id }})"
                                                                class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-700 hover:bg-slate-100"
                                                            >
                                                                Editar
                                                            </button>

                                                            <form method="POST" action="{{ route('admin.journey.videos.destroy', $video) }}" onsubmit="return confirm('Deseja realmente excluir esta aula?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button
                                                                    type="submit"
                                                                    class="inline-flex items-center rounded-md border border-red-300 bg-red-50 px-3 py-1.5 text-xs text-red-700 hover:bg-red-100"
                                                                >
                                                                    Excluir
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">
                                                        Nenhuma aula cadastrada para esta etapa.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-amber-300 bg-amber-50 p-6 text-sm text-amber-900">
                            Nenhuma etapa da jornada foi cadastrada ainda.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div
            x-show="stepModalOpen"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
            @keydown.escape.window="closeStepModal()"
        >
            <div class="w-full max-w-2xl rounded-2xl bg-white shadow-xl" @click.away="closeStepModal()">
                <div class="flex items-center justify-between border-b border-slate-200 p-5">
                    <h3 class="text-lg font-semibold text-slate-900" x-text="stepForm.editing ? 'Editar etapa' : 'Nova etapa'"></h3>
                    <button type="button" @click="closeStepModal()" class="text-slate-400 hover:text-slate-600">
                        <span class="sr-only">Fechar</span>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form method="POST" :action="stepForm.action" class="space-y-4 p-5">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" :disabled="!stepForm.editing">

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <x-input-label for="journey_step_title" value="Título da etapa" />
                            <x-text-input id="journey_step_title" name="title" type="text" class="mt-1 block w-full" x-model="stepForm.title" required />
                        </div>

                        <div>
                            <x-input-label for="journey_step_completion_rule" value="Gatilho automático" />
                            <template x-if="stepForm.is_fixed">
                                <div class="mt-1 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
                                    <span x-text="completionLabel(stepForm.completion_rule)"></span>
                                </div>
                            </template>
                            <template x-if="!stepForm.is_fixed">
                                <select id="journey_step_completion_rule" name="completion_rule" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" x-model="stepForm.completion_rule" required>
                                    <template x-for="value in extraCompletionRules" :key="value">
                                        <option :value="value" x-text="completionLabel(value)"></option>
                                    </template>
                                </select>
                            </template>
                            <input type="hidden" name="completion_rule" :value="stepForm.completion_rule" :disabled="!stepForm.is_fixed">
                        </div>

                        <div>
                            <x-input-label for="journey_step_xp" value="XP" />
                            <x-text-input id="journey_step_xp" name="xp" type="number" min="0" class="mt-1 block w-full" x-model="stepForm.xp" required />
                        </div>

                        <div>
                            <x-input-label for="journey_step_sort_order" value="Ordem" />
                            <template x-if="stepForm.is_fixed">
                                <div class="mt-1 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
                                    <span x-text="stepForm.sort_order"></span>
                                </div>
                            </template>
                            <template x-if="!stepForm.is_fixed">
                                <x-text-input id="journey_step_sort_order" name="sort_order" type="number" min="6" class="mt-1 block w-full" x-model="stepForm.sort_order" required />
                            </template>
                            <input type="hidden" name="sort_order" :value="stepForm.sort_order" :disabled="!stepForm.is_fixed">
                        </div>

                        <div class="md:col-span-2" x-show="!stepForm.is_fixed">
                            <label for="journey_step_goal_value" class="block text-sm font-medium text-gray-700" x-text="goalValueLabel()"></label>
                            <input
                                id="journey_step_goal_value"
                                name="goal_value"
                                type="number"
                                x-bind:step="stepForm.completion_rule === 'sales_count_at_least' ? 1 : 0.01"
                                x-bind:min="stepForm.completion_rule === 'sales_count_at_least' ? 1 : 0.01"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                x-model="stepForm.goal_value"
                                x-bind:required="!stepForm.is_fixed"
                            />
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <input id="journey_step_is_active" name="is_active" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" x-model="stepForm.is_active" :disabled="stepForm.is_fixed">
                            <x-input-label for="journey_step_is_active" value="Etapa ativa" />
                            <span x-show="stepForm.is_fixed" class="text-xs text-slate-500">Etapas base não podem ser desativadas.</span>
                            <input type="hidden" name="is_active" value="1" :disabled="!stepForm.is_fixed">
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="journey_step_description" value="Descrição" />
                            <textarea id="journey_step_description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" x-model="stepForm.description"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="closeStepModal()" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-700 hover:bg-slate-50">
                            Cancelar
                        </button>
                        <button type="submit" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-500">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div
            x-show="videoModalOpen"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
            @keydown.escape.window="closeVideoModal()"
        >
            <div class="w-full max-w-3xl rounded-2xl bg-white shadow-xl" @click.away="closeVideoModal()">
                <div class="flex items-center justify-between border-b border-slate-200 p-5">
                    <h3 class="text-lg font-semibold text-slate-900" x-text="videoForm.editing ? 'Editar aula' : 'Nova aula'"></h3>
                    <button type="button" @click="closeVideoModal()" class="text-slate-400 hover:text-slate-600">
                        <span class="sr-only">Fechar</span>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form method="POST" :action="videoForm.action" class="space-y-4 p-5">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" :disabled="!videoForm.editing">

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <x-input-label for="journey_video_title" value="Título da aula" />
                            <x-text-input id="journey_video_title" name="title" type="text" class="mt-1 block w-full" x-model="videoForm.title" required />
                        </div>

                        <div>
                            <x-input-label for="journey_video_youtube_id" value="YouTube ID" />
                            <x-text-input id="journey_video_youtube_id" name="youtube_id" type="text" class="mt-1 block w-full" x-model="videoForm.youtube_id" required />
                        </div>

                        <div>
                            <x-input-label for="journey_video_sort_order" value="Ordem" />
                            <x-text-input id="journey_video_sort_order" name="sort_order" type="number" min="0" class="mt-1 block w-full" x-model="videoForm.sort_order" required />
                        </div>

                        <div class="flex items-center gap-3 pt-6">
                            <input id="journey_video_is_primary" name="is_primary" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" x-model="videoForm.is_primary">
                            <x-input-label for="journey_video_is_primary" value="Vídeo principal" />
                        </div>

                        <div class="flex items-center gap-3 pt-6">
                            <input id="journey_video_is_active" name="is_active" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" x-model="videoForm.is_active">
                            <x-input-label for="journey_video_is_active" value="Aula ativa" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="journey_video_support_html" value="Texto/material de apoio (HTML livre)" />
                            <textarea id="journey_video_support_html" name="support_html" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" x-model="videoForm.support_html"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="closeVideoModal()" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-700 hover:bg-slate-50">
                            Cancelar
                        </button>
                        <button type="submit" class="inline-flex items-center rounded-md border border-transparent bg-slate-900 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-slate-800">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function journeyAdminManager(config) {
            return {
                steps: Array.isArray(config.steps) ? config.steps : [],
                completionRules: config.completionRules || {},
                extraCompletionRules: Array.isArray(config.extraCompletionRules) ? config.extraCompletionRules : [],
                openStepId: config.openStepId ?? null,
                stepStoreUrl: config.stepStoreUrl,
                stepModalOpen: false,
                videoModalOpen: false,
                stepForm: {
                    editing: false,
                    action: config.stepStoreUrl,
                    id: null,
                    is_fixed: false,
                    title: '',
                    completion_rule: (Array.isArray(config.extraCompletionRules) && config.extraCompletionRules.length > 0) ? config.extraCompletionRules[0] : '',
                    xp: 0,
                    sort_order: 6,
                    goal_value: 1,
                    is_active: true,
                    description: '',
                },
                videoForm: {
                    editing: false,
                    action: '',
                    id: null,
                    journey_step_id: null,
                    title: '',
                    youtube_id: '',
                    support_html: '',
                    sort_order: 0,
                    is_primary: false,
                    is_active: true,
                },

                isVideosOpen(stepId) {
                    return Number(this.openStepId) === Number(stepId);
                },

                toggleVideos(stepId) {
                    this.openStepId = this.isVideosOpen(stepId) ? null : stepId;
                },

                completionLabel(value) {
                    return this.completionRules[value] || value || '';
                },

                goalValueLabel() {
                    if (this.stepForm.completion_rule === 'sales_total_at_least') {
                        return 'Valor mínimo em vendas (R$)';
                    }

                    return 'Quantidade mínima de vendas';
                },

                nextExtraSortOrder() {
                    const extraSteps = this.steps.filter((step) => !step.is_fixed);
                    if (extraSteps.length === 0) {
                        return 6;
                    }

                    const maxOrder = Math.max(...extraSteps.map((step) => Number(step.sort_order || 6)));
                    return Math.max(6, maxOrder + 1);
                },

                openCreateStep() {
                    this.stepForm = {
                        editing: false,
                        action: this.stepStoreUrl,
                        id: null,
                        is_fixed: false,
                        title: '',
                        completion_rule: this.extraCompletionRules[0] || '',
                        xp: 0,
                        sort_order: this.nextExtraSortOrder(),
                        goal_value: 1,
                        is_active: true,
                        description: '',
                    };
                    this.stepModalOpen = true;
                },

                openEditStep(step) {
                    this.stepForm = {
                        editing: true,
                        action: `/administrador/jornada/etapas/${step.id}`,
                        id: step.id,
                        is_fixed: !!step.is_fixed,
                        title: step.title || '',
                        completion_rule: step.completion_rule || '',
                        xp: Number(step.xp || 0),
                        sort_order: Number(step.sort_order || 0),
                        goal_value: step.goal_value !== null && step.goal_value !== undefined ? Number(step.goal_value) : 1,
                        is_active: !!step.is_active,
                        description: step.description || '',
                    };
                    this.stepModalOpen = true;
                },

                openEditStepById(stepId) {
                    const step = this.steps.find((item) => Number(item.id) === Number(stepId));
                    if (!step) {
                        return;
                    }

                    this.openEditStep(step);
                },

                closeStepModal() {
                    this.stepModalOpen = false;
                },

                openCreateVideo(stepId) {
                    const step = this.steps.find((item) => Number(item.id) === Number(stepId));
                    const nextSortOrder = step && Array.isArray(step.videos) ? step.videos.length + 1 : 1;

                    this.videoForm = {
                        editing: false,
                        action: `/administrador/jornada/etapas/${stepId}/videos`,
                        id: null,
                        journey_step_id: stepId,
                        title: '',
                        youtube_id: '',
                        support_html: '',
                        sort_order: nextSortOrder,
                        is_primary: !step || !Array.isArray(step.videos) || !step.videos.some((video) => video.is_primary),
                        is_active: true,
                    };
                    this.openStepId = stepId;
                    this.videoModalOpen = true;
                },

                openEditVideo(video) {
                    this.videoForm = {
                        editing: true,
                        action: `/administrador/jornada/videos/${video.id}`,
                        id: video.id,
                        journey_step_id: video.journey_step_id,
                        title: video.title || '',
                        youtube_id: video.youtube_id || '',
                        support_html: video.support_html || '',
                        sort_order: Number(video.sort_order || 0),
                        is_primary: !!video.is_primary,
                        is_active: !!video.is_active,
                    };
                    this.openStepId = video.journey_step_id;
                    this.videoModalOpen = true;
                },

                openEditVideoById(videoId) {
                    for (const step of this.steps) {
                        if (!Array.isArray(step.videos)) {
                            continue;
                        }

                        const video = step.videos.find((item) => Number(item.id) === Number(videoId));
                        if (!video) {
                            continue;
                        }

                        this.openEditVideo(video);
                        return;
                    }
                },

                closeVideoModal() {
                    this.videoModalOpen = false;
                },
            };
        }
    </script>
</x-app-layout>
