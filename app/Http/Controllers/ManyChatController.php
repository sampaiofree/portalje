<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


class ManyChatController extends Controller
{
    protected $many_access_token;

    public function __construct($many_access_token)
    {
        $this->many_access_token = $many_access_token;
    }

 
    public function many($dados=null){

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

                /*return response()->json([
                    'message' => 'Contato criado com sucesso!',
                    'data' => $response->json()
                ]);*/
            } else {
                // Trata erros
                return response()->json([
                    'message' => 'Erro ao criar o contato!',
                    'message2' => json_encode($data),
                    'error' => $response->json()
                ], $response->status());
            }
        }
    }

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