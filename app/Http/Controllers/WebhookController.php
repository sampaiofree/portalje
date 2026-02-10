<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\ManyChatService;
use App\Models\Curso;
use App\Models\Codigo_ref;
use App\Http\Controllers\LeadsController;

class WebhookController extends Controller
{
    protected $many_access_token;

    public function handle(Request $request)
    {

        
        
        try {
            // Log the incoming request for debugging
            //Log::info('Webhook received', ['payload' => $request->all()]);

            // Validação básica
            $validatedData = $request->all();

            //return response()->json(['message' => json_encode($validatedData)], 200);

            // Extracting data from the JSON payload
            $data = $validatedData['data']?? null;
            $product = $data['product']?? null;
            $buyer = $data['buyer']?? null;
            $producer = $data['producer']?? null;
            $commissions = $data['commissions']?? null;
            $purchase = $data['purchase']?? null;
            $subscription = $data['subscription'] ?? null;
            $affiliates = $data['affiliates'][0] ?? null; // Assuming single affiliate for simplicity
            $nomeCliente = $buyer['name'] ?? null;
            $nomeCurso = $product['name'] ?? null;

            //NÃO RECEBER DADOS DE CERTOS PRODUTOS
            //5951365 - Guia e Roteiro Coreia do Sul
            if(isset($product['id']) AND $product['id']=='5951365'){return false;}

            echo "1";

            // Update or create the purchase event
            $purchaseEvent = PurchaseEvent::updateOrCreate(
                ['transaction' => $purchase['transaction'], 'buyer_checkout_phone' => $buyer['checkout_phone'] ?? null],
                [
                    'id' => $validatedData['id'],
                    'creation_date' => $validatedData['creation_date']?? null,
                    'event' => $validatedData['event']?? null,
                    'version' => $validatedData['version']?? null,
                    'product_id' => $product['id'] ?? null,
                    'product_ucode' => $product['ucode'] ?? null,
                    'product_name' => $product['name'] ?? null,
                    'product_has_co_production' => $product['has_co_production'] ?? null,
                    'buyer_email' => $buyer['email'] ?? null,
                    'buyer_name' => $buyer['name'] ?? null,
                    'buyer_document' => $buyer['document'] ?? null,
                    'buyer_address_zipcode' => $buyer['address']['zipcode'] ?? null,
                    'buyer_address_country' => $buyer['address']['country'] ?? null,
                    'buyer_address_number' => $buyer['address']['number'] ?? null,
                    'buyer_address_address' => $buyer['address']['address'] ?? null,
                    'buyer_address_city' => $buyer['address']['city'] ?? null,
                    'buyer_address_state' => $buyer['address']['state'] ?? null,
                    'buyer_address_neighborhood' => $buyer['address']['neighborhood'] ?? null,
                    'buyer_address_complement' => $buyer['address']['complement'] ?? null,
                    'buyer_address_country_iso' => $buyer['address']['country_iso'] ?? null,
                    'producer_name' => $producer['name'] ?? null,
                    'commission_marketplace_value' => $commissions[0]['value'] ?? null,
                    'commission_marketplace_currency' => $commissions[0]['currency_value'] ?? null,
                    'commission_producer_value' => $commissions[1]['value'] ?? null,
                    'commission_producer_currency' => $commissions[1]['currency_value'] ?? null,
                    'commission_producer_converted_value' => $commissions[1]['currency_conversion']['converted_value'] ?? null,
                    'commission_producer_converted_currency' => $commissions[1]['currency_conversion']['converted_to_currency'] ?? null,
                    'commission_producer_conversion_rate' => $commissions[1]['currency_conversion']['conversion_rate'] ?? null,
                    'purchase_approved_date' => $purchase['approved_date'] ?? null,
                    'purchase_full_price_value' => $purchase['full_price']['value'] ?? null,
                    'purchase_full_price_currency' => $purchase['full_price']['currency_value'] ?? null,
                    'purchase_original_offer_price_value' => $purchase['original_offer_price']['value'] ?? null,
                    'purchase_original_offer_price_currency' => $purchase['original_offer_price']['currency_value'] ?? null,
                    'purchase_price_value' => $purchase['price']['value'] ?? null,
                    'purchase_price_currency' => $purchase['price']['currency_value'] ?? null,
                    'purchase_offer_code' => $purchase['offer']['code'] ?? null,
                    'purchase_recurrence_number' => $purchase['recurrence_number'] ?? null,
                    'purchase_subscription_anticipation_purchase' => $purchase['subscription_anticipation_purchase'] ?? null,
                    'purchase_checkout_country_name' => $purchase['checkout_country']['name'] ?? null,
                    'purchase_checkout_country_iso' => $purchase['checkout_country']['iso'] ?? null,
                    'purchase_origin_xcod' => $purchase['origin']['xcod'] ?? null,
                    'purchase_order_bump' => $purchase['order_bump']['is_order_bump'] ?? null,
                    'purchase_order_bump_parent_transaction' => $purchase['order_bump']['parent_purchase_transaction'] ?? null,
                    'purchase_order_date' => $purchase['order_date'] ?? null,
                    'purchase_date_next_charge' => $purchase['date_next_charge'] ?? null,
                    'purchase_status' => $purchase['status'] ?? null,
                    'transaction' => $purchase['transaction'],
                    'purchase_payment_billet_barcode' => $purchase['payment']['billet_barcode'] ?? null,
                    'purchase_payment_billet_url' => $purchase['payment']['billet_url'] ?? null,
                    'purchase_payment_installments_number' => $purchase['payment']['installments_number'] ?? null,
                    'purchase_payment_pix_code' => $purchase['payment']['pix_code'] ?? null,
                    'purchase_payment_pix_expiration_date' => $purchase['payment']['pix_expiration_date'] ?? null,
                    'purchase_payment_pix_qrcode' => $purchase['payment']['pix_qrcode'] ?? null,
                    'purchase_payment_refusal_reason' => $purchase['payment']['refusal_reason'] ?? null,
                    'purchase_payment_type' => $purchase['payment']['type'] ?? null,
                    'subscription_status' => $subscription['status'] ?? null,
                    'subscription_plan_id' => $subscription['plan']['id'] ?? null,
                    'subscription_plan_name' => $subscription['plan']['name'] ?? null,
                    'subscription_subscriber_code' => $subscription['subscriber']['code'] ?? null,
                    'affiliate_code' => $affiliates['affiliate_code'] ?? null,
                    'affiliate_name' => $affiliates['name'] ?? null,
                ]
            );

            echo "2";

            //EVENTO HOTMART
            if($validatedData['event'] and $validatedData['event']=="PURCHASE_BILLET_PRINTED"){
                $hotmart_event = $purchase['payment']['type'] ?? null;
            }else{
                $hotmart_event = $validatedData['event'] ?? null;
            }

            echo "3";

            //BUSCAR DADOS DO CURSO
            $curso = Curso::where('codigo_id_hotmart', $product['id'])->first();

            echo "4";

            if ($curso) {
                echo "5";
                $curso_link = !empty($curso->url) ? "https://jovemempreendedor.org/" . $curso->url : "";
                $curso_area_membros = !empty($curso->link_area_membros) ? $curso->link_area_membros : "";
                $curso_link_checkout = !empty($curso->link_checkout_completo) ? $curso->link_checkout_completo : "";
                $conteudo_principal = !empty((string)$this->topicos_whatsapp($curso->conteudo_principal)) ? (string)$this->topicos_whatsapp($curso->conteudo_principal) : "";
                $conteudo_bonus = !empty((string)$this->topicos_whatsapp($curso->conteudo_bonus)) ? (string)$this->topicos_whatsapp($curso->conteudo_bonus) : "";
                $areas_de_atuacao = !empty((string)$this->areas_atuacao_whatsapp($curso->areas_de_atuacao)) ? (string)$this->areas_atuacao_whatsapp($curso->areas_de_atuacao) : "";
                $preco_completo = !empty($curso->preco_cheio_completo) ? $curso->preco_cheio_completo : "";
                $preco_parcelado_completo = !empty($curso->preco_parcelado_completo) ? $curso->preco_parcelado_completo : "";
                $horas_completo = !empty((string)$curso->horas_completo) ? (string)$curso->horas_completo : "";

                //FORMATAR PREÇO BÁSICO
                $formatar_preco_parcelado_basico = !empty($curso->preco_parcelado_completo) ? explode('R$', $curso->preco_parcelado_completo) : [""];
                $preco_parcelado_basico = !empty($formatar_preco_parcelado_basico[1]) ? $this->formatar_preco_parcelado(((float)$formatar_preco_parcelado_basico[1]) * 0.5, $formatar_preco_parcelado_basico[0]) : "";
                $preco_basico = !empty($curso->preco_cheio_completo) ? number_format((float)str_replace("R$", "", $curso->preco_cheio_completo) * 0.5, 2, ',', '') : "";
                

            } else {
                echo "6";
                $curso_link = "Curso não encontrado";
                $curso_area_membros = "Curso não encontrado";
                $curso_link_checkout = "Curso não encontrado";
                $conteudo_principal = "Curso não encontrado";
                $conteudo_bonus = "Curso não encontrado";
                $areas_de_atuacao = "Curso não encontrado";
                $preco_completo = "Curso não encontrado";
                $preco_parcelado_completo = "Curso não encontrado";
                $preco_basico = "Curso não encontrado";
                $preco_parcelado_basico = "Curso não encontrado";
                $horas_completo = "Curso não encontrado";
            }

            //BUSCAR DADOS DO AFILIADO
            if(isset($affiliates['affiliate_code'])){
                echo "7";
                $afiliado = Codigo_ref::where('codigo_ref', $affiliates['affiliate_code'])->first();
            }

            
            if(isset($afiliado)){
                echo "8";
                //return json_encode($afiliado->user); exit;
                $afiliado_nome = $afiliado->user->name ?? "";
                $afiliado_id = $afiliado->user->id?? "";
                $afiliado_whatsapp_atendimento = $afiliado->user->whatsapp_atendimento?? "";
                $homepage =  $afiliado->user->dominio?? "";
            }else{
                echo "9";
                $afiliado_nome = "";
                $afiliado_id = "";
                $afiliado_whatsapp_atendimento = "";
                $homepage =  "";
                
            }
            

            //FORMATAR TELEFONE
            if(!isset($buyer['checkout_phone'])){
                return response()->json(['message' => 'checkout_phone não enviado'], 200);
            }
            
            if(strlen($buyer['checkout_phone']) < 12) //244948469781 556295772922
            {
                echo "10";
                $phone = "55".$buyer['checkout_phone'] ?? "";

            }else{
                echo "11";
                $phone = $buyer['checkout_phone'] ?? "";
            }

            echo "12";

            //ENVIAR PARA O MANYCHAT
            $nome = explode(" ", $buyer['name']);
            
            $data = [
                "first_name"=> $nome[0] ?? "",
                "last_name"=>  $nome[1] ?? "",
                "phone"=> $phone,
                "whatsapp_phone"=> $phone,
                "email"=> $buyer['email'] ?? "",
                "gender"=> "string",
                "has_opt_in_sms"=> true,
                "has_opt_in_email"=> true,
                "consent_phrase"=> "string",
                "boleto_link" => $purchase['payment']['billet_url'] ?? "",

                "home_page" => $homepage ?? "",
                "cidade" => "",

                //DADOS DO AFILIADO
                "afiliado_id" => $afiliado_id ?? "",
                "afiliado_nome" => $afiliado_nome ?? "",
                "afiliado_whatsapp_atendimento" =>  $afiliado_whatsapp_atendimento ?? "",
                
                "cliente_email" => $buyer['email'] ?? "",
                "cliente_telefone" => $phone,
                "cliente_nome" =>  $buyer['name'] ?? "",
                
                "curso_nome" => $product['name'] ?? "",
                "curso_link"  => $curso_link , 
                "curso_area_membros" => $curso_area_membros,
                "curso_link_checkout" => $curso_link_checkout,
                "curso_conteudo_principal" => $conteudo_principal ,
                "curso_conteudo_bonus" => $conteudo_bonus,
                "curso_areas_de_atuacao" => $areas_de_atuacao,
                "curso_preco_completo" => (string)$preco_completo,
                "curso_preco_basico" => (string)$preco_basico,
                "curso_preco_parcelado_completo" => (string)$preco_parcelado_completo,
                "curso_preco_parcelado_basico" => (string)$preco_parcelado_basico,
                "curso_carga_horaria" => $horas_completo,
                
                //DADOS DA HOTMART
                "pix_codigo" => $purchase['payment']['pix_code'] ?? "",
                "transacao_hp" => $purchase['transaction']?? "",
                "hotmart_event" => $hotmart_event?? "",
                "cancelamento_motivo" => $purchase['payment']['refusal_reason'] ?? "",
               
            ];
            echo "13";

            //$manychat = new ManyChatService("2250527:c0257f693d4b13e84e7d1c7340253ed2");
            
            //dd($manychat); exit;
            
            //return $manychat->many($data);

            //ENVIAR PARA 3F7 EVOLUTION
            $payload = [
                    "instance" => '283',
                    "data" => [
                        "key" => [
                            "remoteJid" => "{$phone}@s.whatsapp.net",
                            "fromMe" => false,
                        ],
                        "pushName" => "Bruno Sampaio",
                        "message" => [
                            "conversation" => "[Notificação de transação da Hotmart:{$hotmart_event}. Cliente: {$nomeCliente}. Dê boas vindas, caso seja a primeira interação e inicie uma conversa de acordo com a transação][Demais informações do Webhook da Hotmart: ".json_encode($purchaseEvent)."]",
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


            //VERIFICAR SE JA EXISTE REGISTRO COM O MESMO EVENTO PARA NÃO REPETIR ENVIO
            $existe = PurchaseEvent::where('transaction', $purchase['transaction'])->where('event', $validatedData['event'])->exists(); // Retorna true ou false

            /*if($hotmart_event!="PURCHASE_REFUNDED" 
            AND $hotmart_event!="PURCHASE_REFUNDED"
            AND $hotmart_event!="PURCHASE_DELAYED"
            AND $hotmart_event!="PURCHASE_CHARGEBACK"
            AND $hotmart_event!="PURCHASE_PROTEST"
            AND $hotmart_event!="PURCHASE_COMPLETE"
            AND !$existe
            ){
                $response = Http::post('https://app.3f7.org/api/conversation', $payload);
                return $response;
            }*/
            
            return true;

            return response()->json(['message' => 'Success'], 200);
        } catch (\Exception $e) {
            Log::error('Webhook handling error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Error processing webhook',
                'error' => $e->getMessage(),
                'trace' => $e->getTrace()
            ], 500);
        }
    }

    private function montar_msg_3f7($purchaseEvent){

        if($purchaseEvent=="PURCHASE_APPROVED"){

        }

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
            if($index<7){
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
    

    /*public function many($dados=null){

        //VERIFICAR SE JÁ EXISTE USUÁRIO
        $usuario = $this->manygetid($dados['email']);
        //$this->many_access_token="2264460:4b9d8b170e1b285230c911a58c4c7c99";  
        //$usuario = $this->manygetid('74698N2320');
        //dd($usuario); exit;

        if($usuario){
            $this->manyFiel($usuario, $dados); //USUARIO EXISTE ENTÃO APENAS ATUALIZAR DADOS
            
            //COLOCAR TAGS
            $id = $usuario;
            $produtos = explode(";", $dados['curso_nome']);
                foreach($produtos as $produto){
                    $tag = $dados['hotmart_event']." - ".$produto;
                    $this->manyCriarTag($tag);
                    $this->manySetTag($tag, $id);
                }
            return true;
        }else{

            //USUARIO NÃO EXISTE VAMOS CRIAR O USUARIO
            // Token de acesso para a API do ManyChat
            $accessToken = $this->many_access_token;

            // Endpoint da API
            $url = 'https://api.manychat.com/fb/subscriber/createSubscriber';

            // Dados do contato
            $data = [
                "first_name"=> $dados['first_name'],
                "last_name"=> $dados['last_name'],
                "phone"=> $dados['phone'],
                "whatsapp_phone"=> $dados['whatsapp_phone'],
                "email"=> $dados['email'],
                "gender"=> "string",
                "has_opt_in_sms"=> true,
                "has_opt_in_email"=> true,
                "consent_phrase"=> "string",
            ];

            // Faz a requisição para a API do ManyChat
            $response = Http::withToken($accessToken)
                            ->post($url, $data);

            // Verifica e retorna a resposta
            if ($response->successful()) {

                $id  = $response->json();
                $id = $id['data']['id']; //ID DO USUÁRIO CADASTRADO

                $this->manyFiel($id, $dados); //ALTERAR CAMPOS DO USUÁRIO

                //CRIAR AS TAGS
                $produtos = explode(";", $dados['curso_nome']);
                foreach($produtos as $produto){
                    $tag = $dados['hotmart_event']." - ".$produto;
                    $this->manyCriarTag($tag);
                    $this->manySetTag($tag, $id);
                }

                return true;

                
            } else {
                // Trata erros
                return response()->json([
                    'message' => 'Erro ao criar o contato!',
                    'message2' => json_encode($data),
                    'error' => $response->json()
                ], $response->status());
            }
        }
    }*/

    public function manyFiel($id = null, $dados = null){
        // Token de acesso para a API do ManyChat
        $accessToken = $this->many_access_token;

        // Endpoint da API
        $url = 'https://api.manychat.com/fb/subscriber/setCustomFields';
       

        // Dados do contato
        $data = [
            "subscriber_id"=> (int)$id,
            "fields" => [
                [
                    "field_name" => "boleto_link",
                    "field_value" => $dados['boleto_link']
                ],
                [
                    "field_name" => "cliente_email",
                    "field_value" => $dados['cliente_email']
                ],
                [
                    "field_name" => "curso_nome",
                    "field_value" => $dados['curso_nome']
                ],                
                [
                    "field_name" => "hotmart_event",
                    "field_value" => $dados['hotmart_event']
                ],
                [
                    "field_name" => "pix_codigo",
                    "field_value" => $dados['pix_codigo']
                ],
                [
                    "field_name" => "transacao_hp",
                    "field_value" => $dados['transacao_hp']
                ]
            ]
            // Inclua outros dados necessários aqui
        ];

        // Faz a requisição para a API do ManyChat
        $response = Http::withToken($accessToken)
                        ->post($url, $data);

        // Verifica e retorna a resposta
        if ($response->successful()) {
            return response()->json([
                'message' => 'Campo alterado!',
                'data' => $response->json()
            ]);
        } else {
            // Trata erros
            return response()->json([
                'message' => 'Erro ao ao alterar o campo!',
                'message2' => json_encode($data),
                'error' => $response->json()
            ], $response->status());
        }
    }

    public function manygetid($email=null){
        ///fb/subscriber/findByCustomField
        // Endpoint e parâmetros, conforme definido em seu comando curl
        $url = 'https://api.manychat.com/fb/subscriber/findByCustomField';
        $fieldId = '12328762'; // cliente_email
        $fieldValue = $email; 
        $accessToken = $this->many_access_token;

        // Faz a requisição GET
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get($url, [
            'field_id' => $fieldId,
            'field_value' => $fieldValue
        ]);

        // Verifica o sucesso da resposta e retorna ou lida com um erro
        if ($response->successful()) {
            
            $data = $response->json();

            // Acessar o ID do primeiro elemento em 'data'
            if (!empty($data['data']) && isset($data['data'][0]['id'])) {
                return $data['data'][0]['id'];
            } else {
                return null;
            }

        } else {
            return response()->json([
                'message' => 'Erro na requisição!',
                'error' => $response->json(),
            ], $response->status());
        }
    }

    public function manyCriarTag($tagNome){
        $accessToken = $this->many_access_token;

        // Endpoint da API
        $url = 'https://api.manychat.com/fb/page/createTag';

        // Dados do contato
        $data = [
            "name"=> $tagNome,
        ];

        // Faz a requisição para a API do ManyChat
        $response = Http::withToken($accessToken)
                        ->post($url, $data);

        // Verifica e retorna a resposta
        /*if ($response->successful()) {
            return true;
            return response()->json([
                'message' => 'Campo alterado!',
                'data' => $response->json()
            ]);
        } else {
            return null;
            // Trata erros
            return response()->json([
                'message' => 'Erro ao ao alterar o campo!',
                'error' => $response->json()
            ], $response->status());
        }*/

    }

    public function manySetTag($tagNome, $userID){
        $accessToken = $this->many_access_token;

        // Endpoint da API
        $url = 'https://api.manychat.com/fb/subscriber/addTagByName';

        // Dados do contato
        $data = [
            "subscriber_id"=> $userID,
            "tag_name" => $tagNome
        ];

        // Faz a requisição para a API do ManyChat
        $response = Http::withToken($accessToken)
                        ->post($url, $data);

        // Verifica e retorna a resposta
        /*if ($response->successful()) {
            return true;
            return response()->json([
                'message' => 'Campo alterado!',
                'data' => $response->json()
            ]);
        } else {
            return null;
            // Trata erros
            return response()->json([
                'message' => 'Erro ao ao alterar o campo!',
                'error' => $response->json()
            ], $response->status());
        }*/

    }
}
