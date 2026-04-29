<?php

namespace Tests\Feature;

use App\Models\Codigo_ref;
use App\Models\Curso;
use App\Models\User;
use App\Models\WhatsappAtendimento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsappRedirectRotationTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_whatsapp_redirect_keeps_number_in_same_session_and_rotates_new_session(): void
    {
        [$user, $curso] = $this->createAffiliateCourse('afiliado-redirect.test', 'curso-redirect-rotation');

        $primeiro = WhatsappAtendimento::create([
            'user_id' => $user->id,
            'whatsapp' => '5511999991111',
            'is_active' => true,
        ]);

        $segundo = WhatsappAtendimento::create([
            'user_id' => $user->id,
            'whatsapp' => '5511999992222',
            'is_active' => true,
        ]);

        $firstResponse = $this->get("http://afiliado-redirect.test/whatsapp/curso/{$curso->url}");
        $firstLocation = $firstResponse->headers->get('Location') ?? '';

        $this->assertStringStartsWith('https://wa.me/5511999991111', $firstLocation);
        $this->assertNotNull($primeiro->refresh()->last_routed_at);
        $this->assertSame(1, $primeiro->routed_count);
        $this->assertNull($segundo->refresh()->last_routed_at);

        $secondResponse = $this->get("http://afiliado-redirect.test/whatsapp/curso/{$curso->url}");
        $secondLocation = $secondResponse->headers->get('Location') ?? '';

        $this->assertStringStartsWith('https://wa.me/5511999991111', $secondLocation);
        $this->assertSame(1, $primeiro->refresh()->routed_count);
        $this->assertNull($segundo->refresh()->last_routed_at);

        $this->app['session']->flush();

        $thirdResponse = $this->get("http://afiliado-redirect.test/whatsapp/curso/{$curso->url}");
        $thirdLocation = $thirdResponse->headers->get('Location') ?? '';

        $this->assertStringStartsWith('https://wa.me/5511999992222', $thirdLocation);
        $this->assertNotNull($segundo->refresh()->last_routed_at);
        $this->assertSame(1, $segundo->routed_count);
    }

    public function test_public_home_whatsapp_form_keeps_rendered_number_in_same_session_and_rotates_new_session(): void
    {
        [$user] = $this->createAffiliateCourse(
            'afiliado-home-rotation.test',
            'curso-home-rotation',
            'whatsapp',
            'formulario'
        );

        $primeiro = WhatsappAtendimento::create([
            'user_id' => $user->id,
            'whatsapp' => '5511999991111',
            'is_active' => true,
        ]);

        $segundo = WhatsappAtendimento::create([
            'user_id' => $user->id,
            'whatsapp' => '5511999992222',
            'is_active' => true,
        ]);

        $firstResponse = $this->get('http://afiliado-home-rotation.test/');

        $firstResponse->assertOk();
        $firstResponse->assertViewIs('home1');
        $firstResponse->assertSee('"whatsapp_atendimento":"5511999991111"', false);
        $this->assertNotNull($primeiro->refresh()->last_routed_at);
        $this->assertSame(1, $primeiro->routed_count);
        $this->assertNull($segundo->refresh()->last_routed_at);

        $secondResponse = $this->get('http://afiliado-home-rotation.test/');

        $secondResponse->assertOk();
        $secondResponse->assertViewIs('home1');
        $secondResponse->assertSee('"whatsapp_atendimento":"5511999991111"', false);
        $this->assertSame(1, $primeiro->refresh()->routed_count);
        $this->assertNull($segundo->refresh()->last_routed_at);

        $this->app['session']->flush();

        $thirdResponse = $this->get('http://afiliado-home-rotation.test/');

        $thirdResponse->assertOk();
        $thirdResponse->assertViewIs('home1');
        $thirdResponse->assertSee('"whatsapp_atendimento":"5511999992222"', false);
        $this->assertNotNull($segundo->refresh()->last_routed_at);
        $this->assertSame(1, $segundo->routed_count);
    }

    public function test_public_w3_page_uses_one_rotated_number_for_config_and_cards(): void
    {
        [$user] = $this->createAffiliateCourse(
            'afiliado-w3-rotation.test',
            'curso-w3-rotation',
            'whatsapp',
            'formulario'
        );

        $user->update([
            'home_page_layout' => 'w3',
        ]);

        $primeiro = WhatsappAtendimento::create([
            'user_id' => $user->id,
            'whatsapp' => '5511999993333',
            'is_active' => true,
        ]);

        $segundo = WhatsappAtendimento::create([
            'user_id' => $user->id,
            'whatsapp' => '5511999994444',
            'is_active' => true,
        ]);

        $response = $this->get('http://afiliado-w3-rotation.test/');

        $response->assertOk();
        $response->assertViewIs('home_e_cursos.w3');
        $response->assertSee('"whatsapp_atendimento":"5511999993333"', false);
        $response->assertSee('https://wa.me/5511999993333', false);
        $response->assertDontSee('https://wa.me/5511999994444', false);

        $this->assertNotNull($primeiro->refresh()->last_routed_at);
        $this->assertSame(1, $primeiro->routed_count);
        $this->assertNull($segundo->refresh()->last_routed_at);
        $this->assertSame(0, $segundo->routed_count);
    }

    public function test_course_whatsapp_redirect_with_t_uses_fixed_number_without_moving_rotation(): void
    {
        [$user, $curso] = $this->createAffiliateCourse('afiliado-fixed-t.test', 'curso-fixed-t');

        $rodizio = WhatsappAtendimento::create([
            'user_id' => $user->id,
            'whatsapp' => '5511999993333',
            'is_active' => true,
        ]);

        $response = $this->get("http://afiliado-fixed-t.test/whatsapp/curso/{$curso->url}?t=5511888877777");
        $location = $response->headers->get('Location') ?? '';

        $this->assertStringStartsWith('https://wa.me/5511888877777', $location);
        $this->assertNull($rodizio->refresh()->last_routed_at);
        $this->assertSame(0, $rodizio->routed_count);
    }

    public function test_float_whatsapp_redirect_uses_specific_channel_without_moving_rotation(): void
    {
        [$user] = $this->createAffiliateCourse('afiliado-float-fixed.test', 'curso-float-fixed');

        $rodizio = WhatsappAtendimento::create([
            'user_id' => $user->id,
            'whatsapp' => '5511999994444',
            'is_active' => true,
        ]);

        $fixo = WhatsappAtendimento::create([
            'user_id' => $user->id,
            'whatsapp' => '5511999995555',
            'is_active' => true,
        ]);

        $user->update([
            'w3_whatsapp_float_whatsapp_atendimento_id' => $fixo->id,
        ]);

        $response = $this->get('http://afiliado-float-fixed.test/whatsapp');
        $location = $response->headers->get('Location') ?? '';

        $this->assertStringStartsWith('https://wa.me/5511999995555', $location);
        $this->assertNull($rodizio->refresh()->last_routed_at);
        $this->assertSame(0, $rodizio->routed_count);
        $this->assertNull($fixo->refresh()->last_routed_at);
        $this->assertSame(0, $fixo->routed_count);
    }

    public function test_home1_form_renders_whatsapp_atendimento_id_and_lead_updates_last_lead_at(): void
    {
        [$user, $curso] = $this->createAffiliateCourse(
            'afiliado-form-last-lead.test',
            'curso-form-last-lead',
            'whatsapp',
            'formulario'
        );

        $whatsapp = WhatsappAtendimento::create([
            'user_id' => $user->id,
            'whatsapp' => '5511999996666',
            'is_active' => true,
        ]);

        $response = $this->get('http://afiliado-form-last-lead.test/');

        $response->assertOk();
        $response->assertViewIs('home1');
        $response->assertSee('data-whatsapp-atendimento-id="' . $whatsapp->id . '"', false);
        $response->assertSee('id="input_lead_whatsapp_atendimento_id"', false);

        $leadResponse = $this->post(route('lead_whatsapp'), [
            'nome' => 'Lead Formulario',
            'telefone' => '62999998888',
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'origem' => 'whatsapp',
            'whatsapp_atendimento_id' => $whatsapp->id,
        ]);

        $leadResponse->assertOk();
        $this->assertNotNull($whatsapp->refresh()->last_lead_at);
        $this->assertNotNull($whatsapp->last_routed_at);
        $this->assertSame(1, $whatsapp->routed_count);
    }

    public function test_course_page_whatsapp_without_form_uses_internal_redirect(): void
    {
        [$user, $curso] = $this->createAffiliateCourse('afiliado-course-direct.test', 'curso-page-direct');

        Codigo_ref::where('user_id', $user->id)
            ->where('curso_id', $curso->id)
            ->update(['formulario_pre_checkout' => false]);

        WhatsappAtendimento::create([
            'user_id' => $user->id,
            'whatsapp' => '5511999997777',
            'is_active' => true,
        ]);

        $response = $this->get('http://afiliado-course-direct.test/curso-page-direct/w');

        $response->assertOk();
        $response->assertSee('/whatsapp/curso/curso-page-direct?intent=inscricao', false);
    }

    public function test_w3_form_flow_respects_fixed_t_number(): void
    {
        [$user, $curso] = $this->createAffiliateCourse(
            'afiliado-w3-fixed-t.test',
            'curso-w3-fixed-t',
            'curso',
            'formulario'
        );

        WhatsappAtendimento::create([
            'user_id' => $user->id,
            'whatsapp' => '5511999998888',
            'is_active' => true,
        ]);

        $response = $this->get('http://afiliado-w3-fixed-t.test/w3?w=1&t=5511888811111');

        $response->assertOk();
        $response->assertViewIs('home_e_cursos.w3');
        $response->assertSee('https://wa.me/5511888811111', false);
        $response->assertSee('data-whatsapp-atendimento-id=""', false);
        $response->assertDontSee('https://wa.me/5511999998888', false);
    }

    private function createAffiliateCourse(
        string $domain,
        string $courseUrl,
        string $homeDestination = 'curso',
        string $homeWhatsappFlow = 'direto'
    ): array {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'dominio' => $domain,
            'whatsapp_atendimento' => '5511999990000',
            'home_page_layout' => 'padrao',
            'home_page_destination' => $homeDestination,
            'home_page_whatsapp_flow' => $homeWhatsappFlow,
            'formulario_whatsapp' => true,
            'formulario_pre_checkout' => true,
        ]);

        $curso = Curso::create([
            'ordem' => 1,
            'url' => $courseUrl,
            'publicado' => true,
            'permitir_afiliacao' => true,
            'mostrar_na_pagina' => true,
            'titulo' => 'Curso Redirect WhatsApp',
            'headline' => 'Curso para validar redirect interno de WhatsApp.',
            'capa_vertical' => 'img/home_page/cartaestagio.webp',
            'capa_quadrada' => 'img/home_page/cartaestagio.webp',
            'capa_horizontal' => 'img/home_page/certificadoNovo2.webp',
            'horas_completo' => 40,
            'numero_alunos' => 1200,
            'nota_avaliacao' => 4.8,
            'codigo_id_hotmart' => 'P12345678A',
            'codigo_afiliado_plano_completo' => 'abc123',
            'link_checkout_completo' => 'https://go.hotmart.com/T99999999A?ap=abc123',
            'preco_cheio_completo' => 'R$197,00',
            'preco_parcelado_completo' => '12xR$19,70',
        ]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFREDIRECT' . $curso->id,
            'mostrar_curso' => true,
        ]);

        return [$user, $curso];
    }
}
