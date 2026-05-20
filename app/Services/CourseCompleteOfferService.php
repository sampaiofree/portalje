<?php

namespace App\Services;

use App\Models\Codigo_ref;
use App\Models\Curso;

class CourseCompleteOfferService
{
    public function __construct(
        private RootDomainCoursePublicStateService $pricingStateService
    ) {
    }

    public function completeOffer(Curso $curso, ?Codigo_ref $ref): array
    {
        foreach ($this->offersForCatalog($curso, $ref) as $offer) {
            if (($offer['label'] ?? '') === 'Plano completo') {
                return $offer;
            }
        }

        return [
            'label' => 'Plano completo',
            'price_value' => (string) ($curso->preco_parcelado_completo ?? 'Consulte'),
            'cash_value' => (string) ($curso->preco_cheio_completo ?? 'Consulte'),
            'show_cash_line' => trim((string) ($curso->preco_cheio_completo ?? '')) !== '',
            'checkout_url' => $this->checkoutBaseUrl($curso, $ref) ?: '#',
        ];
    }

    public function offersForCatalog(Curso $curso, ?Codigo_ref $ref): array
    {
        $dados = $this->pricingData($curso, $ref);

        $cursoAtual = $this->pricingStateService->prepareCoursePricingBase($curso);
        $this->pricingStateService->hydrateCourseForPublicState($cursoAtual, $dados);

        $cursoBasePricing = clone $cursoAtual;
        $this->pricingStateService->applyPricingConfigurationByCoupon($cursoAtual, $dados, false);

        $pricingConfig = $this->pricingStateService->buildPublicCoursePricingConfig(
            $cursoBasePricing,
            $cursoAtual,
            $dados,
            false
        );

        $cards = $pricingConfig['initial_state']['cards'] ?? [];
        $offers = [];

        if (is_array($cards['completo'] ?? null) && !empty($cards['completo'])) {
            $offers[] = $this->normalizeOffer($cards['completo'], 'Plano completo');
        }

        if (is_array($cards['basico'] ?? null) && !empty($cards['basico'])) {
            $offers[] = $this->normalizeOffer($cards['basico'], 'Plano básico');
        }

        return $offers;
    }

    public function checkoutBaseUrl(Curso $curso, ?Codigo_ref $ref): string
    {
        $codigoRef = trim((string) ($ref->codigo_ref ?? ''));
        $codigoAfiliado = trim((string) ($curso->codigo_afiliado_plano_completo ?? ''));

        if ($codigoRef !== '' && $codigoAfiliado !== '') {
            return "https://go.hotmart.com/{$codigoRef}?ap={$codigoAfiliado}";
        }

        return trim((string) ($curso->link_checkout_completo ?? ''));
    }

    private function pricingData(Curso $curso, ?Codigo_ref $ref): array
    {
        return [
            'link_checkout_completo' => $this->checkoutBaseUrl($curso, $ref),
            'modo_precos' => $this->configuredPriceMode($ref),
            'cupom_principal_id' => !empty($ref?->cupom_principal_id) ? (int) $ref->cupom_principal_id : null,
            'cupom_secundario_id' => !empty($ref?->cupom_secundario_id) ? (int) $ref->cupom_secundario_id : null,
            'formulario_pre_checkout' => true,
            'affiliate_code' => trim((string) ($ref->codigo_ref ?? '')),
            'user_id' => null,
            'usar_contador' => false,
            'contador_minutos' => null,
            'contador_acao' => null,
            'contador_destino_oferta' => null,
        ];
    }

    private function normalizeOffer(array $offer, string $label): array
    {
        return [
            'label' => $label,
            'price_value' => (string) ($offer['price_value'] ?? 'Consulte'),
            'cash_value' => (string) ($offer['cash_value'] ?? 'Consulte'),
            'show_cash_line' => (bool) ($offer['show_cash_line'] ?? false),
            'checkout_url' => (string) ($offer['checkout_url'] ?? '#'),
        ];
    }

    private function configuredPriceMode(?Codigo_ref $ref): string
    {
        if (!$ref || !array_key_exists('modo_precos', $ref->getAttributes())) {
            return 'padrao';
        }

        return in_array($ref->modo_precos, ['padrao', 'um_preco', 'dois_precos'], true)
            ? $ref->modo_precos
            : 'padrao';
    }
}
