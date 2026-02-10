<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecionando...</title>
</head>
<body>
    <p>Você está sendo redirecionado...</p>

    <!-- Iframe oculto para carregar o URL e gerar o cookie -->
    <iframe src="{{ $iframe }}" style="display:none;"></iframe>

    <script>
        // Redirecionar o usuário após um pequeno atraso para garantir que o iframe seja carregado
        setTimeout(function() {
            window.location.href = "{{ $url }}";
        }, 2000); // Ajuste o tempo conforme necessário (em milissegundos)
    </script>
</body>
</html>
