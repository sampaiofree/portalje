<?php

namespace Tests\Feature;

use App\Models\Codigo_ref;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class W3PageRedesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_w3_renders_new_layout_preserving_copy_sequence_and_tracking_contract(): void
    {
        [$user, $curso] = $this->createAffiliateAndCursoForW3('curso-w3-redesign');

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFW3REDESIGN',
            'mostrar_curso' => true,
        ]);

        $response = $this->get('http://afiliado.test/w3');
        $html = $response->getContent();

        $response->assertOk();
        $response->assertSeeInOrder([
            '4 motivos para você fazer um curso profissionalizante',
            'Por que estudar no Portal Jovem Empreendedor?',
            'Benefícios de você entrar no Programa',
            'Conheça o curso que vai fazer você entrar no mercado de trabalho mais rápido,',
            'São mais de 120 mil alunos no Brasil e em 14 países',
            'Ao se inscrever agora',
            'Perguntas e Respostas',
        ]);

        $this->assertSame(1, substr_count(strtolower($html), '<h1'));
        $this->assertSame(0, substr_count($html, 'id="imgTOPO"'));

        $response->assertSee('Cursos Profissionalizantes');
        $response->assertSee('Nao pague mensalidades');
        $response->assertDontSee('id="imgTOPODesktop"', false);
        $response->assertDontSee('id="imgTOPOMobile"', false);
        $response->assertSee('class="w3-course-card js-course-trigger"', false);
        $response->assertSee('id="modal_form_lead"', false);
        $response->assertSee('id="w3-course-config"', false);
        $response->assertSee('id="whatsapp_botao"', false);
        $response->assertSee('"pixel_id":123456', false);
        $response->assertSee('class="w3-testimonial"', false);
        $response->assertSee('class="w3-testimonial__trigger js-testimonial-trigger"', false);
        $response->assertSee('class="w3-testimonial__play-icon"', false);
        $response->assertSee('loading="lazy"', false);
        $response->assertDontSee('youtube-nocookie.com/embed', false);

        $videoIds = [
            'rejxwJ2lX-Q',
            '1hekoAyPVRs',
            'Mnn2yIAlhZk',
            '9mmtunKAnMY',
            'uQ5lB9r8ZlI',
            'dMIxLKj35aU',
            'gIV1MGief-0',
            'X1IJZkVXgBw',
            '1qWXa9F0qBw',
        ];

        foreach ($videoIds as $videoId) {
            $response->assertSee('data-video-id="' . $videoId . '"', false);
            $response->assertSee('https://img.youtube.com/vi/' . $videoId . '/hqdefault.jpg', false);
        }
    }

    public function test_w4_redirects_permanently_to_w3_preserving_city_and_query_string(): void
    {
        $response = $this->get('/w4/campinas?src=meta&sck=remarketing');

        $response->assertStatus(301);

        $location = $response->headers->get('Location') ?? '';

        $this->assertStringContainsString('/w3/campinas', $location);
        $this->assertStringContainsString('src=meta', $location);
        $this->assertStringContainsString('sck=remarketing', $location);
    }

    public function test_w3_uses_route_city_as_whatsapp_compat_and_query_city_as_course_mode(): void
    {
        [$user, $curso] = $this->createAffiliateAndCursoForW3('curso-w3-destino');

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFW3DESTINO',
            'mostrar_curso' => true,
        ]);

        $courseResponse = $this->get('http://afiliado.test/w3?c=campinas');
        $courseResponse->assertOk();
        $courseResponse->assertSee('Clique no curso para abrir a página do curso.');
        $courseResponse->assertSee('data-origem="curso"', false);
        $courseResponse->assertSee('data-link="https://afiliado.test/curso-w3-destino?c=campinas"', false);

        $whatsResponse = $this->get('http://afiliado.test/w3/campinas');
        $whatsResponse->assertOk();
        $whatsResponse->assertSee('Clique no curso para falar com nosso consultor no WhatsApp.');
        $whatsResponse->assertSee('data-origem="whatsapp"', false);
        $whatsResponse->assertSee('https://wa.me/', false);
    }

    public function test_w3_hides_float_button_when_affiliate_disables_it(): void
    {
        [$user, $curso] = $this->createAffiliateAndCursoForW3('curso-w3-float-off');

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFW3FLOATOFF',
            'mostrar_curso' => true,
        ]);

        $user->update([
            'w3_whatsapp_float_enabled' => false,
            'w3_whatsapp_float_delay_seconds' => 30,
        ]);

        $response = $this->get('http://afiliado.test/w3');
        $response->assertOk();
        $response->assertSee('"whatsapp_show":false', false);
        $response->assertSee('"whatsapp_delay_seconds":30', false);

        $cityResponse = $this->get('http://afiliado.test/w3/campinas');
        $cityResponse->assertOk();
        $cityResponse->assertSee('"whatsapp_show":false', false);
        $cityResponse->assertSee('"whatsapp_delay_seconds":30', false);
    }

    public function test_w3_uses_affiliate_delay_when_float_button_is_enabled(): void
    {
        [$user, $curso] = $this->createAffiliateAndCursoForW3('curso-w3-float-on');

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFW3FLOATON',
            'mostrar_curso' => true,
        ]);

        $user->update([
            'w3_whatsapp_float_enabled' => true,
            'w3_whatsapp_float_delay_seconds' => 30,
        ]);

        $response = $this->get('http://afiliado.test/w3');
        $response->assertOk();
        $response->assertSee('"whatsapp_show":true', false);
        $response->assertSee('"whatsapp_delay_seconds":30', false);
    }

    /**
     * @return array{0: User, 1: Curso}
     */
    private function createAffiliateAndCursoForW3(string $url): array
    {
        $user = User::factory()->create([
            'dominio' => 'afiliado.test',
            'whatsapp_atendimento' => '5511999999999',
            'whatsapp_atendimento_tempo' => '5',
            'w3_whatsapp_float_enabled' => true,
            'w3_whatsapp_float_delay_seconds' => 0,
            'meta_pixel_id' => '123456',
            'formulario_whatsapp' => true,
            'formulario_pre_checkout' => true,
        ]);

        $curso = Curso::create([
            'ordem' => 1,
            'url' => $url,
            'publicado' => true,
            'permitir_afiliacao' => true,
            'mostrar_na_pagina' => true,
            'titulo' => 'Curso Teste W3',
            'headline' => 'Headline de teste do curso para validar o novo layout da página W3.',
            'capa_vertical' => 'img/home_page/cartaestagio.webp',
            'horas_completo' => 40,
            'numero_alunos' => 1280,
            'nota_avaliacao' => 4.9,
        ]);

        return [$user, $curso];
    }
}
