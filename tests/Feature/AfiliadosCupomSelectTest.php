<?php

namespace Tests\Feature;

use App\Http\Middleware\MinhaJornada;
use App\Models\Codigo_ref;
use App\Models\Cupom;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AfiliadosCupomSelectTest extends TestCase
{
    use RefreshDatabase;

    public function test_afiliados_cadastrar_curso_renders_dynamic_coupon_options(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        Curso::create([
            'titulo' => 'Curso Teste',
            'url' => 'curso-teste',
            'publicado' => true,
            'permitir_afiliacao' => true,
            'preco_cheio_completo' => 'R$197',
            'codigo_afiliado_plano_completo' => 'abc123',
            'link_checkout_completo' => 'https://go.hotmart.com/T99999999A?ap=abc123',
        ]);

        Cupom::create(['codigo' => '10OFF', 'desconto' => 10]);
        Cupom::create(['codigo' => '25OFF', 'desconto' => 25]);

        $response = $this->actingAs($user)->get(route('cadastrar_cursos'));

        $response->assertOk();
        $response->assertSee('Sem cupom - R$197,00');
        $response->assertSee('10% OFF (10OFF) - R$177,30');
        $response->assertSee('25% OFF (25OFF) - R$147,75');
        $response->assertSee('value="10OFF"', false);
        $response->assertSee('value="25OFF"', false);
    }

    public function test_afiliados_cadastrar_curso_renders_public_page_settings_collapsed_accordion(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = Curso::create([
            'titulo' => 'Curso Configurado',
            'url' => 'curso-configurado',
            'publicado' => true,
            'permitir_afiliacao' => true,
            'preco_cheio_completo' => 'R$197',
            'codigo_afiliado_plano_completo' => 'abc123',
            'link_checkout_completo' => 'https://go.hotmart.com/T99999999A?ap=abc123',
        ]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFCOLAPSO123',
            'mostrar_curso' => true,
            'modo_precos' => 'padrao',
        ]);

        $response = $this->actingAs($user)->get(route('cadastrar_cursos'));

        $response->assertOk();
        $response->assertSee('Configurações da página pública');
        $response->assertSee('publicPageConfigExpanded: false', false);
        $response->assertSee('@click="publicPageConfigExpanded = !publicPageConfigExpanded"', false);
        $response->assertSee(':class="publicPageConfigExpanded ? \'rotate-180\' : \'\'"', false);
    }

    public function test_afiliados_cadastrar_curso_renders_bulk_actions_dropdown(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        Curso::create([
            'titulo' => 'Curso Com Ações',
            'url' => 'curso-com-acoes',
            'publicado' => true,
            'permitir_afiliacao' => true,
            'preco_cheio_completo' => 'R$197',
            'codigo_afiliado_plano_completo' => 'abc123',
            'link_checkout_completo' => 'https://go.hotmart.com/T99999999A?ap=abc123',
        ]);

        $response = $this->actingAs($user)->get(route('cadastrar_cursos'));

        $response->assertOk();
        $response->assertSee('Ações');
        $response->assertSee('Ativar todos');
        $response->assertSee('Desativar todos');
        $response->assertSee('Configurar página pública em massa');
        $response->assertSee('Configurações da página pública em massa');
        $response->assertSee('Aplicar em todos');
        $response->assertSee('configurar_pagina_publica_todos');
        $response->assertSee('bulkPublicConfigModalOpen', false);
        $response->assertSee('bulkActionsUrl', false);
        $response->assertSee('.je-dark-surface', false);
        $response->assertSee('background-color: #181b1e', false);
    }

    public function test_afiliados_cadastrar_curso_renders_countdown_configuration_fields(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = Curso::create([
            'titulo' => 'Curso Contador',
            'url' => 'curso-contador',
            'publicado' => true,
            'permitir_afiliacao' => true,
            'preco_cheio_completo' => 'R$197',
            'codigo_afiliado_plano_completo' => 'abc123',
            'link_checkout_completo' => 'https://go.hotmart.com/T99999999A?ap=abc123',
        ]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFCOUNT123',
            'mostrar_curso' => true,
            'modo_precos' => 'dois_precos',
        ]);

        Cupom::create(['codigo' => '10OFF', 'desconto' => 10]);
        Cupom::create(['codigo' => '25OFF', 'desconto' => 25]);

        $response = $this->actingAs($user)->get(route('cadastrar_cursos'));

        $response->assertOk();
        $response->assertSee('Usar contador?');
        $response->assertSee('Minutos');
        $response->assertSee('1 minuto');
        $response->assertSee('Após o contador');
        $response->assertSee('Encerrar o plano básico');
        $response->assertSee('Alterar o preço para');
        $response->assertSee('contador_destino_oferta_', false);
        $response->assertSee('completo_padrao');
        $response->assertSee('completo_cupom:1');
        $response->assertSee('basico_cupom:2');
        $response->assertSee('Plano completo - Sem cupom - R$197,00');
        $response->assertSee('25OFF) - R$147,75');
    }
}
