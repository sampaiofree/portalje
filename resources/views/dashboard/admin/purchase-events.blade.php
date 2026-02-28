<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Purchase Events por Telefone') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @php
                $activeTagCount = 0;
                foreach ($tagFieldLabels as $field => $label) {
                    $activeTagCount += count($filters[$field]['include'] ?? []);
                    $activeTagCount += count($filters[$field]['exclude'] ?? []);
                }
                $hasAnyActiveFilters = (($filters['phone'] ?? '') !== '') || $activeTagCount > 0;
                $clearSearchUrl = route('admin.purchase_events', collect(request()->query())->except(['free_search', 'page'])->toArray());
                $clearFiltersUrl = route('admin.purchase_events', ($filters['free_search'] ?? '') !== '' ? ['free_search' => $filters['free_search']] : []);
            @endphp

            <div class="bg-white shadow-sm sm:rounded-lg p-4 border border-slate-200">
                <form method="GET" action="{{ route('admin.purchase_events') }}" class="space-y-3">
                    @if (($filters['date_start'] ?? '') !== '')
                        <input type="hidden" name="date_start" value="{{ $filters['date_start'] }}">
                    @endif
                    @if (($filters['date_end'] ?? '') !== '')
                        <input type="hidden" name="date_end" value="{{ $filters['date_end'] }}">
                    @endif
                    @if (($filters['phone'] ?? '') !== '')
                        <input type="hidden" name="phone" value="{{ $filters['phone'] }}">
                    @endif
                    <input type="hidden" name="per_page" value="{{ $filters['per_page'] }}">

                    @foreach ($tagFieldLabels as $field => $label)
                        @foreach (($filters[$field]['include'] ?? []) as $value)
                            <input type="hidden" name="{{ $field }}[]" value="{{ $value }}">
                        @endforeach
                        @foreach (($filters[$field]['exclude'] ?? []) as $value)
                            <input type="hidden" name="{{ $field }}_exclude[]" value="{{ $value }}">
                        @endforeach
                    @endforeach

                    <div class="grid grid-cols-1 md:grid-cols-[1fr_auto] gap-3 items-end">
                        <div>
                            <x-input-label for="free_search" value="Busca livre (nome, email, telefone, documento)" />
                            <x-text-input
                                id="free_search"
                                name="free_search"
                                type="text"
                                class="mt-1 block w-full bg-slate-50 border-slate-300 text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Digite qualquer termo para buscar"
                                value="{{ $filters['free_search'] ?? '' }}"
                            />
                        </div>

                        <div class="flex gap-2">
                            <x-primary-button type="submit">Buscar</x-primary-button>
                            @if (($filters['free_search'] ?? '') !== '')
                                <a href="{{ $clearSearchUrl }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-200">Limpar busca</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-4 border border-slate-200">
                <button
                    type="button"
                    id="purchase-filters-toggle"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100"
                    aria-expanded="{{ $hasAnyActiveFilters ? 'true' : 'false' }}"
                    data-open-label="Abrir filtros"
                    data-close-label="Ocultar filtros"
                >
                    <span id="purchase-filters-toggle-label">{{ $hasAnyActiveFilters ? 'Ocultar filtros' : 'Abrir filtros' }}</span>
                    @if ($activeTagCount > 0)
                        <span class="inline-flex items-center justify-center min-w-6 h-6 px-2 rounded-full text-xs font-semibold bg-indigo-600 text-white">
                            {{ $activeTagCount }}
                        </span>
                    @endif
                </button>
            </div>

            <div id="purchase-filters-panel" class="{{ $hasAnyActiveFilters ? '' : 'hidden' }} bg-slate-50/70 shadow-sm sm:rounded-lg p-6 border border-slate-200">
                <form id="purchase-events-filter-form" method="GET" action="{{ route('admin.purchase_events') }}" class="space-y-4">
                    <input type="hidden" name="free_search" value="{{ $filters['free_search'] ?? '' }}">

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <x-input-label for="date_start" value="Data inicial" />
                            <x-text-input id="date_start" name="date_start" type="date" class="mt-1 block w-full bg-white border-slate-300 text-slate-900 focus:border-indigo-500 focus:ring-indigo-500" value="{{ $filters['date_start'] }}" />
                        </div>

                        <div>
                            <x-input-label for="date_end" value="Data final" />
                            <x-text-input id="date_end" name="date_end" type="date" class="mt-1 block w-full bg-white border-slate-300 text-slate-900 focus:border-indigo-500 focus:ring-indigo-500" value="{{ $filters['date_end'] }}" />
                        </div>

                        <div>
                            <x-input-label for="phone" value="Telefone" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full bg-white border-slate-300 text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ex: 11999999999" value="{{ $filters['phone'] }}" />
                        </div>

                        <div>
                            <x-input-label for="per_page" value="Itens por página" />
                            <select id="per_page" name="per_page" class="mt-1 block w-full bg-white border-slate-300 text-slate-900 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach ([25, 50, 100, 200] as $size)
                                    <option value="{{ $size }}" @selected((int) $filters['per_page'] === $size)>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($tagFieldLabels as $field => $label)
                            <div>
                                <x-input-label :for="'tag_' . $field" :value="$label" />
                                <div class="relative mt-1 tag-field rounded-lg border border-slate-200 bg-white p-3" data-field="{{ $field }}" data-suggest-url="{{ route('admin.purchase_events.suggestions') }}">
                                    <input
                                        id="{{ 'tag_' . $field }}"
                                        type="text"
                                        class="tag-input block w-full bg-slate-50 border-slate-300 text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        placeholder="Digite 2+ caracteres"
                                        autocomplete="off"
                                    >

                                    <div class="tag-suggestions hidden absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-52 overflow-auto"></div>
                                    <p class="tag-feedback hidden text-xs text-red-600 mt-1"></p>

                                    <div class="mt-2">
                                        <div class="tag-list flex flex-wrap gap-2 min-h-7">
                                            @foreach (($filters[$field]['include'] ?? []) as $value)
                                                <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200 tag-item" data-value="{{ $value }}" data-type="include">
                                                    <span>{{ $value }}</span>
                                                    <button type="button" class="tag-remove text-indigo-600 hover:text-indigo-900" aria-label="Remover">x</button>
                                                </span>
                                                <input type="hidden" name="{{ $field }}[]" value="{{ $value }}" data-hidden-for="{{ $field }}" data-hidden-type="include">
                                            @endforeach

                                            @foreach (($filters[$field]['exclude'] ?? []) as $value)
                                                <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200 tag-item" data-value="{{ $value }}" data-type="exclude">
                                                    <span>{{ $value }}</span>
                                                    <button type="button" class="tag-remove text-rose-600 hover:text-rose-900" aria-label="Remover">x</button>
                                                </span>
                                                <input type="hidden" name="{{ $field }}_exclude[]" value="{{ $value }}" data-hidden-for="{{ $field }}" data-hidden-type="exclude">
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex gap-2">
                        <x-primary-button type="submit">Filtrar</x-primary-button>
                        <a href="{{ $clearFiltersUrl }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-200">Limpar</a>
                    </div>
                </form>
            </div>

            @if ($warningMessage)
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-md">
                    {{ $warningMessage }}
                </div>
            @endif

            @if (!$warningMessage)
                <div class="flex justify-end">
                    <a
                        href="{{ route('admin.purchase_events.export_csv', request()->query()) }}"
                        class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-emerald-700 rounded-md text-sm text-white hover:bg-emerald-700"
                    >
                        Exportar CSV
                    </a>
                </div>
            @endif

            @if ($purchaseEventsGrouped)
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eventos</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transações</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aprovadas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última ocorrência</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($purchaseEventsGrouped as $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $row->buyer_name ?: '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $row->buyer_checkout_phone }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ number_format((int) $row->total_events, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ number_format((int) $row->total_transactions, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ number_format((int) $row->total_approved, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            @if ($row->last_event_at)
                                                {{ \Carbon\Carbon::parse($row->last_event_at)->format('d/m/Y H:i') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700 js-open-related"
                                                data-phone="{{ $row->buyer_checkout_phone }}"
                                                data-name="{{ $row->buyer_name }}"
                                            >
                                                Ver
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                            Nenhum registro encontrado para os filtros informados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @php
                        $currentCount = $purchaseEventsGrouped->count();
                        $from = $currentCount > 0 ? (($purchaseEventsGrouped->currentPage() - 1) * $purchaseEventsGrouped->perPage()) + 1 : 0;
                        $to = $currentCount > 0 ? $from + $currentCount - 1 : 0;
                    @endphp

                    <div class="p-4 border-t border-gray-200 space-y-3">
                        <div class="text-sm text-gray-600">
                            Mostrando {{ number_format($from, 0, ',', '.') }} até {{ number_format($to, 0, ',', '.') }} de {{ number_format((int) $totalGroupedRecords, 0, ',', '.') }} registros agrupados.
                        </div>
                        <div>
                            {{ $purchaseEventsGrouped->links('pagination::simple-tailwind') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div id="related-modal" class="hidden fixed inset-0 z-50" aria-hidden="true">
        <div class="absolute inset-0 bg-black/50 js-modal-close"></div>
        <div class="relative mx-auto mt-8 w-[95%] max-w-6xl bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Registros relacionados</h3>
                <button type="button" class="text-gray-500 hover:text-gray-800 js-modal-close" aria-label="Fechar">x</button>
            </div>
            <div class="px-5 py-4 text-sm text-gray-600" id="related-modal-meta"></div>
            <div class="px-5 pb-5 overflow-x-auto max-h-[65vh]">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Data</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Nome</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Produto</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Evento</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Valor</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Status</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Pagamento</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Afiliado</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Código</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-600">Transação</th>
                        </tr>
                    </thead>
                    <tbody id="related-modal-body" class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const form = document.getElementById('purchase-events-filter-form');
            if (!form) return;

            const debounceTimers = new Map();
            const filtersToggle = document.getElementById('purchase-filters-toggle');
            const filtersToggleLabel = document.getElementById('purchase-filters-toggle-label');
            const filtersPanel = document.getElementById('purchase-filters-panel');
            const FILTER_PANEL_STORAGE_KEY = 'admin.purchase_events.filters.open';

            function updateFiltersToggleLabel() {
                if (!filtersToggle || !filtersToggleLabel || !filtersPanel) return;

                const isOpen = !filtersPanel.classList.contains('hidden');
                filtersToggleLabel.textContent = isOpen ? (filtersToggle.dataset.closeLabel || 'Ocultar filtros') : (filtersToggle.dataset.openLabel || 'Abrir filtros');
                filtersToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            }

            function readStoredPanelState() {
                try {
                    const value = window.localStorage.getItem(FILTER_PANEL_STORAGE_KEY);
                    if (value === '1') return true;
                    if (value === '0') return false;
                } catch (error) {
                    return null;
                }

                return null;
            }

            function writeStoredPanelState(isOpen) {
                try {
                    window.localStorage.setItem(FILTER_PANEL_STORAGE_KEY, isOpen ? '1' : '0');
                } catch (error) {
                    // localStorage pode estar indisponivel no navegador/modo privado.
                }
            }

            function setFiltersPanelOpen(isOpen) {
                if (!filtersPanel) return;
                filtersPanel.classList.toggle('hidden', !isOpen);
                updateFiltersToggleLabel();
            }

            if (filtersToggle && filtersPanel) {
                const storedPanelState = readStoredPanelState();
                if (storedPanelState !== null) {
                    setFiltersPanelOpen(storedPanelState);
                } else {
                    updateFiltersToggleLabel();
                }

                filtersToggle.addEventListener('click', () => {
                    const nextIsOpen = filtersPanel.classList.contains('hidden');
                    setFiltersPanelOpen(nextIsOpen);
                    writeStoredPanelState(nextIsOpen);
                });
            }

            function getAllQueryParams() {
                const data = new FormData(form);
                return new URLSearchParams(data);
            }

            function escapeSelector(value) {
                if (window.CSS && typeof window.CSS.escape === 'function') {
                    return window.CSS.escape(value);
                }

                return String(value).replace(/["\\\\]/g, '\\\\$&');
            }

            function getTagList(fieldContainer, type) {
                return fieldContainer.querySelector('.tag-list');
            }

            function getHiddenInputName(field, type) {
                return type === 'include' ? `${field}[]` : `${field}_exclude[]`;
            }

            function getTypeLabel(type) {
                return type === 'include' ? 'inclusão' : 'exclusão';
            }

            function setFieldFeedback(fieldContainer, message = '') {
                const feedback = fieldContainer.querySelector('.tag-feedback');
                if (!feedback) return;

                if (!message) {
                    feedback.textContent = '';
                    feedback.classList.add('hidden');
                    return;
                }

                feedback.textContent = message;
                feedback.classList.remove('hidden');
            }

            function hasTag(fieldContainer, value, type) {
                const field = fieldContainer.dataset.field;
                const inputName = getHiddenInputName(field, type);
                return Array.from(fieldContainer.querySelectorAll(`input[name="${inputName}"]`))
                    .some((input) => input.value === value);
            }

            function removeTag(fieldContainer, value, type) {
                const safeValue = escapeSelector(value);
                const tag = fieldContainer.querySelector(`.tag-item[data-value="${safeValue}"][data-type="${type}"]`);
                if (tag) tag.remove();

                const field = fieldContainer.dataset.field;
                const inputName = getHiddenInputName(field, type);
                fieldContainer
                    .querySelectorAll(`input[type="hidden"][data-hidden-for]`)
                    .forEach((input) => {
                        if (input.name === inputName && input.value === value) {
                            input.remove();
                        }
                    });

                setFieldFeedback(fieldContainer);
            }

            function addTag(fieldContainer, value, type) {
                const field = fieldContainer.dataset.field;
                const otherType = type === 'include' ? 'exclude' : 'include';
                const tagList = getTagList(fieldContainer, type);

                if (hasTag(fieldContainer, value, otherType)) {
                    setFieldFeedback(fieldContainer, `Esse valor já está na lista de ${getTypeLabel(otherType)}.`);
                    return false;
                }

                if (hasTag(fieldContainer, value, type)) {
                    setFieldFeedback(fieldContainer, `Esse valor já foi adicionado na lista de ${getTypeLabel(type)}.`);
                    return false;
                }

                setFieldFeedback(fieldContainer);

                const chip = document.createElement('span');
                chip.className = type === 'include'
                    ? 'inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200 tag-item'
                    : 'inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200 tag-item';
                chip.dataset.value = value;
                chip.dataset.type = type;
                chip.innerHTML = type === 'include'
                    ? `<span></span><button type="button" class="tag-remove text-indigo-600 hover:text-indigo-900" aria-label="Remover">x</button>`
                    : `<span></span><button type="button" class="tag-remove text-rose-600 hover:text-rose-900" aria-label="Remover">x</button>`;
                chip.querySelector('span').textContent = value;

                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = getHiddenInputName(field, type);
                hidden.value = value;
                hidden.dataset.hiddenFor = field;
                hidden.dataset.hiddenType = type;

                tagList.appendChild(chip);
                fieldContainer.appendChild(hidden);
                return true;
            }

            function hideSuggestions(box) {
                box.classList.add('hidden');
                box.innerHTML = '';
            }

            function renderSuggestions(fieldContainer, values) {
                const box = fieldContainer.querySelector('.tag-suggestions');
                const field = fieldContainer.dataset.field;

                if (!values.length) {
                    hideSuggestions(box);
                    return;
                }

                box.innerHTML = '';
                values.forEach((value) => {
                    const row = document.createElement('div');
                    row.className = 'flex items-center justify-between gap-2 px-3 py-2 text-sm border-b border-gray-100 last:border-b-0';

                    const text = document.createElement('span');
                    text.className = 'truncate text-gray-700';
                    text.textContent = value;

                    const actions = document.createElement('div');
                    actions.className = 'flex items-center gap-1 shrink-0';

                    const addButton = document.createElement('button');
                    addButton.type = 'button';
                    addButton.className = 'px-2 py-1 rounded bg-indigo-600 text-white text-xs hover:bg-indigo-700';
                    addButton.textContent = 'Adicionar';
                    addButton.addEventListener('click', () => {
                        const created = addTag(fieldContainer, value, 'include');
                        if (created) {
                            hideSuggestions(box);
                            const input = fieldContainer.querySelector('.tag-input');
                            input.value = '';
                            input.focus();
                        }
                    });

                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'px-2 py-1 rounded bg-rose-600 text-white text-xs hover:bg-rose-700';
                    removeButton.textContent = 'Remover';
                    removeButton.addEventListener('click', () => {
                        const created = addTag(fieldContainer, value, 'exclude');
                        if (created) {
                            hideSuggestions(box);
                            const input = fieldContainer.querySelector('.tag-input');
                            input.value = '';
                            input.focus();
                        }
                    });

                    actions.appendChild(addButton);
                    actions.appendChild(removeButton);
                    row.appendChild(text);
                    row.appendChild(actions);
                    box.appendChild(row);
                });

                box.classList.remove('hidden');
            }

            document.querySelectorAll('.tag-field').forEach((fieldContainer) => {
                const input = fieldContainer.querySelector('.tag-input');
                const box = fieldContainer.querySelector('.tag-suggestions');
                const suggestUrl = fieldContainer.dataset.suggestUrl;
                const field = fieldContainer.dataset.field;

                fieldContainer.addEventListener('click', (event) => {
                    const removeBtn = event.target.closest('.tag-remove');
                    if (!removeBtn) return;
                    const chip = removeBtn.closest('.tag-item');
                    if (!chip) return;
                    removeTag(fieldContainer, chip.dataset.value, chip.dataset.type || 'include');
                });

                input.addEventListener('input', () => {
                    const query = input.value.trim();
                    setFieldFeedback(fieldContainer);

                    if (debounceTimers.has(field)) {
                        clearTimeout(debounceTimers.get(field));
                    }

                    if (query.length < 2) {
                        hideSuggestions(box);
                        return;
                    }

                    debounceTimers.set(field, setTimeout(async () => {
                        const params = getAllQueryParams();
                        params.set('field', field);
                        params.set('q', query);

                        try {
                            const response = await fetch(`${suggestUrl}?${params.toString()}`, {
                                headers: { 'Accept': 'application/json' },
                            });

                            if (!response.ok) {
                                hideSuggestions(box);
                                return;
                            }

                            const values = await response.json();
                            renderSuggestions(fieldContainer, Array.isArray(values) ? values : []);
                        } catch (error) {
                            hideSuggestions(box);
                        }
                    }, 250));
                });

                input.addEventListener('blur', () => {
                    setTimeout(() => hideSuggestions(box), 120);
                });
            });

            const modal = document.getElementById('related-modal');
            const modalMeta = document.getElementById('related-modal-meta');
            const modalBody = document.getElementById('related-modal-body');
            const relatedUrl = @json(route('admin.purchase_events.related'));

            function closeModal() {
                modal.classList.add('hidden');
                modalBody.innerHTML = '';
                modalMeta.textContent = '';
            }

            function setModalLoading() {
                modalBody.innerHTML = '<tr><td colspan="10" class="px-3 py-4 text-center text-gray-500">Carregando...</td></tr>';
            }

            function escapeText(value) {
                if (value === null || value === undefined || value === '') return '-';
                return String(value);
            }

            async function openModal(phone, name) {
                modal.classList.remove('hidden');
                modalMeta.textContent = `Telefone: ${escapeText(phone)} | Nome: ${escapeText(name)}`;
                setModalLoading();

                try {
                    const response = await fetch(`${relatedUrl}?phone=${encodeURIComponent(phone)}`, {
                        headers: { 'Accept': 'application/json' },
                    });

                    if (!response.ok) {
                        modalBody.innerHTML = '<tr><td colspan="10" class="px-3 py-4 text-center text-red-600">Erro ao carregar registros.</td></tr>';
                        return;
                    }

                    const payload = await response.json();
                    const records = Array.isArray(payload.records) ? payload.records : [];

                    if (!records.length) {
                        modalBody.innerHTML = '<tr><td colspan="10" class="px-3 py-4 text-center text-gray-500">Nenhum registro relacionado.</td></tr>';
                        return;
                    }

                    modalBody.innerHTML = '';

                    records.forEach((record) => {
                        const tr = document.createElement('tr');
                        const cols = [
                            escapeText(record.created_at),
                            escapeText(record.buyer_name),
                            escapeText(record.product_name),
                            escapeText(record.event),
                            escapeText(record.purchase_full_price_value),
                            escapeText(record.purchase_status),
                            escapeText(record.purchase_payment_type),
                            escapeText(record.affiliate_name),
                            escapeText(record.affiliate_code),
                            escapeText(record.transaction),
                        ];

                        cols.forEach((col) => {
                            const td = document.createElement('td');
                            td.className = 'px-3 py-2 whitespace-nowrap';
                            td.textContent = col;
                            tr.appendChild(td);
                        });

                        modalBody.appendChild(tr);
                    });
                } catch (error) {
                    modalBody.innerHTML = '<tr><td colspan="10" class="px-3 py-4 text-center text-red-600">Erro ao carregar registros.</td></tr>';
                }
            }

            document.querySelectorAll('.js-open-related').forEach((button) => {
                button.addEventListener('click', () => {
                    openModal(button.dataset.phone || '', button.dataset.name || '');
                });
            });

            modal.querySelectorAll('.js-modal-close').forEach((button) => {
                button.addEventListener('click', closeModal);
            });
        })();
    </script>
</x-app-layout>
