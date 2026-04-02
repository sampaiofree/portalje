<?php

namespace Tests\Feature;

use App\Models\Codigo_ref;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AfiliadoCatalogoLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalogo_xml_does_not_append_ref_to_course_link(): void
    {
        $user = User::factory()->create([
            'dominio' => 'afiliado.portalje.test',
        ]);

        $curso = Curso::create([
            'ordem' => 1,
            'url' => 'curso-auxiliar-contabilidade',
            'titulo' => 'Curso Auxiliar Contabilidade',
            'headline' => 'Curso teste para validar feed do catálogo.',
            'gratuito' => false,
            'publicado' => true,
            'permitir_afiliacao' => true,
            'mostrar_na_pagina' => true,
            'preco_cheio_completo' => 'R$197',
        ]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFCAT123',
            'mostrar_curso' => true,
        ]);

        $response = $this->get("http://{$user->dominio}/afiliado/catalogo");

        $response->assertOk();
        $response->assertSee(
            "<g:link>http://{$user->dominio}/{$curso->url}</g:link>",
            false
        );
        $response->assertDontSee('?ref=', false);
    }
}
