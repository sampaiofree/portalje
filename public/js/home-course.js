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

    document.addEventListener('DOMContentLoaded', function () {
        var config = parseHomeConfig();
        var whatsappButton = document.getElementById('whatsapp_botao');

        document.documentElement.classList.add('home-course-ready');

        document.addEventListener('click', function (event) {
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
