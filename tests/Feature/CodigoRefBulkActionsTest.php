<?php

namespace Tests\Feature;

use App\Http\Middleware\MinhaJornada;
use App\Models\Codigo_ref;
use App\Models\Cupom;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CodigoRefBulkActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_bulk_activate_enables_only_authenticated_user_refs_with_codigo_ref(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $otherUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso1 = $this->createCurso('curso-bulk-1', 'Curso Bulk 1');
        $curso2 = $this->createCurso('curso-bulk-2', 'Curso Bulk 2');
        $curso3 = $this->createCurso('curso-bulk-3', 'Curso Bulk 3');

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso1->id,
            'codigo_ref' => 'REFATIVA1',
            'mostrar_curso' => false,
        ]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso2->id,
            'codigo_ref' => ' REFATIVA2 ',
            'mostrar_curso' => false,
        ]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso3->id,
            'codigo_ref' => '   ',
            'mostrar_curso' => false,
        ]);

        Codigo_ref::create([
            'user_id' => $otherUser->id,
            'curso_id' => $curso1->id,
            'codigo_ref' => 'REFOTHER1',
            'mostrar_curso' => false,
        ]);

        $response = $this->actingAs($user)->postJson(route('cadastrar_cursos_bulk_actions'), [
            'action' => 'ativar_todos',
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('action', 'ativar_todos');
        $response->assertJsonPath('updated_count', 2);

        $this->assertEqualsCanonicalizing([$curso1->id, $curso2->id], $response->json('updated_course_ids'));

        $this->assertDatabaseHas('codigo_ref', [
            'user_id' => $user->id,
            'curso_id' => $curso1->id,
            'mostrar_curso' => 1,
        ]);
        $this->assertDatabaseHas('codigo_ref', [
            'user_id' => $user->id,
            'curso_id' => $curso2->id,
            'mostrar_curso' => 1,
        ]);
        $this->assertDatabaseHas('codigo_ref', [
            'user_id' => $user->id,
            'curso_id' => $curso3->id,
            'mostrar_curso' => 0,
        ]);
        $this->assertDatabaseHas('codigo_ref', [
            'user_id' => $otherUser->id,
            'curso_id' => $curso1->id,
            'mostrar_curso' => 0,
        ]);
    }

    public function test_bulk_deactivate_disables_existing_refs_and_does_not_create_missing_rows(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso1 = $this->createCurso('curso-bulk-off-1', 'Curso Bulk Off 1');
        $curso2 = $this->createCurso('curso-bulk-off-2', 'Curso Bulk Off 2');
        $cursoSemRegistro = $this->createCurso('curso-bulk-off-3', 'Curso Bulk Off 3');

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso1->id,
            'codigo_ref' => 'REFDESATIVA1',
            'mostrar_curso' => true,
        ]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso2->id,
            'codigo_ref' => '',
            'mostrar_curso' => true,
        ]);

        $beforeCount = Codigo_ref::count();

        $response = $this->actingAs($user)->postJson(route('cadastrar_cursos_bulk_actions'), [
            'action' => 'desativar_todos',
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('action', 'desativar_todos');
        $response->assertJsonPath('updated_count', 1);
        $this->assertEqualsCanonicalizing([$curso1->id], $response->json('updated_course_ids'));

        $this->assertSame($beforeCount, Codigo_ref::count());
        $this->assertDatabaseHas('codigo_ref', [
            'user_id' => $user->id,
            'curso_id' => $curso1->id,
            'mostrar_curso' => 0,
        ]);
        $this->assertDatabaseHas('codigo_ref', [
            'user_id' => $user->id,
            'curso_id' => $curso2->id,
            'mostrar_curso' => 1,
        ]);
        $this->assertDatabaseMissing('codigo_ref', [
            'user_id' => $user->id,
            'curso_id' => $cursoSemRegistro->id,
        ]);
    }

    public function test_bulk_public_page_config_updates_only_authenticated_user_refs_with_codigo_ref(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $otherUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso1 = $this->createCurso('curso-bulk-config-1', 'Curso Bulk Config 1');
        $curso2 = $this->createCurso('curso-bulk-config-2', 'Curso Bulk Config 2');
        $curso3 = $this->createCurso('curso-bulk-config-3', 'Curso Bulk Config 3');

        $cupomPrincipal = Cupom::create(['codigo' => 'BULK10', 'desconto' => 10]);
        $cupomSecundario = Cupom::create(['codigo' => 'BULK20', 'desconto' => 20]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso1->id,
            'codigo_ref' => 'REFCONF1',
            'formulario_pre_checkout' => true,
            'modo_precos' => 'padrao',
            'cupom_principal_id' => null,
            'cupom_secundario_id' => null,
        ]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso2->id,
            'codigo_ref' => ' REFCONF2 ',
            'formulario_pre_checkout' => true,
            'modo_precos' => 'um_preco',
            'cupom_principal_id' => $cupomPrincipal->id,
            'cupom_secundario_id' => null,
        ]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso3->id,
            'codigo_ref' => '',
            'formulario_pre_checkout' => true,
            'modo_precos' => 'padrao',
            'cupom_principal_id' => null,
            'cupom_secundario_id' => null,
        ]);

        Codigo_ref::create([
            'user_id' => $otherUser->id,
            'curso_id' => $curso1->id,
            'codigo_ref' => 'REFOUTRO1',
            'formulario_pre_checkout' => true,
            'modo_precos' => 'padrao',
            'cupom_principal_id' => null,
            'cupom_secundario_id' => null,
        ]);

        $beforeCount = Codigo_ref::count();

        $response = $this->actingAs($user)->postJson(route('cadastrar_cursos_bulk_actions'), [
            'action' => 'configurar_pagina_publica_todos',
            'formulario_pre_checkout' => '0',
            'modo_precos' => 'dois_precos',
            'cupom_principal_id' => (string) $cupomPrincipal->id,
            'cupom_secundario_id' => (string) $cupomSecundario->id,
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('action', 'configurar_pagina_publica_todos');
        $response->assertJsonPath('updated_count', 2);
        $response->assertJsonPath('formulario_pre_checkout', false);
        $response->assertJsonPath('modo_precos', 'dois_precos');
        $response->assertJsonPath('cupom_principal_id', $cupomPrincipal->id);
        $response->assertJsonPath('cupom_secundario_id', $cupomSecundario->id);
        $this->assertEqualsCanonicalizing([$curso1->id, $curso2->id], $response->json('updated_course_ids'));

        $this->assertSame($beforeCount, Codigo_ref::count());
        $this->assertDatabaseHas('codigo_ref', [
            'user_id' => $user->id,
            'curso_id' => $curso1->id,
            'formulario_pre_checkout' => 0,
            'modo_precos' => 'dois_precos',
            'cupom_principal_id' => $cupomPrincipal->id,
            'cupom_secundario_id' => $cupomSecundario->id,
        ]);
        $this->assertDatabaseHas('codigo_ref', [
            'user_id' => $user->id,
            'curso_id' => $curso2->id,
            'formulario_pre_checkout' => 0,
            'modo_precos' => 'dois_precos',
            'cupom_principal_id' => $cupomPrincipal->id,
            'cupom_secundario_id' => $cupomSecundario->id,
        ]);
        $this->assertDatabaseHas('codigo_ref', [
            'user_id' => $user->id,
            'curso_id' => $curso3->id,
            'formulario_pre_checkout' => 1,
            'modo_precos' => 'padrao',
            'cupom_principal_id' => null,
            'cupom_secundario_id' => null,
        ]);
        $this->assertDatabaseHas('codigo_ref', [
            'user_id' => $otherUser->id,
            'curso_id' => $curso1->id,
            'formulario_pre_checkout' => 1,
            'modo_precos' => 'padrao',
            'cupom_principal_id' => null,
            'cupom_secundario_id' => null,
        ]);
    }

    public function test_bulk_public_page_config_forces_padrao_when_coupons_table_is_empty(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = $this->createCurso('curso-bulk-config-sem-cupom', 'Curso Bulk Config Sem Cupom');

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFSEMCP1',
            'formulario_pre_checkout' => true,
            'modo_precos' => 'padrao',
            'cupom_principal_id' => null,
            'cupom_secundario_id' => null,
        ]);

        $response = $this->actingAs($user)->postJson(route('cadastrar_cursos_bulk_actions'), [
            'action' => 'configurar_pagina_publica_todos',
            'formulario_pre_checkout' => '0',
            'modo_precos' => 'dois_precos',
            'cupom_principal_id' => '9999',
            'cupom_secundario_id' => '8888',
        ]);

        $response->assertOk();
        $response->assertJsonPath('updated_count', 1);
        $response->assertJsonPath('formulario_pre_checkout', false);
        $response->assertJsonPath('modo_precos', 'padrao');
        $response->assertJsonPath('cupom_principal_id', null);
        $response->assertJsonPath('cupom_secundario_id', null);

        $this->assertDatabaseHas('codigo_ref', [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'formulario_pre_checkout' => 0,
            'modo_precos' => 'padrao',
            'cupom_principal_id' => null,
            'cupom_secundario_id' => null,
        ]);
    }

    public function test_bulk_public_page_config_rejects_dois_precos_without_secondary_coupon(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = $this->createCurso('curso-bulk-config-sem-secundario', 'Curso Bulk Config Sem Secundário');
        $cupomPrincipal = Cupom::create(['codigo' => 'BULK30', 'desconto' => 30]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFSEMSEC1',
            'formulario_pre_checkout' => true,
            'modo_precos' => 'padrao',
            'cupom_principal_id' => null,
            'cupom_secundario_id' => null,
        ]);

        $response = $this->actingAs($user)->postJson(route('cadastrar_cursos_bulk_actions'), [
            'action' => 'configurar_pagina_publica_todos',
            'formulario_pre_checkout' => '1',
            'modo_precos' => 'dois_precos',
            'cupom_principal_id' => (string) $cupomPrincipal->id,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['cupom_secundario_id']);
    }

    public function test_bulk_public_page_config_rejects_duplicate_coupons_on_dois_precos(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $curso = $this->createCurso('curso-bulk-config-cupom-duplicado', 'Curso Bulk Config Cupom Duplicado');
        $cupom = Cupom::create(['codigo' => 'BULK40', 'desconto' => 40]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFDUPCP1',
            'formulario_pre_checkout' => true,
            'modo_precos' => 'padrao',
            'cupom_principal_id' => null,
            'cupom_secundario_id' => null,
        ]);

        $response = $this->actingAs($user)->postJson(route('cadastrar_cursos_bulk_actions'), [
            'action' => 'configurar_pagina_publica_todos',
            'formulario_pre_checkout' => '1',
            'modo_precos' => 'dois_precos',
            'cupom_principal_id' => (string) $cupom->id,
            'cupom_secundario_id' => (string) $cupom->id,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['cupom_secundario_id']);
    }

    public function test_bulk_action_rejects_invalid_action(): void
    {
        $this->withoutMiddleware(MinhaJornada::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->postJson(route('cadastrar_cursos_bulk_actions'), [
            'action' => 'algo_invalido',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['action']);
    }

    private function createCurso(string $url, string $titulo): Curso
    {
        return Curso::create([
            'titulo' => $titulo,
            'url' => $url,
            'publicado' => true,
            'permitir_afiliacao' => true,
            'preco_cheio_completo' => 'R$197',
            'codigo_afiliado_plano_completo' => 'abc123',
            'link_checkout_completo' => 'https://go.hotmart.com/T99999999A?ap=abc123',
        ]);
    }
}
