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
        if (Schema::hasTable('aulas_demonstrativas')) {
            return;
        }

        Schema::create('aulas_demonstrativas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_curso')->constrained('curso')->cascadeOnDelete();
            $table->string('aula_titulo');
            $table->string('aula_id_youtube');
            $table->timestamps();

            $table->index('id_curso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aulas_demonstrativas');
    }
};
