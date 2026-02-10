<div class="investment-box">
    <!-- Coluna da Esquerda: O que está incluso -->
    <div class="investment-includes">
        <h4>Seu Acesso Completo Inclui:</h4>
        <ul>
            <li><i class="fas fa-check"></i> Curso Completo de {{ $curso->titulo }}</li>
            <li><i class="fas fa-check"></i> Certificado Digital Válido de {{ $curso->horas_completo }} horas</li>
            <li><i class="fas fa-check"></i> Suporte Direto com o Professor</li>
            <li><i class="fas fa-check"></i> Acesso por 02 anos ao Conteúdo</li>
            <li><i class="fas fa-gift"></i> <strong>Pacote de Bônus Exclusivos</strong></li>
        </ul>
    </div>
    <!-- Coluna da Direita: Preço e Ação -->
    <div class="investment-action">
        <h5>INVESTIMENTO ÚNICO</h5>
        <div class="main-price">
            {{ $curso->preco_parcelado_completo }}
            <small>ou {{ $curso->preco_cheio_completo }} à vista</small>
        </div>
        <a href="{{ $curso->link_checkout_completo }}" 
           class="btn btn-primary btn-inscricao w-100" 
           data-bs-toggle="modal" 
           data-bs-target="#inscricaoModal"
           data-cursoid="{{$curso->id}}">
            <i class="fas fa-lock"></i> Garantir Minha Vaga Agora
        </a>
        <p class="guarantee-text"><i class="fas fa-shield-alt"></i> Risco Zero! 7 dias de garantia.</p>
        <div class="payment-methods"><img src="{{asset('img/icons/pagamentos.webp')}}" alt="Formas de Pagamento"></div>
    </div>
</div>
<style>
    .investment-action {
            background: white;
            padding: 40px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .investment-action h5 {
            font-size: 0.9rem;
            color: var(--light-text);
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
</style>