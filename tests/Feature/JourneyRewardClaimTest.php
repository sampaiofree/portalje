<?php

namespace Tests\Feature;

use App\Models\Codigo_ref;
use App\Models\Curso;
use App\Models\JourneyRewardClaim;
use App\Models\JourneyRewardSetting;
use App\Models\JourneyStep;
use App\Models\PurchaseEvent;
use App\Models\User;
use App\Models\WhatsappAtendimento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JourneyRewardClaimTest extends TestCase
{
    use RefreshDatabase;

    public function test_incomplete_user_cannot_claim_reward(): void
    {
        $user = $this->createVerifiedUser();

        $dashboardResponse = $this->actingAs($user)->get(route('dashboard'));

        $dashboardResponse->assertOk();
        $dashboardResponse->assertDontSee('Resgatar selo');
        $this->assertFalse($dashboardResponse->viewData('journey_reward')['can_claim']);

        $claimResponse = $this->actingAs($user)
            ->from(route('dashboard'))
            ->post(route('journey.reward.claim'));

        $claimResponse->assertRedirect(route('dashboard'));
        $claimResponse->assertSessionHas('error');
        $this->assertDatabaseCount('journey_reward_claims', 0);
    }

    public function test_complete_user_sees_claim_button_and_can_claim_reward_once(): void
    {
        $user = $this->createUnlockedUser();

        $dashboardResponse = $this->actingAs($user)->get(route('dashboard'));

        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Resgatar selo');
        $this->assertTrue($dashboardResponse->viewData('journey_reward')['can_claim']);

        $firstClaim = $this->actingAs($user)
            ->from(route('dashboard'))
            ->post(route('journey.reward.claim'));

        $firstClaim->assertRedirect(route('dashboard'));
        $firstClaim->assertSessionHas('success');
        $this->assertDatabaseCount('journey_reward_claims', 1);

        $secondClaim = $this->actingAs($user)
            ->from(route('dashboard'))
            ->post(route('journey.reward.claim'));

        $secondClaim->assertRedirect(route('dashboard'));
        $secondClaim->assertSessionHas('success');
        $this->assertDatabaseCount('journey_reward_claims', 1);

        $rewardClaim = JourneyRewardClaim::query()->where('user_id', $user->id)->firstOrFail();
        $this->assertNotNull($rewardClaim->claimed_at);
    }

    public function test_claimed_reward_remains_visible_even_if_user_loses_progress_later(): void
    {
        $user = $this->createUnlockedUser();

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->post(route('journey.reward.claim'))
            ->assertRedirect(route('dashboard'));

        $user->update([
            'dominio' => null,
            'dominio_externo' => null,
        ]);

        $dashboardResponse = $this->actingAs($user)->get(route('dashboard'));
        $journeyReward = $dashboardResponse->viewData('journey_reward');

        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Selo resgatado');
        $this->assertTrue($journeyReward['claimed']);
        $this->assertFalse($journeyReward['can_claim']);
        $this->assertFalse($journeyReward['unlocked']);
    }

    public function test_dashboard_handles_missing_reward_configuration_without_breaking(): void
    {
        JourneyRewardSetting::query()->delete();

        $user = $this->createVerifiedUser();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $this->assertFalse($response->viewData('journey_reward')['configured']);
    }

    public function test_reward_unlock_remains_based_on_fixed_base_steps_when_extra_step_exists(): void
    {
        JourneyStep::create([
            'title' => 'Faça pelo menos 10 vendas',
            'slug' => 'meta-10-vendas',
            'completion_rule' => JourneyStep::COMPLETION_RULE_SALES_COUNT_AT_LEAST,
            'xp' => 200,
            'sort_order' => 6,
            'is_active' => true,
            'is_fixed' => false,
            'goal_value' => 10,
            'description' => '',
        ]);

        $user = $this->createUnlockedUser();
        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $this->assertTrue($response->viewData('journey_reward')['can_claim']);
        $this->assertTrue($response->viewData('dashboard_jornada_base_summary')['reward_unlocked']);
        $this->assertFalse($response->viewData('dashboard_jornada_full_summary')['reward_unlocked']);
    }

    private function createVerifiedUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'email_verified_at' => now(),
            'telefone_pessoal_1' => '5562999998888',
            'telefone_pessoal_1_verified_at' => now(),
        ], $attributes));
    }

    private function createUnlockedUser(): User
    {
        $user = $this->createVerifiedUser([
            'dominio' => 'teste.portalje.test',
        ]);

        WhatsappAtendimento::create([
            'user_id' => $user->id,
            'whatsapp' => '5562999999999',
            'is_active' => true,
        ]);

        $curso = Curso::create([
            'ordem' => 1,
            'url' => 'curso-teste',
            'titulo' => 'Curso Teste',
        ]);

        Codigo_ref::create([
            'codigo_ref' => 'CODIGO-TESTE',
            'curso_id' => $curso->id,
            'user_id' => $user->id,
            'mostrar_curso' => true,
        ]);

        PurchaseEvent::create([
            'event' => 'PURCHASE_APPROVED',
            'affiliate_code' => 'CODIGO-TESTE',
            'buyer_checkout_phone' => '5562991112222',
            'purchase_status' => 'APPROVED',
            'purchase_original_offer_price_value' => 197.00,
        ]);

        return $user;
    }
}
