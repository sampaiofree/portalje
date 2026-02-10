@extends('viajandinho.html_base')

@section('content')

<form id="form_editar_expedicao" method="POST"  action="{{route('editar_exedicao_alterar') }}" >
    @csrf
    @method('PUT')

    <input type="hidden" id="id" name="id" class="form-control" value="{{$expedicao->id}}">
    <div class="mb-3">
        <label for="destino" class="form-label">Nome da Expedição</label>
        <input type="text" id="destino" name="destino" class="form-control" value="{{$expedicao->destino}}">
    </div>
    <div class="mb-3">
        <label for="editor-informacoes" class="form-label">Descrição</label>
        <div id="editor"></div>
        <input type="hidden" name="informacoes" id="informacoes">
    </div>
    <div class="mb-3">
        <label for="preco" class="form-label">Preço total da Expedição</label>
        <input type="text" id="preco" name="preco" class="form-control" value="{{$expedicao->preco}}">
    </div>
    <div class="mb-3">
        <button class="btn btn-primary" type="submit">Editar</button>
    </div>
</form>

@endsection

@section('head')
    <link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css">
@endsection

@section('scripts')

<!-- Toast UI Editor JS -->
<script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js"></script>


<script>
   document.addEventListener('DOMContentLoaded', function () {
            // Escapa o conteúdo do Markdown para garantir que seja seguro para uso em JavaScript
            const initialMarkdown = @json($expedicao->informacoes ?? '');

            const editor = new toastui.Editor({
                el: document.querySelector('#editor'),
                height: '400px',
                initialEditType: 'markdown', // Inicia no modo Markdown
                previewStyle: 'vertical', // Estilo de visualização em preview
                initialValue: initialMarkdown // Carrega o conteúdo Markdown inicial
            });

            // Função para capturar o conteúdo e salvar no campo oculto antes de enviar o formulário
            document.querySelector('form').addEventListener('submit', function () {
                const markdownContent = editor.getMarkdown(); // Obtém o conteúdo em Markdown
                document.getElementById('informacoes').value = markdownContent;
            });
    });
</script>



@endsection