<?php

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DataController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\AbandonedCartController;
use App\Http\Controllers\OpenAIController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\ConsultarController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); 

//CONSULTAR
Route::get('/gancho', [ConsultarController::class, 'gancho']);
Route::get('/consultar/cursos/{id?}', [ConsultarController::class, 'consultarCurso']);
Route::get('/consultar/aluno/{nomeCampo}/{conteudoCampo}', [ConsultarController::class, 'consultarAluno']);
Route::post('/consultar/cidade', [ConsultarController::class, 'consultarNovaCidade']); 

//BUSCAR PIXEL DO USER
Route::get('/pixel_user', [UserController::class, 'pixel_user'])->name('pixel_user');

Route::post('/receive-data/user', [DataController::class, 'user']);

Route::post('/receive-data/curso', [DataController::class, 'curso']);

Route::post('/webhook/purchase', [WebhookController::class, 'handle']);
Route::post('/webhook/abandoned-cart', [AbandonedCartController::class, 'handle']);

//BUSCAR DADOS DO LEAD
Route::get('/lead/{whatsapp}', [LeadsController::class, 'buscar_lead_whatsapp']);
Route::post('/lead', [LeadsController::class, 'buscar_lead_whatsapp_post']);

//ROTA OPENAI CHATGPT
Route::post('/ask', [OpenAIController::class, 'ask']);
Route::get('/openai', [OpenAIController::class, 'create_thread']);
Route::post('/openai/adicionar_mensagem', [OpenAIController::class, 'adicionar_mensagem']);
Route::post('/openai/rodar_assistente', [OpenAIController::class, 'rodar_assistente']);
Route::post('/openai/verificar_status', [OpenAIController::class, 'verificar_status']);
Route::post('/openai/receber_mensagem', [OpenAIController::class, 'receber_mensagem']);
Route::post('/openai/bot_conversa', [OpenAIController::class, 'bot_conversa']);
Route::post('/openai/bot_conversa_create_thread', [OpenAIController::class, 'bot_conversa_create_thread']);
Route::post('/openai/receber_mensagem_historico', [OpenAIController::class, 'receber_mensagem_historico']);

