<?php
namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Home_e_cursosController;

use App\Models\Codigo_ref;
use App\Models\Curso;
use App\Models\AulasDemonstrativa;
use App\Models\Cupom;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CursoController extends Controller 
{
    public function afiliados_cadastrar_curso(Request $request){
        $user = Auth::user();
        $home_e_cursosController = new Home_e_cursosController();
        $cursos = $home_e_cursosController->listar_cursos($request, $user);
        $cupons = Cupom::orderBy('desconto')->orderBy('codigo')->get();

        return view('dashboard.cursos.index', compact('cursos', 'cupons')); 
        //return view('adm.cursos.afiliados_cadastrar_curso', compact('cursos')); 
    }
 
    public function updateOrder(Request $request)
    {
        $payload = $request->input('order');
        if (!is_array($payload)) {
            return response()->json(['success' => false, 'message' => 'Dados inválidos.'], 400);
        }

        $idsRecebidos = collect($payload)
            ->map(function ($item) {
                if (!is_array($item) || !isset($item['id'])) {
                    return null;
                }
                return (int) $item['id'];
            })
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($idsRecebidos->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Nenhum curso válido enviado.'], 422);
        }

        DB::transaction(function () use ($idsRecebidos) {
            $idsAtuais = Curso::query()
                ->orderBy('ordem')
                ->orderBy('id')
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values();

            $idsPrioritarios = $idsRecebidos
                ->filter(fn ($id) => $idsAtuais->contains($id))
                ->values();

            $idsRestantes = $idsAtuais
                ->reject(fn ($id) => $idsPrioritarios->contains($id))
                ->values();

            $ordemNormalizada = $idsPrioritarios->concat($idsRestantes)->values();

            foreach ($ordemNormalizada as $index => $cursoId) {
                Curso::whereKey($cursoId)->update([
                    'ordem' => $index + 1,
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Ordem atualizada com sucesso.']);
    }


    public function afiliados_cadastrar_curso_ref(Request $request){
        $codigoRefInput = trim((string) $request->input('codigo_ref', ''));
        $request->merge([
            'codigo_ref' => $codigoRefInput,
        ]);

        $temCupons = Schema::hasTable('cupons') && Cupom::query()->exists();

        $rules = [
            'user_id' => 'required',
            'codigo_ref' => [
                'required',
                'max:15',
                'regex:/^(?=.*[a-zA-Z])[^\s\.\/:]*$/'
            ],
            'curso_id' => 'required',
            'mostrar_curso' => 'nullable',
            'formulario_pre_checkout' => 'nullable|boolean',
            'modo_precos' => 'nullable|string|in:padrao,um_preco,dois_precos',
            'cupom_principal_id' => ['nullable', 'integer'],
            'cupom_secundario_id' => ['nullable', 'integer'],
        ];

        if ($temCupons) {
            $rules['cupom_principal_id'][] = 'exists:cupons,id';
            $rules['cupom_secundario_id'][] = 'exists:cupons,id';
        }

        $validator = Validator::make($request->all(), $rules, [
            'codigo_ref.regex' => 'O campo Código REF não pode conter os caracteres ".", "/", ou ":".',
            'codigo_ref.max' => 'O campo Código REF deve ter no máximo 15 caracteres.',
            'cupom_secundario_id.required' => 'Selecione o cupom do segundo preço.',
            'cupom_secundario_id.different' => 'O cupom do segundo preço deve ser diferente do principal.',
        ]);

      $validator->after(function ($validator) use ($request, $temCupons) {
          if (!$temCupons) {
              return;
          }

          $modoPrecos = $request->input('modo_precos', 'padrao');
          $cupomPrincipalId = $request->filled('cupom_principal_id') ? (int) $request->input('cupom_principal_id') : null;
          $cupomSecundarioId = $request->filled('cupom_secundario_id') ? (int) $request->input('cupom_secundario_id') : null;

          if ($modoPrecos === 'dois_precos' && empty($cupomSecundarioId)) {
              $validator->errors()->add('cupom_secundario_id', 'Selecione o cupom do segundo preço.');
          }

          if (
              $modoPrecos === 'dois_precos' &&
              !empty($cupomPrincipalId) &&
              !empty($cupomSecundarioId) &&
              $cupomPrincipalId === $cupomSecundarioId
          ) {
              $validator->errors()->add('cupom_secundario_id', 'O cupom do segundo preço deve ser diferente do principal.');
          }
      });

      $validator->validate();

      $formularioPreCheckout = $request->has('formulario_pre_checkout')
          ? $request->boolean('formulario_pre_checkout')
          : true;

      $modoPrecos = $request->input('modo_precos', 'padrao');
      if (!in_array($modoPrecos, ['padrao', 'um_preco', 'dois_precos'], true)) {
          $modoPrecos = 'padrao';
      }

      $cupomPrincipalId = $request->filled('cupom_principal_id') ? (int) $request->input('cupom_principal_id') : null;
      $cupomSecundarioId = $request->filled('cupom_secundario_id') ? (int) $request->input('cupom_secundario_id') : null;

      if (!$temCupons) {
          $modoPrecos = 'padrao';
          $cupomPrincipalId = null;
          $cupomSecundarioId = null;
      } elseif ($modoPrecos === 'padrao') {
          $cupomPrincipalId = null;
          $cupomSecundarioId = null;
      } elseif ($modoPrecos === 'um_preco') {
          $cupomSecundarioId = null;
      }

      $userId = (int) $request->input('user_id');

      if ($request->filled('id')) {
          $codigo_ref = Codigo_ref::firstOrNew(['id' => $request->input('id')]);
      } else {
          $codigo_ref = Codigo_ref::firstOrNew([
              'user_id' => $userId,
              'curso_id' => $request->input('curso_id'),
          ]);
      }

      $isNewCodigoRef = !$codigo_ref->exists;
      if ($request->has('mostrar_curso')) {
          $mostrarCurso = $request->boolean('mostrar_curso');
      } elseif ($isNewCodigoRef) {
          $mostrarCurso = true;
      } else {
          $mostrarCurso = (bool) ($codigo_ref->mostrar_curso ?? false);
      }

      $codigoRefNovo = (string) $request->input('codigo_ref');
      $codigoRefAtual = $codigo_ref->exists ? trim((string) $codigo_ref->codigo_ref) : null;
      $deveValidarHotmart = !$codigo_ref->exists || $codigoRefAtual !== $codigoRefNovo;

      if ($deveValidarHotmart) {
          $resultadoValidacao = $this->validarCodigoRefNoHotmart($codigoRefNovo);

          if ($resultadoValidacao === 'invalid') {
              return $this->codigoRefValidationErrorResponse(
                  $request,
                  'Código REF inválido. Verifique se o link da Hotmart existe antes de salvar.'
              );
          }

          if ($resultadoValidacao === 'unavailable') {
              return $this->codigoRefValidationErrorResponse(
                  $request,
                  'Não foi possível validar o Código REF na Hotmart agora. Tente novamente em instantes.'
              );
          }
      }

      if ($codigo_ref->exists) {
          // Registro já existe
          $codigo_ref->update([
              'user_id' => $userId,
              'curso_id' => $request->input('curso_id'),
              'codigo_ref' => $request->input('codigo_ref'),
              'mostrar_curso' => $mostrarCurso,
              'formulario_pre_checkout' => $formularioPreCheckout,
              'modo_precos' => $modoPrecos,
              'cupom_principal_id' => $cupomPrincipalId,
              'cupom_secundario_id' => $cupomSecundarioId,
          ]);
          $registroExistente = true;
      } else {
          // Registro não existe, vamos criar um novo
          $codigo_ref->fill([
              'user_id' => $userId,
              'curso_id' => $request->input('curso_id'),
              'codigo_ref' => $request->input('codigo_ref'),
              'mostrar_curso' => $mostrarCurso,
              'formulario_pre_checkout' => $formularioPreCheckout,
              'modo_precos' => $modoPrecos,
              'cupom_principal_id' => $cupomPrincipalId,
              'cupom_secundario_id' => $cupomSecundarioId,
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
              'formulario_pre_checkout' => (bool) ($codigo_ref->formulario_pre_checkout ?? true),
              'curso_id' => (int) $codigo_ref->curso_id,
              'modo_precos' => $codigo_ref->modo_precos ?? 'padrao',
              'cupom_principal_id' => $codigo_ref->cupom_principal_id ? (int) $codigo_ref->cupom_principal_id : null,
              'cupom_secundario_id' => $codigo_ref->cupom_secundario_id ? (int) $codigo_ref->cupom_secundario_id : null,
              'base_checkout_url' => $baseCheckoutUrl,
          ]);
      }

      if ($registroExistente) {
        return redirect()->back()->with('success', "Código REF do curso ".$request->input('titulo')." editado com sucesso!");
        } else {
            return redirect()->back()->with('success', "Código REF do curso ".$request->input('titulo')." cadastrado com sucesso!");
        }
        
    }

    private function validarCodigoRefNoHotmart(string $codigoRef): string
    {
        if ($codigoRef === '') {
            return 'invalid';
        }

        $url = 'https://go.hotmart.com/' . urlencode($codigoRef);

        try {
            $response = Http::timeout(8)
                ->connectTimeout(5)
                ->withOptions([
                    'allow_redirects' => true,
                ])
                ->withHeaders([
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'User-Agent' => 'Mozilla/5.0 (compatible; PortalJE/1.0; +https://portalje.org)',
                ])
                ->get($url);

            $status = $response->status();

            if (in_array($status, [400, 404, 410], true)) {
                return 'invalid';
            }

            if ($status >= 500 || in_array($status, [0, 401, 403, 429], true)) {
                return 'unavailable';
            }

            return 'valid';
        } catch (\Throwable $e) {
            return 'unavailable';
        }
    }

    private function codigoRefValidationErrorResponse(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Os dados informados são inválidos.',
                'errors' => [
                    'codigo_ref' => [$message],
                ],
            ], 422);
        }

        return redirect()
            ->back()
            ->withErrors([
                'codigo_ref' => $message,
            ])
            ->withInput();
    }

    public function aulas_gratuitas_index()
    {
        $aulas = AulasDemonstrativa::with('curso')->orderByDesc('id')->get(); // Pega todas as aulas cadastradas
        return view('adm.cursos.aulas_gratuitas_lista', compact('aulas'));
    }

    public function aulas_gratuitas_editar($id)
    {
        $aula = AulasDemonstrativa::findOrFail($id);
        $cursos = Curso::orderBy('titulo')->get();

        return view('adm.cursos.aulas_gratuitas_inserir', compact('cursos', 'aula'));
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

    public function aulas_gratuitas_editar_post(Request $request, $id)
    {
        $request->validate([
            'id_curso' => 'required|exists:curso,id',
            'aula_titulo' => 'required|string|max:255',
            'aula_id_youtube' => 'required|string|max:255',
        ]);

        $aula = AulasDemonstrativa::findOrFail($id);
        $aula->update([
            'id_curso' => $request->id_curso,
            'aula_titulo' => $request->aula_titulo,
            'aula_id_youtube' => $request->aula_id_youtube,
        ]);

        return redirect()->route('aulas_gratuitas_index')->with('success', 'Aula demonstrativa atualizada com sucesso!');
    }
    
}
