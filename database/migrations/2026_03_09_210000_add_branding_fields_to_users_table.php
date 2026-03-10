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
        if (!Schema::hasTable('users')) {
            return;
        }

        $hasNomeEmpresa = Schema::hasColumn('users', 'nome_empresa');
        $hasLogoPadraoPath = Schema::hasColumn('users', 'logo_padrao_path');
        $hasLogoDarkPath = Schema::hasColumn('users', 'logo_dark_path');

        Schema::table('users', function (Blueprint $table) use ($hasNomeEmpresa, $hasLogoPadraoPath, $hasLogoDarkPath) {
            if (!$hasNomeEmpresa) {
                $table->string('nome_empresa', 120)->nullable()->after('apelido');
            }

            if (!$hasLogoPadraoPath) {
                $table->string('logo_padrao_path')->nullable()->after('nome_empresa');
            }

            if (!$hasLogoDarkPath) {
                $table->string('logo_dark_path')->nullable()->after('logo_padrao_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        $hasNomeEmpresa = Schema::hasColumn('users', 'nome_empresa');
        $hasLogoPadraoPath = Schema::hasColumn('users', 'logo_padrao_path');
        $hasLogoDarkPath = Schema::hasColumn('users', 'logo_dark_path');

        Schema::table('users', function (Blueprint $table) use ($hasNomeEmpresa, $hasLogoPadraoPath, $hasLogoDarkPath) {
            if ($hasLogoDarkPath) {
                $table->dropColumn('logo_dark_path');
            }

            if ($hasLogoPadraoPath) {
                $table->dropColumn('logo_padrao_path');
            }

            if ($hasNomeEmpresa) {
                $table->dropColumn('nome_empresa');
            }
        });
    }
};

