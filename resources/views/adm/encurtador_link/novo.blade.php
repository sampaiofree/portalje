@extends('adm.html_base')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <h4 class="mb-4">Encurtador de Links</h4>
        
        <!-- Formulário para encurtar a URL -->
        <form id="form_link_encurtado" action="{{route('encurtar_link')}}" method="POST">
            
            @csrf
            <!-- Campo de URL longa -->
            <div class="mb-3">
                <label for="url_longa" class="form-label">URL de destino</label>
                <input type="url" class="form-control" id="url_longa" name="url_longa" placeholder="https://www.exemplo.com" required>
            </div>

            <!-- Botão de submissão -->
            <div class="">
                <button type="submit" class="btn btn-primary">Encurtar URL</button>
            </div>
        </form>
        
        <!-- Seção para exibir o link encurtado (exemplo) -->
        <div id="alert_link" class="mt-4">
            @if(session('link_encurtado'))
                <div class="alert alert-success">
                    <p>Seu link encurtado é: <a href="https://{{ session('link_encurtado') }}" target="_blank">{{ session('link_encurtado') }}</a></p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>
    $(document).ready(function() {
    // Intercepta o evento de submissão do formulário
        $('#form_link_encurtado').on('submit', function(event) {
            event.preventDefault(); // Impede a submissão tradicional do formulário

            // Coleta os dados do formulário
            var formData = $(this).serialize();

            // Envia o formulário via AJAX
            $.ajax({
                url: $(this).attr('action'), // URL do controlador Laravel
                method: $(this).attr('method'), // Método POST
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    // Se a resposta for bem-sucedida, exibe o link encurtado
                    var alertHtml = '<div class="alert alert-success">' +
                                    '<p>Seu link encurtado é: <a href="https://' + response.link_encurtado + '" target="_blank">' + response.link_encurtado + '</a></p>' +
                                    '</div>';
                    $('#alert_link').html(alertHtml); // Insere o alerta com o link encurtado
                },
                error: function(xhr, status, error) {
                    console.log("Status: " + status);
                    console.log("Erro: " + error);
                    console.log("Resposta completa: ", xhr.responseText);

                    // Tenta parsear a resposta como JSON para extrair a mensagem de erro
                    var errorMessage;
                    try {
                        var responseJson = JSON.parse(xhr.responseText);
                        errorMessage = responseJson.message || "Ocorreu um erro desconhecido.";
                    } catch (e) {
                        errorMessage = "Não foi possível processar a resposta do servidor.";
                    }

                    // Exibe uma mensagem de erro, se houver falha
                    var alertHtml = '<div class="alert alert-danger">Ocorreu um erro ao encurtar a URL. ' + errorMessage + ' Por favor, tente novamente.</div>';
                    $('#alert_link').html(alertHtml);
                }

            });
        });
    });



</script>

@endsection