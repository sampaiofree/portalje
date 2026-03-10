<?php

namespace Tests\Feature;

use App\Models\AulasDemonstrativa;
use App\Models\Codigo_ref;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FreeCourseCtaTargetTest extends TestCase
{
    use RefreshDatabase;

    public function test_g_one_page_primary_cta_points_to_canonical_course_url_without_g_and_aula(): void
    {
        [$user, $curso] = $this->createAffiliateAndCourse();

        $response = $this->get("http://{$user->dominio}/{$curso->url}?g=1&aula=abc&ref=1&src=x");

        $response->assertOk();
        $response->assertSee('Quero Me Inscrever Agora');

        $ctaHref = $this->extractPrimaryEnrollmentCtaHref($response->getContent());

        $this->assertSame(
            "http://{$user->dominio}/{$curso->url}?ref=1&src=x",
            $ctaHref
        );
    }

    public function test_slug_w_g_one_keeps_whatsapp_flow_for_primary_cta(): void
    {
        [$user, $curso] = $this->createAffiliateAndCourse();

        $response = $this->get("http://{$user->dominio}/{$curso->url}/w?g=1&aula=abc&ref=1");

        $response->assertOk();
        $response->assertSee('Quero Me Inscrever Agora');

        $ctaHref = $this->extractPrimaryEnrollmentCtaHref($response->getContent());

        $this->assertStringStartsWith('https://wa.me/5511999999999?text=', $ctaHref);
    }

    public function test_g_one_page_shows_unavailable_message_when_no_valid_free_lessons_exist(): void
    {
        [$user, $curso] = $this->createAffiliateAndCourse();

        AulasDemonstrativa::create([
            'id_curso' => $curso->id,
            'aula_titulo' => 'Aula com link inválido',
            'aula_id_youtube' => 'link-quebrado',
        ]);

        $response = $this->get("http://{$user->dominio}/{$curso->url}?g=1");

        $response->assertOk();
        $response->assertSee('Não há aula gratuita disponível no momento');
        $response->assertSee('Não encontramos vídeos gratuitos válidos para este curso.');
        $response->assertSee('id="video-player-area-empty"', false);
        $response->assertDontSee('id="video-player-area"', false);
    }

    public function test_g_one_page_ignores_invalid_requested_video_and_uses_first_valid_lesson(): void
    {
        [$user, $curso] = $this->createAffiliateAndCourse();

        AulasDemonstrativa::create([
            'id_curso' => $curso->id,
            'aula_titulo' => 'Aula 1 válida',
            'aula_id_youtube' => 'https://youtu.be/dQw4w9WgXcQ',
        ]);

        AulasDemonstrativa::create([
            'id_curso' => $curso->id,
            'aula_titulo' => 'Aula 2 inválida',
            'aula_id_youtube' => 'youtube/invalido',
        ]);

        $response = $this->get("http://{$user->dominio}/{$curso->url}?g=1&aula=INVALIDA");

        $response->assertOk();
        $response->assertSee('id="video-player-area"', false);
        $response->assertSee('data-video-id="dQw4w9WgXcQ"', false);
        $response->assertSee('img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg');
    }

    /**
     * @return array{0: User, 1: Curso}
     */
    private function createAffiliateAndCourse(): array
    {
        $user = User::factory()->create([
            'dominio' => 'bruno.portalje.test',
            'whatsapp_atendimento' => '5511999999999',
            'formulario_pre_checkout' => true,
            'formulario_whatsapp' => true,
        ]);

        $curso = Curso::create([
            'ordem' => 1,
            'url' => 'curso-auxiliar-contabilidade',
            'titulo' => 'Curso Auxiliar Contabilidade',
            'headline' => 'Curso teste para validar CTA gratuito.',
            'gratuito' => false,
            'publicado' => true,
            'permitir_afiliacao' => true,
            'mostrar_na_pagina' => true,
            'capa_quadrada' => 'img/home_page/cartaestagio.webp',
            'capa_vertical' => 'img/home_page/cartaestagio.webp',
            'capa_horizontal' => 'img/home_page/certificadoNovo2.webp',
            'conteudo_principal' => '<ul><li>Módulo 1</li><li class="ql-indent-1">Aula 1</li></ul>',
            'conteudo_bonus' => '<ul><li>Bônus 1</li><li class="ql-indent-1">Item bônus</li></ul>',
            'areas_de_atuacao' => 'Contabilidade/Financeiro',
            'codigo_afiliado_plano_completo' => 'abc123',
            'link_checkout_completo' => 'https://go.hotmart.com/ORIGINAL?ap=abc123',
            'preco_parcelado_completo' => '12xR$19,19',
            'preco_cheio_completo' => 'R$197',
            'horas_completo' => 40,
            'numero_alunos' => 1200,
            'nota_avaliacao' => 4.8,
            'salario_maximo' => 2500,
        ]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFCTA123',
            'mostrar_curso' => true,
            'formulario_pre_checkout' => true,
        ]);

        return [$user, $curso];
    }

    private function extractPrimaryEnrollmentCtaHref(string $html): string
    {
        $matched = preg_match(
            "/<a href='([^']+)' class='btn-certificate'[^>]*>Quero Me Inscrever Agora<\\/a>/",
            $html,
            $matches
        );

        if ($matched !== 1 || empty($matches[1])) {
            $this->fail('Não foi possível localizar o botão primário "Quero Me Inscrever Agora" na página gratuita.');
        }

        return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
    }
}
