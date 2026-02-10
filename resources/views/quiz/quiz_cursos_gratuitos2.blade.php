<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descubra seu Perfil e Ganhe um Curso Gratuito</title>
    <link rel="icon" href="{{asset('/img/logo/logo-je-sm.png')}}" type="image/x-icon">
    <!-- Meta description (descrição da página) -->
    <meta name="description" content="Qualifique-se com cursos completos e certificados. Aprenda habilidades valorizadas pelo mercado e conte com suporte especializado para transformar sua carreira. Inscreva-se e conquiste oportunidades reais com a ajuda dos nossos professores experientes!">
    <!-- Meta keywords (palavras-chave relacionadas ao conteúdo) -->
    <meta name="keywords" content="Curso, Certificado, Online, Profissionalização, Emprego, Educação, Carreira, Certificação, Portal Jovem Empreendedor, Programa Jovem Empreendedor, qualificação profissional, mercado de trabalho, capacitação profissional, curso com certificado, curso profissionalizante.">
    <!-- Meta author (autor do conteúdo) -->
    <meta name="author" content="Portal Jovem Empreendedor">
    <!-- Robots meta tag (controla indexação e rastreamento pelos bots de pesquisa) -->
    <meta name="robots" content="index, follow">
    <!-- Data de publicação -->
    <meta property="article:published_time" content="2024-10-24T08:00:00Z"> 

    <!-- Open Graph meta tags for Facebook and Instagram -->
    <meta property="og:title" content="Portal Jovem Empreendedor">
    <meta property="og:description" content="Qualifique-se com cursos completos e certificados. Aprenda habilidades valorizadas pelo mercado e conte com suporte especializado para transformar sua carreira. Inscreva-se e conquiste oportunidades reais com a ajuda dos nossos professores experientes!">
    <meta property="og:type" content="article"> <!-- Pode ser 'article', 'video', etc. -->
    <meta property="og:url" content="https://jovemempreendedor.org"> <!-- URL canônica -->
    <meta property="og:image" content="{{asset('img/home_page/certificadoNovo2.webp')}}"> <!-- URL da imagem de pré-visualização -->
    <meta property="og:image:alt" content="Portal Jovem Empreendedor">
    <meta property="og:site_name" content="Portal Jovem Empreendedor">
    <meta property="og:locale" content="pt_BR"> 

    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9796869151117705"
     crossorigin="anonymous"></script>
     <meta name="google-adsense-account" content="ca-pub-9796869151117705">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
            position: relative;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .header p {
            opacity: 0.9;
            font-size: 1rem;
        }

        .progress-bar {
            height: 4px;
            background: rgba(255,255,255,0.3);
            position: relative;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: #4CAF50;
            width: 0%;
            transition: width 0.3s ease;
        }

        .quiz-content {
            padding: 40px;
        }

        .question, .result, .lead-capture {
            display: none; /* All sections start hidden */
            animation: fadeIn 0.5s ease-in;
        }

        .question.active, .result.active, .lead-capture.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .question h3, .lead-capture h3 {
            color: #333;
            margin-bottom: 25px;
            font-size: 1.3rem;
            line-height: 1.4;
            text-align: center;
        }
        
        .lead-capture p {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
        }

        .options {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .option {
            background: #f8f9fa;
            border: 2px solid transparent;
            border-radius: 12px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .option:hover {
            background: #e9ecef;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .option::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .option:hover::before {
            left: 100%;
        }
        
        /* Lead Capture Form Styles */
        .lead-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .form-group {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }
        
        .form-actions {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-skip {
            background: transparent;
            color: #6c757d;
            border: 2px solid #6c757d;
        }
        
        .btn-skip:hover {
            background: #6c757d;
            color: white;
        }

        .result {
            text-align: center;
        }

        .result-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .result h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.8rem;
        }

        .result-course {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
        }

        .result-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .restart-btn {
            background: #28a745;
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 25px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .restart-btn:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }

        .question-counter {
            position: absolute;
            top: 90px;
            right: 20px;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.5rem;
        }

        @media (max-width: 768px) {
            .container { margin: 10px; }
            .header { padding: 20px; }
            .header h1 { font-size: 1.5rem; }
            .quiz-content { padding: 20px; }
            .question h3 { font-size: 1.1rem; }
            .option { padding: 15px; }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <div class="question-counter" id="questionCounter"></div>
            <h1>🎯 Descubra seu Perfil e ganhe um curso gratuito</h1>
            <p>Responda as perguntas abaixo e receba um curso gratuito!</p>
        </div>
        
        <div class="progress-bar">
            <div class="progress-fill" id="progressFill"></div>
        </div>

        <div class="quiz-content">
            <!-- As perguntas permanecem as mesmas -->
            <!-- Pergunta 1 -->
            <div class="question" data-question="1">
                <h3>Qual dessas metas se aproxima mais da sua realidade hoje?</h3>
                <div class="options">
                    <div class="option" data-answer="A">Quero aprender uma nova língua pra melhorar no trabalho.</div>
                    <div class="option" data-answer="B">Quero saber lidar melhor com clientes.</div>
                    <div class="option" data-answer="G">Quero abrir meu próprio negócio um dia.</div>
                </div>
            </div>
            <!-- Pergunta 2 -->
            <div class="question" data-question="2">
                <h3>Qual dessas situações mais te incomoda?</h3>
                <div class="options">
                    <div class="option" data-answer="D">Me sinto desorganizado e perco prazos.</div>
                    <div class="option" data-answer="C">Tenho dificuldade em me expressar bem.</div>
                    <div class="option" data-answer="F">Tenho vergonha ao atender pessoas.</div>
                </div>
            </div>
            <!-- ... (o resto das perguntas 3 a 10 vão aqui, sem alterações) ... -->
             <!-- Pergunta 3 -->
            <div class="question" data-question="3">
                <h3>Como você se comporta em grupo?</h3>
                <div class="options">
                    <div class="option" data-answer="E">Gosto de liderar e tomar iniciativa.</div>
                    <div class="option" data-answer="C">Prefiro ouvir e falar quando for necessário.</div>
                    <div class="option" data-answer="B">Gosto de ajudar e atender as pessoas.</div>
                </div>
            </div>
            <!-- Pergunta 4 -->
            <div class="question" data-question="4">
                <h3>Se tivesse que escolher um trabalho agora, preferiria:</h3>
                <div class="options">
                    <div class="option" data-answer="F">Trabalhar em recepção ou atendimento.</div>
                    <div class="option" data-answer="A">Trabalhar com algo que exija inglês.</div>
                    <div class="option" data-answer="D">Ter liberdade mas com muita organização.</div>
                </div>
            </div>
            <!-- Pergunta 5 -->
            <div class="question" data-question="5">
                <h3>O que mais te atrapalha no dia a dia?</h3>
                <div class="options">
                    <div class="option" data-answer="D">Falta de organização.</div>
                    <div class="option" data-answer="E">Falta de confiança para liderar.</div>
                    <div class="option" data-answer="B">Dificuldade em lidar com o público.</div>
                </div>
            </div>
            <!-- Pergunta 6 -->
            <div class="question" data-question="6">
                <h3>Quando alguém te pede ajuda, você...</h3>
                <div class="options">
                    <div class="option" data-answer="B">Atendo com paciência e boa vontade.</div>
                    <div class="option" data-answer="E">Tento resolver e dar direcionamento.</div>
                    <div class="option" data-answer="C">Procuro ouvir bem antes de falar.</div>
                </div>
            </div>
            <!-- Pergunta 7 -->
            <div class="question" data-question="7">
                <h3>Qual dessas qualidades você mais gostaria de desenvolver?</h3>
                <div class="options">
                    <div class="option" data-answer="A">Falar outra língua.</div>
                    <div class="option" data-answer="E">Ser um bom líder.</div>
                    <div class="option" data-answer="G">Aprender sobre negócios.</div>
                </div>
            </div>
            <!-- Pergunta 8 -->
            <div class="question" data-question="8">
                <h3>Qual frase mais combina com você?</h3>
                <div class="options">
                    <div class="option" data-answer="C">Quero aprender a me comunicar melhor.</div>
                    <div class="option" data-answer="F">Gosto de receber bem as pessoas.</div>
                    <div class="option" data-answer="G">Quero entender como uma empresa funciona.</div>
                </div>
            </div>
            <!-- Pergunta 9 -->
            <div class="question" data-question="9">
                <h3>Qual sua maior prioridade agora?</h3>
                <div class="options">
                    <div class="option" data-answer="D">Ser mais produtivo e focado.</div>
                    <div class="option" data-answer="F">Conseguir um trabalho simples, mas com estabilidade.</div>
                    <div class="option" data-answer="E">Crescer profissionalmente e assumir mais responsabilidade.</div>
                </div>
            </div>
            <!-- Pergunta 10 -->
            <div class="question" data-question="10">
                <h3>Como você se imagina em 1 ano?</h3>
                <div class="options">
                    <div class="option" data-answer="A">Entendendo inglês e com mais oportunidades.</div>
                    <div class="option" data-answer="G">Com um negócio próprio ou liderando algo.</div>
                    <div class="option" data-answer="B">Trabalhando com pessoas, atendendo bem.</div>
                </div>
            </div>

            <!-- NOVA PÁGINA DE CAPTURA DE LEADS -->
            <div class="lead-capture" id="leadCapture">
                <h3>🎉 Você está quase lá!</h3>
                <p>Para receber seu resultado e o acesso ao curso gratuito, preencha seus dados abaixo. É opcional!</p>
                <form class="lead-form" id="leadForm">
                    <div class="form-group">
                        <input type="text" id="leadName" name="name" placeholder="Seu nome" class="form-input">
                    </div>
                    <div class="form-group">
                        <input type="email" id="leadEmail" name="email" placeholder="Seu melhor e-mail" class="form-input">
                    </div>
                    <div class="form-group">
                        <input type="tel" id="leadWhatsapp" name="whatsapp" placeholder="Seu WhatsApp (Opcional)" class="form-input">
                    </div>
                    <div class="form-actions">
                         <button type="submit" class="btn btn-submit">Ver meu Resultado Agora!</button>
                         <button type="button" class="btn btn-skip" id="skipBtn">Pular e ver apenas o resultado</button>
                    </div>
                </form>
            </div>

            <!-- Resultado -->
            <div class="result" id="result">
                <div class="result-icon" id="resultIcon">🎉</div>
                <h2>Seu curso ideal é:</h2>
                <div class="result-course" id="resultCourse"></div>
                <div class="result-description" id="resultDescription"></div>
                <a href="/" class="btn restart-btn">Acessar curso grátis</a>
            </div>
        </div>

       <!--<div style="max-width: 95%; overflow: hidden; margin: 20px auto; text-align: center; position: relative;">
        <small style="display: block; font-size: 12px; color: #555; margin-bottom: 5px;">Anúncio</small>
        <ins class="adsbygoogle"
            style="display: inline-block; width: 100% !important; max-width: 728px; overflow: hidden;"
            data-ad-client="ca-pub-9796869151117705"
            data-ad-slot="4439120025"
            data-ad-format="auto"
            data-full-width-responsive="true"></ins>
        </div>-->


    </div>

    <script>
        const totalQuestions = 10;
        
        const courses = {
            'A': { name: '🌟 Inglês para Iniciantes', description: 'Perfeito para você! Você tem interesse em expandir suas oportunidades profissionais através do aprendizado de uma nova língua. Este curso vai te dar a base sólida que você precisa para começar a se comunicar em inglês no ambiente de trabalho.' },
            'B': { name: '🤝 Atendimento ao Público', description: 'Ideal para seu perfil! Você tem o desejo natural de ajudar e atender bem as pessoas. Este curso vai te ensinar técnicas profissionais para lidar com diferentes tipos de clientes e situações, desenvolvendo suas habilidades de relacionamento.' },
            'C': { name: '💬 Comunicação Eficaz', description: 'Excelente escolha para você! Você reconhece a importância de se expressar bem e tem o perfil de alguém que ouve antes de falar. Este curso vai te ajudar a desenvolver confiança e técnicas para se comunicar de forma clara e persuasiva.' },
            'D': { name: '⏰ Gestão do Tempo e Organização Profissional', description: 'Perfeito para suas necessidades! Você busca mais produtividade e organização na sua vida profissional. Este curso vai te ensinar métodos práticos para gerenciar seu tempo, estabelecer prioridades e aumentar sua eficiência no trabalho.' },
            'E': { name: '👑 Liderança e Capacidade de Influenciar', description: 'Ideal para seu potencial! Você tem características naturais de liderança e busca crescimento profissional. Este curso vai desenvolver suas habilidades de liderança, te ensinando a motivar equipes e influenciar positivamente as pessoas ao seu redor.' },
            'F': { name: '🏢 Recepcionista', description: 'Perfeito para você! Você tem o perfil de alguém que gosta de receber bem as pessoas e busca estabilidade profissional. Este curso vai te preparar completamente para trabalhar como recepcionista, ensinando técnicas de atendimento e comunicação profissional.' },
            'G': { name: '📊 Gestão Empresarial', description: 'Excelente para seus objetivos! Você tem visão empreendedora e interesse em entender como os negócios funcionam. Este curso vai te dar uma base sólida em gestão, preparando você para liderar projetos ou até mesmo abrir seu próprio negócio.' }
        };
        
        // Função para obter parâmetros da URL
        function getQueryParams() {
            const params = {};
            new URLSearchParams(window.location.search).forEach((value, key) => {
                params[key] = value;
            });
            return params;
        }

        // Função para calcular o resultado
        function calculateResult(answersArray) {
            const scores = { A: 0, B: 0, C: 0, D: 0, E: 0, F: 0, G: 0 };
            answersArray.forEach(answer => {
                if (scores[answer] !== undefined) scores[answer]++;
            });
            
            let maxScore = -1;
            let result = 'A'; // Default result
            for (let course in scores) {
                if (scores[course] > maxScore) {
                    maxScore = scores[course];
                    result = course;
                }
            }
            return result;
        }

        // Função para enviar os dados para um webhook
        async function sendToWebhook(data) {
            // ================================================================
            // ✨ COLE A URL DO SEU WEBHOOK AQUI ✨
            // Exemplo de webhook (use o seu): https://hooks.zapier.com/hooks/catch/12345/abcde/
            const webhookUrl = ''; 
            // ================================================================

            if (!webhookUrl) {
                console.log('Webhook URL não definida. Dados para envio:', data);
                return; // Não prossegue se a URL não estiver configurada
            }

            try {
                const response = await fetch(webhookUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                });

                if (response.ok) {
                    console.log('Dados enviados para o webhook com sucesso!');
                } else {
                    console.error('Falha ao enviar dados para o webhook:', response.statusText);
                }
            } catch (error) {
                console.error('Erro de rede ou na requisição do webhook:', error);
            }
        }
        
        // Função principal que executa ao carregar a página
        function handlePageLoad() {
            const params = getQueryParams();
            const currentQuestion = parseInt(params.q || '1');
            const answers = params.answers ? params.answers.split(',') : [];

            // Se o quiz foi concluído e o resultado deve ser mostrado
            if (params.showResult === 'true') {
                displayResult(params);
            }
            // Se as perguntas acabaram, mas o resultado ainda não foi mostrado -> mostre a captura de lead
            else if (currentQuestion > totalQuestions) {
                displayLeadCapture(answers);
            }
            // Senão, mostre a pergunta atual
            else {
                displayQuestion(currentQuestion, answers);
            }
        }

        function displayQuestion(questionNumber, answers) {
            const questionDiv = document.querySelector(`.question[data-question="${questionNumber}"]`);
            if (questionDiv) {
                questionDiv.classList.add('active');
                
                // Atualizar barra de progresso e contador
                const progress = (questionNumber - 1) / totalQuestions * 100;
                document.getElementById('progressFill').style.width = progress + '%';
                document.getElementById('questionCounter').textContent = `${questionNumber}/${totalQuestions}`;
                
                // Adicionar listeners de clique nas opções
                questionDiv.querySelectorAll('.option').forEach(option => {
                    option.addEventListener('click', () => {
                        const newAnswers = [...answers, option.dataset.answer];
                        const nextQuestion = questionNumber + 1;
                        // Recarrega a página para a próxima pergunta, guardando as respostas na URL
                        window.location.href = `quiz?q=${nextQuestion}&answers=${newAnswers.join(',')}`;
                    });
                });
            }
        }

        function displayLeadCapture(answers) {
            document.getElementById('leadCapture').classList.add('active');
            document.getElementById('questionCounter').textContent = `Finalizando...`;
            document.getElementById('progressFill').style.width = '100%';

            const form = document.getElementById('leadForm');
            const skipBtn = document.getElementById('skipBtn');
            const answersStr = answers.join(',');

            // Handler para submeter o formulário
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const name = document.getElementById('leadName').value;
                const email = document.getElementById('leadEmail').value;
                const whatsapp = document.getElementById('leadWhatsapp').value;

                // Constrói a URL com os dados do lead
                let url = `quiz?showResult=true&answers=${answersStr}`;
                if (name) url += `&name=${encodeURIComponent(name)}`;
                if (email) url += `&email=${encodeURIComponent(email)}`;
                if (whatsapp) url += `&whatsapp=${encodeURIComponent(whatsapp)}`;
                
                window.location.href = url;
            });

            // Handler para o botão de pular
            skipBtn.addEventListener('click', () => {
                 window.location.href = `quiz?showResult=true&answers=${answersStr}`;
            });
        }
        
        function displayResult(params) {
            const answersArray = params.answers ? params.answers.split(',') : [];
            const resultCode = calculateResult(answersArray);
            const course = courses[resultCode];

            // Exibir o resultado na tela
            document.getElementById('result').classList.add('active');
            document.getElementById('resultCourse').textContent = course.name;
            document.getElementById('resultDescription').textContent = course.description;
            
            // Atualizar UI
            document.getElementById('progressFill').style.width = '100%';
            document.getElementById('questionCounter').textContent = 'Concluído!';
            
            // Preparar dados para o webhook
            const webhookData = {
                finalResultCode: resultCode,
                finalResultName: course.name,
                answers: answersArray,
                lead: {
                    name: params.name || null,
                    email: params.email || null,
                    whatsapp: params.whatsapp || null
                },
                timestamp: new Date().toISOString()
            };
            
            // Chamar a função do webhook
            sendToWebhook(webhookData);
        }

        // Inicia o quiz quando o DOM estiver pronto
        document.addEventListener('DOMContentLoaded', handlePageLoad);

        /*document.addEventListener("DOMContentLoaded", function () {
            setTimeout(function () {
            (adsbygoogle = window.adsbygoogle || []).push({});
            }, 500); // espera 1 segundo depois que o DOM estiver pronto
        });*/
    </script>
</body>
</html>