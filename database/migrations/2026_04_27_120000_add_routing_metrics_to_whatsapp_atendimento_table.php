<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('whatsapp_atendimento')) {
            return;
        }

        Schema::table('whatsapp_atendimento', function (Blueprint $table) {
            if (!Schema::hasColumn('whatsapp_atendimento', 'last_routed_at')) {
                $table->timestamp('last_routed_at')->nullable()->after('last_lead_at');
                $table->index('last_routed_at');
            }

            if (!Schema::hasColumn('whatsapp_atendimento', 'routed_count')) {
                $table->unsignedInteger('routed_count')->default(0)->after('last_routed_at');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('whatsapp_atendimento')) {
            return;
        }

        Schema::table('whatsapp_atendimento', function (Blueprint $table) {
            if (Schema::hasColumn('whatsapp_atendimento', 'last_routed_at')) {
                $table->dropIndex(['last_routed_at']);
                $table->dropColumn('last_routed_at');
            }

            if (Schema::hasColumn('whatsapp_atendimento', 'routed_count')) {
                $table->dropColumn('routed_count');
            }
        });
    }
};
