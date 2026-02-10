<?php
namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Home_e_cursosController;

use App\Models\Codigo_ref;
use App\Models\Curso;
use App\Models\AulasDemonstrativa;
use App\Models\User;

class CursoController extends Controller 
{
    public function afiliados_cadastrar_curso(Request $request){
        $user = Auth::user();
        $home_e_cursosController = new Home_e_cursosController();
        $cursos = $home_e_cursosController->listar_cursos($request, $user);

        return view('dashboard.cursos.index', compact('cursos')); 
        //return view('adm.cursos.afiliados_cadastrar_curso', compact('cursos')); 
    }
 
    public function updateOrder(Request $request)
    {
        $order = $request->input('order');

        if (is_array($order)) {
            foreach ($order as $item) {
                // Encontra o curso pelo ID e atualiza o campo "ordem"
                $curso = Curso::find($item['id']);
                if ($curso) {
                    $curso->ordem = $item['ordem'];
                    $curso->save();
                }
            }

            return response()->json(['success' => true, 'message' => 'Ordem atualizada com sucesso.']);
        }

        return response()->json(['success' => false, 'message' => 'Dados inválidos.'], 400);
    }


    public function afiliados_cadastrar_curso_ref(Request $request){

        $request->validate([
            'user_id' => 'required',
            'codigo_ref' => [
                'required',
                'max:15',
                'regex:/^(?=.*[a-zA-Z])[^\s\.\/:]*$/'
            ],
            'curso_id' => 'required',
            'mostrar_curso' => 'nullable',
        ], [
            'codigo_ref.regex' => 'O campo Código REF não pode conter os caracteres ".", "/", ou ":".',
            'codigo_ref.max' => 'O campo Código REF deve ter no máximo 15 caracteres.',
        ]);
        

      // printf($request->input('mostrar_curso'));
      // exit;

       
        
      if ($request->filled('id')) {
          $codigo_ref = Codigo_ref::firstOrNew(['id' => $request->input('id')]);
      } else {
          $codigo_ref = Codigo_ref::firstOrNew([
              'user_id' => $request->input('user_id'),
              'curso_id' => $request->input('curso_id'),
          ]);
      }

      if ($codigo_ref->exists) {
          // Registro já existe
          $codigo_ref->update([
              'user_id' => $request->input('user_id'),
              'curso_id' => $request->input('curso_id'),
              'codigo_ref' => $request->input('codigo_ref'),
              'mostrar_curso' =>  $request->input('mostrar_curso') ?? 0,
          ]);
          $registroExistente = true;
      } else {
          // Registro não existe, vamos criar um novo
          $codigo_ref->fill([
              'user_id' => $request->input('user_id'),
              'curso_id' => $request->input('curso_id'),
              'codigo_ref' => $request->input('codigo_ref'),
              'mostrar_curso' =>  $request->input('mostrar_curso') ?? 0,
          ]);
          $codigo_ref->save();
          $registroExistente = false;
      }

      if ($request->expectsJson()) {
          $message = $registroExistente
              ? "Codigo REF do curso ".$request->input('titulo')." editado com sucesso!"
              : "Codigo REF do curso ".$request->input('titulo')." cadastrado com sucesso!";

          $curso = Curso::find($codigo_ref->curso_id);
          $baseCheckoutUrl = $curso && $curso->codigo_afiliado_plano_completo
              ? "https://go.hotmart.com/{$codigo_ref->codigo_ref}?ap={$curso->codigo_afiliado_plano_completo}"
              : ($curso->link_checkout_completo ?? null);

          return response()->json([
              'success' => $message,
              'codigo_ref_id' => $codigo_ref->id,
              'codigo_ref' => $codigo_ref->codigo_ref,
              'mostrar_curso' => (bool) $codigo_ref->mostrar_curso,
              'curso_id' => (int) $codigo_ref->curso_id,
              'base_checkout_url' => $baseCheckoutUrl,
          ]);
      }

      if ($registroExistente) {
        return redirect()->back()->with('success', "Código REF do curso ".$request->input('titulo')." editado com sucesso!");
        } else {
            return redirect()->back()->with('success', "Código REF do curso ".$request->input('titulo')." cadastrado com sucesso!");
        }
        
    }

    public function aulas_gratuitas_index()
    {
        $aulas = AulasDemonstrativa::all(); // Pega todas as aulas cadastradas
        return view('adm.cursos.aulas_gratuitas_lista', compact('aulas'));
    }

    public function aulas_gratuitas_destroy($id)
    {
        $aula = AulasDemonstrativa::findOrFail($id);
        $aula->delete();

        return redirect()->route('aulas_gratuitas_index')->with('success', 'Aula excluída com sucesso!');
    }

    public function aulas_gratuitas_cadastrar(){

        $cursos = Curso::all();
        return view('adm.cursos.aulas_gratuitas_inserir', compact('cursos')); 
    }

    public function aulas_gratuitas_cadastrar_post(Request $request){
        // Validação dos dados
        $request->validate([
            'id_curso' => 'required|exists:curso,id',
            'aula_titulo' => 'required|string|max:255',
            'aula_id_youtube' => 'required|string|max:255',
        ]);

        // Criação do novo registro na tabela
        AulasDemonstrativa::create([
            'id_curso' => $request->id_curso,
            'aula_titulo' => $request->aula_titulo,
            'aula_id_youtube' => $request->aula_id_youtube,
        ]);

        return redirect()->route('aulas_gratuitas_cadastrar')->with('success', 'Aula demonstrativa criada com sucesso!');
        
    }
    
}
