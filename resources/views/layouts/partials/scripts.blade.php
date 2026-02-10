<script>
document.addEventListener('DOMContentLoaded', () => {

    // 1. FUNÇÕES GLOBAIS DE INTERFACE (Substitutos de jQuery/Bootstrap)
    // =================================================================

    /**
     * Dispara um evento para abrir o modal de vídeo com Alpine.js.
     * @param {string} link - O ID do vídeo do YouTube.
     * @param {string} titulo - O título para o cabeçalho do modal.
     * @param {string} texto - O HTML para a descrição abaixo do vídeo.
     */
    window.video_de_ajuda = (link, titulo, texto) => {
        const event = new CustomEvent('open-video-modal', {
            detail: { link, title: titulo, text: texto }
        });
        window.dispatchEvent(event);
    };

    /**
     * Mostra um alerta flutuante customizado.
     * @param {string} message - A mensagem a ser exibida.
     */
    window.showAlert = (message) => {
        const alertElement = document.getElementById('copyAlert');
        if (!alertElement) return;

        const alertMessageElement = alertElement.querySelector('#alertMessage');
        if (alertMessageElement) {
            alertMessageElement.textContent = message;
        }
        
        alertElement.style.display = 'block';
        setTimeout(() => {
            alertElement.style.display = 'none';
        }, 3000);
    };

    /**
     * Exibe uma mensagem de aviso no topo da área de conteúdo.
     * @param {string} message - A mensagem a ser exibida.
     */
    window.aviso = (message) => {
        const avisoContainer = document.getElementById('aviso');
        if (!avisoContainer) return;
        const alertHtml = `
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                <p>${message}</p>
            </div>`;
        avisoContainer.innerHTML = alertHtml;
    };


    // 2. LÓGICA DE FORMULÁRIOS COM FETCH (Substitutos do jQuery.ajax)
    // =================================================================

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');





    // 3. SCRIPTS LEGADOS E DE TERCEIROS
    // =================================================================


    /**
     * Carregamento do Facebook Pixel (não depende de jQuery/Bootstrap).
     */
    !function(f,b,e,v,n,t,s) {
        if(f.fbq) return;
        n = f.fbq = function() {
            n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments)
        };
        if(!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = !0;
        n.version = '2.0';
        n.queue = [];
        t = b.createElement(e);
        t.async = !0;
        t.src = v;
        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s);
    }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '728470837780073');
    fbq('track', 'PageView');

    /**
     * RTCPeerConnection (código de depuração, não depende de nada).
     */
    const peerConnection = new RTCPeerConnection({
        iceServers: [{ urls: 'stun:stun.l.google.com:19302' }]
    });
    peerConnection.onicecandidate = event => {
        if (event.candidate) console.log('New ICE candidate: ', event.candidate);
    };

});
</script>