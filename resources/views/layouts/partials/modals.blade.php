{{-- Este arquivo contém todos os modais da aplicação, reescritos com Tailwind e Alpine.js --}}

<!-- 
    MODAL 1: VÍDEO DE AJUDA
    Controlado por um evento global 'open-video-modal'.
-->
<div
    x-data="{ show: false, videoUrl: '', modalTitle: '', modalText: '' }"
    x-on:open-video-modal.window="
        show = true;
        modalTitle = $event.detail.title;
        modalText = $event.detail.text;
        videoUrl = 'https://www.youtube.com/embed/' + $event.detail.link;
    "
    x-show="show"
    x-cloak
    x-on:keydown.escape.window="show = false; videoUrl = ''"
    style="display: none;"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <!-- Overlay -->
    <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/75"></div>

    <!-- Conteúdo do Modal -->
    <div x-show="show" x-transition class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl">
        <!-- Cabeçalho -->
        <div class="flex items-center justify-between p-4 border-b dark:border-gray-600">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white" x-text="modalTitle"></h3>
            <button @click="show = false; videoUrl = ''" class="text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg p-1.5">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>
        </div>
        <!-- Corpo -->
        <!-- Corpo (Corrigido) -->
        <div class="p-6 space-y-6">
            <!-- O iframe agora tem uma altura responsiva e foi removido do div de aspect-ratio -->
            <iframe class="w-full rounded-lg h-[65vh]" :src="videoUrl" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            
            <div class="text-base leading-relaxed text-gray-500" x-html="modalText"></div>
        </div>
    </div>
</div>



<!-- 
    MODAL 4: MINHA JORNADA (Right-side Drawer)
-->
<div x-data="{ show: false }" x-on:open-jornada-modal.window="show = true" x-cloak style="display: none;">
    <div class="fixed inset-0 z-40" @keydown.escape.window="show = false">
        <!-- Overlay -->
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/50" @click="show = false"></div>
        <!-- Drawer -->
        <div x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
             class="fixed top-0 right-0 h-full w-full max-w-sm bg-white dark:bg-gray-800 shadow-xl z-50 overflow-y-auto p-4">
            <button @click="show = false" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <h4 class="text-lg font-semibold mb-4">Minha Jornada</h4>
            <div class="space-y-4">
                {{-- Aqui vai a lógica do seu foreach para a jornada --}}
                @isset($minha_jornada)
                    @foreach($minha_jornada as $tarefa)
                        <div class="border-b pb-2">
                            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full {{ $tarefa['concluido'] ? 'text-green-600 bg-green-200' : 'text-red-600 bg-red-200' }}">
                                {{ $tarefa['concluido'] ? 'Concluído' : 'Pendente' }}
                            </span>
                            <a href="#" @click.prevent="window.video_de_ajuda('{{ $tarefa['link'] }}', '{{ $tarefa['titulo'] }}', '{{ addslashes($tarefa['texto']) }}')"
                               class="flex items-center gap-2 mt-1 text-gray-700 dark:text-gray-300 hover:text-blue-500">
                                <i class="ri-{{ $tarefa['concluido'] ? 'checkbox-circle-fill' : 'youtube-fill' }} {{ $tarefa['concluido'] ? 'text-green-500' : 'text-red-500' }}"></i>
                                <span>{{ $tarefa['titulo'] }}</span>
                            </a>
                        </div>
                    @endforeach
                @endisset
            </div>
        </div>
    </div>
</div>