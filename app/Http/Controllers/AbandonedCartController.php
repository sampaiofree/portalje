<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbandonedCart;
use Illuminate\Support\Facades\Log;
use App\Services\ManyChatService;
use App\Models\Curso;
use Illuminate\Support\Facades\Http;

class AbandonedCartController extends Controller
{
    public function handle(Request $request)
    {
        // Validação básica
        $validatedData = $request->validate([
            'id' => 'required|uuid',
            'creation_date' => 'required|integer',
            'event' => 'required|string',
            'version' => 'required|string',
            'data' => 'required|array',
        ]);

        // Extracting data from the JSON payload
        $data = $validatedData['data'];
        $product = $data['product'];
        $buyer = $data['buyer'];
        $offer = $data['offer'];
        $checkout_country = $data['checkout_country'];

        // Create the abandoned cart entry
        $abandonedCart = AbandonedCart::updateOrCreate(
            ['id' => $validatedData['id']],
            [
            'creation_date' => $validatedData['creation_date'],
            'event' => $validatedData['event'],
            'version' => $validatedData['version'],
            'affiliate' => $data['affiliate'] ?? null,
            'product_id' => $product['id'] ?? null,
            'product_name' => $product['name'] ?? null,
            'buyer_name' => $buyer['name'] ?? null,
            'buyer_email' => $buyer['email'] ?? null,
            'buyer_phone' => $buyer['phone'] ?? null,
            'offer_code' => $offer['code'] ?? null,
            'checkout_country_name' => $checkout_country['name'] ?? null,
            'checkout_country_iso' => $checkout_country['iso'] ?? null, 
        ]);

         //ENVIAR PARA 3F7 EVOLUTION
         $phone = preg_replace('/\D/', '', $buyer['phone']) ?? '';
         $nomeCliente = $buyer['name'] ?? '';
         $purchaseEvent = $request->all();
         if(!$phone){
            return true;
         }
         $payload = [
                    "instance" => '283',
                    "data" => [
                        "key" => [
                            "remoteJid" => "{$phone}@s.whatsapp.net",
                            "fromMe" => false,
                        ],
                        "pushName" => "Bruno Sampaio",
                        "message" => [
                            "conversation" => "[Notificação de transação da Hotmart:Abandono de Carrinho. Cliente: {$nomeCliente}. Dê boas vindas, caso seja a primeira interação e inicie uma conversa de acordo com a transação][Demais informações do Webhook da Hotmart: ".json_encode($purchaseEvent)."]",
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
            //Http::post('https://app.3f7.org/api/conversation', $payload);   

        return true;
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
}
