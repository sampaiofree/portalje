<?php

namespace Tests\Feature;

use App\Models\Curso;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AfiliadosCentralTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_central_shows_only_eligible_courses_and_real_links(): void
    {
        Curso::create(['titulo' => 'Curso público oculto', 'url' => 'curso-oculto', 'ordem' => 2, 'publicado' => true, 'permitir_afiliacao' => true, 'mostrar_na_pagina' => false, 'link_materiais' => 'https://example.com/materiais', 'link_afiliacao' => 'https://example.com/afiliacao']);
        Curso::create(['titulo' => 'Primeiro curso', 'url' => 'primeiro', 'ordem' => 1, 'publicado' => true, 'permitir_afiliacao' => true]);
        Curso::create(['titulo' => 'Curso não publicado', 'url' => 'nao-publicado', 'publicado' => false, 'permitir_afiliacao' => true]);
        Curso::create(['titulo' => 'Curso sem afiliação', 'url' => 'sem-afiliacao', 'publicado' => true, 'permitir_afiliacao' => false]);

        $response = $this->get('/afiliados');
        $response->assertOk()->assertSeeInOrder(['Primeiro curso', 'Curso público oculto'])
            ->assertSee('https://example.com/materiais')->assertSee('https://example.com/afiliacao')
            ->assertSee('Materiais em breve')->assertSee('60% sobre o valor da venda')
            ->assertDontSee('Curso não publicado')->assertDontSee('Curso sem afiliação');
    }

    public function test_empty_catalog_is_available_to_guests(): void
    {
        $this->get('/afiliados')->assertOk()->assertSee('Nenhum curso disponível no momento.');
    }
}
