<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Logs do Sistema') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Arquivos de log</h3>
                        <p class="text-sm text-gray-500">Visualize os últimos 500KB de cada log ou baixe o arquivo completo.</p>
                    </div>

                    @if(empty($logFiles))
                        <div class="rounded-md border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                            Nenhum arquivo de log encontrado em `storage/logs`.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Arquivo</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Tamanho</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Última modificação</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @foreach($logFiles as $log)
                                        <tr>
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $log['name'] }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700">{{ $log['size_human'] }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700">{{ $log['modified_at'] }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                <div class="flex items-center gap-3">
                                                    <a
                                                        href="{{ route('admin.logs.view', ['logFile' => $log['name']]) }}"
                                                        class="inline-flex items-center rounded-md bg-blue-50 px-3 py-1.5 text-blue-700 hover:bg-blue-100"
                                                    >
                                                        Ver
                                                    </a>
                                                    <a
                                                        href="{{ route('admin.logs.download', ['logFile' => $log['name']]) }}"
                                                        class="inline-flex items-center rounded-md bg-green-50 px-3 py-1.5 text-green-700 hover:bg-green-100"
                                                    >
                                                        Baixar
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
