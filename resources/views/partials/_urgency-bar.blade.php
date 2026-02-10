    @if(isset($info['cidade']) and $info['cidade'])
    <div class="urgency-bar">
        <p class="m-0">Bolsas de Estudo Liberadas para <strong class="text-uppercase">{{$info['cidade']}}</strong>! Vagas Limitadas!</p>
    </div>
    @elseif(isset($desconto_banner) and $desconto_banner)
    <div class="urgency-bar">
        <p class="m-0">🔥 Desconto Especial de {{$desconto_banner}}%! Termina em <span class="countdown"></span></p>
    </div>
    @endif 