<?php

namespace App\Http\Controllers\Afiliados;

use App\Http\Controllers\Controller;
use App\Models\Codigo_ref;
use App\Models\Cupom;
use App\Models\Curso;
use App\Models\User;
use App\Services\RootDomainCoursePublicStateService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class CatalogoController extends Controller
{
    public function __construct(
        private RootDomainCoursePublicStateService $pricingStateService
    ) {
    }

    public function index(Request $request)
    {
        $catalogo = $this->montarCatalogo($request);

        return response()
            ->view('afiliados.catalogo', $catalogo)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function markdown(Request $request)
    {
        $catalogo = $this->montarCatalogo($request);

        return response()
            ->view('afiliados.catalogo2', $catalogo)
            ->header('Content-Type', 'text/markdown; charset=UTF-8');
    }

    private function montarCatalogo(Request $request): array
    {
        $host = $request->getHost();
        $fallbackBaseUrl = $request->getSchemeAndHttpHost();

        [$cursos, $refsPorCurso, $user] = $this->cursosPorDominio($host);
        $baseUrl = $this->resolverBaseUrl($request, $user, $fallbackBaseUrl);
        $cuponsPorId = $this->carregarCuponsPorId();

        $itens = $cursos->map(function ($curso) use ($baseUrl, $refsPorCurso, $cuponsPorId) {
            $ref = $refsPorCurso[$curso->id] ?? null;

            return $this->mapearItemCatalogo($curso, $baseUrl, $ref, $cuponsPorId);
        })->values();

        return [
            'host' => $host,
            'baseUrl' => $baseUrl,
            'itens' => $itens,
        ];
    }

    private function mapearItemCatalogo(Curso $curso, string $baseUrl, ?Codigo_ref $ref, Collection $cuponsPorId): array
    {
        $link = rtrim($baseUrl, '/') . '/' . ltrim((string) $curso->url, '/');

        return [
            'id' => 'curso_' . $curso->id,
            'title' => trim((string) ($curso->titulo ?? '')),
            'description' => $this->descricaoCurso($curso),
            'availability' => 'in stock',
            'condition' => 'new',
            'price' => $this->precoMetaPorConfiguracao($curso, $ref, $cuponsPorId),
            'link' => $link,
            'checkout_link' => $this->checkoutLinkPorConfiguracao($curso, $ref, $cuponsPorId),
            'offers' => $this->ofertasPorConfiguracao($curso, $ref),
            'workload' => $this->cargaHorariaCurso($curso),
            'teacher_name' => $this->nomeProfessorCurso($curso),
            'image_link' => $curso->capa_quadrada ? asset('storage/' . $curso->capa_quadrada) : '',
            'brand' => 'Portal JE',
        ];
    }

    private function cursosPorDominio(string $host): array
    {
        if (!Schema::hasTable('curso')) {
            return [collect(), collect(), null];
        }

        $user = $this->buscarUsuarioPorDominio($host);

        if (!$user || !Schema::hasTable('codigo_ref')) {
            $cursos = Curso::query()
                ->where('publicado', true)
                ->where('mostrar_na_pagina', true)
                ->orderBy('ordem')
                ->get();

            return [$cursos, collect(), null];
        }

        $hasModoPrecos = Schema::hasColumn('codigo_ref', 'modo_precos');
        $hasCupomPrincipal = Schema::hasColumn('codigo_ref', 'cupom_principal_id');
        $hasCupomSecundario = Schema::hasColumn('codigo_ref', 'cupom_secundario_id');

        $refsPorCursoQuery = Codigo_ref::query()
            ->where('user_id', $user->id)
            ->whereNotNull('codigo_ref')
            ->where('codigo_ref', '<>', '')
            ->where('mostrar_curso', true);

        $select = ['id', 'curso_id', 'codigo_ref'];
        if ($hasModoPrecos) {
            $select[] = 'modo_precos';
        }
        if ($hasCupomPrincipal) {
            $select[] = 'cupom_principal_id';
        }
        if ($hasCupomSecundario) {
            $select[] = 'cupom_secundario_id';
        }

        $refsPorCurso = $refsPorCursoQuery
            ->select($select)
            ->get()
            ->keyBy('curso_id');

        if ($refsPorCurso->isEmpty()) {
            return [collect(), collect(), $user];
        }

        $cursos = Curso::query()
            ->where('publicado', true)
            ->where('permitir_afiliacao', true)
            ->whereIn('id', $refsPorCurso->keys()->map(fn ($id) => (int) $id)->all())
            ->orderBy('ordem')
            ->get();

        return [$cursos, $refsPorCurso, $user];
    }

    private function buscarUsuarioPorDominio(string $host): ?User
    {
        if (!Schema::hasTable('users')) {
            return null;
        }

        $hostsBloqueados = [
            'portalje.org',
            'dns.portalje.org',
            'jemp.me',
            'jovemempreendedor.org',
            'dns.jovemempreendedor.org',
        ];

        if (in_array($host, $hostsBloqueados, true)) {
            return null;
        }

        return User::query()
            ->where('dominio', $host)
            ->orWhere('dominio_externo', $host)
            ->first();
    }

    private function resolverBaseUrl(Request $request, ?User $user, string $fallbackBaseUrl): string
    {
        if (!$user) {
            return $fallbackBaseUrl;
        }

        $dominioPreferencial = trim((string) ($user->dominio_externo ?: $user->dominio));
        if ($dominioPreferencial === '') {
            return $fallbackBaseUrl;
        }

        return $request->getScheme() . '://' . $dominioPreferencial;
    }

    private function descricaoCurso($curso): string
    {
        $descricao = $curso->descricao_curta ?: $curso->headline;
        $descricao = trim(strip_tags((string) $descricao));

        if ($descricao === '') {
            $descricao = 'Curso online';
        }

        return mb_substr($descricao, 0, 500);
    }

    private function cargaHorariaCurso(Curso $curso): string
    {
        $cargaHoraria = trim(strip_tags((string) ($curso->horas_completo ?? '')));
        if ($cargaHoraria === '') {
            return '';
        }

        if (preg_match('/hora/i', $cargaHoraria)) {
            return $cargaHoraria;
        }

        return $cargaHoraria . ' horas';
    }

    private function nomeProfessorCurso(Curso $curso): string
    {
        return trim(strip_tags((string) ($curso->professor_nome ?? '')));
    }

    private function carregarCuponsPorId(): Collection
    {
        if (!Schema::hasTable('cupons')) {
            return collect();
        }

        return Cupom::query()->get()->keyBy('id');
    }

    private function checkoutLinkPorConfiguracao(Curso $curso, ?Codigo_ref $ref, Collection $cuponsPorId): string
    {
        $checkoutUrl = $this->checkoutBaseUrl($curso, $ref);
        if ($checkoutUrl === '') {
            return '';
        }

        $cupomPrincipal = $this->cupomPrincipalPorConfiguracao($ref, $cuponsPorId);
        if (!$cupomPrincipal) {
            return $checkoutUrl;
        }

        return $this->aplicarCupomNoCheckoutUrl($checkoutUrl, $cupomPrincipal->codigo) ?? $checkoutUrl;
    }

    private function ofertasPorConfiguracao(Curso $curso, ?Codigo_ref $ref): array
    {
        $dados = $this->dadosDePrecificacao($curso, $ref);

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
        $ofertas = [];

        if (is_array($cards['completo'] ?? null) && !empty($cards['completo'])) {
            $ofertas[] = $this->normalizarOfertaMarkdown($cards['completo'], 'Plano completo');
        }

        if (is_array($cards['basico'] ?? null) && !empty($cards['basico'])) {
            $ofertas[] = $this->normalizarOfertaMarkdown($cards['basico'], 'Plano básico');
        }

        return $ofertas;
    }

    private function dadosDePrecificacao(Curso $curso, ?Codigo_ref $ref): array
    {
        return [
            'link_checkout_completo' => $this->checkoutBaseUrl($curso, $ref),
            'modo_precos' => $this->modoPrecosConfigurado($ref),
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

    private function normalizarOfertaMarkdown(array $offer, string $label): array
    {
        return [
            'label' => $label,
            'price_value' => (string) ($offer['price_value'] ?? 'Consulte'),
            'cash_value' => (string) ($offer['cash_value'] ?? 'Consulte'),
            'show_cash_line' => (bool) ($offer['show_cash_line'] ?? false),
            'checkout_url' => (string) ($offer['checkout_url'] ?? '#'),
        ];
    }

    private function checkoutBaseUrl(Curso $curso, ?Codigo_ref $ref): string
    {
        $codigoRef = trim((string) ($ref->codigo_ref ?? ''));
        $codigoAfiliado = trim((string) ($curso->codigo_afiliado_plano_completo ?? ''));

        if ($codigoRef !== '' && $codigoAfiliado !== '') {
            return "https://go.hotmart.com/{$codigoRef}?ap={$codigoAfiliado}";
        }

        return trim((string) ($curso->link_checkout_completo ?? ''));
    }

    private function cupomPrincipalPorConfiguracao(?Codigo_ref $ref, Collection $cuponsPorId): ?Cupom
    {
        if (!$ref || $cuponsPorId->isEmpty() || !array_key_exists('modo_precos', $ref->getAttributes())) {
            return null;
        }

        $modoPrecos = $this->modoPrecosConfigurado($ref);

        if ($modoPrecos === 'padrao' || empty($ref->cupom_principal_id)) {
            return null;
        }

        return $cuponsPorId->get((int) $ref->cupom_principal_id);
    }

    private function precoMetaPorConfiguracao(Curso $curso, ?Codigo_ref $ref, Collection $cuponsPorId): string
    {
        $precoCompleto = $this->extrairValorMonetario($curso->preco_cheio_completo);
        if ($precoCompleto === null) {
            return '0.00 BRL';
        }

        if (!$ref) {
            return $this->formatarPrecoMeta($precoCompleto);
        }

        if (!array_key_exists('modo_precos', $ref->getAttributes())) {
            return $this->formatarPrecoMeta($precoCompleto);
        }

        $modoPrecos = $this->modoPrecosConfigurado($ref);

        $cupomPrincipalId = !empty($ref->cupom_principal_id) ? (int) $ref->cupom_principal_id : null;
        $cupomSecundarioId = !empty($ref->cupom_secundario_id) ? (int) $ref->cupom_secundario_id : null;

        if ($modoPrecos === 'padrao') {
            return $this->formatarPrecoMeta($this->precoPadraoCatalogo($precoCompleto));
        }

        if ($cuponsPorId->isEmpty()) {
            return $this->formatarPrecoMeta($this->precoPadraoCatalogo($precoCompleto));
        }

        if ($modoPrecos === 'um_preco') {
            if (!$cupomPrincipalId) {
                return $this->formatarPrecoMeta($precoCompleto);
            }

            $cupomPrincipal = $cuponsPorId->get($cupomPrincipalId);
            if (!$cupomPrincipal) {
                return $this->formatarPrecoMeta($this->precoPadraoCatalogo($precoCompleto));
            }

            return $this->formatarPrecoMeta(
                $this->aplicarDescontoPercentual($precoCompleto, (float) $cupomPrincipal->desconto)
            );
        }

        if (!$cupomSecundarioId) {
            return $this->formatarPrecoMeta($this->precoPadraoCatalogo($precoCompleto));
        }

        $cupomSecundario = $cuponsPorId->get($cupomSecundarioId);
        if (!$cupomSecundario) {
            return $this->formatarPrecoMeta($this->precoPadraoCatalogo($precoCompleto));
        }

        $precoPrincipal = $precoCompleto;
        if ($cupomPrincipalId) {
            $cupomPrincipal = $cuponsPorId->get($cupomPrincipalId);
            if (!$cupomPrincipal) {
                return $this->formatarPrecoMeta($this->precoPadraoCatalogo($precoCompleto));
            }

            if ($cupomPrincipalId === $cupomSecundarioId) {
                return $this->formatarPrecoMeta($this->precoPadraoCatalogo($precoCompleto));
            }

            $precoPrincipal = $this->aplicarDescontoPercentual($precoCompleto, (float) $cupomPrincipal->desconto);
        }

        $precoSecundario = $this->aplicarDescontoPercentual($precoCompleto, (float) $cupomSecundario->desconto);
        return $this->formatarPrecoMeta(min($precoPrincipal, $precoSecundario));
    }

    private function modoPrecosConfigurado(?Codigo_ref $ref): string
    {
        if (!$ref || !array_key_exists('modo_precos', $ref->getAttributes())) {
            return 'padrao';
        }

        return in_array($ref->modo_precos, ['padrao', 'um_preco', 'dois_precos'], true)
            ? $ref->modo_precos
            : 'padrao';
    }

    private function precoPadraoCatalogo(float $precoCompleto): float
    {
        // Na configuração padrão da LP, o plano básico usa 50% OFF.
        return $this->aplicarDescontoPercentual($precoCompleto, 50.0);
    }

    private function extrairValorMonetario(?string $preco): ?float
    {
        if (!$preco) {
            return null;
        }

        $valorNormalizado = preg_replace('/^\s*\d+\s*x(?:\s*de)?\s*/i', '', trim($preco)) ?? trim($preco);
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

    private function aplicarDescontoPercentual(float $valor, float $desconto): float
    {
        return $valor * (1 - ($desconto / 100));
    }

    private function aplicarCupomNoCheckoutUrl(?string $url, ?string $codigoCupom): ?string
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

    private function formatarPrecoMeta(float $numero): string
    {
        return number_format($numero, 2, '.', '') . ' BRL';
    }
}
