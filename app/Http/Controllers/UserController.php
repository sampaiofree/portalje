<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\User;

use App\Http\Controllers\Home_e_cursosController;

use App\Models\PurchaseEvent;

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
        
        return view('dashboard.dashboard', compact('cursos'));
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
        // Processamento do campo 'dominio'
        $dominio = $request->input('dominio');
        $dominio = preg_replace('/[^a-z0-9]/', '', strtolower($dominio));
        $dominio = str_replace(".portalje.org", "", $dominio);
        $dominio = $dominio . ".portalje.org";

        // Validação dos dados do formulário
        $request->validate([
            'dominio' => 'required|unique:users,dominio,' . Auth::id(),
        ]);

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
       // Validação dos dados do formulário
        $request->validate([
            'whatsapp_atendimento' => [
                'required',
                'regex:/^[0-9]{12,14}$/',
            ],
            'whatsapp_atendimento_tempo' => 'required',
        ]);

        // Atualizar os dados do usuário
        $user = Auth::user();
        $user->whatsapp_atendimento = $request->input('whatsapp_atendimento');
        $user->whatsapp_atendimento_tempo = $request->input('whatsapp_atendimento_tempo');
        $user->save();

        // Retornar resposta JSON
        return response()->json(['success' => 'WhatsApp alterado com sucesso!']);
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
        $user->dominio_externo = $request->input('dominio_externo')??null;
        $user->many_api = $request->input('many_api')??null;
        $user->many_cliente_telefone_id = $request->input('many_cliente_telefone_id')??null;
        $user->botconversa_webhook = $request->input('botconversa_webhook')??null;
        $user->save();

        // Retornar resposta JSON
        return redirect()->back()->with('success', "Configurações alteradas com sucesso!");
    }
}
