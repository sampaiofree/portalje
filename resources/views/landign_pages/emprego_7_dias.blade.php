<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Como se Preparar para Conquistar um Emprego em Até 7 Dias</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .hero {
      background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1350&q=80') center center / cover no-repeat;
      color: #fff;
      padding: 80px 20px;
      text-shadow: 0 1px 3px rgba(0,0,0,0.6);
    }
    .hero h1 {
      font-size: 2.5rem;
    }
    .section-content {
      background-color: #fff;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
  </style>
</head>
<body>
  <section class="hero text-center">
    <div class="container">
      <h1 class="fw-bold">Como se Preparar para Conquistar um Emprego em Até 7 Dias</h1>
      <p class="mt-3">Treinamento gratuito com dicas práticas de currículo e entrevista para quem quer entrar ou voltar ao mercado de trabalho.</p>
      <button class="btn btn-warning btn-lg mt-3 text-uppercase fw-bold" data-bs-toggle="modal" data-bs-target="#leadModal">Quero Participar Gratuitamente</button>
      <p>Clique no botão acima para entrar no grupo onde o material será liberado. É um grupo fechado e silencioso — você vai receber tudo por lá com tranquilidade.</p>
    </div>
  </section>

  <section class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="section-content">
          <div class="text-center mb-4">
            <img src="https://img.freepik.com/free-vector/job-hunting-concept-illustration_114360-477.jpg?w=826&t=st=1713120000~exp=1713120600~hmac=example" alt="Ilustração sobre emprego" class="img-fluid mb-4" style="max-width: 400px;">
          </div>

          <h4 class="fw-semibold mb-3">📌 Você vai aprender:</h4>
          <ul class="list-group list-group-flush mb-4">
            <li class="list-group-item">Como montar um currículo simples e direto, mesmo sem experiência</li>
            <li class="list-group-item">O que realmente importa para o recrutador na entrevista</li>
            <li class="list-group-item">Como se apresentar com mais confiança, mesmo estando nervoso</li>
            <li class="list-group-item">Como organizar seus documentos e sua fala na hora de procurar vaga</li>
            <li class="list-group-item">Erros que fazem seu currículo ser ignorado (e como evitar)</li>
          </ul>

          <h4 class="fw-semibold mb-3">🎓 O que você vai receber ao entrar:</h4>
          <ul class="list-group list-group-flush mb-4">
            <li class="list-group-item">Acesso ao treinamento gratuito</li>
            <li class="list-group-item">Modelo de currículo pronto pra editar</li>
            <li class="list-group-item">Conteúdo com linguagem simples, direto no WhatsApp</li>
            <li class="list-group-item">Sem enrolação, sem termos técnicos</li>
          </ul>

          <h4 class="fw-semibold mb-3">📲 Como funciona:</h4>
          <p>O conteúdo será enviado por WhatsApp. Ao clicar no botão abaixo, você vai entrar em um grupo silencioso onde vamos liberar o material gratuito.</p>

          <div class="text-center mt-4">
            <button class="btn btn-success btn-lg text-uppercase fw-bold" data-bs-toggle="modal" data-bs-target="#leadModal">Quero Participar Gratuitamente</button>
          </div>

          <div class="text-muted text-center small mt-4">
            <p>🔒 Este é um conteúdo educacional. Os resultados dependem do seu esforço, dedicação e contexto individual.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Modal -->
  <div class="modal fade" id="leadModal" tabindex="-1" aria-labelledby="leadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="leadModalLabel">Preencha seus dados abaixo para participar do treinamento gratuito.</h5>
          
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          
        </div>
        <div class="modal-body">
          <p>Assim que você enviar, vamos te direcionar para o grupo do WhatsApp, onde o conteúdo será liberado.</p>
          <form id="inscricaoForm" action="{{ route('lead_whatsapp') }}" method="POST">
            <div class="mb-3">
              <label for="nome" class="form-label">Seu nome</label>
              <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="mb-3">
              <label for="whatsapp" class="form-label">Seu WhatsApp</label>
              <input type="tel" class="form-control" id="whatsapp" name="whatsapp" required placeholder="(DDD) 91234-5678">
            </div>
            <p class="fs-6">🔒 Fique tranquilo: seus dados estão seguros e não enviaremos spam</p>
            <button type="submit" class="btn btn-success text-uppercase fw-bold w-100">Entrar no Grupo e receber o curso gratuito</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script>
    
    window.onload = function() {


      $('#inscricaoForm').on('submit', function(event) {
          event.preventDefault();
          fbq('track', 'Lead');
          
          var nome = $('#nome').val();
          var whatsapp = $('#whatsapp').val();
          var telefoneApenasNumeros = whatsapp.replace(/\D/g, '');

          if (telefoneApenasNumeros.length < 10) {
              alert('Digite o seu WhatsApp com o DDD');
              return;
          }

          var ddd = telefoneApenasNumeros.substring(0, 2);
          var telefone = telefoneApenasNumeros.substring(2);
          
          window.location.href = 'https://chat.whatsapp.com/CxKFUs7cY1dLZo6EWu6TNr';
          
          $.ajax({
              url: $(this).attr('action'),
              method: $(this).attr('method'),
              data: {
                  _token: '{{ csrf_token() }}',  // Certifique-se de incluir o CSRF token se necessário
                  nome: nome,
                  origem: "lp_emprego_7_dias",
                  telefone: telefoneApenasNumeros,
                  
              },
              success: function(response) {
                  console.log(response);
              },
              error: function(xhr, status, error) {
                  var errors = xhr.responseJSON.errors;
                  console.log(errors);
              }
          });
      });

        /* Código base para dois Pixels do Facebook*/
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');

        // Inicializa o primeiro pixel
        fbq('init', '419961365827965'); // Substitua PIXEL_ID_1 pelo ID do primeiro Pixel
        // Inicializa o segundo pixel
        fbq('init', '948808649224691'); // Substitua PIXEL_ID_2 pelo ID do segundo Pixel


        // Rastreia a visualização de página para ambos os pixels
        fbq('track', 'PageView');

};  
  </script>
</body>
</html>