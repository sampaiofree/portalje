<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('codigo_ref')) {
            return;
        }

        $hasUsarContador = Schema::hasColumn('codigo_ref', 'usar_contador');
        $hasContadorMinutos = Schema::hasColumn('codigo_ref', 'contador_minutos');
        $hasContadorAcao = Schema::hasColumn('codigo_ref', 'contador_acao');
        $hasContadorDestinoOferta = Schema::hasColumn('codigo_ref', 'contador_destino_oferta');

        Schema::table('codigo_ref', function (Blueprint $table) use (
            $hasUsarContador,
            $hasContadorMinutos,
            $hasContadorAcao,
            $hasContadorDestinoOferta
        ) {
            if (!$hasUsarContador) {
                $table->boolean('usar_contador')->default(false)->after('cupom_secundario_id');
            }

            if (!$hasContadorMinutos) {
                $table->unsignedSmallInteger('contador_minutos')->nullable()->after('usar_contador');
            }

            if (!$hasContadorAcao) {
                $table->string('contador_acao', 30)->nullable()->after('contador_minutos');
            }

            if (!$hasContadorDestinoOferta) {
                $table->string('contador_destino_oferta', 60)->nullable()->after('contador_acao');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('codigo_ref')) {
            return;
        }

        $hasUsarContador = Schema::hasColumn('codigo_ref', 'usar_contador');
        $hasContadorMinutos = Schema::hasColumn('codigo_ref', 'contador_minutos');
        $hasContadorAcao = Schema::hasColumn('codigo_ref', 'contador_acao');
        $hasContadorDestinoOferta = Schema::hasColumn('codigo_ref', 'contador_destino_oferta');

        Schema::table('codigo_ref', function (Blueprint $table) use (
            $hasUsarContador,
            $hasContadorMinutos,
            $hasContadorAcao,
            $hasContadorDestinoOferta
        ) {
            $columns = [];

            if ($hasContadorDestinoOferta) {
                $columns[] = 'contador_destino_oferta';
            }

            if ($hasContadorAcao) {
                $columns[] = 'contador_acao';
            }

            if ($hasContadorMinutos) {
                $columns[] = 'contador_minutos';
            }

            if ($hasUsarContador) {
                $columns[] = 'usar_contador';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
