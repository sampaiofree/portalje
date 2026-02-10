<?php

namespace App\Http\Controllers;

use App\Models\ShortenedUrl;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Auth;

class ShortenedUrlController extends Controller
{
    /**
     * Método para criar um link encurtado
     */
    public function encurtar(Request $request)
{
    // A validação já redireciona de volta com os erros automaticamente
    $request->validate([
        'url_longa' => 'required|url',
    ]);

    try {
        $user = Auth::user();
        $dominio = $user->dominio_externo ?? $user->dominio;

        // Geração de um slug único
        do {
            $slug = Str::random(6);
        } while (ShortenedUrl::where('slug', $slug)->where('dominio', $dominio)->exists());

        // Criação do link encurtado
        $shortenedUrl = ShortenedUrl::create([
            'user_id' => $user->id,
            'dominio' => $dominio,
            'slug' => $slug,
            'url_longa' => $request->url_longa,
        ]);
        
        // Constrói o link completo para enviar na mensagem
        $linkCompleto = $dominio . '/e/' . $slug;

        // Retorna para a página anterior com uma mensagem de sucesso na sessão
        return redirect()->back()->with('success', 'Link criado com sucesso!')->with('link_encurtado', $linkCompleto);

    } catch (\Exception $e) {
        // Em caso de qualquer outro erro, retorna com uma mensagem de erro na sessão
        return redirect()->back()->with('error', 'Ocorreu um erro inesperado ao criar o link.')->withInput();
    }
}

    public function editar_mostrar($id){
        $link = ShortenedUrl::where('id', $id)->first();

        return view('dashboard.encurtadorlinks.edit', compact('link'));
        //return view('adm.encurtador_link.editar', compact('link'));
    }

    public function editar(Request $request, $id)
    {
        try {
            // Valida os novos valores para a URL longa e o slug
            $request->validate([
                'url_longa' => 'required|url',
                'slug' => 'required|string|max:255|unique:shortened_urls,slug,' . $id,
            ]);

            // Obtém o usuário autenticado
            $user = Auth::user();

            // Busca o link encurtado pelo ID e garante que ele pertence ao usuário autenticado
            $link = ShortenedUrl::where('id', $id)->where('user_id', $user->id)->first();

            if (!$link) {
                return response()->json([
                    'error' => 'Link não encontrado ou você não tem permissão para editá-lo.'
                ], 404);
            }

            //VERIFICAR SE JÁ EXISTE O LINK COM O MESMO SLUG COM O MESMO DOMINIO
            /*$slug = ShortenedUrl::where('slug', $request->input('slug'))->where('dominio', $user->dominio)->exists();
            if ($slug) {
                return response()->json([
                    'error' => 'Você já tem um link encurtado com o mesmo slug'
                ], 404);
            }*/

            // Atualiza o link com os novos valores
            $link->url_longa = $request->input('url_longa');
            $link->slug = $request->input('slug');
            $link->save();

            $dominio = $user->dominio_externo ?? $user->dominio;

            return response()->json([
                'success' => 'Link atualizado com sucesso.',
                'link_encurtado' => $dominio . '/e/' . $link->slug,
            ], 200);

        } catch (\Exception $e) {
            // Captura e retorna a mensagem de erro
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
        $host = $request->getHost();

        // Busca pelo link encurtado no banco de dados
        $shortenedUrl = ShortenedUrl::where('dominio', $host)
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
