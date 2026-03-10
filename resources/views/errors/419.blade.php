<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sessao Expirada</title>
    <meta http-equiv="refresh" content="1;url={{ route('login') }}">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f8fafc;
            color: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 16px;
        }
        .box {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            max-width: 460px;
            width: 100%;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        }
        h1 {
            margin: 0 0 8px;
            font-size: 20px;
            line-height: 1.3;
        }
        p {
            margin: 0;
            color: #4b5563;
            line-height: 1.5;
        }
        a {
            color: #2563eb;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>Sessao expirada</h1>
        <p>Estamos te redirecionando para o login para renovar a sessao. Se nao acontecer automaticamente, <a href="{{ route('login') }}">clique aqui</a>.</p>
    </div>

    <script>
        setTimeout(function () {
            window.location.href = @json(route('login'));
        }, 900);
    </script>
</body>
</html>
