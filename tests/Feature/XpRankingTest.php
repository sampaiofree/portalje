<?php

namespace Tests\Feature;

use App\Console\Commands\RebuildXpRanking;
use App\Http\Middleware\MinhaJornada;
use App\Models\Codigo_ref;
use App\Models\Curso;
use App\Models\JourneyStep;
use App\Models\PurchaseEvent;
use App\Models\User;
use App\Models\WhatsappAtendimento;
use App\Models\XpRankingSnapshot;
use App\Services\JourneyProgressService;
use App\Services\XpRankingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

class XpRankingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Exercise the command's real lock lifecycle without external services.
        config(['cache.stores.redis' => ['driver' => 'array', 'serialize' => false]]);
    }

    public function test_batch_scores_match_dashboard_rules_and_include_all_profile_fields(): void
    {
        foreach ([['sales_count_at_least', 2, 125], ['sales_total_at_least', 300, 175], ['sales_count_at_least', 0, 999]] as [$rule, $goal, $xp]) {
            JourneyStep::create([
                'title' => $rule.$goal, 'slug' => $rule.$goal, 'completion_rule' => $rule,
                'goal_value' => $goal, 'xp' => $xp, 'is_active' => true, 'is_fixed' => false,
            ]);
        }
        JourneyStep::create([
            'title' => 'Inativa', 'slug' => 'inativa', 'completion_rule' => 'domain_configured',
            'xp' => 9000, 'is_active' => false,
        ]);
        $users = collect([
            User::factory()->create(),
            User::factory()->create(['dominio' => 'dominio.test']),
            User::factory()->create(['dominio_externo' => 'externo.test']),
            User::factory()->create(['whatsapp_atendimento' => '5511999999999']),
            User::factory()->create(['whatsapp_atendimento' => '   ']),
        ]);
        foreach ([true, false] as $active) {
            $user = User::factory()->create();
            WhatsappAtendimento::create(['user_id' => $user->id, 'whatsapp' => '5511999999999', 'is_active' => $active]);
            $users->push($user);
        }
        $course = Curso::create(['ordem' => 1, 'url' => 'ranking', 'titulo' => 'Ranking']);
        foreach (['product', 'lead', 'sale', 'goals'] as $kind) {
            $user = User::factory()->create();
            $users->push($user);
            Codigo_ref::create(['user_id' => $user->id, 'curso_id' => $course->id, 'codigo_ref' => $kind, 'mostrar_curso' => $kind === 'product']);
            if ($kind !== 'product') {
                PurchaseEvent::create([
                    'affiliate_code' => $kind, 'purchase_status' => $kind === 'lead' ? 'CANCELED' : 'APPROVED',
                    'purchase_original_offer_price_value' => 150,
                ]);
            }
            if ($kind === 'goals') {
                PurchaseEvent::create(['affiliate_code' => $kind, 'purchase_status' => 'COMPLETED', 'purchase_original_offer_price_value' => 150]);
                PurchaseEvent::create(['affiliate_code' => $kind, 'purchase_status' => 'CANCELED', 'purchase_original_offer_price_value' => 10000]);
            }
        }

        $this->rebuild();
        $dataset = app(XpRankingService::class)->publishedDataset();
        $rows = collect($dataset['rows'])->keyBy('user_id');
        foreach ($users as $user) {
            $expected = app(JourneyProgressService::class)->buildForUser($user)['dashboard_jornada_summary']['xp_total'];
            $this->assertSame($expected, $rows[$user->id]['xp_total'] ?? 0, 'User '.$user->id);
        }
        $this->assertSame(100, $rows[$users[1]->id]['xp_total']);
        $this->assertSame(100, $rows[$users[2]->id]['xp_total']);
        $this->assertSame(100, $rows[$users[3]->id]['xp_total']);
        $this->assertSame(950, $rows[$users->last()->id]['xp_total']);
        $this->assertNotNull($dataset['generated_at']);
    }

    public function test_duplicate_codes_preserve_the_existing_sales_join_semantics(): void
    {
        $user = User::factory()->create();
        JourneyStep::create([
            'title' => 'Duas vendas', 'slug' => 'duas', 'completion_rule' => 'sales_count_at_least',
            'goal_value' => 2, 'xp' => 123, 'is_active' => true,
        ]);
        foreach ([1, 2] as $i) {
            $course = Curso::create(['ordem' => $i, 'url' => 'duplicado'.$i, 'titulo' => 'Curso']);
            Codigo_ref::create(['user_id' => $user->id, 'curso_id' => $course->id, 'codigo_ref' => 'SHARED', 'mostrar_curso' => false]);
        }
        PurchaseEvent::create(['affiliate_code' => 'SHARED', 'purchase_status' => 'APPROVED', 'purchase_original_offer_price_value' => 10]);
        $this->rebuild();
        $expected = app(JourneyProgressService::class)->buildForUser($user)['dashboard_jornada_summary']['xp_total'];
        $this->assertSame(773, $expected);
        $this->assertSame($expected, app(XpRankingService::class)->publishedDataset()['rows'][0]['xp_total']);
    }

    public function test_top_100_and_global_totals_have_deterministic_ties_and_display_names(): void
    {
        $first = User::factory()->create(['dominio' => 'a.test', 'apelido' => '  Ana  ']);
        $second = User::factory()->create(['dominio' => 'a.test', 'apelido' => 'ana']);
        $fallback = User::factory()->create(['dominio' => 'a.test', 'apelido' => ' ', 'name' => 'Beatriz Silva']);
        $nameless = User::factory()->create(['dominio' => 'a.test', 'apelido' => null, 'name' => ' ']);
        User::factory()->count(101)->create(['dominio' => 'a.test', 'apelido' => 'ZZZ']);
        User::factory()->create();

        $this->rebuild();
        $dataset = app(XpRankingService::class)->publishedDataset();
        $this->assertCount(100, $dataset['rows']);
        $this->assertSame([$first->id, $second->id, $fallback->id, $nameless->id], array_column(array_slice($dataset['rows'], 0, 4), 'user_id'));
        $this->assertSame(['Ana', 'ana', 'Beatriz', 'Sem nome'], array_column(array_slice($dataset['rows'], 0, 4), 'display_name'));
        $this->assertSame(range(1, 100), array_column($dataset['rows'], 'position'));
        $this->assertSame(['participants' => 105, 'xp_total' => 10500, 'xp_average' => 100], $dataset['totals']);
    }

    public function test_failed_publication_keeps_the_complete_previous_snapshot_visible(): void
    {
        $user = User::factory()->create(['dominio' => 'a.test']);
        $this->rebuild();
        $before = app(XpRankingService::class)->publishedDataset();
        $user->update(['whatsapp_atendimento' => '5511999999999']);
        $checks = 0;

        try {
            app(XpRankingService::class)->rebuild(function () use (&$checks, $before) {
                $this->assertSame($before, app(XpRankingService::class)->publishedDataset());
                if (++$checks === 2) {
                    throw new RuntimeException('Lease perdida antes da publicação');
                }
            });
            $this->fail('A publicação deveria falhar.');
        } catch (RuntimeException $e) {
            $this->assertSame('Lease perdida antes da publicação', $e->getMessage());
        }
        $this->assertSame($before, app(XpRankingService::class)->publishedDataset());
        $this->rebuild();
        $this->assertSame(200, app(XpRankingService::class)->publishedDataset()['rows'][0]['xp_total']);
        $this->assertDatabaseCount('xp_ranking_snapshots', 1);
    }

    public function test_missing_required_table_fails_command_and_preserves_snapshot(): void
    {
        User::factory()->create(['dominio' => 'a.test']);
        $this->rebuild();
        $before = app(XpRankingService::class)->publishedDataset();
        Schema::rename('purchase_events', 'purchase_events_unavailable');
        try {
            $this->artisan('ranking:xp:rebuild')->expectsOutputToContain('Tabela obrigatória ausente: purchase_events')->assertFailed();
        } finally {
            Schema::rename('purchase_events_unavailable', 'purchase_events');
        }
        $this->assertSame($before, app(XpRankingService::class)->publishedDataset());
        $this->assertTrue(Cache::store('redis')->lock(RebuildXpRanking::LOCK_KEY, 60)->get());
    }

    public function test_command_rejects_concurrent_execution_and_releases_its_own_lock_on_success(): void
    {
        $lock = Cache::store('redis')->lock(RebuildXpRanking::LOCK_KEY, 60);
        $this->assertTrue($lock->get());
        $this->artisan('ranking:xp:rebuild')->expectsOutputToContain('Já existe uma apuração')->assertFailed();
        $this->assertTrue($lock->isOwnedByCurrentProcess());
        $this->assertNull(XpRankingSnapshot::findOrFail(1)->generated_at);
        $lock->release();

        $this->artisan('ranking:xp:rebuild')->expectsOutputToContain('users_processed')->assertSuccessful();
        $next = Cache::store('redis')->lock(RebuildXpRanking::LOCK_KEY, 60);
        $this->assertTrue($next->get());
        $next->release();
    }

    public function test_lost_redis_lock_cannot_publish_and_command_returns_failure(): void
    {
        $lock = \Mockery::mock(\Illuminate\Cache\RedisLock::class);
        $lock->shouldReceive('get')->once()->andReturnTrue();
        $lock->shouldReceive('isOwnedByCurrentProcess')->once()->andReturnFalse();
        $lock->shouldReceive('release')->once()->andReturnFalse();
        Cache::shouldReceive('store')->with('redis')->once()->andReturnSelf();
        Cache::shouldReceive('lock')->with(RebuildXpRanking::LOCK_KEY, 7200)->once()->andReturn($lock);
        $this->artisan('ranking:xp:rebuild')->expectsOutputToContain('publicação cancelada')->assertFailed();
        $this->assertNull(XpRankingSnapshot::findOrFail(1)->generated_at);
    }

    public function test_unavailable_redis_fails_without_publishing_or_using_another_store(): void
    {
        Cache::shouldReceive('store')->with('redis')->once()->andThrow(new RuntimeException('Redis indisponível'));
        $this->artisan('ranking:xp:rebuild')->expectsOutputToContain('Redis indisponível')->assertFailed();
        $this->assertNull(XpRankingSnapshot::findOrFail(1)->generated_at);
    }

    public function test_first_snapshot_and_stale_snapshot_are_displayed_without_rebuilding(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($user);
        $this->withoutMiddleware(MinhaJornada::class);
        $this->get(route('ranking'))->assertOk()->assertSee('Ranking aguardando primeira apuração');
        $this->getJson(route('ranking.xp.load_more'))->assertOk()->assertJsonPath('generated_at', null)->assertJsonPath('rows', []);

        $this->travel(-27)->hours();
        $generatedAt = now()->format('d/m/Y H:i');
        $this->rebuild();
        $this->travelBack();
        $this->get(route('ranking'))->assertOk()->assertSee('Nenhum afiliado com XP')
            ->assertSee('Atualização diária')->assertSee($generatedAt)->assertSee('Atualização em atraso');
    }

    public function test_json_pagination_skips_journey_middleware_and_only_reads_published_data(): void
    {
        $user = User::factory()->create(['email_verified_at' => now(), 'dominio' => 'a.test']);
        User::factory()->count(24)->create(['dominio' => 'a.test']);
        $this->rebuild();
        $dataset = app(XpRankingService::class)->publishedDataset();
        $this->mock(JourneyProgressService::class)->shouldNotReceive('buildForUser');
        $this->actingAs($user);
        $this->getJson(route('ranking.xp.load_more', ['offset' => 10, 'limit' => 500]))->assertOk()
            ->assertJsonCount(10, 'rows')->assertJsonPath('rows.0.position', 11)
            ->assertJsonPath('next_offset', 20)->assertJsonPath('shown_count', 20)
            ->assertJsonPath('has_more', true)->assertJsonPath('generated_at', $dataset['generated_at']);
        $this->getJson(route('ranking.xp.load_more', ['offset' => 20]))->assertOk()
            ->assertJsonCount(5, 'rows')->assertJsonPath('has_more', false)->assertJsonPath('next_offset', 25);
        $this->getJson(route('ranking.xp.load_more', ['offset' => -1, 'limit' => 0]))->assertOk()->assertJsonCount(1, 'rows');
        $this->getJson(route('ranking.xp.load_more', ['offset' => 100]))->assertOk()->assertJsonPath('rows', [])->assertJsonPath('has_more', false);
    }

    public function test_initial_page_still_builds_journey_only_for_authenticated_user(): void
    {
        $user = User::factory()->create(['email_verified_at' => now(), 'dominio' => 'a.test']);
        User::factory()->count(14)->create(['dominio' => 'a.test']);
        $this->rebuild();
        $payload = app(JourneyProgressService::class)->defaultPayload();
        $this->mock(JourneyProgressService::class)->shouldReceive('buildForUser')->once()
            ->withArgs(fn (User $actual) => $actual->id === $user->id)->andReturn($payload);
        $response = $this->actingAs($user)->get(route('ranking'));
        $response->assertOk();
        $this->assertCount(10, $response->viewData('xpRows'));
        $this->assertTrue($response->viewData('xpMeta')['has_more']);
        $this->assertSame(15, $response->viewData('xpTotals')['participants']);
    }

    public function test_sales_tab_keeps_its_existing_data_and_does_not_read_xp_snapshot(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        PurchaseEvent::create([
            'affiliate_name' => 'Afiliado Teste', 'purchase_status' => 'APPROVED',
            'purchase_approved_date' => now()->getTimestamp() * 1000, 'purchase_price_value' => 100,
        ]);
        $this->mock(XpRankingService::class)->shouldNotReceive('publishedDataset');
        $this->withoutMiddleware(MinhaJornada::class);
        $response = $this->actingAs($user)->get(route('ranking', ['tab' => 'sales']));
        $response->assertOk()->assertSee('Afiliado')->assertSee('Atualizado em tempo real');
        $this->assertCount(1, $response->viewData('d')['Afiliado Teste']);
    }

    public function test_http_query_count_does_not_grow_with_participant_count(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($user);
        $this->getJson(route('ranking.xp.load_more'))->assertOk();
        DB::enableQueryLog();
        DB::flushQueryLog();
        $this->getJson(route('ranking.xp.load_more'))->assertOk();
        $small = count(DB::getQueryLog());
        DB::disableQueryLog();
        User::factory()->count(105)->create(['dominio' => 'a.test']);
        $this->rebuild();
        DB::enableQueryLog();
        DB::flushQueryLog();
        $this->getJson(route('ranking.xp.load_more'))->assertOk();
        $this->assertSame($small, count(DB::getQueryLog()));
        DB::disableQueryLog();
    }

    private function rebuild(): void
    {
        app(XpRankingService::class)->rebuild(static function () {});
    }
}
