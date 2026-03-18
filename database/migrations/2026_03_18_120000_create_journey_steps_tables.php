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
            Schema::create('journey_steps', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('completion_rule', 50);
                $table->unsignedInteger('xp')->default(0);
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index(['is_active', 'sort_order']);
                $table->index('completion_rule');
            });
        }

        if (!Schema::hasTable('journey_step_videos')) {
            Schema::create('journey_step_videos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('journey_step_id')->constrained('journey_steps')->cascadeOnDelete();
                $table->string('title');
                $table->string('youtube_id', 30);
                $table->text('support_html')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_primary')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['journey_step_id', 'is_active', 'sort_order']);
            });
        }

        if (DB::table('journey_steps')->exists()) {
            return;
        }

        $now = now();

        $steps = [
            [
                'title' => 'Configurar domínio',
                'slug' => 'configurar-dominio',
                'completion_rule' => JourneyStep::COMPLETION_RULE_DOMAIN_CONFIGURED,
                'xp' => 100,
                'sort_order' => 1,
                'is_active' => true,
                'description' => 'Configure o domínio principal da sua estrutura.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Configurar o WhatsApp de atendimento',
                'slug' => 'configurar-whatsapp-atendimento',
                'completion_rule' => JourneyStep::COMPLETION_RULE_WHATSAPP_CONFIGURED,
                'xp' => 100,
                'sort_order' => 2,
                'is_active' => true,
                'description' => 'Cadastre e ative pelo menos um WhatsApp de atendimento.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Cadastrar o seu primeiro produto',
                'slug' => 'cadastrar-primeiro-produto',
                'completion_rule' => JourneyStep::COMPLETION_RULE_PRODUCT_ACTIVATED,
                'xp' => 150,
                'sort_order' => 3,
                'is_active' => true,
                'description' => 'Ative seu primeiro produto ou código na sua estrutura.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Conseguir o seu primeiro lead',
                'slug' => 'conseguir-primeiro-lead',
                'completion_rule' => JourneyStep::COMPLETION_RULE_FIRST_LEAD,
                'xp' => 250,
                'sort_order' => 4,
                'is_active' => true,
                'description' => 'Capte o seu primeiro lead com seus links e páginas.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Conseguir a sua primeira venda',
                'slug' => 'conseguir-primeira-venda',
                'completion_rule' => JourneyStep::COMPLETION_RULE_FIRST_SALE,
                'xp' => 400,
                'sort_order' => 5,
                'is_active' => true,
                'description' => 'Conquiste sua primeira venda aprovada para desbloquear o selo.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('journey_steps')->insert($steps);

        $stepIds = DB::table('journey_steps')
            ->pluck('id', 'completion_rule');

        $videos = [
            [
                'journey_step_id' => $stepIds[JourneyStep::COMPLETION_RULE_DOMAIN_CONFIGURED],
                'title' => 'Configurar domínio',
                'youtube_id' => 'DcwJ6yeQMdo',
                'support_html' => '',
                'sort_order' => 1,
                'is_primary' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'journey_step_id' => $stepIds[JourneyStep::COMPLETION_RULE_WHATSAPP_CONFIGURED],
                'title' => 'Configurar o WhatsApp de atendimento',
                'youtube_id' => 'lkRWo6_Lc78',
                'support_html' => '',
                'sort_order' => 1,
                'is_primary' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'journey_step_id' => $stepIds[JourneyStep::COMPLETION_RULE_PRODUCT_ACTIVATED],
                'title' => 'Como cadastrar seus produtos',
                'youtube_id' => 'G3_UjvwizSc',
                'support_html' => "<h5>Links e vídeos mencionados no vídeo</h5><a class='d-block' href='https://www.youtube.com/playlist?list=PL8UPaaNJEdSBxmq-xDp3pFcCEYVEf89Ua' target='_blank'><i class='me-1 ri-youtube-line'></i>O que é um afiliado e Como Funciona a Hotmart?</a><a class='d-block' href='https://sso.hotmart.com/signup' target='_blank'><i class='me-1 ri-external-link-line'></i>Link para fazer o cadastro na Hotmart</a>",
                'sort_order' => 1,
                'is_primary' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'journey_step_id' => $stepIds[JourneyStep::COMPLETION_RULE_PRODUCT_ACTIVATED],
                'title' => 'Como ativar e desativar os cursos?',
                'youtube_id' => 'ToROzX6dq9k',
                'support_html' => '',
                'sort_order' => 2,
                'is_primary' => false,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'journey_step_id' => $stepIds[JourneyStep::COMPLETION_RULE_PRODUCT_ACTIVATED],
                'title' => 'Como funciona o plano básico?',
                'youtube_id' => 'lX5mxC4qUQE',
                'support_html' => '',
                'sort_order' => 3,
                'is_primary' => false,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'journey_step_id' => $stepIds[JourneyStep::COMPLETION_RULE_PRODUCT_ACTIVATED],
                'title' => 'Como corrigir erros no site',
                'youtube_id' => '7xIt3PLPZvw',
                'support_html' => '',
                'sort_order' => 4,
                'is_primary' => false,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'journey_step_id' => $stepIds[JourneyStep::COMPLETION_RULE_FIRST_LEAD],
                'title' => 'Qual link devo usar?',
                'youtube_id' => 'F_RYIH7NpOQ',
                'support_html' => '',
                'sort_order' => 1,
                'is_primary' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'journey_step_id' => $stepIds[JourneyStep::COMPLETION_RULE_FIRST_LEAD],
                'title' => 'Como personalizar os seus links?',
                'youtube_id' => 'pm48QOzVfZA',
                'support_html' => '',
                'sort_order' => 2,
                'is_primary' => false,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'journey_step_id' => $stepIds[JourneyStep::COMPLETION_RULE_FIRST_LEAD],
                'title' => 'Ofereça aulas gratuitas',
                'youtube_id' => 'Y6r6-XtsxMY',
                'support_html' => '',
                'sort_order' => 3,
                'is_primary' => false,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'journey_step_id' => $stepIds[JourneyStep::COMPLETION_RULE_FIRST_LEAD],
                'title' => 'Suporte para os Parceiros',
                'youtube_id' => 'a3pAutrATok',
                'support_html' => '',
                'sort_order' => 4,
                'is_primary' => false,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'journey_step_id' => $stepIds[JourneyStep::COMPLETION_RULE_FIRST_LEAD],
                'title' => 'Anunciar em grupos do WhatsAPP',
                'youtube_id' => 'vI-eMiiWEyk',
                'support_html' => '',
                'sort_order' => 5,
                'is_primary' => false,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'journey_step_id' => $stepIds[JourneyStep::COMPLETION_RULE_FIRST_LEAD],
                'title' => 'Abordagem individual pelo WhatsAPP',
                'youtube_id' => 'lw6nQokHnRo',
                'support_html' => '',
                'sort_order' => 6,
                'is_primary' => false,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'journey_step_id' => $stepIds[JourneyStep::COMPLETION_RULE_FIRST_SALE],
                'title' => 'Consiga Leads de Graça',
                'youtube_id' => 'h45Yh10Yico',
                'support_html' => '',
                'sort_order' => 1,
                'is_primary' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'journey_step_id' => $stepIds[JourneyStep::COMPLETION_RULE_FIRST_SALE],
                'title' => 'Tráfego Pago para Captação de Leads',
                'youtube_id' => 'mfjCq1sT73Y',
                'support_html' => '',
                'sort_order' => 2,
                'is_primary' => false,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('journey_step_videos')->insert($videos);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journey_step_videos');
        Schema::dropIfExists('journey_steps');
    }
};
