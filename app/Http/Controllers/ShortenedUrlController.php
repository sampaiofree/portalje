<?php

namespace App\Http\Controllers;

use App\Models\ShortenedUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Auth;

class ShortenedUrlController extends Controller
{
    private const SLUG_REGEX = '/^[a-z0-9_-]+$/';

    private function normalizeDomain(?string $domain): ?string
    {
        $domain = strtolower(trim((string) $domain));
        if ($domain === '') {
            return null;
        }

        if (!str_contains($domain, '://')) {
            $domain = 'https://' . $domain;
        }

        $host = parse_url($domain, PHP_URL_HOST) ?: '';
        $host = preg_replace('/^www\./', '', strtolower(trim($host)));

        return $host !== '' ? $host : null;
    }

    private function domainCandidates(string $host): array
    {
        $candidates = [
            $host,
            'www.' . $host,
            'http://' . $host,
            'https://' . $host,
            'http://www.' . $host,
            'https://www.' . $host,
        ];

        $normalized = [];
        foreach ($candidates as $candidate) {
            $normalized[] = rtrim($candidate, '/');
            $normalized[] = rtrim($candidate, '/') . '/';
        }

        return array_values(array_unique($normalized));
    }

    private function normalizeSlug(?string $slug): ?string
    {
        $slug = strtolower(trim((string) $slug));

        return $slug === '' ? null : $slug;
    }

    /**
     * Método para criar um link encurtado
     */
    public function encurtar(Request $request)
    {
        $user = Auth::user();
        $dominio = $this->normalizeDomain($user->dominio_externo ?? $user->dominio);
        if (!$dominio) {
            $message = 'Configure um domínio válido antes de criar links encurtados.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return redirect()->back()->with('error', $message);
        }

        $request->merge([
            'slug' => $this->normalizeSlug($request->input('slug')),
        ]);

        $validated = $request->validate([
            'url_longa' => 'required|url',
            'slug' => [
                'nullable',
                'string',
                'max:64',
                'regex:' . self::SLUG_REGEX,
                Rule::unique('shortened_urls', 'slug')->where(static function ($query) use ($dominio) {
                    $query->where('dominio', $dominio);
                }),
            ],
        ], [
            'slug.regex' => 'O slug deve conter apenas letras, números, hífen e underscore.',
            'slug.max' => 'O slug deve ter no máximo 64 caracteres.',
            'slug.unique' => 'Este slug já está em uso no seu domínio.',
        ]);

        try {
            $slug = $validated['slug'] ?? null;

            if (!$slug) {
                do {
                    $slug = Str::lower(Str::random(6));
                } while (ShortenedUrl::where('slug', $slug)->where('dominio', $dominio)->exists());
            }

            ShortenedUrl::create([
                'user_id' => $user->id,
                'dominio' => $dominio,
                'slug' => $slug,
                'url_longa' => $validated['url_longa'],
            ]);

            $linkCompleto = $dominio . '/e/' . $slug;

            if ($request->expectsJson()) {
                $request->session()->flash('success', 'Link criado com sucesso!');
                $request->session()->flash('link_encurtado', $linkCompleto);

                return response()->json([
                    'success' => true,
                    'message' => 'Link criado com sucesso!',
                    'link_encurtado' => $linkCompleto,
                    'redirect_to' => route('encurtar_link_lista'),
                ]);
            }

            return redirect()->route('encurtar_link_lista')
                ->with('success', 'Link criado com sucesso!')
                ->with('link_encurtado', $linkCompleto);
        } catch (\Throwable $e) {
            $message = 'Ocorreu um erro inesperado ao criar o link.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 500);
            }

            return redirect()->back()->with('error', $message)->withInput();
        }
    }

    public function editar_mostrar($id){
        $link = ShortenedUrl::where('id', $id)->first();

        return view('dashboard.encurtadorlinks.edit', compact('link'));
        //return view('adm.encurtador_link.editar', compact('link'));
    }

    public function editar(Request $request, $id)
    {
        $user = Auth::user();
        $link = ShortenedUrl::where('id', $id)->where('user_id', $user->id)->first();

        if (!$link) {
            return response()->json([
                'error' => 'Link não encontrado ou você não tem permissão para editá-lo.',
            ], 404);
        }

        $dominioAtual = $this->normalizeDomain($user->dominio_externo ?? $user->dominio);
        $dominio = $dominioAtual ?: $link->dominio;
        if (!$dominio) {
            return response()->json([
                'message' => 'Configure um domínio válido antes de editar links encurtados.',
            ], 422);
        }

        $request->merge([
            'slug' => $this->normalizeSlug($request->input('slug')),
        ]);

        $validated = $request->validate([
            'url_longa' => 'required|url',
            'slug' => [
                'required',
                'string',
                'max:64',
                'regex:' . self::SLUG_REGEX,
                Rule::unique('shortened_urls', 'slug')
                    ->where(static function ($query) use ($dominio) {
                        $query->where('dominio', $dominio);
                    })
                    ->ignore($link->id),
            ],
        ], [
            'slug.regex' => 'O slug deve conter apenas letras, números, hífen e underscore.',
            'slug.max' => 'O slug deve ter no máximo 64 caracteres.',
            'slug.unique' => 'Este slug já está em uso no seu domínio.',
        ]);

        try {
            $link->dominio = $dominio;
            $link->url_longa = $validated['url_longa'];
            $link->slug = $validated['slug'];
            $link->save();

            return response()->json([
                'success' => 'Link atualizado com sucesso.',
                'link_encurtado' => $dominio . '/e/' . $link->slug,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Ocorreu um erro ao tentar editar o link.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function lista() {
        $user = Auth::user();
        $linkss = new ShortenedUrl();
        $linkss = $linkss->where('user_id', $user->id)->get();

        $links = array();
        foreach($linkss as $link){
            if($link->dominio=='jemp.me'){
                $link->dominio="https://$link->dominio/$link->slug";
            }else{
                $link->dominio="https://$link->dominio/e/$link->slug";
            }
            $links[] = $link;
        }

        return view('dashboard.encurtadorlinks.index', compact('links'));
        //return view('adm.encurtador_link.lista', compact('links'));

    }

    /**
     * Método para redirecionar o usuário com base no subdomínio e no slug
     */
    public function redirecionar(Request $request, $slug)
    {
         // Obtém o host completo
        $host = $this->normalizeDomain($request->getHost());
        if (!$host) {
            abort(404);
        }

        // Busca pelo link encurtado no banco de dados
        $shortenedUrl = ShortenedUrl::whereIn('dominio', $this->domainCandidates($host))
            ->where('slug', $slug)
            ->firstOrFail();

        // Incrementa o contador de cliques
        $shortenedUrl->increment('click_count');

        // Redireciona para a URL longa
        return redirect($shortenedUrl->url_longa);
    }

    public function excluir($id)
    {
        try {
            $user = Auth::user();

            // Busca o link encurtado pelo ID e garante que ele pertence ao usuário autenticado
            $link = ShortenedUrl::where('id', $id)->where('user_id', $user->id)->first();

            if (!$link) {
                return redirect()->back()->with('error', "Link não encontrado ou você não tem permissão para excluí-lo.");
            }

            // Exclui o link
            $link->delete();
            return redirect()->back()->with('success', "Link excluído com sucesso.");

        } catch (\Exception $e) {
            
            return redirect()->back()->with('error', "Ocorreu um erro ao tentar excluir o link.");
        }
    }

}
