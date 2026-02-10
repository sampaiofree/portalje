<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class Api_receberController extends Controller
{
    public function usuarios(Request $request)
    {
        Log::info('Requisição recebida', $request->all()); //Olá

        // Obter todos os dados da requisição
        $dados = $request->all();

        // Preparar os dados para inserção
        $novoUsuario = [
            'name' => $dados['user_name'] . " " . $dados['user_lastname'],
            'email' => $dados['user_email'],
            'password' => bcrypt($dados['user_password']), // Criptografar a senha
            'nivel_acesso' => 'user',
            'thumb' => $dados['user_thumb'],
            'cpf' => $dados['user_document'],
            'telefone_pessoal_1' => $dados['user_cell'],
            'telefone_pessoal_2' => $dados['user_cell'],
            'created_at' => $dados['user_registration'],
            'apelido' => $dados['user_apelido'],
            'mentorado' => $dados['user_mentorado'],
            'meta_pixel_id' => $dados['user_aff_pixelfb'],
            'meta_pixel_api' => $dados['pixelFaceAPI'],
            'meta_pixel_eventcode' => $dados['pixeltesteventcode'],
            'google_ads' => $dados['user_aff_pixelggads'],
            'whatsapp_atendimento' => $dados['user_aff_whats'],
            'whatsapp_atendimento_tempo' => $dados['user_aff_domain_whatsapp_tempo'],
            'dominio' => $dados['user_aff_domain'],
            'meta_conta_anuncios_id' => $dados['user_ad_account_id'],
            'meta_pagina_id' => $dados['user_page_id'],
            'meta_instagram_id' => $dados['user_instagram_actor_id'],
            'meta_app_id' => $dados['appId'],
            'faturamento_total' => $dados['user_total_faturamento'],
            'comissao_total' => $dados['user_total_comissao'],
            'numero_total_vendas' => $dados['user_total_vendas']
        ];

        // Armazene os dados no banco de dados
        $data = User::create($novoUsuario);

        return response()->json(['message' => 'Dados recebidos e armazenados com sucesso!', 'data' => $data], 201);
    }
}
