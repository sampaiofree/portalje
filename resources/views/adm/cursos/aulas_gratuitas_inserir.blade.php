<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cadastrar Aula Demonstrativa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('aulas_gratuitas_cadastrar_post') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <x-input-label for="id_curso" value="Curso" />
                            <select name="id_curso" id="id_curso" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Selecione um curso</option>
                                @foreach($cursos as $curso)
                                    <option value="{{ $curso->id }}" {{ old('id_curso') == $curso->id ? 'selected' : '' }}>
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
                            <x-text-input id="aula_titulo" name="aula_titulo" type="text" class="mt-1 block w-full" :value="old('aula_titulo')" />
                            @error('aula_titulo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-input-label for="aula_id_youtube" value="ID do Vídeo no YouTube" />
                            <x-text-input id="aula_id_youtube" name="aula_id_youtube" type="text" class="mt-1 block w-full" :value="old('aula_id_youtube')" />
                            @error('aula_id_youtube')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <x-primary-button>Criar Aula</x-primary-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
