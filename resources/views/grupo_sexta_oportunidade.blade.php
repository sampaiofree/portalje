<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sexta da Oportunidade - Sua Chance de Crescer</title>
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
            color: #333;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(45deg, #25d366, #128c7e);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: white;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        h1 {
            font-size: 2.2em;
            margin-bottom: 10px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }

        .subtitle {
            font-size: 1.1em;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .highlight {
            background: linear-gradient(120deg, #ffeaa7 0%, #fab1a0 100%);
            padding: 20px;
            border-radius: 15px;
            margin: 25px 0;
            border-left: 5px solid #e17055;
        }

        .highlight h3 {
            color: #2d3436;
            margin-bottom: 10px;
            font-size: 1.3em;
        }

        .highlight p {
            color: #636e72;
            line-height: 1.6;
        }

        .benefits {
            display: grid;
            gap: 15px;
            margin: 25px 0;
        }

        .benefit {
            display: flex;
            align-items: center;
            padding: 15px;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .benefit:hover {
            transform: translateX(5px);
        }

        .benefit-icon {
            width: 30px;
            height: 30px;
            background: linear-gradient(45deg, #00b894, #00cec9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-weight: bold;
        }

        .whatsapp-btn {
            background: linear-gradient(45deg, #25d366, #128c7e);
            color: white;
            border: none;
            padding: 18px 40px;
            font-size: 1.2em;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            margin-top: 20px;
            box-shadow: 0 10px 25px rgba(37, 211, 102, 0.3);
        }

        .whatsapp-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(37, 211, 102, 0.4);
            background: linear-gradient(45deg, #128c7e, #25d366);
        }

        .whatsapp-btn:active {
            transform: translateY(-1px);
        }

        .footer-text {
            margin-top: 25px;
            font-size: 0.9em;
            color: #888;
            line-height: 1.4;
        }

        .urgency {
            background: linear-gradient(120deg, #ff7675, #fd79a8);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            font-weight: 600;
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from { box-shadow: 0 0 10px rgba(255, 118, 117, 0.5); }
            to { box-shadow: 0 0 20px rgba(255, 118, 117, 0.8); }
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 1.8em;
            }
            
            .whatsapp-btn {
                padding: 15px 30px;
                font-size: 1.1em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <img alt='Portal Jovem Empreendedor' src="{{asset('img/logo/logo-dark-je-sm.png')}}" />
        </div>
        
        <h1>Sexta da Oportunidade</h1>
        <p class="subtitle">Transforme sua carreira com cursos profissionalizantes em condições especiais</p>
        
        <div class="highlight">
            <h3>🎯 Como Funciona?</h3>
            <p>Toda sexta-feira disponibilizamos um curso profissionalizante com desconto exclusivo para nossos membros entrarem no mercado de trabalho!</p>
        </div>
        
        <div class="benefits">
            <div class="benefit">
                <div class="benefit-icon">✅</div>
                <div>
                    <strong>Cursos Semanais</strong><br>
                    <small>Nova oportunidade toda sexta</small>
                </div>
            </div>
            
            <div class="benefit">
                <div class="benefit-icon">💰</div>
                <div>
                    <strong>Preços Especiais</strong><br>
                    <small>Condições exclusivas para o grupo</small>
                </div>
            </div>
            
            <div class="benefit">
                <div class="benefit-icon">🚀</div>
                <div>
                    <strong>Foco no Mercado</strong><br>
                    <small>Cursos voltados para empregabilidade</small>
                </div>
            </div>
        </div>
        
        <div class="urgency">
            ⏰ Não perca as próximas oportunidades!
        </div>
        
        <!--<a href="#" class="whatsapp-btn" onclick="joinWhatsApp()">
            <span style="font-size: 1.3em;">📱</span>
            Entrar no Grupo Agora
        </a>-->

        <a href="https://chat.whatsapp.com/Eh0R1S0HRFO0AAnEkfjZiB" class="whatsapp-btn" >
            <span style="font-size: 1.3em;">📱</span>
            Entrar no Grupo Agora
        </a>
        
        <p class="footer-text">
            Junte-se a centenas de pessoas que já estão aproveitando as melhores oportunidades de capacitação profissional. É gratuito!
        </p>
    </div>

    <script>
        function joinWhatsApp() {
            // Substitua o número abaixo pelo seu número do WhatsApp (formato: 5511999999999)
            const phoneNumber = "5527999999999"; // Exemplo: 55 + 27 (ES) + número
            const message = encodeURIComponent("Olá! Gostaria de participar do grupo Sexta da Oportunidade 🚀");
            const whatsappUrl = `https://wa.me/${phoneNumber}?text=${message}`;
            window.open(whatsappUrl, '_blank');
        }

        // Adiciona um efeito de hover nos elementos
        document.addEventListener('DOMContentLoaded', function() {
            const benefits = document.querySelectorAll('.benefit');
            benefits.forEach(benefit => {
                benefit.addEventListener('mouseenter', function() {
                    this.style.background = 'rgba(102, 126, 234, 0.2)';
                });
                benefit.addEventListener('mouseleave', function() {
                    this.style.background = 'rgba(102, 126, 234, 0.1)';
                });
            });
        });
    </script>
</body>
</html>