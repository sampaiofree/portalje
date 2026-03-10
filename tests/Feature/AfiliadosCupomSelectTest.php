<?php

namespace Tests\Feature;

use App\Http\Middleware\MinhaJornada;
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
}
