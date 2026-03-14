<?php

namespace Tests\Feature;

use App\Models\Cupom;
use App\Models\Curso;
use App\Models\RootDomainCourseConfig;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRootDomainCourseConfigTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_root_domain_course_pages_screen(): void
    {
        $admin = $this->createAdmin();
        $curso = $this->createPublishedCourse('curso-admin-root-screen', [
            'titulo' => 'Curso Admin Root',
            'permitir_afiliacao' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.root_domain_course_pages'));

        $response->assertOk();
        $response->assertSee('Páginas dos Domínios');
        $response->assertSee('Configuração compartilhada dos domínios raiz');
        $response->assertSee('portalje.org e jovemempreendedor.org');
        $response->assertSee('dns.portalje.org e dns.jovemempreendedor.org');
        $response->assertSee('Curso Admin Root');
        $response->assertSee('Mostrar curso nos domínios raiz?');
        $response->assertSee('Configurações da página pública');
        $response->assertSee('Usar contador?');
        $response->assertDontSee('Código REF');
        $response->assertDontSee('Gerador da Página de Vendas');
        $response->assertDontSee('Gerador do Checkout');
        $response->assertDontSee('links personalizados');
    }

    public function test_non_admin_cannot_open_root_domain_course_pages_screen(): void
    {
        $user = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_USER,
        ]);

        $response = $this->actingAs($user)->get(route('admin.root_domain_course_pages'));

        $response->assertRedirect('/login');
    }

    public function test_admin_can_create_and_update_root_domain_course_config_with_normalization(): void
    {
        $admin = $this->createAdmin();
        $curso = $this->createPublishedCourse('curso-admin-root-save');
        $cupomPrincipal = Cupom::create(['codigo' => 'ROOT10', 'desconto' => 10]);
        $cupomSecundario = Cupom::create(['codigo' => 'ROOT35', 'desconto' => 35]);

        $createResponse = $this->actingAs($admin)->postJson(route('admin.root_domain_course_pages.save'), [
            'curso_id' => $curso->id,
            'mostrar_curso' => '1',
            'formulario_pre_checkout' => '0',
            'modo_precos' => 'dois_precos',
            'cupom_principal_id' => (string) $cupomPrincipal->id,
            'cupom_secundario_id' => (string) $cupomSecundario->id,
            'usar_contador' => '1',
            'contador_minutos' => '10',
            'contador_acao' => 'alterar_preco',
            'contador_destino_oferta' => 'basico_cupom:' . $cupomSecundario->id,
        ]);

        $createResponse
            ->assertOk()
            ->assertJsonPath('curso_id', $curso->id)
            ->assertJsonPath('mostrar_curso', true)
            ->assertJsonPath('formulario_pre_checkout', false)
            ->assertJsonPath('modo_precos', 'dois_precos')
            ->assertJsonPath('cupom_principal_id', $cupomPrincipal->id)
            ->assertJsonPath('cupom_secundario_id', $cupomSecundario->id)
            ->assertJsonPath('usar_contador', true)
            ->assertJsonPath('contador_minutos', 10)
            ->assertJsonPath('contador_acao', 'alterar_preco')
            ->assertJsonPath('contador_destino_oferta', 'basico_cupom:' . $cupomSecundario->id);

        $this->assertDatabaseHas('root_domain_course_configs', [
            'curso_id' => $curso->id,
            'mostrar_curso' => 1,
            'formulario_pre_checkout' => 0,
            'modo_precos' => 'dois_precos',
            'cupom_principal_id' => $cupomPrincipal->id,
            'cupom_secundario_id' => $cupomSecundario->id,
            'usar_contador' => 1,
            'contador_minutos' => 10,
            'contador_acao' => 'alterar_preco',
            'contador_destino_oferta' => 'basico_cupom:' . $cupomSecundario->id,
        ]);

        $updateResponse = $this->actingAs($admin)->postJson(route('admin.root_domain_course_pages.save'), [
            'curso_id' => $curso->id,
            'mostrar_curso' => '0',
            'formulario_pre_checkout' => '1',
            'modo_precos' => 'padrao',
            'cupom_principal_id' => (string) $cupomPrincipal->id,
            'cupom_secundario_id' => (string) $cupomSecundario->id,
            'usar_contador' => '0',
            'contador_minutos' => '5',
            'contador_acao' => 'nada',
            'contador_destino_oferta' => 'completo_padrao',
        ]);

        $updateResponse
            ->assertOk()
            ->assertJsonPath('curso_id', $curso->id)
            ->assertJsonPath('mostrar_curso', false)
            ->assertJsonPath('formulario_pre_checkout', true)
            ->assertJsonPath('modo_precos', 'padrao')
            ->assertJsonPath('cupom_principal_id', null)
            ->assertJsonPath('cupom_secundario_id', null)
            ->assertJsonPath('usar_contador', false)
            ->assertJsonPath('contador_minutos', null)
            ->assertJsonPath('contador_acao', null)
            ->assertJsonPath('contador_destino_oferta', null);

        $this->assertSame(1, RootDomainCourseConfig::query()->where('curso_id', $curso->id)->count());
        $this->assertDatabaseHas('root_domain_course_configs', [
            'curso_id' => $curso->id,
            'mostrar_curso' => 0,
            'formulario_pre_checkout' => 1,
            'modo_precos' => 'padrao',
            'cupom_principal_id' => null,
            'cupom_secundario_id' => null,
            'usar_contador' => 0,
            'contador_minutos' => null,
            'contador_acao' => null,
            'contador_destino_oferta' => null,
        ]);
    }

    public function test_admin_root_domain_config_rejects_invalid_countdown_combinations(): void
    {
        $admin = $this->createAdmin();
        $curso = $this->createPublishedCourse('curso-admin-root-invalid');
        $cupom = Cupom::create(['codigo' => 'ROOT15', 'desconto' => 15]);

        $encerrarBasicoResponse = $this->actingAs($admin)->postJson(route('admin.root_domain_course_pages.save'), [
            'curso_id' => $curso->id,
            'mostrar_curso' => '1',
            'formulario_pre_checkout' => '1',
            'modo_precos' => 'um_preco',
            'cupom_principal_id' => (string) $cupom->id,
            'usar_contador' => '1',
            'contador_minutos' => '5',
            'contador_acao' => 'encerrar_basico',
        ]);

        $encerrarBasicoResponse
            ->assertStatus(422)
            ->assertJsonValidationErrors(['contador_acao']);

        $destinoInvalidoResponse = $this->actingAs($admin)->postJson(route('admin.root_domain_course_pages.save'), [
            'curso_id' => $curso->id,
            'mostrar_curso' => '1',
            'formulario_pre_checkout' => '1',
            'modo_precos' => 'um_preco',
            'cupom_principal_id' => (string) $cupom->id,
            'usar_contador' => '1',
            'contador_minutos' => '5',
            'contador_acao' => 'alterar_preco',
            'contador_destino_oferta' => 'basico_padrao',
        ]);

        $destinoInvalidoResponse
            ->assertStatus(422)
            ->assertJsonValidationErrors(['contador_destino_oferta']);
    }

    public function test_root_domains_use_shared_config_for_public_course_pages(): void
    {
        $curso = $this->createPublishedCourse('curso-root-public-config');
        $cupom = Cupom::create(['codigo' => 'ROOT25', 'desconto' => 25]);

        RootDomainCourseConfig::create([
            'curso_id' => $curso->id,
            'mostrar_curso' => true,
            'formulario_pre_checkout' => false,
            'modo_precos' => 'um_preco',
            'cupom_principal_id' => $cupom->id,
            'usar_contador' => true,
            'contador_minutos' => 5,
            'contador_acao' => 'alterar_preco',
            'contador_destino_oferta' => 'completo_cupom:' . $cupom->id,
        ]);

        foreach ([
            'http://portalje.org/' . $curso->url,
            'http://jovemempreendedor.org/' . $curso->url,
            'http://dns.portalje.org/' . $curso->url,
            'http://dns.jovemempreendedor.org/' . $curso->url,
        ] as $url) {
            $response = $this->get($url);
            $html = $response->getContent();
            $config = $this->extractLpCourseConfig($html);

            $response->assertOk();
            $response->assertSee('Plano Completo');
            $response->assertDontSee('Plano Básico');
            $response->assertSee('offDiscount=ROOT25', false);
            $response->assertSee('data-requires-lead="0"', false);
            $this->assertTrue((bool) data_get($config, 'countdown.enabled'));
            $this->assertSame(5, data_get($config, 'countdown.minutes'));
            $this->assertSame('alterar_preco', data_get($config, 'countdown.action'));
            $this->assertSame('completo_cupom:' . $cupom->id, data_get($config, 'countdown.destination_offer'));
            $this->assertSame('um_preco', data_get($config, 'modo_precos'));
        }
    }

    public function test_root_domain_listing_respects_shared_visibility_and_fallback(): void
    {
        $curso = $this->createPublishedCourse('curso-root-listing-visibility', [
            'titulo' => 'Curso Root Visibility',
            'mostrar_na_pagina' => true,
        ]);

        $fallbackResponse = $this->get('http://portalje.org/');
        $fallbackResponse->assertOk();
        $fallbackResponse->assertSee('Curso Root Visibility');

        $fallbackDnsResponse = $this->get('http://dns.portalje.org/');
        $fallbackDnsResponse->assertOk();
        $fallbackDnsResponse->assertSee('Curso Root Visibility');

        RootDomainCourseConfig::create([
            'curso_id' => $curso->id,
            'mostrar_curso' => false,
            'formulario_pre_checkout' => true,
            'modo_precos' => 'padrao',
            'usar_contador' => false,
        ]);

        $hiddenResponse = $this->get('http://portalje.org/');
        $hiddenResponse->assertOk();
        $hiddenResponse->assertDontSee('Curso Root Visibility');

        $hiddenDnsResponse = $this->get('http://dns.jovemempreendedor.org/');
        $hiddenDnsResponse->assertOk();
        $hiddenDnsResponse->assertDontSee('Curso Root Visibility');
    }

    private function createAdmin(): User
    {
        return User::factory()->create([
            'email_verified_at' => now(),
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
        ]);
    }

    private function createPublishedCourse(string $url, array $overrides = []): Curso
    {
        return Curso::create(array_merge([
            'ordem' => 1,
            'titulo' => 'Curso Teste Root Domain',
            'url' => $url,
            'publicado' => true,
            'permitir_afiliacao' => true,
            'mostrar_na_pagina' => true,
            'headline' => 'Headline de teste para domínios raiz.',
            'preco_parcelado_completo' => '12xR$19,70',
            'preco_cheio_completo' => 'R$197',
            'codigo_afiliado_plano_completo' => 'abc123',
            'link_checkout_completo' => 'https://go.hotmart.com/T99999999A?ap=abc123',
            'areas_de_atuacao' => 'Atendimento/Vendas',
            'horas_completo' => 120,
            'numero_alunos' => 1200,
            'nota_avaliacao' => 4.8,
        ], $overrides));
    }

    private function extractLpCourseConfig(string $html): array
    {
        $matched = preg_match('/<script id="lp-course-config" type="application\/json">(.*?)<\/script>/s', $html, $matches);
        if ($matched !== 1 || empty($matches[1])) {
            $this->fail('Bloco lp-course-config não encontrado na LP pública.');
        }

        $decoded = json_decode(html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8'), true);
        if (!is_array($decoded)) {
            $this->fail('Configuração JSON da LP pública está inválida.');
        }

        return $decoded;
    }
}
