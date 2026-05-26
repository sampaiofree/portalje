(function () {
    'use strict';

    var configEl = document.getElementById('lp-course-config');
    if (!configEl) {
        return;
    }

    var config = {};
    try {
        config = JSON.parse(configEl.textContent || '{}');
    } catch (error) {
        config = {};
    }

    var query = new URLSearchParams(window.location.search);
    var leadModal = document.getElementById('lead-modal');
    var leadForm = document.getElementById('lead-form');
    var leadSubmit = document.getElementById('lead-submit');
    var leadError = document.getElementById('lead-form-error');
    var leadEmail = document.getElementById('lead_email');
    var leadPhone = document.getElementById('lead_telefone');
    var selectedCheckoutInput = document.getElementById('selected_checkout_url');
    var testimonialsSection = document.getElementById('depoimentos');
    var testimonialsContainer = testimonialsSection ? testimonialsSection.querySelector('.lp-testimonials') : null;
    var heroBadgeEl = document.querySelector('[data-lp-hero-badge]');
    var heroPriceBlock = document.querySelector('[data-lp-hero-price]');
    var heroLabelEl = document.querySelector('[data-lp-hero-label]');
    var heroValueEl = document.querySelector('[data-lp-hero-value]');
    var heroCashEl = document.querySelector('[data-lp-hero-cash]');
    var heroWaitlistEl = document.querySelector('[data-lp-hero-waitlist]');
    var pricingTitleEl = document.querySelector('[data-lp-pricing-title]');
    var pricingCopyEl = document.querySelector('[data-lp-pricing-copy]');
    var pricingGrid = document.getElementById('lp-pricing-grid');
    var waitlistBlock = document.querySelector('[data-lp-waitlist-block]');
    var primaryScrollCtas = document.querySelectorAll('[data-lp-primary-scroll-cta]');
    var countdownTimers = document.querySelectorAll('[data-lp-countdown-timer]');
    var countdownBlocks = document.querySelectorAll('[data-lp-countdown-block]');
    var whatsappButton = document.getElementById('whatsapp_botao');

    var selectedCheckoutUrl = '';
    var selectedCtaContext = {};
    var isSubmittingLead = false;
    var isRedirectingBack = false;
    var countdownExpiredApplied = false;

    function compactObject(obj) {
        var output = {};
        Object.keys(obj || {}).forEach(function (key) {
            var value = obj[key];
            if (value !== null && value !== undefined && value !== '') {
                output[key] = value;
            }
        });
        return output;
    }

    function normalizeDigits(value) {
        return (value || '').replace(/\D/g, '');
    }

    function maskBrazilPhone(rawDigits) {
        var digits = (rawDigits || '').slice(0, 11);

        if (digits.length <= 2) {
            return digits;
        }

        if (digits.length <= 6) {
            return '(' + digits.slice(0, 2) + ') ' + digits.slice(2);
        }

        if (digits.length <= 10) {
            return '(' + digits.slice(0, 2) + ') ' + digits.slice(2, 6) + '-' + digits.slice(6);
        }

        return '(' + digits.slice(0, 2) + ') ' + digits.slice(2, 7) + '-' + digits.slice(7);
    }

    function showLeadError(message) {
        if (!leadError) {
            return;
        }

        if (!message) {
            leadError.hidden = true;
            leadError.textContent = '';
            return;
        }

        leadError.hidden = false;
        leadError.textContent = message;
    }

    function decodeHtmlEntities(value) {
        if (!value) {
            return '';
        }
        var textarea = document.createElement('textarea');
        textarea.innerHTML = value;
        return textarea.value;
    }

    function extractCheckoutContext(url) {
        var context = {
            sck: '',
            offDiscount: ''
        };

        if (!url || url === '#') {
            return context;
        }

        try {
            var parsed = new URL(decodeHtmlEntities(url), window.location.origin);
            context.sck = parsed.searchParams.get('sck') || '';
            context.offDiscount = parsed.searchParams.get('offDiscount') || '';
        } catch (error) {
            return context;
        }

        return context;
    }

    function buildPayload(extra) {
        var payload = {
            content_ids: config.course_id ? [String(config.course_id)] : undefined,
            content_type: 'product',
            content_name: config.course_title || undefined,
            ref: query.get('ref') || config.ref || undefined,
            src: query.get('src') || undefined,
            modo_precos: config.modo_precos || undefined
        };

        return compactObject(Object.assign(payload, extra || {}));
    }

    function ensureMetaPixel(pixelIds) {
        if (!Array.isArray(pixelIds) || !pixelIds.length) {
            return;
        }

        if (!window.fbq) {
            (function (f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function () {
                    n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = true;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = true;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s);
            })(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
        }

        pixelIds.forEach(function (pixelId) {
            if (pixelId) {
                window.fbq('init', String(pixelId));
            }
        });

        window.fbq('track', 'PageView');
    }

    function trackMeta(eventName, payload) {
        if (typeof window.fbq !== 'function') {
            return;
        }

        try {
            window.fbq('track', eventName, payload || {});
        } catch (error) {
            // no-op
        }
    }

    function openLeadModal(checkoutUrl, ctaContext) {
        if (!leadModal) {
            window.location.href = checkoutUrl;
            return;
        }

        selectedCheckoutUrl = checkoutUrl;
        selectedCtaContext = ctaContext || {};

        if (selectedCheckoutInput) {
            selectedCheckoutInput.value = checkoutUrl;
        }

        showLeadError('');
        leadModal.classList.add('is-open');
        leadModal.setAttribute('aria-hidden', 'false');

        var leadName = document.getElementById('lead_nome');
        if (leadName) {
            leadName.focus();
        }
    }

    function closeLeadModal() {
        if (!leadModal) {
            return;
        }

        leadModal.classList.remove('is-open');
        leadModal.setAttribute('aria-hidden', 'true');
    }

    function prepareCheckoutUrl(baseUrl, buyerName, buyerEmail, buyerDigits) {
        var checkoutUrl = decodeHtmlEntities(baseUrl || '').replace('{nome}', encodeURIComponent(buyerName));
        if (!checkoutUrl || checkoutUrl === '#') {
            return window.location.href;
        }

        try {
            var parsed = new URL(checkoutUrl, window.location.origin);

            parsed.searchParams.set('name', buyerName);
            parsed.searchParams.set('email', buyerEmail);
            parsed.searchParams.set('phoneac', buyerDigits);
            parsed.searchParams.delete('phonenumber');

            return parsed.toString();
        } catch (error) {
            return checkoutUrl;
        }
    }

    function getCardElementsFromCard(card) {
        if (!card) {
            return {
                card: null,
                value: null,
                cash: null,
                cta: null,
                ended: null
            };
        }

        return {
            card: card,
            value: card.querySelector('.lp-price-card__value'),
            cash: card.querySelector('.lp-price-card__cash'),
            cta: card.querySelector('.js-cta'),
            ended: card.querySelector('.lp-price-card__ended')
        };
    }

    function getPlanElements(plan) {
        var card = document.querySelector(
            '[data-lp-plan-card="' + plan + '"]:not([data-lp-generated-card="1"])'
        );

        if (!card) {
            card = document.querySelector('[data-lp-plan-card="' + plan + '"]');
        }

        return getCardElementsFromCard(card);
    }

    function getVisibleStaticPlans() {
        return ['basico', 'completo'].filter(function (plan) {
            var elements = getPlanElements(plan);
            return !!(elements.card && !elements.card.hidden);
        });
    }

    function registerCtaHandler(button) {
        if (!button || button.dataset.lpCtaBound === '1') {
            return;
        }

        button.addEventListener('click', onCtaClick);
        button.dataset.lpCtaBound = '1';
    }

    function clearGeneratedOfferCards() {
        if (!pricingGrid) {
            return;
        }

        pricingGrid.querySelectorAll('[data-lp-generated-card="1"]').forEach(function (card) {
            card.remove();
        });
    }

    function setCardPrimaryState(card, isPrimary) {
        if (!card) {
            return;
        }

        var elements = getCardElementsFromCard(card);

        card.classList.toggle('lp-price-card--primary', !!isPrimary);
        card.classList.toggle('lp-price-card--secondary', !isPrimary);

        if (elements.cta) {
            elements.cta.classList.toggle('lp-btn--outline', !isPrimary);
        }
    }

    function enableCard(card) {
        var elements = getCardElementsFromCard(card);
        if (!elements.card) {
            return;
        }

        elements.card.classList.remove('is-disabled');

        if (elements.ended) {
            elements.ended.hidden = true;
        }

        if (elements.cta) {
            elements.cta.removeAttribute('aria-disabled');
            elements.cta.classList.remove('is-disabled');
            elements.cta.tabIndex = 0;
        }
    }

    function disableCard(card, endLabel) {
        var elements = getCardElementsFromCard(card);
        if (!elements.card) {
            return;
        }

        elements.card.classList.add('is-disabled');

        if (elements.ended) {
            elements.ended.hidden = false;
            elements.ended.textContent = endLabel || 'Encerrado';
        }

        if (elements.cta) {
            elements.cta.setAttribute('aria-disabled', 'true');
            elements.cta.classList.add('is-disabled');
            elements.cta.setAttribute('href', '#');
            elements.cta.setAttribute('data-checkout-url', '#');
            elements.cta.tabIndex = -1;
        }
    }

    function applyOfferToCard(card, plan, offer) {
        if (!offer) {
            return;
        }

        var elements = getCardElementsFromCard(card);
        if (!elements.card) {
            return;
        }

        if (elements.value) {
            elements.value.textContent = offer.price_value || '';
        }

        if (elements.cash) {
            if (offer.show_cash_line) {
                elements.cash.hidden = false;
                elements.cash.textContent = 'ou ' + (offer.cash_value || 'consulte') + ' à vista';
            } else {
                elements.cash.hidden = true;
                elements.cash.textContent = '';
            }
        }

        if (elements.cta) {
            var checkoutUrl = withSck(
                offer.checkout_url || '#',
                plan === 'basico' ? 'plano_basico' : 'plano_completo'
            );

            elements.cta.textContent = offer.cta_label || elements.cta.textContent;
            elements.cta.setAttribute('href', checkoutUrl);
            elements.cta.setAttribute('data-checkout-url', checkoutUrl);
            elements.cta.setAttribute('data-plan', plan);
            elements.cta.setAttribute('data-requires-lead', offer.requires_lead ? '1' : '0');
            registerCtaHandler(elements.cta);
        }

        enableCard(elements.card);
    }

    function createGeneratedOfferCard(plan, offer) {
        if (!pricingGrid) {
            return null;
        }

        var sourceElements = getPlanElements(plan);
        if (!sourceElements.card) {
            return null;
        }

        var generatedCard = sourceElements.card.cloneNode(true);
        generatedCard.hidden = false;
        generatedCard.dataset.lpGeneratedCard = '1';
        generatedCard.classList.add('lp-price-card--generated');

        applyOfferToCard(generatedCard, plan, offer);
        setCardPrimaryState(generatedCard, true);

        return generatedCard;
    }

    function withSck(url, sck) {
        if (!url || url === '#') {
            return '#';
        }

        try {
            var parsed = new URL(decodeHtmlEntities(url), window.location.origin);
            parsed.searchParams.set('sck', sck);
            return parsed.toString();
        } catch (error) {
            return url;
        }
    }

    function setHeroPricingState(offer) {
        if (!offer) {
            return;
        }

        if (heroPriceBlock) {
            heroPriceBlock.hidden = false;
        }

        if (heroLabelEl) {
            heroLabelEl.textContent = offer.hero_label || '';
        }

        if (heroValueEl) {
            heroValueEl.textContent = offer.price_value || '';
        }

        if (heroCashEl) {
            if (offer.show_cash_line) {
                heroCashEl.hidden = false;
                heroCashEl.textContent = 'ou ' + (offer.cash_value || 'consulte') + ' à vista';
            } else {
                heroCashEl.hidden = true;
                heroCashEl.textContent = '';
            }
        }
    }

    function setElementText(el, text) {
        if (!el) {
            return;
        }

        el.textContent = text;
    }

    function setPrimaryScrollCtaText(text) {
        primaryScrollCtas.forEach(function (ctaEl) {
            setElementText(ctaEl, text);
        });
    }

    function applyWhatsAppWaitlistState() {
        if (heroPriceBlock) {
            heroPriceBlock.hidden = true;
        }

        if (heroBadgeEl) {
            setElementText(heroBadgeEl, 'Lista de espera');
        }

        if (heroWaitlistEl) {
            heroWaitlistEl.hidden = false;
        }

        if (pricingTitleEl) {
            setElementText(pricingTitleEl, 'Lista de espera');
        }

        if (pricingCopyEl) {
            setElementText(
                pricingCopyEl,
                'Entre na lista de espera pelo WhatsApp e avisaremos quando novas vagas forem liberadas.'
            );
        }

        if (pricingGrid) {
            pricingGrid.hidden = true;
        }

        if (waitlistBlock) {
            waitlistBlock.hidden = false;
        }

        setPrimaryScrollCtaText('Entrar na lista de espera');
    }

    function setPricingLayout(layout) {
        if (!pricingGrid) {
            return;
        }

        pricingGrid.setAttribute('data-lp-pricing-layout', layout);
        pricingGrid.classList.toggle('lp-pricing--two', layout === 'two');
        pricingGrid.classList.toggle('lp-pricing--one', layout !== 'two');
    }

    function setPlanVisibility(plan, visible) {
        var elements = getPlanElements(plan);
        if (!elements.card) {
            return;
        }

        elements.card.hidden = !visible;
    }

    function setPlanPrimary(plan, isPrimary) {
        var elements = getPlanElements(plan);
        if (!elements.card) {
            return;
        }

        setCardPrimaryState(elements.card, isPrimary);
    }

    function enablePlan(plan) {
        var elements = getPlanElements(plan);
        if (!elements.card) {
            return;
        }

        enableCard(elements.card);
    }

    function disablePlan(plan, endLabel) {
        var elements = getPlanElements(plan);
        if (!elements.card) {
            return;
        }

        disableCard(elements.card, endLabel);
    }

    function applyOfferToPlan(plan, offer) {
        if (!offer) {
            return;
        }

        var elements = getPlanElements(plan);
        if (!elements.card) {
            return;
        }

        applyOfferToCard(elements.card, plan, offer);
    }

    function formatCountdown(msRemaining) {
        var totalSeconds = Math.max(0, Math.floor(msRemaining / 1000));
        var minutes = Math.floor(totalSeconds / 60);
        var seconds = totalSeconds % 60;

        return String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
    }

    function setCountdownText(text, ended) {
        countdownBlocks.forEach(function (blockEl) {
            var labelEl = blockEl.querySelector('.lp-countdown__label');
            var timerEl = blockEl.querySelector('[data-lp-countdown-timer]');
            var blockType = blockEl.getAttribute('data-lp-countdown-block') || '';

            blockEl.classList.toggle('is-ended', !!ended);

            if (labelEl && !labelEl.dataset.originalText) {
                labelEl.dataset.originalText = labelEl.textContent || '';
            }

            if (ended) {
                blockEl.hidden = true;

                return;
            }

            blockEl.hidden = false;

            if (labelEl) {
                labelEl.textContent = labelEl.dataset.originalText || labelEl.textContent;
            }

            if (timerEl) {
                timerEl.hidden = false;
                timerEl.textContent = text;
            }
        });
    }

    function applyCountdownExpiration() {
        if (countdownExpiredApplied) {
            return;
        }

        countdownExpiredApplied = true;

        var countdownConfig = config.countdown || {};
        var pricingConfig = config.pricing || {};
        var variants = pricingConfig.offer_variants || {};
        var endLabel = countdownConfig.end_label || 'Encerrado';

        setCountdownText(endLabel, true);

        if (countdownConfig.action === 'whatsapp') {
            clearGeneratedOfferCards();
            applyWhatsAppWaitlistState();
            return;
        }

        if (countdownConfig.action === 'encerrar_basico') {
            var currentCompleteKey = pricingConfig.current_complete_offer_key || 'completo_padrao';
            var completeOffer = variants[currentCompleteKey];

            if (completeOffer) {
                applyOfferToPlan('completo', completeOffer);
                setHeroPricingState(completeOffer);
            }

            setPlanVisibility('completo', true);
            setPlanPrimary('completo', true);

            if (pricingConfig.current_basic_offer_key) {
                setPlanVisibility('basico', true);
                setPlanPrimary('basico', false);
                disablePlan('basico', endLabel);
                setPricingLayout('two');
            }

            return;
        }

        if (countdownConfig.action === 'alterar_preco') {
            var destinationKey = countdownConfig.destination_offer || '';
            var destinationOffer = variants[destinationKey];

            if (!destinationOffer) {
                return;
            }

            clearGeneratedOfferCards();

            getVisibleStaticPlans().forEach(function (plan) {
                setPlanVisibility(plan, true);
                setPlanPrimary(plan, false);
                disablePlan(plan, endLabel);
            });

            var generatedCard = createGeneratedOfferCard(destinationOffer.plan, destinationOffer);
            if (!generatedCard) {
                return;
            }

            pricingGrid.appendChild(generatedCard);
            setHeroPricingState(destinationOffer);
            setPricingLayout('two');
        }
    }

    function initCountdown() {
        var countdownConfig = config.countdown || {};

        if (!countdownConfig.enabled || !countdownTimers.length || !countdownConfig.storage_key) {
            return;
        }

        var storageKey = String(countdownConfig.storage_key);
        var endTimestamp = null;

        try {
            endTimestamp = window.localStorage.getItem(storageKey);
        } catch (error) {
            endTimestamp = null;
        }

        var parsedTimestamp = parseInt(endTimestamp || '', 10);
        if (!parsedTimestamp) {
            parsedTimestamp = Date.now() + (Number(countdownConfig.minutes || 0) * 60 * 1000);

            try {
                window.localStorage.setItem(storageKey, String(parsedTimestamp));
            } catch (error) {
                // no-op
            }
        }

        function tick() {
            var remaining = parsedTimestamp - Date.now();

            if (remaining < 1000) {
                applyCountdownExpiration();
                return;
            }

            setCountdownText(formatCountdown(remaining), false);
            window.setTimeout(tick, 1000);
        }

        if (parsedTimestamp <= Date.now()) {
            applyCountdownExpiration();
            return;
        }

        tick();
    }

    function sendLeadPayload(payload) {
        var params = new URLSearchParams(payload);
        var encodedPayload = params.toString();
        var sentByBeacon = false;

        if (navigator.sendBeacon) {
            try {
                var blob = new Blob([encodedPayload], {
                    type: 'application/x-www-form-urlencoded;charset=UTF-8'
                });
                sentByBeacon = navigator.sendBeacon(config.lead_endpoint, blob);
            } catch (error) {
                sentByBeacon = false;
            }
        }

        if (!sentByBeacon) {
            fetch(config.lead_endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                    'X-CSRF-TOKEN': config.csrf_token || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: encodedPayload,
                keepalive: true
            }).catch(function () {
                // no-op
            });
        }
    }

    function getStickyHeaderOffset() {
        var header = document.querySelector('.lp-header');
        if (!header) {
            return 0;
        }

        return header.offsetHeight + 12;
    }

    function smoothScrollToAnchor(anchorElement) {
        var href = anchorElement ? anchorElement.getAttribute('href') : '';
        if (!href || href.charAt(0) !== '#') {
            return false;
        }

        var targetId = href.slice(1);
        if (!targetId) {
            return false;
        }

        var target = document.getElementById(targetId);
        if (!target) {
            return false;
        }

        var offsetTop = target.getBoundingClientRect().top + window.pageYOffset - getStickyHeaderOffset();
        window.scrollTo({
            top: Math.max(0, offsetTop),
            behavior: 'smooth'
        });
        if (window.history && typeof window.history.replaceState === 'function') {
            window.history.replaceState(null, '', '#' + targetId);
        }

        return true;
    }

    function setupBackRedirect() {
        var backRedirectUrl = typeof config.back_redirect_url === 'string'
            ? config.back_redirect_url.trim()
            : '';

        if (!backRedirectUrl) {
            return;
        }

        if (!window.history || typeof window.history.pushState !== 'function') {
            return;
        }

        var currentUrl = window.location.href;
        var guardUrl = currentUrl;

        try {
            var parsedCurrentUrl = new URL(currentUrl, window.location.origin);
            parsedCurrentUrl.searchParams.set('_lpbr', String(Date.now()));
            guardUrl = parsedCurrentUrl.toString();
        } catch (error) {
            guardUrl = currentUrl;
        }

        try {
            if (typeof window.history.replaceState === 'function') {
                window.history.replaceState({ lp_back_base: true }, '', currentUrl);
            }

            window.history.pushState({ lp_back_redirect: true, at: Date.now() }, '', guardUrl);

            if (guardUrl !== currentUrl && typeof window.history.replaceState === 'function') {
                window.history.replaceState({ lp_back_redirect: true, at: Date.now() }, '', currentUrl);
            }
        } catch (error) {
            return;
        }

        window.addEventListener('popstate', function () {
            if (isRedirectingBack) {
                return;
            }

            isRedirectingBack = true;
            window.location.replace(backRedirectUrl);
        });
    }

    function syncTestimonialsVisibility() {
        if (!testimonialsSection || !testimonialsContainer) {
            return;
        }

        testimonialsSection.hidden = testimonialsContainer.querySelectorAll('.lp-testimonial').length === 0;
    }

    function removeTestimonialCard(card) {
        if (!card || !card.parentNode) {
            return;
        }

        card.parentNode.removeChild(card);
        syncTestimonialsVisibility();
    }

    function buildYouTubeEmbedUrl(videoId) {
        var params = new URLSearchParams({
            autoplay: '1',
            rel: '0',
            modestbranding: '1',
            playsinline: '1'
        });

        return 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(videoId) + '?' + params.toString();
    }

    function loadTestimonialVideo(triggerButton) {
        if (!triggerButton) {
            return;
        }

        var card = triggerButton.closest('.lp-testimonial');
        if (!card || card.getAttribute('data-loaded') === '1' || card.getAttribute('data-loading') === '1') {
            return;
        }

        var videoId = card.getAttribute('data-video-id') || triggerButton.getAttribute('data-video-id');
        if (!videoId) {
            removeTestimonialCard(card);
            return;
        }

        card.setAttribute('data-loading', '1');

        var player = document.createElement('div');
        player.className = 'lp-testimonial__player';

        var iframe = document.createElement('iframe');
        iframe.src = buildYouTubeEmbedUrl(videoId);
        iframe.title = 'Depoimento de aluno';
        iframe.loading = 'eager';
        iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
        iframe.setAttribute('allowfullscreen', '');

        var didResolve = false;
        var failTimeout = window.setTimeout(function () {
            if (didResolve) {
                return;
            }
            didResolve = true;
            removeTestimonialCard(card);
        }, 9000);

        iframe.addEventListener('load', function () {
            if (didResolve) {
                return;
            }
            didResolve = true;
            window.clearTimeout(failTimeout);
            card.setAttribute('data-loaded', '1');
            card.removeAttribute('data-loading');
        }, { once: true });

        iframe.addEventListener('error', function () {
            if (didResolve) {
                return;
            }
            didResolve = true;
            window.clearTimeout(failTimeout);
            removeTestimonialCard(card);
        }, { once: true });

        player.appendChild(iframe);
        triggerButton.replaceWith(player);
    }

    function onCtaClick(event) {
        var button = event.currentTarget;

        if (
            !button ||
            button.getAttribute('aria-disabled') === 'true' ||
            button.classList.contains('is-disabled')
        ) {
            event.preventDefault();
            return;
        }

        var checkoutUrl = button.getAttribute('data-checkout-url') || button.getAttribute('href') || '#';
        var checkoutContext = extractCheckoutContext(checkoutUrl);
        var plan = button.getAttribute('data-plan') || 'completo';

        var eventPayload = buildPayload({
            sck: checkoutContext.sck || undefined,
            offDiscount: checkoutContext.offDiscount || undefined,
            plan: plan
        });

        trackMeta('InitiateCheckout', eventPayload);
        trackMeta('AddToCart', eventPayload);

        if (button.getAttribute('data-requires-lead') === '1') {
            event.preventDefault();
            openLeadModal(checkoutUrl, {
                sck: checkoutContext.sck || '',
                offDiscount: checkoutContext.offDiscount || '',
                plan: plan
            });
        }
    }

    function onLeadSubmit(event) {
        event.preventDefault();

        if (isSubmittingLead) {
            return;
        }

        var leadName = document.getElementById('lead_nome');
        var nameValue = (leadName ? leadName.value : '').trim();
        var emailValue = (leadEmail ? leadEmail.value : '').trim();
        var phoneDigits = normalizeDigits(leadPhone ? leadPhone.value : '');
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (nameValue.length < 3) {
            showLeadError('Informe seu nome completo para continuar.');
            return;
        }

        if (!emailRegex.test(emailValue)) {
            showLeadError('Informe um e-mail válido para continuar.');
            return;
        }

        if (phoneDigits.length < 10) {
            showLeadError('Informe um WhatsApp válido com DDD.');
            return;
        }

        var leadPayload = buildPayload({
            sck: selectedCtaContext.sck || undefined,
            offDiscount: selectedCtaContext.offDiscount || undefined,
            plan: selectedCtaContext.plan || undefined
        });
        trackMeta('Lead', leadPayload);

        var tokenField = leadForm.querySelector('input[name="_token"]');
        var payload = {
            _token: tokenField ? tokenField.value : (config.csrf_token || ''),
            nome: nameValue,
            email: emailValue,
            telefone: phoneDigits,
            user_id: document.getElementById('lead_user_id') ? document.getElementById('lead_user_id').value : '',
            curso_id: document.getElementById('lead_curso_id') ? document.getElementById('lead_curso_id').value : '',
            origem: document.getElementById('lead_origem') ? document.getElementById('lead_origem').value : '',
            whatsapp_atendimento_id: document.getElementById('lead_whatsapp_atendimento_id') ? document.getElementById('lead_whatsapp_atendimento_id').value : '',
            cidade: document.getElementById('lead_cidade') ? document.getElementById('lead_cidade').value : ''
        };

        isSubmittingLead = true;
        if (leadSubmit) {
            leadSubmit.disabled = true;
            leadSubmit.textContent = 'Redirecionando...';
        }

        sendLeadPayload(payload);

        var checkoutToUse = selectedCheckoutUrl || (selectedCheckoutInput ? selectedCheckoutInput.value : window.location.href);
        var redirectUrl = prepareCheckoutUrl(checkoutToUse, nameValue, emailValue, phoneDigits);

        window.setTimeout(function () {
            window.location.href = redirectUrl;
        }, 140);
    }

    if (leadPhone) {
        leadPhone.addEventListener('input', function () {
            var digits = normalizeDigits(leadPhone.value);
            leadPhone.value = maskBrazilPhone(digits);
        });
    }

    if (leadModal) {
        leadModal.addEventListener('click', function (event) {
            var target = event.target;
            if (target && target.hasAttribute('data-close-modal')) {
                closeLeadModal();
            }
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeLeadModal();
        }
    });

    document.addEventListener('click', function (event) {
        var target = event.target;
        if (!target || typeof target.closest !== 'function') {
            return;
        }

        var anchorButton = target.closest('.js-anchor-scroll[href^="#"]');
        if (anchorButton) {
            if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }
            if (smoothScrollToAnchor(anchorButton)) {
                event.preventDefault();
            }
            return;
        }

        var testimonialButton = target.closest('.js-testimonial-trigger');
        if (testimonialButton) {
            loadTestimonialVideo(testimonialButton);
        }
    });

    document.addEventListener('error', function (event) {
        var target = event.target;
        if (!target || target.tagName !== 'IMG') {
            return;
        }

        if (!target.closest('.lp-testimonial__trigger')) {
            return;
        }

        var card = target.closest('.lp-testimonial');
        removeTestimonialCard(card);
    }, true);

    document.querySelectorAll('.js-cta').forEach(function (button) {
        registerCtaHandler(button);
    });

    if (leadForm) {
        leadForm.addEventListener('submit', onLeadSubmit);
    }

    setupBackRedirect();
    syncTestimonialsVisibility();
    initCountdown();
    if (config.whatsapp_show && whatsappButton) {
        window.setTimeout(function () {
            whatsappButton.style.visibility = 'visible';
        }, Math.max(0, Number(config.whatsapp_delay_seconds || 0) * 1000));
    }
    ensureMetaPixel(config.pixel_ids || []);
    trackMeta('ViewContent', buildPayload());
})();
