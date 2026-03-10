<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Visualizar Log') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $logFile }}</h3>
                            <p class="text-sm text-gray-500">
                                Exibindo os últimos {{ number_format($maxBytes / 1024, 0) }}KB do arquivo
                                (tamanho total: {{ number_format($fileSize / 1024, 2, ',', '.') }}KB).
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.logs.index') }}" class="inline-flex items-center rounded-md bg-gray-100 px-3 py-2 text-sm text-gray-700 hover:bg-gray-200">
                                Voltar
                            </a>
                            <a href="{{ route('admin.logs.download', ['logFile' => $logFile]) }}" class="inline-flex items-center rounded-md bg-green-50 px-3 py-2 text-sm text-green-700 hover:bg-green-100">
                                Baixar arquivo completo
                            </a>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-gray-900 p-4">
                        <pre class="max-h-[70vh] overflow-auto whitespace-pre-wrap break-words font-mono text-xs text-gray-100">{{ $previewContent }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
