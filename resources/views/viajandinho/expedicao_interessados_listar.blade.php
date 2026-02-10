@extends('viajandinho.html_base')

@section('content')
<table class="table table-centered mb-0">
    <thead>
        <tr>
            <th>Nome</th>
            <th>email</th>
            <th>telefone</th>
            <th>destino</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($interessados as $interessado)
        <tr>
            <td>{{$interessado->nome}}</td>
            <td>{{$interessado->email}}</td>
            <td>{{$interessado->telefone}}</td>
            <td>{{$interessado->expedicao->destino}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

@section('head')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endsection

@section('scripts')

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Seleciona todos os checkboxes na página
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');

    // Adiciona um evento de 'change' a cada checkbox
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const isChecked = checkbox.checked ? 1 : 0; // 1 para marcado, 0 para desmarcado
            const checkboxId = checkbox.id; // Captura o ID do checkbox

            // Enviar o estado do checkbox ao servidor via AJAX
            fetch("{{ route('editar_exedicao_ativo') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    id: checkboxId,
                    ativo: isChecked
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Estado do checkbox atualizado:', data);
                showAlert(data.message, 'success');
                // Adicione aqui qualquer lógica adicional que você precise após o sucesso
            })
            .catch(error => {
                console.error('Erro ao atualizar o estado do checkbox:', error);
                // Adicione aqui qualquer lógica adicional para lidar com erros
            });
        });
    });
});

</script>


@endsection