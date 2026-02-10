<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

use App\Models\Lead;
use App\Models\User;
use App\Models\Curso;
use App\Models\PurchaseEvent;
use App\Models\Codigo_ref;
use App\Services\ManyChatService;


use App\Exports\TabelaExport;
use Maatwebsite\Excel\Facades\Excel;

use Carbon\Carbon;

use App\Http\Controllers\Meta_apiController;

use Illuminate\Support\Str;

use GuzzleHttp\Client;

use DOMDocument;
use DOMXPath;


class LeadsController extends Controller
{
    // Armazenar o lead no banco de dados
    public function novo_lead(Request $request)
    {

        Log::info('Início do processamento da requisição.', ['request_data' => $request->all()]);

        if ($request->input('user_id')) {
            Log::info('User ID recebido.', ['user_id' => $request->input('user_id')]);

            $user = User::find($request->input('user_id')) ?? null;
            Log::info('Usuário buscado.', ['user' => $user]);

            $ref = Codigo_ref::where('user_id', $user->id)
                ->where('curso_id', $request->input('curso_id'))
                ->first() ?? null;
            Log::info('Código de referência buscado.', ['ref' => $ref]);

        } else {
            Log::warning('Nenhum user_id recebido.');
            $user = false;
        }

        $telefone = preg_replace('/\D/', '', $request->input('telefone')) ?? null;
        Log::info('Telefone formatado.', ['telefone' => $telefone]);

        $curso = Curso::find($request->input('curso_id'));
        Log::info('Curso buscado.', ['curso' => $curso]);

        $data = [
            "id" => (string)Str::uuid(),
            "buyer_name" => $request->input('nome') ?? null,
            "buyer_checkout_phone" => $telefone ?? null,
            "product_id" => $curso->codigo_id_hotmart ?? null,
            "product_name" => $curso->titulo ?? null,
            "affiliate_code" => $ref->codigo_ref ?? null,
            "affiliate_name" => $user->name ?? null,
            "purchase_status" => $request->input('origem') ?? null,
            "transaction" => (string)Str::uuid(),
        ];
        Log::info('Dados preparados para cadastro.', ['data' => $data]);

        PurchaseEvent::updateOrCreate(
            ["buyer_checkout_phone" => $telefone],
            [
                "id" => (string)Str::uuid(),
                "buyer_name" => $request->input('nome') ?? null,
                "buyer_checkout_phone" => $telefone ?? null,
                "product_id" => $curso->codigo_id_hotmart ?? null,
                "product_name" => $curso->titulo ?? null,
                "affiliate_code" => $ref->codigo_ref ?? null,
                "affiliate_name" => $user->name ?? null,
                "purchase_status" => $request->input('origem') ?? null,
                "transaction" => (string)Str::uuid(),
            ]
        );
        Log::info('PurchaseEvent atualizado/criado com sucesso.', ['telefone' => $telefone]);
            
        

        
       
        //ENVIAR PARA O MANYCHAT E BOTCONVERSA
        $nome = explode(" ", $request->input('nome'));
        
           
            
        if($user AND !empty($user)){

            $dominio = $user->dominio_externo ?? $user->dominio;
            $afiliado_nome = $user->name ?? "";
            $afiliado_whatsapp_atendimento = (string)$user->whatsapp_atendimento ?? "";
            $curso_link_checkout = "https://go.hotmart.com/".$user->codigoRefPorCurso($curso->id)."?ap=$curso->codigo_afiliado_plano_completo";

            //FORMATAÇÃO DOS PREÇOS
            $preco_completo = $curso->preco_cheio_completo; 
            $preco_parcelado_completo = $curso->preco_parcelado_completo;
            
            $preco_basico = number_format((float)str_replace("R$", "", $curso->preco_cheio_completo) * 0.5, 2, ',', '');

            $formatar_preco_parcelado_basico = explode('R$', $curso->preco_parcelado_completo);
            $preco_parcelado_basico = $this->formatar_preco_parcelado(((float)$formatar_preco_parcelado_basico[1])*0.5, $formatar_preco_parcelado_basico[0]);
            
            $data = [
                "first_name"=> $nome[0] ?? "",
                "last_name"=>  $nome[1] ?? "",
                "phone"=> "55".$telefone ?? "",
                "whatsapp_phone"=> "55".$telefone ?? "",
                "cliente_telefone" =>"55".$telefone ?? "",
                'cidade' => $request->input('cidade') ?? "",
                "email"=> "",
                "gender"=> "string",
                "has_opt_in_sms"=> true,
                "has_opt_in_email"=> true,
                "consent_phrase"=> "string",
                "boleto_link" => "",
                "cliente_email" => "",
                "cliente_nome" => $request->input('nome'),
                
                "curso_nome" => $curso->titulo ?? "",
                "curso_link"  => "https://$dominio/".$curso->url ?? "",
                "curso_link_checkout" => $curso_link_checkout,
                "curso_area_membros" => $curso->link_area_membros ?? "",
                "curso_conteudo_principal" => (string)$this->topicos_whatsapp($curso->conteudo_principal)?? "",
                "curso_conteudo_bonus" => (string)$this->topicos_whatsapp($curso->conteudo_bonus)?? "",
                "curso_areas_de_atuacao" => (string)$this->areas_atuacao_whatsapp($curso->areas_de_atuacao)?? "",
                "curso_preco_completo" => (string)$preco_completo?? "",
                "curso_preco_basico" => (string)$preco_basico?? "",
                "curso_preco_parcelado_completo" => (string)$preco_parcelado_completo?? "",
                "curso_preco_parcelado_basico" => (string)$preco_parcelado_basico?? "",
                "curso_carga_horaria" => (string)$curso->horas_completo?? "",

                "home_page" => "https://$dominio/" ?? "",
                "hotmart_event" => "WHATSAPP",
                "pix_codigo" => "",
                "transacao_hp" => "55".$telefone ?? "",
                
                "many_api" => $user->many_api ?? "",
                "many_cliente_telefone_id" => (string)$user->many_cliente_telefone_id?? "",
                
                "afiliado_id" => "",                
                "afiliado_nome" => $afiliado_nome?? "",
                "afiliado_whatsapp_atendimento" => (string)$afiliado_whatsapp_atendimento?? "",
            ];

            if(isset($user->many_api) AND !empty($user->many_api) AND $user->id != "13"){
                $manychat = new ManyChatService($user->many_api);
                $manychat->many($data); 
            }


            //ENVIAR PARA O BOTCONVERSA
            if(isset($user->botconversa_webhook) AND !empty($user->botconversa_webhook)){
                $botconversa = $this->enviar_lead_bot_conversa($data, $user->botconversa_webhook);
                return json_encode($botconversa);
            }
        }
   

        exit;
        
        
    }

    public function curso_gratuito_lead(Request $request){ 
        
        //BUSCA USER_ID PELO DOMINIO
        $user = User::where('dominio', $request->getHost())
            ->orWhere('dominio_externo', $request->getHost()) 
            ->first();
         

        $dados = [];
        $dados['whatsapp'] = "55".preg_replace('/\D/', '', $request->whatsapp);
        if($user){$dados['user_id'] = $user['id'];}

        
        if ($request->filled('nome')) $dados['nome'] = $request->nome;
        if ($request->filled('cidade')) $dados['cidade'] = $request->cidade;
        if ($request->filled('curso')) $dados['curso'] = $request->curso;
        if ($request->filled('curso_id')) $dados['curso_id'] = $request->curso_id;
        if ($request->filled('evento_portal')) $dados['evento_portal'] = $request->evento_portal;
        if ($request->filled('origem')) $dados['origem'] = $request->origem;
        

        try {
            $resposta = Lead::updateOrCreate(
                ['whatsapp' => $dados['whatsapp']],
                $dados
            );

            if(!isset($user['id']) OR (isset($user['id']) AND $user['id']=='13')){
                //ENVIAR PARA MANYCHAT
                $curso = Curso::find($request->input('curso_id'));
                $dados['curso_nome'] = $curso->titulo;
                $dados['curso_link'] = !empty($curso->url) ? "https://jovemempreendedor.org/" . $curso->url : "";
                $dados['curso_area_membros'] = !empty($curso->link_area_membros) ? $curso->link_area_membros : "";
                $dados['curso_link_checkout'] = !empty($curso->link_checkout_completo) ? $curso->link_checkout_completo : "";
                $dados['curso_areas_de_atuacao'] = "";
                $dados['curso_conteudo_bonus'] = "";
                $dados['curso_conteudo_principal'] = "";
                $dados['curso_preco_completo'] = !empty($curso->preco_cheio_completo) ? $curso->preco_cheio_completo : "";
                $dados['curso_preco_basico'] = "";
                $dados['curso_preco_parcelado_completo'] = !empty($curso->preco_parcelado_completo) ? $curso->preco_parcelado_completo : "";
                $dados['curso_preco_parcelado_basico'] = "";
                $dados['curso_carga_horaria'] = !empty((string)$curso->horas_completo) ? (string)$curso->horas_completo : "";
                $dados['cliente_nome'] = $dados['nome'];
                $dados['curso_nome'] = $dados['curso'];
                $dados['whatsapp_phone'] = $dados['whatsapp'];
                $dados['phone'] = $dados['whatsapp'];
                $dados['cliente_telefone'] = $dados['whatsapp'];
                $dados['hotmart_event'] = $dados['evento_portal'];
               
                //$manychat = new ManyChatService("2250527:c0257f693d4b13e84e7d1c7340253ed2");
                //$manychat->many($dados);

                //ENVIAR MENSAGEM PELO APP.3F7.ORG
                $phone = $dados['whatsapp'];
                $curso_nome = $dados['curso'];
                $nomeCliente = $dados['nome'];
                $payload = [
                    "instance" => '177',
                    "data" => [
                        "key" => [
                            "remoteJid" => "{$phone}@s.whatsapp.net",
                            "fromMe" => false,
                        ],
                        "pushName" => "Bruno Sampaio",
                        "message" => [
                            "conversation" => "[Notificação do Sistema: Cadastro nas aulas gratuitas do curso {$curso_nome}. Cliente: {$nomeCliente}. Dê boas vindas, caso seja a primeira interação e inicie uma conversa de venda para o cliente ter acesso ao curso completo com todas as aulas.]",
                            "messageContextInfo" => [
                                "deviceListMetadata" => [
                                    "senderKeyHash" => "+RkkmtKA3xshYA==",
                                    "senderTimestamp" => "1753482615",
                                    "senderAccountType" => "E2EE",
                                    "receiverAccountType" => "E2EE",
                                    "recipientKeyHash" => "66VUYIModCrCxQ==",
                                    "recipientTimestamp" => "1753087838"
                                ],
                                "deviceListMetadataVersion" => 2,
                                "messageSecret" => "o3/+rLP8nDszLuY/UfeADgIBltdhovEV11l4ShB1uOQ="
                            ]
                        ],
                        "messageType" => "conversation",                        
                    ],
                    "server_url" => "https://evo.3f7.org",
                    "apikey" => "6336FB15-EFA5-454D-9044-4F3155AA3429"
                ];
                //$response = Http::post('https://app.3f7.org/api/conversation', $payload);

            }

            return response()->json(['status' => 'sucesso', 'lead' => $resposta]);

        } catch (\Exception $e) {
            Log::error('Erro ao salvar lead: '.$e->getMessage());

            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Erro ao processar os dados.',
                'erro' => json_encode($e)
            ], 500);
        }  
        
        return response()->json(['status' => $resposta]);

    }

    public function buscar_lead_whatsapp($whatsapp)
    {
        if (preg_match('/[a-zA-Z]/', $whatsapp)) {
            
            // Busca o registro mais recente e carrega os relacionamentos necessários
            $lead = PurchaseEvent::where('buyer_name', 'like', "%{$whatsapp}%")
            ->latest('created_at')
            ->with(['curso', 'codigo_ref.user'])
            ->first();
        
        } else {
                  // Remove os dois primeiros dígitos do número
            $whatsapp = substr($whatsapp, 2);

            // Busca o registro mais recente e carrega os relacionamentos necessários
            $lead = PurchaseEvent::where('buyer_checkout_phone', $whatsapp)
            ->latest('created_at')
            ->with(['curso', 'codigo_ref.user']) // Carrega os relacionamentos
            ->first();
        }


 

        // Verifica se encontrou um lead
        if (!$lead) {
            $lead = PurchaseEvent::where('purshase_status', 'whatsapp')
            ->latest('created_at')
            ->first();
            /*return response()->json([
                'success' => false,
                'message' => 'Nenhum lead encontrado'
            ], 404);*/
        }

        //FORMATAR DADDOS DO CURSO
        $preco_completo = $lead->curso->preco_cheio_completo ?? ""; // $curso->preco_cheio_completo $curso->preco_parcelado_completo
        $preco_parcelado_completo = $lead->curso->preco_parcelado_completo?? "";
        $preco_basico = number_format((float)str_replace("R$", "", $lead->curso->preco_cheio_completo) * 0.5, 2, ',', '')?? "";
        $formatar_preco_parcelado_basico = explode('R$', $lead->curso->preco_parcelado_completo)?? "";
        $preco_parcelado_basico = $this->formatar_preco_parcelado(((float)$formatar_preco_parcelado_basico[1])*0.5, $formatar_preco_parcelado_basico[0])?? "";

        $curso_conteudo_bonus = $this->topicos_whatsapp($lead->curso->conteudo_bonus);

        //DADDOS OS AFILIADO
        if($lead->codigo_ref?->user){
            $dominio = $lead->codigo_ref->user->dominio_externo ?? $lead->codigo_ref->user->dominio;
            $many_api = $lead->codigo_ref->user->many_api ?? null;
            $afiliado_id = (string)$lead->codigo_ref->user->id ?? "";
            $many_cliente_telefone_id =  $lead->codigo_ref->user->many_cliente_telefone_id ?? "";
            $afiliado_nome = $lead->codigo_ref->user->name ?? "";
            $afiliado_whatsapp_atendimento = (string)$lead->codigo_ref->user->whatsapp_atendimento ?? "";
            $curso_link_checkout = "https://go.hotmart.com/".$lead->codigo_ref->codigo_ref."?ap=".$lead->curso->codigo_afiliado_plano_completo;

            //return json_encode($curso_checkout_completo); exit;
        }else{
            $many_api = null;
            $dominio = "jovemempreendedor.org";
            $afiliado_id = "";
            $many_cliente_telefone_id = "";
            $afiliado_nome = "";
            $afiliado_whatsapp_atendimento = "";
            $curso_link_checkout =  $lead->curso->link_checkout_completo;
        }

        //dd($lead); exit;

        //STATUS HOTMART
        if(!$lead->event OR $lead->event=="2.0.0" OR $lead->event=="-"){
            if($lead->purchase_status =="approved") {
                $lead->event = "PURCHASE_APPROVED";
            }elseif($lead->purchase_status =="completed") {
                $lead->event = "PURCHASE_COMPLETE";
            }else{
                $lead->event = "WHATSAPP";
            }
        }


        //RETORNAR DADOS
        $data = [
            //"many_api" => (string)$many_api ?? "",
            //"many_cliente_telefone_id" => (string)$many_cliente_telefone_id,

            'cliente_email' => isset($lead->buyer_email) && !empty($lead->buyer_email) ? (string)$lead->buyer_email : "",
            'cliente_telefone' => isset($lead->buyer_checkout_phone) && !empty($lead->buyer_checkout_phone) ? (string)$lead->buyer_checkout_phone : "",
            'afiliado_id' => (string)$afiliado_id,
            'afiliado_nome' => (string)$afiliado_nome,
            'afiliado_whatsapp_atendimento' =>  (string)$afiliado_whatsapp_atendimento,
            'home_page' => (string)$dominio ?? "",
            'cidade' => "",
            'curso_nome' => isset($lead->product_name) && !empty($lead->product_name) ? (string)$lead->product_name : "",
            'boleto_link' => isset($lead->purchase_payment_billet_url) && !empty($lead->purchase_payment_billet_url) ? (string)$lead->purchase_payment_billet_url : "",
            'pix_codigo' => isset($lead->purchase_payment_pix_code) && !empty($lead->purchase_payment_pix_code) ? (string)$lead->purchase_payment_pix_code : "",
            'transacao_hp' => isset($lead->transaction) && !empty($lead->transaction) ? (string)$lead->transaction : "",
            'cancelamento_motivo' => isset($lead->purchase_payment_refusal_reason) && !empty($lead->purchase_payment_refusal_reason) ? (string)$lead->purchase_payment_refusal_reason : "",
            'hotmart_event' => $lead->event,
            'curso_link' => isset($lead->curso->url) && !empty($lead->curso->url) ? $dominio."/".(string)$lead->curso->url : "",
            'curso_area_membros' => isset($lead->curso->link_area_membros) && !empty($lead->curso->link_area_membros) ? (string)$lead->curso->link_area_membros : "",
            'curso_link_checkout' => $curso_link_checkout,
            "curso_conteudo_principal" => isset($lead->curso->conteudo_principal) && !empty($lead->curso->conteudo_principal)? (string)$this->topicos_whatsapp($lead->curso->conteudo_principal): "",
            "curso_conteudo_bonus" => !empty($curso_conteudo_bonus) ? (string)$curso_conteudo_bonus: "",
            "curso_areas_de_atuacao" => isset($lead->curso->areas_de_atuacao) && !empty($lead->curso->areas_de_atuacao)? (string)$this->areas_atuacao_whatsapp($lead->curso->areas_de_atuacao): "",
            'curso_preco_completo' => isset($preco_completo) && !empty($preco_completo) ? (string)$preco_completo : "",
            'curso_preco_basico' => isset($preco_basico) && !empty($preco_basico) ? (string)$preco_basico : "",
            'curso_preco_parcelado_completo' => isset($preco_parcelado_completo) && !empty($preco_parcelado_completo) ? (string)$preco_parcelado_completo : "",
            'curso_preco_parcelado_basico' => isset($preco_parcelado_basico) && !empty($preco_parcelado_basico) ? (string)$preco_parcelado_basico : "",
            'curso_carga_horaria' => isset($lead->curso->horas_completo) && !empty($lead->curso->horas_completo) ? (string)$lead->curso->horas_completo : "",
        ];
        


        //return json_encode($lead->curso->conteudo_bonus);



        // Retorna os dados no formato JSON
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function buscar_lead_whatsapp_post(Request $request)
    {
        $clienteNumero = $request->input('cliente_numero');

        $clienteNumero = str_replace("+", "", $clienteNumero);

        // Chama a função buscar_lead_whatsapp e captura o retorno
        $response = $this->buscar_lead_whatsapp($clienteNumero);

        // Decodifica o JSON para um array associativo
        $responseArray = json_decode($response->getContent(), true);

        // Verifica se "data" está presente e retorna apenas ele
        if (isset($responseArray['data'])) {
            return response()->json($responseArray['data']);
        }

        // Caso "data" não exista, retorna um erro ou a resposta original
        return response()->json(['error' => 'Dados não encontrados'], 400);
    }

    private function areas_atuacao_whatsapp($areas_atuacao)
    {
        // Divide a string original em um array com base nos delimitadores '/' ou ','.
        $array = preg_split('/[\/,]/', $areas_atuacao);
    
        // Formata o resultado
        $resultado = "";
        foreach ($array as $index => $topico) {
            if($index<7){
                $titulo = mb_convert_encoding(trim($topico), 'UTF-8', 'auto'); // Remove espaços extras
                $resultado .= '* ' . $titulo . "\n"; // Adiciona quebra de linha real (\n)
            }
        }
    
        return $resultado;
    }

    private function topicos_whatsapp($conteudo)
    {
        if (empty($conteudo)) {
            return null; // Valida entrada vazia
        }
    
        $html = mb_convert_encoding($conteudo, 'HTML-ENTITIES', 'UTF-8');
        $dom = new \DOMDocument();
    
        try {
            // Carrega o HTML e suprime erros
            @$dom->loadHTML($html);
        } catch (\Exception $e) {
            return null; // Caso falhe, retorne null
        }
    
        $listItems = $dom->getElementsByTagName('li');
        if (!$listItems->length) {
            return null; // Nenhum <li> encontrado
        }
    
        $courses = [];
        $currentCourse = null;
    
        foreach ($listItems as $item) {
            $text = trim($item->textContent); // Remove espaços desnecessários
            $class = $item->getAttribute('class') ?? '';
    
            if (strpos($class, 'ql-indent-1') === false) {
                // Novo curso
                if ($currentCourse) {
                    $courses[] = $currentCourse;
                }
                $currentCourse = ['title' => $text, 'topics' => []];
            } else {
                // Adiciona tópico ao curso atual
                if ($currentCourse) {
                    $currentCourse['topics'][] = $text;
                }
            }
        }
    
        // Adiciona o último curso, se existir
        if ($currentCourse) {
            $courses[] = $currentCourse;
        }
    
        // Formata o resultado
        $resultado = "";
        foreach ($courses as $index => $topico) {
            if($index<12){
                $titulo = mb_convert_encoding($topico['title'], 'UTF-8', 'auto');
                $resultado .= '* ' . $titulo . "\n"; // Adiciona quebra de linha real (\n)
            }
            
        }
    
        // Retorna o resultado formatado como JSON
        return $resultado;

    }

    private function formatar_preco_parcelado ($preco, $parcelamento){

        $dados['preco'] = number_format($preco, 2, ',', '');
        $dados['parcelamento'] = $parcelamento;

        if($dados['preco']=="9,50"){
            $dados['preco'] = "9,60";
        }

        if($dados['preco']=='5,76' AND $dados['parcelamento']==12){
            $dados['preco'] = "7,41";
            $dados['parcelamento'] = "9";
        }elseif($dados['preco']=='3,84'){
            $dados['preco'] = "7,15";
            $dados['parcelamento'] = "6";
        }

        return $dados['parcelamento'].$dados['preco'];

    }
    

    private function enviar_lead_bot_conversa($data, $link)
    {
        $response = Http::post($link, $data);
        return $response->json();

        /*if ($response->successful()) {
            return $response->json(); // Retorna o corpo da resposta como array
        } else {
            // Captura erro e pode fazer log ou retornar mensagem personalizada
            return [
                'error' => true,
                'status' => $response->status(),
                'message' => $response->body()
            ];
        }*/
    }

    private function enviar_lead_bot_conversa_teste($lead)
    {
        $client = new Client();
        $webhookUrl = 'https://new-backend.botconversa.com.br/api/v1/webhooks-automation/catch/114808/FT24xsPIwaqZ/'; // Substitua pela URL do seu webhook
        
        $lead['curso_checkout'] .= "&offDiscount=50OFF&src=wd";
        
        if($lead['curso_taxa_pix']){
            $lead['curso_taxa_pix'] = (int)(str_replace("R$", "", $lead['curso_taxa_pix'])); //$lead['curso_taxa_pix'] = "R$".((str_replace("R$", "", $lead['curso_taxa_pix']))*0.5);
            $lead['curso_taxa_pix'] = $lead['curso_taxa_pix']*0.5;
            $lead['curso_taxa_pix'] = "R$". $lead['curso_taxa_pix'];
        }

        try {
            $response = $client->post($webhookUrl, [
                'json' => [
                    'user_id' => $lead['user_id']?? null,
                    'curso_id' => $lead['curso_id']?? null,
                    'nome' => $lead['nome']?? null,
                    'telefone' => $lead['telefone']?? null,
                    'nome_curso' => $lead['nome_curso']?? null,
                    'cidade' => $lead['cidade']?? "Cidade",
                    'areas' => $lead['areas']?? "Escritórios, Lojas de varejo, Supermercados, Escolas, Hotéis, Empresas públicas e privadas",
                    'curso_descricao' => $lead['curso_descricao']?? null,
                    'curso_taxa_cartao' => $lead['curso_taxa_cartao'] ?? null,
                    'curso_taxa_pix' => $lead['curso_taxa_pix'] ?? null,
                    'curso_checkout' => $lead['curso_checkout']."&src=wd" ?? null,
                    'pagina_curso' => 'ead.portalje.org/'.$lead['url'] ?? null,
                    'curso_area_membros' => $lead['curso_area_membros'] ?? null,
                ]
            ]);

            // Verifica a resposta do webhook
            if ($response->getStatusCode() == 200) {
                // Webhook enviado com sucesso
                return true;
            } else {
                // Algo deu errado
                return false;
            }
        } catch (\Exception $e) {
            // Tratamento de erro
            \Log::error('Erro ao enviar webhook: ' . $e->getMessage());
            return $e->getMessage();
        }
    }

    public function hotmart_leads(Request $request)
    {      
        // Captura todos os parâmetros da query string
        $queryParams = $request->query();

        if(isset($queryParams['purchase_status'])){
            if($queryParams['purchase_status']== 'WAITING_PAYMENT'){$titulo_pagina = 'Aguardando Pagamento';}
            elseif($queryParams['purchase_status']== 'APPROVED'){$titulo_pagina = 'Vendas Realizadas';}
            elseif($queryParams['purchase_status']== 'COMPLETE'){$titulo_pagina = 'Vendas Realizadas';}
            elseif($queryParams['purchase_status']== 'CANCELLED'){$titulo_pagina = 'Cartao de Crédito Recusado';}
            elseif($queryParams['purchase_status']== 'EXPIRED'){$titulo_pagina = 'Prazo de pagamento vencido';}
            elseif($queryParams['purchase_status']== 'EXPIRED'){$titulo_pagina = 'Prazo de pagamento vencido';}
            else{$titulo_pagina = $queryParams['purchase_status'];}
        }else{
            $titulo_pagina = "Todos os Leads";
        } 
        
        $titulo_pagina = "<span class='badge bg-danger'>$titulo_pagina</span>";
        
        // Obtém o usuário autenticado
        $user = Auth::user();
        
        //PEGAR CÓDIGOS REF DO USER
        $codigo_ref = new Codigo_ref();
        $codigos_ref = $codigo_ref->where('user_id', $user->id)->pluck('codigo_ref');       

        // Inicia uma consulta no modelo PurchaseEvent
        $purchaseEvents = PurchaseEvent::query();
        $query = $purchaseEvents->whereIn('affiliate_code', $codigos_ref);

        // Itera sobre os parâmetros da query string e adiciona cláusulas where


        if ($request->filled('purchase_status')) {
            if ($request->input('purchase_status') == 'APPROVED') { //COMPRA APROVADA E COMPLETA
                $query->where(function($q) {
                    $q->where('purchase_status', 'APPROVED')
                      ->orWhere('purchase_status', 'COMPLETE');
                });
                $btn_status  = "Vendas";
            } elseif ($request->input('purchase_status') == 'WAITING_PAYMENT') { //AGUARDANDO PAGAMENTO PIX E BOLETO
                $query->where(function($q) {
                    $q->where('purchase_status', 'WAITING_PAYMENT')
                      ->orWhere('purchase_status', 'BILLET_PRINTED');
                });
                $btn_status  = "Aguardando Pagamento";
            } else {
                $query->where('purchase_status', $request->input('purchase_status'));
                $btn_status  = $request->input('purchase_status');
            }
        }else{
            $btn_status = "Todas as Origens";
        }
        

        if($request->filled('created_at') AND !$request->filled('date_fim') AND $request->input('created_at')!='todos'){
            $query->whereDate('created_at', $request->input('created_at'));
            $btn_data = $request->input('created_at');
        }else{
            $btn_data = null;
        }        

        if($request->filled('date_fim')){
            $btn_fim = $request->input('date_fim');
            if($request->filled('created_at')){
                $query->whereBetween('created_at', [$request->input('created_at'), $request->input('date_fim') ." 23:59:59"]);
                $btn_data = $request->input('created_at');
            }else{
                return redirect()->back()->withErrors(['error' => 'Selecione primeiro uma data de início']);
            }
        }else{
            $btn_fim = null;
        }
        
        if ($request->filled('atendimento')) {
            if ($request->input('atendimento') == 'aguardando') {
                $query->whereNull('atendimento');
                $btn_atendimento = "Aguardando Atendimento";
            }else {
                $query->where('atendimento', $request->input('atendimento'));
                $btn_atendimento = $request->input('atendimento') . "º Atendimento";
                if($request->input('atendimento') == 'arquivado'){$btn_atendimento = "Arquivados";}
            }
        } else {
            $btn_atendimento = "Todos os atendimentos";
        }
        
        if ($request->filled('purchase_payment_type')) {
            $query->where('purchase_payment_type', $request->input('purchase_payment_type'));
        }
        
        if ($request->filled('version') AND $request->input('version')=='2.0.0') {
            $query->where('version', $request->input('version'));
            $btn_leads_portal_hotmart = "Leads da Hotmart";
        }elseif ($request->filled('version') AND $request->input('version')=='Grupo_WhatsApp') {
            $query->where('version', $request->input('version'));
            $btn_leads_portal_hotmart = "Leads de Grupos";
        } else {
            $query->where(function($query) {
                $query->where('version', '<>', '2.0.0')->where('version', '<>', 'Grupo_WhatsApp')
                      ->orWhereNull('version')
                      ->orWhere('version', '');
            });
            $btn_leads_portal_hotmart = "Leads do Portal";
        }
        

        $data = $query->orderBy('created_at', 'desc')->paginate(150)->appends($request->query());     

        $hotmart_leads = $data;

        if (isset($queryParams['excel']) && !empty($queryParams['excel'])) {
            $export = new TabelaExport($data->items());
    
            return Excel::download($export, 'tabela.xlsx');
        }

        return view('dashboard.leads.index', compact('hotmart_leads', 'btn_leads_portal_hotmart', 'btn_atendimento', 'btn_data', 'btn_status', 'btn_fim'));
        // Retorna a view com os leads paginados
        //return view('adm.leads.leads', compact('hotmart_leads', 'btn_leads_portal_hotmart', 'btn_atendimento', 'btn_data', 'btn_status', 'btn_fim'));
    }

    public function alterar_atendimento(Request $request){

        // Decodifica o JSON enviado e verifica se não está vazio
        $leads = json_decode($request->input('selectedLeads'), true);
        $atendimento = $request->input('select_atendimento');
    
        // Itera sobre cada lead e faz o updateOrCreate
        foreach($leads as $lead){
            $purchaseEvent = PurchaseEvent::find($lead['id']);
            if ($purchaseEvent) {
                $purchaseEvent->update([
                    'atendimento' => $atendimento,
                ]);
            }
        };
        
        // Retorna uma resposta de sucesso
        return response()->json(['success' => 'Atendimento updated successfully']);
    }

    public function lead_grupo_zap($leads, $afiliado, $grupo_id){

        foreach($leads as $dado){

            $telefone = $this->lipar_numero_grupo_zap($dado['id']);
            
            PurchaseEvent::updateOrCreate(
                ['buyer_checkout_phone' => $telefone ?? null],
                [
                    "id" => (string)Str::uuid(),
                    "affiliate_name" => $afiliado['nome'] ?? null,
                    "affiliate_code" => $afiliado['ref'] ?? null,
                    "transaction" => (string)Str::uuid(),
                    "purchase_status" => $grupo_id??null,
                    "event" => 'Grupo_WhatsApp',
                    "version" => 'Grupo_WhatsApp',
                ]
            );
            
        }

        return true;
    }

    private function lipar_numero_grupo_zap($numero){

        if (preg_match('/55(\d{10,11})@/', $numero, $matches)) {
            $numero = $matches[1];
            return $numero;
        } else {
            return false;
        }
    }

    public function painel_leads_afiliado(Request $request){

        $user = Auth::user();

        $query = Lead::query();

         if ($request->filled('buscar')) {
            $busca = $request->input('buscar');
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%$busca%")
                ->orWhere('whatsapp', 'like', "%$busca%");
            });
        }else{
            $query->where('arquivar', $request->query('arquivar') ?? 0);
        }

        $query->where('user_id', $user->id);

        $btn_data = "";
        $btn_fim = "";

        // Filtro por data única
        if ($request->filled('created_at') && $request->input('created_at') != 'todos' && !$request->filled('date_fim')) {
            $btn_data = $request->input('created_at');
            $query->whereDate('created_at', $request->input('created_at'));
        }
       

        // Filtro por intervalo de datas
        if ($request->filled('date_fim')) {
            $btn_fim = $request->input('date_fim');
            if ($request->filled('created_at')) {
                $query->whereBetween('created_at', [
                    $request->input('created_at'),
                    $request->input('date_fim') . " 23:59:59"
                ]);
            } else {
                return redirect()->back()->withErrors(['error' => 'Selecione primeiro uma data de início']);
            }
        }


        // Paginar com os filtros aplicados
        $leads = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());


         // Retorna a view com os leads paginados
        return view('adm.leads.painel_leads_afiliado', compact('leads', 'btn_data', 'btn_fim'));

    }

    public function arquivar_lead($id)
    {
        $lead = Lead::findOrFail($id);
        $lead->arquivar = 1;
        $lead->save();

        return redirect()->back()->with('success', 'Lead arquivado com sucesso.');
    }

}
