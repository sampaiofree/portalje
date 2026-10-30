<?php

namespace Tests\Feature;

use App\Models\Cupom;
use App\Models\Curso;
use App\Services\RootDomainCoursePublicStateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PricingQueryEfficiencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_selected_coupons_are_loaded_and_checkout_prices_are_preserved(): void
    {
        $principal = Cupom::create(['codigo' => 'MAIN10', 'desconto' => 10]);
        $secondary = Cupom::create(['codigo' => 'BASIC50', 'desconto' => 50]);
        Cupom::create(['codigo' => 'UNUSED', 'desconto' => 80]);
        $course = $this->course();
        DB::enableQueryLog();
        app(RootDomainCoursePublicStateService::class)->applyPricingConfigurationByCoupon($course, [
            'modo_precos' => 'dois_precos', 'cupom_principal_id' => $principal->id, 'cupom_secundario_id' => $secondary->id,
        ], false);
        $queries = collect(DB::getQueryLog())->filter(fn ($query) => str_contains(strtolower($query['query']), 'from "cupons"'))->values();
        DB::disableQueryLog();

        $this->assertCount(1, $queries);
        $this->assertStringContainsString(' in ', strtolower($queries[0]['query']));
        $this->assertSame([$principal->id, $secondary->id], $queries[0]['bindings']);
        $this->assertSame('R$180,00', $course->preco_cheio_completo);
        $this->assertSame('R$100,00', $course->preco_cheio_basico);
        $this->assertStringContainsString('offDiscount=MAIN10', $course->link_checkout_completo);
        $this->assertStringContainsString('offDiscount=BASIC50', $course->link_checkout_basico);
    }

    public function test_one_price_without_principal_preserves_empty_table_fallback(): void
    {
        $service = app(RootDomainCoursePublicStateService::class);
        $course = $this->course();
        $service->applyPricingConfigurationByCoupon($course, ['modo_precos' => 'um_preco'], false);
        $this->assertSame('padrao', $course->modo_precos);

        Cupom::create(['codigo' => 'EXISTING', 'desconto' => 10]);
        $course = $this->course();
        $service->applyPricingConfigurationByCoupon($course, ['modo_precos' => 'um_preco', 'cupom_secundario_id' => 9999], false);
        $this->assertSame('um_preco', $course->modo_precos);
        $this->assertNull($course->cupom_secundario_id);
    }

    public function test_default_price_does_not_read_coupons(): void
    {
        DB::enableQueryLog();
        app(RootDomainCoursePublicStateService::class)->applyPricingConfigurationByCoupon($this->course(), ['modo_precos' => 'padrao'], false);
        $queries = collect(DB::getQueryLog())->filter(fn ($query) => str_contains(strtolower($query['query']), 'from "cupons"'));
        DB::disableQueryLog();
        $this->assertCount(0, $queries);
    }

    public function test_invalid_coupon_configurations_fall_back_to_default(): void
    {
        $coupon = Cupom::create(['codigo' => 'SAME', 'desconto' => 10]);
        $service = app(RootDomainCoursePublicStateService::class);

        foreach ([
            ['modo_precos' => 'um_preco', 'cupom_principal_id' => 9999],
            ['modo_precos' => 'dois_precos', 'cupom_principal_id' => $coupon->id, 'cupom_secundario_id' => $coupon->id],
            ['modo_precos' => 'dois_precos', 'cupom_secundario_id' => 9999],
        ] as $data) {
            $course = $this->course();
            $service->applyPricingConfigurationByCoupon($course, $data, false);
            $this->assertSame('padrao', $course->modo_precos);
            $this->assertNull($course->cupom_principal_id);
            $this->assertNull($course->cupom_secundario_id);
            $this->assertSame('https://go.hotmart.com/REF?ap=abc', $course->link_checkout_completo);
        }
    }

    public function test_known_missing_config_uses_course_defaults_without_another_query(): void
    {
        $course = $this->course();
        $course->mostrar_na_pagina = true;
        $service = app(RootDomainCoursePublicStateService::class);
        DB::enableQueryLog();
        $this->assertTrue($service->isVisibleForCourse($course, null, true));
        $service->applyConfigOnCourse($course, null, ['formulario_pre_checkout' => true], true);
        $queries = collect(DB::getQueryLog())->filter(fn ($query) => str_contains(strtolower($query['query']), 'root_domain_course_configs'));
        DB::disableQueryLog();

        $this->assertCount(0, $queries);
        $this->assertTrue($course->mostrar_curso);
        $this->assertSame('padrao', $course->modo_precos);
        $this->assertTrue($course->formulario_pre_checkout);
    }

    public function test_compact_affiliate_previews_have_the_same_cash_values_as_full_previews(): void
    {
        $coupons = collect([
            Cupom::create(['codigo' => 'TEN', 'desconto' => 10]),
            Cupom::create(['codigo' => 'HALF', 'desconto' => 50]),
        ]);
        $service = app(RootDomainCoursePublicStateService::class);
        foreach (['R$200,00', null] as $price) {
            $course = $this->course();
            $course->preco_cheio_completo = $price;
            $full = $service->buildAffiliateCoursePricingPreviewVariants($course, $coupons);
            $compact = $service->buildAffiliateCoursePricingPreviewVariants($course, $coupons, true);
            foreach ($full as $key => $offer) {
                $this->assertSame($offer['cash_value'], $compact[$key]['cash_value']);
            }
        }
    }

    public function test_required_offers_preserve_current_prices_and_countdown_destination(): void
    {
        $principal = Cupom::create(['codigo' => 'MAIN', 'desconto' => 10]);
        $secondary = Cupom::create(['codigo' => 'BASIC', 'desconto' => 50]);
        $destination = Cupom::create(['codigo' => 'LATER', 'desconto' => 25]);
        $service = app(RootDomainCoursePublicStateService::class);
        $base = $this->course();
        $base->parcelamento = 10;
        $current = clone $base;
        $data = ['modo_precos' => 'dois_precos', 'cupom_principal_id' => $principal->id,
            'cupom_secundario_id' => $secondary->id, 'usar_contador' => true,
            'contador_minutos' => 1, 'contador_acao' => 'alterar_preco',
            'contador_destino_oferta' => 'completo_cupom:' . $destination->id];
        $service->applyPricingConfigurationByCoupon($current, $data, false);
        $full = $service->buildPublicCoursePricingConfig($base, $current, $data, false);
        $compact = $service->buildPublicCoursePricingConfig($base, $current, $data, false, true);

        $this->assertSame($full['initial_state'], $compact['initial_state']);
        $this->assertSame($full['countdown'], $compact['countdown']);
        foreach ([$compact['current_complete_offer_key'], $compact['current_basic_offer_key'], $data['contador_destino_oferta']] as $key) {
            $this->assertSame($full['offer_variants'][$key], $compact['offer_variants'][$key]);
        }
        $this->assertCount(4, $compact['offer_variants']);
    }

    private function course(): Curso
    {
        return new Curso([
            'preco_cheio_completo' => 'R$200,00',
            'preco_parcelado_completo' => '10xR$20,00',
            'link_checkout_completo' => 'https://go.hotmart.com/REF?ap=abc',
            'link_checkout_basico' => 'https://go.hotmart.com/REF?ap=abc&offDiscount=50OFF',
        ]);
    }
}
