<?php

namespace App\Console\Commands;

use App\Services\XpRankingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class RebuildXpRanking extends Command
{
    public const LOCK_KEY = 'ranking:xp:rebuild';

    private const LOCK_SECONDS = 7200;

    protected $signature = 'ranking:xp:rebuild';

    protected $description = 'Apura e publica o ranking diário de XP dos afiliados';

    public function handle(XpRankingService $ranking): int
    {
        $started = hrtime(true);
        $lock = null;
        $acquired = false;

        try {
            // Explicitly use Redis even when the application's default cache is file.
            $lock = Cache::store('redis')->lock(self::LOCK_KEY, self::LOCK_SECONDS);
            $acquired = $lock->get();
            if (! $acquired) {
                $this->warn('Já existe uma apuração do ranking de XP em andamento.');

                return self::FAILURE;
            }

            $result = $ranking->rebuild(function () use ($lock, $started) {
                if ((hrtime(true) - $started) / 1e9 >= self::LOCK_SECONDS - 60 || ! $lock->isOwnedByCurrentProcess()) {
                    throw new RuntimeException('Bloqueio da apuração expirou ou foi perdido; publicação cancelada.');
                }
            });
            $context = $result + [
                'duration_seconds' => round((hrtime(true) - $started) / 1e9, 3),
                'peak_memory_bytes' => memory_get_peak_usage(true),
            ];
            Log::info('Ranking de XP publicado.', $context);
            $this->info('Ranking de XP publicado: '.json_encode($context, JSON_UNESCAPED_UNICODE));

            return self::SUCCESS;
        } catch (Throwable $e) {
            report($e);
            $this->error('Falha na apuração do ranking de XP: '.$e->getMessage());

            return self::FAILURE;
        } finally {
            if ($acquired) {
                try {
                    $lock->release();
                } catch (Throwable $e) {
                    report($e);
                }
            }
        }
    }
}
