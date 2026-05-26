<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'w3_whatsapp_float_show_home')) {
                $table->boolean('w3_whatsapp_float_show_home')
                    ->default(true)
                    ->after('w3_whatsapp_float_whatsapp_atendimento_id');
            }

            if (!Schema::hasColumn('users', 'w3_whatsapp_float_show_courses')) {
                $table->boolean('w3_whatsapp_float_show_courses')
                    ->default(true)
                    ->after('w3_whatsapp_float_show_home');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'w3_whatsapp_float_show_courses')) {
                $table->dropColumn('w3_whatsapp_float_show_courses');
            }

            if (Schema::hasColumn('users', 'w3_whatsapp_float_show_home')) {
                $table->dropColumn('w3_whatsapp_float_show_home');
            }
        });
    }
};
