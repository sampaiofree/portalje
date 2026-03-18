<?php

namespace Tests\Feature;

use App\Models\Codigo_ref;
use App\Models\Curso;
use App\Models\JourneyStep;
use App\Models\PurchaseEvent;
use App\Models\User;
use App\Models\WhatsappAtendimento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JourneyDashboardRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_user_dashboard_locks_future_steps_and_highlights_current_step(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'telefone_pessoal_1' => '5562999998888',
            'telefone_pessoal_1_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));
        $dashboardJourney = array_values($response->viewData('dashboard_jornada'));
        $legacyJourney = array_values($response->viewData('minha_jornada'));

        $response->assertOk();
        $response->assertSee('Sua Jornada de Sucesso');
        $response->assertSee('Configurar domínio');
        $response->assertSee('Configurar o WhatsApp de atendimento');
        $response->assertSee('Etapa atual');
        $response->assertSee('Bloqueada');

        $this->assertTrue($dashboardJourney[0]['em_foco']);
        $this->assertTrue($dashboardJourney[0]['pode_abrir']);
        $this->assertFalse($dashboardJourney[0]['bloqueado']);
        $this->assertSame('Etapa atual', $dashboardJourney[0]['status_label']);

        $this->assertTrue($dashboardJourney[1]['bloqueado']);
        $this->assertFalse($dashboardJourney[1]['pode_abrir']);
        $this->assertSame('Bloqueada', $dashboardJourney[1]['status_label']);

        $this->assertTrue($legacyJourney[0]['em_foco']);
        $this->assertTrue($legacyJourney[1]['bloqueado']);
    }

    public function test_completing_first_step_unlocks_second_step_and_keeps_later_steps_blocked(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'telefone_pessoal_1' => '5562999998888',
            'telefone_pessoal_1_verified_at' => now(),
            'dominio' => 'teste.portalje.test',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));
        $dashboardJourney = array_values($response->viewData('dashboard_jornada'));

        $response->assertOk();

        $this->assertTrue($dashboardJourney[0]['concluido']);
        $this->assertFalse($dashboardJourney[0]['bloqueado']);

        $this->assertTrue($dashboardJourney[1]['em_foco']);
        $this->assertTrue($dashboardJourney[1]['pode_abrir']);
        $this->assertSame('Etapa atual', $dashboardJourney[1]['status_label']);

        $this->assertTrue($dashboardJourney[2]['bloqueado']);
        $this->assertFalse($dashboardJourney[2]['pode_abrir']);
    }

    public function test_all_completed_dashboard_steps_have_no_locked_or_focused_items(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'telefone_pessoal_1' => '5562999998888',
            'telefone_pessoal_1_verified_at' => now(),
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

        $response = $this->actingAs($user)->get(route('dashboard'));
        $dashboardJourney = array_values($response->viewData('dashboard_jornada'));

        $response->assertOk();

        foreach ($dashboardJourney as $step) {
            $this->assertTrue($step['concluido']);
            $this->assertFalse($step['bloqueado']);
            $this->assertFalse($step['em_foco']);
            $this->assertTrue($step['pode_abrir']);
        }
    }

    public function test_inactive_intermediate_step_does_not_break_unlock_order(): void
    {
        JourneyStep::query()
            ->where('completion_rule', JourneyStep::COMPLETION_RULE_WHATSAPP_CONFIGURED)
            ->update(['is_active' => false]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'telefone_pessoal_1' => '5562999998888',
            'telefone_pessoal_1_verified_at' => now(),
            'dominio' => 'teste.portalje.test',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));
        $dashboardJourney = array_values($response->viewData('dashboard_jornada'));

        $response->assertOk();

        $this->assertCount(4, $dashboardJourney);
        $this->assertSame(JourneyStep::COMPLETION_RULE_DOMAIN_CONFIGURED, JourneyStep::query()->where('id', $dashboardJourney[0]['id'])->value('completion_rule'));
        $this->assertTrue($dashboardJourney[0]['concluido']);
        $this->assertTrue($dashboardJourney[1]['em_foco']);
        $this->assertFalse($dashboardJourney[1]['bloqueado']);
        $this->assertTrue($dashboardJourney[2]['bloqueado']);
    }

    public function test_extra_sales_count_step_is_shown_after_base_steps_and_uses_same_linear_unlock(): void
    {
        JourneyStep::create([
            'title' => 'Faça pelo menos 5 vendas',
            'slug' => 'faca-5-vendas',
            'completion_rule' => JourneyStep::COMPLETION_RULE_SALES_COUNT_AT_LEAST,
            'xp' => 150,
            'sort_order' => 6,
            'is_active' => true,
            'is_fixed' => false,
            'goal_value' => 5,
            'description' => '',
        ]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'telefone_pessoal_1' => '5562999998888',
            'telefone_pessoal_1_verified_at' => now(),
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

        $response = $this->actingAs($user)->get(route('dashboard'));
        $dashboardJourney = array_values($response->viewData('dashboard_jornada'));

        $response->assertOk();
        $this->assertCount(6, $dashboardJourney);
        $this->assertTrue($dashboardJourney[4]['concluido']);
        $this->assertTrue($dashboardJourney[5]['em_foco']);
        $this->assertFalse($dashboardJourney[5]['concluido']);
        $this->assertSame('Faça pelo menos 5 vendas', $dashboardJourney[5]['goal_label']);
    }

    public function test_extra_sales_total_step_concludes_when_total_sum_reaches_goal(): void
    {
        JourneyStep::create([
            'title' => 'Faça pelo menos R$ 300 em vendas',
            'slug' => 'faca-300-vendas',
            'completion_rule' => JourneyStep::COMPLETION_RULE_SALES_TOTAL_AT_LEAST,
            'xp' => 180,
            'sort_order' => 6,
            'is_active' => true,
            'is_fixed' => false,
            'goal_value' => 300,
            'description' => '',
        ]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'telefone_pessoal_1' => '5562999998888',
            'telefone_pessoal_1_verified_at' => now(),
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

        PurchaseEvent::create([
            'event' => 'PURCHASE_APPROVED',
            'affiliate_code' => 'CODIGO-TESTE',
            'buyer_checkout_phone' => '5562991113333',
            'purchase_status' => 'APPROVED',
            'purchase_original_offer_price_value' => 150.00,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));
        $dashboardJourney = array_values($response->viewData('dashboard_jornada'));

        $response->assertOk();
        $this->assertCount(6, $dashboardJourney);
        $this->assertTrue($dashboardJourney[5]['concluido']);
        $this->assertSame('Faça pelo menos R$ 300,00 em vendas', $dashboardJourney[5]['goal_label']);
    }
}
