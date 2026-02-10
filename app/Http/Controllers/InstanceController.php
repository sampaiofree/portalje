<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;

use App\Http\Controllers\LeadsController;

use App\Models\Instance;
use App\Models\Codigo_ref;

use App\Jobs\ZapJob;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\User;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InstanceController extends Controller
{

    public function index()
    {        

        $status = false;
        $instacia = false;

        $user = Auth::user(); // OBTER DADOS DO USUARIO LOGADO
        $instance = Instance::where('user_id', $user->id)->first(); // CONSULTAR SE EXISTE REGISTRO DA INSTANCIA NO BANCO DE DADOS

        $buscar_instancia = false; // Inicializa a variável para evitar erros

        if ($instance) { //CASO EXISTA INSTANCIA CRIADA NO BANCO DE DADOS
            
            $buscar_instancia = $this->buscar_instancia($instance->instanceName); // CONSULTAR SE EXISTE INSTANCIA NA EVOLUTION
            
            if (!$buscar_instancia) { //SE NÃO EXISTIR INSTANCIA NA EVOLUTION
                $instance->delete(); // DELETAR DO BANCO DE DADOS
                $instacia = false;

            }else{ //EXISTE INSTANCIA NA EVOLUTION
                $instacia = true;
                $instanceName = '';
                //$status = "Aui";
                $status = $this->checkConnectionStatus($instance->instanceName); //CONSULTAR STATUS DE CONEXÃO
            }

        }
        
        if(!$instacia){ //SE NÃO EXISTE INSTANCIA REGISTRADA NO BANCO DE DADOS ENTÃO CRIAR
            $instacia = false; 
            $instance = Instance::create(['user_id' => $user->id]);
            $instance->instanceName = $instance->id;
            $instance->save();
            $instanceName = $instance->id;
        }

        //print_r($status); exit;
        
        
        return view('adm.zap.qr_code', compact('instacia', 'instanceName', 'status')); // RETORNA PARA A VIEW COM O STATE
        
    }

    public function extrair_leads_grupos(Request $request)
    {        

        $grupo_id = $request->input('grupo_id');

        $user = Auth::user(); // OBTER DADOS DO USUARIO LOGADO
        $instance = Instance::where('user_id', $user->id)->first(); // CONSULTAR SE EXISTE REGISTRO DA INSTANCIA NO BANCO DE DADOS

        $buscar_instancia = false; // Inicializa a variável para evitar erros

        if ($instance) { //CASO EXISTA INSTANCIA CRIADA NO BANCO DE DADOS
            
            $buscar_instancia = $this->buscar_instancia($instance->instanceName); // CONSULTAR SE EXISTE INSTANCIA NA EVOLUTION
            
            if ($buscar_instancia) { //SE NÃO EXISTIR INSTANCIA NA EVOLUTION
                 // Defina a URL da requisição
                $url = "https://escolaparaafiliados.org/group/participants/{$instance->instanceName}?groupJid=$grupo_id";
                
                // Faça a requisição GET com o cabeçalho 'apikey'
                $response = Http::withHeaders([
                    'apikey' => env('EVOLUTION_API_KEY'),
                ])->get($url);

                $data = $response->json();
        
                
                

                if(isset($data['participants'])){ 

                    $ref = Codigo_ref::where('user_id', $user->id)->where('codigo_ref', '<>', '')->whereNotNull('codigo_ref')->first();

                    if($ref){
                        $afiliado = [
                            "nome" => $user->name,
                            "ref" => $ref->codigo_ref,
                        ];

                        //print_r($afiliado); exit;
                        
                        $lead = new LeadsController;
                        $lead->lead_grupo_zap($data['participants'], $afiliado, $grupo_id);     
                        return true;
                    }
                    
                }

            }

        }
        
        return false;
    }

    public function criar_instancia(Request $request){

        $data = [
            
            "instanceName" => $request->input('instanceName'),
            "proxyHost" => $request->input('proxyHost'),
            "proxyPort" => $request->input('proxyPort'),
            "proxyProtocol" => 'http',
            "proxyUsername" => $request->input('proxyUsername'),
            "proxyPassword" => $request->input('proxyPassword'),
        ];

        // Fazer a requisição para a Evolution API para criar a instância
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'apikey' => env('EVOLUTION_API_KEY'),
        ])->post('https://evolution.escolaparaafiliados.org/instance/create', $data);


        /*echo "<pre>";
        print_r($response);
        echo "</pre>";
        exit;*/


        if ($response->successful()) {
            // Sucesso na criação da instância
            $instanceData = $response->json();

            $inst = [
                'instance_id' => $instanceData['instance']['instanceId'],
                'instanceName' =>$instanceData['instance']['instanceName'],
                'status' => $instanceData['instance']['status'],
                'apikey' => $instanceData['hash']['apikey'],
                "proxyHost" => $request->input('proxyHost'),
                "proxyPort" => $request->input('proxyPort'),
                "proxyUsername" => $request->input('proxyUsername'),
                "proxyPassword" => $request->input('proxyPassword'),
            ];

            $this->criar_atualizar_instancia($inst); //REGISTRAR NO BANCO DE DADOS
            $instacia = true;
        } else {
            $instacia = false;
        }

        return redirect()->route('instance.index');
    }

    public function logoutInstance()
    {

        $user = Auth::user(); //OBTER DADOS DO USUARIO LOGADO
        $instance = Instance::where('user_id', $user->id)->first(); //CONSULTAR SE EXISTE REGISTRO DA INSTANCIA NO BANCO DE DADOS
        
        
        if(isset($instance->instanceName) AND !empty($instance->instanceName)) {// CONSULTAR SE EXISTE INSTANCIA NA EVOLUTION
            $instanceName = $this->buscar_instancia($instance->instanceName);
        }else{
            $instanceName = false;
        }

        if(!$instanceName){return redirect()->route('instance.index');}
        

        // Substitua pela URL correta do servidor Evolution API
        $url = "https://evolution.escolaparaafiliados.org/instance/logout/{$instance->instanceName}";

        // Executa a requisição DELETE usando o cliente HTTP do Laravel
        $response = Http::withHeaders([
            'apikey' => env('EVOLUTION_API_KEY'),
        ])->delete($url);

        //$data = $response->json();
        //print_r($data); exit; 

        $this->delete($instance->instanceName);
        return redirect()->route('instance.index');
    }

    private function delete($instanceName){
        $url = "https://evolution.escolaparaafiliados.org/instance/delete/{$instanceName}";

        // Executa a requisição DELETE usando o cliente HTTP do Laravel
        Http::withHeaders([
            'apikey' => env('EVOLUTION_API_KEY'),
        ])->delete($url);

    }

    private function criar_atualizar_instancia($dados){
        
        $user = Auth::user();
        Instance::updateOrCreate(
            [
                'user_id' => $user->id
            ],
            [
                'instance_id' => $dados['instance_id'],
                'instanceName' => $dados['instanceName'],
                'status' => $dados['status'],
                'apikey' => $dados['apikey'], // Acesso ao campo correto
                "proxyHost" => $dados['proxyHost'],
                "proxyPort" =>$dados['proxyPort'],
                "proxyUsername" => $dados['proxyUsername'],
                "proxyPassword" => $dados['proxyPassword'],
            ]
        );
        

    }

    public function get_QRCode()
    {
        $user = Auth::user(); // OBTER DADOS DO USUARIO LOGADO       

        $instance = Instance::where('user_id', $user->id)->first(); // CONSULTAR SE EXISTE REGISTRO DA INSTANCIA NO BANCO DE DADOS        
        
        
        $status = $this->checkConnectionStatus($instance->instanceName);

        //return response()->json(['code' => $status]);

        if(!$status){
    
            // Substitua pela URL correta do servidor Evolution API
            $url = "https://evolution.escolaparaafiliados.org/instance/connect/{$instance->instanceName}";
    
            // Executa a requisição GET usando o cliente HTTP do Laravel
            $response = Http::withHeaders([
                'apikey' => env('EVOLUTION_API_KEY'), // A API key deve estar no seu arquivo .env
            ])->get($url);
    
            // Verifica se a requisição foi bem-sucedida
            if ($response->successful()) {
                $data = $response->json();
                
                // Retorna o HTML do QR code como JSON para ser usado no AJAX
                return response()->json(['code' => $data['code']]);
            } else {
                // Trata o erro, você pode lançar uma exceção ou retornar um array vazio
                /*return response()->json([
                    'message' => 'Erro ao obter o QR code',
                    'error' => $response->body(),
                ]);*/
                return response()->json(['code' => false]);
            }
        }else{
            return response()->json(['code' => false]);
        }

        
    }

    public function checkConnectionStatus($instanceName)
    {
        // Substitua pela URL correta do servidor Evolution API
         $url = "https://evolution.escolaparaafiliados.org/instance/connectionState/{$instanceName}";
        //$url = "https://evolution.escolaparaafiliados.org/instance/connectionState/39";

        // Executa a requisição GET usando o cliente HTTP do Laravel
        $response = Http::withHeaders([
            'apikey' => env('EVOLUTION_API_KEY'),
        ])->get($url);

        $data = $response->json();

        //print_r($data['instance']['state']); exit;
        
        if($data['instance']){
            foreach($data['instance'] as $index => $value){
                if($value=='open'){return true;}
            }
        }
        
        return false;
    }

    private function buscar_instancia($instanceName){

         // Defina a URL da requisição
         $url = 'https://escolaparaafiliados.org/instance/fetchInstances?instanceName='.$instanceName;
         
         // Faça a requisição GET com o cabeçalho 'apikey'
         $response = Http::withHeaders([
             'apikey' => env('EVOLUTION_API_KEY'),
         ])->get($url);

         $data = $response->json();
 
         /*print_r($data);
         exit;*/

        foreach($data as $index => $value){
            if($index=='error'){return false;}
        }

        return true;

    }

    public function restartInstance()
    {

        $user = Auth::user(); //OBTER DADOS DO USUARIO LOGADO
        $instance = Instance::where('user_id', $user->id)->first(); //CONSULTAR SE EXISTE REGISTRO DA INSTANCIA NO BANCO DE DADOS
        
        
        if(isset($instance->instanceName) AND !empty($instance->instanceName)) {// CONSULTAR SE EXISTE INSTANCIA NA EVOLUTION
            $instanceName = $this->buscar_instancia($instance->instanceName);
        }else{
            $instanceName = false;
        }

        if(!$instanceName){ return response()->json(['message' => 'Instância não encontrada']);}

        // Substitua pela URL correta do servidor Evolution API
        $url = "https://evolution.escolaparaafiliados.org/instance/restart/{$instanceName}";

        // Executa a requisição PUT usando o cliente HTTP do Laravel
        $response = Http::withHeaders([
            'apikey' => env('EVOLUTION_API_KEY'),
        ])->put($url);

        // Verifica se a requisição foi bem-sucedida
        if ($response->successful()) {
            // Retorna a resposta como JSON
            return response()->json(['message' => 'Instância reiniciada com sucesso']);
        } else {
            return response()->json([
                'message' => 'Erro ao reiniciar a instância',
                'error' => $response->body(),
            ], $response->status());
        }
    }

    public function enviar_mensagem(Request $request){

        $user = Auth::user(); //OBTER DADOS DO USUARIO LOGADO
        $instance = Instance::where('user_id', $user->id)->first(); //CONSULTAR SE EXISTE REGISTRO DA INSTANCIA NO BANCO DE DADOS
        
        
        if(isset($instance->instanceName) AND !empty($instance->instanceName)) {// CONSULTAR SE EXISTE INSTANCIA NA EVOLUTION
            $instanceName = $this->buscar_instancia($instance->instanceName);
        }else{
            $instanceName = false;
        }

        if(!$instanceName){ return response()->json(['message' => 'Instância não encontrada']);}else{
            $instanceName = $instance->instanceName;
        }

        $leads = json_decode($request->input('selectedRowsData'), true); // Decodifica como um array associativo

        $texto_padrao = $request->input('texto_padrao') ?? null;
        $imagem = $request->input('imagem') ?? null;
        $imagem_legenda = $request->input('imagem_legenda') ?? null;
        $audio = $request->input('audio') ?? null;
        $intervalo = $request->input('intervalo');

        $tempo = 0;

        foreach($leads as $lead){

            if(strlen($lead['telefone'])<12){$lead['telefone'] = "55".$lead['telefone'];}//ADICIONAR 55 NO TELEFONE CASO SEJA NUMERO BRASILEIRO

            $delay = rand($intervalo, $intervalo+=50);
            $tempo += $delay."000";

            if($texto_padrao){ //SE EXISTIR TEXTO A SER ENVIADO
                $texto = str_replace("{nome}", $lead['nome'], $texto_padrao); //SUBSTITUIR {nome} PELO NOME DO LEAD
                $texto = str_replace("{curso}", $lead['curso'], $texto); // SUBSTITUIR {curso} PELO NOME DO CURSO
                
                $this->send_text($lead, $tempo, $texto, $instanceName); //ENVIAR TEXTO
                sleep(5);
            } 

            if ($request->hasFile('imagem')){ //SE TIVER IMAGEM A SER ENVIADA
                
                $image = $request->file('imagem');
                $imageBase64 = base64_encode(file_get_contents($image->getRealPath()));

                if($imagem_legenda){ //SE EXISTIR LEGENDA
                    $imagem_legenda = str_replace("{nome}", $lead['nome'], $imagem_legenda); //SUBSTITUIR {nome} PELO NOME DO LEAD
                    $imagem_legenda = str_replace("{curso}", $lead['curso'], $imagem_legenda); // SUBSTITUIR {curso} PELO NOME DO CURSO
                } 

                // Preenche os dados da imagem no payload
                $imagem['fileName'] = $image->getClientOriginalName();
                $imagem['media'] = $imageBase64;
                $imagem['caption'] = $imagem_legenda??"";

                $this->send_image($lead, $tempo, $imagem, $instanceName);
                sleep(5);
            }

            if ($request->hasFile('audio')) {//SE TIVER AUDIO A SER ENVIADO

                $audio = $request->file('audio');
                // Converte o áudio para base64
                $audioBase64 = base64_encode(file_get_contents($audio->getRealPath()));

                $this->send_audio($lead, $tempo, $audioBase64, $instanceName);
                sleep(5);

            }
        }

        
        

    }

    public function send_text($lead, $tempo, $texto, $instanceName)
    {
        $payload = [
            'number' => $lead['telefone'],
            'options' => [
                'delay' => (int)$tempo,
                'presence' => 'composing',
                'linkPreview' => true,
                'quoted' => null,
                'mentions' => null,
            ],
            'textMessage' => [
                'text' => $texto,
            ]
        ];
        
        $link = "https://evolution.escolaparaafiliados.org/message/sendText/{$instanceName}";
        $post_get = "POST";
        $this->processar($link, $payload, $post_get);
        
    }

    public function send_audio($lead, $tempo, $audio, $instanceName)
    {
        $payload = [
            'number' => $lead['telefone'],
            'options' => [
                'delay' => (int)$tempo,
                'presence' => 'composing',
                'encoding' => true,
            ],
            'audioMessage' => [
                'audio' => $audio,
            ]
        ];
        
        $link = "https://evolution.escolaparaafiliados.org/message/sendWhatsAppAudio/{$instanceName}";
        $post_get = "POST";
        $this->processar($link, $payload, $post_get);
        
    }

    public function send_image($lead, $tempo, $imagem, $instanceName)
    {
        $telefone = $lead['telefone'];
        $telefone = $lead['telefone'];

        // Prepara os dados para a requisição POST
        $payload = [
            'number' => $telefone,
            'options' => [
                'delay' => (int)$tempo,
                'presence' => 'composing',
            ],
            'mediaMessage' => [
                "mediatype"=> "image",
                'fileName' => $imagem['fileName']??"",
                'caption' => $imagem['caption']??"",
                'media' => $imagem['media'],
            ]
        ];
        $link = "https://evolution.escolaparaafiliados.org/message/sendMedia/{$instanceName}";
        $post_get = "POST";
        $this->processar($link, $payload, $post_get);
    }

    private function processar($link, $payload, $post_get){
        $curl = curl_init();

        curl_setopt_array($curl, [
        CURLOPT_URL =>  $link,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 1,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $post_get,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "apikey: ".env('EVOLUTION_API_KEY')
        ],
        //CURLOPT_NOSIGNAL => 1, // Evita problemas com timeouts em sistemas 64 bits
        ]);

        curl_exec($curl);
        curl_close($curl);
    }

}
