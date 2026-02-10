<?php

namespace App\Http\Controllers;

use App\Models\Curso;

use App\Services\OpenAIService;
use App\Http\Resources\OpenAIResource;
use App\Services\ManyChatService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


use GuzzleHttp\Client;

use Exception;

class OpenAIController extends Controller
{
    protected $openAIService;
    protected $api_key;

    public function __construct() 
    {
        
    }

    public function create_thread()
    {
        $this->openAIService = new OpenAIService($this->api_key); //ADICIONAR APIKEY
        
        $response = $this->openAIService->create_thread();

        return $response;
    }

    public function adicionar_mensagem($mensagem, $thread_id, $role)
    {
        $this->openAIService = new OpenAIService($this->api_key); //ADICIONAR APIKEY

        $response = $this->openAIService->adicionar_mensagem($role, $mensagem, $thread_id);

        return $response;
    }

    public function rodar_assistente($assistant_id, $thread_id)
    {
        $this->openAIService = new OpenAIService($this->api_key); //ADICIONAR APIKEY

        $response = $this->openAIService->rodar_assistente($assistant_id, $thread_id);

        return $response;
    }

    private function aguardar_status($thread_id, $run_id){

        $status = $this->openAIService->verificar_status($thread_id, $run_id);

        //print_r($status); exit;

        while($status['status']=="queued" OR $status['status']=="in_progress"){  //
            sleep(1);
            $status = $this->openAIService->verificar_status($thread_id, $run_id);
        }

        return $status;

    }

    public function receber_mensagem($thread_id)
    {
        $this->openAIService = new OpenAIService($this->api_key); //ADICIONAR APIKEY

        $response = $this->openAIService->receber_mensagem($thread_id);

        return $response;
    }

    public function bot_conversa(Request $request)
    {
        $error = false;

        $mensagem = $request->input('mensagem')??null;
        $mensagem = str_replace("**", "*", $mensagem);
        $role = $request->input('role')??null;
        $assistant_id = $request->input('assistant_id')??null;
        $thread_id = $request->input('thread_id')??null;
        $nome = $request->input('nome')??null;
        $telefone = $request->input('telefone')??null;
        $webhook_url = $request->input('webhook_url')??null;
        $run_id = $request->input('run_id')??null;
        $this->api_key = $request->input('api_key')??null;

        //return response()->json(['respota' => $request->input('mensagem')], 200);

        $this->openAIService = new OpenAIService($this->api_key); //ADICIONAR APIKEY

        //create_thread
        if(!$thread_id OR $thread_id=="{{cuf_13143909}}" OR $thread_id=="123456" OR $thread_id==123456 OR $thread_id=='0' OR $thread_id==0 OR $thread_id=="{thread_id}" OR empty($thread_id)){
            $thread_id = $this->create_thread();
        }
        
        //return response()->json(['error' => $thread_id], 500);

        if (is_array($thread_id) && isset($thread_id['error'])) {
            $error = $thread_id['error'];
            return response()->json(['error' => $error], 500);
        }


        //if($run_id){return $this->aguardar_status($thread_id, $run_id);} //VER O QUE ESTÁ RETORNANDO DA RUN_ID
       

        if(!$run_id){
            //adicionar_mensagem
            if($mensagem){
                $mensagem = $this->adicionar_mensagem($mensagem, $thread_id, $role);
                if (is_array($mensagem) && isset($mensagem['error'])) {
                    return response()->json(['error' => $mensagem['error']], 500);
                }
                
                if($role=='assistant'){
                    return response()->json(['thread_id' => $thread_id]);
                    exit;
                }
            }
            
            // echo "<pre>";
            // print_r($mensagem); 
            // echo "</pre>";

            //rodar_assistente
            $rodar_assistente = $this->rodar_assistente($assistant_id, $thread_id);
            if (is_array($rodar_assistente) && isset($rodar_assistente['error'])) {
                return response()->json(['error' => $rodar_assistente['error']], 500);
            }
            
            // echo "<pre>";
            // print_r($rodar_assistente); 
            // echo "</pre>";

            // exit; 

            //verificar_status
            $run_id = $rodar_assistente;
            
           
        }
        
        

        $status = $this->aguardar_status($thread_id, $run_id);        

        

        if($status['status']=="requires_action"){
            while($status['status']=="requires_action"){
                $tool_outputs =  $this->executar_functions($status);
                //echo "tool_outputs: "; print_r($tool_outputs); 
                //return response()->json(['tool_outputs' => $tool_outputs]);
                $submit_outputs = $this->submit_outputs($run_id, $thread_id, $tool_outputs);
                //echo "|| submit_outputs: "; print_r($submit_outputs); 
                //return response()->json(['submit_outputs' => $submit_outputs]);
                sleep(1);
                $status = $this->aguardar_status($thread_id, $run_id);
                //echo "|| Executou o requires_action. || ";
            }
            
        } 
        

        //echo "Status:";
        //print_r($status['status']); 

        $mensagem = $this->receber_mensagem($thread_id);    
        $mensagem = str_replace("###", '', $mensagem);
         /*teste*/
         //return   $webhook_url;
        
        if($mensagem){

            $dados = [
                'mensagem' => $mensagem,
                'nome' => $nome,
                'telefone' => $telefone,
                'thread_id' => $thread_id,
                // outros dados que você deseja enviar
            ];


            $webhookUrl = $webhook_url;

            if($webhookUrl){
                $response = Http::post($webhookUrl, $dados); 
            }
            

            //ENVIAR PARA MANYCHAT
            $this->manychat("0", $nome, $telefone, $mensagem, $thread_id);
            
        
            // Verificar a resposta do webhook
            if ($response->successful()) {
                // O webhook recebeu a solicitação com sucesso
                return response()->json(['message' => 'Dados enviados com sucesso para o webhook.']);
            } else {
                // Ocorreu um erro ao enviar os dados para o webhook
                $response = json_encode($response);
                return response()->json(['error' => 'Falha ao enviar dados para o webhook.'.$response ], 500);
            }
        }

        //ENVIAR PARA MANYCHAT
        $this->manychat("1", $nome, $telefone, $mensagem, $thread_id);
        
    }

    public function manychat($error, $nome, $telefone, $mensagem, $thread_id){

        $manychat = new ManyChatService('2250527:c0257f693d4b13e84e7d1c7340253ed2');
        $dados_many = [
                'cliente_nome' => $nome,
                'cliente_telefone' => $telefone,
                'phone' => $telefone,
                'whatsapp_phone' => $telefone,
                'gpt_reposta' => $mensagem,
                'gpt_thread_id' => $thread_id,
                'gpt_error' => $error?"1":"0",
                'cliente_telefone_id' => '12347345'

            ];
            $manychat->many($dados_many);

    }

    public function bot_conversa_create_thread(Request $request){

        $this->api_key = $request->input('api_key');

        $this->openAIService = new OpenAIService($this->api_key); //ADICIONAR APIKEY
        $thread_id = $this->create_thread();
        return response()->json(['message' => $thread_id]);
    }

    public function receber_mensagem_historico(Request $request)
    {
        $this->api_key = $request->input('api_key');
        $this->openAIService = new OpenAIService($this->api_key); //ADICIONAR APIKEY

        $thread_id = $request->input('thread_id');

        $response = $this->openAIService->receber_mensagem_historico($thread_id);

        return $response;
    }

    public function executar_functions($status){
        
        foreach($status['required_action']['submit_tool_outputs']['tool_calls'] as $function){
            
            $name = $function['function']['name'];
            $arguments = $function['function']['arguments'];

            
            
            if($name=='bd'){
                $tool_outputs[]=[
                "tool_call_id"=> $function['id'],
                "output" => json_encode($this->banco_de_dados($arguments))
                ];

                
            }

            if($name=='pesquisar_curso_titulo'){
                $tool_outputs[]=[
                "tool_call_id"=> $function['id'],
                "output" => json_encode($this->pesquisar_curso_titulo($arguments))
                ];
            }
            
            if($name=='funcao_lista_dos_cursos'){
                $tool_outputs[]=[
                "tool_call_id"=> $function['id'],
                "output" => json_encode($this->funcao_lista_dos_cursos())
                ];

                
            }if($name=='funcao_dados_curso_individual'){
                $tool_outputs[]=[
                "tool_call_id"=> $function['id'],
                "output" => json_encode($this->funcao_dados_curso_individual($arguments))
                ];

                
            }elseif($name=='chamado_boot_conversa'){
                
                $tool_outputs[]=[
                    "tool_call_id"=> $function['id'],
                    "output" => json_encode($this->chamado_boot_conversa($arguments))
                    ];
                
            }elseif($name=='suporte_aluno'){

                //print_r($function); exit;

                $tool_outputs[]=[
                    "tool_call_id"=> $function['id'],
                    "output" => json_encode($this->suporte_aluno($arguments))
                    ];
                
            }

        }

        return $tool_outputs;
    }

    private function banco_de_dados($arguments, $bindings = [])
    {
        $array = json_decode($arguments, true);

        $tipo = $array['tipo'];
        $query = $array['query'];

        //return $query;

        try {
            switch ($tipo) {
                case 'select':
                    return DB::select($query, $bindings);
                case 'insert':
                    return DB::insert($query, $bindings);
                case 'update':
                    return DB::update($query, $bindings);
                case 'delete':
                    return DB::delete($query, $bindings);
                default:
                    throw new Exception("Tipo de consulta SQL não suportado: " . $tipo);
            }
        } catch (Exception $e) {
            // Log do erro ou retorno de uma resposta apropriada
            // Você pode usar o Log do Laravel para capturar o erro: Log::error($e->getMessage());
            return response()->json(['error' => 'Erro ao executar consulta SQL.'], 500);
        }
    }

    public function funcao_dados_curso_individual($arguments)
    {
        // Decodificando o JSON e verificando se está correto
        $array = json_decode($arguments, true);
    
        // Verificando se a conversão de JSON funcionou e se o ID existe no array
        if (is_array($array) && isset($array['id'])) {
            $id = $array['id'];
    
            // Buscando o curso pelo ID
            $curso = Curso::where('id', $id)->first();
    
            // Verificando se o curso foi encontrado
            if ($curso) {
                // Retornar o curso encontrado
                return response()->json($curso);
            } else {
                // Retornar resposta de erro se o curso não for encontrado
                return response()->json(['error' => 'Curso não encontrado, use a funcion funcao_lista_dos_cursos para verificar o id correto do curso'], 404);
            }
        } else {
            // Retornar resposta de erro se o JSON for inválido ou ID não estiver presente
            return response()->json(['error' => 'Dados inválidos'], 400);
        }
    }
    
    public function pesquisar_curso_titulo($arguments)
    {
        // Decodificando o JSON e verificando se está correto
        $array = json_decode($arguments, true);
    
        // Verificando se a conversão de JSON funcionou e se o ID existe no array
        if (is_array($array) && isset($array['titulo'])) {
            $titulo = $array['titulo'];
    
            // Buscando o curso pelo ID
            $dados = Curso::where('titulo', 'LIKE', $titulo)->first();
            $markdown = "# Curso: " . $dados->titulo . "\n";
            $markdown .= "## Descricao curta: " . $dados->descricao_curta . "\n";
            $markdown .= "## Conteudo principal: " . $dados->conteudo_principal . "\n";
            $markdown .= "## Conteudo bonus: " . $dados->conteudo_bonus . "\n";
            $markdown .= "## Areas de atuacao: " . $dados->areas_de_atuacao . "\n";
            $markdown .= "## Video dentro do curso: https://youtu.be/" . $dados->video_dentro_do_curso . "\n";
            $markdown .= "## Video de Apresentação do curso: https://youtu.be/" . $dados->video_apresentacao . "\n";
            $markdown .= "## Link do checkout: " . $dados->link_checkout_completo . "\n";
            $markdown .= "## Preço da taxa de inscrição Parcelado: " . $dados->preco_parcelado_completo . "\n";
            $markdown .= "## Preço da taxa de inscrição: " . $dados->preco_cheio_completo . "\n";
            $markdown .= "## Horas do curso: " . $dados->horas_completo . "\n";
            $markdown .= "## Link da área de membros: " . $dados->link_area_membros . "\n";
            $markdown .= "## Nome do Professor: " . $dados->professor_nome . "\n";
            $markdown .= "## Biografia do Professor: " . $dados->professor_biografia . "\n";
            $curso = $markdown;

            // Verificando se o curso foi encontrado
            if ($curso) {
                // Retornar o curso encontrado
                return response()->json($curso);
            } else {
                // Retornar resposta de erro se o curso não for encontrado
                return response()->json(['error' => 'Curso não encontrado, use a funcion funcao_lista_dos_cursos para verificar o id correto do curso'], 404);
            }
        } else {
            // Retornar resposta de erro se o JSON for inválido ou ID não estiver presente
            return response()->json(['error' => 'Dados inválidos'], 400);
        }
    }

    public function funcao_lista_dos_cursos()
    {
        $cursos = Curso::select('id', 'titulo')->get();

        // Retornar os resultados (você pode personalizar a forma de retorno, como JSON, por exemplo)
        return response()->json($cursos);
            
    }

    private function chamado_boot_conversa($arguments){
        $client = new Client();
        $webhook_viajandinho = 'https://backend.botconversa.com.br/api/v1/webhooks-automation/catch/120218/xLS7xFxKe04a/'; // Substitua pela URL do seu webhook
        
        $array = json_decode($arguments, true);

        //print_r($array); exit;
        
        try {
            $response = $client->post($webhook_viajandinho, [
                'json' => [
                    'nome' => $array['nome']??null,
                    'telefone' => $array['telefone']??null,
                    'email' => $array['email']??null,
                    'resumo' => $array['resumo']??null,
                    'destino' => $array['destino']??null,
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
            return false;
        }
    }

    private function suporte_aluno($arguments){
        $client = new Client();
        $webhookUrl = 'https://backend.botconversa.com.br/api/v1/webhooks-automation/catch/64070/AFHmUXNoqjXi/'; // Substitua pela URL do seu webhook
        
        $array = json_decode($arguments, true);

        $nome = $array['nome'];
        $email = $array['email'];
        $telefone = $array['telefone'];
        $problema = $array['problema'];
        $curso = $array['curso'];

        if (strlen($telefone) < 12) {
            // Adiciona o prefixo '55' no início do número
            $telefone = '55' . $telefone;
        }

        try {
            $response = $client->post($webhookUrl, [
                'json' => [
                    'nome' => $nome,
                    'email' => $email,
                    'telefone' => $telefone,
                    'problema' => $problema,
                    'curso' => $curso,
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
            return false;
        }
    }   

    private function submit_outputs($run_id, $thread_id, $tool_outputs){
        //return "oi";
        return $this->openAIService->submit_outputs($run_id, $thread_id, $tool_outputs);
    }
}
