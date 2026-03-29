@php
    $tituloCurso = trim((string) ($curso->titulo ?? 'Curso profissionalizante'));
    $empresaNome = trim((string) ($curso->company_name ?? 'Programa Jovem Empreendedor'));
    if ($empresaNome === '') {
        $empresaNome = 'Programa Jovem Empreendedor';
    }
    $logoPadraoUrl = $curso->logo_padrao_url ?? asset('img/home_page/logojecolor.webp');
    $logoDarkUrl = $curso->logo_dark_url ?? asset('img/home_page/logowhite.png');
    $headlineCurso = trim((string) ($curso->headline ?? 'Aprenda habilidades práticas e acelere sua carreira com certificado reconhecido.'));
    $descricaoSeo = $headlineCurso !== '' ? $headlineCurso : 'Curso online com certificado e aplicação prática para o mercado de trabalho.';
    $canonicalUrl = url(request()->path());

    $areasAtuacao = [];
    if (isset($curso->areas_de_atuacao) && is_array($curso->areas_de_atuacao)) {
        $areasAtuacao = array_values(array_filter(array_map('trim', $curso->areas_de_atuacao)));
    }

    $conteudoPrincipal = [];
    if (isset($curso->conteudo_principal_acordion) && is_array($curso->conteudo_principal_acordion)) {
        $conteudoPrincipal = $curso->conteudo_principal_acordion;
    }
    $topicosDestaque = array_slice($conteudoPrincipal, 0, 6);

    $bonusConteudo = [];
    if (isset($curso->conteudo_bonus) && is_array($curso->conteudo_bonus)) {
        $bonusConteudo = $curso->conteudo_bonus;
    }

    $bonusTitulos = [];
    foreach ($bonusConteudo as $bonusItem) {
        if (!empty($bonusItem['title'])) {
            $bonusTitulos[] = trim((string) $bonusItem['title']);
        }
    }

    $depoimentosVideos = [
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

    $modoPrecos = in_array(($curso->modo_precos ?? 'padrao'), ['padrao', 'um_preco', 'dois_precos'], true)
        ? $curso->modo_precos
        : 'padrao';

    $mostrarPlanoSecundario = !($desconto_banner ?? false)
        && ($curso->origem ?? null) !== 'whatsapp'
        && in_array($modoPrecos, ['padrao', 'dois_precos'], true);

    $permiteSck = ($curso->origem ?? 'checkout_completo') !== 'whatsapp';

    $appendSck = static function (?string $url, string $sck, bool $permitirSck = true): string {
        if (!$url) {
            return '#';
        }

        if (!$permitirSck) {
            return $url;
        }

        if (preg_match('/([?&])sck=[^&]*/', $url)) {
            $substituido = preg_replace('/([?&])sck=[^&]*/', '$1sck=' . $sck, $url, 1);
            return $substituido ?: $url;
        }

        return $url . (str_contains($url, '?') ? '&' : '?') . 'sck=' . $sck;
    };

    $checkoutHero = $appendSck($curso->link_checkout_completo ?? '#', 'hero_completo', $permiteSck);
    $checkoutFinal = $appendSck($curso->link_checkout_completo ?? '#', 'final_completo', $permiteSck);
    $checkoutCompletoPlano = $appendSck($curso->link_checkout_completo ?? '#', 'plano_completo', $permiteSck);
    $checkoutBasicoPlano = $appendSck($curso->link_checkout_basico ?? '#', 'plano_basico', $permiteSck);

    $notaAvaliacao = (string) ($curso->nota_avaliacao ?? '4.9');
    $numeroAlunos = (int) ($curso->numero_alunos ?? 0);
    if ($numeroAlunos < 1000) {
        $numeroAlunos += 1058;
    }

    $parseMonetaryValue = static function ($valor): ?float {
        if (!is_scalar($valor) || $valor === '') {
            return null;
        }

        $normalizado = trim((string) $valor);
        $normalizado = preg_replace('/^\s*\d+\s*x(?:\s*de)?\s*/i', '', $normalizado) ?? $normalizado;
        $somenteNumeros = preg_replace('/[^\d,.]/', '', $normalizado);

        if (!$somenteNumeros) {
            return null;
        }

        if (str_contains($somenteNumeros, ',') && str_contains($somenteNumeros, '.')) {
            $ultimaVirgula = strrpos($somenteNumeros, ',');
            $ultimoPonto = strrpos($somenteNumeros, '.');

            if ($ultimaVirgula !== false && $ultimoPonto !== false && $ultimaVirgula > $ultimoPonto) {
                $somenteNumeros = str_replace('.', '', $somenteNumeros);
                $somenteNumeros = str_replace(',', '.', $somenteNumeros);
            } else {
                $somenteNumeros = str_replace(',', '', $somenteNumeros);
            }
        } elseif (str_contains($somenteNumeros, ',')) {
            $somenteNumeros = str_replace('.', '', $somenteNumeros);
            $somenteNumeros = str_replace(',', '.', $somenteNumeros);
        } elseif (substr_count($somenteNumeros, '.') > 1) {
            $partes = explode('.', $somenteNumeros);
            $decimal = array_pop($partes);
            $somenteNumeros = implode('', $partes) . '.' . $decimal;
        }

        return is_numeric($somenteNumeros) ? (float) $somenteNumeros : null;
    };

    $precoCheioCompletoValor = $parseMonetaryValue($curso->preco_cheio_completo ?? null);
    $precoCheioBasicoValor = $parseMonetaryValue($curso->preco_cheio_basico ?? null);

    $ocultarParceladoCompleto = $precoCheioCompletoValor !== null && $precoCheioCompletoValor < 100;
    $ocultarParceladoBasico = $precoCheioBasicoValor !== null && $precoCheioBasicoValor < 100;

    $exibicaoPrecoCompleto = $ocultarParceladoCompleto
        ? ($curso->preco_cheio_completo ?? 'Consulte')
        : ($curso->preco_parcelado_completo ?? 'Consulte');
    $exibicaoPrecoBasico = $ocultarParceladoBasico
        ? ($curso->preco_cheio_basico ?? 'Consulte')
        : ($curso->preco_parcelado_basico ?? 'Consulte');

    $heroPriceLabel = $mostrarPlanoSecundario
        ? 'Investimento único de'
        : 'Investimento do plano completo';
    $heroPriceValue = $mostrarPlanoSecundario
        ? $exibicaoPrecoBasico
        : $exibicaoPrecoCompleto;
    $heroShowCashLine = $mostrarPlanoSecundario
        ? false
        : !$ocultarParceladoCompleto;
    $heroCashValue = $curso->preco_cheio_completo ?? 'consulte';

    $backRedirectParams = request()->query();
    unset($backRedirectParams['g'], $backRedirectParams['aula']);
    $backRedirectParams['g'] = 1;
    $backRedirectUrl = url()->current() . '?' . http_build_query($backRedirectParams);

    $pixelIds = array_values(array_unique(array_filter([
        env('META_PIXEL_ID_PRIMARY', '419961365827965'),
        env('META_PIXEL_ID_SECONDARY', '948808649224691'),
        $curso->meta_pixel_id ?? null,
    ])));

    $pricingInitialState = is_array($curso->pricing_initial_state ?? null) ? $curso->pricing_initial_state : [];
    $pricingOfferVariants = is_array($curso->pricing_offer_variants ?? null) ? $curso->pricing_offer_variants : [];
    $countdownConfig = is_array($curso->countdown ?? null) ? $curso->countdown : [
        'enabled' => false,
        'minutes' => null,
        'action' => null,
        'destination_offer' => null,
        'storage_key' => null,
        'end_label' => 'Encerrado',
    ];
    $waitlistCopy = 'Entre na lista de espera pelo WhatsApp e avisaremos quando novas vagas forem liberadas.';
    $waitlistWhatsappDigits = preg_replace('/\D/', '', (string) ($curso->whatsapp_atendimento ?? '')) ?? '';
    $waitlistWhatsappText = 'Olá, quero entrar na lista de espera do curso de ' . $tituloCurso;
    $waitlistWhatsappUrl = $waitlistWhatsappDigits !== ''
        ? 'https://wa.me/' . $waitlistWhatsappDigits . '?text=' . rawurlencode($waitlistWhatsappText)
        : '#';
    $currentCompleteOfferKey = $curso->current_complete_offer_key ?? 'completo_padrao';
    $currentBasicOfferKey = $curso->current_basic_offer_key ?? null;

    $trackingConfig = [
        'pixel_ids' => $pixelIds,
        'course_id' => (string) ($curso->id ?? ''),
        'course_title' => $tituloCurso,
        'modo_precos' => $modoPrecos,
        'ref' => request()->query('ref'),
        'csrf_token' => csrf_token(),
        'lead_endpoint' => route('lead_whatsapp'),
        'back_redirect_url' => $backRedirectUrl,
        'countdown' => $countdownConfig,
        'pricing' => [
            'initial_state' => $pricingInitialState,
            'offer_variants' => $pricingOfferVariants,
            'current_complete_offer_key' => $currentCompleteOfferKey,
            'current_basic_offer_key' => $currentBasicOfferKey,
        ],
    ];

    $lpCssVersion = file_exists(public_path('css/lp-course.css'))
        ? (string) filemtime(public_path('css/lp-course.css'))
        : null;
    $lpJsVersion = file_exists(public_path('js/lp-course.js'))
        ? (string) filemtime(public_path('js/lp-course.js'))
        : null;
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $tituloCurso }} | {{ $empresaNome }}</title>
    <meta name="description" content="{{ $descricaoSeo }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="icon" href="{{ asset('img/logo/logo-je-sm.png') }}" type="image/x-icon">

    <meta property="og:title" content="{{ $tituloCurso }}">
    <meta property="og:description" content="{{ $descricaoSeo }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ asset('/storage/' . $curso->capa_horizontal) }}">
    <meta property="og:locale" content="pt_BR">

    <link rel="stylesheet" href="{{ asset('css/lp-course.css') }}{{ $lpCssVersion ? '?v=' . $lpCssVersion : '' }}">
</head>
<body>
    <svg xmlns="http://www.w3.org/2000/svg" class="lp-icon-sprite" aria-hidden="true">
        <symbol id="lp-icon-check-circle" viewBox="0 0 24 24">
            <path fill="currentColor" d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm-1 14-4-4 1.4-1.4 2.6 2.6 5.6-5.6L18 9Z"/>
        </symbol>
        <symbol id="lp-icon-users" viewBox="0 0 24 24">
            <path fill="currentColor" d="M8 11a4 4 0 1 1 4-4 4 4 0 0 1-4 4Zm8 1a3 3 0 1 1 3-3 3 3 0 0 1-3 3ZM2 20a6 6 0 0 1 12 0v1H2Zm13 1v-1a6 6 0 0 0-2.25-4.68A5.5 5.5 0 0 1 22 20v1Z"/>
        </symbol>
        <symbol id="lp-icon-star" viewBox="0 0 24 24">
            <path fill="currentColor" d="m12 2 2.9 5.88 6.5.94-4.7 4.57 1.11 6.48L12 16.82 6.19 19.87l1.11-6.48-4.7-4.57 6.5-.94Z"/>
        </symbol>
        <symbol id="lp-icon-clock" viewBox="0 0 24 24">
            <path fill="currentColor" d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm1 10.41 3.29 3.3-1.41 1.41L11 13.24V6h2Z"/>
        </symbol>
        <symbol id="lp-icon-certificate" viewBox="0 0 24 24">
            <path fill="currentColor" d="M12 3a7 7 0 0 0-4.66 12.22L6 21l6-2 6 2-1.34-5.78A7 7 0 0 0 12 3Zm0 10a3 3 0 1 1 3-3 3 3 0 0 1-3 3Z"/>
        </symbol>
        <symbol id="lp-icon-book" viewBox="0 0 24 24">
            <path fill="currentColor" d="M4 4a2 2 0 0 1 2-2h14v18H6a2 2 0 0 0-2 2Zm2 0v14h12V4Zm2 3h8v2H8Zm0 4h6v2H8Z"/>
        </symbol>
        <symbol id="lp-icon-chevron-down" viewBox="0 0 24 24">
            <path fill="currentColor" d="m12 16-6-6 1.4-1.4L12 13.2l4.6-4.6L18 10Z"/>
        </symbol>
        <symbol id="lp-icon-gift" viewBox="0 0 24 24">
            <path fill="currentColor" d="M20 7h-2.18A3 3 0 1 0 12 4.65 3 3 0 0 0 6.18 7H4a1 1 0 0 0-1 1v3h2v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-9h2V8a1 1 0 0 0-1-1ZM14 4a1 1 0 1 1 0 2h-1V5a1 1 0 0 1 1-1ZM10 4a1 1 0 0 1 1 1v1h-1a1 1 0 1 1 0-2ZM7 11h4v8H7Zm6 8v-8h4v8Z"/>
        </symbol>
        <symbol id="lp-icon-briefcase" viewBox="0 0 24 24">
            <path fill="currentColor" d="M9 4h6a2 2 0 0 1 2 2v1h3a2 2 0 0 1 2 2v4h-9v-1h-2v1H2V9a2 2 0 0 1 2-2h3V6a2 2 0 0 1 2-2Zm0 3h6V6H9Zm13 8v5a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-5h9v1h2v-1Z"/>
        </symbol>
        <symbol id="lp-icon-rocket" viewBox="0 0 24 24">
            <path fill="currentColor" d="M14.5 2a8.3 8.3 0 0 0-6 2.49L5 8l3 3 3.51-3.5A6.2 6.2 0 0 1 16 6a6.2 6.2 0 0 1-1.5 4.49L11 14l3 3 3.51-3.5A8.3 8.3 0 0 0 20 7.5V2ZM4 13l-2 2 3 1 1 3 2-2Zm7 2-4 4h4v3l4-4Z"/>
        </symbol>
        <symbol id="lp-icon-tag" viewBox="0 0 24 24">
            <path fill="currentColor" d="M21 10 11 20 2 11V3h8Zm-13-2a2 2 0 1 0-2-2 2 2 0 0 0 2 2Z"/>
        </symbol>
        <symbol id="lp-icon-shield" viewBox="0 0 24 24">
            <path fill="currentColor" d="m12 2 8 3v6c0 5.55-3.84 10.74-8 12-4.16-1.26-8-6.45-8-12V5Zm-1 12 6-6-1.4-1.4-4.6 4.6-2.6-2.6L7 9.99Z"/>
        </symbol>
    </svg>
    <header class="lp-header">
        <div class="lp-shell lp-header__inner">
            <a class="lp-brand" href="#hero" aria-label="Topo da página">
                <img src="{{ $logoPadraoUrl }}" alt="{{ $empresaNome }}" width="180" height="47">
            </a>
            <a
                href="#planos"
                class="lp-btn lp-btn--small js-anchor-scroll"
                data-lp-primary-scroll-cta
            >
                Garantir vaga
            </a>
        </div>
    </header>

    <main>
        <section id="hero" class="lp-hero">
            <div class="lp-shell lp-hero__grid">
                <div class="lp-hero__content">
                    <p class="lp-badge" data-lp-hero-badge>Matrículas abertas</p>
                    <h1>{{ $tituloCurso }}</h1>
                    <p class="lp-subtitle">{{ $headlineCurso }}</p>

                    <ul class="lp-keypoints">
                        <li>
                            <span class="lp-keypoint__icon" aria-hidden="true">
                                <svg class="lp-icon lp-icon--sm"><use href="#lp-icon-clock"></use></svg>
                            </span>
                            <span>Carga horária de {{ $curso->horas_completo ?? '--' }} horas com aplicação prática.</span>
                        </li>
                        <li>
                            <span class="lp-keypoint__icon" aria-hidden="true">
                                <svg class="lp-icon lp-icon--sm"><use href="#lp-icon-certificate"></use></svg>
                            </span>
                            <span>Certificado reconhecido e válido em todo o Brasil.</span>
                        </li>
                        <li>
                            <span class="lp-keypoint__icon" aria-hidden="true">
                                <svg class="lp-icon lp-icon--sm"><use href="#lp-icon-check-circle"></use></svg>
                            </span>
                            <span>Acesso imediato para assistir de onde e quando quiser.</span>
                        </li>
                    </ul>

                    <div class="lp-price-highlight" data-lp-hero-price>
                        <span class="lp-price-highlight__label" data-lp-hero-label>{{ $heroPriceLabel }}</span>
                        <strong data-lp-hero-value>{{ $heroPriceValue }}</strong>
                        <small data-lp-hero-cash @if(!$heroShowCashLine) hidden @endif>ou {{ $heroCashValue }} à vista</small>
                    </div>

                    <div class="lp-hero__actions">
                        <a
                            href="#planos"
                            class="lp-btn js-anchor-scroll"
                            data-lp-primary-scroll-cta
                        >
                            Quero me inscrever agora
                        </a>
                    </div>

                    <p class="lp-hero__waitlist" data-lp-hero-waitlist hidden>{{ $waitlistCopy }}</p>

                    <div class="lp-proof-inline">
                        <span class="lp-proof-inline__item">
                            <svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-star"></use></svg>
                            <span>{{ $notaAvaliacao }} de avaliação média</span>
                        </span>
                        <span class="lp-proof-inline__item">
                            <svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-users"></use></svg>
                            <span>{{ number_format($numeroAlunos, 0, ',', '.') }} alunos</span>
                        </span>
                    </div>
                </div>

                <div class="lp-hero__media">
                    <img
                        src="{{ asset('/storage/' . $curso->capa_horizontal) }}"
                        alt="Capa do curso {{ $tituloCurso }}"
                        width="1280"
                        height="720"
                        fetchpriority="high"
                    >
                    <div class="lp-media-note lp-media-note--top">Acesso imediato</div>
                    <div class="lp-media-note lp-media-note--bottom">7 dias de garantia</div>
                </div>
            </div>
        </section>

        <section class="lp-strip">
            <div class="lp-shell lp-strip__grid">
                <article>
                    <span class="lp-strip__icon" aria-hidden="true">
                        <svg class="lp-icon"><use href="#lp-icon-users"></use></svg>
                    </span>
                    <strong>{{ number_format($numeroAlunos, 0, ',', '.') }}+</strong>
                    <span>alunos já matriculados</span>
                </article>
                <article>
                    <span class="lp-strip__icon" aria-hidden="true">
                        <svg class="lp-icon"><use href="#lp-icon-star"></use></svg>
                    </span>
                    <strong>{{ $notaAvaliacao }}/5</strong>
                    <span>média de avaliação</span>
                </article>
                <article>
                    <span class="lp-strip__icon" aria-hidden="true">
                        <svg class="lp-icon"><use href="#lp-icon-clock"></use></svg>
                    </span>
                    <strong>{{ $curso->horas_completo ?? '--' }}h</strong>
                    <span>de conteúdo objetivo</span>
                </article>
                <article>
                    <span class="lp-strip__icon" aria-hidden="true">
                        <svg class="lp-icon"><use href="#lp-icon-certificate"></use></svg>
                    </span>
                    <strong>Certificado</strong>
                    <span>reconhecido nacionalmente</span>
                </article>
            </div>
        </section>

        <section id="conteudo" class="lp-section">
            <div class="lp-shell">
                <div class="lp-heading">
                    <h2 class="lp-heading__title">
                        
                        <span>O que você vai aprender</span>
                    </h2>
                    <p>Conteúdo organizado para acelerar sua evolução, com foco em prática e aplicação no mercado.</p>
                </div>

                <div class="lp-modules-accordion">
                    @forelse($topicosDestaque as $indice => $topico)
                        <details class="lp-module-item">
                            <summary class="lp-module-item__summary">
                                <span class="lp-module-item__index">Módulo {{ $indice + 1 }}</span>
                                <span class="lp-module-item__title-row">
                                    <span class="lp-module-item__title-wrap">
                                        <svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-book"></use></svg>
                                        <span class="lp-module-item__title">{{ $topico['title'] ?? 'Conteúdo do módulo' }}</span>
                                    </span>
                                    <svg class="lp-icon lp-icon--sm lp-module-item__chevron" aria-hidden="true"><use href="#lp-icon-chevron-down"></use></svg>
                                </span>
                            </summary>
                            @if(!empty($topico['topics']) && is_array($topico['topics']))
                                <div class="lp-module-item__body">
                                    <ul>
                                        @foreach(array_slice($topico['topics'], 0, 4) as $itemTopico)
                                            <li>{{ $itemTopico }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </details>
                    @empty
                        <article class="lp-module-empty">
                            <h3>
                                <svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-book"></use></svg>
                                <span>Conteúdo completo disponível após matrícula</span>
                            </h3>
                            <p>Você terá acesso às aulas, materiais e exercícios do curso imediatamente após a confirmação.</p>
                        </article>
                    @endforelse
                </div>

                @if(!empty($areasAtuacao))
                    <div class="lp-areas-block">
                        <h3 class="lp-areas__title">
                            <svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-briefcase"></use></svg>
                            <span>Áreas de atuação</span>
                        </h3>
                        <ul class="lp-areas__list">
                            @foreach($areasAtuacao as $area)
                                <li class="lp-areas__item">
                                    <svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-check-circle"></use></svg>
                                    <span>{{ $area }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </section>

        <section id="bonus" class="lp-section lp-section--muted">
            <div class="lp-shell">
                <div class="lp-heading">
                    <h2 class="lp-heading__title">
                        
                        <span>Bônus e diferenciais</span>
                    </h2>
                    <p>Além do conteúdo principal, você recebe recursos extras para acelerar sua entrada no mercado.</p>
                </div>

                <div class="lp-bonus-grid">
                    <article class="lp-bonus-card">
                        <h3>
                            <svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-briefcase"></use></svg>
                            <span>Carta de estágio</span>
                        </h3>
                        <p>Use para comprovar seu preparo e fortalecer sua candidatura em oportunidades reais.</p>
                    </article>
                    <article class="lp-bonus-card">
                        <h3>
                            <svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-rocket"></use></svg>
                            <span>Preparatório Jovem Aprendiz</span>
                        </h3>
                        <p>Treinamento complementar para aumentar sua performance em processos seletivos.</p>
                    </article>
                    <article class="lp-bonus-card">
                        <h3>
                            <svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-certificate"></use></svg>
                            <span>Certificado com validação</span>
                        </h3>
                        <p>Documento com autenticidade digital para reforçar seu currículo.</p>
                    </article>
                </div>

                @if(!empty($bonusTitulos))
                    <div class="lp-bonus-extra">
                        <h3>
                            <svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-gift"></use></svg>
                            <span>Bônus adicionais incluídos neste curso</span>
                        </h3>
                        <ul>
                            @foreach($bonusTitulos as $tituloBonus)
                                <li>{{ $tituloBonus }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </section>

        <section id="planos" class="lp-section lp-section--dark">
            <div class="lp-shell">
                <div class="lp-heading lp-heading--light">
                    <h2 class="lp-heading__title">
                        <span data-lp-pricing-title>Escolha seu plano de acesso</span>
                    </h2>
                    <p data-lp-pricing-copy>Oferta ativa por tempo limitado. Garanta o valor promocional enquanto as vagas estão abertas.</p>
                </div>

                @if(!empty($countdownConfig['enabled']))
                    <div class="lp-countdown lp-countdown--plans" data-lp-countdown-block="plans">
                        <span class="lp-countdown__label">Oferta encerra em</span>
                        <strong class="lp-countdown__timer" data-lp-countdown-timer>{{ sprintf('%02d:00', (int) ($countdownConfig['minutes'] ?? 0)) }}</strong>
                    </div>
                @endif

                <div
                    class="lp-pricing {{ $mostrarPlanoSecundario ? 'lp-pricing--two' : 'lp-pricing--one' }}"
                    id="lp-pricing-grid"
                    data-lp-pricing-layout="{{ $mostrarPlanoSecundario ? 'two' : 'one' }}"
                >
                    @if($mostrarPlanoSecundario)
                        <article class="lp-price-card lp-price-card--secondary" data-lp-plan-card="basico">
                            <p class="lp-price-card__tag">
                                <svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-tag"></use></svg>
                                <span>Plano Básico</span>
                            </p>
                            <h3>Acesso Essencial</h3>
                            <p class="lp-price-card__value" data-lp-plan-value="basico">{{ $exibicaoPrecoBasico }}</p>
                            <p class="lp-price-card__cash" data-lp-plan-cash="basico" @if($ocultarParceladoBasico) hidden @endif>ou {{ $curso->preco_cheio_basico ?? 'consulte' }} à vista</p>

                            <ul>
                                <li>Acesso ao curso completo</li>
                                <li>Certificado digital</li>
                                <li class="is-off">Carta de estágio</li>
                                <li class="is-off">Preparatório Jovem Aprendiz</li>
                                @foreach($bonusTitulos as $tituloBonus)
                                    <li class="is-off">{{ $tituloBonus }}</li>
                                @endforeach
                            </ul>

                            <a
                                href="{{ $checkoutBasicoPlano }}"
                                class="lp-btn lp-btn--outline js-cta"
                                data-checkout-url="{{ $checkoutBasicoPlano }}"
                                data-plan="basico"
                                data-requires-lead="{{ !empty($curso->formulario) ? '1' : '0' }}"
                                data-lp-plan-cta="basico"
                            >
                                Quero o plano básico
                            </a>

                            <p class="lp-price-card__ended" data-lp-plan-ended="basico" hidden>Encerrado</p>
                        </article>
                    @endif

                    <article class="lp-price-card lp-price-card--primary" data-lp-plan-card="completo">
                        <p class="lp-price-card__tag">
                            <svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-tag"></use></svg>
                            <span>Plano Completo</span>
                        </p>
                        <h3>Mais recomendado</h3>
                        <p class="lp-price-card__value" data-lp-plan-value="completo">{{ $exibicaoPrecoCompleto }}</p>
                        <p class="lp-price-card__cash" data-lp-plan-cash="completo" @if($ocultarParceladoCompleto) hidden @endif>ou {{ $curso->preco_cheio_completo ?? 'consulte' }} à vista</p>

                        <ul>
                            <li>Acesso ao curso completo</li>
                            <li>Certificado digital</li>
                            <li>Carta de estágio</li>
                            <li>Preparatório Jovem Aprendiz</li>
                            @foreach($bonusTitulos as $tituloBonus)
                                <li>{{ $tituloBonus }}</li>
                            @endforeach
                        </ul>

                        <a
                            href="{{ $checkoutCompletoPlano }}"
                            class="lp-btn js-cta"
                            data-checkout-url="{{ $checkoutCompletoPlano }}"
                            data-plan="completo"
                            data-requires-lead="{{ !empty($curso->formulario) ? '1' : '0' }}"
                            data-lp-plan-cta="completo"
                        >
                            Quero o plano completo
                        </a>

                        <p class="lp-price-card__ended" data-lp-plan-ended="completo" hidden>Encerrado</p>

                        <p class="lp-guarantee">
                            <svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-shield"></use></svg>
                            <span>Garantia incondicional de 7 dias.</span>
                        </p>
                    </article>
                </div>

                <div class="lp-waitlist-card" data-lp-waitlist-block hidden>
                    <p class="lp-waitlist-card__tag">Lista de espera</p>
                    <h3>Inscrições encerradas</h3>
                    <p>{{ $waitlistCopy }}</p>
                    <a
                        href="{{ $waitlistWhatsappUrl }}"
                        class="lp-btn"
                        data-lp-waitlist-cta
                        target="_blank"
                        rel="noopener noreferrer"
                        @if($waitlistWhatsappUrl === '#') hidden @endif
                    >
                        Entrar na lista de espera
                    </a>
                </div>
            </div>
        </section>

        <section class="lp-section">
            <div class="lp-shell lp-certificate">
                <div>
                    <div class="lp-heading">
                        <h2 class="lp-heading__title">
                            
                            <span>Certificação para fortalecer seu currículo</span>
                        </h2>
                    </div>
                    <p>Seu certificado de conclusão é reconhecido e pode ser validado digitalmente, trazendo mais confiança para recrutadores.</p>
                    <ul>
                        <li>
                            <svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-check-circle"></use></svg>
                            <span>Validade nacional.</span>
                        </li>
                        <li>
                            <svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-check-circle"></use></svg>
                            <span>Comprovação de horas e conteúdo estudado.</span>
                        </li>
                        <li>
                            <svg class="lp-icon lp-icon--sm" aria-hidden="true"><use href="#lp-icon-check-circle"></use></svg>
                            <span>Documento ideal para processos seletivos.</span>
                        </li>
                    </ul>
                </div>
                <img
                    src="{{ asset('img/home_page/certificadoNovo2.webp') }}"
                    alt="Exemplo de certificado do curso"
                    width="728"
                    height="515"
                    loading="lazy"
                >
            </div>
        </section>

        <section id="depoimentos" class="lp-section lp-section--muted">
            <div class="lp-shell">
                <div class="lp-heading">
                    <h2>Depoimentos de alunos</h2>
                    <p>Veja relatos reais de quem aplicou o curso e conquistou novas oportunidades.</p>
                </div>

                <div class="lp-testimonials" aria-label="Depoimentos em vídeo">
                    @foreach($depoimentosVideos as $indiceDepoimento => $depoimentoVideoId)
                        <article class="lp-testimonial" data-video-id="{{ $depoimentoVideoId }}" data-loaded="0">
                            <button
                                type="button"
                                class="lp-testimonial__trigger js-testimonial-trigger"
                                data-video-id="{{ $depoimentoVideoId }}"
                                aria-label="Assistir depoimento {{ $indiceDepoimento + 1 }} em vídeo"
                            >
                                <img
                                    src="https://img.youtube.com/vi/{{ $depoimentoVideoId }}/hqdefault.jpg"
                                    alt="Depoimento de aluno do curso {{ $tituloCurso }}"
                                    width="480"
                                    height="270"
                                    loading="lazy"
                                    decoding="async"
                            >
                                <span class="lp-testimonial__play" aria-hidden="true">
                                    <span class="lp-testimonial__play-icon">▶</span>
                                </span>
                            </button>
                            <noscript>
                                <a
                                    href="https://www.youtube.com/watch?v={{ $depoimentoVideoId }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="lp-testimonial__fallback"
                                >
                                    Assistir depoimento no YouTube
                                </a>
                            </noscript>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="faq" class="lp-section lp-section--muted">
            <div class="lp-shell">
                <div class="lp-heading">
                    <h2 class="lp-heading__title">
                        <span>Perguntas frequentes</span>
                    </h2>
                </div>
                <div class="lp-faq">
                    <details>
                        <summary>Quando tenho acesso ao curso?</summary>
                        <p>O acesso é liberado logo após a confirmação da inscrição.</p>
                    </details>
                    <details>
                        <summary>Por quanto tempo posso assistir às aulas?</summary>
                        <p>Você tem acesso ao conteúdo conforme as regras de acesso vigentes na plataforma após a matrícula.</p>
                    </details>
                    <details>
                        <summary>O certificado é válido?</summary>
                        <p>Sim. O certificado é emitido digitalmente e reconhecido em todo o território nacional.</p>
                    </details>
                    <details>
                        <summary>Preciso ter experiência prévia?</summary>
                        <p>Não. O conteúdo foi estruturado para quem está começando e para quem busca atualização prática.</p>
                    </details>
                    <details>
                        <summary>E se eu não gostar?</summary>
                        <p>Você tem 7 dias de garantia para solicitar o reembolso conforme a política da plataforma de pagamento.</p>
                    </details>
                </div>
            </div>
        </section>

        <section class="lp-final-cta">
            <div class="lp-shell lp-final-cta__inner">
                <div>
                    <h2>Pronto para acelerar sua carreira?</h2>
                    <p>Comece hoje com acesso imediato ao conteúdo e suporte para avançar com segurança.</p>
                </div>
                <a
                    href="#planos"
                    class="lp-btn js-anchor-scroll"
                    data-lp-primary-scroll-cta
                >
                    Quero garantir minha vaga agora
                </a>
            </div>
        </section>
    </main>

    <footer class="lp-footer">
        <div class="lp-shell lp-footer__inner">
            <img src="{{ $logoDarkUrl }}" alt="{{ $empresaNome }}" width="150" height="34" loading="lazy">
            <p>© {{ date('Y') }} {{ $empresaNome }}. Todos os direitos reservados.</p>
        </div>
    </footer>

    <div id="lead-modal" class="lp-modal" aria-hidden="true">
        <div class="lp-modal__backdrop" data-close-modal></div>
        <div class="lp-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="lead-modal-title">
            <button type="button" class="lp-modal__close" data-close-modal aria-label="Fechar modal">×</button>
            <h2 id="lead-modal-title">Preencha para continuar</h2>
            <p class="lp-modal__subtitle">Informe seu nome, e-mail e WhatsApp para seguir para o checkout com segurança.</p>

            <form id="lead-form" action="{{ route('lead_whatsapp') }}" method="POST" novalidate>
                @csrf
                <label for="lead_nome">Nome completo</label>
                <input id="lead_nome" name="nome" type="text" autocomplete="name" required>

                <label for="lead_email">E-mail</label>
                <input id="lead_email" name="email" type="email" autocomplete="email" required>

                <label for="lead_telefone">WhatsApp</label>
                <input id="lead_telefone" name="telefone" type="tel" autocomplete="tel" inputmode="numeric" minlength="13" required>

                <input type="hidden" id="lead_user_id" name="user_id" value="{{ $curso->user_id ?? 13 }}">
                <input type="hidden" id="lead_curso_id" name="curso_id" value="{{ $curso->id ?? '' }}">
                <input type="hidden" id="lead_origem" name="origem" value="{{ $curso->origem ?? 'checkout_completo' }}">
                <input type="hidden" id="lead_whatsapp_atendimento_id" name="whatsapp_atendimento_id" value="{{ $curso->whatsapp_atendimento_id ?? '' }}">
                <input type="hidden" id="lead_cidade" name="cidade" value="{{ $curso->cidade ?? '' }}">
                <input type="hidden" id="selected_checkout_url" value="">

                <p id="lead-form-error" class="lp-form-message lp-form-message--error" hidden></p>

                <button id="lead-submit" type="submit" class="lp-btn lp-btn--full">Continuar para o pagamento</button>
            </form>
        </div>
    </div>

    <script id="lp-course-config" type="application/json">@json($trackingConfig)</script>
    <script defer src="{{ asset('js/lp-course.js') }}{{ $lpJsVersion ? '?v=' . $lpJsVersion : '' }}"></script>

    @foreach($pixelIds as $pixelId)
        <noscript>
            <img
                height="1"
                width="1"
                style="display:none"
                src="https://www.facebook.com/tr?id={{ $pixelId }}&ev=PageView&noscript=1"
                alt=""
            >
        </noscript>
    @endforeach
</body>
</html>
