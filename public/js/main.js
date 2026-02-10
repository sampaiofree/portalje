document.addEventListener('DOMContentLoaded', function() {
    
    // --- FUNÇÕES GLOBAIS DE SETUP ---
    function startCountdown() {
        const countdownElements = document.querySelectorAll('.countdown');
        if (countdownElements.length === 0) return;
        let today = new Date();
        today.setHours(23, 59, 59, 999);
        const countDownDate = today.getTime();
        const countdownFunction = setInterval(function() {
            const now = new Date().getTime();
            const distance = countDownDate - now;
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            countdownElements.forEach(element => { element.innerHTML = `${hours}h ${minutes}m ${seconds}s`; });
            if (distance < 0) {
                clearInterval(countdownFunction);
                countdownElements.forEach(element => { element.innerHTML = "PROMOÇÃO ENCERRADA"; });
            }
        }, 1000);
    }

    function setupLazyLoading() {
        const lazyElements = document.querySelectorAll(".lazy-load");
        if (!("IntersectionObserver" in window)) {
            lazyElements.forEach(el => el.classList.add("visible"));
            return;
        }
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("visible");
                    const imgs = entry.target.querySelectorAll("img.lazy-img");
                    imgs.forEach(img => { if (img.dataset.src) { img.src = img.dataset.src; img.removeAttribute("data-src"); } });
                    observer.unobserve(entry.target);
                }
            });
        }, { rootMargin: "0px 0px -100px 0px" });
        lazyElements.forEach((div) => observer.observe(div));
    }
    
    function setupInscricaoModal() {
        const inscricaoModalEl = document.getElementById('inscricaoModal');
        if (!inscricaoModalEl || typeof bootstrap === 'undefined') return;
        const links = document.querySelectorAll('.btn-unlock, .btn-primary, .btn-inscricao');
        const modal = new bootstrap.Modal(inscricaoModalEl);
        links.forEach(link => {
            if (link.hasAttribute('data-bs-toggle') && link.getAttribute('data-bs-target') === '#inscricaoModal') {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    document.getElementById('input_lead_curso_id').value = link.getAttribute('data-cursoid') || '';
                    document.getElementById('input_lead_href').value = link.getAttribute('href');
                    modal.show();
                });
            }
        });
    }

    function setupFormSubmission() {
        const form = document.getElementById("inscricaoForm");
        if (!form) return;
        form.addEventListener("submit", function(e) {
            e.preventDefault();
            this.querySelector('button[type="submit"]').disabled = true;
            const formData = new FormData(this);
            const nome = document.getElementById("input_lead_nome").value;
            let redirectLink = document.getElementById("input_lead_href").value;
            if (redirectLink.includes('{nome}')) { redirectLink = redirectLink.replace("{nome}", encodeURIComponent(nome)); }
            if (typeof fbq === 'function') { fbq('track', 'Lead'); }
            fetch(this.action, { method: "POST", body: formData })
                .catch(error => console.error('Erro ao enviar o formulário:', error))
                .finally(() => { window.location.href = redirectLink; });
        });
    }

    function setupPhoneMask() {
        if (typeof $ !== 'undefined' && typeof $.fn.inputmask !== 'undefined') {
            $('.input_telefone').inputmask({ mask: ["(99) 9999-9999", "(99) 99999-9999"], keepStatic: true });
        }
    }

    function setupProfessorBio() {
        const toggleBtn = document.getElementById('toggle-bio-btn');
        const bioWrapper = document.getElementById('professor-bio-wrapper');
        if (!toggleBtn || !bioWrapper) return; // Se não encontrar os elementos, ele para.

        toggleBtn.addEventListener('click', () => {
            bioWrapper.classList.toggle('show-full');
            toggleBtn.textContent = bioWrapper.classList.contains('show-full') ? 'Mostrar menos' : 'Saiba mais';
        });
    }

    function setupVideoModal() {
        const videoModalEl = document.getElementById('aulasDemonstrativasModal');
        if (!videoModalEl || typeof bootstrap === 'undefined') return;
        let player;
        videoModalEl.addEventListener('shown.bs.modal', function() {
            const firstVideoId = this.querySelector('[onclick^="changeVideo"]')?.getAttribute('onclick').match(/'([^']+)'/)[1];
            if (firstVideoId) {
                changeVideo(firstVideoId, 'video-player-container').then(p => player = p);
            }
        });
        videoModalEl.addEventListener('hidden.bs.modal', function() {
            if (player) {
                player.destroy();
                document.getElementById('video-player-container').innerHTML = '';
            }
        });
    }

    // --- INICIALIZAÇÃO ---
    startCountdown();
    setupLazyLoading();
    setupInscricaoModal();
    setupFormSubmission();
    setupPhoneMask();
    setupProfessorBio();
    setupVideoModal();
});

// =============================================
// FUNÇÕES GLOBAIS (acessíveis pelo HTML)
// =============================================
function loadVideo(element) {
    if (element.querySelector('iframe')) return;
    const videoId = element.getAttribute('data-video-id');
    const playerHTML = `<div class="plyr__video-embed player"><iframe src="https://www.youtube.com/embed/${videoId}?autoplay=1&origin=${window.location.origin}&modestbranding=1&rel=0" allow="autoplay; fullscreen" allowfullscreen frameborder="0"></iframe></div>`;
    element.innerHTML = playerHTML;
    if (typeof Plyr !== 'undefined') { new Plyr(element.querySelector('.player')); }
}

let currentVideoPlayer;
function changeVideo(videoId, containerId) {
    return new Promise((resolve) => {
        if (currentVideoPlayer) { currentVideoPlayer.destroy(); }
        const container = document.getElementById(containerId);
        if (!container) { return resolve(null); }
        container.innerHTML = `<iframe src="https://www.youtube.com/embed/${videoId}?autoplay=1&origin=${window.location.origin}&modestbranding=1&rel=0" allow="autoplay; fullscreen" allowfullscreen frameborder="0"></iframe>`;
        if (typeof Plyr !== 'undefined') {
            currentVideoPlayer = new Plyr(container, {});
            resolve(currentVideoPlayer);
        } else {
            resolve(null);
        }
    });
}