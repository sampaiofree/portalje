<?php

namespace Tests\Feature;

use App\Models\Cupom;
use App\Models\Curso;
use App\Models\Dados_portal;
use App\Models\RootDomainCourseConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiConsultarCursosRootDomainConfigTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_course_listing_hides_root_domain_hidden_courses_but_keeps_visible_and_fallback_visible(): void
    {
        $cursoVisivel = $this->createPublishedCourse('api-root-visible', [
            'titulo' => 'Curso API Visivel',
            'mostrar_na_pagina' => true,
        ]);
        $cursoOculto = $this->createPublishedCourse('api-root-hidden', [
            'titulo' => 'Curso API Oculto',
            'mostrar_na_pagina' => true,
        ]);
        $cursoFallback = $this->createPublishedCourse('api-root-fallback', [
            'titulo' => 'Curso API Fallback',
            'mostrar_na_pagina' => true,
        ]);
        $cursoNaoPublicado = $this->createPublishedCourse('api-root-unpublished', [
            'titulo' => 'Curso API Nao Publicado',
            'publicado' => false,
            'mostrar_na_pagina' => true,
        ]);

        RootDomainCourseConfig::create([
            'curso_id' => $cursoVisivel->id,
            'mostrar_curso' => true,
            'formulario_pre_checkout' => true,
            'modo_precos' => 'padrao',
            'usar_contador' => false,
        ]);

        RootDomainCourseConfig::create([
            'curso_id' => $cursoOculto->id,
            'mostrar_curso' => false,
            'formulario_pre_checkout' => true,
            'modo_precos' => 'padrao',
            'usar_contador' => false,
        ]);

        $response = $this->get('/api/consultar/cursos');
        $content = $response->getContent();

        $response->assertOk();
        $this->assertStringContainsString('Curso API Visivel', $content);
        $this->assertStringContainsString('Curso API Fallback', $content);
        $this->assertStringNotContainsString('Curso API Oculto', $content);
        $this->assertStringNotContainsString('Curso API Nao Publicado', $content);
    }

    public function test_api_course_lookup_by_id_returns_hidden_course_with_visibility_line(): void
    {
        $curso = $this->createPublishedCourse('api-root-hidden-by-id', [
            'titulo' => 'Curso API Oculto por ID',
            'mostrar_na_pagina' => true,
        ]);

        RootDomainCourseConfig::create([
            'curso_id' => $curso->id,
            'mostrar_curso' => false,
            'formulario_pre_checkout' => true,
            'modo_precos' => 'padrao',
            'usar_contador' => false,
        ]);

        $response = $this->get('/api/consultar/cursos/' . $curso->id);
        $content = $response->getContent();

        $response->assertOk();
        $this->assertStringContainsString('Curso API Oculto por ID', $content);
        $this->assertStringContainsString('**Visibilidade nos domínios raiz:** Oculto', $content);
    }

    public function test_api_course_lookup_reflects_root_domain_one_price_and_countdown_configuration(): void
    {
        $curso = $this->createPublishedCourse('api-root-one-price', [
            'titulo' => 'Curso API Um Preco',
            'preco_parcelado_completo' => '10xR$20,00',
            'preco_cheio_completo' => 'R$200',
        ]);
        $cupom = Cupom::create([
            'codigo' => 'ROOT25',
            'desconto' => 25,
        ]);

        RootDomainCourseConfig::create([
            'curso_id' => $curso->id,
            'mostrar_curso' => true,
            'formulario_pre_checkout' => false,
            'modo_precos' => 'um_preco',
            'cupom_principal_id' => $cupom->id,
            'usar_contador' => true,
            'contador_minutos' => 1,
            'contador_acao' => 'alterar_preco',
            'contador_destino_oferta' => 'completo_cupom:' . $cupom->id,
        ]);

        $response = $this->get('/api/consultar/cursos/' . $curso->id);
        $content = $response->getContent();

        $response->assertOk();
        $this->assertStringContainsString('**Formulário antes do checkout:** Não', $content);
        $this->assertStringContainsString('**Modo de preços:** um_preco', $content);
        $this->assertStringContainsString('**Plano completo:** 10xR$15,00 (ou R$150,00 à vista no PIX)', $content);
        $this->assertStringContainsString('**Pagamento plano completo:** https://go.hotmart.com/T99999999A?ap=abc123&hideBillet=1&offDiscount=ROOT25', $content);
        $this->assertStringNotContainsString('**Plano básico:**', $content);
        $this->assertStringContainsString('**Contador:** 1 minuto | ação: alterar_preco | destino: Plano completo com cupom ROOT25', $content);
    }

    public function test_api_course_lookup_reflects_root_domain_two_price_configuration(): void
    {
        $curso = $this->createPublishedCourse('api-root-two-prices', [
            'titulo' => 'Curso API Dois Precos',
            'preco_parcelado_completo' => '10xR$20,00',
            'preco_cheio_completo' => 'R$200',
        ]);
        $cupomPrincipal = Cupom::create([
            'codigo' => 'ROOT10',
            'desconto' => 10,
        ]);
        $cupomSecundario = Cupom::create([
            'codigo' => 'ROOT50',
            'desconto' => 50,
        ]);

        RootDomainCourseConfig::create([
            'curso_id' => $curso->id,
            'mostrar_curso' => true,
            'formulario_pre_checkout' => true,
            'modo_precos' => 'dois_precos',
            'cupom_principal_id' => $cupomPrincipal->id,
            'cupom_secundario_id' => $cupomSecundario->id,
            'usar_contador' => false,
        ]);

        $response = $this->get('/api/consultar/cursos/' . $curso->id);
        $content = $response->getContent();

        $response->assertOk();
        $this->assertStringContainsString('**Modo de preços:** dois_precos', $content);
        $this->assertStringContainsString('**Plano completo:** 10xR$18,00 (ou R$180,00 à vista no PIX)', $content);
        $this->assertStringContainsString('**Pagamento plano completo:** https://go.hotmart.com/T99999999A?ap=abc123&hideBillet=1&offDiscount=ROOT10', $content);
        $this->assertStringContainsString('**Plano básico:** 10xR$10,00 (ou R$100,00 à vista no PIX)', $content);
        $this->assertStringContainsString('**Pagamento plano básico:** https://go.hotmart.com/T99999999A?ap=abc123&hideBillet=1&offDiscount=ROOT50', $content);
        $this->assertStringNotContainsString('**Contador:**', $content);
    }

    public function test_api_course_lookup_uses_root_domain_fallback_values_without_custom_config_and_hotmart_lookup_still_works(): void
    {
        $curso = $this->createPublishedCourse('api-root-fallback-values', [
            'titulo' => 'Curso API Fallback Valores',
            'codigo_id_hotmart' => 'HOTMART-API-123',
            'preco_parcelado_completo' => '10xR$20,00',
            'preco_cheio_completo' => 'R$200',
            'mostrar_na_pagina' => true,
        ]);

        Dados_portal::query()->update([
            'formulario_pre_checkout' => false,
        ]);

        $response = $this->get('/api/consultar/cursos/HOTMART-API-123');
        $content = $response->getContent();

        $response->assertOk();
        $this->assertStringContainsString('Curso API Fallback Valores', $content);
        $this->assertStringContainsString('**Visibilidade nos domínios raiz:** Visível', $content);
        $this->assertStringContainsString('**Formulário antes do checkout:** Não', $content);
        $this->assertStringContainsString('**Modo de preços:** padrao', $content);
        $this->assertStringContainsString('**Plano completo:** 10xR$20,00 (ou R$200,00 à vista no PIX)', $content);
        $this->assertStringContainsString('**Pagamento plano completo:** https://go.hotmart.com/T99999999A?ap=abc123&hideBillet=1', $content);
        $this->assertStringContainsString('**Plano básico:** 10xR$10,00 (ou R$100,00 à vista no PIX)', $content);
        $this->assertStringContainsString('**Pagamento plano básico:** https://go.hotmart.com/T99999999A?ap=abc123&hideBillet=1&offDiscount=50OFF', $content);
        $this->assertStringNotContainsString('**Contador:**', $content);
        $this->assertStringNotContainsString('**Configuração raiz personalizada:** Sim', $content);
    }

    private function createPublishedCourse(string $url, array $overrides = []): Curso
    {
        return Curso::create(array_merge([
            'ordem' => 1,
            'titulo' => 'Curso Teste API Domínio Raiz',
            'url' => $url,
            'publicado' => true,
            'permitir_afiliacao' => true,
            'mostrar_na_pagina' => true,
            'headline' => 'Headline de teste para API de domínios raiz.',
            'preco_parcelado_completo' => '12xR$19,70',
            'preco_cheio_completo' => 'R$197',
            'codigo_id_hotmart' => 'HOT-' . strtoupper($url),
            'codigo_afiliado_plano_completo' => 'abc123',
            'link_checkout_completo' => 'https://go.hotmart.com/T99999999A?ap=abc123',
            'areas_de_atuacao' => 'Atendimento/Vendas',
            'horas_completo' => 120,
            'numero_alunos' => 1200,
            'nota_avaliacao' => 4.8,
        ], $overrides));
    }
}
