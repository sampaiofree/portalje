@php
    $empresaNome = trim((string) ($pagina['company_name'] ?? 'Programa Jovem Empreendedor'));
    if ($empresaNome === '') {
        $empresaNome = 'Programa Jovem Empreendedor';
    }
    $logoPadraoUrl = $pagina['logo_padrao_url'] ?? asset('img/home_page/logojecolor.webp');
    $logoDarkUrl = $pagina['logo_dark_url'] ?? asset('img/home_page/logowhite.png');

    $w3CssVersion = @filemtime(public_path('css/w3-course.css'));
    $w3JsVersion = @filemtime(public_path('js/w3-course.js'));

    $videoIds = [
        'rejxwJ2lX-Q',
        '1hekoAyPVRs',
        'Mnn2yIAlhZk',
        '9mmtunKAnMY',
        'uQ5lB9r8ZlI',
        'dMIxLKj35aU',
        'gIV1MGief-0',
        'X1IJZkVXgBw',
        '1qWXa9F0qBw',
    ];

    $cursosVisiveis = collect($cursos ?? [])->filter(function ($curso) {
        return !empty($curso->publicado) && !empty($curso->mostrar_na_pagina);
    })->values();

    $whatsappFloatParams = request()->query();
    $whatsappFloatQuery = http_build_query(array_filter($whatsappFloatParams, fn ($value) => $value !== null && $value !== ''));
    $whatsappFloatUrl = request()->getSchemeAndHttpHost() . '/whatsapp' . ($whatsappFloatQuery !== '' ? '?' . $whatsappFloatQuery : '');
    $cardsDestino = in_array((string) ($pagina['cards_destino'] ?? 'curso'), ['curso', 'whatsapp', 'typebot'], true)
        ? (string) $pagina['cards_destino']
        : 'curso';
    $w3UserId = $pagina['user_id'] ?? $cursosVisiveis->pluck('card_user_id')->filter()->first();

    $trackingConfig = [
        'pixel_id' => (int) ($pagina['pidel_id'] ?? 0),
        'lead_endpoint' => route('lead_whatsapp'),
        'whatsapp_show' => (bool) ($pagina['whatsapp_mostrar'] ?? false),
        'whatsapp_delay_seconds' => (int) ($pagina['whatsapp_atendimento_tempo'] ?? 0),
        'whatsapp_requires_form' => (bool) ($pagina['whatsapp_requires_form'] ?? true),
        'user_id' => $w3UserId,
        'whatsapp_atendimento' => $pagina['whatsapp'] ?? null,
        'whatsapp_atendimento_id' => $pagina['whatsapp_atendimento_id'] ?? null,
        'typebot_enabled' => $cardsDestino === 'typebot',
        'csrf_token' => csrf_token(),
    ];
@endphp
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $empresaNome }}</title>
    <link rel="icon" href="{{ asset('/img/logo/logo-je-sm.png') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/w3-course.css') }}{{ $w3CssVersion ? '?v=' . $w3CssVersion : '' }}">
</head>
<body>
    <main>
        <section class="w3-top-banner" aria-label="Cursos Profissionalizantes">
            <div class="w3-shell w3-top-banner__inner">
                <div class="w3-top-banner__logo-card">
                    <img
                        class="w3-top-banner__logo"
                        src="{{ $logoPadraoUrl }}"
                        alt="{{ $empresaNome }}"
                        width="260"
                        height="86"
                        fetchpriority="high"
                    >
                </div>
                <h2>Cursos Profissionalizantes</h2>
                <ul class="w3-top-banner__list">
                    <li>Nao pague mensalidades</li>
                    <li>Nao pague pelos materiais</li>
                    <li>Nao pague pelo certificado</li>
                    <li>Pague apenas a taxa de inscricao</li>
                </ul>
            </div>
        </section>

        <section class="w3-hero w3-shell">
            <h1>{!! $pagina['headline'] !!}</h1>
            <p class="w3-hero__subtitle">{{ $pagina['headline_sub'] ?? 'Escolha seu curso para falar com o nosso consultor pelo WhatsApp' }}</p>
            <a href="#lista-cursos" class="w3-btn w3-btn--primary js-scroll-to-cursos">Escolher meu curso agora!</a>
        </section>

        <section class="w3-section w3-shell">
            <header class="w3-section__head">
                <h2>4 motivos para você fazer um curso profissionalizante</h2>
            </header>
            <div class="w3-reasons-grid">
                <article class="w3-reason-card">
                    <img src="{{ asset('img/home_page/qualificacao.webp') }}" alt="Qualificação profissional" width="220" height="220" loading="lazy" decoding="async">
                    <h3>Cansado de perder oportunidades por <strong>falta de qualificação?</strong></h3>
                    <p>Destaque-se no mercado de trabalho com qualificação de excelência! Nossos cursos são a chave para abrir as portas de um emprego dos sonhos. Invista em você e aumente suas chances de uma carreira de sucesso.</p>
                </article>
                <article class="w3-reason-card">
                    <img src="{{ asset('img/home_page/semexperiencia.webp') }}" alt="Sem experiência" width="220" height="220" loading="lazy" decoding="async">
                    <h3>Não te contratam por que <strong>não tem experiência?</strong></h3>
                    <p>Prepare-se para o mercado com os melhores professores! Nossos cursos proporcionam uma base teórica sólida, complementada por uma carta de estágio. Assim, você terá a oportunidade de buscar experiência prática na sua área de atuação, aumentando sua empregabilidade.</p>
                </article>
                <article class="w3-reason-card">
                    <img src="{{ asset('img/home_page/primeiroemprego.webp') }}" alt="Primeiro emprego" width="220" height="220" loading="lazy" decoding="async">
                    <h3>Está buscando o seu <strong>primeiro emprego?</strong></h3>
                    <p>Dê o primeiro passo na sua carreira com confiança! Nosso programa de capacitação é desenhado para garantir que você entre no mercado de trabalho pronto para impressionar desde o primeiro dia.</p>
                </article>
                <article class="w3-reason-card">
                    <img src="{{ asset('/img/home_page/empregomelhor.webp') }}" alt="Salário melhor" width="220" height="220" loading="lazy" decoding="async">
                    <h3>Você quer um <strong>salário melhor?</strong></h3>
                    <p>Impulsione seu potencial de ganhos! Com nossa formação, você se qualifica para posições mais elevadas e salários competitivos. Seja um profissional requisitado e valorizado no mercado.</p>
                </article>
            </div>
        </section>

        <section id="lista-cursos" class="w3-section w3-section--courses w3-shell">
            <header class="w3-section__head">
                <h2>Escolha seu curso</h2>
                <p>
                    @if($cardsDestino === 'typebot')
                        Clique no curso para falar com nosso atendimento.
                    @elseif($cardsDestino === 'whatsapp')
                        Clique no curso para falar com nosso consultor no WhatsApp.
                    @else
                        Clique no curso para abrir a página do curso.
                    @endif
                </p>
            </header>

            @if($cursosVisiveis->isNotEmpty())
                <div class="w3-courses-grid">
                    @foreach($cursosVisiveis as $curso)
                        <article class="w3-course-item">
                            <button
                                type="button"
                                class="w3-course-card js-course-trigger"
                                data-link="{{ $curso->card_link ?? '' }}"
                                data-curso="{{ $curso->id }}"
                                data-user="{{ $curso->card_user_id ?? '' }}"
                                data-origem="{{ $curso->card_origem ?? 'whatsapp' }}"
                                data-whatsapp-atendimento-id="{{ $curso->card_whatsapp_atendimento_id ?? '' }}"
                                data-course-title="{{ $curso->titulo }}"
                                @if($cardsDestino === 'typebot')
                                    data-typebot-course-trigger="1"
                                    data-typebot-checkout-url="{{ $curso->typebot_checkout_url ?? '' }}"
                                    data-typebot-whatsapp-url="{{ $curso->typebot_whatsapp_url ?? '' }}"
                                    data-typebot-curso-nome="{{ $curso->typebot_curso_nome ?? $curso->titulo }}"
                                    data-typebot-curso-preco="{{ $curso->typebot_curso_preco ?? '' }}"
                                    data-typebot-curso-imagem="{{ $curso->typebot_curso_imagem_url ?? '' }}"
                                    data-typebot-curso-areas="{{ json_encode($curso->typebot_curso_areas ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}"
                                    data-typebot-curso-conteudo="{{ json_encode($curso->typebot_curso_conteudo ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}"
                                    data-typebot-curso-bonus="{{ json_encode($curso->typebot_curso_bonus ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}"
                                @endif
                            >
                                <span class="w3-course-card__media">
                                    <img
                                        src="{{ $curso->card_image ?? asset('storage/' . $curso->capa_vertical) }}"
                                        alt="{{ $curso->titulo }}"
                                        width="420"
                                        height="560"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </span>
                                <span class="w3-course-card__body">
                                    <strong class="w3-course-card__title">{{ $curso->titulo }}</strong>
                                    <span class="w3-course-card__headline">{{ $curso->card_headline ?? $curso->headline }}</span>
                                    <span class="w3-course-card__meta">Até {{ $curso->horas_completo }} horas</span>
                                    <span class="w3-course-card__meta">{{ number_format((int) $curso->numero_alunos, 0, ',', '.') }} Alunos</span>
                                    <span class="w3-course-card__rating">{{ number_format((float) $curso->nota_avaliacao, 1, ',', '.') }}/5</span>
                                    <span class="w3-course-card__vacancy">{{ $curso->card_vagas ?? 0 }} vagas restantes</span>
                                    <span class="w3-course-card__cta">Quero minha vaga</span>
                                </span>
                            </button>
                        </article>
                    @endforeach
                </div>
            @else
                <p class="w3-empty-courses">Nenhum curso disponível no momento.</p>
            @endif
        </section>

        <section class="w3-section w3-shell">
            <header class="w3-section__head">
                <h2>Por que estudar no {{ $empresaNome }}?</h2>
            </header>
            <div class="w3-why-grid">
                <img src="{{ asset('img/home_page/portal-jovem-empreendedor.webp') }}" alt="{{ $empresaNome }}" width="700" height="430" loading="lazy" decoding="async">
                <div>
                    <p>Os nossos treinamentos irão ajudar você conseguir um emprego de forma rápida mesmo que você não tenha <strong>nenhuma experiência.</strong></p>
                    <p>Somos <strong>a maior escola de cursos profissionalizantes do Brasil.</strong></p>
                </div>
            </div>
        </section>

        <section id="beneficios" class="w3-section w3-section--dark">
            <div class="w3-shell w3-benefits-grid">
                <article>
                    <h2>Benefícios de você entrar no Programa</h2>
                    <ul>
                        <li>Aumente suas chances de contratação imediatamente</li>
                        <li>Aprenda técnicas modernas e eficazes que os empregadores procuram</li>
                        <li>Obtenha habilidades práticas que você pode aplicar desde o primeiro dia no emprego</li>
                        <li>Curso 100% online: Estude de onde estiver e quando puder</li>
                    </ul>
                </article>
                <article>
                    <h2>Diferenciais</h2>
                    <ul>
                        <li>Certificado reconhecido em todo o Brasil</li>
                        <li>Instrutores renomados com vasta experiência</li>
                        <li>O curso é tão rápido que você pode começar a se candidatar para as vagas já na próxima semana!</li>
                        <li>Acesso vitalício</li>
                    </ul>
                </article>
            </div>
        </section>

        <section id="certificado" class="w3-section w3-shell">
            <header class="w3-section__head">
                <h2>Conheça o curso que vai fazer você entrar no mercado de trabalho mais rápido, <span>mesmo sem experiência.</span></h2>
                <p>Com vídeo aulas fáceis de assistir, você termina rápido, em menos de duas semanas, e recebe um <strong>certificado que vale em todo o Brasil.</strong></p>
            </header>
            <div class="w3-certificate-image-wrap">
                <img src="{{ asset('img/home_page/certificadoNovo2.webp') }}" alt="Certificado do curso" width="728" height="515" loading="lazy" decoding="async">
            </div>
            <ul class="w3-check-list">
                <li>Certificado Reconhecido em Todo o Brasil</li>
                <li>Tem a Mesma validade de um Curso Presencial</li>
                <li>Assinatura digital</li>
                <li>Válido como Extensão universitária</li>
                <li>Concursos públicos (mediante verificação do edital)</li>
            </ul>
        </section>

        <section id="alunos" class="w3-section w3-section--deep-dark">
            <div class="w3-shell">
                <header class="w3-section__head w3-section__head--light">
                    <h2>São mais de 120 mil alunos no Brasil e em 14 países</h2>
                    <p>Veja o eles estão dizendo sobre nossos cursos?</p>
                </header>
                <div class="w3-testimonials" aria-label="Depoimentos em vídeo">
                    @foreach($videoIds as $index => $videoId)
                        <article class="w3-testimonial" data-video-id="{{ $videoId }}" data-loaded="0">
                            <button
                                type="button"
                                class="w3-testimonial__trigger js-testimonial-trigger"
                                data-video-id="{{ $videoId }}"
                                aria-label="Assistir depoimento {{ $index + 1 }}"
                            >
                                <img
                                    src="https://img.youtube.com/vi/{{ $videoId }}/hqdefault.jpg"
                                    alt="Depoimento de aluno"
                                    width="480"
                                    height="270"
                                    loading="lazy"
                                    decoding="async"
                                >
                                <span class="w3-testimonial__play" aria-hidden="true">
                                    <span class="w3-testimonial__play-icon">▶</span>
                                </span>
                            </button>
                            <noscript>
                                <a href="https://www.youtube.com/watch?v={{ $videoId }}" target="_blank" rel="noopener noreferrer">Assistir no YouTube</a>
                            </noscript>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="bonus_padrao" class="w3-section w3-shell">
            <header class="w3-section__head">
                <h2>Ao se inscrever agora</h2>
                <p>Você poderá ganhar todos esses bônus abaixo gratuitamente.</p>
                <p>Estes bônus não são vendidos separadamente.</p>
            </header>

            <div class="w3-bonus-item">
                <img src="{{ asset('img/home_page/cartaestagio.webp') }}" alt="Carta de estágio" width="140" height="140" loading="lazy" decoding="async">
                <div>
                    <h3>Carta de Estágio</h3>
                    <p>Uma ferramenta poderosa para abrir portas no competitivo mercado de trabalho, oferecendo um grande diferencial em seu currículo.</p>
                    <p>DE <s>R$ 197,00</s> <span>por R$ 0,00</span></p>
                </div>
            </div>

            <div class="w3-bonus-item">
                <img src="{{ asset('img/home_page/jovemaprendiz.webp') }}" alt="Preparatório Jovem Aprendiz" width="140" height="140" loading="lazy" decoding="async">
                <div>
                    <h3>Preparatório para Jovem Aprendiz</h3>
                    <p>Treinamento em vídeo aulas para ajudar você a ser aprovado neste programa altamente competitivo.</p>
                    <p>DE <s>R$ 297,00</s> <span>por R$ 0,00</span></p>
                </div>
            </div>
        </section>

        <section id="garantia" class="w3-section w3-section--dark-accent">
            <div class="w3-shell w3-guarantee">
                <img src="{{ asset('img/home_page/garantia-7-dias.png') }}" alt="Garantia de 7 dias" width="150" height="150" loading="lazy" decoding="async">
                <p>Satisfação garantida ou seu dinheiro de volta! Você tem 7 dias para experimentar o curso sem riscos</p>
                <p>Não deixe para depois o que você pode fazer hoje para mudar sua vida. As vagas são limitadas e a demanda é alta. Inscreva-se agora!</p>
            </div>
        </section>

        <section id="perguntas_e_respotas" class="w3-section w3-shell">
            <header class="w3-section__head">
                <h2>Perguntas e Respostas</h2>
                <p>Ficou alguma dúvida? Clique nas perguntas abaixo.</p>
            </header>

            <div class="w3-faq">
                <details>
                    <summary>Como funciona o curso</summary>
                    <p>Após a confirmação do pagamento, você receberá por e-mail o acesso ao seu curso. O curso é totalmente online, composto por vídeo aulas acessíveis 24 horas por dia. Assim, você tem a liberdade de estudar quando e onde desejar.</p>
                </details>
                <details>
                    <summary>Quando começo a fazer o meu curso</summary>
                    <p>O acesso ao curso é liberado após a confirmação do pagamento. Se o pagamento for feito por cartão de crédito, a liberação ocorre imediatamente. Você receberá um e-mail da HOTMART no próximo dia útil após o pagamento. Este e-mail pode estar na caixa de spam ou lixo eletrônico. Ao encontrá-lo, clique em "ACESSAR MEU PRODUTO" e crie uma senha para acessar o curso usando seu e-mail e a senha criada. Quando o pagamento for feito via PIX é necessário aguardar a confirmação do banco, que pode ser em alguns minutos ou em até 3 dias.</p>
                </details>
                <details>
                    <summary>Quando posso fazer minha inscrição</summary>
                    <p>Este valor promocional é limitado, e as inscrições podem ser encerradas a qualquer momento. Recomendamos que você faça sua inscrição o quanto antes para garantir sua vaga.</p>
                </details>
                <details>
                    <summary>O certificado é reconhecido em todo o Brasil?</summary>
                    <p>SIM! Nossos cursos profissionalizantes, enquadrados como cursos livres, são autorizados a emitir certificados com base no Decreto N° 5.154, de 23 de Julho de 2004, Art. 1° e 3°, e de acordo com as normas do MEC pela Resolução CNE nº 04/99, Art 11º. Válidos em todo o território nacional, nossos certificados podem ser utilizados para enriquecer seu currículo e contar como horas extracurriculares em faculdades.</p>
                </details>
                <details>
                    <summary>Como vou receber o certificado</summary>
                    <p>O certificado, em formato PDF, será disponibilizado ao final do curso para download e impressão. A parte frontal do certificado, contendo seu nome, é liberada após a conclusão da última aula. Exceção feita ao curso de Operador de Caixa, cujo certificado pode ser solicitado via WhatsApp, conforme informado no curso.</p>
                </details>
                <details>
                    <summary>Este site é seguro?</summary>
                    <p>Sim! Nosso site é protegido por certificado de segurança, como indicado na URL. Além disso, o processamento de pagamentos é realizado por uma empresa especializada que garante a segurança do valor pago por 7 dias, permitindo o reembolso em caso de problemas com o curso. Nossa empresa, reconhecida e ativa há vários anos, tem uma forte presença nas redes sociais, evidenciando a realização de diversos projetos em todo o Brasil.</p>
                </details>
                <details>
                    <summary>Há testes ou provas?</summary>
                    <p>Sim! Existem avaliações de recapitulação ao longo do curso. Não se preocupe, pois é possível refazer as avaliações mais de uma vez, se necessário.</p>
                </details>
                <details>
                    <summary>Quais os requisitos para fazer o curso?</summary>
                    <p>Os cursos do {{ $empresaNome }} são acessíveis a pessoas de todas as idades e níveis de escolaridade. Mesmo para cursos em profissões que exigem ensino médio completo, é possível se matricular e iniciar o aprendizado enquanto conclui seus estudos.</p>
                </details>
            </div>
        </section>
    </main>

    <div class="w3-modal" id="modal_lead" aria-hidden="true">
        <div class="w3-modal__backdrop" data-close-modal></div>
        <div class="w3-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="lead-modal-title">
            <button type="button" class="w3-modal__close" data-close-modal aria-label="Fechar">×</button>
            <div class="w3-modal__brand">
                <img src="{{ $logoPadraoUrl }}" alt="{{ $empresaNome }}" width="220" height="49">
            </div>
            <h2 id="lead-modal-title">{!! $pagina['form_lead_titulo'] !!}</h2>
            <form id="modal_form_lead" action="{{ route('lead_whatsapp') }}" method="POST" novalidate>
                @csrf
                <label for="input_lead_nome">Digite seu nome completo</label>
                <input id="input_lead_nome" type="text" name="nome" placeholder="Digite seu nome completo" required>

                <label for="input_lead_telefone">Digite seu WhatsApp com DDD</label>
                <input id="input_lead_telefone" type="tel" name="telefone" inputmode="numeric" minlength="13" placeholder="Digite apenas números" required>

                <input id="input_lead_link" type="hidden" name="link">
                <input id="input_lead_curso_id" type="hidden" name="curso_id">
                <input id="input_lead_user_id" type="hidden" name="user_id">
                <input id="input_lead_origem" type="hidden" name="origem">
                <input id="input_lead_whatsapp_atendimento_id" type="hidden" name="whatsapp_atendimento_id">

                <p id="lead_form_error" class="w3-form-error" hidden></p>

                <button id="lead_submit_button" type="submit" class="w3-btn w3-btn--success">{!! $pagina['form_lead_botao'] !!}</button>
                <p class="w3-form-note">Ao clicar no botão, você será redirecionado para o nosso Conselheiro de Carreiras no WhatsApp.</p>
            </form>
        </div>
    </div>

    <footer class="w3-footer">
        <div class="w3-shell w3-footer__grid">
            <div>
                <img src="{{ $logoDarkUrl }}" alt="{{ $empresaNome }}" width="200" height="49" loading="lazy" decoding="async">
                <p>Nossa missão é especializar jovens, equipando-os com as habilidades e conhecimentos essenciais para se destacarem em seu primeiro emprego.</p>
            </div>
            <div>
                <h2>Informações para Contato</h2>
                <p><a href="mailto:atendimento@jovemempreendedor.org">atendimento@jovemempreendedor.org</a></p>
            </div>
        </div>
    </footer>

    <a
        id="whatsapp_botao"
        href="{{ $whatsappFloatUrl }}"
        target="_blank"
        rel="noopener noreferrer"
        class="w3-whatsapp-float"
        aria-label="Falar no WhatsApp"
        style="visibility: hidden;"
    >
        <img src="{{ asset('img/home_page/whatsapp.gif') }}" alt="WhatsApp" width="70" height="70" loading="lazy" decoding="async">
    </a>

    <script id="w3-course-config" type="application/json">@json($trackingConfig)</script>
    @if($cardsDestino === 'typebot')
        @include('partials.typebot-course-popup')
    @endif
    <script defer src="{{ asset('js/w3-course.js') }}{{ $w3JsVersion ? '?v=' . $w3JsVersion : '' }}"></script>

    @if(!empty($pagina['pidel_id']))
        <noscript>
            <img
                height="1"
                width="1"
                style="display:none"
                src="https://www.facebook.com/tr?id={{ (int) $pagina['pidel_id'] }}&ev=PageView&noscript=1"
                alt=""
            >
        </noscript>
    @endif
</body>
</html>
