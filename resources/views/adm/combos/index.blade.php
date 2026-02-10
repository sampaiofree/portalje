<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lista de Combos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    @foreach($combos as $combo)
                        <details class="border rounded-lg p-4">
                            <summary class="cursor-pointer font-semibold">
                                {{ $combo->titulo }} - {{ $combo->headline }}
                            </summary>
                            <div class="mt-3 text-sm space-y-2">
                                <p><strong>ID:</strong> {{ $combo->id }}</p>
                                <p><strong>URL:</strong> {{ $combo->url }}</p>
                                <div>
                                    <p class="font-semibold">Cursos inclusos:</p>
                                    @if($combo->cursos->isNotEmpty())
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach($combo->cursos as $curso)
                                                <li>
                                                    {{ $curso->titulo }}
                                                    <form action="{{ route('combo.curso.excluir', [$combo->id, $curso->id]) }}" method="POST" class="inline-block ml-2">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Excluir</button>
                                                    </form>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p>Nenhum curso adicionado.</p>
                                    @endif
                                </div>
                                <div class="flex gap-3 pt-2">
                                    <a href="{{ route('combo.editar_form', $combo->id) }}" class="text-indigo-600 hover:text-indigo-800">Editar</a>
                                    <a href="{{ route('combo.cursos.form', $combo->id) }}" class="text-indigo-600 hover:text-indigo-800">Adicionar Cursos</a>
                                </div>
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
