<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Curso;
use App\Models\PurchaseEvent;

class AdminController extends Controller
{

    public function adm_cursos_lista(){
        $cursos = Curso::all();
        return view('adm.cursos.cursos_lista', compact('cursos'));
    }

    public function adm_editar_curso($id) {
        $curso = Curso::where('id', $id)->first();
        if($curso){
            return view('adm.cursos.editar_curso', compact('curso'));
        }
    }

    public function create()
    {
        $curso = Curso::create();

        // Obtém o ID do registro recém-criado
        $newCursoId = $curso->id;
        return redirect()->route('adm_editar_curso', ['id' => $newCursoId]);
    }

    public function adm_cursos_lista_editar(Request $request, $id=null){

        $dados = $request->except('_token', '_method');

        //echo json_encode($id);
        //exit;
        
        if(!$id){$id=$dados['id'];}

        // Encontre o curso pelo ID e atualize os dados
        $curso = Curso::find($id);

        if($dados['publicado']==='true'){$dados['publicado']=1;}else{$dados['publicado']=null;}
        if($dados['permitir_afiliacao']==='true'){$dados['permitir_afiliacao']=1;}else{$dados['permitir_afiliacao']=null;}
        if($dados['mostrar_na_pagina']==='true'){$dados['mostrar_na_pagina']=1;}else{$dados['mostrar_na_pagina']=null;}

        if($curso->update($dados)) {
            return response()->json(['message' => 'Curso atualizado com sucesso!']);
        } else {
            return response()->json(['message' => 'Erro na atualização! Contate o Dev'], 500);
        }

    }

    public function adm_editar_curso_post(Request $request, $id){

        $dados = $request->except('_token', '_method');

        // Encontrar o curso pelo ID e atualizar os dados
        $curso = Curso::find($id);

        if($curso){

            if ($request->file('capa_quadrada')) {
                $capa_quadrada = $request->file('capa_quadrada')->store('uploads/capa_cursos', 'public');
                $dados['capa_quadrada'] = $capa_quadrada;
                $this->deleteOldImage($curso->capa_quadrada);
            }
            if ($request->file('capa_vertical')) {
                
                $capa_vertical = $request->file('capa_vertical')->store('uploads/capa_cursos', 'public');
                $dados['capa_vertical'] = $capa_vertical;
                
                $this->deleteOldImage($curso->capa_vertical);
            }
            if ($request->file('capa_horizontal')) {
                $capa_horizontal = $request->file('capa_horizontal')->store('uploads/capa_cursos', 'public');
                $dados['capa_horizontal'] = $capa_horizontal;
                $this->deleteOldImage($curso->capa_horizontal);
            }

            if ($request->file('professor_foto')) {
                $professor_foto = $request->file('professor_foto')->store('uploads/capa_cursos', 'public');
                $dados['professor_foto'] = $professor_foto;
                if($curso->professor_foto){$this->deleteOldImage($curso->professor_foto);}
                
            }

            if(!$curso['url']){
                $dados['url'] = $this->createSlug($dados['titulo']);
                if(!$dados['url']){return redirect()->back()->with('error', 'Curso com mesmo nome já cadastrado');}
            }

            if(!isset($dados['publicado'])){$dados['publicado']=false;}
            if(!isset($dados['permitir_afiliacao'])){$dados['permitir_afiliacao']=false;}
            if(!isset($dados['mostrar_na_pagina'])){$dados['mostrar_na_pagina']=false;}
            
            
            if ($curso->update($dados)) {
                return redirect()->back()->with('success', 'Curso atualizado com sucesso!');
            } else {
                return redirect()->back()->with('error', 'Erro na atualização! Contate o Dev');
            }

        }else{
            return redirect()->back()->with('error', 'Erro na atualização! Contate o Dev');
        }
    }

    private function deleteOldImage($imagePath)
    {
        if ($imagePath) {
            $fullImagePath = storage_path('app/public/' . $imagePath);
            if (is_file($fullImagePath) && file_exists($fullImagePath)) {
                unlink($fullImagePath);
            }
        }
    }


    public function createSlug($string) {
        // Converter para minúsculas
        $string = strtolower($string);

        // Substituir caracteres acentuados por suas versões não acentuadas
        $unwanted_array = array(
            'á'=>'a','à'=>'a','â'=>'a','ã'=>'a','ä'=>'a',
            'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
            'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i',
            'ó'=>'o','ò'=>'o','ô'=>'o','õ'=>'o','ö'=>'o',
            'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u',
            'ç'=>'c','ñ'=>'n',
            'Á'=>'a','À'=>'a','Â'=>'a','Ã'=>'a','Ä'=>'a',
            'É'=>'e','È'=>'e','Ê'=>'e','Ë'=>'e',
            'Í'=>'i','Ì'=>'i','Î'=>'i','Ï'=>'i',
            'Ó'=>'o','Ò'=>'o','Ô'=>'o','Õ'=>'o','Ö'=>'o',
            'Ú'=>'u','Ù'=>'u','Û'=>'u','Ü'=>'u',
            'Ç'=>'c','Ñ'=>'n'
        );
        $string = strtr($string, $unwanted_array);

        // Substituir qualquer caractere que não seja letra ou número por um espaço
        $string = preg_replace('/[^a-z0-9\s]/', '', $string);

        // Substituir múltiplos espaços por um único espaço
        $string = preg_replace('/\s+/', ' ', $string);

        // Substituir espaços por hífens
        $string = str_replace(' ', '-', $string);

        $curso = Curso::where('url', $string)->first();

        if($curso){
            return false;
        }else{
            return $string;
        }

        
    }

    public function leads_hotmart()
    {      
        // Pagina os resultados e mantém os parâmetros da query string
        $data = PurchaseEvent::orderBy('created_at', 'desc')->paginate(150);

        $hotmart_leads = $data;
        $titulo_pagina = "Todos os leads";
        // Retorna a view com os leads paginados
        return view('adm.leads.leads', compact('hotmart_leads', 'titulo_pagina'));
    }
  
}
