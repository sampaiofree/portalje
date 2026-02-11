<?php

namespace App\Http\Controllers\Afiliados;

use App\Http\Controllers\Controller;
use App\Models\Codigo_ref;
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

        $itens = $cursos->map(function ($curso) use ($baseUrl, $refsPorCurso) {
            $ref = $refsPorCurso[$curso->id] ?? null;
            $link = rtrim($baseUrl, '/') . '/' . ltrim((string) $curso->url, '/');

            if ($ref) {
                $link .= '?ref=' . urlencode((string) $ref);
            }

            return [
                'id' => 'curso_' . $curso->id,
                'title' => (string) ($curso->titulo ?? ''),
                'description' => $this->descricaoCurso($curso),
                'availability' => 'in stock',
                'condition' => 'new',
                'price' => $this->precoMeta($curso->preco_cheio_completo),
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

        $refsPorCurso = Codigo_ref::query()
            ->where('user_id', $user->id)
            ->whereNotNull('codigo_ref')
            ->where('codigo_ref', '<>', '')
            ->where('mostrar_curso', true)
            ->pluck('codigo_ref', 'curso_id');

        if ($refsPorCurso->isEmpty()) {
            return [collect(), collect(), $user];
        }

        $cursos = Curso::query()
            ->where('publicado', true)
            ->where('permitir_afiliacao', true)
            ->whereIn('id', $refsPorCurso->keys()->all())
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

    private function precoMeta(?string $preco): string
    {
        if (!$preco) {
            return '0.00 BRL';
        }

        $valor = str_replace(['R$', ' '], '', $preco);
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);
        $valor = preg_replace('/[^0-9.]/', '', $valor);

        $numero = is_numeric($valor) ? (float) $valor : 0.0;

        return number_format($numero, 2, '.', '') . ' BRL';
    }
}
