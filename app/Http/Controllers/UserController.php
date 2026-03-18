<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

use App\Http\Controllers\Home_e_cursosController;

use App\Models\PurchaseEvent;
use App\Models\WhatsappAtendimento;
use App\Services\PhoneVerificationService;
use App\Services\JourneyProgressService;

class UserController extends Controller 
{
    public function __construct()
    {
        // Aplica o middleware de autenticação a todas as ações do controlador
        $this->middleware('auth')->except(['pixel_user']);
    }

    public function pixel_user(Request $request){
        $dominio = $request->getHost(); // ex: meudominio.com
        $user = User::where('dominio', $dominio)
                                ->orWhere('dominio_externo', $dominio)
                                ->first();
        if ($user && $user->meta_pixel_id) {
            return response()->json(['pixel_id' => $user->meta_pixel_id]);
        }

        return response()->json(['pixel_id' => null]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $cursos = app(Home_e_cursosController::class);
        $cursos = $cursos->listar_cursos($request, $user); 

        // Migração suave do campo legado em users para a nova tabela
        if (!empty($user->whatsapp_atendimento) && !$user->whatsappAtendimentos()->exists()) {
            $whatsappLegacy = preg_replace('/\D/', '', (string) $user->whatsapp_atendimento);
            if ($whatsappLegacy !== '') {
                $user->whatsappAtendimentos()->create([
                    'whatsapp' => $whatsappLegacy,
                    'is_active' => true,
                ]);
            }
        }

        $whatsappAtendimentos = $user->whatsappAtendimentos()
            ->orderByDesc('is_active')
            ->orderByDesc('updated_at')
            ->get();
        
        return view('dashboard.dashboard', compact('cursos', 'whatsappAtendimentos'));
        //return view('adm.dashboard', compact('cursos'));
    }

    public function ranking(Request $request)
    {
        $activeTab = $request->query('tab', 'xp');
        if (!in_array($activeTab, ['sales', 'xp'], true)) {
            $activeTab = 'xp';
        }

        $xpRows = [];
        $xpTotals = [
            'participants' => 0,
            'xp_total' => 0,
        ];

        // Obtém o valor do mês enviado pelo GET
        $mes = $request->query('mes');
        if (!is_string($mes) || trim($mes) === '') {
            $mes = date('Y-m');
        }

        if (!preg_match('/^\d{4}-\d{2}$/', $mes)) {
            $mes = date('Y-m');
        }

        list($ano, $mesNumero) = explode('-', $mes);

        // Define $dataInicio e $dataFim com base no mês e ano
        $dataInicio = Carbon::create($ano, $mesNumero, 1)->startOfMonth();
        $dataFim = Carbon::create($ano, $mesNumero, 1)->endOfMonth();

        // Verifica se é o mês atual e ajusta $dataFim para o final de ontem
        /*if ($mes == date('Y-m')) {
            $dataFim = Carbon::yesterday()->endOfDay();
        }*/

        // Converte para o formato timestamp, se necessário
        $dataInicioTimestamp = $this->timestamp($dataInicio);
        $dataFimTimestamp = $this->timestamp($dataFim);

        $d = [];
        if ($activeTab === 'sales') {
            // Filtra os dados pelo intervalo de datas e pelo ID do usuário logado
            if(!$request->query('mes')){
                $mes = null;
                $dataInicioTimestamp = Carbon::now()->startOfYear()->toDateTimeString(); // Início do ano atual
                $dataFimTimestamp = Carbon::now()->endOfYear()->toDateTimeString(); // Fim do ano atual
                $dataInicioTimestamp = $this->timestamp($dataInicioTimestamp);
                $dataFimTimestamp = $this->timestamp($dataFimTimestamp);
                $dados = PurchaseEvent::whereIn('purchase_status', ['APPROVED', 'COMPLETED'])
                    ->whereBetween('purchase_approved_date', [$dataInicioTimestamp, $dataFimTimestamp])
                    ->get();
            }else{
                $dados = PurchaseEvent::whereIn('purchase_status', ['APPROVED', 'COMPLETED'])
                ->whereBetween('purchase_approved_date', [$dataInicioTimestamp, $dataFimTimestamp])
                ->get();
            }

            // Agrupando os dados como no seu código original
            $dadosAgrupados = $dados->groupBy(function($item) {
                return data_get($item, 'affiliate_name');
            });

            // Converte para um array associativo, se necessário
            $d = $dadosAgrupados->toArray();
        } else {
            $journeyProgressService = app(JourneyProgressService::class);
            $users = User::query()
                ->orderBy('name')
                ->get(['id', 'name', 'apelido']);

            foreach ($users as $user) {
                $payload = $journeyProgressService->buildForUser($user);
                $summary = $payload['dashboard_jornada_summary'] ?? [];
                $xpTotal = (int) ($summary['xp_total'] ?? 0);

                if ($xpTotal <= 0) {
                    continue;
                }

                $xpRows[] = [
                    'user_id' => $user->id,
                    'display_name' => $this->resolveRankingDisplayName($user),
                    'xp_total' => $xpTotal,
                ];
            }

            usort($xpRows, function (array $a, array $b): int {
                if (($a['xp_total'] ?? 0) !== ($b['xp_total'] ?? 0)) {
                    return ($b['xp_total'] ?? 0) <=> ($a['xp_total'] ?? 0);
                }

                return strcasecmp((string) ($a['display_name'] ?? ''), (string) ($b['display_name'] ?? ''));
            });

            $xpTotals['participants'] = count($xpRows);
            $xpTotals['xp_total'] = array_sum(array_map(fn ($row) => (int) ($row['xp_total'] ?? 0), $xpRows));
        }

        // Retorna a view com os dados
        return view('adm.ranking.ranking', compact('d', 'mes', 'dataInicio', 'dataFim', 'activeTab', 'xpRows', 'xpTotals'));
    }

    private function resolveRankingDisplayName(User $user): string
    {
        $apelido = trim((string) ($user->apelido ?? ''));
        if ($apelido !== '') {
            return $apelido;
        }

        $nome = trim((string) ($user->name ?? ''));
        if ($nome === '') {
            return 'Sem nome';
        }

        $partes = preg_split('/\s+/', $nome);
        return (string) ($partes[0] ?? $nome);
    }


    private function timestamp($data){
        // Converta para timestamp
        $timestamp = strtotime($data);

        // Converta para milissegundos
        $timestamp_ms = $timestamp * 1000;

        return $timestamp_ms;
    }


    public function update_dominio(Request $request)
    {
        // Validação inicial do formulário
        $request->validate([
            'dominio' => 'required|string|max:255',
        ]);

        $baseDomain = parse_url(config('app.url'), PHP_URL_HOST) ?: $request->getHost();
        $baseDomain = preg_replace('/^www\./', '', strtolower($baseDomain));

        // Aceita apenas o subdomínio e monta o domínio final com base no APP_URL
        $subdominio = strtolower(trim((string) $request->input('dominio')));
        $subdominio = preg_replace('/^https?:\/\//', '', $subdominio);
        $subdominio = preg_replace('/^www\./', '', $subdominio);

        $sufixoBase = '.' . $baseDomain;
        if (str_ends_with($subdominio, $sufixoBase)) {
            $subdominio = substr($subdominio, 0, -strlen($sufixoBase));
        }

        $subdominio = preg_replace('/[^a-z0-9]/', '', $subdominio);
        if (empty($subdominio)) {
            return response()->json(['errors' => ['dominio' => ['Informe um nome de site válido.']]], 422);
        }

        $dominio = $subdominio . '.' . $baseDomain;

        // Verificação manual se o domínio já existe para outro usuário
        $existingUser = User::where('dominio', $dominio)->where('id', '!=', Auth::id())->first();
        if ($existingUser) {
            return response()->json(['errors' => ['dominio' => ['Este domínio já está em uso por outro usuário.']]], 422);
        }

        // Atualizar os dados do usuário
        $user = Auth::user();
        $user->dominio = $dominio;
        $user->save();

        // Retornar resposta JSON
        return response()->json(['success' => 'Domínio alterado com sucesso!']);
    }

    public function update_whatsapp_atendimento(Request $request)
    {
        $validated = $request->validate([
            'whatsapp_id' => 'nullable|integer',
            'whatsapp' => [
                'required',
                'regex:/^[0-9]{12,14}$/',
            ],
            'is_active' => 'required|boolean',
        ]);

        $user = Auth::user();
        $whatsappId = (int) ($validated['whatsapp_id'] ?? 0);
        $registro = null;

        if ($whatsappId > 0) {
            $registro = WhatsappAtendimento::where('id', $whatsappId)
                ->where('user_id', $user->id)
                ->first();

            if (!$registro) {
                return response()->json(['errors' => ['whatsapp' => ['Registro de WhatsApp não encontrado.']]], 422);
            }
        }

        $whatsapp = preg_replace('/\D/', '', (string) $validated['whatsapp']);

        $duplicateQuery = WhatsappAtendimento::where('user_id', $user->id)
            ->where('whatsapp', $whatsapp);
        if ($registro) {
            $duplicateQuery->where('id', '!=', $registro->id);
        }

        if ($duplicateQuery->exists()) {
            return response()->json(['errors' => ['whatsapp' => ['Este WhatsApp já está cadastrado.']]], 422);
        }

        if (!$registro) {
            $registro = new WhatsappAtendimento();
            $registro->user_id = $user->id;
        }

        $registro->whatsapp = $whatsapp;
        $registro->is_active = (bool) $validated['is_active'];
        $registro->save();

        $this->syncWhatsappAtendimentoLegado($user);

        return response()->json(['success' => 'WhatsApp salvo com sucesso!']);
    }

    public function destroy_whatsapp_atendimento(int $id)
    {
        $user = Auth::user();
        $registro = WhatsappAtendimento::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$registro) {
            return response()->json(['errors' => ['whatsapp' => ['Registro de WhatsApp não encontrado.']]], 404);
        }

        $registro->delete();

        $this->syncWhatsappAtendimentoLegado($user);

        return response()->json(['success' => 'WhatsApp excluído com sucesso!']);
    }

    private function syncWhatsappAtendimentoLegado(User $user): void
    {
        $principal = $user->whatsappAtendimentos()
            ->where('is_active', true)
            ->orderByDesc('updated_at')
            ->first();

        $user->whatsapp_atendimento = $principal ? (string) $principal->whatsapp : null;

        if (Schema::hasColumn('users', 'w3_whatsapp_float_whatsapp_atendimento_id')) {
            $selectedFloatWhatsappId = (int) ($user->w3_whatsapp_float_whatsapp_atendimento_id ?? 0);

            if ($selectedFloatWhatsappId > 0) {
                $selectedStillValid = $user->whatsappAtendimentos()
                    ->where('id', $selectedFloatWhatsappId)
                    ->where('is_active', true)
                    ->exists();

                if (!$selectedStillValid) {
                    $user->w3_whatsapp_float_whatsapp_atendimento_id = null;
                }
            }
        }

        $user->save();
    }

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

    public function afiliado_configurar_site(Request $request, PhoneVerificationService $phoneVerificationService)
    {
        $user = Auth::user();

        //printf($request->input('formulario_whatsapp'));
       // exit;

       // Validação dos dados do formulário
    //    $request->validate([
    //     'formulario_whatsapp' => 'nullable|boolean',
    //     'formulario_pre_checkout' => 'nullable|boolean',
    //    ]);

        $request->validate([
            'telefone_pessoal_1' => 'nullable|regex:/^[0-9]{12,14}$/',
            'telefone_pessoal_2' => 'nullable|regex:/^[0-9]{12,14}$/',
            'w3_whatsapp_float_enabled' => 'nullable|boolean',
            'w3_whatsapp_float_delay_seconds' => 'nullable|in:0,5,10,20,30,45,60,120',
            'w3_whatsapp_float_channel' => [
                'nullable',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) use ($user) {
                    $value = trim((string) $value);

                    if ($value === '' || $value === 'rodizio') {
                        return;
                    }

                    if (!ctype_digit($value)) {
                        $fail('Selecione um WhatsApp válido para o botão flutuante.');
                        return;
                    }

                    if (!Schema::hasTable('whatsapp_atendimento')) {
                        $fail('Selecione um WhatsApp válido para o botão flutuante.');
                        return;
                    }

                    $isValid = WhatsappAtendimento::query()
                        ->where('id', (int) $value)
                        ->where('user_id', $user->id)
                        ->where('is_active', true)
                        ->exists();

                    if (!$isValid) {
                        $fail('Selecione um WhatsApp válido para o botão flutuante.');
                    }
                },
            ],
            'nome_empresa' => 'nullable|string|max:120',
            'logo_padrao' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:1024',
            'logo_dark' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:1024',
            'remove_logo_padrao' => 'nullable|boolean',
            'remove_logo_dark' => 'nullable|boolean',
        ]);

        // Atualizar os dados do usuário
        $requestedPhone1 = preg_replace('/\D/', '', (string) $request->input('telefone_pessoal_1'));
        $currentPhone1 = preg_replace('/\D/', '', (string) $user->telefone_pessoal_1);
        $shouldReverifyPhone1 = $requestedPhone1 !== '' && $requestedPhone1 !== $currentPhone1;

        $user->formulario_whatsapp = $request->has('formulario_whatsapp')
            ? $request->boolean('formulario_whatsapp')
            : (bool) ($user->formulario_whatsapp ?? true);
        $user->formulario_pre_checkout = $request->has('formulario_pre_checkout')
            ? $request->boolean('formulario_pre_checkout')
            : (bool) ($user->formulario_pre_checkout ?? true);
        if (!$shouldReverifyPhone1 && $requestedPhone1 !== '') {
            $user->telefone_pessoal_1 = $requestedPhone1;
        }
        $user->telefone_pessoal_2 = preg_replace('/\D/', '', (string) $request->input('telefone_pessoal_2')) ?: null;
        $user->apelido = $request->input('apelido')??null;
        $user->meta_pixel_id = $request->input('meta_pixel_id')??null;
        $user->meta_pixel_api = $request->input('meta_pixel_api')??null;
        $user->meta_pixel_eventcode = $request->input('meta_pixel_eventcode')??null;
        $user->meta_conta_anuncios_id = $request->input('meta_conta_anuncios_id')??null;
        $user->meta_pagina_id = $request->input('meta_pagina_id')??null;
        $user->meta_instagram_id = $request->input('meta_instagram_id')??null;
        $user->meta_app_id = $request->input('meta_app_id')??null;
        $user->dominio_externo = $this->normalizeDomain($request->input('dominio_externo'));
        $user->many_api = $request->input('many_api')??null;
        $user->many_cliente_telefone_id = $request->input('many_cliente_telefone_id')??null;
        if (Schema::hasColumn('users', 'botconversa_webhook')) {
            $user->botconversa_webhook = $request->input('botconversa_webhook')??null;
        }
        if (Schema::hasColumn('users', 'w3_whatsapp_float_enabled')) {
            $user->w3_whatsapp_float_enabled = $request->boolean('w3_whatsapp_float_enabled', true);
        }
        if (Schema::hasColumn('users', 'w3_whatsapp_float_delay_seconds')) {
            $user->w3_whatsapp_float_delay_seconds = (int) $request->input('w3_whatsapp_float_delay_seconds', 0);
        }
        if (Schema::hasColumn('users', 'w3_whatsapp_float_whatsapp_atendimento_id')) {
            $floatChannel = trim((string) $request->input('w3_whatsapp_float_channel', 'rodizio'));
            $user->w3_whatsapp_float_whatsapp_atendimento_id = ($floatChannel !== '' && $floatChannel !== 'rodizio')
                ? (int) $floatChannel
                : null;
        }

        if (Schema::hasColumn('users', 'nome_empresa')) {
            $nomeEmpresa = trim((string) $request->input('nome_empresa', ''));
            $user->nome_empresa = $nomeEmpresa !== '' ? $nomeEmpresa : null;
        }

        $removerLogoPadrao = $request->boolean('remove_logo_padrao', false);
        $removerLogoDark = $request->boolean('remove_logo_dark', false);

        if (Schema::hasColumn('users', 'logo_padrao_path')) {
            if ($request->hasFile('logo_padrao')) {
                $novoPath = $request->file('logo_padrao')->store('uploads/user-logos/' . $user->id, 'public');
                if (!empty($user->logo_padrao_path) && Storage::disk('public')->exists($user->logo_padrao_path)) {
                    Storage::disk('public')->delete($user->logo_padrao_path);
                }
                $user->logo_padrao_path = $novoPath;
            } elseif ($removerLogoPadrao) {
                if (!empty($user->logo_padrao_path) && Storage::disk('public')->exists($user->logo_padrao_path)) {
                    Storage::disk('public')->delete($user->logo_padrao_path);
                }
                $user->logo_padrao_path = null;
            }
        }

        if (Schema::hasColumn('users', 'logo_dark_path')) {
            if ($request->hasFile('logo_dark')) {
                $novoPath = $request->file('logo_dark')->store('uploads/user-logos/' . $user->id, 'public');
                if (!empty($user->logo_dark_path) && Storage::disk('public')->exists($user->logo_dark_path)) {
                    Storage::disk('public')->delete($user->logo_dark_path);
                }
                $user->logo_dark_path = $novoPath;
            } elseif ($removerLogoDark) {
                if (!empty($user->logo_dark_path) && Storage::disk('public')->exists($user->logo_dark_path)) {
                    Storage::disk('public')->delete($user->logo_dark_path);
                }
                $user->logo_dark_path = null;
            }
        }

        $user->save();

        if ($shouldReverifyPhone1) {
            $phoneVerificationService->start($user, $requestedPhone1);

            return redirect()
                ->route('phone.verification.notice')
                ->with('status', 'phone-code-sent');
        }

        // Retornar resposta JSON
        return redirect()->back()->with('success', "Configurações alteradas com sucesso!");
    }

    public function update_home_page_layout(Request $request)
    {
        $validated = $request->validate([
            'home_page_layout' => 'required|in:padrao,w3',
            'home_page_destination' => 'required|in:curso,whatsapp',
            'home_page_whatsapp_flow' => 'required_if:home_page_destination,whatsapp|in:formulario,direto',
        ]);

        $user = Auth::user();
        $user->home_page_layout = $validated['home_page_layout'];
        $user->home_page_destination = $validated['home_page_destination'];
        $user->home_page_whatsapp_flow = $validated['home_page_whatsapp_flow']
            ?? ($user->home_page_whatsapp_flow ?: 'formulario');
        $user->save();

        return response()->json([
            'success' => 'Página inicial atualizada com sucesso!',
            'home_page_layout' => $user->home_page_layout,
            'home_page_destination' => $user->home_page_destination,
            'home_page_whatsapp_flow' => $user->home_page_whatsapp_flow,
        ]);
    }
}
