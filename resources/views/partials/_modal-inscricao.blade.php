    <!-- Modal de Inscrição (Estrutura do seu código original) -->
    <div class="modal fade" id="inscricaoModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalLabel">@if (isset($info['whatsapp']) and $info['whatsapp']) Complete seus dados para falar com um consultor @else Preencha para garantir sua vaga! @endif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Falta pouco! Preencha seus dados abaixo para prosseguir.</p>
                    <form id="inscricaoForm" action="{{ route('lead_whatsapp') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <input type="text" class="form-control" id="input_lead_nome" name="nome" placeholder="Seu nome completo" required>
                            <div class="form-text mt-1">Será usado no seu certificado.</div>
                        </div>
                        <div class="mb-3">
                            <input minlength="13" type="tel" name="telefone" class="form-control input_telefone" placeholder="Seu melhor WhatsApp" required>
                            <div class="form-text mt-1">Para suporte e informações importantes.</div>
                        </div>
                        <input id="input_lead_user_id" type="hidden" name="user_id" value="{{isset($info['user_id']) and $info['user_id']}}">
                        <input id="input_lead_curso_id" type="hidden" name="curso_id" value="">
                        <input id="input_lead_href" type="hidden" name="link" value="">
                        <input id="input_lead_origem" type="hidden" name="origem" value="whatsapp">
                        <input id="input_lead_cidade" type="hidden" name="cidade" value="@if(isset($info['cidade']) and $info['cidade']) {{$info['cidade']}} @endif">
                        <button type="submit" class="btn btn-primary w-100">
                            @if (isset($info['whatsapp']) and $info['whatsapp']) <i class="fab fa-whatsapp"></i> Falar com Consultor @else Concluir Inscrição @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>