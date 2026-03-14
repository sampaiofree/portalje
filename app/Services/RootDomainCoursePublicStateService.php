<?php

namespace App\Services;

use App\Models\Curso;
use App\Models\Cupom;
use App\Models\Dados_portal;
use App\Models\RootDomainCourseConfig;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Throwable;

class RootDomainCoursePublicStateService
{
    public function portalData(): array
    {
        $defaults = [
            'telefone_suporte_alunos' => '5511982671533',
            'whatsapp_atendimento_tempo' => 'Seg a Sex 08:00-18:00',
            'formulario_pre_checkout' => true,
            'formulario_whatsapp' => true,
        ];

        try {
            if (!Schema::hasTable('portal_informacoes')) {
                return $defaults;
            }

            $dados = Dados_portal::first();

            if (!$dados) {
                return $defaults;
            }

            return array_merge($defaults, $dados->toArray());
        } catch (Throwable $e) {
            return $defaults;
        }
    }

    public function rootPortalHosts(): array
    {
        return [
            'portalje.org',
            'dns.portalje.org',
            'jovemempreendedor.org',
            'dns.jovemempreendedor.org',
        ];
    }

    public function normalizeHost(string $dominio): string
    {
        $dominio = strtolower(trim($dominio));
        if ($dominio === '') {
            return '';
        }

        if (!str_contains($dominio, '://')) {
            $dominio = 'https://' . $dominio;
        }

        $host = (string) (parse_url($dominio, PHP_URL_HOST) ?? '');
        $host = preg_replace('/^www\./', '', strtolower(trim($host)));

        return $host ?? '';
    }

    public function isRootPortalHost(?string $dominio): bool
    {
        $dominio = $this->normalizeHost((string) $dominio);

        return in_array($dominio, $this->rootPortalHosts(), true);
    }

    public function configsByCourseId(): Collection
    {
        if (!Schema::hasTable('root_domain_course_configs')) {
            return collect();
        }

        return RootDomainCourseConfig::query()->get()->keyBy('curso_id');
    }

    public function configForCourse(?Curso $curso): ?RootDomainCourseConfig
    {
        if (!$curso || !Schema::hasTable('root_domain_course_configs')) {
            return null;
        }

        return RootDomainCourseConfig::query()
            ->where('curso_id', $curso->id)
            ->first();
    }

    public function isVisibleForCourse(Curso $curso, ?RootDomainCourseConfig $config = null): bool
    {
        $config = $config ?: $this->configForCourse($curso);

        return $config
            ? (bool) $config->mostrar_curso
            : (bool) ($curso->mostrar_na_pagina ?? false);
    }

    public function applyConfigOnCourse(Curso $curso, ?RootDomainCourseConfig $config = null, ?array $portalData = null): void
    {
        $portalData = $portalData ?: $this->portalData();
        $config = $config ?: $this->configForCourse($curso);

        $curso->root_domain_course_config_id = $config->id ?? null;
        $curso->mostrar_curso = $config
            ? (bool) $config->mostrar_curso
            : (bool) ($curso->mostrar_na_pagina ?? false);
        $curso->mostrar_na_pagina = $config
            ? (bool) $config->mostrar_curso
            : (bool) ($curso->mostrar_na_pagina ?? false);
        $curso->modo_precos = $config && in_array((string) $config->modo_precos, ['padrao', 'um_preco', 'dois_precos'], true)
            ? (string) $config->modo_precos
            : 'padrao';
        $curso->cupom_principal_id = $config && !empty($config->cupom_principal_id)
            ? (int) $config->cupom_principal_id
            : null;
        $curso->cupom_secundario_id = $config && !empty($config->cupom_secundario_id)
            ? (int) $config->cupom_secundario_id
            : null;
        $curso->formulario_pre_checkout = $config
            ? (bool) $config->formulario_pre_checkout
            : (bool) ($portalData['formulario_pre_checkout'] ?? true);
        $curso->usar_contador = $config ? (bool) $config->usar_contador : false;
        $curso->contador_minutos = $config && !empty($config->contador_minutos)
            ? (int) $config->contador_minutos
            : null;
        $curso->contador_acao = $config->contador_acao ?? null;
        $curso->contador_destino_oferta = $config->contador_destino_oferta ?? null;
    }

    public function buildRootDomainCourseData(
        Curso $curso,
        ?RootDomainCourseConfig $config = null,
        array $overrides = [],
        ?array $portalData = null
    ): array {
        $portalData = $portalData ?: $this->portalData();
        $config = $config ?: $this->configForCourse($curso);

        $dados = [
            'whatsapp_atendimento' => $portalData['telefone_suporte_alunos'],
            'whatsapp_atendimento_id' => null,
            'whatsapp_atendimento_tempo' => $portalData['whatsapp_atendimento_tempo'],
            'meta_pixel_id' => null,
            'company_name' => 'Programa Jovem Empreendedor',
            'logo_padrao_url' => asset('img/home_page/logojecolor.webp'),
            'logo_dark_url' => asset('img/home_page/logowhite.png'),
            'formulario_pre_checkout' => $config
                ? (bool) $config->formulario_pre_checkout
                : (bool) ($portalData['formulario_pre_checkout'] ?? true),
            'formulario_whatsapp' => (bool) ($portalData['formulario_whatsapp'] ?? true),
            'user_id' => null,
            'affiliate_code' => null,
            'modo_precos' => $config && in_array((string) $config->modo_precos, ['padrao', 'um_preco', 'dois_precos'], true)
                ? (string) $config->modo_precos
                : 'padrao',
            'cupom_principal_id' => $config && !empty($config->cupom_principal_id)
                ? (int) $config->cupom_principal_id
                : null,
            'cupom_secundario_id' => $config && !empty($config->cupom_secundario_id)
                ? (int) $config->cupom_secundario_id
                : null,
            'usar_contador' => $config ? (bool) $config->usar_contador : false,
            'contador_minutos' => $config && !empty($config->contador_minutos)
                ? (int) $config->contador_minutos
                : null,
            'contador_acao' => $config->contador_acao ?? null,
            'contador_destino_oferta' => $config->contador_destino_oferta ?? null,
            'link_checkout_completo' => $curso->link_checkout_completo . '&hideBillet=1',
        ];

        return array_merge($dados, $overrides);
    }

    public function prepareCoursePricingBase(Curso $curso): Curso
    {
        $preparedCourse = clone $curso;

        $dados = explode('x', (string) $preparedCourse->preco_parcelado_completo);
        $preparedCourse->parcelamento = $dados[0] ?? null;
        $preparedCourse->preco = isset($dados[1])
            ? (float) str_replace('R$', '', str_replace(',', '.', $dados[1]))
            : null;
        $preparedCourse->preco_cheio_completo = (int) str_replace('R$', '', (string) $preparedCourse->preco_cheio_completo);
        $preparedCourse->preco_cheio = $preparedCourse->preco_cheio_completo * 3;

        $preco = $this->formatarPrecoParceladoLegado($preparedCourse->preco, $preparedCourse->parcelamento);
        $preparedCourse->preco_cheio_completo = 'R$' . number_format($preparedCourse->preco_cheio_completo, 2, ',', '');
        $preparedCourse->preco_parcelado_completo = $preco['parcelamento'] . 'xR$' . $preco['preco'];

        $preparedCourse->preco_cheio_basico = (int) str_replace('R$', '', (string) $preparedCourse->preco_cheio_completo);
        $preparedCourse->preco_cheio_basico = (float) $preparedCourse->preco_cheio_basico * 0.5;
        $preparedCourse->preco_cheio_basico = 'R$' . number_format($preparedCourse->preco_cheio_basico, 2, ',', '.');

        $precoParceladoBasico = explode('R$', (string) $preparedCourse->preco_parcelado_completo);
        $precoParceladoBasico = ((float) ($precoParceladoBasico[1] ?? 0)) * 0.5;
        $preco = $this->formatarPrecoParceladoLegado($precoParceladoBasico, $preparedCourse->parcelamento);
        $preparedCourse->preco_parcelado_basico = $preco['parcelamento'] . 'xR$' . $preco['preco'];

        $preparedCourse->preco_cheio_certificado = (int) str_replace('R$', '', (string) $preparedCourse->preco_cheio_completo);
        $preparedCourse->preco_cheio_certificado = (float) $preparedCourse->preco_cheio_certificado * 0.2;
        $preparedCourse->preco_cheio_certificado = 'R$' . number_format($preparedCourse->preco_cheio_certificado, 2, ',', '.');

        $precoParceladoCertificado = explode('R$', (string) $preparedCourse->preco_parcelado_completo);
        $precoParceladoCertificado = ((float) ($precoParceladoCertificado[1] ?? 0)) * 0.2;
        $preco = $this->formatarPrecoParceladoLegado($precoParceladoCertificado, $preparedCourse->parcelamento);
        $preparedCourse->preco_parcelado_certificado = $preco['parcelamento'] . 'xR$' . $preco['preco'];
        $preparedCourse->horas_certificado = (int) (($preparedCourse->horas_completo ?? 0) * 0.5);

        return $preparedCourse;
    }

    public function hydrateCourseForPublicState(Curso $curso, array $dados): void
    {
        $curso->whatsapp_atendimento = $dados['whatsapp_atendimento'] ?? null;
        $curso->whatsapp_atendimento_id = $dados['whatsapp_atendimento_id'] ?? null;
        $curso->whatsapp_atendimento_tempo = $dados['whatsapp_atendimento_tempo'] ?? null;
        $curso->link_checkout_completo = $dados['link_checkout_completo'] ?? $curso->link_checkout_completo;
        $curso->link_checkout_basico = ($dados['link_checkout_completo'] ?? $curso->link_checkout_completo) . '&offDiscount=50OFF';
        $curso->link_checkout_certificado = ($dados['link_checkout_completo'] ?? $curso->link_checkout_completo) . '&offDiscount=80OFF';
        $curso->meta_pixel_id = $dados['meta_pixel_id'] ?? null;
        $curso->formulario = (bool) ($dados['formulario_pre_checkout'] ?? true);
        $curso->user_id = $dados['user_id'] ?? null;
        $curso->affiliate_code = $dados['affiliate_code'] ?? null;
        $curso->company_name = $dados['company_name'] ?? 'Programa Jovem Empreendedor';
        $curso->logo_padrao_url = $dados['logo_padrao_url'] ?? asset('img/home_page/logojecolor.webp');
        $curso->logo_dark_url = $dados['logo_dark_url'] ?? asset('img/home_page/logowhite.png');
        $curso->modo_precos = 'padrao';
        $curso->cupom_principal_id = null;
        $curso->cupom_secundario_id = null;
        $curso->cupom_principal_codigo = null;
        $curso->cupom_secundario_codigo = null;
        $curso->origem = 'checkout_completo';
    }

    public function resolveEffectivePublicStateForCourse(Curso $curso, bool $descontoBannerAtivo = false): array
    {
        $portalData = $this->portalData();
        $config = $this->configForCourse($curso);
        $effectiveCourse = $this->prepareCoursePricingBase($curso);
        $dados = $this->buildRootDomainCourseData($effectiveCourse, $config, [], $portalData);

        $this->hydrateCourseForPublicState($effectiveCourse, $dados);
        $courseBasePricing = clone $effectiveCourse;
        $this->applyPricingConfigurationByCoupon($effectiveCourse, $dados, $descontoBannerAtivo);

        return [
            'course' => $effectiveCourse,
            'config' => $config,
            'data' => $dados,
            'pricing' => $this->buildPublicCoursePricingConfig(
                $courseBasePricing,
                $effectiveCourse,
                $dados,
                $descontoBannerAtivo
            ),
            'visible' => $this->isVisibleForCourse($curso, $config),
            'portal_data' => $portalData,
        ];
    }

    public function buildPublicCoursePricingConfig(
        Curso $cursoBasePricing,
        Curso $cursoAtual,
        array $dados,
        bool $descontoBannerAtivo
    ): array {
        $modoPrecosAtual = in_array(($cursoAtual->modo_precos ?? 'padrao'), ['padrao', 'um_preco', 'dois_precos'], true)
            ? $cursoAtual->modo_precos
            : 'padrao';

        $mostrarPlanoSecundario = ($cursoAtual->origem ?? 'checkout_completo') !== 'whatsapp'
            && !$descontoBannerAtivo
            && in_array($modoPrecosAtual, ['padrao', 'dois_precos'], true);

        $offerVariants = $this->buildPublicCourseOfferVariants($cursoBasePricing, $modoPrecosAtual);

        $currentCompleteOfferKey = !empty($cursoAtual->cupom_principal_id)
            ? 'completo_cupom:' . (int) $cursoAtual->cupom_principal_id
            : 'completo_padrao';

        $currentBasicOfferKey = null;
        if ($mostrarPlanoSecundario) {
            $currentBasicOfferKey = $modoPrecosAtual === 'dois_precos' && !empty($cursoAtual->cupom_secundario_id)
                ? 'basico_cupom:' . (int) $cursoAtual->cupom_secundario_id
                : 'basico_padrao';
        }

        $countdown = $this->normalizePublicCountdownConfig(
            $dados,
            $modoPrecosAtual,
            $offerVariants,
            ($cursoAtual->origem ?? 'checkout_completo') !== 'whatsapp',
            $mostrarPlanoSecundario
        );

        if ($countdown['enabled']) {
            $affiliateSegment = trim((string) ($dados['affiliate_code'] ?? $cursoAtual->affiliate_code ?? $dados['user_id'] ?? ''));
            $affiliateSegment = preg_replace('/[^a-zA-Z0-9_-]/', '', $affiliateSegment) ?? '';
            if ($affiliateSegment === '') {
                $affiliateSegment = 'sem_ref';
            }

            $configHash = substr(sha1(json_encode([
                'minutes' => $countdown['minutes'],
                'action' => $countdown['action'],
                'destination' => $countdown['destination_offer'],
                'mode' => $modoPrecosAtual,
                'principal' => $cursoAtual->cupom_principal_id,
                'secundario' => $cursoAtual->cupom_secundario_id,
                'desconto_banner' => $descontoBannerAtivo,
            ])), 0, 16);

            $countdown['storage_key'] = 'lp_course_countdown_'
                . (int) ($cursoAtual->id ?? 0)
                . '_'
                . $affiliateSegment
                . '_'
                . $configHash;
        }

        return [
            'initial_state' => $this->buildPublicCourseInitialState($cursoAtual, $mostrarPlanoSecundario),
            'offer_variants' => $offerVariants,
            'current_complete_offer_key' => $currentCompleteOfferKey,
            'current_basic_offer_key' => $currentBasicOfferKey,
            'countdown' => $countdown,
        ];
    }

    public function applyPricingConfigurationByCoupon(Curso $curso, array $dados, bool $descontoBannerAtivo): void
    {
        $modoPrecos = $dados['modo_precos'] ?? 'padrao';
        if (!in_array($modoPrecos, ['padrao', 'um_preco', 'dois_precos'], true)) {
            $modoPrecos = 'padrao';
        }

        $cupomPrincipalId = !empty($dados['cupom_principal_id']) ? (int) $dados['cupom_principal_id'] : null;
        $cupomSecundarioId = !empty($dados['cupom_secundario_id']) ? (int) $dados['cupom_secundario_id'] : null;

        if ($descontoBannerAtivo || !Schema::hasTable('cupons')) {
            $this->forceDefaultPricingConfiguration($curso);
            return;
        }

        $cupons = Cupom::query()->get()->keyBy('id');
        if ($cupons->isEmpty()) {
            $this->forceDefaultPricingConfiguration($curso);
            return;
        }

        $cupomPrincipal = $cupomPrincipalId ? $cupons->get($cupomPrincipalId) : null;
        $cupomSecundario = $cupomSecundarioId ? $cupons->get($cupomSecundarioId) : null;

        if ($modoPrecos === 'um_preco' && $cupomPrincipalId && !$cupomPrincipal) {
            $this->forceDefaultPricingConfiguration($curso);
            return;
        }

        if (
            $modoPrecos === 'dois_precos' &&
            (
                !$cupomSecundario ||
                ($cupomPrincipalId && !$cupomPrincipal) ||
                ($cupomPrincipalId && $cupomSecundarioId && $cupomPrincipalId === $cupomSecundarioId)
            )
        ) {
            $this->forceDefaultPricingConfiguration($curso);
            return;
        }

        if ($modoPrecos === 'padrao') {
            $this->forceDefaultPricingConfiguration($curso);
            return;
        }

        $precoCheioCompleto = $this->extractMonetaryValue($curso->preco_cheio_completo);
        $precoParcelaCompleto = $this->extractMonetaryValue($curso->preco_parcelado_completo);

        $curso->modo_precos = $modoPrecos;
        $curso->cupom_principal_id = $cupomPrincipal?->id;
        $curso->cupom_secundario_id = $cupomSecundario?->id;
        $curso->cupom_principal_codigo = $cupomPrincipal?->codigo;
        $curso->cupom_secundario_codigo = $cupomSecundario?->codigo;

        if ($cupomPrincipal) {
            $curso->link_checkout_completo = $this->applyCouponOnCheckoutUrl($curso->link_checkout_completo, $cupomPrincipal->codigo);

            if ($precoCheioCompleto !== null) {
                $curso->preco_cheio_completo = $this->formatMonetaryValue(
                    $this->applyPercentDiscount($precoCheioCompleto, (float) $cupomPrincipal->desconto)
                );
            }

            if ($precoParcelaCompleto !== null) {
                $curso->preco_parcelado_completo = $this->formatInstallmentPrice(
                    $this->applyPercentDiscount($precoParcelaCompleto, (float) $cupomPrincipal->desconto),
                    $curso->parcelamento
                );
            }
        }

        if ($modoPrecos === 'dois_precos' && $cupomSecundario) {
            $curso->link_checkout_basico = $this->applyCouponOnCheckoutUrl($curso->link_checkout_basico, $cupomSecundario->codigo);

            if ($precoCheioCompleto !== null) {
                $curso->preco_cheio_basico = $this->formatMonetaryValue(
                    $this->applyPercentDiscount($precoCheioCompleto, (float) $cupomSecundario->desconto)
                );
            }

            if ($precoParcelaCompleto !== null) {
                $curso->preco_parcelado_basico = $this->formatInstallmentPrice(
                    $this->applyPercentDiscount($precoParcelaCompleto, (float) $cupomSecundario->desconto),
                    $curso->parcelamento
                );
            }
        }
    }

    private function buildPublicCourseInitialState(Curso $cursoAtual, bool $mostrarPlanoSecundario): array
    {
        $completeOffer = $this->buildOfferPayloadFromCourse($cursoAtual, 'completo');
        $basicOffer = $mostrarPlanoSecundario
            ? $this->buildOfferPayloadFromCourse($cursoAtual, 'basico')
            : null;

        return [
            'layout' => $mostrarPlanoSecundario ? 'two' : 'one',
            'hero' => $mostrarPlanoSecundario && $basicOffer
                ? [
                    'label' => $basicOffer['hero_label'],
                    'value' => $basicOffer['price_value'],
                    'cash_value' => $basicOffer['cash_value'],
                    'show_cash_line' => false,
                    'plan' => 'basico',
                ]
                : [
                    'label' => $completeOffer['hero_label'],
                    'value' => $completeOffer['price_value'],
                    'cash_value' => $completeOffer['cash_value'],
                    'show_cash_line' => $completeOffer['show_cash_line'],
                    'plan' => 'completo',
                ],
            'cards' => [
                'basico' => $basicOffer,
                'completo' => $completeOffer,
            ],
        ];
    }

    private function buildPublicCourseOfferVariants(Curso $cursoBasePricing, string $modoPrecos): array
    {
        $variants = [
            'completo_padrao' => $this->buildOfferPayloadFromCourse($cursoBasePricing, 'completo'),
        ];

        if ($modoPrecos === 'padrao') {
            $variants['basico_padrao'] = $this->buildOfferPayloadFromCourse($cursoBasePricing, 'basico');
            return $variants;
        }

        if (!Schema::hasTable('cupons')) {
            return $variants;
        }

        $cupons = Cupom::query()->orderBy('id')->get();
        foreach ($cupons as $cupom) {
            $variants['completo_cupom:' . $cupom->id] = $this->buildOfferPayloadFromCourse(
                $this->applyOfferCouponOnClonedCourse($cursoBasePricing, 'completo', $cupom),
                'completo'
            );
        }

        if ($modoPrecos === 'dois_precos') {
            foreach ($cupons as $cupom) {
                $variants['basico_cupom:' . $cupom->id] = $this->buildOfferPayloadFromCourse(
                    $this->applyOfferCouponOnClonedCourse($cursoBasePricing, 'basico', $cupom),
                    'basico'
                );
            }
        }

        return $variants;
    }

    private function applyOfferCouponOnClonedCourse(Curso $cursoBasePricing, string $plano, Cupom $cupom): Curso
    {
        $cursoVariant = clone $cursoBasePricing;

        $precoCheioCompleto = $this->extractMonetaryValue($cursoBasePricing->preco_cheio_completo);
        $precoParcelaCompleto = $this->extractMonetaryValue($cursoBasePricing->preco_parcelado_completo);

        if ($plano === 'completo') {
            $cursoVariant->link_checkout_completo = $this->applyCouponOnCheckoutUrl(
                $cursoVariant->link_checkout_completo,
                $cupom->codigo
            );

            if ($precoCheioCompleto !== null) {
                $cursoVariant->preco_cheio_completo = $this->formatMonetaryValue(
                    $this->applyPercentDiscount($precoCheioCompleto, (float) $cupom->desconto)
                );
            }

            if ($precoParcelaCompleto !== null) {
                $cursoVariant->preco_parcelado_completo = $this->formatInstallmentPrice(
                    $this->applyPercentDiscount($precoParcelaCompleto, (float) $cupom->desconto),
                    $cursoVariant->parcelamento
                );
            }

            return $cursoVariant;
        }

        $cursoVariant->link_checkout_basico = $this->applyCouponOnCheckoutUrl(
            $cursoVariant->link_checkout_basico,
            $cupom->codigo
        );

        if ($precoCheioCompleto !== null) {
            $cursoVariant->preco_cheio_basico = $this->formatMonetaryValue(
                $this->applyPercentDiscount($precoCheioCompleto, (float) $cupom->desconto)
            );
        }

        if ($precoParcelaCompleto !== null) {
            $cursoVariant->preco_parcelado_basico = $this->formatInstallmentPrice(
                $this->applyPercentDiscount($precoParcelaCompleto, (float) $cupom->desconto),
                $cursoVariant->parcelamento
            );
        }

        return $cursoVariant;
    }

    private function buildOfferPayloadFromCourse(Curso $curso, string $plano): array
    {
        $isBasico = $plano === 'basico';
        $precoCheio = $isBasico
            ? ($curso->preco_cheio_basico ?? null)
            : ($curso->preco_cheio_completo ?? null);
        $precoParcelado = $isBasico
            ? ($curso->preco_parcelado_basico ?? null)
            : ($curso->preco_parcelado_completo ?? null);
        $precoCheioValor = $this->extractMonetaryValue($precoCheio);
        $ocultarParcelado = $precoCheioValor !== null && $precoCheioValor < 100;

        return [
            'plan' => $isBasico ? 'basico' : 'completo',
            'price_value' => $ocultarParcelado
                ? ($precoCheio ?? 'Consulte')
                : ($precoParcelado ?? 'Consulte'),
            'cash_value' => $precoCheio ?? 'consulte',
            'show_cash_line' => !$ocultarParcelado,
            'checkout_url' => $isBasico
                ? ($curso->link_checkout_basico ?? '#')
                : ($curso->link_checkout_completo ?? '#'),
            'requires_lead' => !empty($curso->formulario),
            'cta_label' => $isBasico ? 'Quero o plano básico' : 'Quero o plano completo',
            'hero_label' => $isBasico ? 'Investimento único de' : 'Investimento do plano completo',
        ];
    }

    private function normalizePublicCountdownConfig(
        array $dados,
        string $modoPrecos,
        array $offerVariants,
        bool $paginaPermiteCountdown,
        bool $planoBasicoVisivel
    ): array {
        $minutes = !empty($dados['contador_minutos']) ? (int) $dados['contador_minutos'] : null;
        $action = $dados['contador_acao'] ?? null;
        $destinationOffer = $dados['contador_destino_oferta'] ?? null;
        $enabled = !empty($dados['usar_contador']) && $paginaPermiteCountdown;

        if (!$enabled) {
            return [
                'enabled' => false,
                'minutes' => null,
                'action' => null,
                'destination_offer' => null,
                'storage_key' => null,
                'end_label' => 'Encerrado',
            ];
        }

        if (!in_array($minutes, [1, 5, 10, 20, 30, 50], true)) {
            $enabled = false;
        }

        if (!in_array($action, ['nada', 'encerrar_basico', 'alterar_preco', 'whatsapp'], true)) {
            $enabled = false;
        }

        if ($action === 'encerrar_basico' && (!$planoBasicoVisivel || !in_array($modoPrecos, ['padrao', 'dois_precos'], true))) {
            $enabled = false;
        }

        if ($action === 'alterar_preco' && !array_key_exists((string) $destinationOffer, $offerVariants)) {
            $enabled = false;
        }

        if (
            $action === 'alterar_preco' &&
            is_string($destinationOffer) &&
            str_starts_with($destinationOffer, 'basico_') &&
            !$planoBasicoVisivel
        ) {
            $enabled = false;
        }

        if (!$enabled) {
            return [
                'enabled' => false,
                'minutes' => null,
                'action' => null,
                'destination_offer' => null,
                'storage_key' => null,
                'end_label' => 'Encerrado',
            ];
        }

        return [
            'enabled' => true,
            'minutes' => $minutes,
            'action' => $action,
            'destination_offer' => $action === 'alterar_preco' ? (string) $destinationOffer : null,
            'storage_key' => null,
            'end_label' => 'Encerrado',
        ];
    }

    private function forceDefaultPricingConfiguration(Curso $curso): void
    {
        $curso->modo_precos = 'padrao';
        $curso->cupom_principal_id = null;
        $curso->cupom_secundario_id = null;
        $curso->cupom_principal_codigo = null;
        $curso->cupom_secundario_codigo = null;
    }

    private function extractMonetaryValue(?string $valor): ?float
    {
        if (!$valor) {
            return null;
        }

        $valorNormalizado = preg_replace('/^\s*\d+\s*x(?:\s*de)?\s*/i', '', trim($valor)) ?? trim($valor);
        $somenteNumeros = preg_replace('/[^\d,.]/', '', $valorNormalizado);
        if (!$somenteNumeros) {
            return null;
        }

        if (str_contains($somenteNumeros, ',') && str_contains($somenteNumeros, '.')) {
            $ultimaVirgula = strrpos($somenteNumeros, ',');
            $ultimoPonto = strrpos($somenteNumeros, '.');

            if ($ultimaVirgula !== false && $ultimoPonto !== false && $ultimaVirgula > $ultimoPonto) {
                $somenteNumeros = str_replace('.', '', $somenteNumeros);
                $somenteNumeros = str_replace(',', '.', $somenteNumeros);
            } else {
                $somenteNumeros = str_replace(',', '', $somenteNumeros);
            }
        } elseif (str_contains($somenteNumeros, ',')) {
            $somenteNumeros = str_replace('.', '', $somenteNumeros);
            $somenteNumeros = str_replace(',', '.', $somenteNumeros);
        } elseif (substr_count($somenteNumeros, '.') > 1) {
            $partes = explode('.', $somenteNumeros);
            $decimal = array_pop($partes);
            $somenteNumeros = implode('', $partes) . '.' . $decimal;
        }

        if (!is_numeric($somenteNumeros)) {
            return null;
        }

        return (float) $somenteNumeros;
    }

    private function formatMonetaryValue(?float $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        return 'R$' . number_format($valor, 2, ',', '.');
    }

    private function formatInstallmentPrice(?float $valorParcela, $parcelamento): ?string
    {
        if ($valorParcela === null) {
            return null;
        }

        $preco = $this->formatarPrecoParceladoLegado($valorParcela, $parcelamento);
        return $preco['parcelamento'] . 'xR$' . $preco['preco'];
    }

    private function applyPercentDiscount(?float $valor, float $desconto): ?float
    {
        if ($valor === null) {
            return null;
        }

        return $valor * (1 - ($desconto / 100));
    }

    private function applyCouponOnCheckoutUrl(?string $url, ?string $codigoCupom): ?string
    {
        if (!$url || !$codigoCupom) {
            return $url;
        }

        if (preg_match('/([?&])offDiscount=[^&]*/', $url)) {
            return preg_replace('/([?&])offDiscount=[^&]*/', '$1offDiscount=' . $codigoCupom, $url, 1) ?? $url;
        }

        $separador = str_contains($url, '?') ? '&' : '?';
        return $url . $separador . 'offDiscount=' . $codigoCupom;
    }

    private function formatarPrecoParceladoLegado($preco, $parcelamento): array
    {
        $dados['preco'] = number_format((float) $preco, 2, ',', '');
        $dados['parcelamento'] = $parcelamento;

        if ($dados['preco'] == '9,50') {
            $dados['preco'] = '9,60';
        }

        if ($dados['preco'] == '6,53' && (int) $dados['parcelamento'] === 12) {
            $dados['preco'] = '7,04';
            $dados['parcelamento'] = '11';
        } elseif ($dados['preco'] == '2,91' && (int) $dados['parcelamento'] === 12) {
            $dados['preco'] = '7,95';
            $dados['parcelamento'] = '4';
        } elseif ($dados['preco'] == '5,76' && $dados['parcelamento'] == 12) {
            $dados['preco'] = '7,41';
            $dados['parcelamento'] = '9';
        } elseif ($dados['preco'] == '3,84') {
            $dados['preco'] = '7,15';
            $dados['parcelamento'] = '6';
        }

        return $dados;
    }
}
