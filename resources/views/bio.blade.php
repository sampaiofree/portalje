<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cursos Profissionalizantes Online</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #00a859 0%, #0059a9 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: -50px;
        }

        .container {
            background: white;
            border-radius: 25px;
            padding: 40px 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            max-width: 400px;
            width: 100%;
            text-align: center;
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

        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #00a859, #0059a9);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            font-weight: bold;
            box-shadow: 0 8px 25px rgba(0, 168, 89, 0.3);
        }

        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .subtitle {
            color: #666;
            font-size: 16px;
            margin-bottom: 35px;
            line-height: 1.4;
        }

        .link-button {
            display: block;
            width: 100%;
            padding: 18px 20px;
            margin-bottom: 15px;
            text-decoration: none;
            border-radius: 15px;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: none;
            cursor: pointer;
        }

        .link-button:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .link-button:hover:before {
            left: 100%;
        }

        .youtube {
            background: linear-gradient(135deg, #ff4757, #ff3742);
            color: white;
            box-shadow: 0 8px 25px rgba(255, 71, 87, 0.3);
        }

        .youtube:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(255, 71, 87, 0.4);
        }

        .whatsapp {
            background: linear-gradient(135deg, #25d366, #128c7e);
            color: white;
            box-shadow: 0 8px 25px rgba(37, 211, 102, 0.3);
        }

        .whatsapp:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(37, 211, 102, 0.4);
        }

        .website {
            background: linear-gradient(135deg, #00a859, #0059a9);
            color: white;
            box-shadow: 0 8px 25px rgba(0, 168, 89, 0.3);
        }

        .website:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0, 168, 89, 0.4);
        }

        .instagram {
            background: linear-gradient(135deg, #f58634, #e1306c);
            color: white;
            box-shadow: 0 8px 25px rgba(245, 134, 52, 0.3);
        }

        .instagram:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(245, 134, 52, 0.4);
        }

        .icon {
            margin-right: 12px;
            font-size: 20px;
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
                margin: 10px;
            }
            
            h1 {
                font-size: 22px;
            }
            
            .link-button {
                font-size: 16px;
                padding: 16px 18px;
            }
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #999;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo"><img alt='Portal Jovem Empreendedor' src="{{asset('img/logo/logo-dark-je-sm.png')}}" /></div>
        
        <h1>Portal JE</h1>
        <p class="subtitle">Aprenda novas habilidades e transforme sua carreira com nossos cursos online!</p>
        
        <a href="https://www.youtube.com/@PortalJovemEmpreendedor" class="link-button youtube">
            <span class="icon">📺</span>
            Assista nossos vídeos
        </a>
        
        <a href="https://jempreendedor.com/e/grupo-sexta-da-oportunidade" class="link-button whatsapp">
            <span class="icon">💬</span>
            Entre no grupo WhatsApp
        </a>
        
        <a href="https://jempreendedor.com?src=instagram_portal_bio" class="link-button website">
            <span class="icon">🎓</span>
            Conheça todos os cursos
        </a>
        
        <a href="https://instagram.com/jovemempreendedororg" class="link-button instagram" >
            <span class="icon">📷</span>
            Siga no Instagram
        </a>
        
        <div class="footer">
            Invista no seu futuro profissional! 🚀
        </div>
    </div>
</body>
</html>