<?php

namespace Tests\Feature;

use App\Models\Codigo_ref;
use App\Models\Curso;
use App\Models\PurchaseEvent;
use App\Models\User;
use App\Models\WhatsappAtendimento;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminDashboardActivationFunnelTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_dashboard_calculates_funnel_and_health_metrics_with_status_normalization(): void
    {
        [$admin, $users] = $this->seedHealthScenario();

        $response = $this->actingAs($admin)->get(route('dashboard_adm', [
            'period_scope' => 'last_90_days',
            'date_start' => '2026-03-01',
            'date_end' => '2026-03-31',
        ]));

        $response->assertOk();
        $response->assertDontSee('Admin fora do escopo');
        $response->assertDontSee('Afiliado Fora do Período');

        $funnel = $response->viewData('metrics');
        $this->assertSame(5, $funnel['total_cadastros']);
        $this->assertSame(4, $funnel['com_dominio']);
        $this->assertSame(3, $funnel['com_lead']);
        $this->assertSame(2, $funnel['com_lead_venda']);
        $this->assertSame(4, $funnel['com_produto']);
        $this->assertSame(4, $funnel['com_whatsapp']);

        $health = $response->viewData('healthMetrics');
        $this->assertSame(5, $health['afiliados_filtrados']);
        $this->assertSame(4, $health['setup_completo']);
        $this->assertSame(3, $health['com_lead']);
        $this->assertSame(2, $health['com_venda']);
        $this->assertSame(80.0, $health['taxa_setup']);
        $this->assertSame(60.0, $health['taxa_lead']);
        $this->assertSame(40.0, $health['taxa_venda']);
        $this->assertSame(66.67, $health['taxa_lead_para_venda']);
        $this->assertSame(5.0, $health['mediana_dias_primeiro_lead']);
        $this->assertSame(4.5, $health['mediana_dias_primeira_venda']);

        $response->assertSee($users['setup_sem_lead']->name);
        $response->assertSee('5511999997777');
    }

    public function test_dashboard_operational_queues_respect_days_thresholds(): void
    {
        [$admin, $users] = $this->seedHealthScenario();

        $baseQuery = [
            'period_scope' => 'last_90_days',
            'date_start' => '2026-03-01',
            'date_end' => '2026-03-31',
        ];

        $defaultThresholds = $this->actingAs($admin)->get(route('dashboard_adm', array_merge($baseQuery, [
            'dias_sem_lead' => 7,
            'dias_sem_venda' => 7,
        ])));

        $defaultThresholds->assertOk();
        $setupRows = collect($defaultThresholds->viewData('queueSetupRows')->items());
        $leadRows = collect($defaultThresholds->viewData('queueLeadRows')->items());

        $this->assertSame([$users['setup_sem_lead']->id], $setupRows->pluck('id')->all());
        $this->assertSame([$users['lead_sem_venda']->id], $leadRows->pluck('id')->all());

        $strictThresholds = $this->actingAs($admin)->get(route('dashboard_adm', array_merge($baseQuery, [
            'dias_sem_lead' => 30,
            'dias_sem_venda' => 15,
        ])));

        $strictThresholds->assertOk();
        $this->assertSame(0, $strictThresholds->viewData('queueSetupRows')->total());
        $this->assertSame(0, $strictThresholds->viewData('queueLeadRows')->total());
    }

    public function test_dashboard_export_csv_respects_selected_queue_and_columns(): void
    {
        [$admin, $users] = $this->seedHealthScenario();

        $query = [
            'period_scope' => 'last_90_days',
            'date_start' => '2026-03-01',
            'date_end' => '2026-03-31',
            'dias_sem_lead' => 7,
            'dias_sem_venda' => 7,
        ];

        $leadOnlyResponse = $this->actingAs($admin)->get(route('dashboard_adm_export_csv', array_merge($query, [
            'fila' => 'lead_sem_venda',
        ])));

        $leadOnlyResponse->assertOk();
        $leadOnlyResponse->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $leadOnlyResponse->assertHeader('content-disposition');

        $csvLeadOnly = str_replace("\xEF\xBB\xBF", '', $leadOnlyResponse->streamedContent());
        $this->assertStringContainsString('Nome;"Telefone de contato";Email;"Telefone de atendimento";"Data cadastro";"Dias desde cadastro";"Total leads";"Total vendas";Fila', $csvLeadOnly);
        $this->assertStringContainsString($users['lead_sem_venda']->name, $csvLeadOnly);
        $this->assertStringContainsString('lead_sem_venda', $csvLeadOnly);
        $this->assertStringNotContainsString($users['setup_sem_lead']->name, $csvLeadOnly);

        $allQueuesResponse = $this->actingAs($admin)->get(route('dashboard_adm_export_csv', array_merge($query, [
            'fila' => 'all',
        ])));

        $allQueuesResponse->assertOk();
        $csvAll = str_replace("\xEF\xBB\xBF", '', $allQueuesResponse->streamedContent());
        $this->assertStringContainsString($users['setup_sem_lead']->name, $csvAll);
        $this->assertStringContainsString($users['lead_sem_venda']->name, $csvAll);
        $this->assertStringContainsString('setup_sem_lead', $csvAll);
        $this->assertStringContainsString('lead_sem_venda', $csvAll);
    }

    public function test_dashboard_yes_no_filters_still_drive_results_consistently(): void
    {
        [$admin, $users] = $this->seedHealthScenario();

        $response = $this->actingAs($admin)->get(route('dashboard_adm', [
            'period_scope' => 'last_90_days',
            'date_start' => '2026-03-01',
            'date_end' => '2026-03-31',
            'tem_dominio' => 'no',
            'tem_lead' => 'no',
            'tem_produto' => 'no',
            'tem_whatsapp' => 'no',
        ]));

        $response->assertOk();
        $funnel = $response->viewData('metrics');
        $this->assertSame(1, $funnel['total_cadastros']);
        $this->assertSame(0, $funnel['com_dominio']);
        $this->assertSame(0, $funnel['com_lead']);
        $this->assertSame(0, $funnel['com_lead_venda']);
        $this->assertSame(0, $funnel['com_produto']);
        $this->assertSame(0, $funnel['com_whatsapp']);

        $response->assertSee($users['sem_setup']->name);
        $response->assertDontSee($users['setup_sem_lead']->name);
        $response->assertDontSee($users['lead_sem_venda']->name);
        $response->assertDontSee($users['com_venda_completed']->name);
        $response->assertDontSee($users['com_venda_approved']->name);
    }

    /**
     * @return array{0: User, 1: array<string, User>}
     */
    private function seedHealthScenario(): array
    {
        Carbon::setTestNow(Carbon::parse('2026-04-01 12:00:00'));

        $admin = User::factory()->create([
            'name' => 'Admin principal',
            'email' => 'admin@example.com',
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
            'created_at' => '2026-03-01 10:00:00',
            'updated_at' => '2026-03-01 10:00:00',
        ]);

        User::factory()->create([
            'name' => 'Admin fora do escopo',
            'email' => 'admin2@example.com',
            'nivel_acesso' => User::NIVEL_ACESSO_ADMIN,
            'dominio' => 'admin.portalje.org',
            'whatsapp_atendimento' => '5511999990000',
            'created_at' => '2026-03-10 10:00:00',
            'updated_at' => '2026-03-10 10:00:00',
        ]);

        $comVendaCompleted = User::factory()->create([
            'name' => 'Afiliado Venda Completed',
            'email' => 'afiliado-completed@example.com',
            'nivel_acesso' => User::NIVEL_ACESSO_USER,
            'dominio' => 'completed.portalje.org',
            'telefone_pessoal_1' => '111',
            'whatsapp_atendimento' => '5511999991111',
            'created_at' => '2026-03-01 10:00:00',
            'updated_at' => '2026-03-01 10:00:00',
        ]);

        $setupSemLead = User::factory()->create([
            'name' => 'Afiliado Setup Sem Lead',
            'email' => 'afiliado-setup-sem-lead@example.com',
            'nivel_acesso' => User::NIVEL_ACESSO_USER,
            'dominio_externo' => 'setupsemlead.com.br',
            'telefone_pessoal_2' => '222',
            'created_at' => '2026-03-05 10:00:00',
            'updated_at' => '2026-03-05 10:00:00',
        ]);

        $leadSemVenda = User::factory()->create([
            'name' => 'Afiliado Lead Sem Venda',
            'email' => 'afiliado-lead-sem-venda@example.com',
            'nivel_acesso' => User::NIVEL_ACESSO_USER,
            'dominio' => 'leadsemvenda.portalje.org',
            'telefone_pessoal_1' => '333',
            'whatsapp_atendimento' => '5511999993333',
            'created_at' => '2026-03-10 10:00:00',
            'updated_at' => '2026-03-10 10:00:00',
        ]);

        $semSetup = User::factory()->create([
            'name' => 'Afiliado Sem Setup',
            'email' => 'afiliado-sem-setup@example.com',
            'nivel_acesso' => User::NIVEL_ACESSO_USER,
            'telefone_pessoal_1' => '444',
            'created_at' => '2026-03-12 10:00:00',
            'updated_at' => '2026-03-12 10:00:00',
        ]);

        $comVendaApproved = User::factory()->create([
            'name' => 'Afiliado Venda Approved',
            'email' => 'afiliado-approved@example.com',
            'nivel_acesso' => User::NIVEL_ACESSO_USER,
            'dominio' => 'approved.portalje.org',
            'telefone_pessoal_1' => '555',
            'whatsapp_atendimento' => '5511999995555',
            'created_at' => '2026-03-20 10:00:00',
            'updated_at' => '2026-03-20 10:00:00',
        ]);

        User::factory()->create([
            'name' => 'Afiliado Fora do Período',
            'email' => 'afiliado-fora-periodo@example.com',
            'nivel_acesso' => User::NIVEL_ACESSO_USER,
            'dominio' => 'fora.portalje.org',
            'whatsapp_atendimento' => '5511999999999',
            'created_at' => '2025-12-20 10:00:00',
            'updated_at' => '2025-12-20 10:00:00',
        ]);

        $cursoA = $this->createCurso('a');
        $cursoB = $this->createCurso('b');
        $cursoC = $this->createCurso('c');
        $cursoD = $this->createCurso('d');

        Codigo_ref::create([
            'user_id' => $comVendaCompleted->id,
            'curso_id' => $cursoA->id,
            'codigo_ref' => 'REF-COMPLETED',
            'mostrar_curso' => true,
        ]);

        Codigo_ref::create([
            'user_id' => $setupSemLead->id,
            'curso_id' => $cursoB->id,
            'codigo_ref' => 'REF-SETUP',
            'mostrar_curso' => true,
        ]);

        Codigo_ref::create([
            'user_id' => $leadSemVenda->id,
            'curso_id' => $cursoC->id,
            'codigo_ref' => 'REF-LEAD',
            'mostrar_curso' => true,
        ]);

        Codigo_ref::create([
            'user_id' => $comVendaApproved->id,
            'curso_id' => $cursoD->id,
            'codigo_ref' => 'REF-APPROVED',
            'mostrar_curso' => true,
        ]);

        PurchaseEvent::create([
            'id' => (string) Str::uuid(),
            'affiliate_code' => 'REF-COMPLETED',
            'purchase_status' => 'WAITING_PAYMENT',
            'buyer_name' => 'Lead completed',
            'created_at' => '2026-03-06 12:00:00',
            'updated_at' => '2026-03-06 12:00:00',
        ]);

        PurchaseEvent::create([
            'id' => (string) Str::uuid(),
            'affiliate_code' => 'REF-COMPLETED',
            'purchase_status' => 'completed',
            'buyer_name' => 'Venda completed',
            'created_at' => '2026-03-07 12:00:00',
            'updated_at' => '2026-03-07 12:00:00',
        ]);

        PurchaseEvent::create([
            'id' => (string) Str::uuid(),
            'affiliate_code' => 'REF-LEAD',
            'purchase_status' => 'pending',
            'buyer_name' => 'Lead sem venda',
            'created_at' => '2026-03-18 12:00:00',
            'updated_at' => '2026-03-18 12:00:00',
        ]);

        PurchaseEvent::create([
            'id' => (string) Str::uuid(),
            'affiliate_code' => 'REF-APPROVED',
            'purchase_status' => ' APPROVED ',
            'buyer_name' => 'Venda approved',
            'created_at' => '2026-03-23 12:00:00',
            'updated_at' => '2026-03-23 12:00:00',
        ]);

        WhatsappAtendimento::create([
            'user_id' => $setupSemLead->id,
            'whatsapp' => 5511999997777,
            'is_active' => true,
            'updated_at' => '2026-03-28 12:00:00',
            'created_at' => '2026-03-28 12:00:00',
        ]);

        return [$admin, [
            'com_venda_completed' => $comVendaCompleted,
            'setup_sem_lead' => $setupSemLead,
            'lead_sem_venda' => $leadSemVenda,
            'sem_setup' => $semSetup,
            'com_venda_approved' => $comVendaApproved,
        ]];
    }

    private function createCurso(string $suffix): Curso
    {
        return Curso::create([
            'titulo' => 'Curso ' . strtoupper($suffix),
            'url' => 'curso-' . $suffix . '-' . Str::lower(Str::random(6)),
            'codigo_id_hotmart' => 'HOT-' . strtoupper($suffix),
        ]);
    }
}
