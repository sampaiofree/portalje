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
        if (!Schema::hasTable('codigo_ref')) {
            return;
        }

        $hasModoPrecos = Schema::hasColumn('codigo_ref', 'modo_precos');
        $hasCupomPrincipal = Schema::hasColumn('codigo_ref', 'cupom_principal_id');
        $hasCupomSecundario = Schema::hasColumn('codigo_ref', 'cupom_secundario_id');

        Schema::table('codigo_ref', function (Blueprint $table) use ($hasModoPrecos, $hasCupomPrincipal, $hasCupomSecundario) {
            if (!$hasModoPrecos) {
                $table->string('modo_precos', 20)->default('padrao')->after('mostrar_curso');
            }

            if (!$hasCupomPrincipal) {
                $table->foreignId('cupom_principal_id')
                    ->nullable()
                    ->after('modo_precos')
                    ->constrained('cupons')
                    ->nullOnDelete();
            }

            if (!$hasCupomSecundario) {
                $table->foreignId('cupom_secundario_id')
                    ->nullable()
                    ->after('cupom_principal_id')
                    ->constrained('cupons')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('codigo_ref')) {
            return;
        }

        $hasModoPrecos = Schema::hasColumn('codigo_ref', 'modo_precos');
        $hasCupomPrincipal = Schema::hasColumn('codigo_ref', 'cupom_principal_id');
        $hasCupomSecundario = Schema::hasColumn('codigo_ref', 'cupom_secundario_id');

        Schema::table('codigo_ref', function (Blueprint $table) use ($hasModoPrecos, $hasCupomPrincipal, $hasCupomSecundario) {
            if ($hasCupomSecundario) {
                $table->dropConstrainedForeignId('cupom_secundario_id');
            }

            if ($hasCupomPrincipal) {
                $table->dropConstrainedForeignId('cupom_principal_id');
            }

            if ($hasModoPrecos) {
                $table->dropColumn('modo_precos');
            }
        });
    }
};
