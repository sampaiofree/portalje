<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAIService
{
    protected $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function create_thread()
    {
        // URL da API
        $url = 'https://api.openai.com/v1/threads';

        // Configuração dos cabeçalhos
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiKey,
            'OpenAI-Beta' => 'assistants=v2'
        ];

        // Dados para o corpo da requisição (neste caso, vazio)
        $data = [];

        // Envia a requisição POST para criar um thread
        $response = $this->enviar($url, $headers, $data, 'post');

        if ($response->successful()) {
            $responseData = $response->json();
            $this->criarthread = $responseData;
            $this->thread_id = $responseData['id'] ?? null;

            return $this->thread_id;
        }

        $errorData = $response->json();

        return [
            'error' => 'Erro ao criar a thread na API da OpenAI',
            
        ];
    }

    public function adicionar_mensagem($role='user', $mensagem, $thread_id)    
    {
        // URL da API
        $url = "https://api.openai.com/v1/threads/{$thread_id}/messages";

        // Configuração dos cabeçalhos
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiKey,
            'OpenAI-Beta' => 'assistants=v2'
        ];

        // Dados para o corpo da requisição (neste caso, vazio)
        $data = [
            'role' => $role,
            'content' => $mensagem,
        ];

        // Envia a requisição POST para criar um thread
        $response = $this->enviar($url, $headers, $data, 'post');

        if ($response->successful()) {
            $responseData = $response->json();
            return $responseData;
        }

        //return $response;

        return [
            'error' => 'Erro ao adicionar_mensagem na API da OpenAI: '.$response,
            'details' => $response->json(),
        ];
    }

    public function rodar_assistente($assistant_id, $thread_id)    
    {
        // URL da API
        $url = "https://api.openai.com/v1/threads/{$thread_id}/runs";

        // Configuração dos cabeçalhos
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiKey,
            'OpenAI-Beta' => 'assistants=v2'
        ];

        // Dados para o corpo da requisição (neste caso, vazio)
        $data = [
            'assistant_id' => $assistant_id
        ];

        // Envia a requisição POST para criar um thread
        $response = $this->enviar($url, $headers, $data, 'post');

        if ($response->successful()) {
            $responseData = $response->json();
            $urn_id = $responseData['id'];
            return $urn_id;
        }

        return $response;
    }

    public function verificar_status($thread_id, $run_id)    
    {
        // URL da API
        $url = "https://api.openai.com/v1/threads/{$thread_id}/runs/{$run_id}";

        // Configuração dos cabeçalhos
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiKey,
            'OpenAI-Beta' => 'assistants=v2'
        ];

        // Dados para o corpo da requisição (neste caso, vazio)
        $data = [
            //'assistant_id' => $assistant_id
        ];

        // Envia a requisição POST para criar um thread
        $response = $this->enviar($url, $headers, $data, 'get');

        if ($response->successful()) {
            $responseData = $response->json();
            return $responseData; // $responseData['status'] == queued, in_progress, requires_action, cancelling, cancelled, failed, completed, incomplete, or expired
        }

        return [
            'error' => 'Erro ao verificar_status na API da OpenAI',
        ];
    }

    public function submit_outputs($run_id, $thread_id, $tool_outputs)    
    {
        //return "oi";
        // URL da API
        $url = "https://api.openai.com/v1/threads/{$thread_id}/runs/{$run_id}/submit_tool_outputs";

        // Configuração dos cabeçalhos
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiKey,
            'OpenAI-Beta' => 'assistants=v2'
        ];

        // Dados para o corpo da requisição (neste caso, vazio)
        $data = [
            'tool_outputs' => $tool_outputs
        ];

        //return $data;

        // Envia a requisição POST para criar um thread
        $response = $this->enviar($url, $headers, $data, 'post');
        //return $response;

        if ($response->successful()) {
            $responseData = $response->json();
            //return $responseData;
            return true;
        }

        //return $response;

        return false;
    }

    public function receber_mensagem($thread_id){
         // URL da API
         $url = "https://api.openai.com/v1/threads/{$thread_id}/messages";

         // Configuração dos cabeçalhos
         $headers = [
             'Content-Type' => 'application/json',
             'Authorization' => 'Bearer ' . $this->apiKey,
             'OpenAI-Beta' => 'assistants=v2'
         ];
 
         // Dados para o corpo da requisição (neste caso, vazio)
         $data = [
             //'assistant_id' => $assistant_id
         ];
 
         // Envia a requisição POST para criar um thread
         $response = $this->enviar($url, $headers, $data, 'get');
 
         if ($response->successful()) {
             $responseData = $response->json();
             $data['role'] = $responseData['data'][0]['role'];
             $data['resposta'] = $responseData['data'][0]['content'][0]['text']['value'];
             //dd($responseData);
             if($data['role']=="assistant"){
                return $data['resposta'];
             }
            
         }
 
         return false;
    }

    public function receber_mensagem_historico($thread_id){
        // URL da API
        $url = "https://api.openai.com/v1/threads/{$thread_id}/messages";

        // Configuração dos cabeçalhos
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiKey,
            'OpenAI-Beta' => 'assistants=v2'
        ];

        // Dados para o corpo da requisição (neste caso, vazio)
        $data = [
            //'assistant_id' => $assistant_id
        ];

        
        $response = $this->enviar($url, $headers, $data, 'get');

        if ($response->successful()) {
            $responseData = $response->json();
            
            return $responseData;
           
        }

        return false;
   }

   
    private function enviar($url, $headers, $data, $method='post'){

        if($method=='post'){
            $response = Http::withHeaders($headers)->post($url, $data);
        }else{
            $response = Http::withHeaders($headers)->get($url, $data);
        }

        return $response;

    }

    public function gerarImagem($tituloDoPost, $tamanho = '1024x1024')
    {
        $url = 'https://api.openai.com/v1/images/generations';

        $response = Http::withToken($this->apiKey)
            ->post($url, [
                'prompt' => "A clickbait digital illustration showing a shocking moment related to: {$tituloDoPost}. Hyper-realistic expressions, vibrant colors, curious and dramatic atmosphere, attention-grabbing composition.",
                'n' => 1,
                'size' => $tamanho,
                'response_format' => 'url'
            ]);

        if ($response->successful()) {
            return $response->json()['data'][0]['url'];
        }

        // Retorna erro se falhar
        return [
            'error' => true,
            'message' => $response->json()['error']['message'] ?? 'Erro ao gerar imagem.'
        ];
    }

    

    
}
