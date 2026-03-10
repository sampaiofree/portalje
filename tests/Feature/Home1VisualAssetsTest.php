<?php

namespace Tests\Feature;

use App\Models\Curso;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Home1VisualAssetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_home1_renders_new_visual_assets_and_core_sections(): void
    {
        Curso::create([
            'titulo' => 'Curso Home Teste',
            'url' => 'curso-home-teste',
            'publicado' => true,
            'permitir_afiliacao' => true,
            'preco_cheio_completo' => 'R$197',
            'codigo_afiliado_plano_completo' => 'abc123',
            'link_checkout_completo' => 'https://go.hotmart.com/T99999999A?ap=abc123',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('css/home-course.css');
        $response->assertSee('js/home-course.js');
        $response->assertSee('class="home-course color1"', false);
        $response->assertSee('id="sessao_cursos"', false);
        $response->assertSee('id="inscricaoModal"', false);
        $response->assertSee('class="hero home-section home-section--hero', false);
    }
}
