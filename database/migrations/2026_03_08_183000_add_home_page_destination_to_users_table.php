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
        if (!Schema::hasTable('users') || Schema::hasColumn('users', 'home_page_destination')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('home_page_destination', 20)
                ->default('curso')
                ->after('home_page_layout');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'home_page_destination')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('home_page_destination');
        });
    }
};
