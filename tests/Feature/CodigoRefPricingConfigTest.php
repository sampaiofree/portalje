<?php

namespace Tests\Feature;

use App\Http\Middleware\MinhaJornada;
use App\Models\Codigo_ref;
use App\Models\Cupom;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CodigoRefPricingConfigTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fakeHotmartValidationStatus(200);
    }

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
        Http::assertSentCount(1);
        Http::assertSent(fn (HttpRequest $request) => str_starts_with($request->url(), 'https://go.hotmart.com/'));

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
        Http::assertSentCount(1);

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
        Http::assertSentCount(1);
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

    public function test_store_saves_valid_countdown_configuration_with_one_minute(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = $this->createCurso('curso-contador-valido', 'Curso Contador Válido');
        $cupom = Cupom::create(['codigo' => 'COUNT20', 'desconto' => 20]);

        $response = $this->actingAs($user)->postJson(route('cadastrar_codigo_ref'), [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'AFILIADOCOUNT1',
            'mostrar_curso' => '1',
            'modo_precos' => 'um_preco',
            'usar_contador' => '1',
            'contador_minutos' => '1',
            'contador_acao' => 'alterar_preco',
            'contador_destino_oferta' => 'completo_cupom:' . $cupom->id,
            'titulo' => $curso->titulo,
        ]);

        $response->assertOk();
        $response->assertJsonPath('usar_contador', true);
        $response->assertJsonPath('contador_minutos', 1);
        $response->assertJsonPath('contador_acao', 'alterar_preco');
        $response->assertJsonPath('contador_destino_oferta', 'completo_cupom:' . $cupom->id);

        $this->assertDatabaseHas('codigo_ref', [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'usar_contador' => 1,
            'contador_minutos' => 1,
            'contador_acao' => 'alterar_preco',
            'contador_destino_oferta' => 'completo_cupom:' . $cupom->id,
        ]);
    }

    public function test_store_clears_countdown_fields_when_disabled(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = $this->createCurso('curso-contador-off', 'Curso Contador Off');

        $registro = Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'COUNTCLEAR1',
            'mostrar_curso' => true,
            'modo_precos' => 'padrao',
            'usar_contador' => true,
            'contador_minutos' => 20,
            'contador_acao' => 'nada',
            'contador_destino_oferta' => 'completo_padrao',
        ]);

        $response = $this->actingAs($user)->postJson(route('cadastrar_codigo_ref'), [
            'id' => $registro->id,
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'COUNTCLEAR1',
            'mostrar_curso' => '1',
            'modo_precos' => 'padrao',
            'usar_contador' => '0',
            'titulo' => $curso->titulo,
        ]);

        $response->assertOk();
        $response->assertJsonPath('usar_contador', false);
        $response->assertJsonPath('contador_minutos', null);
        $response->assertJsonPath('contador_acao', null);
        $response->assertJsonPath('contador_destino_oferta', null);

        $this->assertDatabaseHas('codigo_ref', [
            'id' => $registro->id,
            'usar_contador' => 0,
            'contador_minutos' => null,
            'contador_acao' => null,
            'contador_destino_oferta' => null,
        ]);
    }

    public function test_store_rejects_encerrar_basico_in_um_preco_mode(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = $this->createCurso('curso-contador-acao-invalida', 'Curso Contador Ação Inválida');
        Cupom::create(['codigo' => 'COUNT15', 'desconto' => 15]);

        $response = $this->actingAs($user)->postJson(route('cadastrar_codigo_ref'), [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'COUNTACT1',
            'mostrar_curso' => '1',
            'modo_precos' => 'um_preco',
            'usar_contador' => '1',
            'contador_minutos' => '5',
            'contador_acao' => 'encerrar_basico',
            'titulo' => $curso->titulo,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['contador_acao']);
    }

    public function test_store_rejects_alterar_preco_without_destination_offer(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = $this->createCurso('curso-contador-sem-destino', 'Curso Contador Sem Destino');

        $response = $this->actingAs($user)->postJson(route('cadastrar_codigo_ref'), [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'COUNTDEST1',
            'mostrar_curso' => '1',
            'modo_precos' => 'padrao',
            'usar_contador' => '1',
            'contador_minutos' => '30',
            'contador_acao' => 'alterar_preco',
            'titulo' => $curso->titulo,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['contador_destino_oferta']);
    }

    public function test_store_rejects_invalid_countdown_destination_for_current_mode(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = $this->createCurso('curso-contador-destino-invalido', 'Curso Contador Destino Inválido');
        $cupom = Cupom::create(['codigo' => 'INVALID20', 'desconto' => 20]);

        $response = $this->actingAs($user)->postJson(route('cadastrar_codigo_ref'), [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'COUNTINVALID1',
            'mostrar_curso' => '1',
            'modo_precos' => 'padrao',
            'usar_contador' => '1',
            'contador_minutos' => '20',
            'contador_acao' => 'alterar_preco',
            'contador_destino_oferta' => 'completo_cupom:' . $cupom->id,
            'titulo' => $curso->titulo,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['contador_destino_oferta']);
    }

    public function test_store_defaults_mostrar_curso_to_true_on_first_creation_when_omitted(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = $this->createCurso('curso-mostrar-default', 'Curso Mostrar Default');

        $response = $this->actingAs($user)->postJson(route('cadastrar_codigo_ref'), [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'AFILIADO8',
            'modo_precos' => 'padrao',
            'titulo' => $curso->titulo,
        ]);

        $response->assertOk();
        $response->assertJsonPath('mostrar_curso', true);

        $this->assertDatabaseHas('codigo_ref', [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'AFILIADO8',
            'mostrar_curso' => 1,
        ]);
    }

    public function test_store_forces_mostrar_curso_to_true_on_first_creation_even_when_zero_is_sent(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = $this->createCurso('curso-mostrar-force', 'Curso Mostrar Force');

        $response = $this->actingAs($user)->postJson(route('cadastrar_codigo_ref'), [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'AFILIADO9',
            'mostrar_curso' => '0',
            'modo_precos' => 'padrao',
            'titulo' => $curso->titulo,
        ]);

        $response->assertOk();
        $response->assertJsonPath('mostrar_curso', true);

        $this->assertDatabaseHas('codigo_ref', [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'AFILIADO9',
            'mostrar_curso' => 1,
        ]);
    }

    public function test_store_rejects_codigo_ref_when_hotmart_returns_invalid_status(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);
        $this->fakeHotmartValidationStatus(400);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = $this->createCurso('curso-codigo-invalido', 'Curso Código Inválido');

        $response = $this->actingAs($user)->postJson(route('cadastrar_codigo_ref'), [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'CODIGOX1',
            'mostrar_curso' => '1',
            'modo_precos' => 'padrao',
            'titulo' => $curso->titulo,
        ]);

        Http::assertSentCount(1);
        Http::assertSent(fn (HttpRequest $request) => $request->url() === 'https://go.hotmart.com/CODIGOX1');

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['codigo_ref']);

        $this->assertDatabaseMissing('codigo_ref', [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'CODIGOX1',
        ]);
    }

    public function test_store_rejects_when_hotmart_validation_is_unavailable(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        Http::fake(function () {
            throw new ConnectionException('Timeout');
        });

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = $this->createCurso('curso-codigo-timeout', 'Curso Código Timeout');

        $response = $this->actingAs($user)->postJson(route('cadastrar_codigo_ref'), [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'CODIGOTIME1',
            'mostrar_curso' => '1',
            'modo_precos' => 'padrao',
            'titulo' => $curso->titulo,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['codigo_ref']);

        $this->assertDatabaseMissing('codigo_ref', [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'CODIGOTIME1',
        ]);
    }

    public function test_store_skips_hotmart_validation_when_codigo_ref_does_not_change(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);
        Http::fake();

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = $this->createCurso('curso-sem-revalidacao', 'Curso Sem Revalidação');

        $registro = Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFSEMVALIDAR1',
            'mostrar_curso' => true,
        ]);

        $response = $this->actingAs($user)->postJson(route('cadastrar_codigo_ref'), [
            'id' => $registro->id,
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFSEMVALIDAR1',
            'mostrar_curso' => '0',
            'modo_precos' => 'padrao',
            'titulo' => $curso->titulo,
        ]);

        $response->assertOk();
        Http::assertNothingSent();
    }

    public function test_store_revalidates_when_codigo_ref_changes(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);
        $this->fakeHotmartValidationStatus(200);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = $this->createCurso('curso-revalidacao', 'Curso Revalidação');

        $registro = Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFANTIGO123',
            'mostrar_curso' => true,
        ]);

        $response = $this->actingAs($user)->postJson(route('cadastrar_codigo_ref'), [
            'id' => $registro->id,
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFNOVO1234',
            'mostrar_curso' => '1',
            'modo_precos' => 'padrao',
            'titulo' => $curso->titulo,
        ]);

        $response->assertOk();
        Http::assertSentCount(1);
        $this->assertDatabaseHas('codigo_ref', [
            'id' => $registro->id,
            'codigo_ref' => 'REFNOVO1234',
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

    private function fakeHotmartValidationStatus(int $status): void
    {
        $factory = new HttpFactory();
        $factory->fake(static function () use ($status) {
            return Http::response('<html></html>', $status);
        });
        Http::swap($factory);
    }
}
