<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Listar e Ordenar Cursos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium">Listando cursos por ordem</h3>
                    <p class="text-sm text-gray-500 mb-4">Arraste os cursos pelo ícone <i class="ri-drag-move-2-fill"></i> para reordená-los.</p>

                    {{-- O contêiner para o Dragula foi mantido com o ID original --}}
                    <div id="handle-dragula-left" class="space-y-3">
                       @foreach($cursos as $curso)

                       {{-- O '.card' foi mantido para o seletor do JavaScript --}}
                       <div class="card border rounded-lg shadow-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-20 h-20">
                                    <img src="{{ asset('storage/'.$curso->capa_quadrada) }}" class="w-20 h-20 rounded-l-lg object-cover" alt="{{ $curso->titulo }}">
                                </div>
                                <div class="flex-grow p-4">
                                    <h5 class="font-bold text-gray-800">{{ $curso->titulo }}</h5>
                                    
                                    {{-- A estrutura de grid foi refeita com Tailwind --}}
                                    <div class="mt-2 grid grid-cols-2 md:grid-cols-5 gap-4 items-center">
                                        
                                        {{-- Os toggles mantêm a estrutura original para compatibilidade com o script --}}
                                        <div class="flex flex-col items-center text-center">
                                            <p class="text-xs font-medium text-gray-600 mb-1">Publicar?</p>
                                            <input type="checkbox" id="publicado_{{ $curso->id }}" name="publicado_{{ $curso->id }}" @if($curso->publicado) checked @endif data-switch="bool" data-id="{{ $curso->id }}" />
                                            <label for="publicado_{{ $curso->id }}" data-on-label="Sim" data-off-label="Não"></label>
                                        </div>
                                        <div class="flex flex-col items-center text-center">
                                            <p class="text-xs font-medium text-gray-600 mb-1">Afiliação?</p>
                                            <input type="checkbox" id="permitir_afiliacao_{{ $curso->id }}" name="permitir_afiliacao_{{ $curso->id }}" @if($curso->permitir_afiliacao) checked @endif data-switch="bool" data-id="{{ $curso->id }}" />
                                            <label for="permitir_afiliacao_{{ $curso->id }}" data-on-label="Sim" data-off-label="Não"></label>
                                        </div>
                                        <div class="flex flex-col items-center text-center">
                                            <p class="text-xs font-medium text-gray-600 mb-1">Na Página?</p>
                                            <input type="checkbox" id="mostrar_na_pagina_{{ $curso->id }}" name="mostrar_na_pagina_{{ $curso->id }}" @if($curso->mostrar_na_pagina) checked @endif data-switch="bool" data-id="{{ $curso->id }}" />
                                            <label for="mostrar_na_pagina_{{ $curso->id }}" data-on-label="Sim" data-off-label="Não"></label>
                                        </div>
                                        <div class="flex flex-col items-center text-center">
                                            <p class="text-xs font-medium text-gray-600 mb-1">Gratuito?</p>
                                            <input type="checkbox" id="gratuito{{ $curso->id }}" name="gratuito{{ $curso->id }}" @if($curso->gratuito) checked @endif data-switch="bool" data-id="{{ $curso->id }}" />
                                            <label for="gratuito{{ $curso->id }}" data-on-label="Sim" data-off-label="Não"></label>
                                        </div>
                                        
                                        {{-- O input hidden foi mantido para a lógica de ordenação --}}
                                        <input type="hidden" name="id" value="{{ $curso->id }}" />
                                        
                                        <div class="flex items-center justify-self-center">
                                            <a href="{{ route('adm_editar_curso',['id'=>$curso->id]) }}" class="text-indigo-600 hover:text-indigo-900" title="Editar Curso">
                                                <i class="ri-edit-2-line text-2xl"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div> 
                                
                                {{-- A alça de arrasto foi mantida com a classe original --}}
                                <div class="p-4 text-gray-400 cursor-move dragula-handle">
                                    <span class="float-end"><i class="ri-drag-move-2-fill text-2xl"></i></span>
                                </div>
                            </div> 
                        </div>
                       @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const container = document.getElementById('handle-dragula-left');

                if (!container || !csrfToken) {
                    return;
                }

                const updateOrder = async () => {
                    const order = [];
                    container.querySelectorAll('.card').forEach((card, index) => {
                        const cursoId = Number(card.querySelector('input[type="hidden"]')?.value || 0);
                        if (cursoId > 0) {
                            order.push({ id: cursoId, ordem: index + 1 });
                        }
                    });

                    try {
                        await fetch('{{ route("adm_cursos_update_order") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ order }),
                        });
                    } catch (error) {
                        console.error('Erro ao atualizar a ordem:', error);
                    }
                };

                if (window.Sortable) {
                    new window.Sortable(container, {
                        animation: 150,
                        handle: '.dragula-handle',
                        draggable: '.card',
                        ghostClass: 'opacity-60',
                        onEnd: updateOrder,
                    });
                } else {
                    console.error('Sortable não está disponível na página.');
                }

                const updateCourseStatus = async (cursoId) => {
                    const publicado = document.getElementById(`publicado_${cursoId}`)?.checked ?? false;
                    const permitirAfiliacao = document.getElementById(`permitir_afiliacao_${cursoId}`)?.checked ?? false;
                    const mostrarNaPagina = document.getElementById(`mostrar_na_pagina_${cursoId}`)?.checked ?? false;
                    const gratuito = document.getElementById(`gratuito${cursoId}`)?.checked ?? false;

                    const formData = new FormData();
                    formData.append('_token', csrfToken);
                    formData.append('id', String(cursoId));
                    formData.append('publicado', String(publicado));
                    formData.append('permitir_afiliacao', String(permitirAfiliacao));
                    formData.append('mostrar_na_pagina', String(mostrarNaPagina));
                    formData.append('gratuito', String(gratuito));

                    try {
                        await fetch('{{ route('adm_cursos_lista_editar') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: formData,
                        });
                    } catch (error) {
                        console.error('Erro ao atualizar o status:', error);
                    }
                };

                container.querySelectorAll('input[type="checkbox"][data-switch="bool"]').forEach((checkbox) => {
                    checkbox.addEventListener('change', function () {
                        const cursoId = Number(this.getAttribute('data-id') || 0);
                        if (cursoId > 0) {
                            updateCourseStatus(cursoId);
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
