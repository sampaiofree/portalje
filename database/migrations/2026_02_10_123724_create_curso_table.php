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
        Schema::create('curso', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('ordem')->default(0);
            $table->string('codigo_id_hotmart')->nullable();
            $table->string('url')->unique();
            $table->boolean('gratuito')->default(false);
            $table->boolean('publicado')->default(true);
            $table->boolean('permitir_afiliacao')->default(true);
            $table->boolean('mostrar_na_pagina')->default(true);
            $table->string('categorias')->nullable();
            $table->string('titulo');
            $table->text('headline')->nullable();
            $table->longText('beneficios_lista')->nullable();
            $table->text('descricao_curta')->nullable();
            $table->longText('conteudo_principal')->nullable();
            $table->longText('conteudo_bonus')->nullable();
            $table->string('capa_quadrada')->nullable();
            $table->string('capa_vertical')->nullable();
            $table->string('capa_horizontal')->nullable();
            $table->decimal('nota_avaliacao', 3, 1)->default(0);
            $table->unsignedInteger('numero_alunos')->default(0);
            $table->text('areas_de_atuacao')->nullable();
            $table->decimal('salario_maximo', 10, 2)->nullable();
            $table->string('video_dentro_do_curso')->nullable();
            $table->string('video_apresentacao')->nullable();
            $table->text('link_checkout_basico')->nullable();
            $table->text('link_checkout_completo')->nullable();
            $table->string('codigo_afiliado_plano_basico')->nullable();
            $table->string('codigo_afiliado_plano_completo')->nullable();
            $table->string('preco_parcelado_basico')->nullable();
            $table->string('preco_parcelado_completo')->nullable();
            $table->string('preco_cheio_basico')->nullable();
            $table->string('preco_cheio_completo')->nullable();
            $table->unsignedInteger('horas_basico')->nullable();
            $table->unsignedInteger('horas_completo')->nullable();
            $table->text('link_materiais')->nullable();
            $table->text('link_afiliacao')->nullable();
            $table->text('link_area_membros')->nullable();
            $table->string('professor_nome')->nullable();
            $table->text('professor_biografia')->nullable();
            $table->string('professor_foto')->nullable();
            $table->timestamps();

            $table->index(['publicado', 'permitir_afiliacao']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curso');
    }
};
