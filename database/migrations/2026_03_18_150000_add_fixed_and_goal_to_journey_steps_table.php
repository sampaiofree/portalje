<?php

use App\Models\JourneyStep;
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
        if (!Schema::hasTable('journey_steps')) {
            return;
        }

        Schema::table('journey_steps', function (Blueprint $table) {
            if (!Schema::hasColumn('journey_steps', 'is_fixed')) {
                $table->boolean('is_fixed')->default(false)->after('is_active');
            }

            if (!Schema::hasColumn('journey_steps', 'goal_value')) {
                $table->decimal('goal_value', 12, 2)->nullable()->after('is_fixed');
            }
        });

        $fixedRules = JourneyStep::fixedCompletionRules();
        $fixedOrders = array_combine($fixedRules, [1, 2, 3, 4, 5]);
        $fixedDefinitions = [
            JourneyStep::COMPLETION_RULE_DOMAIN_CONFIGURED => [
                'title' => 'Configurar domínio',
                'slug' => 'configurar-dominio',
                'xp' => 100,
                'description' => 'Configure o domínio principal da sua estrutura.',
            ],
            JourneyStep::COMPLETION_RULE_WHATSAPP_CONFIGURED => [
                'title' => 'Configurar o WhatsApp de atendimento',
                'slug' => 'configurar-whatsapp-atendimento',
                'xp' => 100,
                'description' => 'Cadastre e ative pelo menos um WhatsApp de atendimento.',
            ],
            JourneyStep::COMPLETION_RULE_PRODUCT_ACTIVATED => [
                'title' => 'Cadastrar o seu primeiro produto',
                'slug' => 'cadastrar-primeiro-produto',
                'xp' => 150,
                'description' => 'Ative seu primeiro produto ou código na sua estrutura.',
            ],
            JourneyStep::COMPLETION_RULE_FIRST_LEAD => [
                'title' => 'Conseguir o seu primeiro lead',
                'slug' => 'conseguir-primeiro-lead',
                'xp' => 250,
                'description' => 'Capte o seu primeiro lead com seus links e páginas.',
            ],
            JourneyStep::COMPLETION_RULE_FIRST_SALE => [
                'title' => 'Conseguir a sua primeira venda',
                'slug' => 'conseguir-primeira-venda',
                'xp' => 400,
                'description' => 'Conquiste sua primeira venda aprovada para desbloquear o selo.',
            ],
        ];
        $now = now();

        foreach ($fixedOrders as $rule => $order) {
            $fixedStep = DB::table('journey_steps')
                ->where('completion_rule', $rule)
                ->orderBy('id')
                ->first();

            if ($fixedStep) {
                DB::table('journey_steps')
                    ->where('id', $fixedStep->id)
                    ->update([
                        'is_fixed' => true,
                        'is_active' => true,
                        'sort_order' => $order,
                        'goal_value' => null,
                    ]);
                continue;
            }

            $definition = $fixedDefinitions[$rule] ?? null;
            if (!$definition) {
                continue;
            }

            $baseSlug = $definition['slug'];
            $slug = $baseSlug;
            $index = 2;
            while (DB::table('journey_steps')->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $index;
                $index++;
            }

            DB::table('journey_steps')->insert([
                'title' => $definition['title'],
                'slug' => $slug,
                'completion_rule' => $rule,
                'xp' => $definition['xp'],
                'sort_order' => $order,
                'is_active' => true,
                'is_fixed' => true,
                'goal_value' => null,
                'description' => $definition['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        Schema::table('journey_steps', function (Blueprint $table) {
            $table->unique(
                ['completion_rule', 'goal_value', 'is_fixed'],
                'journey_steps_rule_goal_fixed_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('journey_steps')) {
            return;
        }

        Schema::table('journey_steps', function (Blueprint $table) {
            if (Schema::hasColumn('journey_steps', 'goal_value')) {
                $table->dropUnique('journey_steps_rule_goal_fixed_unique');
                $table->dropColumn('goal_value');
            }

            if (Schema::hasColumn('journey_steps', 'is_fixed')) {
                $table->dropColumn('is_fixed');
            }
        });
    }
};
