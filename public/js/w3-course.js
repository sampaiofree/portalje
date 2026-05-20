(function () {
    'use strict';

    var configElement = document.getElementById('w3-course-config');
    var config = {};

    if (configElement) {
        try {
            config = JSON.parse(configElement.textContent || '{}');
        } catch (error) {
            config = {};
        }
    }

    var modal = document.getElementById('modal_lead');
    var leadForm = document.getElementById('modal_form_lead');
    var leadError = document.getElementById('lead_form_error');
    var leadSubmitButton = document.getElementById('lead_submit_button');

    var inputLeadNome = document.getElementById('input_lead_nome');
    var inputLeadTelefone = document.getElementById('input_lead_telefone');
    var inputLeadLink = document.getElementById('input_lead_link');
    var inputLeadCursoId = document.getElementById('input_lead_curso_id');
    var inputLeadUserId = document.getElementById('input_lead_user_id');
    var inputLeadOrigem = document.getElementById('input_lead_origem');
    var inputLeadWhatsappAtendimentoId = document.getElementById('input_lead_whatsapp_atendimento_id');

    var whatsappButton = document.getElementById('whatsapp_botao');
    var testimonialsSection = document.getElementById('alunos');
    var testimonialsContainer = testimonialsSection ? testimonialsSection.querySelector('.w3-testimonials') : null;
    var isSubmittingLead = false;

    function normalizeDigits(value) {
        return (value || '').replace(/\D+/g, '');
    }

    function parseJsonListAttribute(element, attributeName) {
        try {
            var parsed = JSON.parse(element.getAttribute(attributeName) || '[]');
            return Array.isArray(parsed) ? parsed : [];
        } catch (error) {
            return [];
        }
    }

    function parseListTextAttribute(element, attributeName) {
        return parseJsonListAttribute(element, attributeName).join('\n');
    }

    function maskBrazilPhone(value) {
        var digits = normalizeDigits(value).slice(0, 11);

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

    function parsePixelId(value) {
        var id = String(value || '').trim();

        if (!id || id === '0') {
            return null;
        }

        return id;
    }

    function ensureMetaPixel(pixelId) {
        var id = parsePixelId(pixelId);

        if (!id) {
            return;
        }

        if (!window.fbq) {
            !(function (f, b, e, v, n, t, s) {
                if (f.fbq) {
                    return;
                }

                n = f.fbq = function () {
                    n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
                };

                if (!f._fbq) {
                    f._fbq = n;
                }

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

        if (!window.__w3MetaPixelInitialized) {
            window.fbq('init', id);
            window.__w3MetaPixelInitialized = true;
        }
    }

    function trackMeta(eventName, payload) {
        if (typeof window.fbq !== 'function') {
            return;
        }

        if (payload && Object.keys(payload).length > 0) {
            window.fbq('track', eventName, payload);
            return;
        }

        window.fbq('track', eventName);
    }

    function currentQueryWhatsapp() {
        try {
            return normalizeDigits(new URLSearchParams(window.location.search).get('t'));
        } catch (error) {
            return '';
        }
    }

    function resolveWhatsappSelection() {
        var queryWhatsapp = currentQueryWhatsapp();

        if (queryWhatsapp.length > 10 && queryWhatsapp.length <= 15) {
            return {
                whatsapp: queryWhatsapp,
                whatsapp_atendimento_id: ''
            };
        }

        var renderedWhatsapp = normalizeDigits(config.whatsapp_atendimento);
        if (renderedWhatsapp.length <= 10 || renderedWhatsapp.length > 15) {
            return null;
        }

        var renderedSelection = {
            whatsapp: renderedWhatsapp,
            whatsapp_atendimento_id: config.whatsapp_atendimento_id ? String(config.whatsapp_atendimento_id) : ''
        };

        return renderedSelection;
    }

    function withWhatsappPhone(url, whatsapp) {
        try {
            var parsed = new URL(url, window.location.href);
            var host = parsed.hostname.replace(/^www\./, '');

            if (host === 'wa.me') {
                return url.replace(/(https?:\/\/(?:www\.)?wa\.me\/)\d+/i, '$1' + whatsapp);
            }

            if (host === 'api.whatsapp.com' || host === 'web.whatsapp.com') {
                if (/[?&]phone=/.test(url)) {
                    return url.replace(/([?&]phone=)[^&]*/i, '$1' + whatsapp);
                }

                return url + (url.indexOf('?') >= 0 ? '&' : '?') + 'phone=' + whatsapp;
            }

            return url;
        } catch (error) {
            return url;
        }
    }

    function applyWhatsappSelection() {
        var selection = resolveWhatsappSelection();
        if (!selection) {
            return;
        }

        document.querySelectorAll('.js-course-trigger').forEach(function (trigger) {
            var link = trigger.getAttribute('data-link') || '';
            if (link) {
                trigger.setAttribute('data-link', withWhatsappPhone(link, selection.whatsapp));
            }

            trigger.setAttribute('data-whatsapp-atendimento-id', selection.whatsapp_atendimento_id);
        });

        if (whatsappButton) {
            var buttonHref = whatsappButton.getAttribute('href') || '';
            if (buttonHref) {
                whatsappButton.setAttribute('href', withWhatsappPhone(buttonHref, selection.whatsapp));
            }
        }
    }

    applyWhatsappSelection();

    function openLeadModal(button) {
        if (!modal || !button) {
            return;
        }

        inputLeadLink.value = button.getAttribute('data-link') || '';
        inputLeadCursoId.value = button.getAttribute('data-curso') || '';
        inputLeadUserId.value = button.getAttribute('data-user') || '';
        inputLeadOrigem.value = button.getAttribute('data-origem') || 'whatsapp';
        inputLeadWhatsappAtendimentoId.value = button.getAttribute('data-whatsapp-atendimento-id') || '';

        if (leadError) {
            leadError.hidden = true;
            leadError.textContent = '';
        }

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        if (inputLeadNome) {
            inputLeadNome.focus();
        }
    }

    function closeLeadModal() {
        if (!modal) {
            return;
        }

        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    function showLeadError(message) {
        if (!leadError) {
            return;
        }

        leadError.hidden = false;
        leadError.textContent = message;
    }

    function smoothScrollToCursos(anchorElement) {
        if (!anchorElement) {
            return;
        }

        var href = anchorElement.getAttribute('href') || '';

        if (!href.startsWith('#')) {
            return;
        }

        var target = document.querySelector(href);

        if (!target) {
            return;
        }

        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function buildRedirectUrl(baseUrl, nome, telefoneDigits) {
        var ddd = telefoneDigits.substring(0, 2);
        var telefone = telefoneDigits.substring(2);
        var encodedName = encodeURIComponent(nome);

        var finalBase = String(baseUrl || '').replace('{nome}', encodedName);

        if (!finalBase) {
            return '#';
        }

        var separator = finalBase.indexOf('?') >= 0 ? '&' : '?';
        return finalBase + separator + 'name=' + encodedName + '&phoneac=' + encodeURIComponent(ddd) + '&phonenumber=' + encodeURIComponent(telefone);
    }

    function sendLeadPayload(formElement, payload) {
        if (!formElement || !payload) {
            return;
        }

        var sentByBeacon = false;

        if (navigator.sendBeacon) {
            sentByBeacon = navigator.sendBeacon(formElement.action, payload);
        }

        if (sentByBeacon) {
            return;
        }

        fetch(formElement.action, {
            method: 'POST',
            body: payload,
            keepalive: true,
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': config.csrf_token || ''
            }
        }).catch(function () {
            // Ignora erro de rede para não bloquear o redirecionamento.
        });
    }

    function syncTestimonialsVisibility() {
        if (!testimonialsSection || !testimonialsContainer) {
            return;
        }

        testimonialsSection.hidden = testimonialsContainer.querySelectorAll('.w3-testimonial').length === 0;
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

        var card = triggerButton.closest('.w3-testimonial');

        if (!card || card.getAttribute('data-loaded') === '1' || card.getAttribute('data-loading') === '1') {
            return;
        }

        var videoId = triggerButton.getAttribute('data-video-id') || card.getAttribute('data-video-id');

        if (!videoId) {
            removeTestimonialCard(card);
            return;
        }

        card.setAttribute('data-loading', '1');

        var player = document.createElement('div');
        player.className = 'w3-testimonial__player';

        var iframe = document.createElement('iframe');
        iframe.src = buildYouTubeEmbedUrl(videoId);
        iframe.title = 'Depoimento de aluno';
        iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
        iframe.setAttribute('allowfullscreen', '');
        iframe.loading = 'eager';

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

    if (inputLeadTelefone) {
        inputLeadTelefone.addEventListener('input', function () {
            inputLeadTelefone.value = maskBrazilPhone(inputLeadTelefone.value);
        });
    }

    document.addEventListener('click', function (event) {
        var target = event.target;

        if (!target || typeof target.closest !== 'function') {
            return;
        }

        var closeButton = target.closest('[data-close-modal]');
        if (closeButton) {
            event.preventDefault();
            closeLeadModal();
            return;
        }

        var courseButton = target.closest('.js-course-trigger');
        if (courseButton) {
            event.preventDefault();

            var origem = String(courseButton.getAttribute('data-origem') || '').toLowerCase();
            if (origem === 'typebot') {
                trackMeta('Lead', {
                    content_type: 'typebot',
                    content_name: courseButton.getAttribute('data-course-title') || undefined,
                    content_ids: [courseButton.getAttribute('data-curso') || '']
                });

                var typebotOpener = window.PortalJeTypebotCourse;
                if (typebotOpener && typeof typebotOpener.open === 'function') {
                    typebotOpener.open({
                        curso_nome: courseButton.getAttribute('data-typebot-curso-nome') || '',
                        curso_preco: courseButton.getAttribute('data-typebot-curso-preco') || '',
                        curso_checkout: courseButton.getAttribute('data-typebot-checkout-url') || '',
                        whatsapp_atendimento: courseButton.getAttribute('data-typebot-whatsapp-url') || '',
                        curso_imagem: courseButton.getAttribute('data-typebot-curso-imagem') || '',
                        curso_areas: parseListTextAttribute(courseButton, 'data-typebot-curso-areas'),
                        curso_conteudo: parseListTextAttribute(courseButton, 'data-typebot-curso-conteudo'),
                        curso_bonus: parseListTextAttribute(courseButton, 'data-typebot-curso-bonus')
                    });
                    return;
                }

                var typebotFallbackUrl = courseButton.getAttribute('data-link') || '';
                if (typebotFallbackUrl) {
                    window.location.href = typebotFallbackUrl;
                }
                return;
            }

            if (origem === 'curso') {
                trackMeta('ViewContent', {
                    content_type: 'course',
                    content_name: courseButton.getAttribute('data-course-title') || undefined,
                    content_ids: [courseButton.getAttribute('data-curso') || '']
                });

                var targetUrl = courseButton.getAttribute('data-link') || '';
                if (targetUrl) {
                    window.location.href = targetUrl;
                }
                return;
            }

            trackMeta('Lead', {
                content_type: 'whatsapp',
                content_name: courseButton.getAttribute('data-course-title') || undefined,
                content_ids: [courseButton.getAttribute('data-curso') || '']
            });

            if (!Boolean(config.whatsapp_requires_form)) {
                var whatsappUrl = courseButton.getAttribute('data-link') || '';
                if (whatsappUrl) {
                    window.location.href = whatsappUrl;
                }
                return;
            }

            openLeadModal(courseButton);
            return;
        }

        var scrollButton = target.closest('.js-scroll-to-cursos');
        if (scrollButton) {
            if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            event.preventDefault();
            smoothScrollToCursos(scrollButton);
            return;
        }

        var testimonialButton = target.closest('.js-testimonial-trigger');
        if (testimonialButton) {
            event.preventDefault();
            loadTestimonialVideo(testimonialButton);
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeLeadModal();
        }
    });

    document.addEventListener('error', function (event) {
        var target = event.target;
        if (!target || target.tagName !== 'IMG') {
            return;
        }

        if (!target.closest('.w3-testimonial__trigger')) {
            return;
        }

        var card = target.closest('.w3-testimonial');
        removeTestimonialCard(card);
    }, true);

    if (leadForm) {
        leadForm.addEventListener('submit', function (event) {
            event.preventDefault();

            if (isSubmittingLead) {
                return;
            }

            var nome = (inputLeadNome ? inputLeadNome.value : '').trim();
            var telefoneDigits = normalizeDigits(inputLeadTelefone ? inputLeadTelefone.value : '');

            if (nome.length < 3) {
                showLeadError('Informe seu nome completo para continuar.');
                return;
            }

            if (telefoneDigits.length < 10) {
                showLeadError('Digite seu WhatsApp com DDD válido.');
                return;
            }

            if (leadError) {
                leadError.hidden = true;
                leadError.textContent = '';
            }

            isSubmittingLead = true;

            if (leadSubmitButton) {
                leadSubmitButton.disabled = true;
                leadSubmitButton.textContent = 'Redirecionando...';
            }

            var payload = new FormData(leadForm);
            payload.set('nome', nome);
            payload.set('telefone', telefoneDigits);
            payload.set('curso_id', inputLeadCursoId ? inputLeadCursoId.value : '');
            payload.set('user_id', inputLeadUserId ? inputLeadUserId.value : '');
            payload.set('origem', inputLeadOrigem ? inputLeadOrigem.value : 'whatsapp');
            payload.set('whatsapp_atendimento_id', inputLeadWhatsappAtendimentoId ? inputLeadWhatsappAtendimentoId.value : '');

            sendLeadPayload(leadForm, payload);

            var redirectUrl = buildRedirectUrl(inputLeadLink ? inputLeadLink.value : '', nome, telefoneDigits);

            window.setTimeout(function () {
                window.location.href = redirectUrl;
            }, 140);
        });
    }

    if (config.whatsapp_show && whatsappButton) {
        var delayMs = Math.max(0, Number(config.whatsapp_delay_seconds || 0) * 1000);

        window.setTimeout(function () {
            whatsappButton.style.visibility = 'visible';
        }, delayMs);
    }

    syncTestimonialsVisibility();
    ensureMetaPixel(config.pixel_id);
    trackMeta('PageView');
})();
