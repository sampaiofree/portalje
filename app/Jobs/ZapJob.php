<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;

class ZapJob 
{
    use Dispatchable;

    protected $lead;
    protected $tempo;
    protected $texto;
    protected $instanceName;
    protected $type;

    public function __construct($lead, $tempo, $texto, $instanceName, $type)
    {
        $this->lead = $lead;
        $this->tempo = $tempo;
        $this->texto = $texto;
        $this->instanceName = $instanceName;
        $this->type = $type;
    }

    public function handle()
    {
        switch ($this->type) {
            case 'text':
                $this->sendTextMessage();
                break;
            // Outros tipos de mensagens
            default:
                \Log::warning("Tipo de mensagem desconhecido: {$this->type}");
        }
    }

    public function sendTextMessage(){
        $payload = [
            'number' => $this->lead['telefone'],
            'options' => [
                //'delay' => 30,
                //'delay' => (int)$this->tempo,
                'presence' => 'composing',
                'linkPreview' => true,
                'quoted' => null,
                'mentions' => null,
            ],
            'textMessage' => [
                'text' => $this->texto,
            ]
        ];

            // URL da API de envio de mensagens
        $url = "https://evolution.escolaparaafiliados.org/message/sendText/{$this->instanceName}";

        Http::withHeaders([
                'Content-Type' => 'application/json',
                'apikey' => env('EVOLUTION_API_KEY'),
        ])->async()->post($url, $payload);

    }

}
