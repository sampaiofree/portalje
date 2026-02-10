<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editando Curso: {{ $curso->titulo }}
        </h2>
    </x-slot>

    {{-- Incluindo o CSS do Quill.js apenas nesta página --}}
    @push('head')
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form id="form_editar_curso" method="POST" action="{{ route('adm_editar_curso_post', ['id' => $curso->id]) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Coluna Principal do Formulário (2/3 da largura) -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Card de Imagens -->
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                            <h2 class="text-lg font-medium text-gray-900">Imagens do Curso</h2>
                            <p class="mt-1text-sm text-gray-600">Faça o upload das capas e foto do professor. Formato recomendado: .webp</p>
                            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="capa_quadrada" value="Capa Quadrada (600x600)" />
                                    <x-text-input id="capa_quadrada" name="capa_quadrada" type="file" class="mt-1 p-2 bg-gray-100 block w-full file-input" />
                                </div>
                                <div>
                                    <x-input-label for="capa_vertical" value="Capa Vertical (720x1040)" />
                                    <x-text-input id="capa_vertical" name="capa_vertical" type="file" class="mt-1 p-2 bg-gray-100 block w-full file-input" />
                                </div>
                                <div>
                                    <x-input-label for="capa_horizontal" value="Capa Horizontal (1040x720)" />
                                    <x-text-input id="capa_horizontal" name="capa_horizontal" type="file" class="mt-1 p-2 bg-gray-100 block w-full file-input" />
                                </div>
                                <div>
                                    <x-input-label for="professor_foto" value="Foto do Professor (600x600)" />
                                    <x-text-input id="professor_foto" name="professor_foto" type="file" class="mt-1 p-2 bg-gray-100 block w-full file-input" />
                                </div>
                            </div>
                        </div>
                        
                        <!-- Card de Configurações Gerais -->
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                            <h2 class="text-lg font-medium text-gray-900">Configurações Gerais</h2>
                            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="titulo" value="Nome do curso" />
                                    <x-text-input id="titulo" name="titulo" type="text" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('titulo', $curso->titulo)" />
                                </div>
                                <div>
                                    <x-input-label for="codigo_id_hotmart" value="Código ID Hotmart" />
                                    <x-text-input id="codigo_id_hotmart" name="codigo_id_hotmart" type="text" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('codigo_id_hotmart', $curso->codigo_id_hotmart)" />
                                </div>
                            </div>
                            <div class="mt-6">
                                <x-input-label for="headline" value="Headline" />
                                <x-text-input id="headline" name="headline" type="text" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('headline', $curso->headline)" />
                            </div>

                            <!-- Toggles (Substituindo data-switch) -->
                            <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
                                @php
                                    $toggles = [
                                        ['name' => 'publicado', 'label' => 'Publicar Curso?', 'checked' => $curso->publicado],
                                        ['name' => 'permitir_afiliacao', 'label' => 'Permitir Afiliação?', 'checked' => $curso->permitir_afiliacao],
                                        ['name' => 'mostrar_na_pagina', 'label' => 'Mostrar na Página?', 'checked' => $curso->mostrar_na_pagina],
                                        ['name' => 'gratuito', 'label' => 'Curso Gratuito?', 'checked' => $curso->gratuito],
                                    ];
                                @endphp
                                @foreach($toggles as $toggle)
                                <div x-data="{ on: {{ $toggle['checked'] ? 'true' : 'false' }} }">
                                    <label for="{{$toggle['name']}}" class="flex flex-col items-center cursor-pointer">
                                        <span class="text-sm font-medium text-gray-700 mb-2">{{ $toggle['label'] }}</span>
                                        <div class="relative inline-flex items-center">
                                            <input type="checkbox" id="{{$toggle['name']}}" name="{{$toggle['name']}}" class="sr-only peer" value="1" x-model="on">
                                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </div>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Card de Conteúdo do Curso (com Quill.js) -->
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg space-y-6">
                            <h2 class="text-lg font-medium text-gray-900">Conteúdo do Curso</h2>
                            <div>
                                <x-input-label for="editor_descricao_curta" value="Descrição Curta" class="mb-2" />
                                <div id="editor_descricao_curta" class="snow-editor h-48">{!! $curso->descricao_curta !!}</div>
                                <input type="hidden" name="descricao_curta" id="descricao_curta">
                            </div>
                            <div>
                                <x-input-label for="editor_conteudo_principal" value="Conteúdo Principal" class="mb-2" />
                                <div id="editor_conteudo_principal" class="snow-editor h-64">{!! $curso->conteudo_principal !!}</div>
                                <input type="hidden" name="conteudo_principal" id="conteudo_principal">
                            </div>
                             <div>
                                <x-input-label for="editor_conteudo_bonus" value="Conteúdo Bônus" class="mb-2" />
                                <div id="editor_conteudo_bonus" class="snow-editor h-64">{!! $curso->conteudo_bonus !!}</div>
                                <input type="hidden" name="conteudo_bonus" id="conteudo_bonus">
                            </div>
                        </div>
                        
                        <!-- Card: Dados e Métricas -->
<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
    <h2 class="text-lg font-medium text-gray-900">Dados e Métricas</h2>
    <p class="mt-1text-sm text-gray-600">Informações usadas na página de vendas para gerar prova social e detalhes.</p>
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
            <x-input-label for="nota_avaliacao" value="Nota de Avaliação (Ex: 4.8)" />
            <x-text-input id="nota_avaliacao" name="nota_avaliacao" type="text" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('nota_avaliacao', $curso->nota_avaliacao)" />
        </div>
        <div>
            <x-input-label for="numero_alunos" value="Número de Alunos" />
            <x-text-input id="numero_alunos" name="numero_alunos" type="text" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('numero_alunos', $curso->numero_alunos)" />
        </div>
        <div>
            <x-input-label for="areas_de_atuacao" value="Áreas de Atuação (separado por /)" />
            <x-text-input id="areas_de_atuacao" name="areas_de_atuacao" type="text" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('areas_de_atuacao', $curso->areas_de_atuacao)" />
        </div>
        <div>
            <x-input-label for="salario_maximo" value="Salário Máximo (apenas números)" />
            <x-text-input id="salario_maximo" name="salario_maximo" type="number" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('salario_maximo', $curso->salario_maximo)" />
        </div>
    </div>
</div>
<!-- Card: Vídeos e Links de Checkout -->
<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
    <h2 class="text-lg font-medium text-gray-900">Vídeos e Links de Checkout</h2>
    <p class="mt-1 text-sm text-gray-600">Links de vídeo do YouTube e URLs de pagamento da Hotmart.</p>
    <div class="mt-6 space-y-6">
        <div>
            <x-input-label for="video_dentro_do_curso" value="Vídeo por Dentro do Curso (ID do YouTube)" />
            <x-text-input id="video_dentro_do_curso" name="video_dentro_do_curso" type="text" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('video_dentro_do_curso', $curso->video_dentro_do_curso)" placeholder="Ex: kJrqK9ZlrA0" />
        </div>
        <div>
            <x-input-label for="video_apresentacao" value="Vídeo de Apresentação (ID do YouTube)" />
            <x-text-input id="video_apresentacao" name="video_apresentacao" type="text" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('video_apresentacao', $curso->video_apresentacao)" placeholder="Ex: G3_UjvwizSc" />
        </div>
        <div>
            <x-input-label for="link_checkout_basico" value="Link do Checkout Básico" />
            <x-text-input id="link_checkout_basico" name="link_checkout_basico" type="text" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('link_checkout_basico', $curso->link_checkout_basico)" />
        </div>
        <div>
            <x-input-label for="link_checkout_completo" value="Link do Checkout Completo" />
            <x-text-input id="link_checkout_completo" name="link_checkout_completo" type="text" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('link_checkout_completo', $curso->link_checkout_completo)" />
        </div>
    </div>
</div>
<!-- Card: Informações do Professor -->
<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
    <h2 class="text-lg font-medium text-gray-900">Informações do Professor</h2>
    <div class="mt-6 space-y-6">
        <div>
            <x-input-label for="professor_nome" value="Nome do Professor" />
            <x-text-input id="professor_nome" name="professor_nome" type="text" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('professor_nome', $curso->professor_nome)" />
        </div>
        <div>
            <x-input-label for="editor_professor_biografia" value="Biografia do Professor" class="mb-2" />
            <div id="editor_professor_biografia" class="snow-editor h-48">{!! $curso->professor_biografia !!}</div>
            <input type="hidden" name="professor_biografia" id="professor_biografia">
        </div>
    </div>
</div>
<!-- Card: Afiliação e Preços -->
<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
    <h2 class="text-lg font-medium text-gray-900">Afiliação e Preços</h2>
    <p class="mt-1text-sm text-gray-600">Detalhes para afiliados e precificação dos planos.</p>
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
            <x-input-label for="codigo_afiliado_plano_basico" value="Código Afiliado (Plano Básico)" />
            <x-text-input id="codigo_afiliado_plano_basico" name="codigo_afiliado_plano_basico" type="text" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('codigo_afiliado_plano_basico', $curso->codigo_afiliado_plano_basico)" />
        </div>
        <div>
            <x-input-label for="codigo_afiliado_plano_completo" value="Código Afiliado (Plano Completo)" />
            <x-text-input id="codigo_afiliado_plano_completo" name="codigo_afiliado_plano_completo" type="text" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('codigo_afiliado_plano_completo', $curso->codigo_afiliado_plano_completo)" />
        </div>
        <div>
            <x-input-label for="preco_parcelado_basico" value="Preço Parcelado (Básico)" />
            <x-text-input id="preco_parcelado_basico" name="preco_parcelado_basico" type="text" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('preco_parcelado_basico', $curso->preco_parcelado_basico)" />
        </div>
        <div>
            <x-input-label for="preco_parcelado_completo" value="Preço Parcelado (Completo)" />
            <x-text-input id="preco_parcelado_completo" name="preco_parcelado_completo" type="text" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('preco_parcelado_completo', $curso->preco_parcelado_completo)" />
        </div>
        <div>
            <x-input-label for="preco_cheio_basico" value="Preço Cheio (Básico)" />
            <x-text-input id="preco_cheio_basico" name="preco_cheio_basico" type="text" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('preco_cheio_basico', $curso->preco_cheio_basico)" />
        </div>
        <div>
            <x-input-label for="preco_cheio_completo" value="Preço Cheio (Completo)" />
            <x-text-input id="preco_cheio_completo" name="preco_cheio_completo" type="text" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('preco_cheio_completo', $curso->preco_cheio_completo)" />
        </div>
        <div>
            <x-input-label for="horas_basico" value="Horas (Plano Básico)" />
            <x-text-input id="horas_basico" name="horas_basico" type="number" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('horas_basico', $curso->horas_basico)" />
        </div>
        <div>
            <x-input-label for="horas_completo" value="Horas (Plano Completo)" />
            <x-text-input id="horas_completo" name="horas_completo" type="number" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('horas_completo', $curso->horas_completo)" />
        </div>
        <div class="sm:col-span-2">
            <x-input-label for="link_materiais" value="Link dos Materiais para Afiliados" />
            <x-text-input id="link_materiais" name="link_materiais" type="text" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('link_materiais', $curso->link_materiais)" />
        </div>
        <div class="sm:col-span-2">
            <x-input-label for="link_afiliacao" value="Link de Afiliação" />
            <x-text-input id="link_afiliacao" name="link_afiliacao" type="text" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('link_afiliacao', $curso->link_afiliacao)" />
        </div>
         <div class="sm:col-span-2">
            <x-input-label for="link_area_membros" value="Link da Área de Membros" />
            <x-text-input id="link_area_membros" name="link_area_membros" type="text" class="mt-1 p-2 bg-gray-100 block w-full" :value="old('link_area_membros', $curso->link_area_membros)" />
        </div>
    </div>
</div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Salvar Alterações') }}</x-primary-button>
                        </div>
                    </div>

                    <!-- Coluna Lateral de Previews (1/3 da largura) -->
                    <div class="lg:col-span-1 space-y-6">
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                             <h2 class="text-lg font-medium text-gray-900 mb-4">Pré-visualização das Imagens</h2>
                             <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <h5 class="text-sm font-medium text-gray-500">Quadrada</h5>
                                    <img src="{{ asset('storage/' . $curso->capa_quadrada) }}" id="mostrar_capa_quadrada" class="w-full aspect-square object-cover rounded-md border">
                                </div>
                                <div class="space-y-1">
                                    <h5 class="text-sm font-medium text-gray-500">Vertical</h5>
                                    <img src="{{ asset('storage/' . $curso->capa_vertical) }}" id="mostrar_capa_vertical" class="w-full aspect-[3/4] object-cover rounded-md border">
                                </div>
                                <div class="space-y-1">
                                    <h5 class="text-sm font-medium text-gray-500">Horizontal</h5>
                                    <img src="{{ asset('storage/' . $curso->capa_horizontal) }}" id="mostrar_capa_horizontal" class="w-full aspect-video object-cover rounded-md border">
                                </div>
                                <div class="space-y-1">
                                    <h5 class="text-sm font-medium text-gray-500">Professor</h5>
                                    <img src="{{ asset('storage/' . $curso->professor_foto) }}" id="mostrar_professor_foto" class="w-full aspect-square object-cover rounded-md border">
                                </div>
                             </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    @push('scripts')
        <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Inicialização do Quill.js
                const editors = {};
                const editorConfigs = [
                    'descricao_curta', 
                    'conteudo_principal', 
                    'conteudo_bonus', 
                    'professor_biografia'
                ];

                editorConfigs.forEach(id => {
                    const editorDiv = document.getElementById(`editor_${id}`);
                    if(editorDiv) {
                        editors[id] = new Quill(editorDiv, { theme: 'snow' });
                    }
                });

                // Sincronização do Quill.js com os inputs hidden antes de submeter o formulário
                const form = document.getElementById('form_editar_curso');
                form.addEventListener('submit', function() {
                    for (const id in editors) {
                        const hiddenInput = document.getElementById(id);
                        if(hiddenInput) {
                            hiddenInput.value = editors[id].root.innerHTML;
                        }
                    }
                });

                // Lógica de Preview de Imagem (vanilla JS)
                function setupImagePreview(inputId, imgId) {
                    const input = document.getElementById(inputId);
                    const img = document.getElementById(imgId);
                    if (input && img) {
                        input.addEventListener('change', function(event) {
                            const file = event.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = (e) => img.src = e.target.result;
                                reader.readAsDataURL(file);
                            }
                        });
                    }
                }

                setupImagePreview('capa_quadrada', 'mostrar_capa_quadrada');
                setupImagePreview('capa_vertical', 'mostrar_capa_vertical');
                setupImagePreview('capa_horizontal', 'mostrar_capa_horizontal');
                setupImagePreview('professor_foto', 'mostrar_professor_foto');
            });
        </script>
    @endpush
</x-app-layout>