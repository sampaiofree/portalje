<div class="bonus-grid">
    <!-- Bônus 1: Carta de Estágio -->
    <div class="bonus-card">
        <div class="bonus-image"><img loading="lazy" alt="Bônus Carta de Estágio" class="img-fluid" src="{{asset('img/home_page/cartaestagio.webp')}}"></div>
        <div class="bonus-details">
            <h4>Carta de Estágio</h4><p>Uma ferramenta poderosa para abrir portas, oferecendo um grande diferencial em seu currículo.</p>
            <div class="bonus-price"><span>De <s>R$ 197,00</s></span><span class="free-tag">GRÁTIS HOJE</span></div>
        </div>
    </div>
    <!-- Bônus 2: Preparatório Jovem Aprendiz -->
    <div class="bonus-card">
        <div class="bonus-image"><img loading="lazy" alt="Bônus Preparatório Jovem Aprendiz" class="img-fluid" src="{{asset('img/home_page/jovemaprendiz.webp')}}"></div>
        <div class="bonus-details">
            <h4>Preparatório Jovem Aprendiz</h4><p>Um treinamento completo para você se destacar nos processos seletivos.</p>
            <div class="bonus-price"><span>De <s>R$ 297,00</s></span><span class="free-tag">GRÁTIS HOJE</span></div>
        </div>
    </div>
    <!-- Outros Bônus (da lista) -->
    @if($curso->conteudo_bonus)
        @foreach($curso->conteudo_bonus as $conteudo)
        <div class="bonus-card">
            <div class="bonus-icon"><i class="fas fa-gift"></i></div>
            <div class="bonus-details">
                <h4>{{$conteudo['title']}}</h4>
                <div class="bonus-price"><span class="free-tag">INCLUSO</span></div>
            </div>
        </div>
        @endforeach
    @endif
</div>