@php
    use Carbon\Carbon;

    // --- LÓGICA DE DATAS E CIDADE COM BASE NA URL ---
    $cidade = request()->get('c', 'sua Cidade');
    $dataParam = request()->get('data', now()->addDays(10)->toDateString()); // Padrão: 10 dias no futuro

    // Define o locale para Português do Brasil para o Carbon
    Carbon::setLocale('pt_BR');

    // Cria as datas com Carbon
    $dataApresentacao = Carbon::parse($dataParam, 'America/Sao_Paulo');
    $dataFimInscricoes = $dataApresentacao->copy()->subDay();
    $dataInicioInscricoes = $dataApresentacao->copy()->subDays(7);
    $dataMatricula = $dataApresentacao->copy()->addDay();

    // Formata as datas para exibição
    $periodoInscricoes = $dataInicioInscricoes->format('d/m') . ' a ' . $dataFimInscricoes->format('d/m');
    $apresentacaoFormatada = $dataApresentacao->translatedFormat('d \de F'); // Ex: 11 de Agosto
    $matriculaFormatada = $dataMatricula->translatedFormat('d \de F \de Y'); // Ex: 12 de Agosto de 2025
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprenda uma Profissão e Consiga Emprego em {{ $cidade }}!</title>
    <style>
        :root {
            --brand-ink: #ffffff;
            --muted: #e2e8f0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: Arial, sans-serif; line-height: 1.4; color: #333; background: #f8f9fa; }
        .container { max-width: 400px; margin: 0 auto; background: white; min-height: 100vh; }
        
        /* Header */
        .header { background: linear-gradient(135deg, #28a745, #20c997); color: white; text-align: center; padding: 20px 15px; }
        .header .pre-title { display: block; font-size: 1rem; font-weight: 500; color: var(--muted); letter-spacing: 0.5px; text-transform: uppercase; }
        .header .city-name { display: block; font-size: 2.2rem; font-weight: 900; color: var(--brand-ink); margin-block: 0.1em; }
        .header .sub-title { display: block; font-size: 1rem; font-weight: 500; opacity: 0.9; }
        .official-badge { background: #dc2626; color: white; padding: 4px 10px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; margin-bottom: 8px; display: inline-block; }
        
        /* Modal */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center; }
        .modal-content { background: white; padding: 20px; border-radius: 8px; max-width: 90%; width: 380px; max-height: 80%; overflow-y: auto; position: relative; box-shadow: 0 5px 20px rgba(0,0,0,0.2); }
        .modal-header { font-size: 18px; font-weight: bold; color: #1e3a8a; margin-bottom: 15px; text-align: center; }
        .close-modal { position: absolute; top: 10px; right: 15px; font-size: 24px; cursor: pointer; color: #6b7280; }
        .modal-course { padding: 8px 0; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        .ver-mais-btn { background: #3b82f6; color: white; border: none; padding: 10px 15px; border-radius: 6px; font-size: 13px; cursor: pointer; width: 100%; margin-top: 20px; font-weight: bold; }
        .ver-mais-btn:hover { background: #2563eb; }
        
        /* Main content */
        .content { padding: 20px 15px; }
        .hero-image { text-align: center; margin-bottom: 20px; }
        .hero-image img { width: 100%; max-width: 300px; height: 200px; object-fit: cover; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .aviso-importante { background: #fef3c7; border: 2px solid #f59e0b; border-radius: 8px; padding: 12px; margin: 15px 0; text-align: center; }
        .aviso-title { font-weight: bold; color: #92400e; font-size: 14px; margin-bottom: 5px; }
        .aviso-text { font-size: 12px; color: #78350f; line-height: 1.4; }
        
        /* CTA Button */
        .cta-button { background: #25d366; color: white; border: none; padding: 18px 20px; font-size: 18px; font-weight: bold; border-radius: 12px; width: 100%; cursor: pointer; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; gap: 10px; box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3); transition: all 0.3s ease; text-decoration: none; }
        .cta-button:hover { background: #20b557; transform: translateY(-1px); }
        .whatsapp-icon { width: 24px; height: 24px; }
        
        /* Form */
        .form-section { margin-top: 20px;  }
        .form-title { font-size: 18px; font-weight: bold; text-align: center; margin-bottom: 15px; color: #28a745; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 14px; font-weight: bold; margin-bottom: 5px; color: #555; }
        .form-group input { width: 100%; padding: 12px; font-size: 16px; border: 2px solid #ddd; border-radius: 8px; background: white; }
        .form-group input:focus { outline: none; border-color: #28a745; }
        
        /* Steps */
        .steps { background: white; border: 1px solid #e9ecef; border-radius: 12px; padding: 20px 15px; margin-top: 20px; }
        .steps h3 { font-size: 18px; text-align: center; margin-bottom: 20px; color: #28a745; }
        .step { display: flex; align-items: flex-start; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #e9ecef; }
        .step:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .step-icon { width: 40px; height: 40px; background: #28a745; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; margin-right: 12px; flex-shrink: 0; }
        .step-text { font-size: 15px; }
        
        /* Courses Section */
        .courses-section { text-align: center; margin-top: 20px; }
        .section-title { font-size: 18px; font-weight: bold; text-align: center; margin-bottom: 15px; color: #28a745; }
        
        /* Urgency */
        .urgency { background: #dc3545; color: white; text-align: center; padding: 15px; border-radius: 12px; margin-top: 20px; }
        .urgency h4 { font-size: 16px; margin-bottom: 5px; }
        .countdown { font-size: 18px; font-weight: bold; }
        
        /* Calendario & Datas info (NOVOS ESTILOS) */
        .calendario { background: #fffbeb; border: 1px solid #f59e0b; border-radius: 8px; padding: 20px 15px; margin-top: 20px; }
        .calendario-title { font-size: 16px; font-weight: bold; color: #92400e; margin-bottom: 15px; text-align: center; }
        .evento { margin-bottom: 15px; padding: 12px; background: white; border-radius: 6px; border-left: 4px solid #f59e0b; }
        .evento:last-child { margin-bottom: 0; }
        .evento-data { font-weight: bold; color: #92400e; margin-bottom: 5px; }
        .evento-titulo { font-weight: bold; color: #374151; margin-bottom: 3px; }
        .evento-descricao { font-size: 12px; color: #6b7280; }
        
        /* Valores e Subsídio (NOVOS ESTILOS) */
        .valores-section { padding: 20px 0; }
        .subsidy-highlight { display: flex; align-items: center; gap: 15px; background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 4px solid #22c55e; border-radius: 12px; padding: 20px; margin: 20px 0; }
        .subsidy-percentage { font-size: 3rem; font-weight: 900; color: #16a34a; line-height: 1; }
        .subsidy-text h3 { font-size: 16px; margin: 0 0 5px 0; color: #15803d; }
        .subsidy-text p { font-size: 13px; margin: 0; color: #166534; }
        .price-example-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; margin-bottom: 10px; text-align: left; }
        .price-example-card .price-from { font-size: 14px; color: #6b7280; }
        .price-example-card .price-from span { text-decoration: line-through; }
        .price-example-card .price-to { font-size: 16px; font-weight: bold; color: #16a34a; }
        .price-example-card .price-to span { font-weight: normal; font-size: 14px; }
        .valores-importante { font-size: 13px; text-align: center; color: #4b5563; margin-top: 15px; }
        
        /* FAQ */
        .faq { margin-top: 20px; }
        .faq-item { background: white; border: 1px solid #d1d5db; border-radius: 6px; margin-bottom: 10px; overflow: hidden; }
        .faq-question { background: #f8fafc; padding: 12px 15px; font-weight: bold; color: #374151; cursor: pointer; border-bottom: 1px solid #e5e7eb; position: relative; }
        .faq-question::after { content: '+'; position: absolute; right: 15px; font-size: 1.2em; transition: transform 0.2s; }
        .faq-item.open .faq-question::after { transform: rotate(45deg); }
        .faq-answer { padding: 12px 15px; font-size: 13px; color: #6b7280; display: none; }
        .faq-item.open .faq-answer { display: block; }
        
        /* Footer */
        .footer { background: #f8f9fa; padding: 20px 15px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #e9ecef; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; text-align: center; padding: 25px 15px;">
          
          <div class="logo-container" style="margin-bottom: 15px;">
              <img src="{{ asset('img/home_page/logowhite.png') }}" alt="Logo Portal Jovem Empreendedor" style="max-width: 180px; height: auto;">
          </div>
            <span class="official-badge">Comunicado Oficial</span>
            <span class="pre-title" style="display: block; font-size: 1rem; font-weight: 500; color: rgba(255, 255, 255, 0.8); text-transform: uppercase; letter-spacing: 0.5px;font-weight: bold;">
                Inscrições abertas para
            </span>

            <span class="city-name" style="display: block; font-size: clamp(2.8rem, 12vw, 4rem); font-weight: 900; color: #ffffff; line-height: 1.1; margin-block: 5px; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
                {{ $cidade }}
            </span>

            <span class="sub-title" style="display: block; font-size: 1.1rem; font-weight: 500; color: rgba(255, 255, 255, 0.85);">
                Programa de capacitação profissional
            </span>

            <div style="margin-top: 5px;">
                <span class="private-initiative-badge" style="display: inline-block; background: rgba(0, 0, 0, 0.15); color: rgba(255, 255, 255, 0.9); padding: 5px 10px; border-radius: 50px; font-size: 10px; font-weight: bold; text-transform: uppercase; border: 1px solid rgba(255, 255, 255, 0.2);">
                    Iniciativa Privada • Não Governamental
                </span>
            </div>

        </div>
        
        <!-- Content -->
        <div class="content">
           <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=400&h=250&fit=crop&crop=faces" alt="Pessoas estudando e trabalhando">
            </div>
                    <!-- Bloco de Datas e CTA Principal (NOVO) -->
        <!-- Bloco de Cronograma com Contador Dinâmico -->
        <div class="dates-info-block" style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 12px; padding: 20px 15px; margin-bottom: 20px;">
            
            <h3 style="font-size: 16px; color: #4338ca; margin-top: 0; margin-bottom: 15px;">🗓️ Cronograma</h3>

            <p id="countdown-inscricoes" style="font-size: 14px; color: #3730a3; margin: 5px 0; font-weight: bold;">
                As inscrições terminam em <span style="color: #4f46e5;">Calculando...</span>
            </p>
            
            <p style="font-size: 14px; color: #4f46e5; margin: 5px 0;">
                <strong style="color: #3730a3;">Apresentação:</strong> {{ $apresentacaoFormatada }} às 20h
            </p>
            
            <p style="font-size: 14px; color: #4f46e5; margin: 5px 0;">
                <strong style="color: #3730a3;">Matrículas:</strong> a partir de {{ $matriculaFormatada }}
            </p>

        </div>
        
        <button class="cta-button modal-trigger-button">
            <svg class="whatsapp-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.525 3.688"/></svg>
            Quero receber no WhatsApp
        </button>
        
        <div class="aviso-importante">
            <div class="aviso-title">Aviso Importante:</div>
            <div class="aviso-text">
                Programa de iniciativa privada. Vagas subsidiadas pela própria organização. Não é programa governamental.
            </div>
        </div>
        
        <div class="steps" style="margin-bottom: 15px;">
            <h3>📋 Como funciona:</h3>
            <div class="step"><div class="step-icon">1</div><div class="step-text"><strong>Inscreva-se:</strong> Preencha o cadastro abaixo para receber os avisos oficiais.</div></div>
            <div class="step"><div class="step-icon">2</div><div class="step-text"><strong>Assista a Apresentação:</strong> Conheça os cursos e o valor subsidiado para sua cidade.</div></div>
            <div class="step"><div class="step-icon">3</div><div class="step-text"><strong>Faça sua matrícula:</strong> Após a apresentação, garanta sua vaga com o desconto especial.</div></div>
        </div>

        <button class="cta-button modal-trigger-button">
            <svg class="whatsapp-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.525 3.688"/></svg>
            Quero receber no WhatsApp
        </button>
        
        <!-- Ideia 3: Layout com Gradiente e Apelo Visual Forte -->
        <div class="courses-section" style="border-radius: 16px; padding: 30px 25px; text-align: center; margin-top: 20px; color: white; background: linear-gradient(135deg, #2563eb, #3b82f6);">
            
            <div style="font-size: 2.5rem; margin-bottom: 10px; line-height: 1;">
                &#127891; <!-- Mortarboard Icon -->
            </div>

            <h3 class="section-title" style="font-size: 20px; font-weight: 800; color: white; margin-bottom: 8px; text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">
                Sua Nova Carreira Começa Aqui
            </h3>

            <p style="font-size: 15px; color: rgba(255, 255, 255, 0.9); line-height: 1.5; margin-bottom: 25px;">
                Com mais de 40 opções, o curso ideal para sua entrada no mercado de trabalho está a um clique de distância.
            </p>

            <button class="ver-mais-btn" onclick="abrirModal()" style="background: white; color: #2563eb; border: none; padding: 14px 20px; border-radius: 8px; font-size: 15px; cursor: pointer; width: 100%; font-weight: bold; transition: all 0.2s; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                Ver Lista Completa de Cursos
            </button>
            
        </div>

     
        
        <!-- Ideia 1: Cartão de Destaque com Ícone -->
        <div class="valores-section" style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 16px; padding: 25px 20px; margin-top: 20px;margin-bottom: 20px;">
            
            
            <div class="subsidy-highlight" style="text-align: center; padding: 20px; background: white; border: 2px dashed #9333ea; border-radius: 12px; margin: 20px 0;">
                
                <div class="subsidy-percentage" style="font-size: 3.5rem; font-weight: 900; color: #9333ea; line-height: 1; display: block;">
                    BOLSAS DE ATÉ 50%
                </div>
            </div>
            
            <h3 style="text-align: center; font-size: 18px; margin: 25px 0 15px; color: #4b5563;">O Portal Jovem Empreendedor cobre <strong>metade do valor</strong> para você!</h3>
            <h4 style="text-align: center; font-size: 14px; margin: 25px 0 15px; color: #4b5563;">Veja como fica na prática:</h4>
            
            <div class="price-examples" style="display: grid; gap: 10px;">
                <!-- Exemplo 1 -->
                <div style="display: flex; justify-content: space-between; align-items: center; background: white; padding: 12px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="font-size: 14px; color: #6b7280;">De <span style="text-decoration: line-through;">R$ 297,00</span></div>
                    <div style="font-size: 16px; font-weight: bold; color: #16a34a;">Por apenas R$ 148,50</div>
                </div>
                <!-- Exemplo 2 -->
                <div style="display: flex; justify-content: space-between; align-items: center; background: white; padding: 12px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="font-size: 14px; color: #6b7280;">De <span style="text-decoration: line-through;">R$ 197,00</span></div>
                    <div style="font-size: 16px; font-weight: bold; color: #16a34a;">Por apenas R$ 98,50</div>
                </div>
                <!-- Exemplo 3 -->
                <div style="display: flex; justify-content: space-between; align-items: center; background: white; padding: 12px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="font-size: 14px; color: #6b7280;">De <span style="text-decoration: line-through;">R$ 97,00</span></div>
                    <div style="font-size: 16px; font-weight: bold; color: #16a34a;">Por apenas R$ 48,50</div>
                </div>
            </div>
            
            <p style="font-size: 13px; text-align: center; color: #4b5563; margin-top: 25px; line-height: 1.5;">
                <strong>Importante:</strong> Condição válida apenas para residentes de <strong>{{ $cidade }}</strong>. O valor final de cada um dos +40 cursos será detalhado na apresentação oficial. Parcelamento em até 12x no cartão disponível.
            </p>
        </div>

        <button class="cta-button modal-trigger-button">
            <svg class="whatsapp-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.525 3.688"/></svg>
            Quero receber no WhatsApp
        </button>

        <div class="faq">
            <h3 class="section-title">❓ Perguntas Frequentes</h3>
            <div class="faq-item"><div class="faq-question">É programa do governo?</div><div class="faq-answer">Não. É uma iniciativa privada do Portal Jovem Empreendedor, com subsídio próprio durante a etapa local de {{ $cidade }}.</div></div>
            <div class="faq-item"><div class="faq-question">Os certificados são reconhecidos pelo MEC?</div><div class="faq-answer">Sim! Nossos cursos são classificados como "Cursos Livres", autorizados pela Lei nº 9.394/96 e regulamentados pelo Decreto Presidencial nº 5.154/04. O certificado é válido em todo o Brasil para comprovação de qualificação profissional, horas complementares e enriquecimento do seu currículo.</div></div>
            <div class="faq-item"><div class="faq-question">Como recebo os avisos oficiais?</div><div class="faq-answer">Ao se inscrever, você será direcionado para o WhatsApp para confirmar seu cadastro e receber os avisos.</div></div>
        </div>
    </div>

    <!-- NOVO: Modal do Formulário de Inscrição / Lista de Espera -->
    <div id="formModal" class="modal" style="display: none; align-items: flex-start; justify-content: center; padding-top: 50px;">
        <div class="modal-content">
            <span class="close-modal" onclick="fecharFormModal()">&times;</span>
            
            <div class="form-section" id="form-section">
                <div id="formModalTitle" class="form-title">
                    <!-- O título será alterado pelo JavaScript -->
                </div>
                <form id="inscricao-form">
                    @csrf
                    <div class="form-group">
                        <label for="nome">Seu nome completo:</label>
                        <input type="text" id="nome" name="nome" required placeholder="Digite seu nome">
                    </div>
                    <div class="form-group">
                        <label for="whatsapp">Seu WhatsApp (com DDD):</label>
                        <input type="tel" id="whatsapp" name="whatsapp" required placeholder="(00) 90000-0000">
                    </div>
                    <input type="hidden" name="cidade" value="{{ $cidade }}">
                    <input type="hidden" name="evento_portal" value="LP EDITAL"> <!-- Campo para saber se é inscrição ou lista de espera -->

                    <button id="formSubmitButton" type="submit" class="cta-button" style="margin-top: 10px;">
                        <!-- O texto do botão será alterado pelo JavaScript -->
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div id="modalCursos" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="fecharModal()">&times;</span>
            <div class="modal-header">Lista Completa de Cursos</div>
            <div id="listaCursos">
              @php
              if (isset($curso->mostrar_curso) AND !$curso->mostrar_curso) {$curso['mostrar_na_pagina'] = false;}
              @endphp
              @forEach($cursos as $curso)
                @if($curso['publicado'] AND $curso['mostrar_na_pagina'])
                  <div class="modal-course">{{$curso->titulo}}</div>
                @endif
              @endforeach
            </div>
        </div>
    </div>
    
    <div id="modalConfirmacao" class="modal">
        <div class="modal-content" style="text-align: center;">
            <h3 style="color: #28a745; font-size: 24px;">✅ Inscrição Recebida!</h3>
            <p style="margin: 15px 0; font-size: 16px;">Falta só um passo! Toque no botão abaixo para <strong>confirmar sua inscrição no WhatsApp</strong> e receber todos os avisos.</p>
            <a onclick="fbq('track', 'Lead')" href="#" id="whatsappConfirmLink" target="_blank" class="cta-button" style="text-decoration: none;">Confirmar no WhatsApp</a>
        </div>
    </div>

    <div class="footer">
        <p>Portal Jovem Empreendedor - Iniciativa privada<br>Programa não governamental</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

       // --- ELEMENTOS GLOBAIS ---
    const formModal = document.getElementById('formModal');
    const modalTriggerButtons = document.querySelectorAll('.modal-trigger-button');
    const countdownElement = document.getElementById('countdown-inscricoes');
    
    // --- FUNÇÕES DO MODAL DO FORMULÁRIO ---
    window.abrirFormModal = function() {
        if (formModal) formModal.style.display = 'flex';
    }
    window.fecharFormModal = function() {
        if (formModal) formModal.style.display = 'none';
    }

    // Adiciona evento de clique para todos os botões que abrem o modal
    modalTriggerButtons.forEach(button => {
        button.addEventListener('click', abrirFormModal);
    });
    
    // Fecha o modal se clicar fora da área de conteúdo
    window.addEventListener('click', function(event) {
        if (event.target === formModal) fecharFormModal();
        
        // Mantém a lógica do modal de cursos
        const modalCursos = document.getElementById('modalCursos');
        if (event.target === modalCursos) window.fecharModal();

        // Mantém a lógica do modal de confirmação
        const modalConfirmacao = document.getElementById('modalConfirmacao');
        if (event.target === modalConfirmacao) modalConfirmacao.style.display = 'none';
    });

    // --- LÓGICA DO COUNTDOWN E ESTADO DA PÁGINA ---
    const dataFinalInscricoes = new Date('{{ $dataFimInscricoes->copy()->endOfDay()->toIso8601String() }}').getTime();
    
    function atualizarEstadoDaPagina(distancia) {
        // Elementos que precisam ser alterados
        const formModalTitle = document.getElementById('formModalTitle');
        const formSubmitButton = document.getElementById('formSubmitButton');
       

        if (distancia < 0) {
            // ESTADO: INSCRIÇÕES ENCERRADAS (LISTA DE ESPERA)
            
            // Altera o texto dos botões na página
            modalTriggerButtons.forEach(button => {
                button.innerHTML = `
                    <svg class="whatsapp-icon" viewBox="0 0 24 24" fill="currentColor"><!-- ... path ... --></svg>
                    Inscrições Encerradas - Entrar na lista de espera
                `;
            });

            // Altera o conteúdo do modal
            if (formModalTitle) formModalTitle.textContent = 'Inscrições Encerradas. Entre para a lista de espera!';
            if (formSubmitButton) formSubmitButton.textContent = 'Entrar na Lista de Espera';
            
            
        } else {
            // ESTADO: INSCRIÇÕES ABERTAS
            
            // Define o conteúdo padrão do modal
            if (formModalTitle) formModalTitle.textContent = '👇 Garanta sua vaga! Preencha seus dados:';
            if (formSubmitButton) formSubmitButton.innerHTML = `
                <svg class="whatsapp-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.525 3.688"/></svg>
                Confirmar Inscrição
            `;
           
        }
    }
    
    const countdownInterval = setInterval(function() {
        const agora = new Date().getTime();
        const distancia = dataFinalInscricoes - agora;

        // Atualiza o estado da página (textos e botões)
        atualizarEstadoDaPagina(distancia);

        // Atualiza o contador visual
        if (countdownElement) {
            if (distancia < 0) {
                clearInterval(countdownInterval);
                countdownElement.innerHTML = '<strong style="color: #b91c1c;">As inscrições estão encerradas!</strong>';
                return;
            }
            const dias = Math.floor(distancia / (1000 * 60 * 60 * 24));
            const horas = Math.floor((distancia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutos = Math.floor((distancia % (1000 * 60 * 60)) / (1000 * 60));
            const segundos = Math.floor((distancia % (1000 * 60)) / 1000);
            
            let textoContador = '';
            if (dias > 0) textoContador = `${dias}d ${horas}h ${minutos}m ${segundos}s`;
            else if (horas > 0) textoContador = `${horas}h ${minutos}m ${segundos}s`;
            else textoContador = `${minutos}m ${segundos}s`;
            
            countdownElement.innerHTML = `As inscrições terminam em <span style="color: #3730a3;">${textoContador}</span>`;
        } else {
             clearInterval(countdownInterval); // Para o contador se o elemento não existir
        }

    }, 1000);

    // --- LÓGICA DO FORMULÁRIO DE INSCRIÇÃO ---
    const form = document.getElementById('inscricao-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const nome = formData.get('nome');
            const cidade = formData.get('cidade');
            const status = formData.get('status');

            // Desabilita o botão para evitar cliques duplos
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = 'Enviando...';

            fetch("{{ route('curso_gratuito_lead') }}", { 
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Lead salvo:', data);
                
            })
            .catch(error => {
                console.error('Erro ao salvar lead:', error);
                //alert('Ocorreu um erro. Tente novamente.');
            })
            .finally(() => {
                fecharFormModal(); // Fecha o modal do formulário
                
                const numeroOficial = '{{$info['whatsapp_atendimento']}}';
                let mensagem = '';
                if (status === 'lista_espera') {
                    mensagem = `Olá! Tenho interesse em entrar na lista de espera para o programa em ${cidade}. Meu nome é ${nome}.`;
                } else {
                    mensagem = `Olá! Me inscrevi para o programa Aprenda uma Profissão em ${cidade}. Meu nome é ${nome}.`;
                }
                
                const whatsappUrl = `https://wa.me/${numeroOficial}?text=${encodeURIComponent(mensagem)}`;
                document.getElementById('whatsappConfirmLink').href = whatsappUrl;
                document.getElementById('modalConfirmacao').style.display = 'flex'; // Abre o modal de confirmação
                form.reset();
                // Reabilita o botão após a operação
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            });
        });
    }

        // Funções do Modal de Cursos
        const modalCursos = document.getElementById('modalCursos');
        window.abrirModal = function() { modalCursos.style.display = 'flex'; }
        window.fecharModal = function() { modalCursos.style.display = 'none'; }
        
        // Função do Modal de Confirmação
        const modalConfirmacao = document.getElementById('modalConfirmacao');
        function abrirModalConfirmacao() { modalConfirmacao.style.display = 'flex'; }
        
        // Fechar modais ao clicar fora
        window.addEventListener('click', function(event) {
            if (event.target === modalCursos) fecharModal();
            if (event.target === modalConfirmacao) modalConfirmacao.style.display = 'none';
        });
        
        // Função para o FAQ
        document.querySelectorAll('.faq-question').forEach(item => {
            item.addEventListener('click', () => {
                item.parentElement.classList.toggle('open');
            });
        });

        // Máscara de Telefone
        const telInput = document.getElementById('whatsapp');
        if (telInput) {
            telInput.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                value = value.substring(0, 11);
                if (value.length > 2) {
                    if (value.length > 6) {
                         if (value.length > 10) {
                            value = `(${value.substring(0,2)}) ${value.substring(2,7)}-${value.substring(7)}`;
                        } else {
                            value = `(${value.substring(0,2)}) ${value.substring(2,6)}-${value.substring(6)}`;
                        }
                    } else {
                       value = `(${value.substring(0,2)}) ${value.substring(2)}`;
                    }
                } else if (value.length > 0) {
                    value = `(${value}`;
                }
                e.target.value = value;
            });
        }
        
    });
  
    @if($info['meta_pixel_id'])
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');

    // INICIALIZE SEU(S) PIXEL(S) AQUI
    fbq('init', '{{$info['meta_pixel_id']}}'); 

    // EVENTO PADRÃO DE VISUALIZAÇÃO DE PÁGINA
    fbq('track', 'PageView');
    @endif
</script>

@if($info['meta_pixel_id']) <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{$info['meta_pixel_id']}}&ev=PageView&noscript=1" /></noscript> @endif
<!-- End Meta Pixel Code -->
</body>
</html>