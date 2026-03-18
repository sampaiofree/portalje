<?php

namespace Tests\Feature;

use App\Models\JourneyRewardSetting;
use App\Models\JourneyStep;
use App\Models\JourneyStepVideo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JourneyAdminCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_migration_bootstraps_default_journey_content(): void
    {
        $this->assertDatabaseCount('journey_steps', 5);
        $this->assertDatabaseCount('journey_step_videos', 14);
        $this->assertDatabaseCount('journey_reward_settings', 1);
        $this->assertDatabaseHas('journey_steps', [
            'completion_rule' => JourneyStep::COMPLETION_RULE_FIRST_SALE,
            'xp' => 400,
            'is_active' => true,
            'is_fixed' => true,
        ]);
        $this->assertDatabaseHas('journey_reward_settings', [
            'badge_name' => 'Afiliado Ativado',
            'claim_button_label' => 'Resgatar selo',
        ]);
    }

    public function test_admin_can_access_journey_index(): void
    {
        $admin = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.journey.index'));

        $response->assertOk();
        $response->assertSee('Etapas e aulas da jornada');
        $response->assertSee('Prêmio final da jornada');
        $response->assertSee('Configurar domínio');
    }

    public function test_non_admin_is_redirected_when_accessing_journey_index(): void
    {
        $user = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_USER,
        ]);

        $response = $this->actingAs($user)->get(route('admin.journey.index'));

        $response->assertRedirect('/login');
    }

    public function test_admin_can_create_step_and_video(): void
    {
        $admin = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
        ]);

        $stepResponse = $this->actingAs($admin)->post(route('admin.journey.steps.store'), [
            'title' => 'Nova etapa administrável',
            'completion_rule' => JourneyStep::COMPLETION_RULE_SALES_COUNT_AT_LEAST,
            'xp' => 25,
            'sort_order' => 6,
            'goal_value' => 5,
            'description' => 'Descrição nova',
            'is_active' => '0',
        ]);

        $stepResponse->assertRedirect(route('admin.journey.index'));
        $step = JourneyStep::query()->where('title', 'Nova etapa administrável')->firstOrFail();

        $this->assertSame('nova-etapa-administravel', $step->slug);
        $this->assertFalse($step->is_active);
        $this->assertFalse($step->is_fixed);
        $this->assertSame(JourneyStep::COMPLETION_RULE_SALES_COUNT_AT_LEAST, $step->completion_rule);
        $this->assertSame(5.0, (float) $step->goal_value);

        $videoResponse = $this->actingAs($admin)->post(route('admin.journey.videos.store', $step), [
            'title' => 'Vídeo teste',
            'youtube_id' => 'ABCdef12345',
            'support_html' => '<p>apoio</p>',
            'sort_order' => 1,
            'is_primary' => '1',
            'is_active' => '1',
        ]);

        $videoResponse->assertRedirect(route('admin.journey.index'));
        $this->assertDatabaseHas('journey_step_videos', [
            'journey_step_id' => $step->id,
            'title' => 'Vídeo teste',
            'youtube_id' => 'ABCdef12345',
            'is_primary' => true,
            'is_active' => true,
        ]);
    }

    public function test_cannot_create_extra_step_using_fixed_rule(): void
    {
        $admin = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
        ]);

        $response = $this->actingAs($admin)
            ->from(route('admin.journey.index'))
            ->post(route('admin.journey.steps.store'), [
                'title' => 'Duplicada',
                'completion_rule' => JourneyStep::COMPLETION_RULE_DOMAIN_CONFIGURED,
                'xp' => 10,
                'sort_order' => 10,
                'goal_value' => 1,
                'description' => '',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.journey.index'));
        $response->assertSessionHasErrors('completion_rule');
        $this->assertDatabaseMissing('journey_steps', [
            'title' => 'Duplicada',
        ]);
    }

    public function test_admin_cannot_delete_fixed_step(): void
    {
        $admin = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
        ]);

        $fixedStep = JourneyStep::query()
            ->where('completion_rule', JourneyStep::COMPLETION_RULE_DOMAIN_CONFIGURED)
            ->firstOrFail();

        $response = $this->actingAs($admin)
            ->delete(route('admin.journey.steps.destroy', $fixedStep));

        $response->assertRedirect(route('admin.journey.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('journey_steps', [
            'id' => $fixedStep->id,
        ]);
    }

    public function test_admin_cannot_disable_fixed_step(): void
    {
        $admin = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
        ]);

        $fixedStep = JourneyStep::query()
            ->where('completion_rule', JourneyStep::COMPLETION_RULE_FIRST_SALE)
            ->firstOrFail();

        $response = $this->actingAs($admin)
            ->from(route('admin.journey.index'))
            ->put(route('admin.journey.steps.update', $fixedStep), [
                'title' => 'Título alterado',
                'completion_rule' => JourneyStep::COMPLETION_RULE_FIRST_SALE,
                'xp' => 500,
                'sort_order' => $fixedStep->sort_order,
                'description' => 'descrição',
                'is_active' => '0',
            ]);

        $response->assertRedirect(route('admin.journey.index'));
        $response->assertSessionHasErrors('is_active');
    }

    public function test_admin_cannot_change_fixed_step_rule_or_order(): void
    {
        $admin = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
        ]);

        $fixedStep = JourneyStep::query()
            ->where('completion_rule', JourneyStep::COMPLETION_RULE_FIRST_SALE)
            ->firstOrFail();

        $response = $this->actingAs($admin)
            ->from(route('admin.journey.index'))
            ->put(route('admin.journey.steps.update', $fixedStep), [
                'title' => 'Título alterado',
                'completion_rule' => JourneyStep::COMPLETION_RULE_SALES_COUNT_AT_LEAST,
                'xp' => 500,
                'sort_order' => 9,
                'goal_value' => 10,
                'description' => 'descrição',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.journey.index'));
        $response->assertSessionHasErrors('completion_rule');
    }

    public function test_admin_can_edit_fixed_step_title_description_and_xp(): void
    {
        $admin = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
        ]);

        $fixedStep = JourneyStep::query()
            ->where('completion_rule', JourneyStep::COMPLETION_RULE_DOMAIN_CONFIGURED)
            ->firstOrFail();

        $response = $this->actingAs($admin)
            ->put(route('admin.journey.steps.update', $fixedStep), [
                'title' => 'Configurar domínio principal',
                'completion_rule' => $fixedStep->completion_rule,
                'xp' => 130,
                'sort_order' => $fixedStep->sort_order,
                'description' => 'Descrição atualizada',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.journey.index'));
        $fixedStep->refresh();

        $this->assertSame('Configurar domínio principal', $fixedStep->title);
        $this->assertSame(130, $fixedStep->xp);
        $this->assertSame('Descrição atualizada', $fixedStep->description);
        $this->assertTrue($fixedStep->is_fixed);
        $this->assertTrue($fixedStep->is_active);
    }

    public function test_admin_can_create_multiple_extra_steps_with_same_rule_and_different_goal(): void
    {
        $admin = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
        ]);

        $first = $this->actingAs($admin)->post(route('admin.journey.steps.store'), [
            'title' => 'Meta 5 vendas',
            'completion_rule' => JourneyStep::COMPLETION_RULE_SALES_COUNT_AT_LEAST,
            'xp' => 50,
            'sort_order' => 6,
            'goal_value' => 5,
            'description' => '',
            'is_active' => '1',
        ]);
        $first->assertRedirect(route('admin.journey.index'));

        $second = $this->actingAs($admin)->post(route('admin.journey.steps.store'), [
            'title' => 'Meta 10 vendas',
            'completion_rule' => JourneyStep::COMPLETION_RULE_SALES_COUNT_AT_LEAST,
            'xp' => 100,
            'sort_order' => 7,
            'goal_value' => 10,
            'description' => '',
            'is_active' => '1',
        ]);
        $second->assertRedirect(route('admin.journey.index'));

        $this->assertDatabaseHas('journey_steps', [
            'title' => 'Meta 5 vendas',
            'completion_rule' => JourneyStep::COMPLETION_RULE_SALES_COUNT_AT_LEAST,
            'goal_value' => 5.00,
        ]);

        $this->assertDatabaseHas('journey_steps', [
            'title' => 'Meta 10 vendas',
            'completion_rule' => JourneyStep::COMPLETION_RULE_SALES_COUNT_AT_LEAST,
            'goal_value' => 10.00,
        ]);
    }

    public function test_admin_cannot_create_duplicate_extra_step_with_same_rule_and_goal(): void
    {
        $admin = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
        ]);

        $this->actingAs($admin)->post(route('admin.journey.steps.store'), [
            'title' => 'Meta R$1000',
            'completion_rule' => JourneyStep::COMPLETION_RULE_SALES_TOTAL_AT_LEAST,
            'xp' => 90,
            'sort_order' => 6,
            'goal_value' => 1000,
            'description' => '',
            'is_active' => '1',
        ])->assertRedirect(route('admin.journey.index'));

        $response = $this->actingAs($admin)
            ->from(route('admin.journey.index'))
            ->post(route('admin.journey.steps.store'), [
                'title' => 'Meta R$1000 duplicada',
                'completion_rule' => JourneyStep::COMPLETION_RULE_SALES_TOTAL_AT_LEAST,
                'xp' => 110,
                'sort_order' => 7,
                'goal_value' => 1000,
                'description' => '',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.journey.index'));
        $response->assertSessionHasErrors('goal_value');
    }

    public function test_setting_a_new_primary_video_unsets_previous_primary(): void
    {
        $admin = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
        ]);

        $step = JourneyStep::query()
            ->where('completion_rule', JourneyStep::COMPLETION_RULE_FIRST_SALE)
            ->firstOrFail();

        $currentPrimary = $step->videos()->where('is_primary', true)->firstOrFail();

        $response = $this->actingAs($admin)->post(route('admin.journey.videos.store', $step), [
            'title' => 'Nova aula principal',
            'youtube_id' => 'ZYXwv987654',
            'support_html' => '',
            'sort_order' => 3,
            'is_primary' => '1',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.journey.index'));

        $currentPrimary->refresh();

        $newPrimary = JourneyStepVideo::query()
            ->where('journey_step_id', $step->id)
            ->where('youtube_id', 'ZYXwv987654')
            ->firstOrFail();

        $this->assertFalse($currentPrimary->is_primary);
        $this->assertTrue($newPrimary->is_primary);
    }

    public function test_admin_can_update_reward_settings(): void
    {
        $admin = User::factory()->create([
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.journey.reward.update'), [
            'badge_name' => 'Afiliado Lendário',
            'badge_icon' => 'ri-vip-crown-line',
            'pre_claim_text' => 'Você liberou o prêmio.',
            'post_claim_text' => 'Seu selo já está ativo.',
            'claim_button_label' => 'Quero meu selo',
        ]);

        $response->assertRedirect(route('admin.journey.index'));

        $setting = JourneyRewardSetting::query()->firstOrFail();

        $this->assertSame('Afiliado Lendário', $setting->badge_name);
        $this->assertSame('ri-vip-crown-line', $setting->badge_icon);
        $this->assertSame('Você liberou o prêmio.', $setting->pre_claim_text);
        $this->assertSame('Seu selo já está ativo.', $setting->post_claim_text);
        $this->assertSame('Quero meu selo', $setting->claim_button_label);
    }
}
