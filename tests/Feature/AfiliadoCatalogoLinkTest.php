<?php

namespace Tests\Feature;

use App\Models\Codigo_ref;
use App\Models\Cupom;
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
            'preco_parcelado_completo' => '12xR$24,75',
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
        $response->assertSee('- Carga horária: 160 horas', false);
        $response->assertSee('- Professor: Maria Souza', false);
        $response->assertSee('- Plano completo: 12xR$24,75', false);
        $response->assertSee('- À vista (plano completo): R$297,00', false);
        $response->assertSee('- Checkout (plano completo): https://checkout.portalje.test/curso-atendente-farmacia', false);
        $response->assertSee('- Plano básico: 12xR$12,00', false);
        $response->assertSee('- À vista (plano básico): R$148,50', false);
        $response->assertSee('- Checkout (plano básico): https://checkout.portalje.test/curso-atendente-farmacia&offDiscount=50OFF', false);
        $response->assertDontSee('Disponibilidade:', false);
        $response->assertDontSee('Condição:', false);
        $response->assertDontSee('Imagem:', false);
        $response->assertDontSee('Marca:', false);
    }

    public function test_catalogo_markdown_shows_two_plan_configuration_with_discounted_basic_checkout(): void
    {
        $user = User::factory()->create([
            'dominio' => 'afiliado-dois-planos.portalje.test',
        ]);

        $curso = Curso::create([
            'ordem' => 1,
            'url' => 'curso-assistente-administrativo',
            'titulo' => 'Curso Assistente Administrativo',
            'headline' => 'Curso teste para validar dois planos no catálogo markdown.',
            'gratuito' => false,
            'publicado' => true,
            'permitir_afiliacao' => true,
            'mostrar_na_pagina' => true,
            'preco_cheio_completo' => 'R$300',
            'preco_parcelado_completo' => '12xR$25,00',
            'codigo_afiliado_plano_completo' => 'abc123',
            'horas_completo' => '200',
            'professor_nome' => 'João Lima',
        ]);

        $cupomPrincipal = Cupom::create([
            'codigo' => 'MAIN15',
            'desconto' => 15,
        ]);

        $cupomSecundario = Cupom::create([
            'codigo' => 'BASIC40',
            'desconto' => 40,
        ]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFCAT002',
            'mostrar_curso' => true,
            'modo_precos' => 'dois_precos',
            'cupom_principal_id' => $cupomPrincipal->id,
            'cupom_secundario_id' => $cupomSecundario->id,
        ]);

        $response = $this->get("http://{$user->dominio}/afiliado/catalogo2");

        $response->assertOk();
        $response->assertSee('- Plano completo: 12xR$21,25', false);
        $response->assertSee('- À vista (plano completo): R$255,00', false);
        $response->assertSee('- Checkout (plano completo): https://go.hotmart.com/REFCAT002?ap=abc123&offDiscount=MAIN15', false);
        $response->assertSee('- Plano básico: 12xR$15,00', false);
        $response->assertSee('- À vista (plano básico): R$180,00', false);
        $response->assertSee('- Checkout (plano básico): https://go.hotmart.com/REFCAT002?ap=abc123&offDiscount=BASIC40', false);
    }
}
