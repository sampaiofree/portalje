<?php

namespace App\Http\Controllers\Afiliados;

use App\Http\Controllers\Controller;
use App\Models\Codigo_ref;
use App\Models\Cupom;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class CatalogoController extends Controller
{
    public function index(Request $request)
    {
        $host = $request->getHost();
        $fallbackBaseUrl = $request->getSchemeAndHttpHost();

        [$cursos, $refsPorCurso, $user] = $this->cursosPorDominio($host);
        $baseUrl = $this->resolverBaseUrl($request, $user, $fallbackBaseUrl);
        $cuponsPorId = $this->carregarCuponsPorId();

        $itens = $cursos->map(function ($curso) use ($baseUrl, $refsPorCurso, $cuponsPorId) {
            $ref = $refsPorCurso[$curso->id] ?? null;
            $link = rtrim($baseUrl, '/') . '/' . ltrim((string) $curso->url, '/');

            if ($ref) {
                $link .= '?ref=' . urlencode((string) $ref->codigo_ref);
            }

            return [
                'id' => 'curso_' . $curso->id,
                'title' => (string) ($curso->titulo ?? ''),
                'description' => $this->descricaoCurso($curso),
                'availability' => 'in stock',
                'condition' => 'new',
                'price' => $this->precoMetaPorConfiguracao($curso, $ref, $cuponsPorId),
                'link' => $link,
                'image_link' => $curso->capa_quadrada ? asset('storage/' . $curso->capa_quadrada) : '',
                'brand' => 'Portal JE',
            ];
        })->values();

        return response()
            ->view('afiliados.catalogo', [
                'host' => $host,
                'baseUrl' => $baseUrl,
                'itens' => $itens,
            ])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
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

    private function carregarCuponsPorId(): Collection
    {
        if (!Schema::hasTable('cupons')) {
            return collect();
        }

        return Cupom::query()->get()->keyBy('id');
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

        $modoPrecos = in_array($ref->modo_precos, ['padrao', 'um_preco', 'dois_precos'], true)
            ? $ref->modo_precos
            : 'padrao';

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

    private function formatarPrecoMeta(float $numero): string
    {
        return number_format($numero, 2, '.', '') . ' BRL';
    }
}
