<?php

namespace Tests\Feature;

use App\Models\Combo;
use App\Models\Curso;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComboPublicPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_combo_public_page_renders_with_public_layout_and_tracking_cta(): void
    {
        $combo = Combo::create([
            'titulo' => 'Combo Profissoes',
            'headline' => 'Aprenda com dois cursos completos e acelere sua qualificacao.',
            'descricao_curta' => '<p>Uma trilha com foco pratico para quem quer ampliar oportunidades no mercado.</p>',
            'url' => 'combo-profissoes',
            'link_checkout' => 'https://checkout.test/combo?origem=meta',
            'preco_parcelado' => '12x de R$ 29,70',
            'preco' => 'R$ 297,00',
        ]);

        $cursoAtendimento = Curso::create([
            'titulo' => 'Atendimento Profissional',
            'url' => 'atendimento-profissional',
            'headline' => 'Aprenda a lidar com clientes e rotinas de atendimento.',
            'areas_de_atuacao' => 'Atendimento/Vendas',
            'horas_completo' => 120,
            'conteudo_principal' => '<ul><li>Modulo de Atendimento</li><li class="ql-indent-1">Boas praticas</li></ul>',
        ]);

        $cursoAdministrativo = Curso::create([
            'titulo' => 'Auxiliar Administrativo',
            'url' => 'auxiliar-administrativo',
            'headline' => 'Entenda os processos essenciais da rotina administrativa.',
            'areas_de_atuacao' => 'Administrativo/Vendas',
            'horas_completo' => 80,
            'conteudo_principal' => '<ul><li>Modulo Administrativo</li><li class="ql-indent-1">Rotinas e documentos</li></ul>',
        ]);

        $combo->cursos()->attach([$cursoAtendimento->id, $cursoAdministrativo->id]);

        $response = $this->get('/combo/combo-profissoes');

        $response->assertOk();
        $response->assertSee('Combo Profissoes');
        $response->assertSee('Aprenda com dois cursos completos e acelere sua qualificacao.');
        $response->assertSee('2 cursos incluidos');
        $response->assertSee('Carga horaria total de ate 200 horas');
        $response->assertSee('Atendimento');
        $response->assertSee('Administrativo');
        $response->assertSee('Modulo de Atendimento');
        $response->assertSee('Modulo Administrativo');
        $response->assertSee('https://checkout.test/combo?origem=meta&amp;sck=plano_completo', false);
    }

    public function test_combo_public_page_returns_404_when_combo_is_missing(): void
    {
        $this->get('/combo/inexistente')->assertNotFound();
    }

    public function test_combo_public_page_uses_fallbacks_for_missing_description_hours_and_areas(): void
    {
        $combo = Combo::create([
            'titulo' => 'Combo Inicial',
            'headline' => 'Conheca o combo inicial.',
            'descricao_curta' => null,
            'url' => 'combo-inicial',
            'link_checkout' => 'https://checkout.test/combo-inicial',
            'preco_parcelado' => '6x de R$ 19,70',
            'preco' => 'R$ 118,20',
        ]);

        $curso = Curso::create([
            'titulo' => 'Curso Unico',
            'url' => 'curso-unico',
            'headline' => 'Base para quem esta comecando.',
            'areas_de_atuacao' => null,
            'horas_completo' => null,
            'conteudo_principal' => null,
        ]);

        $combo->cursos()->attach($curso->id);

        $response = $this->get('/combo/combo-inicial');

        $response->assertOk();
        $response->assertSee('Conheca o combo inicial.');
        $response->assertSee('Este combo reune cursos com aplicacao pratica em diferentes frentes do mercado de trabalho.');
        $response->assertSee('Conteudo detalhado nao informado.');
        $response->assertSee('1 curso incluido');
        $response->assertDontSee('Carga horaria total de ate');
        $response->assertSee('https://checkout.test/combo-inicial?sck=plano_completo', false);
    }
}
