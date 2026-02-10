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
        Schema::create('combos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo')->nullable();
            $table->string('headline')->nullable();
            $table->text('descricao_curta')->nullable();
            $table->string('url')->nullable();
            $table->text('link_checkout')->nullable();
            $table->string('preco_parcelado')->nullable();
            $table->string('preco')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combos');
    }
};

