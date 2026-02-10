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
        Schema::create('combo_cursos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_combo')->constrained('combos')->cascadeOnDelete();
            $table->foreignId('id_curso')->constrained('curso')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['id_combo', 'id_curso']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combo_cursos');
    }
};

