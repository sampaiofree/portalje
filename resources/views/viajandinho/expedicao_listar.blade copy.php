@extends('viajandinho.html_base')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <a class="btn btn-primary" href="{{route('nova_expedicao')}}"><i class="ri-file-edit-fill "></i> Nova Expedição</a>
            </div>
            <div class="card-body">
                <table class="table table-centered mb-0">
                    <thead>
                        <tr>
                            <th>Exedição</th>
                            <th>Preço</th>
                            <th>Ativo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expedicao as $exped)
                        <tr>
                            <td>{{$exped->destino}}</td>
                            <td>{{$exped->preco}}</td>
                            <td>
                                <!-- Switch-->
                                <div>
                                    <input type="checkbox" id="{{$exped->id}}" @if($exped->ativo) checked @endif data-switch="success"/>
                                    <label for="{{$exped->id}}" data-on-label="Sim" data-off-label="Não" class="mb-0 d-block" ></label>
                                </div>
                            </td>
                            <td class="table-action">
                                <a href="{{route('editar_exedicao', ['id'=>$exped->id])}}" class="action-icon"> <i class="mdi mdi-pencil"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
               
            </div>
        </div>
    </div>
</div>

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