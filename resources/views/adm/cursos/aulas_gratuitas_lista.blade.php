<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Aulas Demonstrativas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Lista de Aulas Demonstrativas</h3>
                        <a href="{{ route('aulas_gratuitas_cadastrar') }}" class="text-indigo-600 hover:text-indigo-800">
                            Adicionar aula
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Título</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Curso</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Vídeo</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($aulas as $aula)
                                    <tr>
                                        <td class="px-4 py-2">{{ $aula->id }}</td>
                                        <td class="px-4 py-2">{{ $aula->aula_titulo }}</td>
                                        <td class="px-4 py-2">{{ $aula->curso->titulo }}</td>
                                        <td class="px-4 py-2">
                                            <a href="https://www.youtube.com/watch?v={{ $aula->aula_id_youtube }}" target="_blank" class="text-indigo-600 hover:text-indigo-800">
                                                Assistir
                                            </a>
                                        </td>
                                        <td class="px-4 py-2">
                                            <form action="{{ route('aulas_gratuitas_destroy', $aula->id) }}" method="POST" onsubmit="return confirm('Excluir esta aula?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
