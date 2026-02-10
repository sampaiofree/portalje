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
        if (Schema::hasTable('empresas_parceiras')) {
            return;
        }

        Schema::create('empresas_parceiras', function (Blueprint $table) {
            $table->id();
            $table->string('nome_empresa');
            $table->string('nome_responsavel');
            $table->string('telefone_contato');
            $table->string('cidade');
            $table->string('estado', 2);
            $table->text('informacoes_vagas')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas_parceiras');
    }
};
