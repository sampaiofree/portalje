<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Adicionar Cursos ao Combo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">{{ $combo->titulo }}</h3>

                    <form action="{{ route('combo.cursos.salvar', $combo->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="border rounded-lg divide-y">
                            @foreach($cursos as $curso)
                                <label class="flex items-center gap-3 p-3">
                                    <input
                                        type="checkbox"
                                        name="cursos[]"
                                        value="{{ $curso->id }}"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                        {{ in_array($curso->id, $selected) ? 'checked' : '' }}
                                    >
                                    <span class="text-sm text-gray-500">#{{ $curso->id }}</span>
                                    <span class="font-medium">{{ $curso->titulo }}</span>
                                </label>
                            @endforeach
                        </div>

                        <x-primary-button>Salvar Cursos</x-primary-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
