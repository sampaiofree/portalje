<?php

use App\Models\JourneyRewardSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('journey_reward_settings')) {
            Schema::create('journey_reward_settings', function (Blueprint $table) {
                $table->id();
                $table->string('badge_name');
                $table->string('badge_icon', 100);
                $table->text('pre_claim_text')->nullable();
                $table->text('post_claim_text')->nullable();
                $table->string('claim_button_label', 100);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('journey_reward_claims')) {
            Schema::create('journey_reward_claims', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
                $table->foreignId('journey_reward_setting_id')->nullable()->constrained('journey_reward_settings')->nullOnDelete();
                $table->timestamp('claimed_at')->nullable();
                $table->timestamps();
            });
        }

        if (DB::table('journey_reward_settings')->exists()) {
            return;
        }

        $defaults = JourneyRewardSetting::defaults();
        $now = now();

        DB::table('journey_reward_settings')->insert([
            'badge_name' => $defaults['badge_name'],
            'badge_icon' => $defaults['badge_icon'],
            'pre_claim_text' => $defaults['pre_claim_text'],
            'post_claim_text' => $defaults['post_claim_text'],
            'claim_button_label' => $defaults['claim_button_label'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journey_reward_claims');
        Schema::dropIfExists('journey_reward_settings');
    }
};
