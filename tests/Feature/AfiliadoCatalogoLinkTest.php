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

    public function test_catalogo_markdown_hides_internal_fields_and_keeps_expected_course_data(): void
    {
        $user = User::factory()->create([
            'dominio' => 'afiliado-markdown.portalje.test',
        ]);

        $curso = Curso::create([
            'ordem' => 1,
            'url' => 'curso-atendente-farmacia',
            'titulo' => 'Curso Atendente de Farmácia',
            'headline' => 'Curso teste para validar o catálogo markdown.',
            'descricao_curta' => 'Aprenda rotinas práticas para trabalhar em farmácias.',
            'gratuito' => false,
            'publicado' => true,
            'permitir_afiliacao' => true,
            'mostrar_na_pagina' => true,
            'preco_cheio_completo' => 'R$297',
            'link_checkout_completo' => 'https://checkout.portalje.test/curso-atendente-farmacia',
            'horas_completo' => '160',
            'professor_nome' => 'Maria Souza',
        ]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFMD123',
            'mostrar_curso' => true,
        ]);

        $response = $this->get("http://{$user->dominio}/afiliado/catalogo2");

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');
        $response->assertSee('Curso Atendente de Farmácia');
        $response->assertSee('Aprenda rotinas práticas para trabalhar em farmácias.');
        $response->assertSee("- Link da página: http://{$user->dominio}/{$curso->url}", false);
        $response->assertSee('- Link do checkout: https://checkout.portalje.test/curso-atendente-farmacia', false);
        $response->assertSee('- Carga horária: 160 horas', false);
        $response->assertSee('- Professor: Maria Souza', false);
        $response->assertSee('- Preço: 148.50 BRL', false);
        $response->assertDontSee('Disponibilidade:', false);
        $response->assertDontSee('Condição:', false);
        $response->assertDontSee('Imagem:', false);
        $response->assertDontSee('Marca:', false);
    }
}
