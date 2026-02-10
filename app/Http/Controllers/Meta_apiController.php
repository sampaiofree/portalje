<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\Home_e_cursosController;


use App\Models\User;
use App\Models\Cidades;
use PhpParser\Node\Expr\Cast\Object_;

class Meta_apiController extends Controller
{

    public $accessToken;
    public $adAccountId; //CONTA DE ANUNCIOS
    public $campaignId; // ID DA CAMPANHA
    public $campanhaNome;
    public $campanhaObjetivo;
    public $conjuntoNome;
    public $cityName;
    public $regionName;
    public $cityId;
    public $pixelId;
    public $conversionEvent;
    public $conjuntoID; //$adSetId
    public $inicioDataHora;
    public $adCreativeId;
    public $tituloAnuncio;
    public $corpoAnuncio;
    public $url;
    public $imagePath;
    public $imageHash;
    public $uploadImage;
    public $page_id;
    public $instagram_actor_id;
    public $erros;
    public $getCityId;
    public $createAdSet;
    public $createAd;
    public $adId;
    public $createAdCreative;
    public $createCampaign;

    // enviar evento API META
    public function evento($request, $lead, $evento='Lead'){      
        
        if($request->input('evento')!=null){$evento=$request->input('evento');}
        //exit;

        try {
            // Pegar dados do usuário pelo domínio
            if(isset($lead['user']) AND !empty($lead['user'])){
                $user = $lead['user'];
            }else{
                $user = $this->listar_user_pelo_dominio($request->getHost());
            }
           

            if (!$user) {
                return response()->json(['error' => 'Usuário não encontrado'], 404);
            }

            session(['HTTP_REFERER' => $request->headers->get('referer')]);
            session(['REMOTE_ADDR' => $request->ip()]);
            session(['HTTP_USER_AGENT' => $request->header('User-Agent')]);

            $pixel_id = $user->meta_pixel_id;
            $access_token = $user->meta_pixel_api;
            $http_referer = htmlspecialchars(session('HTTP_REFERER'), ENT_QUOTES, 'UTF-8');
            $remote_addr = htmlspecialchars(session('REMOTE_ADDR'), ENT_QUOTES, 'UTF-8');
            $http_user_agent = htmlspecialchars(session('HTTP_USER_AGENT'), ENT_QUOTES, 'UTF-8');
            $email = $lead['email'] ?? null;
            $telefone = $lead['telefone'] ?? null;
            $test_eventos = $user->meta_pixel_eventcode;

            // Construção do evento
            $event = [
                "event_name" => $evento,
                "event_time" => time(),
                "user_data" => [
                    'client_ip_address' => $remote_addr,
                    'client_user_agent' => $http_user_agent,
                    'em' => $email ? hash('sha256', $email) : null, // Aplicar função de hash ao email
                    'ph' => $telefone ? hash('sha256', $telefone) : null, // Aplicar função de hash ao telefone
                ],
                'event_source_url' => $this->getEventSourceUrl($http_referer),
                "opt_out" => false,
                "event_id" => $this->generateUniqueEventId(),
                "action_source" => "website",
                "data_processing_options" => [],
                "data_processing_options_country" => 0,
                "data_processing_options_state" => 0,
            ];

            // Mistura os eventos num conjunto de eventos
            $events = [$event];
            $payload_data = [
                'data' => $events,
                'test_event_code' => $test_eventos, // Código Test de eventos
            ];

            $url = 'https://graph.facebook.com/v16.0/' . $pixel_id . '/events?access_token=' . $access_token;
            $payload = json_encode($payload_data);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

            $response = curl_exec($ch);

            if ($response === false) {
                return response()->json(['error' => 'Error: ' . curl_error($ch)]);
            } else {
                $response_data = json_decode($response, true);
                if (isset($response_data['error'])) {
                    return response()->json(['error' => $response_data['error']['message']]);
                } else {
                    return response()->json(['success' => $response_data]);
                }
            }
        } catch (\Exception $e) {
            //Log::error('Erro ao enviar evento: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        } finally {
            if (isset($ch)) {
                curl_close($ch);
            }
        }
    }

    public function evento_direto(Request $request){
        $lead = [
            'user_id' =>  null,
            'curso_id' => null,
            'nome' => null,
            'telefone' => null,
            'origem' =>null,
            'nome_curso' => null,
        ];
        $this->evento($request, $lead, 'Lead');
    }

    public function testar_pixel(Request $request)
    {

        $user = Auth::user();

        $lead = [
            'nome' => 'name',
            'telefone' => '999999999999',
            'user' => $user,
        ];

        $response = $this->evento($request, $lead);

        //return response()->json($user);
        return response()->json($response);
    }

    public function auto_ads(Request $request)
    {

        if (!Session::has('accessToken')) {
            Session::put('accessToken', null);
        }

        $user = Auth::user();
        $cursos = new Home_e_cursosController();
        $cursos = $cursos->listar_cursos($request, $user);
        //return $cursos; 
        
        //dd($cursos[3]);

        return view('adm.auto_ads.auto_ads', compact('cursos'));

    }

    public function buscarCidades(Request $request)
    {
        $query = $request->get('query');

        // Verifique se a consulta está sendo recebida corretamente
        if (empty($query)) {
            return response()->json(['error' => 'Query is empty'], 400);
        }


        $cidades = Cidades::where('cidade', $query)->limit(10)->get();

        // Execute a busca
        if ($cidades->isEmpty()){
            $cidades = Cidades::where('cidade', 'LIKE', "%{$query}%")->limit(10)->get();
        }

        // Verifique se há resultados
        if ($cidades->isEmpty()) {
            return response()->json(['message' => 'Cidade não encontrada'], 404);
        }

        return response()->json($cidades);
    }

    public function buscarEstado($estado)
    {
        return Cidades::where('estado', $estado)->pluck('id')->toArray();

    }

    public function processarCidades(Request $request) 
    {
        set_time_limit(1800); // 30 minutos em segundos

        date_default_timezone_set('America/Sao_Paulo');
                // Inicializar variáveis
        $testeCidades = [];
        $imagem = [];
        $campaignId = "";
        $resposta = [];

        // Verifica se o token de acesso está presente
        if (!Session::get('accessToken')) {
            return response()->json(['status' => 'error', 'message' => 'Faça o login novamente']);
        }

        // Definir mensagens de validação personalizadas
        $messages = [
            'campanhaObjetivo.required' => 'O campo objetivo da campanha é obrigatório.',
            'cidades.required' => 'Você deve selecionar pelo menos uma cidade.',
            'imagemAnuncio.required' => 'A imagem do anúncio é obrigatória.',
            'imagemAnuncio.image' => 'O arquivo deve ser uma imagem do tipo jpeg, png, jpg, ou webp.',
            'imagemAnuncio.mimes' => 'A imagem deve ser do tipo jpeg, png, jpg, ou webp.',
            'imagemAnuncio.max' => 'A imagem não pode ter mais de 2MB.',
            'datetime.required' => 'A data e hora de início da campanha são obrigatórias.',
            'adtitulo.required' => 'O título do anúncio é obrigatório.',
            'adtexto.required' => 'O texto do anúncio é obrigatório.',
            'url.required' => 'A URL de destino é obrigatória.',
        ];

        // Validar os dados da requisição
        $validator = \Validator::make($request->all(), [
            'campanhaObjetivo' => 'required|string',
            'cidades' => 'required|array',
            'imagemAnuncio' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'datetime' => 'required|date',
            'adtitulo' => 'required|string',
            'adtexto' => 'required|string',
            'url' => 'required|string'
        ], $messages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $formattedErrors = implode(' ', $errors);
            return response()->json(['status' => 'error', 'message' => $formattedErrors]);
        }

        // Definir variáveis do usuário
        $user = Auth::user();
        $this->accessToken = Session::get('accessToken');
        $this->adAccountId = $user->meta_conta_anuncios_id;
        $this->page_id = $user->meta_pagina_id;
        $this->instagram_actor_id = $user->meta_instagram_id;
        $this->pixelId = $user->meta_pixel_id;
                

        

        // Lidar com o upload da imagem
        $file = $request->file('imagemAnuncio');
        $fileExtension = $file->getClientOriginalExtension();
        $uniqueFileName = uniqid('image_', true) . '.' . $fileExtension;
        $uploadFile = $file->storeAs('uploads', $uniqueFileName);

        if ($uploadFile) {
            $request->merge(['cidades' => str_replace("[]", "", $request->input('cidades'))]);
            $cidades = $request->input('cidades');


            $estado = $request->input('estado');

            if (!empty($estado)) {
                // O usuário não selecionou um estado
                $cidades = $this->buscarEstado($estado);
            } 

            

            foreach ($cidades as $cidade) {

                $dadosCidade = Cidades::find($cidade);
                $regionName = $dadosCidade->estado;
                $cityName = $dadosCidade->cidade;

                //return response()->json(['status' => 'error', 'message' =>  $dadosCidade]);
                //exit;

                // Definir dados do usuário
                $adAccountId = $request->input('adAccountId');
                $campanhaObjetivo = $request->input('campanhaObjetivo');
                $pixelId = $request->input('pixelId');
                $tituloAnuncio = $request->input('adtitulo');
                $corpoAnuncio = $request->input('adtexto');
                $url = $request->input('url');
                $page_id = $request->input('page_id');
                $instagram_actor_id = $request->input('instagram_actor_id');
                //$inicio = $request->input('datetime');

                    // Captura o valor do input datetime-local
                $localTime = $request->input('datetime');

                // Converte o horário local para um objeto DateTime em PHP
                $dateTime = new \DateTime($localTime, new \DateTimeZone('America/Sao_Paulo')); // Substitua pelo fuso horário do usuário

                // Converte para UTC
                $dateTime->setTimezone(new \DateTimeZone('UTC'));

                // Formata o horário em UTC como string compatível com a API do Meta
                $inicio = $dateTime->format('Y-m-d H:i:s');

                /** IMAGEM **/
                $baseImage = match (strtolower($fileExtension)) {
                    'jpg', 'jpeg' => imagecreatefromjpeg(storage_path('app/' . $uploadFile)),
                    'png' => imagecreatefrompng(storage_path('app/' . $uploadFile)),
                    'webp' => imagecreatefromwebp(storage_path('app/' . $uploadFile)),
                    default => null
                };

                if (!$baseImage) {
                    return response()->json(['status' => 'error', 'message' => 'Tipo de arquivo não suportado.']);
                }

                // Configurar a cor do texto
                $textColor = imagecolorallocate($baseImage, 255, 255, 255); // Branco
                // Definir a fonte e o tamanho do texto
                $fontPath = public_path('font/Helvetica-Bold.ttf'); // Caminho da fonte TrueType
                $fontSize = 80;

                // Inicialmente, defina a fonte para o maior tamanho desejado
                $maxFontSize = $fontSize;

                // Calcular a largura da imagem
                $imageWidth = imagesx($baseImage);

                // Diminuir o tamanho da fonte até que o texto caiba na largura da imagem


                //$tituloImagem = "ATENÇÃO ".$cityName;
                $cityName = mb_strtoupper($cityName, 'UTF-8');

                
                do {
                    $bbox = imagettfbbox($fontSize, 0, $fontPath, $cityName);
                    $textWidth = $bbox[2] - $bbox[0];
                    if ($textWidth > $imageWidth - 20) {
                        $fontSize--;
                    } else {
                        break;
                    }
                } while ($fontSize > 10);

                // Calcular as coordenadas para centralizar o texto horizontalmente
                $x = ($imageWidth - $textWidth) / 2;
                $y = 50 + $fontSize; // Ajuste a posição vertical conforme necessário

                // Adicionar o texto à imagem
                imagettftext($baseImage, $fontSize, 0, $x, $y, $textColor, $fontPath, $cityName);

                // Salvar a imagem gerada
                $outputPath = 'imgeradas/custom_image_' . uniqid() . '.png';
                imagepng($baseImage, storage_path('app/public/' . $outputPath));

                $this->imagePath = storage_path('app/public/' . $outputPath);

                // Liberar a memória
                imagedestroy($baseImage);

                // Configurar variáveis de campanha
                $conjuntoNome = $regionName . " - " . $cityName;

                
                $this->inicioDataHora = $inicio;
                $this->cityName = $cityName;
                $this->regionName = $regionName;
                
                $this->url = str_replace("{cidade}", $cityName, $url);
                $this->campanhaObjetivo = $campanhaObjetivo;

                // Configurar nome da campanha e evento de conversão
                $conversionEvent = $this->campanhaObjetivo == "OUTCOME_LEADS" ? "LEAD" : "PURCHASE";
                $campanhaNome = $conversionEvent == "LEAD" ? "Carvalho WhatsApp Cadastro - " . date('Y-m-d | h:i:s') : "Carvalho WhatsApp Compra - " . date('Y-m-d | h:i:s');
                $this->campanhaNome = $campanhaNome;
                $this->conjuntoNome = $conjuntoNome;
                $this->conversionEvent = $conversionEvent;
                $this->tituloAnuncio = str_replace("{cidade}", $cityName, $tituloAnuncio);
                $this->corpoAnuncio = str_replace("{cidade}", $cityName, $corpoAnuncio);

                // Criar campanha
                if (empty($campaignId)) {

                    $this->createCampaign();
                    $campaignId = $this->campaignId;

                } else {
                    $this->campaignId = $campaignId;
                }

                if (!empty($this->campaignId)) {
                    $this->getCityId();

                    if (!empty($this->cityId)) {
                        $this->createAdSet();

                        if ($this->conjuntoID) {
                            $this->uploadImage();

                            if ($this->imageHash) {
                                $createAdCreativeResponse = $this->createAdCreative('OPT_IN');
                                if(!$createAdCreativeResponse){$this->createAdCreative('OPT_OUT');}

                                if ($this->adCreativeId) {
                                    $this->createAd();

                                    if ($this->adId) {
                                        $respostaAnuncio = ['status' => 'success', 'message' => "Anúncio criado com sucesso"];
                                    } else {
                                        $respostaAnuncio = ['status' => 'error', 'message' => "Erro ao buscar a cidade: Nome não encontrado ou público muito pequeno"];"Erro na criação do anúncio: Confira o código do Instagram e da página";
                                    }
                                } else {
                                    $respostaAnuncio = ['status' => 'error', 'message' => "Erro ao buscar a cidade: Nome não encontrado ou público muito pequeno"];"Erro no criativo: Confira o código do Instagram e da página";
                                }
                            } else {
                                $respostaAnuncio = ['status' => 'error', 'message' => "Erro ao buscar a cidade: Nome não encontrado ou público muito pequeno"];"Erro no upload da imagem: Tente outro tipo de imagem com resolução menor";
                            }
                        } else {
                            $respostaAnuncio = ['status' => 'error', 'message' => "Erro ao buscar a cidade: Nome não encontrado ou público muito pequeno"];"Erro na criação do conjunto de anúncios: Confira os dados do pixel e do appID";
                        }
                    } else {
                        $respostaAnuncio = ['status' => 'error', 'message' => "Erro ao buscar a cidade: Nome não encontrado ou público muito pequeno"];
                    }
                } else {
                    $respostaAnuncio = ['status' => 'error', 'message' => "Erro ao criar a campanha: Confira os dados do pixel e do appID"];
                }

                // Excluir a imagem gerada
                Storage::delete('public/' . $outputPath);
                
                $resposta[] = $cityName.": ".$respostaAnuncio['message'];
                
                sleep(2);
            
            }

            // Excluir a imagem carregada
            Storage::delete($uploadFile);
            return response()->json(['status' => 'success', 'message' => $resposta]);

        } else {
            return response()->json(['status' => 'error', 'message' => 'Erro ao mover o arquivo carregado.']);
        }
    }

    private function clearImagesDirectory()
    {
        $files = File::files(storage_path('app/public/images'));

        foreach ($files as $file) {
            File::delete($file);
        }
    }

    public function accessToken(Request $request)
    {
        $accessToken = $request->input('accessToken');
        
        if ($accessToken) {
            Session::put('accessToken', $accessToken);
            return response()->json(['status' => 'success']);
        }
        
    }

    private function getEventSourceUrl($http_referer)
    {
        // Defina sua lógica para obter a URL de origem do evento
        return $http_referer;
    }

    private function generateUniqueEventId()
    {
        // Defina sua lógica para gerar um ID único para o evento
        return uniqid();
    }

    public function listar_user_pelo_dominio($dominio)
    {

        //VERIFICAR SE É UM SOBDOMINIO OU DOMINIO COMPRADO
        if($dominio!='portalje.org'){

            $parts = explode('.', $dominio);

            $verificar = User::where('dominio', $dominio)->first();
            

            if($verificar){
                return $verificar;
            }else{
                return 'redirect';
            }

        }else{
            return false;
        }
        
    }

    private function uploadImage() // RETORNA imageHash
    {
        $accessToken = $this->accessToken;
        $adAccountId = $this->adAccountId;
        $imagePath = $this->imagePath;

        $url = "https://graph.facebook.com/v22.0/act_$adAccountId/adimages";

        $data = [
            'access_token' => $accessToken,
            'filename' => new \CURLFile(realpath($imagePath))
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        
        if (isset($result['images'])) {
            $imageHash = array_values($result['images'])[0]['hash'];
            $this->imageHash = $imageHash;
            return true;
        } else {
            return false;
        }
    }

    public function getCityId() //PESQUISAR CIDADE
    {
        $accessToken = $this->accessToken;
        $cityName = $this->cityName;
        $regionName = $this->regionName;

        $url = "https://graph.facebook.com/v22.0/search";
        $params = [
            'type' => 'adgeolocation',
            'location_types' => 'city',
            'q' => $cityName,
            'access_token' => $accessToken
        ];

        $options = [
            CURLOPT_URL => $url . '?' . http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if (isset($result['data'])) {
            foreach ($result['data'] as $dados) {

                if (
                    (strpos($dados['country_code'], "BR") !== false) &&
                    $dados['type'] == "city"
                ) {
                    $this->cityId = $dados['key'];
                    break; // Found the city, no need to continue
                }
            }
        }
    }

    public function createCampaign() //CRIAR CAMPANHA //RETORNA O ID DA CAMPANHA
    {
        $accessToken = $this->accessToken;
        $adAccountId = $this->adAccountId;
        $campanhaNome = $this->campanhaNome;
        $campanhaObjetivo = $this->campanhaObjetivo;

        $url = "https://graph.facebook.com/v22.0/act_$adAccountId/campaigns";

        $data = [
            'name' => $campanhaNome,
            'objective' => $campanhaObjetivo,
            'status' => 'PAUSED',
            'special_ad_categories' => '[]',
            'access_token' => $accessToken
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if (isset($result['id'])) {
            $this->campaignId = $result['id'];
        } else {
            // Adicionar uma forma de lidar com erros, por exemplo:
            throw new \Exception('Erro ao criar a campanha: ' . $response);
        }
    }

    public function createAdSet() //CRIAR CONJUNTO DE ANÚNCIOS
    {
        $accessToken = $this->accessToken;
        $adAccountId = $this->adAccountId;
        $campaignId = $this->campaignId;
        $conjuntoNome = $this->conjuntoNome;
        $cityId = $this->cityId;
        $pixelId = $this->pixelId;
        $conversionEvent = $this->conversionEvent;
        $inicio = $this->inicioDataHora;

        $url = "https://graph.facebook.com/v22.0/act_$adAccountId/adsets";

        $data = [
            'name' => $conjuntoNome,
            'campaign_id' => $campaignId,
            'daily_budget' => 660, // em centavos
            'billing_event' => 'IMPRESSIONS',
            'optimization_goal' => 'OFFSITE_CONVERSIONS',
            'bid_strategy' => 'LOWEST_COST_WITHOUT_CAP',
            'targeting' => json_encode([
                'geo_locations' => [
                    'cities' => [
                        ['key' => $cityId] // Adiciona o cityID aqui
                    ]
                ],
                'device_platforms' => ['mobile']
            ]),
            'promoted_object' => json_encode([
                'pixel_id' => $pixelId,
                'custom_event_type' => $conversionEvent
            ]),
            'status' => 'PAUSED',
            'start_time' => $inicio,
            'is_optimized_for_quality' => false,
            'access_token' => $accessToken
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if (isset($result['id'])) {
            $this->conjuntoID = $result['id'];
        } else {
            // Adicionar uma forma de lidar com erros, por exemplo:
            throw new \Exception('Erro ao criar o conjunto de anúncios: ' . $response);
        }
    }

    public function createAdCreative($enroll_status = 'OPT_IN') //CRIAR O CRIATIVO
    {
        $accessToken = $this->accessToken;
        $adAccountId = $this->adAccountId;
        $nomeCriativo = $this->regionName . " - " . $this->cityName;
        $tituloAnuncio = $this->tituloAnuncio;
        $corpoAnuncio = $this->corpoAnuncio;
        $urlDestino = $this->url;
        $imageHash = $this->imageHash;
        $page_id = $this->page_id;
        $instagram_actor_id = $this->instagram_actor_id;

        $url = "https://graph.facebook.com/v22.0/act_$adAccountId/adcreatives";

        $data = [
            'name' => $nomeCriativo,
            'object_story_spec' => json_encode([
                'page_id' => $page_id,
                'instagram_actor_id' => $instagram_actor_id,
                'link_data' => [
                    'image_hash' => $imageHash,
                    'link' => $urlDestino,
                    'message' => $corpoAnuncio,
                    'name' => $tituloAnuncio,
                    'description' => $corpoAnuncio,
                    'call_to_action' => [
                        'type' => 'LEARN_MORE',
                        'value' => [
                            'link' => $urlDestino
                        ]
                    ]
                ]
            ]),
            'degrees_of_freedom_spec' => json_encode([
                'creative_features_spec' => [
                    'standard_enhancements' => [
                        'enroll_status' => $enroll_status
                    ]
                ]
            ]),
            'access_token' => $accessToken
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if (isset($result['id'])) {
            $this->adCreativeId = $result['id'];
            return true;
        } else {
            return false;
            // Adicionar uma forma de lidar com erros, por exemplo:
            //throw new \Exception('Erro ao criar o criativo: ' . $response);
        }
    }

    public function createAd() //CRIAR ANÚNCIO
    {
        $accessToken = $this->accessToken;
        $adAccountId = $this->adAccountId;
        $adSetId = $this->conjuntoID;
        $adCreativeId = $this->adCreativeId;
        $nomeAnuncio = $this->regionName . " - " . $this->cityName;

        $url = "https://graph.facebook.com/v22.0/act_$adAccountId/ads";

        $data = [
            'name' => $nomeAnuncio,
            'adset_id' => $adSetId,
            'creative' => json_encode(['creative_id' => $adCreativeId]),
            'status' => 'PAUSED',
            'access_token' => $accessToken
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if (isset($result['id'])) {
            $this->adId = $result['id'];
        } else {
            // Adicionar uma forma de lidar com erros, por exemplo:
            throw new \Exception('Erro ao criar o anúncio: ' . $response);
        }
    }    
   
}
