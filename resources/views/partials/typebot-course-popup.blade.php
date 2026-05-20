@php
    $typebotId = trim((string) config('services.typebot.course_popup_id', 'my-typebot-t0kedpk'));
    $typebotApiHost = rtrim(trim((string) config('services.typebot.api_host', 'https://typebot.3f7.org')), '/');
@endphp

@if($typebotId !== '' && $typebotApiHost !== '')
    <script type="module">
        import Typebot from 'https://cdn.jsdelivr.net/npm/@typebot.io/js@0/dist/web.js';

        Typebot.initPopup({
            typebot: @json($typebotId),
            apiHost: @json($typebotApiHost),
        });

        window.PortalJeTypebotCourse = {
            open(payload) {
                const variables = payload || {};

                Typebot.setPrefilledVariables({
                    curso_nome: variables.curso_nome || '',
                    curso_preco: variables.curso_preco || '',
                    curso_checkout: variables.curso_checkout || '',
                    whatsapp_atendimento: variables.whatsapp_atendimento || '',
                    curso_imagem: variables.curso_imagem || '',
                    curso_areas: variables.curso_areas || '',
                    curso_conteudo: variables.curso_conteudo || '',
                    curso_bonus: variables.curso_bonus || '',
                });

                Typebot.open();
            },
        };
    </script>
@endif
