<?php

namespace Tests\Integration;

use App\Console\Commands\RebuildXpRanking;
use App\Models\XpRankingSnapshot;
use App\Services\XpRankingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/** Uses an isolated Redis instance, never the application's configured server. */
class XpRankingRedisLockTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $socket = getenv('XP_RANKING_REDIS_SOCKET');
        if (! $socket) {
            $this->markTestSkipped('Set XP_RANKING_REDIS_SOCKET to an isolated Redis UNIX socket.');
        }
        $connection = ['host' => $socket, 'port' => 0, 'database' => 0];
        config([
            'database.redis.client' => 'phpredis',
            'database.redis.options.prefix' => 'xp-ranking-test-'.bin2hex(random_bytes(8)).':',
            'database.redis.default' => $connection,
            'database.redis.contender' => $connection,
            'cache.stores.redis.connection' => 'default',
            'cache.stores.redis.lock_connection' => 'default',
            'cache.stores.contender' => ['driver' => 'redis', 'connection' => 'contender', 'lock_connection' => 'contender'],
        ]);
    }

    public function test_independent_redis_connection_blocks_command_and_preserves_owners_lock(): void
    {
        $external = Cache::store('contender')->lock(RebuildXpRanking::LOCK_KEY, 60);
        $this->assertTrue($external->get());
        try {
            $this->artisan('ranking:xp:rebuild')->assertFailed();
            $this->assertTrue($external->isOwnedByCurrentProcess());
            $this->assertNull(XpRankingSnapshot::findOrFail(1)->generated_at);
        } finally {
            $external->release();
        }
        $this->artisan('ranking:xp:rebuild')->assertSuccessful();
        $this->assertNotNull(XpRankingSnapshot::findOrFail(1)->generated_at);
        $this->assertTrue($external->get());
        $external->release();
    }

    public function test_expired_owner_cannot_publish_or_release_a_replacement_redis_lock(): void
    {
        $replacement = Cache::store('contender')->lock(RebuildXpRanking::LOCK_KEY, 60);
        $this->mock(XpRankingService::class)->shouldReceive('rebuild')->once()
            ->andReturnUsing(function ($guard) use ($replacement) {
                Cache::store('contender')->lock(RebuildXpRanking::LOCK_KEY)->forceRelease();
                $this->assertTrue($replacement->get());
                $guard();
            });
        try {
            $this->artisan('ranking:xp:rebuild')->expectsOutputToContain('publicação cancelada')->assertFailed();
            $this->assertTrue($replacement->isOwnedByCurrentProcess());
            $this->assertNull(XpRankingSnapshot::findOrFail(1)->generated_at);
        } finally {
            $replacement->release();
        }
    }
}
