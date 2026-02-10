<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FacebookConversionsController extends Controller
{
    protected $accessToken;
    protected $pixelId;

    public function __construct()
    {
        $this->accessToken = 'EAAUlyz3RdVYBOZBjp8Ky6QljRnJesn6itwZAAF2YUpv3keyHCiKXkr1Dv6l4MvbhqGJ8W9HMpmQ4VZBHKZBAS98UlZBHM38Is765LCKQ46tHnt0YOzW9MZAZBny48IdTc1rAIbaWdiU9s6upMLtUUZAYZAPiIGFSIl77v9WBHfuVYakj48ZAvLXDA4FTsafBXWrEgeTAZDZD';
        $this->pixelId = '948808649224691';
    }

    public function enviarEvento(Request $request)
    {
        $eventName = $request->input('event_name', 'Lead'); // padrão: Lead
        $eventTime = time();
        $eventId = uniqid();

        $payload = [
            'data' => [
                [
                    'event_name' => $eventName,
                    'event_time' => $eventTime,
                    'event_id' => $eventId,
                    'action_source' => 'website',
                    'user_data' => [
                        'em' => hash('sha256', strtolower(trim($request->email ?? ''))),
                        'ph' => hash('sha256', preg_replace('/\D/', '', $request->whatsapp ?? '')),
                        'client_ip_address' => $request->ip(),
                        'client_user_agent' => $request->header('User-Agent'),
                    ],
                ]
            ]
        ];

        $url = "https://graph.facebook.com/v17.0/{$this->pixelId}/events?access_token={$this->accessToken}";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($url, $payload);

        return response()->json($response->json());
    }

}
