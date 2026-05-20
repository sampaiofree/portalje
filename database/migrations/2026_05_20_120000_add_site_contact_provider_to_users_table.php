<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users') || Schema::hasColumn('users', 'site_contact_provider')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('site_contact_provider', 20)
                ->default('whatsapp')
                ->after('w3_whatsapp_float_whatsapp_atendimento_id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'site_contact_provider')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('site_contact_provider');
        });
    }
};
