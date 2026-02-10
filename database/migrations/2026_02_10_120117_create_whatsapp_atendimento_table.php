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
        if (Schema::hasTable('whatsapp_atendimento')) {
            return;
        }

        Schema::create('whatsapp_atendimento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('whatsapp');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_lead_at')->nullable();
            $table->timestamps();

            $table->index('is_active');
            $table->index('last_lead_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_atendimento');
    }
};
