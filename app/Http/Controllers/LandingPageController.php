<?php

namespace App\Http\Controllers;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;


use App\Models\Lead; 
use App\Models\EmpresaParceira;
use App\Models\User;
use App\Models\Curso;

use App\Services\BotConversa;

use App\Http\Controllers\FacebookConversionsController;

class LandingPageController extends Controller
{
    
    public function profissao(Request $request, $cidade = null, $data = null, $whatsapp= null)
    {
        //DATA DA INSCRIÇÃO
        if ($data) { $data = Carbon::createFromFormat('d-m', $data)->translatedFormat('d \d\e F'); // Converte "31-05" para "31 de maio"
        } else { $data = Carbon::now()->translatedFormat('d \d\e F'); } /* Data de hoje sem o ano, ex: "25 de maio" */ 

        $user = User::where('dominio', $request->getHost())
                                ->orWhere('dominio_externo', $request->getHost())
                                ->first();
        if(isset($user->whatsapp)){$whatsapp = $user->whatsapp;} //Whatsapp do Afiliado da Página

        $cursos = Curso::where(function($q) {
                $q->where('publicado', 1)
                  ->orWhere('publicado', 'on');
            })
            ->where(function($q) {
                $q->where('mostrar_na_pagina', 1)
                  ->orWhere('mostrar_na_pagina', 'on');
            })
            ->get();


        $dados = [
            "cidade" => $cidade,
            "data" => $data,
            "whatsapp" => $whatsapp,
            'cursos' => $cursos
        ];
        

        return view('landign_pages.profissao', compact('dados'));
    }

    public function enviar_formulario(Request $request)
    {
            $dados = [];
           
            $dados['whatsapp'] = "55".preg_replace('/\D/', '', $request->whatsapp);

            $cursos = [
                'Informática' => $request->cert_informatica,
                'Como se comportar em uma reunião' => $request->cert_reuniao,
                'Inglês' => $request->cert_ingles,
                'Atendimento' => $request->cert_atendimento,
                'Gestao do Tempo e Organização' => $request->cert_gestao_tempo,
                'Trabalho em Equipe' => $request->cert_equipe,
            ];

            $cursos_interesse_array = [];
            $dados['cursos_feitos'] = "";
            $dados['cursos_nao_feitos'] = "";
            $dados['n_cursos_feitos'] = 0;
            $dados['n_cursos_nao_feitos'] = 0;

            foreach ($cursos as $curso => $resposta) {
                $respostaFinal = $resposta ?: 'Não';

                $cursos_interesse_array[] = "$curso: $respostaFinal";

                if ($respostaFinal === 'Sim') {
                    $dados['cursos_feitos'] .= "* $curso\n";
                    $dados['n_cursos_feitos']++;
                } else {
                    $dados['cursos_nao_feitos'] .= "* $curso\n";
                    $dados['n_cursos_nao_feitos']++;
                }
            }

            // String final para salvar no banco:
            $cursos_interesse = implode(', ', $cursos_interesse_array);


            //LINK DO CHECKOUT BASEADO NO NUMERO DE CURSOS 
            if($dados['n_cursos_nao_feitos']==1){
                $dados['link_checkout'] = "https://ead.portalje.org/e/1_curso";
                $dados['taxa'] = "R$39,40";
            }elseif($dados['n_cursos_nao_feitos']!=0){
                $dados['link_checkout'] = "https://ead.portalje.org/e/".$dados['n_cursos_nao_feitos']."_cursos";
                $dados['taxa'] = number_format(ceil(39.40 * $dados['n_cursos_nao_feitos'] * 100) / 100, 2, ',', ''); 
            }



            

            if ($request->filled('nome')) $dados['nome'] = $request->nome;
            if ($request->filled('idade')) $dados['idade'] = $request->idade;
            if ($request->filled('cidade')) $dados['cidade'] = $request->cidade;
            if ($request->filled('escolaridade')) $dados['escolaridade'] = $request->escolaridade;
            if (!empty($cursos_interesse)) $dados['cursos_interesse'] = $cursos_interesse;

            if ($request->filled('curso')) $dados['curso_id'] = $request->curso;

            if(isset($dados['curso_id'])) {
                $curso = Curso::where('id', $dados['curso_id'])->first();
                //return response()->json(['status' => $curso->titulo]);
                if(isset($curso->titulo)){
                    $dados['curso'] = $curso->titulo;
                    $dados['curso_pagina'] = $request->getHost()."/".$curso->url;
                }
            }
            
            if ($request->has('disponibilidade_online')) $dados['aceita_estudar_online'] = (int)$request->disponibilidade_online;
            if ($request->has('pode_pagar')) $dados['pode_pagar_inscricao'] = (int)$request->pode_pagar;
            if ($request->filled('perdeu_vaga')) $dados['perdeu_vaga'] = $request->perdeu_vaga;
            if ($request->filled('motivacao')) $dados['motivacao'] = $request->motivacao;
            if ($request->has('compartilhar_dados')) $dados['compartilhar_dados'] = $request->compartilhar_dados;
            if ($request->filled('melhor_horario')) $dados['melhor_horario'] = $request->melhor_horario;
            if ($request->filled('preferencia_contato')) $dados['preferencia_contato'] = $request->preferencia_contato;
            if ($request->filled('origem')) $dados['origem'] = $request->origem;



            //BUSCAR DADOS DO USUÁRIO
            $user = User::where('dominio', $request->getHost())
                                ->orWhere('dominio_externo', $request->getHost())
                                ->first();
            $dados['user_id'] = isset($user->id) ? $user->id : null ;                  
            $dados['evento_portal'] = "LP EMPREGO PARA TODOS";
            
            
            //ENVIAR PARA O BANCO DE DADOS
            $resposta = Lead::updateOrCreate(
                ['whatsapp' => $dados['whatsapp']],
                $dados
            );

            //ENVIAR DADOS PARA WEBHOOK BOTCONVERSA DO AFILIADO
            if(isset($user->botconversa_webhook) AND $user->botconversa_webhook){
                $botconversa_webhook = $user->botconversa_webhook;
            }

            //$botconversa_webhook = "https://new-backend.botconversa.com.br/api/v1/webhooks-automation/catch/114808/aPDZb0NywxN9/"; //temp
            
            if(isset($botconversa_webhook)){
                $resposta = Http::post($botconversa_webhook, $dados);
            }                    

       

            return response()->json(['status' => $resposta]);

    }

    public function enviar_formulario_empresa(Request $request)
    {

        $whatsappLimpo = preg_replace('/\D/', '', $request->whatsapp);

        $empresa = EmpresaParceira::create([
            'nome_empresa' => $request->nome_empresa,
            'nome_responsavel' => $request->nome_responsavel,
            'telefone_contato' => $whatsappLimpo,
            'cidade' => $request->cidade,
            'estado' => $request->estado,
            'informacoes_vagas' => $request->informacoes_vagas,
        ]);

    
    }

    public function novos_leads(Request $request)
    {
        //PEGAR ID DO USUÁRIO LOGADO
        $user = Auth::user();

        // 1. Determinar o status do filtro (0 = Novos, 1 = Arquivados)
        // Usamos (int) para garantir que o valor seja numérico (0 ou 1)
        $statusArquivado = (int) $request->query('status', 0);

        // 2. Iniciar a query builder com o filtro de status
        $query = Lead::where('arquivar', $statusArquivado)->where('user_id', $user->id);

        // 3. Aplicar filtros de data, se existirem
        if ($request->filled('data_inicial')) {
            $query->whereDate('created_at', '>=', $request->data_inicial);
        }
        if ($request->filled('data_final')) {
            $query->whereDate('created_at', '<=', $request->data_final);
        }

        // 4. Clonar a query para calcular as estatísticas ANTES da paginação
        $statsQuery = clone $query;

        $total = $statsQuery->count();
        $podePagar = (clone $statsQuery)->where('pode_pagar_inscricao', 1)->count();
        $temHorario = (clone $statsQuery)->whereNotNull('melhor_horario')->where('melhor_horario', '!=', '')->count();
        
        // 5. Ordenar e paginar os resultados
        // Usamos latest() para novos e oldest() para arquivados, para ver os mais antigos primeiro
        $query = ($statusArquivado == 1) ? $query->oldest() : $query->latest();
        $leads = $query->paginate(15)->withQueryString();

        // Percentuais
        $percentualPodePagar = $total > 0 ? number_format(($podePagar / $total) * 100, 1) : 0;
        $percentualTemHorario = $total > 0 ? number_format(($temHorario / $total) * 100, 1) : 0;

         return view('dashboard.leads.aulasgratuitas', compact(
            'leads',
            'total',
            'podePagar',
            'temHorario',
            'percentualPodePagar',
            'percentualTemHorario',
            'statusArquivado' // Passamos o status para a view
        ));
        return view('adm.leads.leads_novo', compact(
            'leads',
            'total',
            'podePagar',
            'temHorario',
            'percentualPodePagar',
            'percentualTemHorario',
            'statusArquivado' // Passamos o status para a view
        )); 
    }

    public function arquivar($id)
    {
        $lead = Lead::findOrFail($id);
        $lead->arquivar = 1;
        $lead->save();

        return redirect()->back()->with('success', 'Lead arquivado com sucesso.');
    }

     public function desarquivar(Lead $lead)
    {
        $lead->update(['arquivar' => 0]);
        return redirect()->back()->with('success', 'Lead restaurado com sucesso!');
    }

}
