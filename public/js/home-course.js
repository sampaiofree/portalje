(function () {
    'use strict';

    function parseHomeConfig() {
        var configElement = document.getElementById('home-course-config');

        if (!configElement) {
            return {};
        }

        try {
            return JSON.parse(configElement.textContent || '{}');
        } catch (error) {
            return {};
        }
    }

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

    function getHeaderOffset() {
        var topBanner = document.querySelector('.top-banner');
        return topBanner ? topBanner.offsetHeight + 12 : 12;
    }

    function scrollToAnchor(anchor) {
        if (!anchor || anchor.charAt(0) !== '#') {
            return;
        }

        var target = document.querySelector(anchor);
        if (!target) {
            return;
        }

        var targetY = target.getBoundingClientRect().top + window.scrollY - getHeaderOffset();
        window.scrollTo({
            top: Math.max(targetY, 0),
            behavior: 'smooth'
        });
    }

    function trackMeta(eventName, payload) {
        if (!eventName || typeof window.fbq !== 'function') {
            return;
        }

        try {
            if (payload && Object.keys(payload).length > 0) {
                window.fbq('track', eventName, payload);
                return;
            }

            window.fbq('track', eventName);
        } catch (error) {
            // no-op
        }
    }

    function buildCoursePayload(trigger, eventName) {
        var courseId = trigger ? String(trigger.getAttribute('data-cursoid') || '').trim() : '';
        var courseTitle = trigger ? String(trigger.getAttribute('data-course-title') || '').trim() : '';

        if (eventName === 'Lead') {
            return compactObject({
                content_type: 'whatsapp',
                content_name: courseTitle || undefined,
                content_ids: courseId ? [courseId] : undefined
            });
        }

        return compactObject({
            content_type: 'course',
            content_name: courseTitle || undefined,
            content_ids: courseId ? [courseId] : undefined
        });
    }

    function normalizeWhatsapp(value) {
        var digits = String(value || '').replace(/\D+/g, '');
        return digits.length > 10 && digits.length <= 15 ? digits : '';
    }

    function currentQueryWhatsapp() {
        try {
            return normalizeWhatsapp(new URLSearchParams(window.location.search).get('t'));
        } catch (error) {
            return '';
        }
    }

    function whatsappStorageKey(config) {
        var userId = String(config.user_id || '').trim() || 'public';
        return 'portalje:whatsapp:' + window.location.host + ':' + userId;
    }

    function readStoredWhatsapp(key) {
        try {
            var parsed = JSON.parse(window.sessionStorage.getItem(key) || '{}');
            var whatsapp = normalizeWhatsapp(parsed.whatsapp);

            if (!whatsapp) {
                return null;
            }

            return {
                whatsapp: whatsapp,
                whatsapp_atendimento_id: parsed.whatsapp_atendimento_id ? String(parsed.whatsapp_atendimento_id) : ''
            };
        } catch (error) {
            return null;
        }
    }

    function writeStoredWhatsapp(key, selection) {
        try {
            window.sessionStorage.setItem(key, JSON.stringify(selection));
        } catch (error) {
            // no-op
        }
    }

    function resolveWhatsappSelection(config) {
        var key = whatsappStorageKey(config);
        var queryWhatsapp = currentQueryWhatsapp();

        if (queryWhatsapp) {
            var fixedSelection = {
                whatsapp: queryWhatsapp,
                whatsapp_atendimento_id: ''
            };
            writeStoredWhatsapp(key, fixedSelection);
            return fixedSelection;
        }

        var storedSelection = readStoredWhatsapp(key);
        if (storedSelection) {
            return storedSelection;
        }

        var renderedWhatsapp = normalizeWhatsapp(config.whatsapp_atendimento);
        if (!renderedWhatsapp) {
            return null;
        }

        var renderedSelection = {
            whatsapp: renderedWhatsapp,
            whatsapp_atendimento_id: config.whatsapp_atendimento_id ? String(config.whatsapp_atendimento_id) : ''
        };
        writeStoredWhatsapp(key, renderedSelection);

        return renderedSelection;
    }

    function withWhatsappQuery(url, whatsapp) {
        try {
            var parsed = new URL(url, window.location.href);
            if (parsed.origin !== window.location.origin || !parsed.pathname.match(/^\/whatsapp(?:\/|$)/)) {
                return url;
            }

            parsed.searchParams.set('t', whatsapp);
            return parsed.toString();
        } catch (error) {
            return url;
        }
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

            return withWhatsappQuery(url, whatsapp);
        } catch (error) {
            return url;
        }
    }

    function applyWhatsappSelection(config) {
        var selection = resolveWhatsappSelection(config);
        if (!selection) {
            return;
        }

        document.querySelectorAll('[data-course-trigger="1"]').forEach(function (trigger) {
            var href = trigger.getAttribute('href') || '';
            if (href) {
                trigger.setAttribute('href', withWhatsappPhone(href, selection.whatsapp));
            }

            trigger.setAttribute('data-whatsapp-atendimento-id', selection.whatsapp_atendimento_id);
        });

        var whatsappButton = document.getElementById('whatsapp_botao');
        if (whatsappButton) {
            var buttonHref = whatsappButton.getAttribute('href') || '';
            if (buttonHref) {
                whatsappButton.setAttribute('href', withWhatsappPhone(buttonHref, selection.whatsapp));
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var config = parseHomeConfig();
        var whatsappButton = document.getElementById('whatsapp_botao');

        document.documentElement.classList.add('home-course-ready');
        applyWhatsappSelection(config);

        document.addEventListener('click', function (event) {
            if (!event.target || typeof event.target.closest !== 'function') {
                return;
            }

            var trigger = event.target.closest('a[href^="#"]');
            if (!trigger) {
                return;
            }

            var href = trigger.getAttribute('href');
            if (!href || href === '#') {
                return;
            }

            var target = document.querySelector(href);
            if (!target) {
                return;
            }

            event.preventDefault();
            scrollToAnchor(href);
        });

        document.addEventListener('click', function (event) {
            if (!event.target || typeof event.target.closest !== 'function') {
                return;
            }

            var trigger = event.target.closest('[data-course-trigger="1"]');
            if (!trigger) {
                return;
            }

            var metaEvent = trigger.getAttribute('data-meta-event')
                || (config.home_destination === 'whatsapp' ? 'Lead' : 'ViewContent');

            if (!metaEvent) {
                return;
            }

            trackMeta(metaEvent, buildCoursePayload(trigger, metaEvent));

            if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            var requiresForm = Boolean(config.whatsapp_requires_form);
            var shouldDelayNavigation = metaEvent === 'ViewContent'
                || (metaEvent === 'Lead' && !requiresForm);

            if (!shouldDelayNavigation) {
                return;
            }

            var href = trigger.getAttribute('href');
            if (!href || href === '#') {
                return;
            }

            event.preventDefault();

            window.setTimeout(function () {
                window.location.href = href;
            }, 120);
        });

        if (whatsappButton && Boolean(config.whatsapp_show)) {
            var delaySeconds = Number(config.whatsapp_delay_seconds || 0);

            if (!Number.isFinite(delaySeconds) || delaySeconds < 0) {
                delaySeconds = 0;
            }

            window.setTimeout(function () {
                whatsappButton.style.visibility = 'visible';
            }, delaySeconds * 1000);
        }
    });
})();
