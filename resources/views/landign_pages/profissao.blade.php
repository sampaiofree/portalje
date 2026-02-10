<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Projeto Trabalho para Todos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  </head>
  <body>
    <div id="alertaTelefone" class="alert alert-danger alert-dismissible fade position-fixed top-0 start-50 translate-middle-x mt-3 w-75" role="alert" style="z-index: 9999; display: none;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    Por favor, digite um WhatsApp válido com DDD.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>



    <div class="container py-5 text-center">
        <img alt="Logo" class=" img-fluid  mb-2 " src="{{asset('img/logo/emprego_para_todos.webp')}}">
        <h1 class="fw-bold mb-2">
            150 vagas liberadas para <br> <span class="text-bg-danger px-1">{{$dados['cidade']}}</span>
        </h1>

        <p class="fw-bold mb-4" style="font-size: small">
            <span class="text-bg-warning p-1"><i class="bi bi-calendar-event me-1"></i>Inscrições até {{$dados['data']}}</span>
        </p>

        <p class="mb-2">Somos uma escola que forma candidatos para vagas de emprego em {{$dados['cidade']}} e região. </p>
        <p class="mb-2">Treinamos cada aluno e indicamos para as vagas disponíveis nas empresas parceiras. </p>
        <p class="mb-1">A decisão final de entrevista e contratação é feita pelas próprias empresas. </p>

        <hr class="my-4">

        <h3 class="mb-4 fs-6 fw-bold">Escolha como deseja participar:</h3>

        <div class="d-grid gap-3 col-12 col-md-6 mx-auto">

            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_1" class="btn btn-success btn-lg">
            <p class="my-0 text-uppercase d-block fw-bold" style="line-height: 1; font-size: initial;"><i class="bi bi-person-badge-fill me-2"></i>Quero me candidatar a uma vaga</span>
            <p class="my-0" style="font-size: x-small">Para quem busca emprego e quer participar da seleção.</small>
            </button>

            <button class="btn btn-outline-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modal_empresa">
            <p class="my-0 text-uppercase d-block fw-bold" style="line-height: 1; font-size: initial;"><i class="bi bi-building me-1"></i>Sou empresa e quero contratar</span>
            <p class="my-0" style="font-size: x-small">Para empresas com vagas abertas que desejam receber candidatos.</small>
            </button>
        </div>
    </div>

 <form id="form_cadastro" method="POST">
    <!-- Modal -->
    <div class="modal fade" id="modal_1" tabindex="-1" aria-labelledby="modal_1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="container py-5 text-center">
                        <h2 class="fw-bold mb-3">
                            <i class="bi bi-person-lines-fill me-2"></i> Vamos começar sua inscrição
                        </h2>

                        <p class="mb-4">
                            Responda as perguntas a seguir para participar do processo e ter a chance de ser indicado para uma vaga em {{$dados['cidade']}} e região.
                        </p>

                       
                            @csrf <!-- se for blade -->
                            <div class="mb-3 text-start">
                            <label for="nome" class="form-label">Nome completo</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>

                            <input type="hidden" name="cidade" value="{{$dados['cidade']}}">
                            <input type="hidden" name="origem" id="origem">

                            </div>

                            <div class="mb-3 text-start">
                                <label for="whatsapp" class="form-label">WhatsApp com DDD</label>
                                <input type="tel" class="form-control" id="whatsapp" name="whatsapp" required>
                                <div id="erro-whatsapp" class="text-danger mt-1" style="display: none; font-size: 14px;">
                                    Por favor, digite um WhatsApp válido com DDD.
                                </div>
                            </div>



                            <div class="mb-3 text-start">
                            <label for="idade" class="form-label">Idade</label>
                            <select class="form-control" id="idade" name="idade" required>
                                <option value="">Selecione sua idade</option>
                                @for ($i = 14; $i <= 65; $i++)
                                <option value="{{ $i }}">{{ $i }} anos</option>
                                @endfor
                                <option value="{{ $i }}">acima de {{ $i }} anos</option>
                            </select>
                            </div>


                            <div class="d-grid">
                            <button 
                                id="continuar1"
                                type="button" 
                                class="btn-avancar btn btn-success btn-lg"
                                data-modal="modal_1"
                                data-target="modal_2"
                            >
                                <i class="bi bi-arrow-right-circle me-1"></i> Continuar
                            </button>
                            </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_2" tabindex="-1" aria-labelledby="modal_2" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="container py-5 text-center">
                        <h2 class="fw-bold mb-3">
                            <i class="bi bi-journal-text me-2"></i>Informe sua Escolaridade
                        </h2>

                       

                            <div class="mb-4 text-start">
                            <label for="escolaridade" class="form-label">Escolha seu nível de escolaridade:</label>
                            <select class="form-select" id="escolaridade" name="escolaridade" >
                                <option value="" disabled selected>Selecione</option>
                                <option value="Ensino Fundamental Incompleto">Ensino Fundamental Incompleto</option>
                                <option value="Ensino Fundamental Completo">Ensino Fundamental Completo</option>
                                <option value="Ensino Médio Incompleto">Ensino Médio Incompleto</option>
                                <option value="Ensino Médio Completo">Ensino Médio Completo</option>
                                <option value="Ensino Superior Incompleto">Ensino Superior Incompleto</option>
                                <option value="Ensino Superior Completo">Ensino Superior Completo</option>
                            </select>
                            </div>

                            <div class="d-flex justify-content-between gap-2 mt-4">
                                <button type="button"
                                        class="btn-voltar btn btn-secondary btn-lg"
                                        data-modal="modal_2"
                                        data-back="modal_1">
                                    <i class="bi bi-arrow-left-circle me-1"></i> Voltar
                                </button>

                                <button type="button"
                                        class="btn-avancar btn btn-success btn-lg"
                                        data-modal="modal_2"
                                        data-target="modal_3">
                                    <i class="bi bi-arrow-right-circle me-1"></i> Continuar
                                </button>
                            </div>

                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_3" tabindex="-1" aria-labelledby="modal_3" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="container py-5 text-center">
                        <h2 class="fw-bold mb-3">
                            <i class="bi bi-patch-check-fill me-2"></i>Você já fez algum curso abaixo?
                        </h2>
                       <div class="mb-3 text-start">
                        <label class="form-label">1. Informática básica</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="cert_informatica" value="Sim" required>
                            <label class="form-check-label">Sim</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="cert_informatica" value="Não" required>
                            <label class="form-check-label">Não</label>
                        </div>
                        </div>

                        <div class="mb-3 text-start">
                        <label class="form-label">2. Como se comportar em uma reunião</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="cert_reuniao" value="Sim" required>
                            <label class="form-check-label">Sim</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="cert_reuniao" value="Não" required>
                            <label class="form-check-label">Não</label>
                        </div>
                        </div>

                        <div class="mb-3 text-start">
                        <label class="form-label">3. Inglês (mesmo que básico)</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="cert_ingles" value="Sim" required>
                            <label class="form-check-label">Sim</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="cert_ingles" value="Não" required>
                            <label class="form-check-label">Não</label>
                        </div>
                        </div>

                        <div class="mb-3 text-start">
                        <label class="form-label">4. Curso de Atendimento</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="cert_atendimento" value="Sim" required>
                            <label class="form-check-label">Sim</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="cert_atendimento" value="Não" required>
                            <label class="form-check-label">Não</label>
                        </div>
                        </div>

                        <div class="mb-3 text-start">
                        <label class="form-label">5. Gestao do Tempo e Organização Profissional</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="cert_gestao_tempo" value="Sim" required>
                            <label class="form-check-label">Sim</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="cert_gestao_tempo" value="Não" required>
                            <label class="form-check-label">Não</label>
                        </div>
                        </div>

                        <div class="mb-3 text-start">
                        <label class="form-label">6. Trabalho em Equipe</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="cert_equipe" value="Sim" required>
                            <label class="form-check-label">Sim</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="cert_equipe" value="Não" required>
                            <label class="form-check-label">Não</label>
                        </div>
                        </div>

                        


                            <div class="d-flex justify-content-between gap-2 mt-4">
                                <button type="button"
                                        class="btn-voltar btn btn-secondary btn-lg"
                                        data-modal="modal_3"
                                        data-back="modal_2">
                                    <i class="bi bi-arrow-left-circle me-1"></i> Voltar
                                </button>

                                <button type="button"
                                        class="btn-avancar btn btn-success btn-lg"
                                        data-modal="modal_3"
                                        data-target="modal_4">
                                    <i class="bi bi-arrow-right-circle me-1"></i> Continuar
                                </button>
                            </div>

                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_4" tabindex="-1" aria-labelledby="modal_4" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="container py-5 text-center">
                        <h2 class="fw-bold mb-3">
                            <i class="bi bi-briefcase-fill me-2"></i>Qual área você gostaria de trabalhar?
                        </h2>

                        <p class="mb-3 text-center">Selecione uma das opções abaixo:</p>
                        
                        <div class="text-start mx-auto" style="max-width: 500px;">
                        
                           <select class="form-select" name="curso" required>
                                <option value="">Selecione uma área abaixo</option>
                                <?php foreach ($dados['cursos'] as $curso): ?>
                                    <option value="<?= $curso['id'] ?>"><?= $curso['titulo'] ?></option>
                                <?php endforeach; ?>
                            </select>

                            
                        </div>

                             <div class="d-flex justify-content-between gap-2 mt-4">
                                <button type="button"
                                        class="btn-voltar btn btn-secondary btn-lg"
                                        data-modal="modal_4"
                                        data-back="modal_3">
                                    <i class="bi bi-arrow-left-circle me-1"></i> Voltar
                                </button>

                                <button type="button"
                                        class="btn-avancar btn btn-success btn-lg"
                                        data-modal="modal_4"
                                        data-target="modal_5">
                                    <i class="bi bi-arrow-right-circle me-1"></i> Continuar
                                </button>
                            </div>
                        
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_5" tabindex="-1" aria-labelledby="modal_5" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="container py-5">
                        <h2 class="fw-bold mb-4 text-center">
                            <i class="bi bi-person-lines-fill me-2"></i>Antes de finalizar
                        </h2>

                        <!-- Pergunta 1 -->
                        <div class="mb-4">
                            <label class="form-label">Você já perdeu alguma vaga por não ter curso ou certificado?</label><br>
                            <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="perdeu_vaga" value="Sim" id="vaga_sim" required>
                            <label class="form-check-label" for="vaga_sim">Sim</label>
                            </div>
                            <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="perdeu_vaga" value="Não" id="vaga_nao">
                            <label class="form-check-label" for="vaga_nao">Não</label>
                            </div>
                        </div>

                        <!-- Pergunta 2 -->
                        <div class="mb-4">
                            <label class="form-label">O que mais te motiva a buscar um emprego hoje?</label>
                            <input type="text" class="form-control" name="motivacao" placeholder="Ex: Ajudar minha família, sair do desemprego..." required>
                        </div>

                        <!-- Pergunta 3 -->
                        <div class="mb-4">
                            <label class="form-label">Caso seja aprovado, e após concluir o curso preparatório, seus dados serão compartilhados com empresas parceiras para possível indicação. Você concorda?</label><br>
                            <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="compartilhar_dados" value="Sim" id="compartilhar_sim" required>
                            <label class="form-check-label" for="compartilhar_sim">Sim, concordo</label>
                            </div>
                            <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="compartilhar_dados" value="Não" id="compartilhar_nao">
                            <label class="form-check-label" for="compartilhar_nao">Não, não concordo</label>
                            </div>
                        </div>

                             <div class="d-flex justify-content-between gap-2 mt-4">
                                <button type="button"
                                        class="btn-voltar btn btn-secondary btn-lg"
                                        data-modal="modal_5"
                                        data-back="modal_4">
                                    <i class="bi bi-arrow-left-circle me-1"></i> Voltar
                                </button>

                                <button type="button"
                                        class="btn-avancar btn btn-success btn-lg"
                                        data-modal="modal_5"
                                        data-target="modal_6">
                                    <i class="bi bi-arrow-right-circle me-1"></i> Continuar
                                </button>
                            </div>
                        
                    </div>

                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="modal_6" tabindex="-1" aria-labelledby="modal_6" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="container py-5 text-start">
                        <h2 class="fw-bold mb-3 text-center">
                            <i class="bi bi-person-video2 me-2"></i>Como funciona o processo
                        </h2>

                        <p class="mb-3">
                            Somos uma escola que forma candidatos para vagas de emprego em {{$dados['cidade']}} e região.
                        </p>

                        <p class="mb-3">
                            Treinamos cada aluno e indicamos para as vagas disponíveis nas empresas parceiras.  
                            
                        </p>

                        <p class="mb-4">
                            Nossos treinamentos são 100% online, com aulas rápidas que você faz direto no celular.
                        </p>

                         <!--<p class="mb-4">Basta ter 2 horas por semana, no seu tempo, e você já consegue concluir.</p>

                        <p class="mb-4">O curso é leve e feito para quem trabalha, cuida da casa ou tem pouco tempo.</p>
                   

                        <div class="mb-4">
                            <label class="form-label fw-bold">Você tem pelo menos 2 horas por semana e acesso à internet para estudar pelo celular?</label><br>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="disponibilidade_online" id="sim" value="1" required>
                                <label class="form-check-label" for="sim">Sim, posso estudar online pelo celular</label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="disponibilidade_online" id="nao" value="0" required>
                                <label class="form-check-label" for="nao">Não tenho disponibilidade agora</label>
                            </div>
                        </div>
                        <div id="alertaOnline" class="alert alert-warning d-none mt-3 small" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Atenção: para ser aprovado no projeto, o curso online é obrigatório. Ele garante sua preparação e sua vaga na lista de indicações.
                        </div>-->

                        <div class="d-flex justify-content-between gap-2 mt-4">
                            <button type="button"
                                        class="btn-voltar btn btn-secondary btn-lg"
                                        data-modal="modal_6"
                                        data-back="modal_5">
                                    <i class="bi bi-arrow-left-circle me-1"></i> Voltar
                            </button>

                            <button type="button"
                                        class="btn-avancar btn btn-success btn-lg"
                                        data-modal="modal_6"
                                        data-target="modal_8">
                                    <i class="bi bi-arrow-right-circle me-1"></i> Continuar
                            </button>
                        </div>
                        
                    
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_7" tabindex="-1" aria-labelledby="modal_7" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="container py-5 text-start">
                        <h2 class="fw-bold mb-3 text-center">
                            <i class="bi bi-award-fill me-2"></i>Sobre o curso
                        </h2>

                        <p class="mb-3">
                            O curso é totalmente online, com aulas práticas no seu celular, no seu tempo.
                            
                        </p>

                        <p class="mb-3">Você recebe certificado reconhecido, suporte e entra na lista de indicação.</p>

                        <p class="mb-3">
                            <strong>Não cobramos mensalidade.</strong>
                            Para liberar o acesso, é cobrada apenas uma taxa simbólica única de <span class="bg-warning px-1">R$39,40</span> paga apenas <strong>uma única vez</strong>.
                        </p>

                        <p class="mb-4">
                            Essa taxa ajuda a manter o projeto e garantir que só continue quem está realmente comprometido.
                        </p>

                   

                        <div class="mb-4">
                            <label class="form-label fw-bold">Você tem condições de contribuir com essa taxa simbólica para garantir seu acesso?</label><br>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="pode_pagar" id="sim" value="1" required>
                                <label class="form-check-label" for="sim">Sim, consigo pagar a taxa</label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="pode_pagar" id="nao" value="0" required>
                                <label class="form-check-label" for="nao">Não posso pagar agora</label>
                            </div>
                        </div>
                        <div id="alertaPagamento" class="alert alert-warning d-none mt-3 small" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Atenção: a participação no projeto só é confirmada após o pagamento da taxa de inscrição. Infelizmente, não é possível seguir no processo sem essa contribuição simbólica, que garante seu acesso ao curso e à lista de indicações. Caso sua situação mude, você poderá retornar e concluir sua inscrição normalmente.
                        </div>
                        <div class="d-flex justify-content-between gap-2 mt-4">
                            <button type="button"
                                        class="btn-voltar btn btn-secondary btn-lg"
                                        data-modal="modal_7"
                                        data-back="modal_6">
                                    <i class="bi bi-arrow-left-circle me-1"></i> Voltar
                            </button>

                            <button type="button"
                                        class="btn-avancar btn btn-success btn-lg"
                                        data-modal="modal_7"
                                        data-target="modal_8">
                                    <i class="bi bi-arrow-right-circle me-1"></i> Continuar
                            </button>
                        </div>
                        
                    
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--<div class="modal fade" id="modal_8" tabindex="-1" aria-labelledby="modal_8" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="container py-5 text-start">
                        <h2 class="fw-bold mb-3 text-center">
                             <i class="bi bi-clipboard-check me-2"></i>Etapa final do cadastro
                        </h2>

                         
                        <div class="mb-4">
                            <p class="fw-bold text-danger">Atenção: sua inscrição está quase finalizada.</p>
                            <p>
                            Caso seu perfil seja aprovado, um <strong>consultor do projeto</strong> vai entrar em contato com você nos próximos dias.
                            </p>
                            <p>
                            Por isso, informe abaixo o melhor horário e como prefere ser contatado.
                            </p>
                        </div>

                        
                        <div class="mb-4">
                            <label class="form-label">Qual o melhor horário para entrarmos em contato com você?</label>
                            <select class="form-select" name="melhor_horario" required>
                                <option value="" disabled selected>Selecione um horário</option>
                                <option value="Manhã (08h às 12h)">Manhã (08h às 12h)</option>
                                <option value="Tarde (12h às 18h)">Tarde (12h às 18h)</option>
                                <option value="Noite (18h às 21h)">Noite (18h às 21h)</option>
                                <option value="Qualquer horário">Qualquer horário</option>
                            </select>
                        </div>

                        
                        <div class="mb-4">
                            <label class="form-label">Como prefere ser contatado?</label><br>
                            <div class="form-check">
                            <input class="form-check-input" type="radio" name="preferencia_contato" value="WhatsApp" id="pref_whatsapp" required>
                            <label class="form-check-label" for="pref_whatsapp">Mensagem no WhatsApp</label>
                            </div>
                            <div class="form-check">
                            <input class="form-check-input" type="radio" name="preferencia_contato" value="Ligação" id="pref_telefone">
                            <label class="form-check-label" for="pref_telefone">Ligação telefônica</label>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between gap-2 mt-4">
                            <button type="button"
                                        class="btn-voltar btn btn-secondary btn-lg"
                                        data-modal="modal_8"
                                        data-back="modal_7">
                                    <i class="bi bi-arrow-left-circle me-1"></i> Voltar
                            </button>

                            <button type="button"
                                        class="btn-avancar btn btn-success btn-lg"
                                        data-modal="modal_8"
                                        data-target="modal_9">
                                    <i class="bi bi-arrow-right-circle me-1"></i> Continuar
                            </button>
                        </div>
                        
                    
                    </div>
                </div>
            </div>
        </div>
    </div>-->

    <div class="modal fade" id="modal_8" tabindex="-1" aria-labelledby="modal_8" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="container py-5 text-start">
                        <h2 class="fw-bold mb-4">
                            <i class="bi bi-hourglass-split me-2"></i>Inscrição em análise
                        </h2>

                        <p class="mb-3">
                            Suas informações foram registradas e agora seu perfil será analisado pela equipe do <strong>Projeto Trabalho para Todos</strong>.
                        </p>

                        <p class="mb-3">
                            Nosso objetivo é identificar pessoas com potencial para participar da próxima turma de preparação para o mercado de trabalho em {{$dados['cidade']}} e região.
                        </p>

                        <p class="mb-3">
                            Caso seu perfil seja aprovado, um membro do nosso time entrará em contato com você diretamente pelo WhatsApp, nos próximos dias.
                        </p>

                        <p class="mb-3">
                            Recomendamos que você salve nosso número e fique atento às mensagens para não perder a oportunidade.
                        </p>

                        <p class="fw-bold text-success mb-4">
                            Desejamos todo o sucesso na avaliação! Estamos na torcida por você. 💼
                        </p>

                        <a href="https://wa.me/{{$dados['whatsapp']}}?text=Olá,%20acabei%20de%20me%20inscrever%20no%20Projeto%20Trabalho%20pra%20Todos%20e%20gostaria%20de%20acompanhar%20o%20meu%20cadastro." class="btn btn-success btn-lg">
                                <i class="bi bi-whatsapp me-2"></i> Acompanhar minha inscrição pelo WhatsApp
                        </a>

                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_whatsapp" tabindex="-1" aria-labelledby="modal_whatsapp" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-body">
                   <div class="container py-5 text-center">
                    <h2 class="fw-bold mb-4">
                        <i class="bi bi-check2-circle me-2"></i>Quase lá!
                    </h2>

                    <p class="mb-3">
                        Suas informações foram recebidas e seu perfil será analisado pela equipe do <strong>Projeto Trabalho para Todos</strong>.
                    </p>

                    <p class="mb-3">
                        Agora, clique no botão abaixo para concluir sua participação e receber o resultado da análise diretamente no seu WhatsApp.
                    </p>

                    <p class="mb-4 fw-bold">
                        Essa é a última etapa. Só quem confirma pelo WhatsApp recebe o retorno.
                    </p>

                    <a href="https://wa.me/{{$dados['whatsapp']}}?text=Olá,%20quero%20saber%20o%20resultado%20da%20minha%20análise%20do%20Projeto%20Trabalho%20para%20Todos." class="btn btn-success btn-lg">
                        <i class="bi bi-whatsapp me-2"></i> Confirmar pelo WhatsApp
                    </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    
    
 </form>

 <div class="modal fade" id="modal_empresa" tabindex="-1" aria-labelledby="modal_empresa" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-body">
                   <div class="container py-5 text-start">
                        <h2 class="fw-bold mb-4 text-center">
                            <i class="bi bi-building me-2"></i>Cadastro da Empresa
                        </h2>

                        <p class="mb-4 text-center">
                            Preencha os dados abaixo para receber candidatos treinados e indicados pelo Projeto Trabalho para Todos – Uberlândia/MG.
                        </p>

                        <form id="formEmpresa" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="nome_empresa" class="form-label">Nome da empresa</label>
                                <input type="text" class="form-control" id="nome_empresa" name="nome_empresa" required>
                            </div>

                            <div class="mb-3">
                                <label for="nome_responsavel" class="form-label">Nome do responsável</label>
                                <input type="text" class="form-control" id="nome_responsavel" name="nome_responsavel" required>
                            </div>

                            <div class="mb-3">
                                <label for="whatsapp" class="form-label">WhatsApp para contato</label>
                                <input type="tel" class="form-control" id="whatsapp" name="whatsapp" required>
                            </div>

                            <div class="mb-3">
                                <label for="cidade" class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="cidade" name="cidade" required>
                            </div>

                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-control" id="estado" name="estado" required>
                                    <option value="" selected>Selecione o estado</option>
                                    <option value="AC">AC - Acre</option>
                                    <option value="AL">AL - Alagoas</option>
                                    <option value="AP">AP - Amapá</option>
                                    <option value="AM">AM - Amazonas</option>
                                    <option value="BA">BA - Bahia</option>
                                    <option value="CE">CE - Ceará</option>
                                    <option value="DF">DF - Distrito Federal</option>
                                    <option value="ES">ES - Espírito Santo</option>
                                    <option value="GO">GO - Goiás</option>
                                    <option value="MA">MA - Maranhão</option>
                                    <option value="MT">MT - Mato Grosso</option>
                                    <option value="MS">MS - Mato Grosso do Sul</option>
                                    <option value="MG" >MG - Minas Gerais</option>
                                    <option value="PA">PA - Pará</option>
                                    <option value="PB">PB - Paraíba</option>
                                    <option value="PR">PR - Paraná</option>
                                    <option value="PE">PE - Pernambuco</option>
                                    <option value="PI">PI - Piauí</option>
                                    <option value="RJ">RJ - Rio de Janeiro</option>
                                    <option value="RN">RN - Rio Grande do Norte</option>
                                    <option value="RS">RS - Rio Grande do Sul</option>
                                    <option value="RO">RO - Rondônia</option>
                                    <option value="RR">RR - Roraima</option>
                                    <option value="SC">SC - Santa Catarina</option>
                                    <option value="SP">SP - São Paulo</option>
                                    <option value="SE">SE - Sergipe</option>
                                    <option value="TO">TO - Tocantins</option>
                                </select>
                            </div>


                            <div class="mb-4">
                                <label for="informacoes_vagas" class="form-label">Informações sobre as vagas</label>
                                <textarea class="form-control" id="informacoes_vagas" name="informacoes_vagas" rows="3" required></textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-send-check me-2"></i>Enviar Solicitação
                                </button>
                            </div>
                        </form>

                    </div>


                </div>
            </div>
        </div>
    </div>
    <!--Rodapé-->
    <footer class="mt-5 py-4 bg-light">
        <div class="container">
            <p class="mb-2 text-muted" style="font-size: 10px;">
                <i class="bi bi-shield-lock-fill me-1"></i>
                Seus dados estão protegidos. Usamos tecnologia segura e não compartilhamos suas informações.
            </p>
            <p class="mb-0 text-muted" style="font-size: 10px;">
                <i class="bi bi-exclamation-circle-fill me-1"></i>
                O Programa Emprego para Todos é uma iniciativa privada, sem vínculo com o governo ou órgãos públicos. Nossa escola capacita pessoas para o mercado de trabalho, mas <strong>não garante emprego</strong>. A chamada para entrevistas e a contratação dependem exclusivamente das empresas parceiras.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>

    <script>
      $(document).ready(function () {
        let botaoClicado = null;

        $('.btn-avancar').on('click', function () {
            botaoClicado = $(this);
        });

        //VERIFICAR NUMERO DO WHATSAPP
        $('#whatsapp').inputmask('(99) 99999-9999');
        $('#whatsapp').on('input', function () {
            const telefone = $(this).val();
            const regex = /^\(\d{2}\) \d{5}-\d{4}$/;

            if (!regex.test(telefone)) {
                $('#erro-whatsapp').show();
            } else {
                $('#erro-whatsapp').hide();
                $('#alertaTelefone').hide();
            }
        });

        //BOTÃO CONTINUAR
        $('.btn-avancar').on('click', function () {
            botaoClicado = $(this);
            $('#form_cadastro').submit(); // chama o submit com controle
        });

        //ENVIAR FORMULÁRIO
        let eventoLeadEnviado = false; // controla envio único

        $('#form_cadastro').on('submit', function (e) {
            e.preventDefault();

            const telefone = $('#whatsapp').val();
            const regex = /^\(\d{2}\) \d{5}-\d{4}$/;

            if (!regex.test(telefone)) {
                const $alerta = $('#alertaTelefone');
                $alerta.show().addClass('show');

                setTimeout(() => {
                    $alerta.removeClass('show').fadeOut();
                }, 4000);

                return;
            }

            const modalAtual = botaoClicado.data('modal');
            const proximoModal = botaoClicado.data('target');
            const dados = $(this).serialize();

            console.log('Enviando dados:', dados);

            $.post('{{ route('enviar_formulario') }}', dados)
                .done(function (resposta) {
                    console.log('Resposta do servidor:', resposta);

                    // Envia evento Lead apenas na primeira vez
                    if (!eventoLeadEnviado && typeof fbq !== 'undefined') {
                        fbq('track', 'Lead');
                        eventoLeadEnviado = true;
                    }

                    $('#' + proximoModal).modal('show');
                    //$('#modal_whatsapp').modal('show');
                    $('#' + modalAtual).modal('hide');
                })
                .fail(function (erro) {
                    console.error('Erro ao enviar:', erro);
                    $('#modal_whatsapp').modal('show');
                    $('#' + modalAtual).modal('hide');
                });
        });


        //BOTÃO BOLTAR
        $('.btn-voltar').on('click', function () {
            const modalAtual = $(this).data('modal');
            const modalAnterior = $(this).data('back');

            $('#' + modalAtual).modal('hide');

            setTimeout(() => {
                $('#' + modalAnterior).modal('show');
            }, 500);
        });

        //FORMULÁRIO DA EMPRESA
        $('#formEmpresa').on('submit', function (e) {
            e.preventDefault();

            const dados = $(this).serialize();

            $.post('{{ route("enviar_formulario_empresa") }}', dados)
                .done(function (resposta) {
                    console.log('Empresa salva:', resposta);
                    alert('Cadastro enviado com sucesso! Em breve um de nossos consultores irá entrar em contato');
                    $('#modal_empresa').modal('hide');
                    $('#formEmpresa')[0].reset();
                })
                .fail(function (xhr) {
                    console.error('Erro ao salvar empresa:', xhr.responseJSON || xhr.responseText);
                    alert('Agradecemos o seu interesse. Em breve um de nossos consultores irá entrar em contato');
                });
        });

        //ALERT PARA QUEM NÃO PODE ESTURAR ONLINE
        $(document).ready(function () {
            $('input[name="disponibilidade_online"]').on('change', function () {
                if ($(this).val() === "0") {
                    $('#alertaOnline').removeClass('d-none');
                } else {
                    $('#alertaOnline').addClass('d-none');
                }
            });
        });
        
        //ALERT PARA PAGAMENTO
        $(document).ready(function () {
            $('input[name="pode_pagar"]').on('change', function () {
                if ($(this).val() === "0") {
                    $('#alertaPagamento').removeClass('d-none');
                } else {
                    $('#alertaPagamento').addClass('d-none');
                }
            });
        });

        //ORIGEM
        const urlParams = new URLSearchParams(window.location.search);
        // Parâmetros que você quer rastrear
        const campos = ['utm_source', 'utm_campaign', 'utm_medium', 'utm_content', 'utm_term', 'src', 'ref', 'cidade'];
        let origemFinal = [];
        campos.forEach(param => {
            const valor = urlParams.get(param);
            if (valor) {
            origemFinal.push(`${param}=${valor}`);
            }
        });
        // Preenche o campo oculto
        document.getElementById('origem').value = origemFinal.join('&');

    });


    //APÓS O CARREGAMENTO COMPLETO DA PÁGINA
   window.addEventListener('load', function () {
        fetch('{{ route('pixel_user') }}', {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Resposta do Pixel:', data);

            if (data.pixel_id) {
                    !function(f,b,e,v,n,t,s){
                        if(f.fbq)return;n=f.fbq=function(){
                            n.callMethod ? n.callMethod.apply(n,arguments) : n.queue.push(arguments)
                        };
                        if(!f._fbq)f._fbq=n;
                        n.push=n;n.loaded=!0;n.version='2.0';
                        n.queue=[];t=b.createElement(e);t.async=!0;
                        t.src=v;s=b.getElementsByTagName(e)[0];
                        s.parentNode.insertBefore(t,s)
                    }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');

                    fbq('init', data.pixel_id);
                    fbq('init', '948808649224691');
                    fbq('track', 'PageView');
                }
        });

    });

    </script>

  </body>
</html>