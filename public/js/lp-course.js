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
    var leadPhone = document.getElementById('lead_telefone');
    var selectedCheckoutInput = document.getElementById('selected_checkout_url');
    var testimonialsSection = document.getElementById('depoimentos');
    var testimonialsContainer = testimonialsSection ? testimonialsSection.querySelector('.lp-testimonials') : null;

    var selectedCheckoutUrl = '';
    var selectedCtaContext = {};
    var isSubmittingLead = false;

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

    function prepareCheckoutUrl(baseUrl, buyerName, buyerDigits) {
        var checkoutUrl = decodeHtmlEntities(baseUrl || '').replace('{nome}', encodeURIComponent(buyerName));
        if (!checkoutUrl || checkoutUrl === '#') {
            return window.location.href;
        }

        try {
            var parsed = new URL(checkoutUrl, window.location.origin);
            var ddd = buyerDigits.slice(0, 2);
            var phone = buyerDigits.slice(2);

            parsed.searchParams.set('name', buyerName);
            parsed.searchParams.set('phoneac', ddd);
            parsed.searchParams.set('phonenumber', phone);

            return parsed.toString();
        } catch (error) {
            return checkoutUrl;
        }
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
        var phoneDigits = normalizeDigits(leadPhone ? leadPhone.value : '');

        if (nameValue.length < 3) {
            showLeadError('Informe seu nome completo para continuar.');
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
        var redirectUrl = prepareCheckoutUrl(checkoutToUse, nameValue, phoneDigits);

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
        button.addEventListener('click', onCtaClick);
    });

    if (leadForm) {
        leadForm.addEventListener('submit', onLeadSubmit);
    }

    syncTestimonialsVisibility();
    ensureMetaPixel(config.pixel_ids || []);
    trackMeta('ViewContent', buildPayload());
})();
