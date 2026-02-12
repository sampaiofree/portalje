<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

use App\Http\Controllers\Home_e_cursosController;

use App\Models\PurchaseEvent;
use App\Models\WhatsappAtendimento;

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
        $cursos = new Home_e_cursosController();
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
        // Obtém o valor do mês enviado pelo GET
        $mes = $request->query('mes') ?? date('Y-m'); // formato esperado: 'Y-m'
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

        // Retorna a view com os dados
        return view('adm.ranking.ranking', compact('d', 'mes', 'dataInicio', 'dataFim'));
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

    public function afiliado_configurar_site(Request $request)
    {

        //printf($request->input('formulario_whatsapp'));
       // exit;

       // Validação dos dados do formulário
    //    $request->validate([
    //     'formulario_whatsapp' => 'nullable|boolean',
    //     'formulario_pre_checkout' => 'nullable|boolean',
    //    ]);

        // Atualizar os dados do usuário
        $user = Auth::user();
        $user->formulario_whatsapp = $request->input('formulario_whatsapp')??null;
        $user->formulario_pre_checkout = $request->input('formulario_pre_checkout')??null;
        $user->telefone_pessoal_1 = $request->input('telefone_pessoal_1')??null;
        $user->telefone_pessoal_2 = $request->input('telefone_pessoal_2')??null;
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
        $user->botconversa_webhook = $request->input('botconversa_webhook')??null;
        $user->save();

        // Retornar resposta JSON
        return redirect()->back()->with('success', "Configurações alteradas com sucesso!");
    }
}
