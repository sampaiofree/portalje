<?php

namespace Tests\Feature;

use App\Models\Codigo_ref;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CursoOrderPropagationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_update_order_normalizes_sequence_without_holes(): void
    {
        $cursoA = $this->createCurso('curso-a-order', 'Curso A', 10);
        $cursoB = $this->createCurso('curso-b-order', 'Curso B', 20);
        $cursoC = $this->createCurso('curso-c-order', 'Curso C', 30);

        $response = $this->postJson(route('adm_cursos_update_order'), [
            'order' => [
                ['id' => $cursoC->id, 'ordem' => 98],
                ['id' => $cursoA->id, 'ordem' => 12],
            ],
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertSame(1, (int) $cursoC->fresh()->ordem);
        $this->assertSame(2, (int) $cursoA->fresh()->ordem);
        $this->assertSame(3, (int) $cursoB->fresh()->ordem);
    }

    public function test_home_routes_follow_global_ordem_without_gratuito_priority(): void
    {
        $cursoOrdemDois = $this->createCurso('curso-ordem-dois-gratuito', 'Curso Ordem Dois Gratuito', 2, true);
        $cursoOrdemUm = $this->createCurso('curso-ordem-um', 'Curso Ordem Um', 1, false);

        $rootResponse = $this->get('/');
        $rootResponse->assertOk();

        $rootHtml = $rootResponse->getContent();
        $rootPosUm = strpos($rootHtml, $cursoOrdemUm->titulo);
        $rootPosDois = strpos($rootHtml, $cursoOrdemDois->titulo);

        $this->assertNotFalse($rootPosUm);
        $this->assertNotFalse($rootPosDois);
        $this->assertLessThan($rootPosDois, $rootPosUm);

        $cursosResponse = $this->get('/cursos');
        $cursosResponse->assertOk();

        $cursosHtml = $cursosResponse->getContent();
        $cursosPosUm = strpos($cursosHtml, $cursoOrdemUm->titulo);
        $cursosPosDois = strpos($cursosHtml, $cursoOrdemDois->titulo);

        $this->assertNotFalse($cursosPosUm);
        $this->assertNotFalse($cursosPosDois);
        $this->assertLessThan($cursosPosDois, $cursosPosUm);
    }

    public function test_w3_cards_follow_same_global_ordem_defined_in_admin(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'dominio' => 'afiliado-order.test',
            'whatsapp_atendimento' => '5511999999999',
            'formulario_whatsapp' => true,
            'formulario_pre_checkout' => true,
        ]);

        $cursoOrdemDois = $this->createCurso('curso-ordem-dois-w3', 'Curso Ordem Dois W3', 2);
        $cursoOrdemUm = $this->createCurso('curso-ordem-um-w3', 'Curso Ordem Um W3', 1);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $cursoOrdemDois->id,
            'codigo_ref' => 'REFW3B' . $cursoOrdemDois->id,
            'mostrar_curso' => true,
        ]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $cursoOrdemUm->id,
            'codigo_ref' => 'REFW3A' . $cursoOrdemUm->id,
            'mostrar_curso' => true,
        ]);

        $response = $this->get('http://afiliado-order.test/w3');
        $response->assertOk();
        $response->assertViewIs('home_e_cursos.w3');

        $html = $response->getContent();
        $posUm = strpos($html, 'data-course-title="' . $cursoOrdemUm->titulo . '"');
        $posDois = strpos($html, 'data-course-title="' . $cursoOrdemDois->titulo . '"');

        $this->assertNotFalse($posUm);
        $this->assertNotFalse($posDois);
        $this->assertLessThan($posDois, $posUm);
    }

    private function createCurso(string $url, string $titulo, int $ordem, bool $gratuito = false): Curso
    {
        return Curso::create([
            'ordem' => $ordem,
            'url' => $url,
            'gratuito' => $gratuito,
            'publicado' => true,
            'permitir_afiliacao' => true,
            'mostrar_na_pagina' => true,
            'titulo' => $titulo,
            'headline' => 'Headline de teste para ' . $titulo,
            'capa_vertical' => 'img/home_page/cartaestagio.webp',
            'capa_quadrada' => 'img/home_page/cartaestagio.webp',
            'capa_horizontal' => 'img/home_page/certificadoNovo2.webp',
            'horas_completo' => 40,
            'numero_alunos' => 1200,
            'nota_avaliacao' => 4.8,
            'codigo_afiliado_plano_completo' => 'abc123',
            'link_checkout_completo' => 'https://go.hotmart.com/abc123?ap=abc123',
            'preco_cheio_completo' => 'R$197,00',
            'preco_parcelado_completo' => '12xR$19,70',
        ]);
    }
}
