<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('nivel_acesso')->default('user');
            $table->string('thumb')->nullable();
            $table->string('cpf')->nullable();
            $table->string('telefone_pessoal_1')->nullable();
            $table->string('telefone_pessoal_2')->nullable();
            $table->string('apelido')->nullable();
            $table->boolean('mentorado')->default(false);
            $table->string('meta_pixel_id')->nullable();
            $table->text('meta_pixel_api')->nullable();
            $table->string('meta_pixel_eventcode')->nullable();
            $table->text('google_ads')->nullable();
            $table->string('whatsapp_atendimento')->nullable();
            $table->string('whatsapp_atendimento_tempo')->nullable();
            $table->string('dominio')->nullable();
            $table->string('dominio_externo')->nullable();
            $table->string('meta_conta_anuncios_id')->nullable();
            $table->string('meta_pagina_id')->nullable();
            $table->string('meta_instagram_id')->nullable();
            $table->string('meta_app_id')->nullable();
            $table->decimal('faturamento_total', 12, 2)->default(0);
            $table->decimal('comissao_total', 12, 2)->default(0);
            $table->unsignedInteger('numero_total_vendas')->default(0);
            $table->boolean('formulario_whatsapp')->default(true);
            $table->boolean('formulario_pre_checkout')->default(true);
            $table->text('many_api')->nullable();
            $table->string('many_cliente_telefone_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
