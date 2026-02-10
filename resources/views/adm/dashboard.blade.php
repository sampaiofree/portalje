@extends('adm.html_base')

@section('titulo_pagina', 'Painel do Afiliado')

@section('head')
    {{-- Estilos personalizados para o novo design do dashboard --}}
    <style>
        .col-12{margin-top: 10px; margin-bottom: 10px;}
        .page-header {
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        .info-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            border: none;
            height: 100%;
        }
        .section-title {
            color: #343a40;
            font-weight: 600;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        .section-title .step-badge {
            font-size: 1rem;
            width: 32px;
            height: 32px;
            line-height: 32px;
            text-align: center;
            border-radius: 50%;
            color: white;
            background-color: #007bff; /* Cor primária */
        }
        .metric-card {
            text-align: center;
            padding: 1.5rem;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            height: 100%;
        }
        .metric-card .metric-title {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            font-weight: 500;
        }
        .metric-card .metric-value {
            font-size: 2rem;
            font-weight: 700;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
            color: #343a40;
        }
        .metric-card .metric-context {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .link-card {
            display: flex;
            align-items: center;
            padding: 1rem;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            text-decoration: none;
            color: #343a40;
            transition: all 0.2s ease;
            height: 100%;
        }
        .link-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.08);
            background-color: white;
            border-color: #007bff;
        }
        .link-card i {
            font-size: 1.5rem;
            color: #007bff;
            margin-right: 1rem;
            width: 24px;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">

    <!-- SEÇÃO 1: BOAS-VINDAS E PRIMEIROS PASSOS -->
    <div class="page-header">
        <h1 class="display-6">Bem-vindo(a), {{ Auth::user()->name }}!</h1>
        <p class="lead text-muted">Sua jornada para o sucesso como afiliado começa agora. Siga os passos abaixo.</p>
    </div>

    <!-- BOX DE ALERTA PRINCIPAL -->
    @if(!Auth::user()->dominio && !Auth::user()->dominio_externo)
        <div class="alert alert-danger p-4 text-center mb-4" role="alert">
            <h4 class="alert-heading"><i class="ri-error-warning-line me-2"></i>Ação Necessária!</h4>
            <p>Você ainda não configurou seu site. Este é o passo mais importante para começar a vender.</p>
            <hr>
            <button class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#modal_form_dominio">
                <i class="ri-global-line me-2"></i>Configurar Meu Site Agora
            </button>
        </div>
    @elseif(!Auth::user()->whatsapp_atendimento)
        <div class="alert alert-warning p-4 text-center mb-4" role="alert">
            <h4 class="alert-heading text-dark"><i class="ri-whatsapp-line me-2"></i>Quase lá!</h4>
            <p class="text-dark">Seu site está no ar, mas você precisa cadastrar seu WhatsApp de atendimento para não perder vendas.</p>
            <hr>
            <button class="btn btn-warning btn-lg" data-bs-toggle="modal" data-bs-target="#modal_form_user_whatsapp_atendimento">
                <i class="ri-edit-2-line me-2"></i>Cadastrar Meu WhatsApp
            </button>
        </div>
    @endif

    <!-- ESTRUTURA GUIADA EM PASSOS -->
    <div class="row">
        <!-- PASSO 1: CONFIGURAÇÃO E APRENDIZADO -->
        <div class="col-12">
            <div class="info-card mb-4">
                <h3 class="section-title"><span class="step-badge">1</span>Comece Por Aqui: Treinamento e Configuração</h3>
                <div class="row g-3">
                    <div class="col-md-6 col-lg-3"><a href="#" onclick="video_de_ajuda('kJrqK9ZlrA0','','')" class="link-card"><i class="ri-file-shield-2-line"></i>Regras do Programa</a></div>
                    <div class="col-md-6 col-lg-3"><a href="https://www.youtube.com/playlist?list=PL8UPaaNJEdSDFGX9Pj20RBn7QCn7aSbaU" target="_blank" class="link-card"><i class="ri-graduation-cap-line"></i>Curso Método Carvalho</a></div>
                    <div class="col-md-6 col-lg-3"><a href="https://www.youtube.com/playlist?list=PL8UPaaNJEdSBFoe9Pd1TBlklRrZUZt2lU" target="_blank" class="link-card text-danger"><i class="ri-customer-service-2-line"></i>Curso de Atendimento</a></div>
                    <div class="col-md-6 col-lg-3"><a href="https://chat.whatsapp.com/Lb7F6OSFUvx1kmyp4h7kgT" target="_blank" class="link-card text-success"><i class="ri-whatsapp-line"></i>Comunidade de Afiliados</a></div>
                </div>
            </div>
        </div>
        
        <!-- PASSO 2: FERRAMENTAS DO DIA A DIA -->
        <div class="col-12">
            <div class="info-card mb-4">
                <h3 class="section-title"><span class="step-badge">2</span>Ferramentas do Dia a Dia</h3>
                @if(Auth::user()->dominio || Auth::user()->dominio_externo)
                    @php $dominio = Auth::user()->dominio_externo ?? Auth::user()->dominio; @endphp
                    <div class="bg-light p-3 rounded mb-4 border">
                        <h5 class="mb-1"><i class="ri-links-line me-2"></i>Gerador de Links de Divulgação</h5>
                        <p class="text-muted small">Use esta ferramenta para criar seus links com descontos e outros parâmetros.</p>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label for="selectPagina" class="form-label small">Página</label>
                                <select id="selectPagina" class="form-select form-select-sm">
                                    <option value="https://{{$dominio}}">Home page</option>
                                    <option value="https://{{$dominio}}/w">Carvalho WhatsApp</option> 
                                    <option value="https://{{$dominio}}?wd=1">Carvalho WhatsApp Direto</option> 
                                    @foreach($cursos as $curso)
                                        @if($curso->publicado AND $curso->permitir_afiliacao AND $curso->codigo_ref != null)
                                            <option value="https://{{$dominio}}/cursos/{{$curso->url}}/w/">{{$curso->titulo}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="selectCupom" class="form-label small">Cupom</label>
                                <select id="selectCupom" class="form-select form-select-sm">
                                    <option value="">Sem cupom</option>
                                    <option value="o10">10%</option><option value="o20">20%</option><option value="o30">30%</option><option value="o40">40%</option><option value="o50">50%</option><option value="o80">80%</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="input_cidade" class="form-label small">Cidade (Opcional)</label>
                                <input type="text" id="input_cidade" class="form-control form-control-sm" placeholder="Ex: salvador">
                            </div>
                            <div class="col-md-3">
                                <label for="input_whatsapp2" class="form-label small">WhatsApp 2 (Opcional)</label>
                                <input type="number" id="input_whatsapp2" class="form-control form-control-sm" placeholder="55119...">
                            </div>
                        </div>
                        <div class="input-group mt-2">
                            <input type="text" id="linkResultado" class="form-control" readonly>
                            <button id="copiarLink" class="btn btn-primary" type="button">Copiar</button>
                        </div>
                    </div>
                @endif
                <div class="row g-3">
                    <div class="col-md-6 col-lg-4"><a href="https://drive.google.com/drive/folders/1H1Obc3ozkNDUkcimhDwHD1olZYvHBL2f?usp=sharing" target="_blank" class="link-card"><i class="ri-folder-zip-line"></i>Materiais de Apoio</a></div>
                    <!--<div class="col-md-6 col-lg-4"><a href="{{ route('auto_ads') }}" class="link-card"><i class="ri-facebook-box-line"></i>Anúncios Automáticos</a></div>-->
                    <div class="col-md-6 col-lg-4"><a href="{{ route('hotmart_leads', ['version' => null]) }}" class="link-card"><i class="ri-user-search-line"></i>Meus Leads</a></div>
                </div>
            </div>
        </div>

        <!-- PASSO 3: ANÁLISE E OTIMIZAÇÃO -->
        <div class="col-12">
            <div class="info-card mb-4">
                <h3 class="section-title"><span class="step-badge">3</span>Análise de Resultados</h3>
                <div class="row g-4">
                    @if($quantidade_vendas)
                        <div class="col-md-6 col-lg-4">
                            <div class="metric-card">
                                <div class="metric-title">Faturamento Bruto</div>
                                <div class="metric-value">R${{ number_format($total_sum, 2, ',', '.') }}</div>
                                <div class="metric-context">{{ $quantidade_vendas}} Vendas Totais</div>
                            </div>
                        </div>
                    @endif
                    @if($vencidos && $vencidos['n'] > 0)
                        <div class="col-md-6 col-lg-4">
                             <div class="metric-card bg-warning-lighten">
                                <div class="metric-title">A Recuperar</div>
                                <div class="metric-value text-danger">R${{ number_format($vencidos['soma'], 2, ',', '.') }}</div>
                                <div class="metric-context">{{ $vencidos['n']}} vendas expiradas</div>
                            </div>
                        </div>
                    @endif
                    @if($dashboard['conversao_vendas'])
                        <div class="col-md-6 col-lg-4">
                            <div class="metric-card">
                                <div class="metric-title">Taxa de Conversão</div>
                                <div class="metric-value @if($dashboard['conversao_vendas']<5) text-danger @else text-success @endif">{{$dashboard['conversao_vendas']}}%</div>
                                <div class="metric-context">{{$dashboard['totalLeads']}} leads → {{$quantidade_vendas}} vendas</div>
                            </div>
                        </div>
                    @endif
                    <div class="col-12 text-center">
                        <a href="{{ route('ranking') }}" class="btn btn-outline-primary"><i class="ri-trophy-line me-2"></i>Ver Ranking Completo de Afiliados</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEÇÃO DE AJUDA E FAQ -->
    <div class="row">
        <div class="col-12">
            <div class="info-card">
                 <h3 class="section-title"><i class="ri-question-answer-line"></i>Precisa de Ajuda?</h3>
                 <p class="text-muted">Ainda tem dúvidas? Use a busca para encontrar respostas ou veja nossa lista de perguntas frequentes.</p>
                 
                 {{-- SEU CÓDIGO DO ACCORDION DO FAQ AQUI --}}
                 <div class="row px-2 py-3">
                    <input class="form-control" type="text" id="campoPesquisa" placeholder="Digite sua pergunta para filtrar as respostas...">
                </div>
                <div class="accordion custom-accordion" id="custom-accordion-one">
                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading1">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse1" aria-expanded="false" aria-controls="collapse1">
                                                    Qual a vantagem dos formulários nas páginas? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse1" class="collapse" aria-labelledby="heading1" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Com certeza, ter um melhor aproveitamento de leads, o que antes do formulário era normal um aproveitamento de 50% a 60%, com o formulário isso aumenta para mais de 80%. Pois os leads que perdíamos por não chegar até o WhatsApp, agora quando eles preenchem o formulário vc tem os dados dele no CRM do portal para poder entrar em contato.                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading2">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                                    Quais páginas eu posso usar para divulgar os cursos <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse2" class="collapse" aria-labelledby="heading2" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Quando você abre uma conta no portal dos parceiros, você ganha um domínio. Você pode divulgar este domínio da maneira que quiser. Quando as pessoas compram pelo seu domínio, você receber a comissão pelas vendas.                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading3">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                                    Como eu explico sobre a parte prática dos cursos? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse3" class="collapse" aria-labelledby="heading3" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/x2qPiRCtWs4?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading4">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                                    O Certificado dos cursos é reconhecido pelo MEC? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse4" class="collapse" aria-labelledby="heading4" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/HbVUS2RGzy8?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading5">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                                    Preciso de experiência prévia em vendas ou marketing digital? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse5" class="collapse" aria-labelledby="heading5" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Não, você não precisa de experiência prévia. Nosso portal fornece todos os treinamentos e recursos necessários para começar do zero. Se você tem vontade de aprender e se dedicar, está pronto para começar!                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading6">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse6" aria-expanded="false" aria-controls="collapse6">
                                                    Quanto tempo leva para começar a ver resultados? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse6" class="collapse" aria-labelledby="heading6" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            O tempo para ver resultados pode variar de pessoa para pessoa. Tudo depende do seu esforço, da dedicação e da aplicação das estratégias que ensinamos. Alguns veem resultados em poucas semanas, enquanto outros podem levar um pouco mais de tempo. Alguns Parceiros conseguiram em 3 meses um faturamento mensal de R$5mil reais. Outros conseguem em alguns meses um faturamento de R$10mil reais.                                                                     </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading7">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse7" aria-expanded="false" aria-controls="collapse7">
                                                    Está aparecendo um erro quando o lead tenta comprar um curso <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse7" class="collapse" aria-labelledby="heading7" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Um dos principais motivos é que o seu código REF pode estar errado. Assista o vídeo de ajuda novamente clicando AQUI. Se mesmo assim continuar o erro, envie um email para parceiros@jovemempreendedor.org                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading8">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse8" aria-expanded="false" aria-controls="collapse8">
                                                    Como posso saber mais informações sobre os cursos para poder vender? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse8" class="collapse" aria-labelledby="heading8" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Todas as informações estão na página de vendas. Você pode verificar as informações e se ainda tiver dúvidas, pode consultar nosso suporte pelo email: parceiros@jovemempreendedor.org.                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading9">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse9" aria-expanded="false" aria-controls="collapse9">
                                                    Como criar sua conta de anuncios do Facebook <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse9" class="collapse" aria-labelledby="heading9" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/zw4r_-xxXkE?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading10">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse10" aria-expanded="false" aria-controls="collapse10">
                                                    Como solicitar o suporte do Facebook <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse10" class="collapse" aria-labelledby="heading10" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/AxR96lMhyvI?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading11">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse11" aria-expanded="false" aria-controls="collapse11">
                                                    Como posso subir minha campanha no Faceboook? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse11" class="collapse" aria-labelledby="heading11" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/hkEU-nkqvP0?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading12">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse12" aria-expanded="false" aria-controls="collapse12">
                                                    Como cadastrar o meu WhatsApp de atendimento? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse12" class="collapse" aria-labelledby="heading12" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Para cadastrar seu número de atendimento vai ir em Meu site &gt; meu domínio.Lembrando que não pode colocar espaço, nem traço, apenas números. DDI+DDD+seu telefone                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading13">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse13" aria-expanded="false" aria-controls="collapse13">
                                                    Como funciona o projeto Parceiros do Portal? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse13" class="collapse" aria-labelledby="heading13" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Esse projeto tem o mesmo intuito que sempre tivemos com os afiliados, de poder proporcionar a possibilidade de trabalhar em casa, ganhando dinheiro vendendo cursos profissionalizantes, cursos que vão ajudar as pessoas a ingressarem no mercado de trabalho. Temos vários parceiros com a gente que conseguem ter um faturamento acima de 5K por mês. Ser parceiro do portal é poder contar com a gente quando precisar, mantendo uma boa comunicação, trocas de experiências, informações e ter acesso a treinamentos que vão te ajudar a chegar nos seus objetivos. Sempre buscamos estar o mais próximo possível dos nossos parceiros, assim como você.                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading14">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse14" aria-expanded="false" aria-controls="collapse14">
                                                    Qual o canal do Youtube para afiliados? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse14" class="collapse" aria-labelledby="heading14" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Segue link do nosso canal do YouTube: https://www.youtube.com/@parceiroJEhttps://www.youtube.com/@parceiroJE                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading15">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse15" aria-expanded="false" aria-controls="collapse15">
                                                    Como posso dar suporte ao meu aluno? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse15" class="collapse" aria-labelledby="heading15" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/ILWthTrToTE?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading16">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse16" aria-expanded="false" aria-controls="collapse16">
                                                    Não consigo abrir um chamado no suporte de alunos <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse16" class="collapse" aria-labelledby="heading16" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Nesse caso pode enviar o link do nosso suporte direto ao aluno: https://www.jovemempreendedor.org/atendimento/suporte.php Assim que ele clicar automaticamente já vai abrir o WhatsApp de suporte.                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading17">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse17" aria-expanded="false" aria-controls="collapse17">
                                                    Como solicitar suporte na Hotmart? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse17" class="collapse" aria-labelledby="heading17" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Para abrir um chamado na Hotmart seja para uma ajuda como afiliado ou para ajudar um aluno é só acessar o link: https://help.hotmart.com/pt-BR/contact-us                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading18">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse18" aria-expanded="false" aria-controls="collapse18">
                                                    Quais são as regras para trabalhar como parceiro do Portal? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse18" class="collapse" aria-labelledby="heading18" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/kJrqK9ZlrA0?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading19">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse19" aria-expanded="false" aria-controls="collapse19">
                                                    Qual link eu devo enviar? Como pegar o link certo? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse19" class="collapse" aria-labelledby="heading19" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/SVU4BIkSnRY?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading20">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse20" aria-expanded="false" aria-controls="collapse20">
                                                    Onde fica a empresa Portal Jovem Empreendedor? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse20" class="collapse" aria-labelledby="heading20" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Quando alguém perguntar de onde o portal é, você pode responder que a matriz fica na cidade de Taquara/RS, que já trabalhamos com cursos online a mais de 10 anos e temos mais de 85 mil alunos. E por se tratar de curso online não há a necessidade de ter uma unidade presencial em cada cidade. Pode complementar falando que você é de tal cidade (pode falar a sua cidade real) e que é responsável em atender a cidade do seu cliente.                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading21">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse21" aria-expanded="false" aria-controls="collapse21">
                                                    Por que não recebi minha comissão?  <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse21" class="collapse" aria-labelledby="heading21" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/qkuCJKlefnI?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading22">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse22" aria-expanded="false" aria-controls="collapse22">
                                                    Não recebi a minha comissão, o que fazer? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse22" class="collapse" aria-labelledby="heading22" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Favor preencher as informações abaixo e nos enviar para o e-mail: parceiros@jovemempreendedor.org ID do Produto:&nbsp;Parceiro e-mail:&nbsp;Parceiro nome:&nbsp;Transação HP:&nbsp;e-mail comprador:&nbsp;Enviar junto ao e-mail um print da conversa com o cliente, onde você envia o link para o aluno fazer a inscrição.                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading23">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse23" aria-expanded="false" aria-controls="collapse23">
                                                    Posso anunciar no Google Ads?  <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse23" class="collapse" aria-labelledby="heading23" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Pode sim, porém todas as nossas estratégias e suporte são voltadas para o Facebook ADS. Mas ATENÇÃO às regras: Não usar as palavras chaves: portal jovem empreendedor, programa jovem empreendedor e Bruno Sampaio.                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading24">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse24" aria-expanded="false" aria-controls="collapse24">
                                                    Posso usar os criativos do Instagram e YouTube do Portal JE? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse24" class="collapse" aria-labelledby="heading24" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Pode Sim. Porém todos os nossos materiais disponíveis são de uso exclusivo para os cursos oferecidos pelo Portal JE.                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading25">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse25" aria-expanded="false" aria-controls="collapse25">
                                                    O Portal disponibiliza lista de Lookalike? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse25" class="collapse" aria-labelledby="heading25" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/_9P2ip7JmbM?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading26">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse26" aria-expanded="false" aria-controls="collapse26">
                                                    Minha campanha só fica em análise, o que eu faço? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse26" class="collapse" aria-labelledby="heading26" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/4luIXb1KNzs?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading27">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse27" aria-expanded="false" aria-controls="collapse27">
                                                    Onde encontrar os links com a opção de boleto? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse27" class="collapse" aria-labelledby="heading27" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Os links de checkouts com boleto estão nos hotlinks de cada curso dentro da Hotmart.                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                            </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    {{-- Cole todo o conteúdo do seu @section('scripts') original aqui --}}
     <script>
        $(document).ready(function() {
        // Quando o usuário digita no campo de pesquisa
            $('#campoPesquisa').on('keyup', function() {
                var valor = $(this).val().toLowerCase(); // Pega o valor digitado, transforma em minúsculas

                // Verifica cada item do acordeão
                $('#custom-accordion-one .card').filter(function() {
                    // Alterna a visibilidade do item do acordeão com base na correspondência da pesquisa
                    $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1);
                });
            });
        });

        // Função para gerar o link e mostrar no campo de resultado
        function gerarLink() {
            const pagina = document.getElementById('selectPagina').value;
            const desconto = document.getElementById('selectCupom').value;
            const cidade = document.getElementById('input_cidade').value;
            const whatsapp2 = document.getElementById('input_whatsapp2').value;

            // Verifica se a URL já contém um ponto de interrogação
            const separador = pagina.includes('?') ? '&' : '?';

            // Gera o link com base na presença do ponto de interrogação
            const linkGerado = `${pagina}${separador}d=${desconto}&c=${cidade}&t=${whatsapp2}`;
            
            document.getElementById('linkResultado').value = linkGerado;
        }

        // Atualiza o link automaticamente quando há mudanças nos inputs
        document.getElementById('selectPagina').addEventListener('change', gerarLink);
        document.getElementById('selectCupom').addEventListener('change', gerarLink);
        document.getElementById('input_cidade').addEventListener('input', gerarLink);
        document.getElementById('input_whatsapp2').addEventListener('input', gerarLink);

        // Copiar link para o clipboard
        document.getElementById('copiarLink').addEventListener('click', () => {
            const linkResultado = document.getElementById('linkResultado');
            linkResultado.select();
            document.execCommand('copy');
            alert('Link copiado!');
        });

        // Chama a função no carregamento da página para gerar o link inicial
        gerarLink();

        window.addEventListener('load', function() {
            // Seleciona todos os iframes com o atributo data-src
            const iframes = document.querySelectorAll('iframe[data-src]');

            // Substitui o data-src por src para carregar os vídeos
            iframes.forEach(iframe => {
                iframe.setAttribute('src', iframe.getAttribute('data-src'));
                iframe.removeAttribute('data-src'); // Remove o atributo data-src após definir o src
            });
        });

    </script>
@endsection