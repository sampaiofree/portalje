@extends('adm.html_base')

@section('content')

    <div class="row">
        <div class="col-12">

        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
                @if(Auth::user()->dominio)
                    @if(Auth::user()->dominio AND !Auth::user()->dominio_externo)
                    <div class="card cta-box bg-info text-white">
                        <div class="card-body">
                            <div class="row">
                                <h4 class="m-0 fw-normal cta-box-title text-reset">Seu site é: 
                                    <a class="text-white href_dominio" target="_blank"  href="https://{{Auth::user()->dominio}}"><strong class="dominio_atual" style="font-size: x-large;">{{Auth::user()->dominio}}</strong></a>
                                </h4>
                            </div>
                            <div class="row g-2">
                                <div class="col-12">
                                    <h5 class="mb-0 pb-0 d-flex align-items-center">Você também pode personalizar seu link:
                                        <a href="https://youtu.be/pm48QOzVfZA" target="_blank" class="ms-1" aria-label="Saiba mais sobre como personalizar seu link">
                                            <i class="ri-information-line text-white" style="font-size: 1.2rem;"></i>
                                        </a>
                                    </h5>
                                </div>
                                <div class="col-md-4">
                                    <label for="selectPagina" class="form-label">Página</label>
                                    <select id="selectPagina" class="form-select">
                                        <option value="https://{{Auth::user()->dominio}}">Home page</option>
                                        <option value="https://{{Auth::user()->dominio}}/w">Carvalho WhatsApp</option> 
                                        @foreach($cursos as $curso)
                                            @if($curso->publicado AND $curso->permitir_afiliacao AND $curso->codigo_ref != null)
                                                <option value="https://{{Auth::user()->dominio}}/cursos/{{$curso->url}}/w/">{{$curso->titulo}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                    
                                <div class="col-md-4">
                                    <label for="selectCupom" class="form-label">Cupom de Desconto</label>
                                    <select id="selectCupom" class="form-select">
                                        <option value="">Sem desconto</option>
                                        <option value="o10">10% de desconto</option>
                                        <option value="o20">20% de desconto</option>
                                        <option value="o30">30% de desconto</option>
                                        <option value="o40">40% de desconto</option>
                                        <option value="o50">50% de desconto</option>
                                        <option value="o60">60% de desconto</option>
                                        <option value="o70">70% de desconto</option>
                                    </select>
                                </div>
                    
                                <div class="col-md-4">
                                    <label for="inputCidade" class="form-label">Nome da Cidade</label>
                                    <input type="text" id="inputCidade" class="form-control" placeholder="Digite o nome da cidade">
                                </div>
                            </div>
                    
                            <!-- Campo de Resultado e Botão de Copiar -->
                            <div class="row mt-4 align-items-center">
                                <div class="col-md-9">
                                    <input type="text" id="linkResultado" class="form-control" readonly placeholder="Link gerado aqui">
                                </div>
                                <div class="col-md-3 mt-2 mt-md-0">
                                    <button id="copiarLink" class="btn btn-sm btn-primary w-100">Copiar Link</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(Auth::user()->dominio_externo)
                    <div class="card cta-box bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-start align-items-center">
                                <div class="w-100 overflow-hidden">
                                    <h4 class="m-0 fw-normal cta-box-title text-reset">Agora você tem um site <b>só seu</b> 
                                        <a class="text-white href_dominio" target="_blank"  href="https://{{Auth::user()->dominio_externo}}"><strong class="dominio_atual" style="font-size: x-large;">{{Auth::user()->dominio_externo}}</strong></a>
                                    </h4>
                                    <h5 class="mb-0 pb-0">Você também pode usar outros links:</h5>
                                    <ul class="mt-0 pt-0">
                                        <li>Página do método carvalho: <a class="text-white href_dominio"  target="_blank" href="https://{{Auth::user()->dominio_externo}}/w3">https://{{Auth::user()->dominio_externo}}/w3</a></li>
                                    </ul>
                                </div>
                                <img class="ms-3" src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/images/svg/email-campaign.svg')}}" width="120" alt="Generic placeholder image">
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(Auth::user()->whatsapp_atendimento)
                        <div class="card cta-box text-white" style="background-color: #008B31">
                            <div class="card-body">
                                <div class="d-flex align-items-start align-items-center">
                                    <div class="w-100 overflow-hidden">
                                        <h4 class="m-0 fw-normal cta-box-title text-reset">Seu WhatsApp de atendimento 
                                            <a href="javascript:void(0);" class="text-white" data-bs-toggle="modal" data-bs-target="#modal_form_user_whatsapp_atendimento"><strong class="mostrar_whatsapp_atendimento" style="font-size: x-large;">{{Auth::user()->whatsapp_atendimento}}</strong></a>
                                        </h4>
                                        <h5 class="mb-0 pb-0">já está cadastrado e funcionando no seu site.</h5>
                                    </div>
                                    <i style="font-size: 50px" class="ms-3 ri-whatsapp-line"> </i>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="javascript:void(0);" class="text-white href_dominio" data-bs-toggle="modal" data-bs-target="#modal_form_user_whatsapp_atendimento">
                            <div class="card cta-box bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-start align-items-center">
                                        <div class="w-100 overflow-hidden">
                                            <h4 class="m-0 fw-normal cta-box-title text-reset">Você não cadastrou seu WhatsApp de atendimento! <strong>Clique AQUI para cadastrar</strong></h4>
                                        </div>
                                        <i style="font-size: 50px" class="ms-3 ri-whatsapp-line"> </i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endif
                @else
                    <a href="javascript:void(0);" class="text-white href_dominio" data-bs-toggle="modal" data-bs-target="#modal_form_dominio">
                        <div class="card cta-box bg-danger text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-start align-items-center">
                                    <div class="w-100 overflow-hidden">
                                        <h4 class="m-0 fw-normal cta-box-title text-reset">Você não cadastrou seu site! <strong>Clique AQUI para cadastrar</strong></h4>
                                    </div>
                                    <i style="font-size: 50px" class="ms-3 ri-link-unlink-m"> </i>
                                </div>
                            </div>
                        </div>
                    </a>
                @endif
                <div class="card border widget-flat">
                    <div class="card-header mb-0 pb-0 d-flex justify-content-between align-items-center">
                        <h4 class="">Vídeos úteis para te ajudar<br>
                            <small style="font-size: 10pt;font-weight: normal;">
                              Estude hoje para conquistar a liberdade amanhã.
                            </small>
                        </h4>
                    </div>
                    <div class="card-body mt-0 pt-0">
                        <p class="mb-0"><a href="https://youtu.be/F_RYIH7NpOQ" target="_blank"><i class="  ri-youtube-line    me-1"></i>Qual link devo usar?</a></p>
                        <p class="mb-0"><a href="https://youtu.be/7xIt3PLPZvw" target="_blank"><i class="  ri-youtube-line    me-1"></i>Corrigindo Erros no Site</a></p>
                        <p class="mb-0"><a href="https://www.youtube.com/playlist?list=PL8UPaaNJEdSBxmq-xDp3pFcCEYVEf89Ua" target="_blank"><i class="  ri-youtube-line    me-1"></i>Como é o trabalho de um Afiliado</a></p>
                        <p class="mb-0"><a href="https://www.youtube.com/playlist?list=PL8UPaaNJEdSDxY3R2DoTYzSNdmNOJB7FA" target="_blank"><i class="  ri-youtube-line    me-1"></i>Método Carvalho: O Melhor Método de Vendas para Iniciantes</a></p>
                        <p class="mb-0"><a class="text-danger" href="https://www.youtube.com/playlist?list=PL8UPaaNJEdSBFoe9Pd1TBlklRrZUZt2lU" target="_blank"><i class="  ri-youtube-line    me-1"></i>Curso de Atendimento e Vendas</a></p>
                        <p class="mb-0"><a class="text-danger" href="https://www.youtube.com/playlist?list=PL8UPaaNJEdSCQa9OWWgJV7JZ4bkuvdVtj" target="_blank"><i class="  ri-youtube-line    me-1"></i>Análise de Atendimentos</a></p>
                        <p class="mb-0"><a href="https://www.youtube.com/playlist?list=PL8UPaaNJEdSBXiS5H7yzEHeBMt2yM5VFs" target="_blank"><i class="  ri-youtube-line    me-1"></i>Dicas e Entrevistas com TOP Afiliados</a></p>
                        <p class="mb-0"><a href="https://www.youtube.com/playlist?list=PL8UPaaNJEdSA9Kge9NVrvkvjZ67UMkiPx" target="_blank"><i class="  ri-youtube-line    me-1"></i>Meta ADS: Tudo sobre BM, Pixel e Conta de Anúncios</a></p>
                        <p class="mb-0"><a href="https://www.youtube.com/playlist?list=PL8UPaaNJEdSBAqMX7yxDh-aa9c5dQRA5c" target="_blank"><i class="  ri-youtube-line    me-1"></i>Dúvidas Frequentes dos Afiliados</a></p>
                    </div> 
                </div>
            
        </div>
        <div class="col-lg-3">
            <div class="card cta-box bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-start align-items-center">
                        <div class="w-100 overflow-hidden text-white">
                            <h4 class="m-0 fw-normal cta-box-title text-white"><b>Suporte para os alunos do Portal JE</b></h3>
                            <p class="d-flex align-items-center mb-0 pb-0 text-danger"><strong>Não atendemos afiliados neste número.</strong></p>
                            <p class="mt-1 pt-0">Copie o link abaixo e envie para o aluno. Ele será direcionado para o nosso WhatsApp de suporte ao aluno.</p>
                            <a target="_blank" class="text-white h4" href="{{route('suporte_alunos')}}" ><strong>{{route('suporte_alunos')}}</strong></a>
                        </div>
                        <i style="font-size: 50px" class="ms-3 ri-alert-line"> </i>
                    </div>
                </div>     
            </div>
            <div class="card cta-box text-white bg-success">
                <div class="card-body">
                    <div class="d-flex align-items-start align-items-center">
                        <div class="w-100 overflow-hidden">
                            <h4 class="m-0 fw-normal cta-box-title text-reset">Grupo de Avisos e Suporte para os Afiliados</h4>
                                <a href="https://chat.whatsapp.com/Lb7F6OSFUvx1kmyp4h7kgT" target="_blank" class="mt-2 text-dark align-itens-center btn btn-light"> Entrar AGORA <i class="ms-1 ri-whatsapp-line"> </i></a>
                            
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="col-lg-3">
            <div class="card border widget-flat">
                <div class="card-header mb-0 pb-0 d-flex justify-content-between align-items-center">
                    <h4 class="">Links úteis para ajudar você a vender!<br>
                        <small style="font-size: 10pt;font-weight: normal;">
                        Você pode usar os nossos criativos e copys a vontade.
                        </small>
                    </h4>
                </div>
                <div class="card-body mt-0 pt-0">
                    <p class="mb-0"><a href="https://docs.google.com/document/d/1SnSGRBme8iMFtzZ9E8QVtRFLnDVmOhRs-qIddpAyA-o" target="_blank"><i class="ri-link   me-1"></i>Script de Atendimento</a></p>
                    <p class="mb-0"><a href="https://drive.google.com/drive/folders/1H1Obc3ozkNDUkcimhDwHD1olZYvHBL2f?usp=sharing" target="_blank"><i class="ri-link me-1"></i>Materiais de Apoio</a></p>
                    <p class="mb-0"><a href="https://drive.google.com/drive/folders/1CrbCmiF5tbF09IJZtFeGfIO6cF--dFjP?usp=sharing" target="_blank"><i class="ri-link me-1"></i>Materiais dos Cursos</a></p>
                    <p class="mb-0"><a href="https://drive.google.com/drive/folders/1Y9B7YbXpffaeYUKqs7q3cVfUxuCRgUrg?usp=sharing" target="_blank"><i class="ri-link me-1"></i>Ebooks / Bônus / Iscas</a></p>
                    <p class="mb-0"><a href="https://www.canva.com/design/DAFgj3kPwyk/Qc7Ap4zUX5TQ-7D-YqbziA/edit?utm_content=DAFgj3kPwyk&utm_campaign=designshare&utm_medium=link2&utm_source=sharebutton" target="_blank"><i class="ri-link me-1"></i>Criativos do Método Carvalho</a></p>
                </div> 
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="card border">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <div class="mb-2">
                            <h4 class="header-title mt-2">Perguntas e Respostas sobre o Programa de Parceiros</h4>
                            <div class="row px-2 py-3">
                                <input class="form-control" type="text" id="campoPesquisa" placeholder="Digite sua pergunta">
                            </div>
                            <div class="accordion custom-accordion" id="custom-accordion-one">
                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading1">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse1" aria-expanded="false" aria-controls="collapse1">
                                                    Qual a vantagem dos formulários nas páginas? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse1" class="collapse" aria-labelledby="heading1" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Com certeza, ter um melhor aproveitamento de leads, o que antes do formulário era normal um aproveitamento de 50% a 60%, com o formulário isso aumenta para mais de 80%. Pois os leads que perdíamos por não chegar até o WhatsApp, agora quando eles preenchem o formulário vc tem os dados dele no CRM do portal para poder entrar em contato.                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading2">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                                    Quais páginas eu posso usar para divulgar os cursos <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse2" class="collapse" aria-labelledby="heading2" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Quando você abre uma conta no portal dos parceiros, você ganha um domínio. Você pode divulgar este domínio da maneira que quiser. Quando as pessoas compram pelo seu domínio, você receber a comissão pelas vendas.                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading3">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                                    Como eu explico sobre a parte prática dos cursos? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse3" class="collapse" aria-labelledby="heading3" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/x2qPiRCtWs4?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading4">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                                    O Certificado dos cursos é reconhecido pelo MEC? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse4" class="collapse" aria-labelledby="heading4" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/HbVUS2RGzy8?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading5">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                                    Preciso de experiência prévia em vendas ou marketing digital? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse5" class="collapse" aria-labelledby="heading5" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Não, você não precisa de experiência prévia. Nosso portal fornece todos os treinamentos e recursos necessários para começar do zero. Se você tem vontade de aprender e se dedicar, está pronto para começar!                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading6">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse6" aria-expanded="false" aria-controls="collapse6">
                                                    Quanto tempo leva para começar a ver resultados? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse6" class="collapse" aria-labelledby="heading6" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            O tempo para ver resultados pode variar de pessoa para pessoa. Tudo depende do seu esforço, da dedicação e da aplicação das estratégias que ensinamos. Alguns veem resultados em poucas semanas, enquanto outros podem levar um pouco mais de tempo. Alguns Parceiros conseguiram em 3 meses um faturamento mensal de R$5mil reais. Outros conseguem em alguns meses um faturamento de R$10mil reais.                                                                     </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading7">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse7" aria-expanded="false" aria-controls="collapse7">
                                                    Está aparecendo um erro quando o lead tenta comprar um curso <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse7" class="collapse" aria-labelledby="heading7" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Um dos principais motivos é que o seu código REF pode estar errado. Assista o vídeo de ajuda novamente clicando AQUI. Se mesmo assim continuar o erro, envie um email para parceiros@jovemempreendedor.org                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading8">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse8" aria-expanded="false" aria-controls="collapse8">
                                                    Como posso saber mais informações sobre os cursos para poder vender? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse8" class="collapse" aria-labelledby="heading8" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Todas as informações estão na página de vendas. Você pode verificar as informações e se ainda tiver dúvidas, pode consultar nosso suporte pelo email: parceiros@jovemempreendedor.org.                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading9">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse9" aria-expanded="false" aria-controls="collapse9">
                                                    Como criar sua conta de anuncios do Facebook <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse9" class="collapse" aria-labelledby="heading9" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/zw4r_-xxXkE?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading10">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse10" aria-expanded="false" aria-controls="collapse10">
                                                    Como solicitar o suporte do Facebook <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse10" class="collapse" aria-labelledby="heading10" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/AxR96lMhyvI?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading11">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse11" aria-expanded="false" aria-controls="collapse11">
                                                    Como posso subir minha campanha no Faceboook? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse11" class="collapse" aria-labelledby="heading11" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/hkEU-nkqvP0?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading12">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse12" aria-expanded="false" aria-controls="collapse12">
                                                    Como cadastrar o meu WhatsApp de atendimento? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse12" class="collapse" aria-labelledby="heading12" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Para cadastrar seu número de atendimento vai ir em Meu site &gt; meu domínio.Lembrando que não pode colocar espaço, nem traço, apenas números. DDI+DDD+seu telefone                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading13">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse13" aria-expanded="false" aria-controls="collapse13">
                                                    Como funciona o projeto Parceiros do Portal? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse13" class="collapse" aria-labelledby="heading13" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Esse projeto tem o mesmo intuito que sempre tivemos com os afiliados, de poder proporcionar a possibilidade de trabalhar em casa, ganhando dinheiro vendendo cursos profissionalizantes, cursos que vão ajudar as pessoas a ingressarem no mercado de trabalho. Temos vários parceiros com a gente que conseguem ter um faturamento acima de 5K por mês. Ser parceiro do portal é poder contar com a gente quando precisar, mantendo uma boa comunicação, trocas de experiências, informações e ter acesso a treinamentos que vão te ajudar a chegar nos seus objetivos. Sempre buscamos estar o mais próximo possível dos nossos parceiros, assim como você.                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading14">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse14" aria-expanded="false" aria-controls="collapse14">
                                                    Qual o canal do Youtube para afiliados? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse14" class="collapse" aria-labelledby="heading14" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Segue link do nosso canal do YouTube: https://www.youtube.com/@parceiroJEhttps://www.youtube.com/@parceiroJE                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading15">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse15" aria-expanded="false" aria-controls="collapse15">
                                                    Como posso dar suporte ao meu aluno? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse15" class="collapse" aria-labelledby="heading15" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/ILWthTrToTE?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading16">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse16" aria-expanded="false" aria-controls="collapse16">
                                                    Não consigo abrir um chamado no suporte de alunos <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse16" class="collapse" aria-labelledby="heading16" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Nesse caso pode enviar o link do nosso suporte direto ao aluno: https://www.jovemempreendedor.org/atendimento/suporte.php Assim que ele clicar automaticamente já vai abrir o WhatsApp de suporte.                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading17">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse17" aria-expanded="false" aria-controls="collapse17">
                                                    Como solicitar suporte na Hotmart? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse17" class="collapse" aria-labelledby="heading17" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Para abrir um chamado na Hotmart seja para uma ajuda como afiliado ou para ajudar um aluno é só acessar o link: https://help.hotmart.com/pt-BR/contact-us                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading18">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse18" aria-expanded="false" aria-controls="collapse18">
                                                    Quais são as regras para trabalhar como parceiro do Portal? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse18" class="collapse" aria-labelledby="heading18" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/kJrqK9ZlrA0?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading19">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse19" aria-expanded="false" aria-controls="collapse19">
                                                    Qual link eu devo enviar? Como pegar o link certo? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse19" class="collapse" aria-labelledby="heading19" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/SVU4BIkSnRY?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading20">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse20" aria-expanded="false" aria-controls="collapse20">
                                                    Onde fica a empresa Portal Jovem Empreendedor? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse20" class="collapse" aria-labelledby="heading20" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Quando alguém perguntar de onde o portal é, você pode responder que a matriz fica na cidade de Taquara/RS, que já trabalhamos com cursos online a mais de 10 anos e temos mais de 85 mil alunos. E por se tratar de curso online não há a necessidade de ter uma unidade presencial em cada cidade. Pode complementar falando que você é de tal cidade (pode falar a sua cidade real) e que é responsável em atender a cidade do seu cliente.                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading21">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse21" aria-expanded="false" aria-controls="collapse21">
                                                    Por que não recebi minha comissão?  <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse21" class="collapse" aria-labelledby="heading21" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/qkuCJKlefnI?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading22">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse22" aria-expanded="false" aria-controls="collapse22">
                                                    Não recebi a minha comissão, o que fazer? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse22" class="collapse" aria-labelledby="heading22" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Favor preencher as informações abaixo e nos enviar para o e-mail: parceiros@jovemempreendedor.org ID do Produto:&nbsp;Parceiro e-mail:&nbsp;Parceiro nome:&nbsp;Transação HP:&nbsp;e-mail comprador:&nbsp;Enviar junto ao e-mail um print da conversa com o cliente, onde você envia o link para o aluno fazer a inscrição.                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading23">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse23" aria-expanded="false" aria-controls="collapse23">
                                                    Posso anunciar no Google Ads?  <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse23" class="collapse" aria-labelledby="heading23" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Pode sim, porém todas as nossas estratégias e suporte são voltadas para o Facebook ADS. Mas ATENÇÃO às regras: Não usar as palavras chaves: portal jovem empreendedor, programa jovem empreendedor e Bruno Sampaio.                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading24">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse24" aria-expanded="false" aria-controls="collapse24">
                                                    Posso usar os criativos do Instagram e YouTube do Portal JE? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse24" class="collapse" aria-labelledby="heading24" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Pode Sim. Porém todos os nossos materiais disponíveis são de uso exclusivo para os cursos oferecidos pelo Portal JE.                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading25">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse25" aria-expanded="false" aria-controls="collapse25">
                                                    O Portal disponibiliza lista de Lookalike? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse25" class="collapse" aria-labelledby="heading25" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/_9P2ip7JmbM?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading26">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse26" aria-expanded="false" aria-controls="collapse26">
                                                    Minha campanha só fica em análise, o que eu faço? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse26" class="collapse" aria-labelledby="heading26" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            <div class="my-3 ratio ratio-16x9">
                                                                <iframe data-src="https://www.youtube.com/embed/4luIXb1KNzs?autohide=0&amp;showinfo=0&amp;controls=0"></iframe>
                                                            </div>
                                                        </div>
                                                                                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    <div class="card mb-1">
                                        <div class="card-header" id="heading27">
                                            <h5 class="m-0">
                                                <a class="custom-accordion-title collapsed d-block" data-bs-toggle="collapse" href="#collapse27" aria-expanded="false" aria-controls="collapse27">
                                                    Onde encontrar os links com a opção de boleto? <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse27" class="collapse" aria-labelledby="heading27" data-bs-parent="#custom-accordion-one">
                                            <div class="card-body">
                                                <div class="row d-flex align-items-center">
                                                                                                                                                                                        <div class="col-lg-6 col-sm-10 mx-auto">
                                                            Os links de checkouts com boleto estão nos hotlinks de cada curso dentro da Hotmart.                                                                    </div>
                                                                                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                                                        </div>
                        </div>
                    </li>


                </ul> <!-- end list-->
            </div> <!-- end card-->
        </div> <!-- end col-->
        <div class="col-lg-4">
            <div class="card border">
                <div class="d-flex card-header justify-content-between align-items-center">
                <h4 class="header-title">Suporte ao Parceiro</h4>
                </div>
                <div class="card-body py-0 mb-3">
                <div class="timeline-alt py-0">
                <div class="timeline-item">
                    <i class="ri-whatsapp-line text-success timeline-icon"></i>
                    <div class="timeline-item-info pb-3">
                        <a href="https://chat.whatsapp.com/Lb7F6OSFUvx1kmyp4h7kgT" target="_blank" class="text-success fw-bold mb-1 d-block">Comunidade do WhatsApp para avisos</a>
                        <small>É muito importante que você entre nesta comunidade para receber nossos avisos.</small>
                    </div>
                </div>
                <div class="timeline-item">
                    <i class="ri-file-info-line text-warning timeline-icon"></i>
                    <div class="timeline-item-info pb-3">
                        <a href="https://www.youtube.com/playlist?list=PL8UPaaNJEdSBAqMX7yxDh-aa9c5dQRA5c" target="_blank" class="text-warning fw-bold mb-1 d-block">Dúvidas Frequentes dos Parceiros</a>
                        <small>Playlist do Youtube com as principais dúvidas dos parceiros</small>
                    </div>
                </div>
                <div class="timeline-item">
                    <i class="ri-mail-send-line text-secondary timeline-icon"></i>
                    <div class="timeline-item-info pb-3">
                        <a href="mailto:parceiros@jovemempreendedor.org" target="_blank" class="text-secondary fw-bold mb-1 d-block">Suporte aos Parceiros por e-mail</a>
                        <small>Para assuntos de comissão e uso de ferramentas da estrutura.</small>
                    </div>
                </div>
                <div class="timeline-item">
                    <i class=" ri-play-circle-fill text-danger timeline-icon"></i>
                    <div class="timeline-item-info pb-3">
                        <a href="https://www.youtube.com/channel/UCY6TJnHCX56Kox0jCT9dM7g/" target="_blank" class="text-danger fw-bold mb-1 d-block">Canal do Youtube para Parceiros</a>
                        <small>Nosso canal oficial dos Parceiros do Programa Jovem Empreendedor</small>
                    </div>
                </div>
                <div class="timeline-item">
                    <i class="ri-instagram-line text-danger timeline-icon"></i>
                    <div class="timeline-item-info pb-3">
                        <a href="https://www.instagram.com/sampaio.free" target="_blank" class="text-danger fw-bold mb-1 d-block">Instagram dos Parceiros</a>
                        <small>Siga nosso Instagram para acompanhar lives e novidades.</small>
                    </div>
                </div>
                <div class="timeline-item">
                    <i class=" ri-information-line  text-info timeline-icon"></i>
                    <div class="timeline-item-info pb-3">
                        <a href="https://atendimento.hotmart.com.br/hc/pt-br/requests/new?ticket_form_id=360000433451" target="_blank" class="text-info fw-bold mb-1 d-block">Suporte da Hotmart</a>
                        <small>Troca de email, senha etc.</small>
                    </div>
                </div>
                <div class="timeline-item">
                    <i class="ri-facebook-box-fill text-primary timeline-icon"></i>
                    <div class="timeline-item-info pb-3">
                        <a href="https://www.facebook.com/business/help/" target="_blank" class="text-primary fw-bold mb-1 d-block">Ajuda META ADS</a>
                        <small>Central de ajuda para empresas meta ads.</small>
                    </div>
                </div>
                </div>
                </div>
                </div>                    </div>
    </div>   
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
        // Quando o usuário digita no campo de pesquisa
            $('#campoPesquisa').on('keyup', function() {
                var valor = $(this).val().toLowerCase(); // Pega o valor digitado, transforma em minúsculas

                // Verifica cada item do acordeão
                $('#custom-accordion-one .card').filter(function() {
                    // Alterna a visibilidade do item do acordeão com base na correspondência da pesquisa
                    $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1);
                });
            });
        });

        // Função para gerar o link e mostrar no campo de resultado
        function gerarLink() {
            const pagina = document.getElementById('selectPagina').value;
            const desconto = document.getElementById('selectCupom').value;
            const cidade = document.getElementById('inputCidade').value;

            const linkGerado = `${pagina}?d=${desconto}&c=${cidade}`;
            document.getElementById('linkResultado').value = linkGerado;
        }

        // Atualiza o link automaticamente quando há mudanças nos inputs
        document.getElementById('selectPagina').addEventListener('change', gerarLink);
        document.getElementById('selectCupom').addEventListener('change', gerarLink);
        document.getElementById('inputCidade').addEventListener('input', gerarLink);

        // Copiar link para o clipboard
        document.getElementById('copiarLink').addEventListener('click', () => {
            const linkResultado = document.getElementById('linkResultado');
            linkResultado.select();
            document.execCommand('copy');
            alert('Link copiado!');
        });

        // Chama a função no carregamento da página para gerar o link inicial
        gerarLink();

        window.addEventListener('load', function() {
            // Seleciona todos os iframes com o atributo data-src
            const iframes = document.querySelectorAll('iframe[data-src]');

            // Substitui o data-src por src para carregar os vídeos
            iframes.forEach(iframe => {
                iframe.setAttribute('src', iframe.getAttribute('data-src'));
                iframe.removeAttribute('data-src'); // Remove o atributo data-src após definir o src
            });
        });

    </script>
@endsection
