<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('xp_ranking_snapshots', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->json('rows')->nullable();
            $table->json('totals')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('generated_at')->nullable();
        });

        // The singleton also provides a row lock for atomic publication.
        DB::table('xp_ranking_snapshots')->insert(['id' => 1]);
    }

    public function down(): void
    {
        Schema::dropIfExists('xp_ranking_snapshots');
    }
};
