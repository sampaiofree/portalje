@php
    $tituloCombo = trim((string) ($combo->titulo ?: 'Combo de cursos profissionalizantes'));
    $empresaNome = trim((string) ($comboLp['company_name'] ?? 'Programa Jovem Empreendedor')) ?: 'Programa Jovem Empreendedor';
    $logoPadraoUrl = $comboLp['logo_padrao_url'] ?? asset('img/home_page/logojecolor.webp');
    $logoDarkUrl = $comboLp['logo_dark_url'] ?? asset('img/home_page/logowhite.png');
    $headlineCombo = trim((string) ($combo->headline ?: 'Conheca os cursos incluidos no combo e acelere sua qualificacao.'));
    $descricaoSeo = trim(strip_tags((string) ($combo->descricao_curta ?: $headlineCombo))) ?: 'Combo online com certificado e aplicacao pratica para o mercado de trabalho.';
    $canonicalUrl = url(request()->path());

    $courseCount = (int) ($comboResumo['course_count'] ?? 0);
    $courseNames = is_array($comboResumo['course_names'] ?? null) ? $comboResumo['course_names'] : [];
    $areasAtuacao = is_array($comboResumo['unique_areas'] ?? null) ? $comboResumo['unique_areas'] : [];
    $moduleGroups = is_array($comboResumo['module_groups'] ?? null) ? $comboResumo['module_groups'] : [];
    $bonusTitulos = is_array($comboResumo['bonus_titles'] ?? null) ? $comboResumo['bonus_titles'] : [];
    $heroImagePath = $comboResumo['hero_image_path'] ?? null;
    $heroImageUrl = $heroImagePath ? asset('storage/' . ltrim($heroImagePath, '/')) : asset('img/home_page/certificadoNovo2.webp');

    $totalHours = (int) ($comboResumo['total_hours'] ?? 0);
    $hasTotalHours = !empty($comboResumo['has_total_hours']);
    $areasCount = count($areasAtuacao);
    $certificadosLabel = $courseCount === 1 ? '1 certificado digital' : $courseCount . ' certificados digitais';

    $precoParcelado = trim((string) ($combo->preco_parcelado ?? ''));
    $precoAvista = trim((string) ($combo->preco ?? ''));
    $heroPriceValue = $precoParcelado !== '' ? $precoParcelado : ($precoAvista !== '' ? $precoAvista : 'Consulte');
    $heroShowCashLine = $precoParcelado !== '' && $precoAvista !== '';
    $heroCashValue = $precoAvista !== '' ? $precoAvista : 'consulte';

    $pricingCard = [
        'plan' => 'completo',
        'price_value' => $heroPriceValue,
        'cash_value' => $heroCashValue,
        'show_cash_line' => $heroShowCashLine,
        'checkout_url' => $comboCheckoutUrl,
        'requires_lead' => false,
        'cta_label' => 'Quero o combo completo',
        'hero_label' => 'Investimento do combo completo',
    ];

    $trackingConfig = [
        'pixel_ids' => is_array($comboLp['pixel_ids'] ?? null) ? $comboLp['pixel_ids'] : [],
        'course_id' => (string) $combo->id,
        'course_title' => $tituloCombo,
        'modo_precos' => 'um_preco',
        'ref' => request()->query('ref'),
        'csrf_token' => csrf_token(),
        'lead_endpoint' => route('lead_whatsapp'),
        'back_redirect_url' => '',
        'countdown' => ['enabled' => false, 'minutes' => null, 'action' => null, 'destination_offer' => null, 'storage_key' => null, 'end_label' => 'Encerrado'],
        'pricing' => [
            'initial_state' => ['layout' => 'one', 'hero' => ['label' => 'Investimento do combo completo', 'value' => $heroPriceValue, 'cash_value' => $heroCashValue, 'show_cash_line' => $heroShowCashLine, 'plan' => 'completo'], 'cards' => ['basico' => null, 'completo' => $pricingCard]],
            'offer_variants' => ['completo_padrao' => $pricingCard],
            'current_complete_offer_key' => 'completo_padrao',
            'current_basic_offer_key' => null,
        ],
    ];

    $lpCssVersion = file_exists(public_path('css/lp-course.css')) ? (string) filemtime(public_path('css/lp-course.css')) : null;
    $lpJsVersion = file_exists(public_path('js/lp-course.js')) ? (string) filemtime(public_path('js/lp-course.js')) : null;
    $depoimentosVideos = ['rejxwJ2lX-Q', '1hekoAyPVRs', 'Mnn2yIAlhZk', '9mmtunKAnMY', 'uQ5lB9r8ZlI', 'dMIxLKj35aU', 'gIV1MGief-0', 'X1IJZkVXgBw', '1qWXa9F0qBw'];
    $extraListItems = $bonusTitulos !== [] ? $bonusTitulos : $courseNames;
    $extraListTitle = $bonusTitulos !== [] ? 'Conteudos extras incluidos neste combo' : 'Cursos inclusos neste combo';
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $tituloCombo }} | {{ $empresaNome }}</title>
    <meta name="description" content="{{ $descricaoSeo }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="icon" href="{{ asset('img/logo/logo-je-sm.png') }}" type="image/x-icon">
    <meta property="og:title" content="{{ $tituloCombo }}">
    <meta property="og:description" content="{{ $descricaoSeo }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $heroImageUrl }}">
    <meta property="og:locale" content="pt_BR">
    <link rel="stylesheet" href="{{ asset('css/lp-course.css') }}{{ $lpCssVersion ? '?v=' . $lpCssVersion : '' }}">
</head>
<body>
    <svg xmlns="http://www.w3.org/2000/svg" class="lp-icon-sprite" aria-hidden="true">
        <symbol id="lp-icon-check-circle" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm-1 14-4-4 1.4-1.4 2.6 2.6 5.6-5.6L18 9Z"/></symbol>
        <symbol id="lp-icon-users" viewBox="0 0 24 24"><path fill="currentColor" d="M8 11a4 4 0 1 1 4-4 4 4 0 0 1-4 4Zm8 1a3 3 0 1 1 3-3 3 3 0 0 1-3 3ZM2 20a6 6 0 0 1 12 0v1H2Zm13 1v-1a6 6 0 0 0-2.25-4.68A5.5 5.5 0 0 1 22 20v1Z"/></symbol>
        <symbol id="lp-icon-clock" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm1 10.41 3.29 3.3-1.41 1.41L11 13.24V6h2Z"/></symbol>
        <symbol id="lp-icon-certificate" viewBox="0 0 24 24"><path fill="currentColor" d="M12 3a7 7 0 0 0-4.66 12.22L6 21l6-2 6 2-1.34-5.78A7 7 0 0 0 12 3Zm0 10a3 3 0 1 1 3-3 3 3 0 0 1-3 3Z"/></symbol>
        <symbol id="lp-icon-book" viewBox="0 0 24 24"><path fill="currentColor" d="M4 4a2 2 0 0 1 2-2h14v18H6a2 2 0 0 0-2 2Zm2 0v14h12V4Zm2 3h8v2H8Zm0 4h6v2H8Z"/></symbol>
        <symbol id="lp-icon-chevron-down" viewBox="0 0 24 24"><path fill="currentColor" d="m12 16-6-6 1.4-1.4L12 13.2l4.6-4.6L18 10Z"/></symbol>
        <symbol id="lp-icon-gift" viewBox="0 0 24 24"><path fill="currentColor" d="M20 7h-2.18A3 3 0 1 0 12 4.65 3 3 0 0 0 6.18 7H4a1 1 0 0 0-1 1v3h2v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-9h2V8a1 1 0 0 0-1-1ZM14 4a1 1 0 1 1 0 2h-1V5a1 1 0 0 1 1-1ZM10 4a1 1 0 0 1 1 1v1h-1a1 1 0 1 1 0-2ZM7 11h4v8H7Zm6 8v-8h4v8Z"/></symbol>
        <symbol id="lp-icon-briefcase" viewBox="0 0 24 24"><path fill="currentColor" d="M9 4h6a2 2 0 0 1 2 2v1h3a2 2 0 0 1 2 2v4h-9v-1h-2v1H2V9a2 2 0 0 1 2-2h3V6a2 2 0 0 1 2-2Zm0 3h6V6H9Zm13 8v5a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-5h9v1h2v-1Z"/></symbol>
        <symbol id="lp-icon-rocket" viewBox="0 0 24 24"><path fill="currentColor" d="M14.5 2a8.3 8.3 0 0 0-6 2.49L5 8l3 3 3.51-3.5A6.2 6.2 0 0 1 16 6a6.2 6.2 0 0 1-1.5 4.49L11 14l3 3 3.51-3.5A8.3 8.3 0 0 0 20 7.5V2ZM4 13l-2 2 3 1 1 3 2-2Zm7 2-4 4h4v3l4-4Z"/></symbol>
        <symbol id="lp-icon-tag" viewBox="0 0 24 24"><path fill="currentColor" d="M21 10 11 20 2 11V3h8Zm-13-2a2 2 0 1 0-2-2 2 2 0 0 0 2 2Z"/></symbol>
        <symbol id="lp-icon-shield" viewBox="0 0 24 24"><path fill="currentColor" d="m12 2 8 3v6c0 5.55-3.84 10.74-8 12-4.16-1.26-8-6.45-8-12V5Zm-1 12 6-6-1.4-1.4-4.6 4.6-2.6-2.6L7 9.99Z"/></symbol>
    </svg>
    <header class="lp-header"><div class="lp-shell lp-header__inner"><a class="lp-brand" href="#hero" aria-label="Topo da página"><img src="{{ $logoPadraoUrl }}" alt="{{ $empresaNome }}" width="180" height="47"></a><a href="#planos" class="lp-btn lp-btn--small js-anchor-scroll" data-lp-primary-scroll-cta>Garantir vaga</a></div></header>
    <main>
        <section id="hero" class="lp-hero">
            <div class="lp-shell lp-hero__grid">
                <div class="lp-hero__content">
                    <p class="lp-badge" data-lp-hero-badge>Matrículas abertas</p>
                    <h1>{{ $tituloCombo }}</h1>
                    <p class="lp-subtitle">{{ $headlineCombo }}</p>
                    <ul class="lp-keypoints">
                        <li><span class="lp-keypoint__icon" aria-hidden="true"><svg class="lp-icon lp-icon--sm"><use href="#lp-icon-book"></use></svg></span><span>{{ $courseCount }} {{ $courseCount === 1 ? 'curso incluso' : 'cursos inclusos' }} no combo.</span></li>
                        <li><span class="lp-keypoint__icon" aria-hidden="true"><svg class="lp-icon lp-icon--sm"><use href="#lp-icon-certificate"></use></svg></span><span>{{ $certificadosLabel }} com validade em todo o Brasil.</span></li>
                        <li><span class="lp-keypoint__icon" aria-hidden="true"><svg class="lp-icon lp-icon--sm"><use href="#lp-icon-clock"></use></svg></span><span>@if($hasTotalHours) Carga horária total de até {{ $totalHours }} horas com aplicação prática. @else Acesso imediato para estudar de onde e quando quiser. @endif</span></li>
                    </ul>
                    <div class="lp-price-highlight" data-lp-hero-price><span class="lp-price-highlight__label" data-lp-hero-label>Investimento do combo completo</span><strong data-lp-hero-value>{{ $heroPriceValue }}</strong><small data-lp-hero-cash @if(!$heroShowCashLine) hidden @endif>ou {{ $heroCashValue }} à vista</small></div>
                    <div class="lp-hero__actions"><a href="#planos" class="lp-btn js-anchor-scroll" data-lp-primary-scroll-cta>Quero me inscrever agora</a></div>
                    <p class="lp-hero__waitlist" data-lp-hero-waitlist hidden>Entre na lista de espera pelo WhatsApp e avisaremos quando novas vagas forem liberadas.</p>
                    <div class="lp-proof-inline"><span class="lp-proof-inline__item"><svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-users"></use></svg><span>{{ $courseCount }} {{ $courseCount === 1 ? 'curso' : 'cursos' }}</span></span><span class="lp-proof-inline__item"><svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-briefcase"></use></svg><span>{{ $areasCount }} {{ $areasCount === 1 ? 'área de atuação' : 'áreas de atuação' }}</span></span></div>
                </div>
                <div class="lp-hero__media"><img src="{{ $heroImageUrl }}" alt="Capa do combo {{ $tituloCombo }}" width="1280" height="720" fetchpriority="high"><div class="lp-media-note lp-media-note--top">Acesso imediato</div><div class="lp-media-note lp-media-note--bottom">7 dias de garantia</div></div>
            </div>
        </section>

        <section class="lp-strip"><div class="lp-shell lp-strip__grid"><article><span class="lp-strip__icon" aria-hidden="true"><svg class="lp-icon"><use href="#lp-icon-book"></use></svg></span><strong>{{ $courseCount }}</strong><span>{{ $courseCount === 1 ? 'curso incluso' : 'cursos inclusos' }}</span></article><article><span class="lp-strip__icon" aria-hidden="true"><svg class="lp-icon"><use href="#lp-icon-clock"></use></svg></span><strong>{{ $hasTotalHours ? $totalHours . 'h' : 'Online' }}</strong><span>{{ $hasTotalHours ? 'de conteúdo objetivo' : 'acesso imediato' }}</span></article><article><span class="lp-strip__icon" aria-hidden="true"><svg class="lp-icon"><use href="#lp-icon-briefcase"></use></svg></span><strong>{{ $areasCount }}</strong><span>{{ $areasCount === 1 ? 'área de atuação' : 'áreas de atuação' }}</span></article><article><span class="lp-strip__icon" aria-hidden="true"><svg class="lp-icon"><use href="#lp-icon-certificate"></use></svg></span><strong>{{ $courseCount }}</strong><span>{{ $courseCount === 1 ? 'certificado' : 'certificados' }}</span></article></div></section>

        <section id="conteudo" class="lp-section">
            <div class="lp-shell">
                <div class="lp-heading"><h2 class="lp-heading__title"><span>O que você vai aprender</span></h2><p>Conteúdo organizado em cursos complementares para acelerar sua evolução com foco em prática e aplicação no mercado.</p></div>
                <div class="lp-modules-accordion">
                    @forelse($moduleGroups as $indice => $grupo)
                        <details class="lp-module-item">
                            <summary class="lp-module-item__summary"><span class="lp-module-item__index">Curso {{ $indice + 1 }}</span><span class="lp-module-item__title-row"><span class="lp-module-item__title-wrap"><svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-book"></use></svg><span class="lp-module-item__title">{{ $grupo['title'] ?? 'Curso do combo' }}</span></span><svg class="lp-icon lp-icon--sm lp-module-item__chevron" aria-hidden="true"><use href="#lp-icon-chevron-down"></use></svg></span></summary>
                            @if(!empty($grupo['topics']))<div class="lp-module-item__body"><ul>@foreach($grupo['topics'] as $topico)<li>{{ $topico }}</li>@endforeach</ul></div>@endif
                        </details>
                    @empty
                        <article class="lp-module-empty"><h3><svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-book"></use></svg><span>Conteúdo completo disponível após matrícula</span></h3><p>Você terá acesso imediato aos cursos, materiais e exercícios do combo após a confirmação da inscrição.</p></article>
                    @endforelse
                </div>
                @if(!empty($areasAtuacao))
                    <div class="lp-areas-block"><h3 class="lp-areas__title"><svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-briefcase"></use></svg><span>Áreas de atuação</span></h3><ul class="lp-areas__list">@foreach($areasAtuacao as $area)<li class="lp-areas__item"><svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-check-circle"></use></svg><span>{{ $area }}</span></li>@endforeach</ul></div>
                @endif
            </div>
        </section>

        <section id="bonus" class="lp-section lp-section--muted">
            <div class="lp-shell">
                <div class="lp-heading"><h2 class="lp-heading__title"><span>Bônus e diferenciais</span></h2><p>Além do conteúdo principal, o combo reúne benefícios para acelerar sua entrada no mercado.</p></div>
                <div class="lp-bonus-grid">
                    <article class="lp-bonus-card"><h3><svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-briefcase"></use></svg><span>Carta de estágio</span></h3><p>Use para reforçar sua candidatura e ampliar sua preparação para oportunidades reais.</p></article>
                    <article class="lp-bonus-card"><h3><svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-rocket"></use></svg><span>Preparatório Jovem Aprendiz</span></h3><p>Treinamento complementar para aumentar sua performance em processos seletivos.</p></article>
                    <article class="lp-bonus-card"><h3><svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-certificate"></use></svg><span>Certificados com validação</span></h3><p>Documentos digitais para fortalecer seu currículo em cada curso concluído.</p></article>
                </div>
                @if(!empty($extraListItems))
                    <div class="lp-bonus-extra"><h3><svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-gift"></use></svg><span>{{ $extraListTitle }}</span></h3><ul>@foreach($extraListItems as $extraItem)<li>{{ $extraItem }}</li>@endforeach</ul></div>
                @endif
            </div>
        </section>

        <section id="planos" class="lp-section lp-section--dark">
            <div class="lp-shell">
                <div class="lp-heading lp-heading--light"><h2 class="lp-heading__title"><span data-lp-pricing-title>Garanta seu acesso ao combo</span></h2><p data-lp-pricing-copy>Oferta ativa por tempo limitado. Garanta agora o combo completo enquanto as vagas estão abertas.</p></div>
                <div class="lp-pricing lp-pricing--one" id="lp-pricing-grid" data-lp-pricing-layout="one">
                    <article class="lp-price-card lp-price-card--primary" data-lp-plan-card="completo">
                        <p class="lp-price-card__tag"><svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-tag"></use></svg><span>Combo Completo</span></p>
                        <h3>Acesso total</h3>
                        <p class="lp-price-card__value" data-lp-plan-value="completo">{{ $heroPriceValue }}</p>
                        <p class="lp-price-card__cash" data-lp-plan-cash="completo" @if(!$heroShowCashLine) hidden @endif>ou {{ $heroCashValue }} à vista</p>
                        <ul><li>Acesso completo ao combo</li><li>{{ $certificadosLabel }}</li>@if($hasTotalHours)<li>Carga horária total de até {{ $totalHours }} horas</li>@endif @foreach($courseNames as $courseName)<li>{{ $courseName }}</li>@endforeach</ul>
                        <a href="{{ $comboCheckoutUrl }}" class="lp-btn js-cta" data-checkout-url="{{ $comboCheckoutUrl }}" data-plan="completo" data-requires-lead="0" data-lp-plan-cta="completo">Quero o combo completo</a>
                        <p class="lp-price-card__ended" data-lp-plan-ended="completo" hidden>Encerrado</p>
                        <p class="lp-guarantee"><svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-shield"></use></svg><span>Garantia incondicional de 7 dias.</span></p>
                    </article>
                </div>
                <div class="lp-waitlist-card" data-lp-waitlist-block hidden><p class="lp-waitlist-card__tag">Lista de espera</p><h3>Inscrições encerradas</h3><p>Entre na lista de espera pelo WhatsApp e avisaremos quando novas vagas forem liberadas.</p></div>
            </div>
        </section>

        <section class="lp-section"><div class="lp-shell lp-certificate"><div><div class="lp-heading"><h2 class="lp-heading__title"><span>Certificação para fortalecer seu currículo</span></h2></div><p>Ao concluir os cursos do combo, você recebe certificados digitais reconhecidos para comprovar sua qualificação.</p><ul><li><svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-check-circle"></use></svg><span>Validade nacional.</span></li><li><svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-check-circle"></use></svg><span>Comprovação de horas e conteúdo estudado.</span></li><li><svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-check-circle"></use></svg><span>Documento ideal para processos seletivos.</span></li></ul></div><img src="{{ asset('img/home_page/certificadoNovo2.webp') }}" alt="Exemplo de certificado do combo" width="728" height="515" loading="lazy"></div></section>

        <section id="depoimentos" class="lp-section lp-section--muted"><div class="lp-shell"><div class="lp-heading"><h2>Depoimentos de alunos</h2><p>Veja relatos reais de quem aplicou os cursos e conquistou novas oportunidades.</p></div><div class="lp-testimonials" aria-label="Depoimentos em vídeo">@foreach($depoimentosVideos as $indiceDepoimento => $depoimentoVideoId)<article class="lp-testimonial" data-video-id="{{ $depoimentoVideoId }}" data-loaded="0"><button type="button" class="lp-testimonial__trigger js-testimonial-trigger" data-video-id="{{ $depoimentoVideoId }}" aria-label="Assistir depoimento {{ $indiceDepoimento + 1 }} em vídeo"><img src="https://img.youtube.com/vi/{{ $depoimentoVideoId }}/hqdefault.jpg" alt="Depoimento de aluno do combo {{ $tituloCombo }}" width="480" height="270" loading="lazy" decoding="async"><span class="lp-testimonial__play" aria-hidden="true"><span class="lp-testimonial__play-icon">▶</span></span></button></article>@endforeach</div></div></section>

        <section id="faq" class="lp-section lp-section--muted"><div class="lp-shell"><div class="lp-heading"><h2 class="lp-heading__title"><span>Perguntas frequentes</span></h2></div><div class="lp-faq"><details><summary>Quando tenho acesso ao combo?</summary><p>O acesso é liberado logo após a confirmação da inscrição.</p></details><details><summary>Por quanto tempo posso assistir às aulas?</summary><p>Você pode acessar os cursos do combo conforme as regras vigentes da plataforma após a matrícula.</p></details><details><summary>Os certificados são válidos?</summary><p>Sim. Os certificados são emitidos digitalmente e reconhecidos em todo o território nacional.</p></details><details><summary>Preciso ter experiência prévia?</summary><p>Não. O combo reúne cursos para quem está começando e para quem busca atualização prática.</p></details><details><summary>E se eu não gostar?</summary><p>Você tem 7 dias de garantia para solicitar o reembolso conforme a política da plataforma de pagamento.</p></details></div></div></section>

        <section class="lp-final-cta"><div class="lp-shell lp-final-cta__inner"><div><h2>Pronto para acelerar sua carreira?</h2><p>Comece hoje com acesso imediato ao combo e mais conteúdo para avançar com segurança.</p></div><a href="#planos" class="lp-btn js-anchor-scroll" data-lp-primary-scroll-cta>Quero garantir minha vaga agora</a></div></section>
    </main>
    <footer class="lp-footer"><div class="lp-shell lp-footer__inner"><img src="{{ $logoDarkUrl }}" alt="{{ $empresaNome }}" width="150" height="34" loading="lazy"><p>© {{ date('Y') }} {{ $empresaNome }}. Todos os direitos reservados.</p></div></footer>
    <script id="lp-course-config" type="application/json">@json($trackingConfig)</script>
    <script defer src="{{ asset('js/lp-course.js') }}{{ $lpJsVersion ? '?v=' . $lpJsVersion : '' }}"></script>
    @foreach($trackingConfig['pixel_ids'] as $pixelId)<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $pixelId }}&ev=PageView&noscript=1" alt=""></noscript>@endforeach
</body>
</html>
