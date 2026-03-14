<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('root_domain_course_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->unique()->constrained('curso')->cascadeOnDelete();
            $table->boolean('mostrar_curso')->default(true);
            $table->boolean('formulario_pre_checkout')->default(true);
            $table->string('modo_precos', 20)->default('padrao');
            $table->foreignId('cupom_principal_id')->nullable()->constrained('cupons')->nullOnDelete();
            $table->foreignId('cupom_secundario_id')->nullable()->constrained('cupons')->nullOnDelete();
            $table->boolean('usar_contador')->default(false);
            $table->unsignedSmallInteger('contador_minutos')->nullable();
            $table->string('contador_acao', 30)->nullable();
            $table->string('contador_destino_oferta', 60)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('root_domain_course_configs');
    }
};
