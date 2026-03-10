<?php

namespace Tests\Feature;

use App\Models\Cupom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CupomAdminCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_cupons_index(): void
    {
        $admin = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.cupons.index'));

        $response->assertOk();
        $response->assertSee('Registros de Cupons');
    }

    public function test_non_admin_is_redirected_when_accessing_cupons_index(): void
    {
        $user = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_USER,
        ]);

        $response = $this->actingAs($user)->get(route('admin.cupons.index'));

        $response->assertRedirect('/login');
    }

    public function test_admin_can_create_cupom_with_normalized_code(): void
    {
        $admin = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.cupons.store'), [
            'codigo' => '  10off  ',
            'desconto' => '10,5',
        ]);

        $response->assertRedirect(route('admin.cupons.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('cupons', [
            'codigo' => '10OFF',
            'desconto' => 10.5,
        ]);
    }

    public function test_store_validates_duplicate_code_and_invalid_discount(): void
    {
        $admin = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
        ]);

        Cupom::create([
            'codigo' => '20OFF',
            'desconto' => 20,
        ]);

        $duplicateResponse = $this->actingAs($admin)->from(route('admin.cupons.index'))->post(route('admin.cupons.store'), [
            'codigo' => '20off',
            'desconto' => 20,
        ]);

        $duplicateResponse->assertRedirect(route('admin.cupons.index'));
        $duplicateResponse->assertSessionHasErrors('codigo');

        $discountResponse = $this->actingAs($admin)->from(route('admin.cupons.index'))->post(route('admin.cupons.store'), [
            'codigo' => '30OFF',
            'desconto' => 0,
        ]);

        $discountResponse->assertRedirect(route('admin.cupons.index'));
        $discountResponse->assertSessionHasErrors('desconto');

        $discountMaxResponse = $this->actingAs($admin)->from(route('admin.cupons.index'))->post(route('admin.cupons.store'), [
            'codigo' => '31OFF',
            'desconto' => '100.01',
        ]);

        $discountMaxResponse->assertRedirect(route('admin.cupons.index'));
        $discountMaxResponse->assertSessionHasErrors('desconto');
    }

    public function test_admin_can_update_cupom_and_keep_uniqueness_rule(): void
    {
        $admin = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
        ]);

        $cupomA = Cupom::create(['codigo' => '10OFF', 'desconto' => 10]);
        $cupomB = Cupom::create(['codigo' => '50OFF', 'desconto' => 50]);

        $successResponse = $this->actingAs($admin)->put(route('admin.cupons.update', $cupomA), [
            'codigo' => ' 15off ',
            'desconto' => '15,75',
        ]);

        $successResponse->assertRedirect(route('admin.cupons.index'));
        $this->assertDatabaseHas('cupons', [
            'id' => $cupomA->id,
            'codigo' => '15OFF',
            'desconto' => 15.75,
        ]);

        $uniqueResponse = $this->actingAs($admin)->from(route('admin.cupons.index'))->put(route('admin.cupons.update', $cupomA), [
            'codigo' => $cupomB->codigo,
            'desconto' => 30,
        ]);

        $uniqueResponse->assertRedirect(route('admin.cupons.index'));
        $uniqueResponse->assertSessionHasErrors('codigo');
    }

    public function test_admin_can_delete_cupom(): void
    {
        $admin = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
        ]);

        $cupom = Cupom::create([
            'codigo' => '80OFF',
            'desconto' => 80,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.cupons.destroy', $cupom));

        $response->assertRedirect(route('admin.cupons.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('cupons', [
            'id' => $cupom->id,
        ]);
    }
}
