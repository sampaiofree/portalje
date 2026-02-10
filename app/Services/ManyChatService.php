<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use PhpParser\Node\Stmt\Return_;

class ManyChatService
{
    protected $many_access_token;

    public function __construct($many_access_token)
    {
        $this->many_access_token = $many_access_token;
    }

 
    public function many($dados=null){  //CADASTRAR DADOS DO USUÁRIO

        //VERIFICAR SE JÁ EXISTE USUÁRIO
        //$usuario = $this->manygetid($dados['email']);
        
        //cliente_telefone 


        $transacao_hp = $dados['transacao_hp'] ?? null;
        $phone = $dados['phone'] ?? null;
        $cliente_email = $dados['cliente_email'] ?? null;

        if(isset($dados['cliente_nome'])){
            $nome = explode(" ", $dados['cliente_nome']);
            $dados['first_name'] = $nome[0];
            $dados['last_name'] = $nome[1] ?? null;
        }
       
        if(isset($dados['many_cliente_telefone_id']) AND $dados['many_cliente_telefone_id']){
            $usuario = $this->manygetid($dados['many_cliente_telefone_id'], $dados['cliente_telefone']); 
        }else{
            
            
            $usuario = $this->manygetid('12328761', $transacao_hp); 
            if(!$usuario){ $usuario = $this->manygetid('12347345', $phone);} 
            if(!$usuario){ $usuario = $this->manygetid('12328762', $cliente_email);} 
            if(!$usuario){ $usuario = $this->many_buscar_campo_sistema($cliente_email);}

            //$usuario = $this->many_buscar_campo_sistema($dados['cliente_email']);
            //return $usuario;
        }
        

        if($usuario){
            $id = $usuario['id'];
            
            //COLOCAR TAGS
            $atualizar_campos = false;
            
            $tags = $usuario['tags'];
            
            if(isset($dados['gpt_reposta'])){
                $atualizar_campos = true;
            }
            
            if(isset($dados['curso_nome'])){
                $produtos = explode(";", $dados['curso_nome']);
                foreach($produtos as $produto){
                    $tagToFind = $dados['hotmart_event']." - ".$produto;

                    $tagFound = array_filter($tags, function($tag) use ($tagToFind) {
                        return $tag['name'] === $tagToFind;
                    }); 

                    // Verificando se a tag foi encontrada
                    if (!empty($tagFound)) {
                        // array_filter retorna um array, então podemos acessar o primeiro item
                        $tagFound = reset($tagFound);
                        //return "Tag encontrada: ";
                    } else {
                        //Tag não encontrada - entao criar e setar o usuario com a tag
                        $this->manyCriarTag($tagToFind);
                        $this->manySetTag($tagToFind, $id);
                        $atualizar_campos = true;
                    }
                }
            }
            
            
            if($atualizar_campos){
                $this->manyFiel($id, $dados); //ALTERAR CAMPOS DO USUÁRIO  
                //return $resposta;  
                return "Usuário já existente, campos atualizados";
            }else{
                return "Usuário já existente, campos não atualizados";
            }

            
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
                "email"=> $dados['email'] ?? "",
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
                if(isset($dados['curso_nome'])){
                    $produtos = explode(";", $dados['curso_nome']);
                    foreach($produtos as $produto){
                        $tag = $dados['hotmart_event']." - ".$produto;
                        $this->manyCriarTag($tag);
                        $this->manySetTag($tag, $id);
                    }
                }
               

                return "Novo usuário cadastrado";

                /*return response()->json([
                    'message' => 'Contato criado com sucesso!',
                    'data' => $response->json()
                ]);*/
            } else {
                // Trata erros
                return response()->json([
                    'message' => 'Erro ao criar o contato!',
                    'error' => $response->json()
                ], $response->status());
            }
        }
    }

    public function manyFiel($id = null, $dados = null){ //ALTERAR CAMPOS DO USUÁRIO

        
        /*####################
        ### DADOS HOTMART ###
        ###################*/
        $campos = [
            "cliente_email", "cliente_nome", "cliente_telefone",
            "boleto_link", "pix_codigo", "transacao_hp", "cancelamento_motivo", "hotmart_event",
            "afiliado_id", "afiliado_nome", "afiliado_whatsapp_atendimento", "home_page", "cidade"
        ];
        
        $fields = [];
        
        foreach ($campos as $campo) {
            if (!empty($dados[$campo])) {
                $fields[] = [
                    "field_name" => $campo,
                    "field_value" => is_scalar($dados[$campo]) ? (string)$dados[$campo] : json_encode($dados[$campo])
                ];
            }
        }
        
        $data = [
            "subscriber_id" => (int)$id,
            "fields" => $fields
        ];
        
        
        $this->many_enviar_campos($data);

      
        /*####################
        ### DADOS DO CURSO ###
        ###################*/
        $campos = [
            "curso_nome", "curso_link", "curso_area_membros", "curso_link_checkout",
            "curso_areas_de_atuacao", "curso_conteudo_bonus", "curso_conteudo_principal",
            "curso_preco_completo", "curso_preco_basico", "curso_preco_parcelado_completo",
            "curso_preco_parcelado_basico", "curso_carga_horaria"
        ];
        
        $fields = [];
        
        foreach ($campos as $campo) {
            if (!empty($dados[$campo])) {
                $fields[] = [
                    "field_name" => $campo,
                    "field_value" => (string)$dados[$campo]
                ];
            }
        }
        
        $data = [
            "subscriber_id" => (int)$id,
            "fields" => $fields
        ];

        // ENVIO DO DADOS DO CURSO
        $this->many_enviar_campos($data);

        /*####################
        ### OUTROS DADOS ###
        ###################*/
        $campos = [
            "gpt_reposta", "gpt_thread_id", "gpt_error" , "gpt_status"
        ];
        
        $fields = [];
        
        foreach ($campos as $campo) {
            if (!empty($dados[$campo])) {
                $fields[] = [
                    "field_name" => $campo,
                    "field_value" => (string)$dados[$campo]
                ];
            }
        }
        
        $data = [
            "subscriber_id" => (int)$id,
            "fields" => $fields
        ];

        // ENVIO DO DADOS DO CURSO
        $this->many_enviar_campos($data);
        
       
    }

    public function many_enviar_campos($data){ 
        // Token de acesso para a API do ManyChat
        $accessToken = $this->many_access_token;

        // Endpoint da API
        $url = 'https://api.manychat.com/fb/subscriber/setCustomFields';


        // Faz a requisição para a API do ManyChat
        Http::withToken($accessToken)
                        ->post($url, $data);

        //return true;                

        // Verifica e retorna a resposta
        /*if ($response->successful()) {
            return response()->json([
                'message' => 'Campo alterado!',
                'data' => $response->json()
            ]);
        } else {
            // Trata erros
            return response()->json([
                'message' => 'Erro ao ao alterar o campo!',
                'error' => $response->json()
            ], $response->status());
        }*/
    }

    public function manygetid($campo="12347345", $campo_valor=""){ //BUSCAR ID DO USUARIO PESQUISANDO PELO CAMPO cliente_telefone
        ///fb/subscriber/findByCustomField
        // Endpoint e parâmetros, conforme definido em seu comando curl
        $url = 'https://api.manychat.com/fb/subscriber/findByCustomField';
        $fieldId = $campo; // cliente_telefone
        $fieldValue = $campo_valor; 
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
                return $data['data'][0];
            } else {
                return null;
            }

        } else {
            return null;
            /*return response()->json([
                'message' => 'Erro na requisição!',
                'error' => $response->json(),
            ], $response->status());*/
        }
    }

    public function many_buscar_campo_sistema($email){ //BUSCAR ID DO USUARIO PESQUISANDO PELO CAMPO 
        
        $url = 'https://api.manychat.com/fb/subscriber/findBySystemField';
        $accessToken = $this->many_access_token;

        // Faz a requisição GET
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get($url, [
            'email' => $email
        ]);

        // Verifica o sucesso da resposta e retorna ou lida com um erro
        if ($response->successful()) {
            
            $data = $response->json();

            // Acessar o ID do primeiro elemento em 'data'
            if (!empty($data['data']) && isset($data['data']['id'])) {
                return $data['data'];
            } else {
                return null;
            }

        } else {
            return null;
            /*return response()->json([
                'message' => 'Erro na requisição!',
                'error' => $response->json(),
            ], $response->status());*/
        }
    }

    public function manyCriarTag($tagNome){ //CRIAR TAG
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

    public function manySetTag($tagNome, $userID){ //SETAR TAG NO USUARIO
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


    //DADOS DO AFILIADO

    //DADOS DO CURSO

    //DADOS DA HOTMART

    
}
