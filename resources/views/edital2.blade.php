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
        .header .sub-title { display: block; font-size: 1rem; font-weight: 500; color: var(--muted); opacity: 0.9; }
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
        .form-section { background: #f8f9fa; border-radius: 12px; padding: 20px 15px; margin-top: 20px; border: 1px solid #e9ecef; }
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
        .dates-info-block { background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 12px; padding: 20px 15px; margin-bottom: 20px; text-align: center; }
        .dates-info-block h3 { font-size: 16px; color: #4338ca; margin-bottom: 15px; }
        .dates-info-block p { font-size: 14px; color: #4f46e5; margin: 5px 0; }
        .dates-info-block strong { color: #3730a3; }
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
        <div class="header">
            <span class="official-badge">Edital Local — Oportunidade Única</span>
            <span class="pre-title">Inscrições abertas para o Programa Aprenda uma Profissão</span>
            <span class="city-name">{{ $cidade }}</span>
            <span class="sub-title">Vagas com 50% de subsídio para moradores locais</span>
        </div>
        
        <!-- Content -->
        <div class="content">
           <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=400&h=250&fit=crop&crop=faces" alt="Pessoas estudando e trabalhando">
            </div>
                    <!-- Bloco de Datas e CTA Principal (NOVO) -->
        <div class="dates-info-block">
            <h3>🗓️ Datas Importantes</h3>
            <p><strong>Inscrições:</strong> {{ $periodoInscricoes }}</p>
            <p><strong>Apresentação (online):</strong> {{ $apresentacaoFormatada }} às 20h</p>
            <p><strong>Matrículas (início):</strong> a partir de {{ $matriculaFormatada }}</p>
        </div>
        
        <a href="#form-section" class="cta-button">
            <svg class="whatsapp-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.525 3.688"/></svg>
            Quero receber no WhatsApp
        </a>
        
        <div class="aviso-importante">
            <div class="aviso-title">Aviso Importante:</div>
            <div class="aviso-text">
                Programa de iniciativa privada. Vagas subsidiadas pela própria organização. Não é programa governamental.
            </div>
        </div>
        
        <div class="steps">
            <h3>📋 Como funciona:</h3>
            <div class="step"><div class="step-icon">1</div><div class="step-text"><strong>Inscreva-se:</strong> Preencha o cadastro abaixo para receber os avisos oficiais.</div></div>
            <div class="step"><div class="step-icon">2</div><div class="step-text"><strong>Assista a Apresentação:</strong> Conheça os cursos e o valor subsidiado para sua cidade.</div></div>
            <div class="step"><div class="step-icon">3</div><div class="step-text"><strong>Faça sua matrícula:</strong> Após a apresentação, garanta sua vaga com o desconto especial.</div></div>
        </div>
        
        <div class="courses-section">
            <h3 class="section-title">🎯 Cursos Disponíveis</h3>
            <p style="text-align:center; font-size: 14px; color: #555; margin-bottom: 15px;">Os candidatos poderão escolher entre mais de 40 cursos em diversas áreas.</p>
            <button class="ver-mais-btn" onclick="abrirModal()">Ver a lista completa de cursos</button>
        </div>

        <div class="urgency">
            <h4>⏰ Atenção: Vagas Limitadas!</h4>
            <div class="countdown" id="countdown">Calculando...</div>
        </div>
        
        <!-- Bloco de Valores e Subsídio (NOVO) -->
        <div class="valores-section">
            <h3 class="section-title">💰 Valores e Subsídio para {{ $cidade }}</h3>
            <div class="subsidy-highlight">
                <div class="subsidy-percentage">50%</div>
                <div class="subsidy-text">
                    <h3>O Portal Jovem Empreendedor cobre metade do valor!</h3>
                    <p>Nesta etapa para <strong>{{ $cidade }}</strong>, todos os cursos têm um subsídio fixo de 50%.</p>
                </div>
            </div>
            <h4 style="text-align: center; font-size: 15px; margin: 20px 0 10px;">Veja exemplos práticos:</h4>
            <div class="price-examples">
                <div class="price-example-card">
                    <div class="price-from">Curso com valor de tabela: <span>R$ 297,00</span></div>
                    <div class="price-to">Com subsídio, você paga: <span>Apenas R$ 148,50</span></div>
                </div>
                <div class="price-example-card">
                    <div class="price-from">Curso com valor de tabela: <span>R$ 197,00</span></div>
                    <div class="price-to">Com subsídio, você paga: <span>Apenas R$ 98,50</span></div>
                </div>
                <div class="price-example-card">
                    <div class="price-from">Curso com valor de tabela: <span>R$ 97,00</span></div>
                    <div class="price-to">Com subsídio, você paga: <span>Apenas R$ 48,50</span></div>
                </div>
            </div>
            <p class="valores-importante"><strong>Importante:</strong> Condição válida apenas para residentes de <strong>{{ $cidade }}</strong>. O valor final de cada um dos +40 cursos será detalhado na apresentação oficial. Parcelamento em até 12x no cartão disponível.</p>
        </div>

        <div class="faq">
            <h3 class="section-title">❓ Perguntas Frequentes</h3>
            <div class="faq-item"><div class="faq-question">É programa do governo?</div><div class="faq-answer">Não. É uma iniciativa privada do Portal Jovem Empreendedor, com subsídio próprio durante a etapa local de {{ $cidade }}.</div></div>
            <div class="faq-item"><div class="faq-question">Os certificados são reconhecidos pelo MEC?</div><div class="faq-answer">Você recebe certificado de conclusão emitido pelo Portal Jovem Empreendedor, válido para cursos livres em todo o Brasil.</div></div>
            <div class="faq-item"><div class="faq-question">Como recebo os avisos oficiais?</div><div class="faq-answer">Ao se inscrever, você será direcionado para o WhatsApp para confirmar seu cadastro e receber os avisos.</div></div>
        </div>
        
        <div class="form-section" id="form-section">
            <div class="form-title">👇 Garanta sua vaga! Preencha seus dados:</div>
            <form id="inscricao-form">
                @csrf
                <div class="form-group"><label for="nome">Seu nome completo:</label><input type="text" id="nome" name="nome" required placeholder="Digite seu nome"></div>
                <div class="form-group"><label for="whatsapp">Seu WhatsApp (com DDD):</label><input type="tel" id="whatsapp" name="whatsapp" required placeholder="(00) 90000-0000"></div>
                <input type="hidden" name="cidade" value="{{ $cidade }}">
                <button type="submit" class="cta-button">
                    <svg class="whatsapp-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.525 3.688"/></svg>
                    Confirmar Inscrição
                </button>
            </form>
        </div>
    </div>
    
    <div id="modalCursos" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="fecharModal()">&times;</span>
            <div class="modal-header">Lista Completa de Cursos</div>
            <div id="listaCursos">
                <div class="modal-course">📊 Administração</div><div class="modal-course">💻 Informática Básica</div><div class="modal-course">🤝 Atendimento ao Cliente</div><div class="modal-course">📱 Marketing Digital</div><div class="modal-course">📈 Vendas</div><div class="modal-course">🔧 Manutenção de Computadores</div><div class="modal-course">📊 Excel Avançado</div><div class="modal-course">🎨 Design Gráfico</div><div class="modal-course">📷 Fotografia</div><div class="modal-course">🍰 Confeitaria</div><div class="modal-course">✂️ Corte e Costura</div><div class="modal-course">💅 Manicure e Pedicure</div><div class="modal-course">💇 Cabeleireiro</div><div class="modal-course">🔌 Elétrica Residencial</div><div class="modal-course">🚿 Hidráulica</div><div class="modal-course">🏠 Pintura Residencial</div><div class="modal-course">🚗 Mecânica Básica</div><div class="modal-course">📞 Telemarketing</div><div class="modal-course">🛒 E-commerce</div><div class="modal-course">📝 Auxiliar Administrativo</div><div class="modal-course">🏥 Cuidador de Idosos</div><div class="modal-course">👶 Babá</div><div class="modal-course">🧹 Diarista</div><div class="modal-course">🚚 Logística</div><div class="modal-course">📦 Estoquista</div><div class="modal-course">🛡️ Segurança</div><div class="modal-course">🚪 Porteiro</div><div class="modal-course">🏪 Vendedor</div><div class="modal-course">☕ Barista</div><div class="modal-course">🍕 Pizzaiolo</div><div class="modal-course">🧼 Produtos de Limpeza</div><div class="modal-course">🧴 Cosméticos Caseiros</div><div class="modal-course">🎂 Bolos Decorados</div><div class="modal-course">🍫 Doces Gourmet</div><div class="modal-course">🥖 Panificação</div><div class="modal-course">🌱 Jardinagem</div><div class="modal-course">🐕 Adestramento de Cães</div><div class="modal-course">🚲 Delivery</div><div class="modal-course">🎵 Operador de Som</div><div class="modal-course">📹 Editor de Vídeo</div><div class="modal-course">💰 Educação Financeira</div><div class="modal-course">🏋️ Personal Trainer</div><div class="modal-course">🧘 Instrutor de Yoga</div><div class="modal-course">📚 Reforço Escolar</div>
            </div>
        </div>
    </div>
    
    <div id="modalConfirmacao" class="modal">
        <div class="modal-content" style="text-align: center;">
            <h3 style="color: #28a745; font-size: 24px;">✅ Inscrição Recebida!</h3>
            <p style="margin: 15px 0; font-size: 16px;">Falta só um passo! Toque no botão abaixo para <strong>confirmar sua inscrição no WhatsApp</strong> e receber todos os avisos.</p>
            <a href="#" id="whatsappConfirmLink" target="_blank" class="cta-button" style="text-decoration: none;">Confirmar no WhatsApp</a>
        </div>
    </div>

    <div class="footer">
        <p>Portal Jovem Empreendedor - Iniciativa privada<br>Programa não governamental</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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
        
        // Contagem Regressiva
        const countdownDate = new Date('{{ $dataFimInscricoes->copy()->endOfDay()->toIso8601String() }}').getTime();
        const countdownEl = document.getElementById('countdown');
        if (countdownEl) {
            const countdownInterval = setInterval(() => {
                const now = new Date().getTime();
                const distance = countdownDate - now;
                if (distance < 0) {
                    clearInterval(countdownInterval);
                    countdownEl.innerHTML = "Inscrições Encerradas!";
                    return;
                }
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                countdownEl.innerHTML = `As inscrições terminam em ${days}d ${hours}h ${minutes}m`;
            }, 1000);
        }

        // Lógica do Formulário de Inscrição
        const form = document.getElementById('inscricao-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                const nome = formData.get('nome');
                const cidade = formData.get('cidade');

                fetch("{{ route('lead_whatsapp') }}", {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    console.log('Lead salvo:', data);
                    const numeroOficial = '556291945608';
                    const mensagem = `Olá! Me inscrevi para o programa em ${cidade}. Meu nome é ${nome}.`;
                    const whatsappUrl = `https://wa.me/${numeroOficial}?text=${encodeURIComponent(mensagem)}`;
                    
                    document.getElementById('whatsappConfirmLink').href = whatsappUrl;
                    abrirModalConfirmacao();
                    form.reset();
                })
                .catch(error => {
                    console.error('Erro ao salvar lead:', error);
                    alert('Ocorreu um erro ao processar sua inscrição. Tente novamente.');
                });
            });
        }
    });
</script>
</body>
</html>