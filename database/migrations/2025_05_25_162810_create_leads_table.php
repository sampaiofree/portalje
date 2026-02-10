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
        if (Schema::hasTable('leads')) {
            return;
        }

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('whatsapp');
            $table->integer('idade')->nullable();
            $table->string('cidade')->nullable();
            $table->string('escolaridade')->nullable();
            $table->text('cursos_interesse')->nullable();
            $table->text('cargos_interesse')->nullable();
            $table->boolean('aceita_estudar_online')->default(false);
            $table->boolean('pode_pagar_inscricao')->default(false);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
