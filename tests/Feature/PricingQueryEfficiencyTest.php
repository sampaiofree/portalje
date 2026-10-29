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
