<?php

namespace Tests\Feature;

use App\Models\Codigo_ref;
use App\Models\Cupom;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCoursePricingDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_padrao_mode_keeps_complete_plus_basico_with_50off(): void
    {
        [$user, $curso] = $this->createAffiliateAndCurso('curso-modo-padrao');

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFPADRAO',
            'mostrar_curso' => true,
            'modo_precos' => 'padrao',
        ]);

        $response = $this->get('http://afiliado.test/' . $curso->url);

        $response->assertOk();
        $response->assertSee('Plano Completo');
        $response->assertSee('Plano Básico');
        $response->assertSee('offDiscount=50OFF', false);
        $this->assertSame(
            $this->buildExpectedAffiliateCheckoutUrl('REFPADRAO', 'abc123', [
                'src' => 'pagina_individual',
                'sck' => 'plano_completo',
            ]),
            $this->extractPlanCheckoutUrl($response->getContent(), 'completo')
        );
        $this->assertSame(
            $this->buildExpectedAffiliateCheckoutUrl('REFPADRAO', 'abc123', [
                'src' => 'pagina_individual',
                'offDiscount' => '50OFF',
                'sck' => 'plano_basico',
            ]),
            $this->extractPlanCheckoutUrl($response->getContent(), 'basico')
        );
    }

    public function test_um_preco_mode_renders_only_complete_with_selected_coupon(): void
    {
        [$user, $curso] = $this->createAffiliateAndCurso('curso-modo-um-preco');
        $cupomPrincipal = Cupom::create(['codigo' => 'MAIN25', 'desconto' => 25]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFUMPRECO',
            'mostrar_curso' => true,
            'modo_precos' => 'um_preco',
            'cupom_principal_id' => $cupomPrincipal->id,
        ]);

        $response = $this->get('http://afiliado.test/' . $curso->url);

        $response->assertOk();
        $response->assertSee('Plano Completo');
        $response->assertDontSee('Plano Básico');
        $response->assertSee('offDiscount=MAIN25', false);
        $this->assertSame(
            $this->buildExpectedAffiliateCheckoutUrl('REFUMPRECO', 'abc123', [
                'src' => 'pagina_individual',
                'offDiscount' => 'MAIN25',
                'sck' => 'plano_completo',
            ]),
            $this->extractPlanCheckoutUrl($response->getContent(), 'completo')
        );
        $heroPriceHighlight = $this->extractHeroPriceHighlight($response->getContent());
        $this->assertStringContainsString('Investimento do plano completo', $heroPriceHighlight);
        $this->assertStringNotContainsString('Investimento único de', $heroPriceHighlight);
    }

    public function test_dois_precos_mode_renders_complete_and_basico_with_distinct_coupons(): void
    {
        [$user, $curso] = $this->createAffiliateAndCurso('curso-modo-dois-precos');
        $cupomPrincipal = Cupom::create(['codigo' => 'MAIN10', 'desconto' => 10]);
        $cupomSecundario = Cupom::create(['codigo' => 'BASIC35', 'desconto' => 35]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFDOISPRECOS',
            'mostrar_curso' => true,
            'modo_precos' => 'dois_precos',
            'cupom_principal_id' => $cupomPrincipal->id,
            'cupom_secundario_id' => $cupomSecundario->id,
        ]);

        $response = $this->get('http://afiliado.test/' . $curso->url);

        $response->assertOk();
        $response->assertSee('Plano Completo');
        $response->assertSee('Plano Básico');
        $response->assertSee('offDiscount=MAIN10', false);
        $response->assertSee('offDiscount=BASIC35', false);
        $this->assertSame(
            $this->buildExpectedAffiliateCheckoutUrl('REFDOISPRECOS', 'abc123', [
                'src' => 'pagina_individual',
                'offDiscount' => 'MAIN10',
                'sck' => 'plano_completo',
            ]),
            $this->extractPlanCheckoutUrl($response->getContent(), 'completo')
        );
        $this->assertSame(
            $this->buildExpectedAffiliateCheckoutUrl('REFDOISPRECOS', 'abc123', [
                'src' => 'pagina_individual',
                'offDiscount' => 'BASIC35',
                'sck' => 'plano_basico',
            ]),
            $this->extractPlanCheckoutUrl($response->getContent(), 'basico')
        );
        $heroPriceHighlight = $this->extractHeroPriceHighlight($response->getContent());
        $this->assertStringContainsString('Investimento único de', $heroPriceHighlight);
        $this->assertStringNotContainsString('Investimento do plano completo', $heroPriceHighlight);
        $this->assertStringContainsString('data-lp-hero-cash', $heroPriceHighlight);
        $this->assertStringContainsString('hidden', $heroPriceHighlight);
        $this->assertSame(
            $this->extractHeroPriceValue($response->getContent()),
            $this->extractFirstPlanCardValue($response->getContent())
        );
    }

    public function test_invalid_or_missing_coupon_configuration_falls_back_to_padrao(): void
    {
        [$user, $curso] = $this->createAffiliateAndCurso('curso-fallback-padrao');
        $cupomPrincipal = Cupom::create(['codigo' => 'MAIN40', 'desconto' => 40]);
        $cupomSecundario = Cupom::create(['codigo' => 'SEC55', 'desconto' => 55]);

        $codigoRef = Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFFALLBACK',
            'mostrar_curso' => true,
            'modo_precos' => 'dois_precos',
            'cupom_principal_id' => $cupomPrincipal->id,
            'cupom_secundario_id' => $cupomSecundario->id,
        ]);

        $cupomSecundario->delete();
        $codigoRef->refresh();

        $response = $this->get('http://afiliado.test/' . $curso->url);

        $response->assertOk();
        $response->assertSee('Plano Completo');
        $response->assertSee('Plano Básico');
        $response->assertSee('offDiscount=50OFF', false);
    }

    public function test_public_page_requires_lead_form_when_formulario_pre_checkout_is_enabled(): void
    {
        [$user, $curso] = $this->createAffiliateAndCurso('curso-form-on');

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFFORMON',
            'mostrar_curso' => true,
            'modo_precos' => 'padrao',
            'formulario_pre_checkout' => true,
        ]);

        $response = $this->get('http://afiliado.test/' . $curso->url);

        $response->assertOk();
        $response->assertSee('data-requires-lead="1"', false);
    }

    public function test_public_page_renders_required_email_field_in_lead_modal(): void
    {
        [$user, $curso] = $this->createAffiliateAndCurso('curso-form-email');

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFFORMEMAIL',
            'mostrar_curso' => true,
            'modo_precos' => 'padrao',
            'formulario_pre_checkout' => true,
        ]);

        $response = $this->get('http://afiliado.test/' . $curso->url);

        $response->assertOk();
        $response->assertSee('id="lead_email"', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('type="email"', false);
        $this->assertMatchesRegularExpression('/id="lead_email"[^>]*required/u', $response->getContent());
    }

    public function test_lp_course_js_prefills_hotmart_checkout_with_email_and_single_phoneac(): void
    {
        $script = file_get_contents(public_path('js/lp-course.js'));

        $this->assertIsString($script);
        $this->assertStringContainsString("parsed.searchParams.set('email', buyerEmail);", $script);
        $this->assertStringContainsString("parsed.searchParams.set('phoneac', buyerDigits);", $script);
        $this->assertStringContainsString("parsed.searchParams.delete('phonenumber');", $script);
        $this->assertStringNotContainsString("parsed.searchParams.set('phonenumber',", $script);
    }

    public function test_public_page_bypasses_form_when_formulario_pre_checkout_is_disabled(): void
    {
        [$user, $curso] = $this->createAffiliateAndCurso('curso-form-off');

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFFORMOFF',
            'mostrar_curso' => true,
            'modo_precos' => 'padrao',
            'formulario_pre_checkout' => false,
        ]);

        $response = $this->get('http://afiliado.test/' . $curso->url);

        $response->assertOk();
        $response->assertSee('data-requires-lead="0"', false);
        $response->assertDontSee('data-requires-lead="1"', false);
    }

    public function test_public_page_serializes_countdown_configuration_for_alterar_preco_with_one_minute_timer(): void
    {
        [$user, $curso] = $this->createAffiliateAndCurso('curso-countdown-alterar');
        $cupomPrincipal = Cupom::create(['codigo' => 'MAIN10', 'desconto' => 10]);
        $cupomDestino = Cupom::create(['codigo' => 'UP40', 'desconto' => 40]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFCOUNTDOWN1',
            'mostrar_curso' => true,
            'modo_precos' => 'dois_precos',
            'cupom_principal_id' => $cupomPrincipal->id,
            'cupom_secundario_id' => $cupomDestino->id,
            'usar_contador' => true,
            'contador_minutos' => 1,
            'contador_acao' => 'alterar_preco',
            'contador_destino_oferta' => 'completo_cupom:' . $cupomDestino->id,
        ]);

        $response = $this->get('http://afiliado.test/' . $curso->url);
        $config = $this->extractLpCourseConfig($response->getContent());

        $this->assertTrue((bool) data_get($config, 'countdown.enabled'));
        $this->assertSame(1, data_get($config, 'countdown.minutes'));
        $this->assertSame('alterar_preco', data_get($config, 'countdown.action'));
        $this->assertSame('completo_cupom:' . $cupomDestino->id, data_get($config, 'countdown.destination_offer'));
        $this->assertIsString(data_get($config, 'countdown.storage_key'));
        $this->assertArrayHasKey('completo_cupom:' . $cupomDestino->id, data_get($config, 'pricing.offer_variants', []));
        $this->assertSame('completo', data_get($config, 'pricing.offer_variants.completo_cupom:' . $cupomDestino->id . '.plan'));
        $this->assertSame(
            $this->buildExpectedAffiliateCheckoutUrl('REFCOUNTDOWN1', 'abc123', [
                'src' => 'pagina_individual',
                'offDiscount' => 'UP40',
            ]),
            data_get($config, 'pricing.offer_variants.completo_cupom:' . $cupomDestino->id . '.checkout_url')
        );
        $this->assertSame(
            $this->buildExpectedAffiliateCheckoutUrl('REFCOUNTDOWN1', 'abc123', [
                'src' => 'pagina_individual',
                'offDiscount' => 'UP40',
            ]),
            data_get($config, 'pricing.offer_variants.basico_cupom:' . $cupomDestino->id . '.checkout_url')
        );
        $response->assertSee('01:00');
    }

    public function test_public_page_serializes_countdown_configuration_for_encerrar_basico(): void
    {
        [$user, $curso] = $this->createAffiliateAndCurso('curso-countdown-encerrar');

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFCOUNTDOWN2',
            'mostrar_curso' => true,
            'modo_precos' => 'padrao',
            'usar_contador' => true,
            'contador_minutos' => 5,
            'contador_acao' => 'encerrar_basico',
        ]);

        $response = $this->get('http://afiliado.test/' . $curso->url);
        $config = $this->extractLpCourseConfig($response->getContent());

        $this->assertTrue((bool) data_get($config, 'countdown.enabled'));
        $this->assertSame('encerrar_basico', data_get($config, 'countdown.action'));
        $this->assertSame('completo_padrao', data_get($config, 'pricing.current_complete_offer_key'));
        $this->assertSame('basico_padrao', data_get($config, 'pricing.current_basic_offer_key'));
        $this->assertArrayHasKey('basico_padrao', data_get($config, 'pricing.offer_variants', []));
    }

    public function test_public_page_serializes_countdown_configuration_for_whatsapp_waitlist(): void
    {
        [$user, $curso] = $this->createAffiliateAndCurso('curso-countdown-whatsapp');

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFCOUNTDOWNWA',
            'mostrar_curso' => true,
            'modo_precos' => 'padrao',
            'usar_contador' => true,
            'contador_minutos' => 5,
            'contador_acao' => 'whatsapp',
        ]);

        $response = $this->get('http://afiliado.test/' . $curso->url);
        $config = $this->extractLpCourseConfig($response->getContent());
        $html = $response->getContent();

        $this->assertTrue((bool) data_get($config, 'countdown.enabled'));
        $this->assertSame('whatsapp', data_get($config, 'countdown.action'));
        $this->assertNull(data_get($config, 'countdown.destination_offer'));
        $response->assertSee('data-lp-waitlist-block', false);
        $response->assertSee('data-lp-hero-waitlist', false);
        $response->assertSee('data-lp-primary-scroll-cta', false);
        $response->assertSee('Inscrições encerradas');
        $response->assertSee('Entrar na lista de espera');
        $response->assertSee(
            'https://wa.me/5511999999999?text=Ol%C3%A1%2C%20quero%20entrar%20na%20lista%20de%20espera%20do%20curso%20de%20Curso%20Teste',
            false
        );
        $this->assertMatchesRegularExpression('/data-lp-waitlist-block[^>]*hidden/u', $html);
    }

    public function test_public_page_renders_modules_as_accordion_and_anchor_ctas_to_planos(): void
    {
        [$user, $curso] = $this->createAffiliateAndCurso('curso-modulos-accordion');

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFACCORDION',
            'mostrar_curso' => true,
            'modo_precos' => 'padrao',
        ]);

        $response = $this->get('http://afiliado.test/' . $curso->url);
        $html = $response->getContent();

        $response->assertOk();
        $response->assertSee('class="lp-modules-accordion"', false);
        $this->assertTrue(
            str_contains($html, 'class="lp-module-item"') || str_contains($html, 'class="lp-module-empty"')
        );
        $response->assertSee('class="lp-btn lp-btn--small js-anchor-scroll"', false);
        $response->assertSee('<div class="lp-sticky-cta">', false);
        $response->assertSee('class="lp-btn js-anchor-scroll"', false);
        $this->assertSame(4, substr_count($html, 'href="#planos"'));
        $response->assertDontSee('Ver prévia do curso');
        $response->assertSee('class="lp-icon-sprite"', false);
        $response->assertSee('id="lp-icon-check-circle"', false);
        $response->assertSee('id="lp-icon-tag"', false);
        $response->assertSee('class="lp-icon lp-icon--sm"', false);
    }

    public function test_public_page_renders_testimonials_with_lazy_thumbnail_and_without_initial_embed(): void
    {
        [$user, $curso] = $this->createAffiliateAndCurso('curso-depoimentos');

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFDEPOIMENTOS',
            'mostrar_curso' => true,
            'modo_precos' => 'padrao',
        ]);

        $response = $this->get('http://afiliado.test/' . $curso->url);

        $videoIds = [
            'rejxwJ2lX-Q',
            '1hekoAyPVRs',
            'Mnn2yIAlhZk',
            '9mmtunKAnMY',
            'uQ5lB9r8ZlI',
            'dMIxLKj35aU',
            'gIV1MGief-0',
            'X1IJZkVXgBw',
            '1qWXa9F0qBw',
        ];

        $response->assertOk();
        $response->assertSee('id="depoimentos"', false);
        $response->assertSee('class="lp-testimonial__trigger js-testimonial-trigger"', false);
        $response->assertSee('class="lp-testimonial"', false);
        $response->assertSee('width="480"', false);
        $response->assertSee('height="270"', false);
        $response->assertSee('loading="lazy"', false);
        $response->assertSee('decoding="async"', false);
        $response->assertDontSee('youtube-nocookie.com/embed', false);
        $response->assertDontSee('Depoimento real de aluno');
        $response->assertDontSee('Reprodução direta na página');

        foreach ($videoIds as $videoId) {
            $response->assertSee('data-video-id="' . $videoId . '"', false);
            $response->assertSee('https://img.youtube.com/vi/' . $videoId . '/hqdefault.jpg', false);
        }
    }

    public function test_public_page_hides_installments_for_complete_plan_when_total_is_below_one_hundred(): void
    {
        [$user, $curso] = $this->createAffiliateAndCurso('curso-parcelamento-ajustado');

        $curso->preco_parcelado_completo = '12xR$6,53';
        $curso->preco_cheio_completo = 'R$78,00';
        $curso->save();

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFPARCELA11X',
            'mostrar_curso' => true,
            'modo_precos' => 'padrao',
        ]);

        $response = $this->get('http://afiliado.test/' . $curso->url);

        $response->assertOk();
        $response->assertSee('R$78,00', false);
        $response->assertDontSee('12xR$6,53', false);
        $this->assertMatchesRegularExpression(
            '/data-lp-plan-cash="completo"[^>]*hidden/u',
            $response->getContent()
        );
    }

    public function test_public_page_hides_installments_for_basico_and_keeps_complete_when_only_basico_is_below_one_hundred(): void
    {
        [$user, $curso] = $this->createAffiliateAndCurso('curso-parcelamento-misto');

        $curso->preco_parcelado_completo = '12xR$19,70';
        $curso->preco_cheio_completo = 'R$197,00';
        $curso->save();

        $cupomSecundario = Cupom::create(['codigo' => 'BASIC50', 'desconto' => 50]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFPARCELAMISTO',
            'mostrar_curso' => true,
            'modo_precos' => 'dois_precos',
            'cupom_secundario_id' => $cupomSecundario->id,
        ]);

        $response = $this->get('http://afiliado.test/' . $curso->url);

        $response->assertOk();
        $response->assertSee('12xR$19,70', false);
        $response->assertSee('ou R$197,00 à vista', false);
        $response->assertSee('R$98,50', false);
        $response->assertDontSee('12xR$9,85', false);
        $this->assertMatchesRegularExpression(
            '/data-lp-plan-cash="basico"[^>]*hidden/u',
            $response->getContent()
        );
    }

    public function test_public_page_keeps_installment_display_when_complete_total_is_equal_or_above_one_hundred(): void
    {
        [$user, $curso] = $this->createAffiliateAndCurso('curso-parcelamento-mantido');

        $curso->preco_parcelado_completo = '12xR$10,00';
        $curso->preco_cheio_completo = 'R$100,00';
        $curso->save();

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFPARCELA100',
            'mostrar_curso' => true,
            'modo_precos' => 'padrao',
        ]);

        $response = $this->get('http://afiliado.test/' . $curso->url);

        $response->assertOk();
        $response->assertSee('12xR$10,00', false);
        $response->assertSee('ou R$100,00 à vista', false);
    }

    /**
     * @return array{0: User, 1: Curso}
     */
    private function createAffiliateAndCurso(string $url): array
    {
        $user = User::factory()->create([
            'dominio' => 'afiliado.test',
            'whatsapp_atendimento' => '5511999999999',
            'whatsapp_atendimento_tempo' => 'até 1 hora',
            'formulario_pre_checkout' => false,
            'formulario_whatsapp' => false,
        ]);

        $curso = Curso::create([
            'titulo' => 'Curso Teste',
            'url' => $url,
            'publicado' => true,
            'permitir_afiliacao' => true,
            'preco_parcelado_completo' => '12xR$19,70',
            'preco_cheio_completo' => 'R$197',
            'codigo_afiliado_plano_completo' => 'abc123',
            'link_checkout_completo' => 'https://go.hotmart.com/T99999999A?ap=abc123',
            'areas_de_atuacao' => 'Atendimento/Vendas',
            'horas_completo' => 120,
            'headline' => 'Headline de teste',
        ]);

        return [$user, $curso];
    }

    private function extractHeroPriceHighlight(string $html): string
    {
        $matched = preg_match('/<div class="lp-price-highlight"[^>]*>(.*?)<\/div>/s', $html, $matches);
        if ($matched !== 1 || empty($matches[1])) {
            $this->fail('Bloco lp-price-highlight não encontrado na LP pública.');
        }

        return $matches[1];
    }

    private function extractHeroPriceValue(string $html): string
    {
        $matched = preg_match(
            '/<div class="lp-price-highlight"[^>]*>.*?<strong[^>]*>(.*?)<\/strong>.*?<\/div>/s',
            $html,
            $matches
        );

        if ($matched !== 1 || empty($matches[1])) {
            $this->fail('Valor principal do hero não encontrado no bloco lp-price-highlight.');
        }

        return trim(strip_tags(html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8')));
    }

    private function extractFirstPlanCardValue(string $html): string
    {
        $matched = preg_match('/<p class="lp-price-card__value"[^>]*>(.*?)<\/p>/', $html, $matches);
        if ($matched !== 1 || empty($matches[1])) {
            $this->fail('Valor do primeiro card de plano não encontrado.');
        }

        return trim(strip_tags(html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8')));
    }

    private function extractPlanCheckoutUrl(string $html, string $plan): string
    {
        $matched = preg_match(
            '/data-lp-plan-card="' . preg_quote($plan, '/') . '".*?data-checkout-url="([^"]+)"/s',
            $html,
            $matches
        );

        if ($matched !== 1 || empty($matches[1])) {
            $this->fail('Checkout do plano ' . $plan . ' não encontrado.');
        }

        return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
    }

    private function buildExpectedAffiliateCheckoutUrl(string $codigoRef, string $ap, array $params = []): string
    {
        $base = 'https://go.hotmart.com/' . $codigoRef . '?ap=' . $ap;

        if ($params === []) {
            return $base;
        }

        return $base . '&' . http_build_query($params);
    }

    private function extractLpCourseConfig(string $html): array
    {
        $matched = preg_match('/<script id="lp-course-config" type="application\/json">(.*?)<\/script>/s', $html, $matches);
        if ($matched !== 1 || empty($matches[1])) {
            $this->fail('Bloco lp-course-config não encontrado na LP pública.');
        }

        $decoded = json_decode(html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8'), true);
        if (!is_array($decoded)) {
            $this->fail('Configuração JSON da LP pública está inválida.');
        }

        return $decoded;
    }
}
