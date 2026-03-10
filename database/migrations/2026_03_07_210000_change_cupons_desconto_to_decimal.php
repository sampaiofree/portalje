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
        if (!Schema::hasTable('cupons')) {
            return;
        }

        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->recreateCuponsTableForSqlite('REAL');
            return;
        }

        Schema::table('cupons', function (Blueprint $table) {
            $table->decimal('desconto', 5, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('cupons')) {
            return;
        }

        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->recreateCuponsTableForSqlite('INTEGER');
            return;
        }

        Schema::table('cupons', function (Blueprint $table) {
            $table->tinyInteger('desconto', false, true)->change();
        });
    }

    private function recreateCuponsTableForSqlite(string $descontoType): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        try {
            DB::statement('CREATE TABLE cupons_temp (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, codigo VARCHAR(30) NOT NULL, desconto ' . $descontoType . ' NOT NULL, created_at DATETIME NULL, updated_at DATETIME NULL)');
            DB::statement('CREATE UNIQUE INDEX cupons_temp_codigo_unique ON cupons_temp (codigo)');
            DB::statement('INSERT INTO cupons_temp (id, codigo, desconto, created_at, updated_at) SELECT id, codigo, desconto, created_at, updated_at FROM cupons');
            DB::statement('DROP TABLE cupons');
            DB::statement('ALTER TABLE cupons_temp RENAME TO cupons');
        } finally {
            DB::statement('PRAGMA foreign_keys = ON');
        }
    }
};
