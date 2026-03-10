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
        if (!Schema::hasTable('codigo_ref') || Schema::hasColumn('codigo_ref', 'formulario_pre_checkout')) {
            return;
        }

        Schema::table('codigo_ref', function (Blueprint $table) {
            $table->boolean('formulario_pre_checkout')->default(true)->after('mostrar_curso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('codigo_ref') || !Schema::hasColumn('codigo_ref', 'formulario_pre_checkout')) {
            return;
        }

        Schema::table('codigo_ref', function (Blueprint $table) {
            $table->dropColumn('formulario_pre_checkout');
        });
    }
};
