<?php

namespace Tests\Performance;

use App\Models\Curso;
use App\Models\User;
use App\Services\XpRankingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/** Run explicitly: php artisan test tests/Performance/XpRankingBenchmarkTest.php */
class XpRankingBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_fifty_thousand_users_are_processed_with_queries_per_batch(): void
    {
        $course = Curso::create(['ordem' => 1, 'url' => 'benchmark', 'titulo' => 'Benchmark']);
        $password = User::factory()->make()->password;
        for ($offset = 0; $offset < 50000; $offset += 500) {
            $users = $refs = $events = $whatsapps = [];
            for ($i = $offset + 1; $i <= $offset + 500; $i++) {
                $users[] = [
                    'id' => $i, 'name' => 'Afiliado '.$i, 'email' => 'bench'.$i.'@example.test', 'password' => $password,
                    'dominio' => $i % 2 === 0 ? 'dominio'.$i.'.test' : null,
                    'whatsapp_atendimento' => $i % 3 === 0 ? '5511999999999' : null,
                ];
                $refs[] = ['user_id' => $i, 'curso_id' => $course->id, 'codigo_ref' => 'BENCH'.$i, 'mostrar_curso' => $i % 5 !== 0];
                $events[] = ['id' => 'event-'.$i, 'affiliate_code' => 'BENCH'.$i, 'purchase_status' => $i % 2 === 0 ? 'APPROVED' : 'CANCELED', 'purchase_original_offer_price_value' => 150];
                if ($i % 4 === 0) {
                    $whatsapps[] = ['user_id' => $i, 'whatsapp' => '5511999999999', 'is_active' => true];
                }
            }
            DB::table('users')->insert($users);
            DB::table('codigo_ref')->insert($refs);
            DB::table('purchase_events')->insert($events);
            DB::table('whatsapp_atendimento')->insert($whatsapps);
        }
        unset($users, $refs, $events, $whatsapps);

        $queries = 0;
        $userReads = $stepReads = $schemaReads = 0;
        DB::listen(function ($query) use (&$queries, &$userReads, &$stepReads, &$schemaReads) {
            $queries++;
            if (str_contains($query->sql, 'from "users"')) {
                $userReads++;
                $this->assertStringContainsString('limit 500', $query->sql);
            }
            $stepReads += str_contains($query->sql, 'from "journey_steps"') ? 1 : 0;
            $schemaReads += str_contains($query->sql, 'sqlite_master') ? 1 : 0;
            $this->assertStringNotContainsString('journey_step_videos', $query->sql);
            $this->assertStringNotContainsString('journey_reward', $query->sql);
        });
        $started = hrtime(true);
        $memoryBefore = memory_get_usage(true);
        $result = app(XpRankingService::class)->rebuild(static function () {});
        $duration = (hrtime(true) - $started) / 1e9;
        $batchQueries = $queries;
        $this->assertSame(50000, $result['users_processed']);
        $this->assertSame(50000, $result['participants']);
        $this->assertSame(101, $userReads); // 100 full chunks and the terminating empty read.
        $this->assertSame(1, $stepReads);
        $this->assertSame(6, $schemaReads);
        $this->assertLessThanOrEqual(510, $batchQueries);
        $this->assertCount(100, app(XpRankingService::class)->publishedDataset()['rows']);
        fwrite(STDERR, "\nXP ranking benchmark (SQLite in-memory): ".json_encode([
            'users' => 50000, 'purchase_events' => 50000, 'queries' => $batchQueries,
            'duration_seconds' => round($duration, 3), 'php_peak_memory_bytes' => memory_get_peak_usage(true),
            'php_retained_memory_delta_bytes' => memory_get_usage(true) - $memoryBefore,
        ])."\n");
    }
}
