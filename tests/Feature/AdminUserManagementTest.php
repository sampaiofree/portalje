<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_dedicated_users_index_page(): void
    {
        $admin = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
        ]);

        $user = User::factory()->create([
            'name' => 'Usuario da Lista',
            'email' => 'lista@example.com',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('Todos os usuários');
        $response->assertSee('Usuario da Lista');
        $response->assertSee('lista@example.com');
    }

    public function test_admin_users_index_can_filter_by_search_and_access_level(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Principal',
            'email' => 'admin-principal@example.com',
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
        ]);

        User::factory()->create([
            'name' => 'Maria Operacao',
            'email' => 'maria@example.com',
            'nivel_acesso' => User::NIVEL_ACESSO_USER,
        ]);

        User::factory()->create([
            'name' => 'Pedro Parceiro',
            'email' => 'pedro@example.com',
            'nivel_acesso' => User::NIVEL_ACESSO_USER,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.index', [
            'search' => 'maria',
            'nivel_acesso' => User::NIVEL_ACESSO_USER,
        ]));

        $response->assertOk();
        $response->assertSee('Maria Operacao');
        $response->assertSee('maria@example.com');
        $response->assertDontSee('Pedro Parceiro');
        $response->assertDontSee('pedro@example.com');
    }

    public function test_admin_can_open_user_edit_page(): void
    {
        $admin = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
        ]);

        $user = User::factory()->create([
            'name' => 'Afiliado Exemplo',
            'email' => 'afiliado@example.com',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.edit', $user));

        $response->assertOk();
        $response->assertSee('Editar Usuário');
        $response->assertSee('Afiliado Exemplo');
        $response->assertSee('afiliado@example.com');
    }

    public function test_admin_can_update_user_fields_from_admin_panel(): void
    {
        config(['app.url' => 'https://portalje.org']);

        $admin = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
        ]);

        $user = User::factory()->create([
            'email' => 'antigo@example.com',
            'telefone_pessoal_1' => '5562999990000',
            'telefone_pessoal_1_verified_at' => null,
            'dominio' => null,
            'dominio_externo' => null,
            'whatsapp_atendimento' => null,
            'nome_empresa' => null,
            'meta_pixel_id' => null,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'name' => 'Novo Nome',
            'email' => 'novo@example.com',
            'nivel_acesso' => User::NIVEL_ACESSO_USER,
            'email_verified' => '1',
            'telefone_pessoal_1' => '5562999998888',
            'telefone_pessoal_1_verified' => '1',
            'telefone_pessoal_2' => '5562999997777',
            'apelido' => 'Parceiro',
            'nome_empresa' => 'Empresa Teste',
            'dominio' => 'novosite',
            'dominio_externo' => 'https://www.parceiro.com.br',
            'whatsapp_atendimento' => '5511999991111',
            'meta_pixel_id' => 'PIXEL-123',
        ]);

        $response->assertRedirect(route('admin.users.edit', $user));
        $response->assertSessionHas('success', 'Usuário atualizado com sucesso.');

        $user->refresh();

        $this->assertSame('Novo Nome', $user->name);
        $this->assertSame('novo@example.com', $user->email);
        $this->assertSame(User::NIVEL_ACESSO_USER, $user->nivel_acesso);
        $this->assertNotNull($user->email_verified_at);
        $this->assertSame('5562999998888', $user->telefone_pessoal_1);
        $this->assertNotNull($user->telefone_pessoal_1_verified_at);
        $this->assertSame('5562999997777', $user->telefone_pessoal_2);
        $this->assertSame('Parceiro', $user->apelido);
        $this->assertSame('Empresa Teste', $user->nome_empresa);
        $this->assertSame('novosite.portalje.org', $user->dominio);
        $this->assertSame('parceiro.com.br', $user->dominio_externo);
        $this->assertSame('5511999991111', $user->whatsapp_atendimento);
        $this->assertSame('PIXEL-123', $user->meta_pixel_id);
    }

    public function test_non_admin_cannot_access_user_edit_page(): void
    {
        $user = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_USER,
        ]);

        $target = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.users.edit', $target));

        $response->assertRedirect('/login');
    }
}
