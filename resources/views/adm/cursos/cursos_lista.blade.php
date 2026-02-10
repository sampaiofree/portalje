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

    {{-- SCRIPTS E ESTILOS ANTIGOS AINDA SÃO NECESSÁRIOS PARA ESTA PÁGINA --}}
    @push('head')
        <meta name="csrf-token" content="{{ csrf_token() }}">
        {{-- Estilos para o componente de switch do tema antigo --}}
        <link href="{{asset('Hyper_v5.4/Admin/dist/saas/assets/css/vendor/switchery.min.css')}}" rel="stylesheet" type="text/css" />
        <style>
            /* Pequeno ajuste para o switch antigo se alinhar melhor */
            .switchery { margin-bottom: 0; }
        </style>
    @endpush

    @push('scripts')
        {{-- Carrega o jQuery, Dragula e os scripts do tema antigo necessários para a funcionalidade --}}
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/dragula/dragula.min.js')}}"></script>
        <script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/switchery.min.js')}}"></script>
        
        {{-- Seu script original, sem alterações na lógica --}}
        <script>
            // Inicializa os toggles 'data-switch'
            $('[data-switch=bool]').each(function () {
                new Switchery($(this)[0], $(this).data());
            });

            document.addEventListener('DOMContentLoaded', function() {
                var drake = dragula([document.getElementById('handle-dragula-left')], {
                    moves: function (el, container, handle) {
                        return handle.classList.contains('dragula-handle');
                    }
                });
                drake.on('drop', function(el, target, source, sibling) {
                    updateOrder();
                });
            });

            function updateOrder() {
                var order = [];
                document.querySelectorAll('#handle-dragula-left .card').forEach(function(card, index) {
                    var cursoId = card.querySelector('input[type="hidden"]').value;
                    if (cursoId) {
                        order.push({ id: cursoId, ordem: index + 1 });
                    }
                });
                var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                $.ajax({
                    url: '{{ route("adm_cursos_update_order") }}',
                    type: 'POST',
                    data: { _token: csrfToken, order: order },
                    success: function(response) { console.log('Ordem atualizada com sucesso:', response); },
                    error: function(xhr, status, error) { console.error('Erro ao atualizar a ordem:', error); }
                });
            }

            $(document).ready(function() {
                function updateCourseStatus(cursoId) {
                    var publicado = $('#publicado_'+cursoId).is(':checked');
                    var permitirAfiliacao = $('#permitir_afiliacao_'+cursoId).is(':checked');
                    var mostrarNaPagina = $('#mostrar_na_pagina_'+cursoId).is(':checked');
                    var gratuito = $('#gratuito'+cursoId).is(':checked');
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    
                    $.ajax({
                        url: '{{route('adm_cursos_lista_editar')}}',
                        type: 'POST',
                        data: {
                            _token: csrfToken,
                            publicado: publicado,
                            permitir_afiliacao: permitirAfiliacao,
                            mostrar_na_pagina: mostrarNaPagina,
                            gratuito: gratuito,
                            id: cursoId,
                        },
                        success: function(response) { console.log('Status atualizado com sucesso', response); },
                        error: function(xhr) { console.error('Erro ao atualizar o status'); }
                    });
                }

                $('input[type="checkbox"][data-switch="bool"]').on('change', function() {
                    var cursoId = $(this).data('id');
                    if (cursoId) {
                        updateCourseStatus(cursoId);
                    } else {
                        console.error('ID do curso não encontrado');
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>