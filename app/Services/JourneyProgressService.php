<?php

namespace App\Services;

use App\Models\Codigo_ref;
use App\Models\JourneyRewardClaim;
use App\Models\JourneyRewardSetting;
use App\Models\JourneyStep;
use App\Models\PurchaseEvent;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class JourneyProgressService
{
    public function buildForUser(User $user): array
    {
        if (!$this->journeyTablesAvailable()) {
            return $this->defaultPayload();
        }

        $summary = $user->approvedCompletedPurchaseEventsSummary();

        $codigoRefQuery = Codigo_ref::query()->where('user_id', $user->id);
        $codigosRef = (clone $codigoRefQuery)
            ->pluck('codigo_ref')
            ->filter(fn ($codigo) => filled($codigo))
            ->values();

        $cursosAtivos = (clone $codigoRefQuery)
            ->where('mostrar_curso', true)
            ->exists();

        $temWhatsappAtivo = false;
        if (Schema::hasTable('whatsapp_atendimento')) {
            $temWhatsappAtivo = $user->whatsappAtendimentos()
                ->where('is_active', true)
                ->exists();
        }
        if (!$temWhatsappAtivo) {
            $temWhatsappAtivo = trim((string) ($user->whatsapp_atendimento ?? '')) !== '';
        }

        $leads = PurchaseEvent::whereIn('affiliate_code', $codigosRef)->exists();
        $totalLeads = PurchaseEvent::whereIn('affiliate_code', $codigosRef)
            ->distinct('buyer_checkout_phone')
            ->count();

        $vencidos = PurchaseEvent::whereIn('affiliate_code', $codigosRef)
            ->where('event', 'PURCHASE_EXPIRED')
            ->get(['purchase_original_offer_price_value']);
        $vencidos['n'] = $vencidos->count();
        $vencidos['soma'] = $vencidos->sum('purchase_original_offer_price_value');

        $aguardadndo = PurchaseEvent::whereIn('affiliate_code', $codigosRef)
            ->where('event', 'PURCHASE_BILLET_PRINTED')
            ->get(['purchase_original_offer_price_value']);
        $aguardadndo['n'] = $aguardadndo->count();
        $aguardadndo['soma'] = $aguardadndo->sum('purchase_original_offer_price_value');

        $canceladas = PurchaseEvent::whereIn('affiliate_code', $codigosRef)
            ->where('event', 'PURCHASE_CANCELED')
            ->get(['purchase_original_offer_price_value']);
        $canceladas['n'] = $canceladas->count();
        $canceladas['soma'] = $canceladas->sum('purchase_original_offer_price_value');

        $quantidadeVendas = (int) ($summary['total_count'] ?? 0);
        $totalSum = (float) ($summary['total_sum'] ?? 0);

        $completionMetrics = [
            JourneyStep::COMPLETION_RULE_DOMAIN_CONFIGURED => (bool) ($user->dominio || $user->dominio_externo),
            JourneyStep::COMPLETION_RULE_WHATSAPP_CONFIGURED => $temWhatsappAtivo,
            JourneyStep::COMPLETION_RULE_PRODUCT_ACTIVATED => $cursosAtivos,
            JourneyStep::COMPLETION_RULE_FIRST_LEAD => $leads,
            JourneyStep::COMPLETION_RULE_FIRST_SALE => $quantidadeVendas >= 1,
            JourneyStep::COMPLETION_RULE_SALES_COUNT_AT_LEAST => $quantidadeVendas,
            JourneyStep::COMPLETION_RULE_SALES_TOTAL_AT_LEAST => $totalSum,
        ];

        $dashboardJornada = $this->buildDashboardJourney($completionMetrics);
        $dashboardBaseSummary = $this->buildDashboardJourneySummary(
            array_values(array_filter($dashboardJornada, fn ($step) => (bool) ($step['is_fixed'] ?? false)))
        );
        $dashboardFullSummary = $this->buildDashboardJourneySummary($dashboardJornada);

        $minhaJornada = $this->applyLegacyJourneyLocking(
            array_merge(
                $this->buildLegacyJourney($dashboardJornada),
                $this->buildLegacySalesMilestones(
                    $quantidadeVendas >= 5,
                    $quantidadeVendas >= 10,
                    $quantidadeVendas >= 50,
                    $quantidadeVendas >= 100,
                    $quantidadeVendas >= 500,
                    $quantidadeVendas >= 1000
                )
            )
        );

        $conversaoVendas = null;
        if ($quantidadeVendas > 0 && $totalLeads > 0) {
            $conversaoVendas = round(($quantidadeVendas / $totalLeads) * 100, 2);
        }

        return [
            'minha_jornada' => $minhaJornada,
            'dashboard_jornada' => $dashboardJornada,
            'dashboard_jornada_summary' => $dashboardFullSummary,
            'dashboard_jornada_base_summary' => $dashboardBaseSummary,
            'dashboard_jornada_full_summary' => $dashboardFullSummary,
            'journey_reward' => $this->buildJourneyReward($user, $dashboardBaseSummary),
            'quantidade_vendas' => $quantidadeVendas,
            'total_sum' => $totalSum,
            'vencidos' => $vencidos,
            'aguardadndo' => $aguardadndo,
            'canceladas' => $canceladas,
            'dashboard' => [
                'totalLeads' => $totalLeads,
                'conversao_vendas' => $conversaoVendas,
            ],
        ];
    }

    public function defaultPayload(): array
    {
        $defaultSummary = [
            'completed_count' => 0,
            'progress_percent' => 0,
            'xp_total' => 0,
            'xp_max' => 0,
            'level_label' => 'Nível 1 · Em ativação',
            'reward_unlocked' => false,
        ];

        return [
            'minha_jornada' => [],
            'dashboard_jornada' => [],
            'dashboard_jornada_summary' => $defaultSummary,
            'dashboard_jornada_base_summary' => $defaultSummary,
            'dashboard_jornada_full_summary' => $defaultSummary,
            'journey_reward' => $this->defaultJourneyReward(),
            'quantidade_vendas' => 0,
            'total_sum' => 0,
            'vencidos' => ['n' => 0, 'soma' => 0],
            'aguardadndo' => ['n' => 0, 'soma' => 0],
            'canceladas' => ['n' => 0, 'soma' => 0],
            'dashboard' => [
                'totalLeads' => 0,
                'conversao_vendas' => null,
            ],
        ];
    }

    private function journeyTablesAvailable(): bool
    {
        return Schema::hasTable('users')
            && Schema::hasTable('codigo_ref')
            && Schema::hasTable('purchase_events')
            && Schema::hasTable('journey_steps')
            && Schema::hasTable('journey_step_videos');
    }

    private function rewardTablesAvailable(): bool
    {
        return Schema::hasTable('journey_reward_settings')
            && Schema::hasTable('journey_reward_claims');
    }

    private function buildDashboardJourney(array $completionMetrics): array
    {
        $steps = JourneyStep::query()
            ->where('is_active', true)
            ->with([
                'videos' => function ($query) {
                    $query->where('is_active', true)
                        ->orderByDesc('is_primary')
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $dashboardJourney = [];

        foreach ($steps as $index => $step) {
            $videos = $step->videos
                ->map(fn ($video) => [
                    'titulo' => $video->title,
                    'link' => $video->youtube_id,
                    'texto' => (string) ($video->support_html ?? ''),
                ])
                ->values()
                ->all();

            $goalValue = $step->goal_value !== null ? (float) $step->goal_value : null;
            $concluido = $this->resolveStepCompletion($step, $completionMetrics);

            $dashboardJourney[] = [
                'id' => $step->id,
                'numero' => $index + 1,
                'slug' => $step->slug,
                'titulo' => $step->title,
                'descricao' => (string) ($step->description ?? ''),
                'xp' => (int) $step->xp,
                'completion_rule' => (string) $step->completion_rule,
                'is_fixed' => (bool) $step->is_fixed,
                'goal_value' => $goalValue,
                'goal_label' => $this->goalLabel((string) $step->completion_rule, $goalValue),
                'concluido' => $concluido,
                'videos' => $videos,
            ];
        }

        $primeiraPendenteJaMarcada = false;

        foreach ($dashboardJourney as &$dashboardStep) {
            $dashboardStep['em_foco'] = false;
            $dashboardStep['bloqueado'] = false;
            $dashboardStep['pode_abrir'] = (bool) $dashboardStep['concluido'];

            if (!$dashboardStep['concluido'] && !$primeiraPendenteJaMarcada) {
                $dashboardStep['em_foco'] = true;
                $dashboardStep['pode_abrir'] = true;
                $primeiraPendenteJaMarcada = true;
            } elseif (!$dashboardStep['concluido'] && $primeiraPendenteJaMarcada) {
                $dashboardStep['bloqueado'] = true;
            }

            if ($dashboardStep['concluido']) {
                $dashboardStep['status_label'] = 'Meta concluída';
            } elseif ($dashboardStep['em_foco']) {
                $dashboardStep['status_label'] = 'Etapa atual';
            } elseif ($dashboardStep['bloqueado']) {
                $dashboardStep['status_label'] = 'Bloqueada';
            } else {
                $dashboardStep['status_label'] = 'Pendente';
            }
        }
        unset($dashboardStep);

        return $dashboardJourney;
    }

    private function resolveStepCompletion(JourneyStep $step, array $completionMetrics): bool
    {
        $rule = (string) $step->completion_rule;

        if ($rule === JourneyStep::COMPLETION_RULE_SALES_COUNT_AT_LEAST) {
            $goalValue = (float) ($step->goal_value ?? 0);
            if ($goalValue <= 0) {
                return false;
            }

            return (int) ($completionMetrics[$rule] ?? 0) >= $goalValue;
        }

        if ($rule === JourneyStep::COMPLETION_RULE_SALES_TOTAL_AT_LEAST) {
            $goalValue = (float) ($step->goal_value ?? 0);
            if ($goalValue <= 0) {
                return false;
            }

            return (float) ($completionMetrics[$rule] ?? 0) >= $goalValue;
        }

        return (bool) ($completionMetrics[$rule] ?? false);
    }

    private function goalLabel(string $rule, ?float $goalValue): ?string
    {
        if ($goalValue === null || $goalValue <= 0) {
            return null;
        }

        if ($rule === JourneyStep::COMPLETION_RULE_SALES_COUNT_AT_LEAST) {
            $meta = (int) round($goalValue);
            return 'Faça pelo menos ' . $meta . ' vendas';
        }

        if ($rule === JourneyStep::COMPLETION_RULE_SALES_TOTAL_AT_LEAST) {
            $meta = number_format($goalValue, 2, ',', '.');
            return 'Faça pelo menos R$ ' . $meta . ' em vendas';
        }

        return null;
    }

    private function buildDashboardJourneySummary(array $dashboardJourney): array
    {
        $totalSteps = count($dashboardJourney);
        $completedCount = count(array_filter($dashboardJourney, fn ($step) => !empty($step['concluido'])));
        $xpTotal = array_sum(array_map(
            fn ($step) => !empty($step['concluido']) ? (int) ($step['xp'] ?? 0) : 0,
            $dashboardJourney
        ));
        $progressPercent = $totalSteps > 0
            ? (int) round(($completedCount / $totalSteps) * 100)
            : 0;
        $rewardUnlocked = $totalSteps > 0 && $completedCount === $totalSteps;
        $xpMax = array_sum(array_map(
            fn ($step) => (int) ($step['xp'] ?? 0),
            $dashboardJourney
        ));

        return [
            'completed_count' => $completedCount,
            'progress_percent' => $progressPercent,
            'xp_total' => $xpTotal,
            'xp_max' => $xpMax,
            'level_label' => $rewardUnlocked ? 'Nível 2 · Afiliado Ativado' : 'Nível 1 · Em ativação',
            'reward_unlocked' => $rewardUnlocked,
        ];
    }

    private function buildLegacyJourney(array $dashboardJourney): array
    {
        return array_values(array_map(function ($step) {
            $videos = $step['videos'] ?? [];
            $goalLabel = trim((string) ($step['goal_label'] ?? ''));
            $description = trim((string) ($step['descricao'] ?? ''));
            $legacyText = $videos[0]['texto'] ?? '';

            if ($goalLabel !== '' && $legacyText === '') {
                $legacyText = $goalLabel;
            } elseif ($goalLabel !== '' && $legacyText !== '') {
                $legacyText = $goalLabel . '<br>' . $legacyText;
            } elseif ($goalLabel === '' && $legacyText === '' && $description !== '') {
                $legacyText = $description;
            }

            return [
                'concluido' => (bool) ($step['concluido'] ?? false),
                'em_foco' => (bool) ($step['em_foco'] ?? false),
                'bloqueado' => (bool) ($step['bloqueado'] ?? false),
                'pode_abrir' => (bool) ($step['pode_abrir'] ?? false),
                'status_label' => (string) ($step['status_label'] ?? ''),
                'titulo' => (string) ($step['titulo'] ?? ''),
                'link' => $videos[0]['link'] ?? '',
                'texto' => $legacyText,
                'aulas' => $this->legacyJourneyLessons($videos),
            ];
        }, $dashboardJourney));
    }

    private function buildLegacySalesMilestones(
        bool $cincoVendas,
        bool $dezVendas,
        bool $cinquentaVendas,
        bool $cemVendas,
        bool $quinhentasVendas,
        bool $milVendas
    ): array {
        return [
            [
                'concluido' => $cincoVendas,
                'em_foco' => false,
                'bloqueado' => false,
                'pode_abrir' => $cincoVendas,
                'status_label' => '',
                'titulo' => 'Consiga 5 vendas',
                'link' => '',
                'texto' => '',
                'aulas' => [
                    [
                        'titulo' => 'A melhor estratégia de vendas para quem está começando',
                        'link' => '97EUtY_otic',
                        'texto' => "Link do Curso do Método Carvalho <br> <a href='https://youtube.com/playlist?list=PL8UPaaNJEdSDFGX9Pj20RBn7QCn7aSbaU&feature=shared'>https://youtube.com/playlist?list=PL8UPaaNJEdSDFGX9Pj20RBn7QCn7aSbaU</a>",
                    ],
                    [
                        'titulo' => 'Como melhorar o seu atendimento?',
                        'link' => 'JnIzFq4oa7E',
                        'texto' => '',
                    ],
                ],
            ],
            [
                'concluido' => $dezVendas,
                'em_foco' => false,
                'bloqueado' => false,
                'pode_abrir' => $dezVendas,
                'status_label' => '',
                'titulo' => 'Consiga 10 vendas',
                'link' => '',
                'texto' => '',
                'aulas' => [
                    [
                        'titulo' => 'VOCÊ GANHOU UM PRESENTE',
                        'link' => 'AkNDPGjXpQY',
                        'texto' => "<a href='https://chat.whatsapp.com/JcnpKdQnMArDfPP99jKAlU'>Clique <strong>AQUI</strong> para entrar no grupo</a>",
                    ],
                ],
            ],
            [
                'concluido' => $cinquentaVendas,
                'em_foco' => false,
                'bloqueado' => false,
                'pode_abrir' => $cinquentaVendas,
                'status_label' => '',
                'titulo' => 'Consiga 50 vendas',
                'link' => '',
                'texto' => '',
                'aulas' => false,
            ],
            [
                'concluido' => $cemVendas,
                'em_foco' => false,
                'bloqueado' => false,
                'pode_abrir' => $cemVendas,
                'status_label' => '',
                'titulo' => 'Consiga 100 vendas',
                'link' => '',
                'texto' => '',
                'aulas' => false,
            ],
            [
                'concluido' => $quinhentasVendas,
                'em_foco' => false,
                'bloqueado' => false,
                'pode_abrir' => $quinhentasVendas,
                'status_label' => '',
                'titulo' => 'Consiga 500 vendas',
                'link' => '',
                'texto' => '',
                'aulas' => false,
            ],
            [
                'concluido' => $milVendas,
                'em_foco' => false,
                'bloqueado' => false,
                'pode_abrir' => $milVendas,
                'status_label' => '',
                'titulo' => 'Consiga 1000 vendas',
                'link' => '',
                'texto' => '',
                'aulas' => false,
            ],
        ];
    }

    private function applyLegacyJourneyLocking(array $journey): array
    {
        $primeiraPendenteJaMarcada = false;

        foreach ($journey as &$step) {
            $step['concluido'] = (bool) ($step['concluido'] ?? false);
            $step['em_foco'] = false;
            $step['bloqueado'] = false;
            $step['pode_abrir'] = $step['concluido'];

            if (!$step['concluido'] && !$primeiraPendenteJaMarcada) {
                $step['em_foco'] = true;
                $step['pode_abrir'] = true;
                $primeiraPendenteJaMarcada = true;
            } elseif (!$step['concluido'] && $primeiraPendenteJaMarcada) {
                $step['bloqueado'] = true;
                $step['pode_abrir'] = false;
            }

            if ($step['concluido']) {
                $step['status_label'] = 'Meta concluída';
            } elseif ($step['em_foco']) {
                $step['status_label'] = 'Etapa atual';
            } elseif ($step['bloqueado']) {
                $step['status_label'] = 'Bloqueada';
            } else {
                $step['status_label'] = 'Pendente';
            }
        }
        unset($step);

        return $journey;
    }

    private function legacyJourneyLessons(array $videos): array|false
    {
        if (count($videos) <= 1) {
            return false;
        }

        return array_values(array_map(fn ($video) => [
            'titulo' => $video['titulo'] ?? '',
            'link' => $video['link'] ?? '',
            'texto' => $video['texto'] ?? '',
        ], $videos));
    }

    private function buildJourneyReward(User $user, array $dashboardBaseSummary): array
    {
        $defaults = $this->defaultJourneyReward();
        $rewardUnlocked = (bool) ($dashboardBaseSummary['reward_unlocked'] ?? false);

        if (!$this->rewardTablesAvailable()) {
            $defaults['unlocked'] = $rewardUnlocked;
            return $defaults;
        }

        $setting = JourneyRewardSetting::query()->orderBy('id')->first();
        $claim = JourneyRewardClaim::query()
            ->where('user_id', $user->id)
            ->orderByDesc('claimed_at')
            ->orderByDesc('id')
            ->first();

        return [
            'configured' => (bool) $setting,
            'badge_name' => (string) ($setting->badge_name ?? $defaults['badge_name']),
            'badge_icon' => (string) ($setting->badge_icon ?? $defaults['badge_icon']),
            'pre_claim_text' => (string) ($setting->pre_claim_text ?? $defaults['pre_claim_text']),
            'post_claim_text' => (string) ($setting->post_claim_text ?? $defaults['post_claim_text']),
            'claim_button_label' => (string) ($setting->claim_button_label ?? $defaults['claim_button_label']),
            'unlocked' => $rewardUnlocked,
            'claimed' => (bool) $claim,
            'can_claim' => (bool) $setting && $rewardUnlocked && !$claim,
            'claimed_at' => $claim?->claimed_at,
        ];
    }

    private function defaultJourneyReward(): array
    {
        $defaults = JourneyRewardSetting::defaults();

        return [
            'configured' => false,
            'badge_name' => $defaults['badge_name'],
            'badge_icon' => $defaults['badge_icon'],
            'pre_claim_text' => $defaults['pre_claim_text'],
            'post_claim_text' => $defaults['post_claim_text'],
            'claim_button_label' => $defaults['claim_button_label'],
            'unlocked' => false,
            'claimed' => false,
            'can_claim' => false,
            'claimed_at' => null,
        ];
    }
}
