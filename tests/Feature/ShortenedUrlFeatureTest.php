<?php

namespace Tests\Feature;

use App\Models\ShortenedUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortenedUrlFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_encurtar_link_lista_renders_without_sql_error(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/user/encurtar_link_lista');

        $response->assertOk();
        $response->assertSee('Encurtador de Links');
        $response->assertSee('Nenhum link encurtado encontrado.');
    }

    public function test_encurtar_link_lista_shows_success_flash_message(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'dominio' => 'afiliado.test',
        ]);

        $response = $this->actingAs($user)
            ->withSession([
                'success' => 'Link criado com sucesso!',
                'link_encurtado' => 'afiliado.test/e/promo2026',
            ])
            ->get('/user/encurtar_link_lista');

        $response->assertOk();
        $response->assertSee('Link criado com sucesso!');
        $response->assertSee('https://afiliado.test/e/promo2026');
    }

    public function test_encurtar_link_creates_shortened_url_for_authenticated_user(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'dominio' => 'afiliado.test',
        ]);

        $response = $this->actingAs($user)
            ->from('/user/encurtar_link')
            ->post('/user/encurtar_link', [
                'url_longa' => 'https://example.com/oferta?src=meta',
            ]);

        $response->assertRedirect('/user/encurtar_link_lista');
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('shortened_urls', 1);
        $this->assertDatabaseHas('shortened_urls', [
            'user_id' => $user->id,
            'dominio' => 'afiliado.test',
            'url_longa' => 'https://example.com/oferta?src=meta',
        ]);
    }

    public function test_encurtar_link_returns_json_for_ajax_requests(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'dominio' => 'afiliado.test',
        ]);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post('/user/encurtar_link', [
                'url_longa' => 'https://example.com/oferta?utm_source=meta',
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Link criado com sucesso!',
        ]);
        $response->assertJsonStructure(['link_encurtado']);

        $this->assertDatabaseCount('shortened_urls', 1);
    }

    public function test_ajax_create_flow_flashes_success_for_lista_page(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'dominio' => 'afiliado.test',
        ]);

        $createResponse = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post('/user/encurtar_link', [
                'url_longa' => 'https://example.com/oferta?utm_source=meta',
                'slug' => 'promocao',
            ]);

        $createResponse->assertOk();

        $listaResponse = $this->actingAs($user)->get('/user/encurtar_link_lista');

        $listaResponse->assertOk();
        $listaResponse->assertSee('Link criado com sucesso!');
        $listaResponse->assertSee('https://afiliado.test/e/promocao');
    }

    public function test_create_page_renders_slug_input_with_domain_prefix(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'dominio' => 'afiliado.test',
        ]);

        $response = $this->actingAs($user)->get('/user/encurtar_link');

        $response->assertOk();
        $response->assertSee('name="slug"', false);
        $response->assertSee('afiliado.test/e/');
    }

    public function test_encurtar_link_creates_with_custom_slug_and_normalizes_to_lowercase(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'dominio' => 'afiliado.test',
        ]);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post('/user/encurtar_link', [
                'url_longa' => 'https://example.com/oferta?utm_medium=cpc',
                'slug' => 'Meu-Slug_2026',
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'link_encurtado' => 'afiliado.test/e/meu-slug_2026',
        ]);

        $this->assertDatabaseHas('shortened_urls', [
            'user_id' => $user->id,
            'dominio' => 'afiliado.test',
            'slug' => 'meu-slug_2026',
        ]);
    }

    public function test_encurtar_link_rejects_invalid_slug(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'dominio' => 'afiliado.test',
        ]);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post('/user/encurtar_link', [
                'url_longa' => 'https://example.com/oferta?utm_campaign=x',
                'slug' => 'inválido!',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['slug']);
    }

    public function test_encurtar_link_rejects_duplicate_slug_on_same_domain(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'dominio' => 'afiliado.test',
        ]);

        ShortenedUrl::create([
            'user_id' => $user->id,
            'dominio' => 'afiliado.test',
            'slug' => 'promo',
            'url_longa' => 'https://example.com/a',
        ]);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post('/user/encurtar_link', [
                'url_longa' => 'https://example.com/b',
                'slug' => 'promo',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['slug']);
    }

    public function test_encurtar_link_allows_same_slug_on_different_domains(): void
    {
        $firstUser = User::factory()->create([
            'email_verified_at' => now(),
            'dominio' => 'primeiro.test',
        ]);

        ShortenedUrl::create([
            'user_id' => $firstUser->id,
            'dominio' => 'primeiro.test',
            'slug' => 'promo',
            'url_longa' => 'https://example.com/a',
        ]);

        $secondUser = User::factory()->create([
            'email_verified_at' => now(),
            'dominio' => 'segundo.test',
        ]);

        $response = $this->actingAs($secondUser)
            ->withHeaders(['Accept' => 'application/json'])
            ->post('/user/encurtar_link', [
                'url_longa' => 'https://example.com/b',
                'slug' => 'promo',
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'link_encurtado' => 'segundo.test/e/promo',
        ]);

        $this->assertDatabaseHas('shortened_urls', [
            'user_id' => $secondUser->id,
            'dominio' => 'segundo.test',
            'slug' => 'promo',
        ]);
    }

    public function test_editar_updates_slug_when_valid(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'dominio' => 'afiliado.test',
        ]);

        $link = ShortenedUrl::create([
            'user_id' => $user->id,
            'dominio' => 'afiliado.test',
            'slug' => 'antigo',
            'url_longa' => 'https://example.com/old',
        ]);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post("/user/encurtar_link/editar/{$link->id}", [
                'url_longa' => 'https://example.com/new',
                'slug' => 'novo_slug',
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => 'Link atualizado com sucesso.',
            'link_encurtado' => 'afiliado.test/e/novo_slug',
        ]);

        $this->assertDatabaseHas('shortened_urls', [
            'id' => $link->id,
            'slug' => 'novo_slug',
            'url_longa' => 'https://example.com/new',
        ]);
    }

    public function test_editar_rejects_duplicate_slug_on_same_domain(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'dominio' => 'afiliado.test',
        ]);

        $linkA = ShortenedUrl::create([
            'user_id' => $user->id,
            'dominio' => 'afiliado.test',
            'slug' => 'slug-a',
            'url_longa' => 'https://example.com/a',
        ]);

        $linkB = ShortenedUrl::create([
            'user_id' => $user->id,
            'dominio' => 'afiliado.test',
            'slug' => 'slug-b',
            'url_longa' => 'https://example.com/b',
        ]);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post("/user/encurtar_link/editar/{$linkB->id}", [
                'url_longa' => 'https://example.com/b',
                'slug' => $linkA->slug,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['slug']);
    }

    public function test_editar_allows_keeping_the_same_slug_on_current_record(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'dominio' => 'afiliado.test',
        ]);

        $link = ShortenedUrl::create([
            'user_id' => $user->id,
            'dominio' => 'afiliado.test',
            'slug' => 'slug-a',
            'url_longa' => 'https://example.com/a',
        ]);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post("/user/encurtar_link/editar/{$link->id}", [
                'url_longa' => 'https://example.com/a-atualizado',
                'slug' => 'slug-a',
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => 'Link atualizado com sucesso.',
            'link_encurtado' => 'afiliado.test/e/slug-a',
        ]);

        $this->assertDatabaseHas('shortened_urls', [
            'id' => $link->id,
            'slug' => 'slug-a',
            'url_longa' => 'https://example.com/a-atualizado',
        ]);
    }
}
