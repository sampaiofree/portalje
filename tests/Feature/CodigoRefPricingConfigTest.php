<?php

namespace Tests\Feature;

use App\Http\Middleware\MinhaJornada;
use App\Models\Cupom;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CodigoRefPricingConfigTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_saves_padrao_mode_correctly(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = $this->createCurso('curso-padrao', 'Curso Padrão');

        $response = $this->actingAs($user)->postJson(route('cadastrar_codigo_ref'), [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'AFILIADO1',
            'mostrar_curso' => '1',
            'modo_precos' => 'padrao',
            'titulo' => $curso->titulo,
        ]);

        $response->assertOk();
        $response->assertJsonPath('modo_precos', 'padrao');
        $response->assertJsonPath('cupom_principal_id', null);
        $response->assertJsonPath('cupom_secundario_id', null);

        $this->assertDatabaseHas('codigo_ref', [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'AFILIADO1',
            'modo_precos' => 'padrao',
            'cupom_principal_id' => null,
            'cupom_secundario_id' => null,
        ]);
    }

    public function test_store_saves_um_preco_with_main_coupon(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = $this->createCurso('curso-um-preco', 'Curso Um Preço');
        $cupom = Cupom::create(['codigo' => 'MAIN10', 'desconto' => 10]);

        $response = $this->actingAs($user)->postJson(route('cadastrar_codigo_ref'), [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'AFILIADO2',
            'mostrar_curso' => '1',
            'modo_precos' => 'um_preco',
            'cupom_principal_id' => (string) $cupom->id,
            'titulo' => $curso->titulo,
        ]);

        $response->assertOk();
        $response->assertJsonPath('modo_precos', 'um_preco');
        $response->assertJsonPath('cupom_principal_id', $cupom->id);
        $response->assertJsonPath('cupom_secundario_id', null);

        $this->assertDatabaseHas('codigo_ref', [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'modo_precos' => 'um_preco',
            'cupom_principal_id' => $cupom->id,
            'cupom_secundario_id' => null,
        ]);
    }

    public function test_store_saves_dois_precos_with_distinct_coupons(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = $this->createCurso('curso-dois-precos', 'Curso Dois Preços');
        $cupomPrincipal = Cupom::create(['codigo' => 'MAIN15', 'desconto' => 15]);
        $cupomSecundario = Cupom::create(['codigo' => 'SEC30', 'desconto' => 30]);

        $response = $this->actingAs($user)->postJson(route('cadastrar_codigo_ref'), [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'AFILIADO3',
            'mostrar_curso' => '1',
            'modo_precos' => 'dois_precos',
            'cupom_principal_id' => (string) $cupomPrincipal->id,
            'cupom_secundario_id' => (string) $cupomSecundario->id,
            'titulo' => $curso->titulo,
        ]);

        $response->assertOk();
        $response->assertJsonPath('modo_precos', 'dois_precos');
        $response->assertJsonPath('cupom_principal_id', $cupomPrincipal->id);
        $response->assertJsonPath('cupom_secundario_id', $cupomSecundario->id);
    }

    public function test_store_rejects_duplicate_coupon_between_main_and_secondary(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = $this->createCurso('curso-cupom-repetido', 'Curso Cupom Repetido');
        $cupom = Cupom::create(['codigo' => 'DUP50', 'desconto' => 50]);

        $response = $this->actingAs($user)->postJson(route('cadastrar_codigo_ref'), [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'AFILIADO4',
            'mostrar_curso' => '1',
            'modo_precos' => 'dois_precos',
            'cupom_principal_id' => (string) $cupom->id,
            'cupom_secundario_id' => (string) $cupom->id,
            'titulo' => $curso->titulo,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['cupom_secundario_id']);
    }

    public function test_without_coupons_backend_forces_padrao_mode(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = $this->createCurso('curso-sem-cupom', 'Curso Sem Cupom');

        $response = $this->actingAs($user)->postJson(route('cadastrar_codigo_ref'), [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'AFILIADO5',
            'mostrar_curso' => '1',
            'modo_precos' => 'dois_precos',
            'cupom_principal_id' => '999',
            'cupom_secundario_id' => '888',
            'titulo' => $curso->titulo,
        ]);

        $response->assertOk();
        $response->assertJsonPath('modo_precos', 'padrao');
        $response->assertJsonPath('cupom_principal_id', null);
        $response->assertJsonPath('cupom_secundario_id', null);

        $this->assertDatabaseHas('codigo_ref', [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'modo_precos' => 'padrao',
            'cupom_principal_id' => null,
            'cupom_secundario_id' => null,
        ]);
    }

    public function test_store_saves_formulario_pre_checkout_disabled(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = $this->createCurso('curso-formulario-off', 'Curso Formulario Off');

        $response = $this->actingAs($user)->postJson(route('cadastrar_codigo_ref'), [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'AFILIADO6',
            'mostrar_curso' => '1',
            'formulario_pre_checkout' => '0',
            'modo_precos' => 'padrao',
            'titulo' => $curso->titulo,
        ]);

        $response->assertOk();
        $response->assertJsonPath('formulario_pre_checkout', false);

        $this->assertDatabaseHas('codigo_ref', [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'formulario_pre_checkout' => 0,
        ]);
    }

    public function test_store_defaults_formulario_pre_checkout_to_true_when_omitted(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = $this->createCurso('curso-formulario-default', 'Curso Formulario Default');

        $response = $this->actingAs($user)->postJson(route('cadastrar_codigo_ref'), [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'AFILIADO7',
            'mostrar_curso' => '1',
            'modo_precos' => 'padrao',
            'titulo' => $curso->titulo,
        ]);

        $response->assertOk();
        $response->assertJsonPath('formulario_pre_checkout', true);

        $this->assertDatabaseHas('codigo_ref', [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'formulario_pre_checkout' => 1,
        ]);
    }

    private function createCurso(string $url, string $titulo): Curso
    {
        return Curso::create([
            'titulo' => $titulo,
            'url' => $url,
            'publicado' => true,
            'permitir_afiliacao' => true,
            'preco_cheio_completo' => 'R$197',
            'preco_parcelado_completo' => '12xR$19,70',
            'codigo_afiliado_plano_completo' => 'abc123',
            'link_checkout_completo' => 'https://go.hotmart.com/T99999999A?ap=abc123',
            'areas_de_atuacao' => 'Atendimento/Vendas',
            'horas_completo' => 120,
        ]);
    }
}
