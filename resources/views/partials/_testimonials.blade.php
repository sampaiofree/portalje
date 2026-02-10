 <!-- Depoimentos em Vídeo -->
    <section class="section testimonials lazy-load">
        <div class="container">
            <h2 class="section-title">O Que Nossos Alunos Dizem</h2>
            <p class="section-subtitle">Histórias reais de quem transformou a carreira com nossos cursos.</p>
            
            @php
                $depoimentos = ['rejxwJ2lX-Q', '1hekoAyPVRs', 'Mnn2yIAlhZk', '9mmtunKAnMY', 'uQ5lB9r8ZlI', 'dMIxLKj35aU'];
            @endphp
            
            <div id="video-depoimentos">
                @foreach($depoimentos as $depoimento)
                <div class="lazy-load">
                    <div class="video-facade" data-video-id="{{ $depoimento }}" onclick="loadVideo(this)">
                        <img data-src="https://img.youtube.com/vi/{{ $depoimento }}/hqdefault.jpg" alt="Thumbnail do depoimento" class="lazy-img">
                        <div class="play-button"><i class="fas fa-play"></i></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>