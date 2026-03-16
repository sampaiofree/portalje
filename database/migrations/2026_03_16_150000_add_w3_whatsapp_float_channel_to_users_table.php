<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users') || Schema::hasColumn('users', 'w3_whatsapp_float_whatsapp_atendimento_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('w3_whatsapp_float_whatsapp_atendimento_id')
                ->nullable()
                ->after('w3_whatsapp_float_delay_seconds');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'w3_whatsapp_float_whatsapp_atendimento_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('w3_whatsapp_float_whatsapp_atendimento_id');
        });
    }
};
