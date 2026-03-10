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

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'w3_whatsapp_float_enabled')) {
                $table->boolean('w3_whatsapp_float_enabled')
                    ->default(true)
                    ->after('whatsapp_atendimento_tempo');
            }

            if (!Schema::hasColumn('users', 'w3_whatsapp_float_delay_seconds')) {
                $table->unsignedSmallInteger('w3_whatsapp_float_delay_seconds')
                    ->default(0)
                    ->after('w3_whatsapp_float_enabled');
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

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'w3_whatsapp_float_delay_seconds')) {
                $table->dropColumn('w3_whatsapp_float_delay_seconds');
            }

            if (Schema::hasColumn('users', 'w3_whatsapp_float_enabled')) {
                $table->dropColumn('w3_whatsapp_float_enabled');
            }
        });
    }
};
