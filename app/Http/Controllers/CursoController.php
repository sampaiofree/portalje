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
use App\Models\Dados_portal;
use App\Models\RootDomainCourseConfig;
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

    public function admin_root_domain_course_pages(Request $request)
    {
        $cupons = Cupom::orderBy('desconto')->orderBy('codigo')->get();
        $formularioPreCheckoutDefault = $this->portalFormularioPreCheckoutDefault();
        $configsByCourseId = Schema::hasTable('root_domain_course_configs')
            ? RootDomainCourseConfig::query()->get()->keyBy('curso_id')
            : collect();

        $cursos = Curso::query()
            ->orderBy('ordem')
            ->orderBy('id')
            ->get()
            ->map(function (Curso $curso) use ($configsByCourseId, $formularioPreCheckoutDefault) {
                /** @var \App\Models\RootDomainCourseConfig|null $config */
                $config = $configsByCourseId->get($curso->id);

                $curso->root_domain_course_config_id = $config->id ?? null;
                $curso->mostrar_curso = $config
                    ? (bool) $config->mostrar_curso
                    : (bool) ($curso->mostrar_na_pagina ?? false);
                $curso->formulario_pre_checkout = $config
                    ? (bool) $config->formulario_pre_checkout
                    : $formularioPreCheckoutDefault;
                $curso->modo_precos = $config && in_array((string) $config->modo_precos, ['padrao', 'um_preco', 'dois_precos'], true)
                    ? (string) $config->modo_precos
                    : 'padrao';
                $curso->cupom_principal_id = $config && !empty($config->cupom_principal_id)
                    ? (int) $config->cupom_principal_id
                    : null;
                $curso->cupom_secundario_id = $config && !empty($config->cupom_secundario_id)
                    ? (int) $config->cupom_secundario_id
                    : null;
                $curso->usar_contador = $config ? (bool) $config->usar_contador : false;
                $curso->contador_minutos = $config && !empty($config->contador_minutos)
                    ? (int) $config->contador_minutos
                    : null;
                $curso->contador_acao = $config->contador_acao ?? null;
                $curso->contador_destino_oferta = $config->contador_destino_oferta ?? null;

                return $curso;
            })
            ->values();

        $sharedDomains = [
            'portalje.org',
            'jovemempreendedor.org',
        ];

        return view('dashboard.admin.root-domain-course-pages', compact('cursos', 'cupons', 'sharedDomains'));
    }

    public function admin_root_domain_course_pages_save(Request $request)
    {
        if (!Schema::hasTable('root_domain_course_configs')) {
            $message = 'A configuração dos domínios raiz ainda não está disponível. Rode as migrations pendentes.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 503);
            }

            return redirect()
                ->route('admin.root_domain_course_pages')
                ->with('error', $message);
        }

        $request->validate([
            'curso_id' => ['required', 'integer', 'exists:curso,id'],
            'id' => ['nullable', 'integer'],
        ]);

        $temCupons = Schema::hasTable('cupons') && Cupom::query()->exists();
        $configuracao = $this->validateAndNormalizeRootDomainPublicPageConfig($request, $temCupons);
        $curso = Curso::query()->findOrFail((int) $request->input('curso_id'));

        $mostrarCurso = $request->has('mostrar_curso')
            ? $request->boolean('mostrar_curso')
            : (bool) ($curso->mostrar_na_pagina ?? false);

        $config = RootDomainCourseConfig::query()->firstOrNew([
            'curso_id' => $curso->id,
        ]);

        $config->fill([
            'mostrar_curso' => $mostrarCurso,
            'formulario_pre_checkout' => $configuracao['formulario_pre_checkout'],
            'modo_precos' => $configuracao['modo_precos'],
            'cupom_principal_id' => $configuracao['cupom_principal_id'],
            'cupom_secundario_id' => $configuracao['cupom_secundario_id'],
            'usar_contador' => $configuracao['usar_contador'],
            'contador_minutos' => $configuracao['contador_minutos'],
            'contador_acao' => $configuracao['contador_acao'],
            'contador_destino_oferta' => $configuracao['contador_destino_oferta'],
        ]);
        $config->save();

        $message = 'Configurações da página pública do curso ' . $curso->titulo . ' salvas com sucesso.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => $message,
                'root_domain_course_config_id' => (int) $config->id,
                'curso_id' => (int) $curso->id,
                'mostrar_curso' => (bool) $config->mostrar_curso,
                'formulario_pre_checkout' => (bool) $config->formulario_pre_checkout,
                'modo_precos' => $config->modo_precos ?? 'padrao',
                'cupom_principal_id' => $config->cupom_principal_id ? (int) $config->cupom_principal_id : null,
                'cupom_secundario_id' => $config->cupom_secundario_id ? (int) $config->cupom_secundario_id : null,
                'usar_contador' => (bool) ($config->usar_contador ?? false),
                'contador_minutos' => $config->contador_minutos ? (int) $config->contador_minutos : null,
                'contador_acao' => $config->contador_acao ?: null,
                'contador_destino_oferta' => $config->contador_destino_oferta ?: null,
            ]);
        }

        return redirect()
            ->route('admin.root_domain_course_pages')
            ->with('success', $message);
    }

    public function afiliados_cadastrar_curso_bulk_actions(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|string|in:ativar_todos,desativar_todos,configurar_pagina_publica_todos',
        ]);

        $action = $validated['action'];

        $baseQuery = $this->codigoRefComCodigoPreenchidoQueryForAuthUser();

        $updatedCourseIds = (clone $baseQuery)
            ->pluck('curso_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if ($action === 'configurar_pagina_publica_todos') {
            $temCupons = Schema::hasTable('cupons') && Cupom::query()->exists();
            $configuracao = $this->validateAndNormalizeBulkPublicPageConfig($request, $temCupons);

            $updatedCount = (clone $baseQuery)->update([
                'formulario_pre_checkout' => $configuracao['formulario_pre_checkout'],
                'modo_precos' => $configuracao['modo_precos'],
                'cupom_principal_id' => $configuracao['cupom_principal_id'],
                'cupom_secundario_id' => $configuracao['cupom_secundario_id'],
            ]);

            return response()->json([
                'success' => true,
                'action' => $action,
                'message' => 'Configurações da página pública aplicadas em todos os cursos com Código REF preenchido.',
                'updated_count' => $updatedCount,
                'updated_course_ids' => $updatedCourseIds,
                'formulario_pre_checkout' => $configuracao['formulario_pre_checkout'],
                'modo_precos' => $configuracao['modo_precos'],
                'cupom_principal_id' => $configuracao['cupom_principal_id'],
                'cupom_secundario_id' => $configuracao['cupom_secundario_id'],
            ]);
        }

        $mostrarCurso = $action === 'ativar_todos';

        $updatedCount = (clone $baseQuery)->update([
            'mostrar_curso' => $mostrarCurso,
        ]);

        $message = $action === 'ativar_todos'
            ? 'Cursos com Código REF ativados com sucesso.'
            : 'Cursos com Código REF desativados com sucesso.';

        return response()->json([
            'success' => true,
            'action' => $action,
            'message' => $message,
            'updated_count' => $updatedCount,
            'updated_course_ids' => $updatedCourseIds,
        ]);
    }

    private function codigoRefComCodigoPreenchidoQueryForAuthUser()
    {
        return Codigo_ref::query()
            ->where('user_id', Auth::id())
            ->whereNotNull('codigo_ref')
            ->whereRaw("TRIM(codigo_ref) <> ''");
    }

    private function validateAndNormalizeBulkPublicPageConfig(Request $request, bool $temCupons): array
    {
        $rules = [
            'formulario_pre_checkout' => ['required', 'boolean'],
            'modo_precos' => ['required', 'string', 'in:padrao,um_preco,dois_precos'],
            'cupom_principal_id' => ['nullable', 'integer'],
            'cupom_secundario_id' => ['nullable', 'integer'],
        ];

        if ($temCupons) {
            $rules['cupom_principal_id'][] = 'exists:cupons,id';
            $rules['cupom_secundario_id'][] = 'exists:cupons,id';
        }

        $validator = Validator::make($request->all(), $rules, [
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

        $validated = $validator->validate();

        $modoPrecos = $validated['modo_precos'] ?? 'padrao';
        if (!in_array($modoPrecos, ['padrao', 'um_preco', 'dois_precos'], true)) {
            $modoPrecos = 'padrao';
        }

        $cupomPrincipalId = !empty($validated['cupom_principal_id']) ? (int) $validated['cupom_principal_id'] : null;
        $cupomSecundarioId = !empty($validated['cupom_secundario_id']) ? (int) $validated['cupom_secundario_id'] : null;

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

        return [
            'formulario_pre_checkout' => $request->boolean('formulario_pre_checkout'),
            'modo_precos' => $modoPrecos,
            'cupom_principal_id' => $cupomPrincipalId,
            'cupom_secundario_id' => $cupomSecundarioId,
        ];
    }

    private function normalizeModoPrecosInput(?string $modoPrecos, bool $temCupons): string
    {
        $modoPrecos = is_string($modoPrecos) ? trim($modoPrecos) : 'padrao';

        if (!in_array($modoPrecos, ['padrao', 'um_preco', 'dois_precos'], true)) {
            $modoPrecos = 'padrao';
        }

        if (!$temCupons) {
            return 'padrao';
        }

        return $modoPrecos;
    }

    private function availableCountdownDestinationOffers(string $modoPrecos, bool $temCupons): array
    {
        $modoPrecos = $this->normalizeModoPrecosInput($modoPrecos, $temCupons);
        $destinos = ['completo_padrao'];

        if ($modoPrecos === 'padrao') {
            $destinos[] = 'basico_padrao';
            return $destinos;
        }

        $cupomIds = $temCupons
            ? Cupom::query()->pluck('id')->map(fn ($id) => (int) $id)->filter(fn ($id) => $id > 0)->values()->all()
            : [];

        foreach ($cupomIds as $cupomId) {
            $destinos[] = 'completo_cupom:' . $cupomId;
        }

        if ($modoPrecos === 'dois_precos') {
            foreach ($cupomIds as $cupomId) {
                $destinos[] = 'basico_cupom:' . $cupomId;
            }
        }

        return $destinos;
    }

    private function normalizeCountdownConfiguration(Request $request, string $modoPrecos, bool $temCupons): array
    {
        if (!$request->boolean('usar_contador')) {
            return [
                'usar_contador' => false,
                'contador_minutos' => null,
                'contador_acao' => null,
                'contador_destino_oferta' => null,
            ];
        }

        $contadorAcao = $request->input('contador_acao');
        $contadorDestinoOferta = trim((string) $request->input('contador_destino_oferta', ''));

        if ($contadorAcao !== 'alterar_preco') {
            $contadorDestinoOferta = null;
        }

        return [
            'usar_contador' => true,
            'contador_minutos' => $request->filled('contador_minutos')
                ? (int) $request->input('contador_minutos')
                : null,
            'contador_acao' => in_array($contadorAcao, ['nada', 'encerrar_basico', 'alterar_preco'], true)
                ? $contadorAcao
                : null,
            'contador_destino_oferta' => $contadorDestinoOferta !== '' ? $contadorDestinoOferta : null,
        ];
    }

    private function validateAndNormalizeRootDomainPublicPageConfig(Request $request, bool $temCupons): array
    {
        $rules = [
            'formulario_pre_checkout' => 'nullable|boolean',
            'modo_precos' => 'nullable|string|in:padrao,um_preco,dois_precos',
            'cupom_principal_id' => ['nullable', 'integer'],
            'cupom_secundario_id' => ['nullable', 'integer'],
            'usar_contador' => 'nullable|boolean',
            'contador_minutos' => 'nullable|integer|in:1,5,10,20,30,50',
            'contador_acao' => 'nullable|string|in:nada,encerrar_basico,alterar_preco',
            'contador_destino_oferta' => 'nullable|string|max:60',
        ];

        if ($temCupons) {
            $rules['cupom_principal_id'][] = 'exists:cupons,id';
            $rules['cupom_secundario_id'][] = 'exists:cupons,id';
        }

        $validator = Validator::make($request->all(), $rules, [
            'cupom_secundario_id.required' => 'Selecione o cupom do segundo preço.',
            'cupom_secundario_id.different' => 'O cupom do segundo preço deve ser diferente do principal.',
            'contador_minutos.in' => 'Selecione um tempo válido para o contador.',
            'contador_acao.in' => 'Selecione uma ação válida para o contador.',
        ]);

        $validator->after(function ($validator) use ($request, $temCupons) {
            $modoPrecos = $request->input('modo_precos', 'padrao');
            $modoPrecosNormalizado = $this->normalizeModoPrecosInput($modoPrecos, $temCupons);
            $cupomPrincipalId = $request->filled('cupom_principal_id') ? (int) $request->input('cupom_principal_id') : null;
            $cupomSecundarioId = $request->filled('cupom_secundario_id') ? (int) $request->input('cupom_secundario_id') : null;

            if ($temCupons) {
                if ($modoPrecosNormalizado === 'dois_precos' && empty($cupomSecundarioId)) {
                    $validator->errors()->add('cupom_secundario_id', 'Selecione o cupom do segundo preço.');
                }

                if (
                    $modoPrecosNormalizado === 'dois_precos' &&
                    !empty($cupomPrincipalId) &&
                    !empty($cupomSecundarioId) &&
                    $cupomPrincipalId === $cupomSecundarioId
                ) {
                    $validator->errors()->add('cupom_secundario_id', 'O cupom do segundo preço deve ser diferente do principal.');
                }
            }

            if (!$request->boolean('usar_contador')) {
                return;
            }

            if (!$request->filled('contador_minutos')) {
                $validator->errors()->add('contador_minutos', 'Selecione os minutos do contador.');
            }

            if (!$request->filled('contador_acao')) {
                $validator->errors()->add('contador_acao', 'Selecione a ação do contador.');
                return;
            }

            $contadorAcao = (string) $request->input('contador_acao');

            if ($contadorAcao === 'encerrar_basico' && !in_array($modoPrecosNormalizado, ['padrao', 'dois_precos'], true)) {
                $validator->errors()->add('contador_acao', 'A ação de encerrar o plano básico só pode ser usada quando a página exibe o plano básico.');
            }

            if ($contadorAcao === 'alterar_preco') {
                $contadorDestinoOferta = trim((string) $request->input('contador_destino_oferta', ''));

                if ($contadorDestinoOferta === '') {
                    $validator->errors()->add('contador_destino_oferta', 'Selecione o preço que será mostrado após o contador.');
                    return;
                }

                $destinosValidos = $this->availableCountdownDestinationOffers($modoPrecosNormalizado, $temCupons);

                if (!in_array($contadorDestinoOferta, $destinosValidos, true)) {
                    $validator->errors()->add('contador_destino_oferta', 'Selecione um preço compatível com a configuração atual da página.');
                }
            }
        });

        $validator->validate();

        $formularioPreCheckout = $request->has('formulario_pre_checkout')
            ? $request->boolean('formulario_pre_checkout')
            : $this->portalFormularioPreCheckoutDefault();

        $modoPrecos = $this->normalizeModoPrecosInput($request->input('modo_precos', 'padrao'), $temCupons);

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

        $countdownConfig = $this->normalizeCountdownConfiguration($request, $modoPrecos, $temCupons);

        return [
            'formulario_pre_checkout' => $formularioPreCheckout,
            'modo_precos' => $modoPrecos,
            'cupom_principal_id' => $cupomPrincipalId,
            'cupom_secundario_id' => $cupomSecundarioId,
            'usar_contador' => $countdownConfig['usar_contador'],
            'contador_minutos' => $countdownConfig['contador_minutos'],
            'contador_acao' => $countdownConfig['contador_acao'],
            'contador_destino_oferta' => $countdownConfig['contador_destino_oferta'],
        ];
    }

    private function portalFormularioPreCheckoutDefault(): bool
    {
        if (!Schema::hasTable('portal_informacoes')) {
            return true;
        }

        $dadosPortal = Dados_portal::query()->first();

        return isset($dadosPortal->formulario_pre_checkout)
            ? (bool) $dadosPortal->formulario_pre_checkout
            : true;
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
            'usar_contador' => 'nullable|boolean',
            'contador_minutos' => 'nullable|integer|in:1,5,10,20,30,50',
            'contador_acao' => 'nullable|string|in:nada,encerrar_basico,alterar_preco',
            'contador_destino_oferta' => 'nullable|string|max:60',
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
            'contador_minutos.in' => 'Selecione um tempo válido para o contador.',
            'contador_acao.in' => 'Selecione uma ação válida para o contador.',
        ]);

      $validator->after(function ($validator) use ($request, $temCupons) {
          $modoPrecos = $request->input('modo_precos', 'padrao');
          $modoPrecosNormalizado = $this->normalizeModoPrecosInput($modoPrecos, $temCupons);
          $cupomPrincipalId = $request->filled('cupom_principal_id') ? (int) $request->input('cupom_principal_id') : null;
          $cupomSecundarioId = $request->filled('cupom_secundario_id') ? (int) $request->input('cupom_secundario_id') : null;

          if ($temCupons) {
              if ($modoPrecosNormalizado === 'dois_precos' && empty($cupomSecundarioId)) {
                  $validator->errors()->add('cupom_secundario_id', 'Selecione o cupom do segundo preço.');
              }

              if (
                  $modoPrecosNormalizado === 'dois_precos' &&
                  !empty($cupomPrincipalId) &&
                  !empty($cupomSecundarioId) &&
                  $cupomPrincipalId === $cupomSecundarioId
              ) {
                  $validator->errors()->add('cupom_secundario_id', 'O cupom do segundo preço deve ser diferente do principal.');
              }
          }

          if (!$request->boolean('usar_contador')) {
              return;
          }

          if (!$request->filled('contador_minutos')) {
              $validator->errors()->add('contador_minutos', 'Selecione os minutos do contador.');
          }

          if (!$request->filled('contador_acao')) {
              $validator->errors()->add('contador_acao', 'Selecione a ação do contador.');
              return;
          }

          $contadorAcao = (string) $request->input('contador_acao');

          if ($contadorAcao === 'encerrar_basico' && !in_array($modoPrecosNormalizado, ['padrao', 'dois_precos'], true)) {
              $validator->errors()->add('contador_acao', 'A ação de encerrar o plano básico só pode ser usada quando a página exibe o plano básico.');
          }

          if ($contadorAcao === 'alterar_preco') {
              $contadorDestinoOferta = trim((string) $request->input('contador_destino_oferta', ''));

              if ($contadorDestinoOferta === '') {
                  $validator->errors()->add('contador_destino_oferta', 'Selecione o preço que será mostrado após o contador.');
                  return;
              }

              $destinosValidos = $this->availableCountdownDestinationOffers($modoPrecosNormalizado, $temCupons);

              if (!in_array($contadorDestinoOferta, $destinosValidos, true)) {
                  $validator->errors()->add('contador_destino_oferta', 'Selecione um preço compatível com a configuração atual da página.');
              }
          }
      });

      $validator->validate();

      $formularioPreCheckout = $request->has('formulario_pre_checkout')
          ? $request->boolean('formulario_pre_checkout')
          : true;

      $modoPrecos = $request->input('modo_precos', 'padrao');
      $modoPrecos = $this->normalizeModoPrecosInput($modoPrecos, $temCupons);

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

      $countdownConfig = $this->normalizeCountdownConfiguration($request, $modoPrecos, $temCupons);

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
      if ($isNewCodigoRef) {
          $mostrarCurso = true;
      } elseif ($request->has('mostrar_curso')) {
          $mostrarCurso = $request->boolean('mostrar_curso');
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
              'usar_contador' => $countdownConfig['usar_contador'],
              'contador_minutos' => $countdownConfig['contador_minutos'],
              'contador_acao' => $countdownConfig['contador_acao'],
              'contador_destino_oferta' => $countdownConfig['contador_destino_oferta'],
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
              'usar_contador' => $countdownConfig['usar_contador'],
              'contador_minutos' => $countdownConfig['contador_minutos'],
              'contador_acao' => $countdownConfig['contador_acao'],
              'contador_destino_oferta' => $countdownConfig['contador_destino_oferta'],
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
              'usar_contador' => (bool) ($codigo_ref->usar_contador ?? false),
              'contador_minutos' => $codigo_ref->contador_minutos ? (int) $codigo_ref->contador_minutos : null,
              'contador_acao' => $codigo_ref->contador_acao ?: null,
              'contador_destino_oferta' => $codigo_ref->contador_destino_oferta ?: null,
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
