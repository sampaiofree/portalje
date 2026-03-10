<?php

namespace Tests\Feature;

use App\Models\Codigo_ref;
use App\Models\Curso;
use App\Models\User;
use App\Models\WhatsappAtendimento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DashboardHomeLayoutPreferenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_configurar_site2_displays_global_float_button_controls(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'w3_whatsapp_float_enabled' => true,
            'w3_whatsapp_float_delay_seconds' => 0,
        ]);

        $response = $this->actingAs($user)->get('/user/configurar_site2');

        $response->assertOk();
        $response->assertSee('Botão flutuante do WhatsApp');
        $response->assertSee('id="w3_whatsapp_float_enabled"', false);
        $response->assertSee('id="w3_whatsapp_float_delay_seconds"', false);
        $response->assertSee('Configuração aplicada à home (/) e /cursos, além da W3 (/w3 e /w3/{cidade}).', false);
    }

    public function test_configurar_site2_shows_logo_remove_icons_only_when_custom_logo_exists(): void
    {
        $comLogoCustom = User::factory()->create([
            'email_verified_at' => now(),
            'logo_padrao_path' => 'uploads/user-logos/1/logo-padrao.png',
            'logo_dark_path' => 'uploads/user-logos/1/logo-dark.png',
        ]);

        $comLogoResponse = $this->actingAs($comLogoCustom)->get('/user/configurar_site2');
        $comLogoResponse->assertOk();
        $comLogoResponse->assertSee('id="remove_logo_padrao"', false);
        $comLogoResponse->assertSee('id="remove_logo_dark"', false);
        $comLogoResponse->assertSee('id="toggle_remove_logo_padrao"', false);
        $comLogoResponse->assertSee('id="toggle_remove_logo_dark"', false);

        $semLogoCustom = User::factory()->create([
            'email_verified_at' => now(),
            'logo_padrao_path' => null,
            'logo_dark_path' => null,
        ]);

        $semLogoResponse = $this->actingAs($semLogoCustom)->get('/user/configurar_site2');
        $semLogoResponse->assertOk();
        $semLogoResponse->assertSee('id="remove_logo_padrao"', false);
        $semLogoResponse->assertSee('id="remove_logo_dark"', false);
        $semLogoResponse->assertDontSee('id="toggle_remove_logo_padrao"', false);
        $semLogoResponse->assertDontSee('id="toggle_remove_logo_dark"', false);
    }

    public function test_configurar_site_post_persists_w3_float_button_settings(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'w3_whatsapp_float_enabled' => true,
            'w3_whatsapp_float_delay_seconds' => 0,
        ]);

        $response = $this->actingAs($user)
            ->from('/user/configurar_site2')
            ->post('/user/configurar_site', [
                'w3_whatsapp_float_enabled' => '0',
                'w3_whatsapp_float_delay_seconds' => '45',
            ]);

        $response->assertRedirect('/user/configurar_site2');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'w3_whatsapp_float_enabled' => 0,
            'w3_whatsapp_float_delay_seconds' => 45,
        ]);
    }

    public function test_configurar_site_post_rejects_invalid_w3_float_button_delay(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->from('/user/configurar_site2')
            ->post('/user/configurar_site', [
                'w3_whatsapp_float_enabled' => '1',
                'w3_whatsapp_float_delay_seconds' => '13',
            ]);

        $response->assertRedirect('/user/configurar_site2');
        $response->assertSessionHasErrors(['w3_whatsapp_float_delay_seconds']);
    }

    public function test_configurar_site_post_removes_custom_logos_when_remove_flags_are_enabled(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'logo_padrao_path' => 'uploads/user-logos/100/logo-padrao.png',
            'logo_dark_path' => 'uploads/user-logos/100/logo-dark.png',
        ]);

        Storage::disk('public')->put($user->logo_padrao_path, 'fake-padrao');
        Storage::disk('public')->put($user->logo_dark_path, 'fake-dark');

        $response = $this->actingAs($user)
            ->from('/user/configurar_site2')
            ->post('/user/configurar_site', [
                'remove_logo_padrao' => '1',
                'remove_logo_dark' => '1',
            ]);

        $response->assertRedirect('/user/configurar_site2');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'logo_padrao_path' => null,
            'logo_dark_path' => null,
        ]);

        Storage::disk('public')->assertMissing('uploads/user-logos/100/logo-padrao.png');
        Storage::disk('public')->assertMissing('uploads/user-logos/100/logo-dark.png');
    }

    public function test_configurar_site_post_upload_has_precedence_over_remove_flag_for_same_logo(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'logo_padrao_path' => 'uploads/user-logos/200/logo-antiga.png',
            'logo_dark_path' => null,
        ]);

        Storage::disk('public')->put($user->logo_padrao_path, 'fake-antiga');

        $novaLogoPadrao = UploadedFile::fake()->image('logo-nova-padrao.png', 300, 80);

        $response = $this->actingAs($user)
            ->from('/user/configurar_site2')
            ->post('/user/configurar_site', [
                'remove_logo_padrao' => '1',
                'logo_padrao' => $novaLogoPadrao,
            ]);

        $response->assertRedirect('/user/configurar_site2');
        $response->assertSessionHas('success');

        $userAtualizado = $user->fresh();
        $this->assertNotNull($userAtualizado->logo_padrao_path);
        $this->assertStringStartsWith('uploads/user-logos/' . $user->id . '/', $userAtualizado->logo_padrao_path);
        $this->assertStringEndsWith('.png', $userAtualizado->logo_padrao_path);
        $this->assertNotSame('uploads/user-logos/200/logo-antiga.png', $userAtualizado->logo_padrao_path);

        Storage::disk('public')->assertMissing('uploads/user-logos/200/logo-antiga.png');
        Storage::disk('public')->assertExists($userAtualizado->logo_padrao_path);
    }

    public function test_after_logo_removal_public_pages_render_fallback_logos(): void
    {
        Storage::fake('public');

        [$user, $curso] = $this->createAffiliateWithConfiguredCurso(
            'afiliado-logo-fallback.test',
            'padrao',
            'curso-logo-fallback'
        );

        $user->update([
            'logo_padrao_path' => 'uploads/user-logos/300/logo-padrao-custom.png',
            'logo_dark_path' => 'uploads/user-logos/300/logo-dark-custom.png',
        ]);

        Storage::disk('public')->put('uploads/user-logos/300/logo-padrao-custom.png', 'fake-padrao');
        Storage::disk('public')->put('uploads/user-logos/300/logo-dark-custom.png', 'fake-dark');

        $this->actingAs($user)->post('/user/configurar_site', [
            'remove_logo_padrao' => '1',
            'remove_logo_dark' => '1',
        ]);

        $homeResponse = $this->get('http://afiliado-logo-fallback.test/');
        $homeResponse->assertOk();
        $homeResponse->assertSee('img/home_page/logowhite.png', false);
        $homeResponse->assertDontSee('storage/uploads/user-logos/300/logo-dark-custom.png', false);

        $w3Response = $this->get('http://afiliado-logo-fallback.test/w3');
        $w3Response->assertOk();
        $w3Response->assertSee('img/home_page/logojecolor.webp', false);
        $w3Response->assertSee('img/home_page/logowhite.png', false);
        $w3Response->assertDontSee('storage/uploads/user-logos/300/logo-padrao-custom.png', false);
        $w3Response->assertDontSee('storage/uploads/user-logos/300/logo-dark-custom.png', false);

        $lpResponse = $this->get('http://afiliado-logo-fallback.test/' . $curso->url);
        $lpResponse->assertOk();
        $lpResponse->assertSee('img/home_page/logojecolor.webp', false);
        $lpResponse->assertSee('img/home_page/logowhite.png', false);
        $lpResponse->assertDontSee('storage/uploads/user-logos/300/logo-padrao-custom.png', false);
        $lpResponse->assertDontSee('storage/uploads/user-logos/300/logo-dark-custom.png', false);
    }

    public function test_dashboard_tools_remove_coupon_and_use_whatsapp_channel_select(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'dominio' => 'dashboard-afiliado.test',
            'whatsapp_atendimento' => '5511999999999',
            'home_page_layout' => 'padrao',
            'home_page_destination' => 'curso',
        ]);

        $active = WhatsappAtendimento::create([
            'user_id' => $user->id,
            'whatsapp' => '5511981111111',
            'is_active' => true,
        ]);

        $inactive = WhatsappAtendimento::create([
            'user_id' => $user->id,
            'whatsapp' => '5511982222222',
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)->get('/user/dashboard');

        $response->assertOk();
        $response->assertSee('Configure sua home');
        $response->assertSee('id="home_settings_model_select"', false);
        $response->assertSee('id="home_settings_destination_select"', false);
        $response->assertSee('id="home_settings_whatsapp_flow_select"', false);
        $response->assertSee('Fluxo do WhatsApp na home');
        $response->assertSee('Gerador de links');
        $response->assertSee('Escolha o modelo da sua home');
        $response->assertSee('Modelo 1');
        $response->assertSee('Modelo 2');
        $response->assertSee('Escolha o destino da home');
        $response->assertSee('Página do curso');
        $response->assertSee('WhatsApp');
        $response->assertSee('Mostrar nome da cidade?');
        $response->assertSee('x-data="configureHomeBuilder($el)"', false);
        $response->assertDontSee('saveHomeLayoutPreference');
        $response->assertDontSee('Cupom de Desconto');
        $response->assertDontSee('WhatsApp Alternativo (opcional)');
        $response->assertSee('id="whatsapp_channel_select"', false);
        $response->assertSee('Rodízio (automático)');

        $html = $response->getContent();
        $this->assertMatchesRegularExpression('/<select[^>]*id="whatsapp_channel_select"[^>]*>.*?<\/select>/s', $html);

        preg_match('/<select[^>]*id="whatsapp_channel_select"[^>]*>(.*?)<\/select>/s', $html, $matches);
        $selectContent = $matches[1] ?? '';

        $this->assertStringContainsString('value="' . $active->whatsapp . '"', $selectContent);
        $this->assertStringNotContainsString('value="' . $inactive->whatsapp . '"', $selectContent);
    }

    public function test_update_home_page_layout_endpoint_persists_valid_values(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'home_page_layout' => 'padrao',
            'home_page_destination' => 'curso',
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('alterar_home_page_layout'), [
                'home_page_layout' => 'w3',
                'home_page_destination' => 'whatsapp',
                'home_page_whatsapp_flow' => 'direto',
            ]);

        $response->assertOk();
        $response->assertJsonFragment(['home_page_layout' => 'w3']);
        $response->assertJsonFragment(['home_page_destination' => 'whatsapp']);
        $response->assertJsonFragment(['home_page_whatsapp_flow' => 'direto']);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'home_page_layout' => 'w3',
            'home_page_destination' => 'whatsapp',
            'home_page_whatsapp_flow' => 'direto',
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('alterar_home_page_layout'), [
                'home_page_layout' => 'padrao',
                'home_page_destination' => 'curso',
            ]);

        $response->assertOk();
        $response->assertJsonFragment(['home_page_layout' => 'padrao']);
        $response->assertJsonFragment(['home_page_destination' => 'curso']);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'home_page_layout' => 'padrao',
            'home_page_destination' => 'curso',
        ]);
    }

    public function test_update_home_page_layout_rejects_invalid_value(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('alterar_home_page_layout'), [
                'home_page_layout' => 'invalido',
                'home_page_destination' => 'curso',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['home_page_layout']);
    }

    public function test_update_home_page_layout_rejects_invalid_destination_value(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('alterar_home_page_layout'), [
                'home_page_layout' => 'w3',
                'home_page_destination' => 'invalido',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['home_page_destination']);
    }

    public function test_update_home_page_layout_rejects_invalid_whatsapp_flow_value(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('alterar_home_page_layout'), [
                'home_page_layout' => 'padrao',
                'home_page_destination' => 'whatsapp',
                'home_page_whatsapp_flow' => 'invalido',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['home_page_whatsapp_flow']);
    }

    public function test_update_home_page_layout_requires_whatsapp_flow_when_destination_is_whatsapp(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('alterar_home_page_layout'), [
                'home_page_layout' => 'padrao',
                'home_page_destination' => 'whatsapp',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['home_page_whatsapp_flow']);
    }

    public function test_root_renders_w3_when_affiliate_home_layout_is_w3(): void
    {
        [$user, $curso] = $this->createAffiliateWithConfiguredCurso('afiliado-w3-root.test', 'w3', 'curso-w3-root');

        $response = $this->get('http://afiliado-w3-root.test/');

        $response->assertOk();
        $response->assertViewIs('home_e_cursos.w3');
        $response->assertSee('4 motivos para você fazer um curso profissionalizante');
        $response->assertSee('class="w3-course-card js-course-trigger"', false);
    }

    public function test_root_w3_layout_uses_course_destination_by_default_and_whatsapp_with_w_query(): void
    {
        [$user, $curso] = $this->createAffiliateWithConfiguredCurso('afiliado-w3-modo.test', 'w3', 'curso-w3-modo', 'curso');

        $defaultResponse = $this->get('http://afiliado-w3-modo.test/');
        $defaultResponse->assertOk();
        $defaultResponse->assertSee('data-origem="curso"', false);

        $whatsResponse = $this->get('http://afiliado-w3-modo.test/?w=1');
        $whatsResponse->assertOk();
        $whatsResponse->assertSee('data-origem="whatsapp"', false);
    }

    public function test_root_w3_layout_uses_saved_whatsapp_destination_without_query(): void
    {
        [$user, $curso] = $this->createAffiliateWithConfiguredCurso('afiliado-w3-whatsapp-default.test', 'w3', 'curso-w3-whatsapp-default', 'whatsapp');

        $response = $this->get('http://afiliado-w3-whatsapp-default.test/');

        $response->assertOk();
        $response->assertSee('data-origem="whatsapp"', false);
    }

    public function test_root_keeps_home1_when_affiliate_home_layout_is_padrao(): void
    {
        [$user, $curso] = $this->createAffiliateWithConfiguredCurso('afiliado-padrao-root.test', 'padrao', 'curso-padrao-root');

        $response = $this->get('http://afiliado-padrao-root.test/');

        $response->assertOk();
        $response->assertViewIs('home1');
        $response->assertSee('Escolha Sua Nova Profissão');
    }

    public function test_root_home_whatsapp_with_formulario_keeps_pre_whatsapp_modal(): void
    {
        [$user, $curso] = $this->createAffiliateWithConfiguredCurso('afiliado-home-whatsapp-form.test', 'padrao', 'curso-home-whatsapp-form', 'whatsapp', 'formulario');

        $response = $this->get('http://afiliado-home-whatsapp-form.test/');

        $response->assertOk();
        $response->assertViewIs('home1');
        $response->assertSee('https://wa.me/', false);
        $response->assertSee("window.modal = new bootstrap.Modal(document.getElementById('inscricaoModal'));", false);
    }

    public function test_root_home_whatsapp_with_direto_skips_pre_whatsapp_modal(): void
    {
        [$user, $curso] = $this->createAffiliateWithConfiguredCurso('afiliado-home-whatsapp-direto.test', 'padrao', 'curso-home-whatsapp-direto', 'whatsapp', 'direto');

        $response = $this->get('http://afiliado-home-whatsapp-direto.test/');

        $response->assertOk();
        $response->assertViewIs('home1');
        $response->assertSee('https://wa.me/', false);
        $response->assertDontSee("window.modal = new bootstrap.Modal(document.getElementById('inscricaoModal'));", false);
    }

    public function test_root_w3_home_whatsapp_with_direto_disables_pre_whatsapp_form_only_on_root(): void
    {
        [$user, $curso] = $this->createAffiliateWithConfiguredCurso('afiliado-w3-home-whatsapp-direto.test', 'w3', 'curso-w3-home-whatsapp-direto', 'whatsapp', 'direto');

        $rootResponse = $this->get('http://afiliado-w3-home-whatsapp-direto.test/');
        $rootResponse->assertOk();
        $rootResponse->assertViewIs('home_e_cursos.w3');
        $rootResponse->assertSee('data-origem="whatsapp"', false);
        $rootResponse->assertSee('"whatsapp_requires_form":false', false);
        $rootResponse->assertSee('https://wa.me/5511999999999?text=Olá, quero saber mais sobre o curso de Curso Teste Home Layout', false);
        $rootResponse->assertDontSee('{nome}', false);

        $w3Response = $this->get('http://afiliado-w3-home-whatsapp-direto.test/w3?w=1');
        $w3Response->assertOk();
        $w3Response->assertViewIs('home_e_cursos.w3');
        $w3Response->assertSee('"whatsapp_requires_form":true', false);
    }

    public function test_cursos_route_remains_home1_even_when_affiliate_layout_is_w3(): void
    {
        [$user, $curso] = $this->createAffiliateWithConfiguredCurso('afiliado-w3-cursos.test', 'w3', 'curso-w3-cursos');

        $response = $this->get('http://afiliado-w3-cursos.test/cursos');

        $response->assertOk();
        $response->assertViewIs('home1');
        $response->assertSee('Escolha Sua Nova Profissão');
    }

    public function test_home1_root_and_cursos_hide_float_button_when_affiliate_disables_it(): void
    {
        [$user, $curso] = $this->createAffiliateWithConfiguredCurso('afiliado-home1-float-off.test', 'padrao', 'curso-home1-float-off');

        $user->update([
            'w3_whatsapp_float_enabled' => false,
            'w3_whatsapp_float_delay_seconds' => 30,
        ]);

        $rootResponse = $this->get('http://afiliado-home1-float-off.test/');
        $rootResponse->assertOk();
        $rootResponse->assertViewIs('home1');
        $rootResponse->assertSee('id="whatsapp_botao"', false);
        $rootResponse->assertSee('id="home-course-config"', false);
        $rootResponse->assertSee('"whatsapp_show":false', false);
        $rootResponse->assertSee('"whatsapp_delay_seconds":30', false);

        $cursosResponse = $this->get('http://afiliado-home1-float-off.test/cursos');
        $cursosResponse->assertOk();
        $cursosResponse->assertViewIs('home1');
        $cursosResponse->assertSee('id="whatsapp_botao"', false);
        $cursosResponse->assertSee('id="home-course-config"', false);
        $cursosResponse->assertSee('"whatsapp_show":false', false);
        $cursosResponse->assertSee('"whatsapp_delay_seconds":30', false);
    }

    public function test_home1_root_and_cursos_use_affiliate_float_button_delay_when_enabled(): void
    {
        [$user, $curso] = $this->createAffiliateWithConfiguredCurso('afiliado-home1-float-on.test', 'padrao', 'curso-home1-float-on');

        $user->update([
            'w3_whatsapp_float_enabled' => true,
            'w3_whatsapp_float_delay_seconds' => 30,
        ]);

        $rootResponse = $this->get('http://afiliado-home1-float-on.test/');
        $rootResponse->assertOk();
        $rootResponse->assertViewIs('home1');
        $rootResponse->assertSee('id="whatsapp_botao"', false);
        $rootResponse->assertSee('id="home-course-config"', false);
        $rootResponse->assertSee('"whatsapp_show":true', false);
        $rootResponse->assertSee('"whatsapp_delay_seconds":30', false);

        $cursosResponse = $this->get('http://afiliado-home1-float-on.test/cursos');
        $cursosResponse->assertOk();
        $cursosResponse->assertViewIs('home1');
        $cursosResponse->assertSee('id="whatsapp_botao"', false);
        $cursosResponse->assertSee('id="home-course-config"', false);
        $cursosResponse->assertSee('"whatsapp_show":true', false);
        $cursosResponse->assertSee('"whatsapp_delay_seconds":30', false);
    }

    /**
     * @return array{0: User, 1: Curso}
     */
    private function createAffiliateWithConfiguredCurso(
        string $dominio,
        string $layout,
        string $url,
        string $destination = 'curso',
        string $whatsappFlow = 'formulario'
    ): array
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'dominio' => $dominio,
            'whatsapp_atendimento' => '5511999999999',
            'home_page_layout' => $layout,
            'home_page_destination' => $destination,
            'home_page_whatsapp_flow' => $whatsappFlow,
            'w3_whatsapp_float_enabled' => true,
            'w3_whatsapp_float_delay_seconds' => 0,
            'formulario_whatsapp' => true,
            'formulario_pre_checkout' => true,
        ]);

        $curso = Curso::create([
            'ordem' => 1,
            'url' => $url,
            'publicado' => true,
            'permitir_afiliacao' => true,
            'mostrar_na_pagina' => true,
            'titulo' => 'Curso Teste Home Layout',
            'headline' => 'Curso de teste para validar home padrão ou W3.',
            'capa_vertical' => 'img/home_page/cartaestagio.webp',
            'capa_quadrada' => 'img/home_page/cartaestagio.webp',
            'capa_horizontal' => 'img/home_page/certificadoNovo2.webp',
            'horas_completo' => 40,
            'numero_alunos' => 1200,
            'nota_avaliacao' => 4.8,
            'codigo_afiliado_plano_completo' => 'abc123',
            'link_checkout_completo' => 'https://go.hotmart.com/abc123?ap=abc123',
            'preco_cheio_completo' => 'R$197,00',
            'preco_parcelado_completo' => '12xR$19,70',
        ]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFHOME' . $curso->id,
            'mostrar_curso' => true,
        ]);

        return [$user, $curso];
    }
}
