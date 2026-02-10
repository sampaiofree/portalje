<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class BotConversa
{
    protected $apiUrl;
    protected $token;

    /*public function __construct()
    {
        $this->apiUrl = env('BOTCONVERSA_URL', 'https://exemplo.com/api');
        $this->token = env('BOTCONVERSA_TOKEN');
    }*/

    public function enviar_webhook($webhook, $dados)
    {
        $response = Http::post($webhook, $dados);
        return $response->json();
    }
}
