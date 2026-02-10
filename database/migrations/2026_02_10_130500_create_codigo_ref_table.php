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
        if (Schema::hasTable('codigo_ref')) {
            return;
        }

        Schema::create('codigo_ref', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('curso_id')->constrained('curso')->cascadeOnDelete();
            $table->string('codigo_ref', 30);
            $table->boolean('mostrar_curso')->default(true);
            $table->timestamps();

            $table->index('codigo_ref');
            $table->unique(['user_id', 'curso_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codigo_ref');
    }
};
