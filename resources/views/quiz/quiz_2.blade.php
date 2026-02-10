<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cursos Gratuitos</title>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9796869151117705" crossorigin="anonymous"></script>
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

        .quiz-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
            position: relative;
        }

        .progress-bar {
            height: 6px;
            background: #e0e0e0;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #00c851, #007e33);
            width: 0%;
            transition: width 0.5s ease;
        }

        .quiz-content {
            padding: 40px;
            text-align: center;
            padding: 40px;
            text-align: center;
            transition: height 0.4s ease;
        }

        .quiz-step {
            display: none;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }

        .quiz-step.active {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .gov-badge {
            background: #0066cc;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        h1 {
            color: #333;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
            line-height: 1.3;
        }

        .subtitle {
            color: #666;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .question {
            color: #333;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .options {
            display: grid;
            gap: 15px;
            margin-bottom: 30px;
        }

        .option {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
            font-weight: 500;
            color: #333;
            position: relative;
            overflow: hidden;
        }

        .option:hover {
            border-color: #007bff;
            background: #f0f8ff;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.15);
        }

        .option.selected {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
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

        .live-counter {
            background: #ff6b35;
            color: white;
            padding: 12px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            animation: pulse 2s infinite;
        }

        .live-counter::before {
            content: '🔴';
            animation: blink 1s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0.5; }
        }

        .final-cta {
            background: linear-gradient(135deg, #28a745, #1e7e34);
            color: white;
            padding: 20px 40px;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
            margin-top: 20px;
        }

        .final-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(40, 167, 69, 0.4);
        }

        .final-message {
            color: #28a745;
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .security-badges {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .security-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #666;
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 20px;
        }

        .icon {
            width: 16px;
            height: 16px;
        }

        @media (max-width: 640px) {
            .quiz-content {
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            .question {
                font-size: 18px;
            }
            
            .option {
                padding: 16px;
                font-size: 14px;
            }
            
            .final-cta {
                padding: 16px 32px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="quiz-container">
        <div class="progress-bar">
            <div class="progress-fill" id="progressFill"></div>
        </div>
        
        <div class="quiz-content">
            <!-- Step 1: Introduction -->
            <div class="quiz-step active" id="step1">
                
                <h1> Quer começar um curso gratuito com apoio de uma plataforma séria e reconhecida?</h1>
                <p class="subtitle">Responda a 3 perguntas rápidas e veja como encontrar cursos 100% gratuitos para mudar seu futuro profissional!</p>
                <div class="question">1. Qual é o seu nível de escolaridade atual?</div>
                <div class="options">
                    <div class="option" onclick="selectOption(this, 'step2')">
                        📚 Ensino fundamental ou médio
                    </div>
                    <div class="option" onclick="selectOption(this, 'step2')">
                        🎓 Ensino superior ou técnico
                    </div>
                </div>
                <div class="live-counter">
                    <span id="liveCount">9</span> pessoas respondendo as perguntas agora em sua cidade
                </div>
            </div>

            <!-- Step 2: Employment Status -->
            <div class="quiz-step" id="step2">
                
                <div class="question">2. Atualmente você está trabalhando?</div>
                <div class="options">
                    <div class="option" onclick="selectOption(this, 'step3')">
                        🔍 Não, estou desempregado
                    </div>
                    <div class="option" onclick="selectOption(this, 'step3')">
                        💼 Sim, mas quero melhorar meu currículo
                    </div>
                </div>
                <div class="live-counter">
                    <span id="liveCount2">12</span> pessoas respondendo as perguntas agora em sua cidade
                </div>
            </div>

            <!-- Step 3: Course Format -->
            <div class="quiz-step" id="step3">
                
                <div class="question">3. Qual formato de curso você prefere?</div>
                <div class="options">
                    <div class="option" onclick="selectOption(this, 'final')">
                        💻 Online, com horários flexíveis
                    </div>
                    <div class="option" onclick="selectOption(this, 'final')">
                        🏫 Presencial, com aulas fixas
                    </div>
                </div>
                <div class="live-counter">
                    <span id="liveCount3">15</span> pessoas respondendo as perguntas agora em sua cidade
                </div>
            </div>

            <!-- Final Step -->
            <div class="quiz-step" id="final">
                
                <div class="final-message" style="margin-bottom: 50px;">✅ Perfeito! Veja agora a lista de cursos gratuitos disponíveis para você:</div>
                <a class="final-cta" href="https://portalje.org/?test=1" style="display: block;
  text-decoration: none;">
                    🎯 Ver cursos gratuitos disponíveis
                </a>
                
                <div class="security-badges">
                    <div class="security-badge">
                        <span class="icon">🔒</span>
                        <span>100% Seguro</span>
                    </div>
                    <div class="security-badge">
                        <span class="icon">🆓</span>
                        <span>Totalmente Gratuito</span>
                    </div>
                    <!--<div class="security-badge">
                        <span class="icon">🇧🇷</span>
                        <span>Governo Oficial</span>
                    </div>-->
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;
        const totalSteps = 3;

        function selectOption(element, nextStep) {
            // Remove selected class from all options in current step
            const currentOptions = element.parentNode.querySelectorAll('.option');
            currentOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Add selected class to clicked option
            element.classList.add('selected');
            
            // Wait a bit for visual feedback, then proceed
            setTimeout(() => {
                showStep(nextStep);
                updateProgress();
                updateLiveCounter();
            }, 500);
        }

        // Substitua sua função showStep por esta
function showStep(stepId) {
    const quizContent = document.querySelector('.quiz-content');
    const currentStepElement = document.querySelector('.quiz-step.active');
    
    // 1. Trava a altura do container para a altura do elemento atual, evitando o "salto"
    quizContent.style.height = currentStepElement.offsetHeight + 'px';

    // 2. Remove a classe 'active' para iniciar a animação de fade-out
    currentStepElement.classList.remove('active');
    
    // 3. Aguarda o fim da animação de fade-out (300ms)
    setTimeout(() => {
        // Esconde o passo antigo completamente
        currentStepElement.style.display = 'none';

        const nextStepElement = document.getElementById(stepId);
        
        // Mostra o próximo passo, mas ele ainda está invisível (opacity: 0)
        nextStepElement.style.display = 'block';
        
        // 4. Calcula a altura do novo passo e a aplica ao container.
        // A transição de CSS que adicionamos fará a animação da altura.
        const nextHeight = nextStepElement.offsetHeight;
        quizContent.style.height = nextHeight + 'px';
        
        // 5. Adiciona a classe 'active' para iniciar a animação de fade-in do novo passo
        nextStepElement.classList.add('active');
        
        // 6. Após a animação de altura terminar (400ms), remove a altura fixa
        // para que o layout volte a ser responsivo.
        setTimeout(() => {
            quizContent.style.height = '';
        }, 400);

        // Atualiza o contador de passo
        if (stepId === 'step2') currentStep = 2;
        else if (stepId === 'step3') currentStep = 3;
        else if (stepId === 'final') currentStep = 4;
        
    }, 300); // Duração da animação de fade-out do passo
}

        function updateProgress() {
            const progressFill = document.getElementById('progressFill');
            const progressPercentage = (currentStep / totalSteps) * 100;
            progressFill.style.width = progressPercentage + '%';
        }

        function updateLiveCounter() {
            // Simulate live counter updates
            const counters = ['liveCount', 'liveCount2', 'liveCount3'];
            counters.forEach(counterId => {
                const counter = document.getElementById(counterId);
                if (counter) {
                    const currentCount = parseInt(counter.textContent);
                    const newCount = currentCount + Math.floor(Math.random() * 3) + 1;
                    counter.textContent = newCount;
                }
            });
        }

        function showCourses() {
            alert('Redirecionando para a lista de cursos gratuitos disponíveis!');
            // Here you would typically redirect to the courses page
            // window.location.href = '/cursos-gratuitos';
        }

        // Auto-update live counters every 10 seconds
        setInterval(updateLiveCounter, 10000);

        // Initialize progress bar
        updateProgress();

        (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
</body>
</html>