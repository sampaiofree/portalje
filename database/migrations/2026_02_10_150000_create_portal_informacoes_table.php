<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('portal_informacoes')) {
            return;
        }

        Schema::create('portal_informacoes', function (Blueprint $table) {
            $table->id();
            $table->string('telefone_suporte_alunos')->default('5511982671533');
            $table->string('telefone_suporte_afiliados')->nullable();
            $table->string('whatsapp_atendimento_tempo')->default('0');
            $table->boolean('formulario_whatsapp')->default(true);
            $table->boolean('formulario_pre_checkout')->default(true);
            $table->string('endereco')->nullable();
            $table->string('cnpj')->nullable();
            $table->timestamps();
        });

        DB::table('portal_informacoes')->insert([
            'telefone_suporte_alunos' => '5511982671533',
            'telefone_suporte_afiliados' => null,
            'whatsapp_atendimento_tempo' => '0',
            'formulario_whatsapp' => true,
            'formulario_pre_checkout' => true,
            'endereco' => null,
            'cnpj' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portal_informacoes');
    }
};
