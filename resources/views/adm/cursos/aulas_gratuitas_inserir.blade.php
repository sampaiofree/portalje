<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($aula) ? __('Editar Aula Demonstrativa') : __('Cadastrar Aula Demonstrativa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold">
                            {{ isset($aula) ? 'Atualizar dados da aula' : 'Nova aula demonstrativa' }}
                        </h3>
                        <a href="{{ route('aulas_gratuitas_index') }}" class="text-indigo-600 hover:text-indigo-800">
                            Voltar para lista
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form
                        action="{{ isset($aula) ? route('aulas_gratuitas_editar_post', $aula->id) : route('aulas_gratuitas_cadastrar_post') }}"
                        method="POST"
                        class="space-y-4"
                    >
                        @csrf
                        @if(isset($aula))
                            @method('PUT')
                        @endif

                        <div>
                            <x-input-label for="id_curso" value="Curso" />
                            <select name="id_curso" id="id_curso" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Selecione um curso</option>
                                @foreach($cursos as $curso)
                                    <option value="{{ $curso->id }}" {{ (string) old('id_curso', $aula->id_curso ?? '') === (string) $curso->id ? 'selected' : '' }}>
                                        {{ $curso->titulo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_curso')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-input-label for="aula_titulo" value="Título da Aula" />
                            <x-text-input id="aula_titulo" name="aula_titulo" type="text" class="mt-1 block w-full" :value="old('aula_titulo', $aula->aula_titulo ?? '')" />
                            @error('aula_titulo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-input-label for="aula_id_youtube" value="ID do Vídeo no YouTube" />
                            <x-text-input id="aula_id_youtube" name="aula_id_youtube" type="text" class="mt-1 block w-full" :value="old('aula_id_youtube', $aula->aula_id_youtube ?? '')" />
                            @error('aula_id_youtube')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <x-primary-button>{{ isset($aula) ? 'Salvar alterações' : 'Criar Aula' }}</x-primary-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
