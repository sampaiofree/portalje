<?php

namespace App\Http\Controllers;

use App\Models\JourneyRewardClaim;
use App\Models\JourneyRewardSetting;
use App\Services\JourneyProgressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JourneyRewardController extends Controller
{
    public function store(Request $request, JourneyProgressService $journeyProgressService): RedirectResponse
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        $payload = $journeyProgressService->buildForUser($user->fresh());
        $journeyReward = $payload['journey_reward'] ?? [];

        if (($journeyReward['claimed'] ?? false) === true) {
            return back()->with('success', 'Seu selo já foi resgatado.');
        }

        if (!($journeyReward['configured'] ?? false)) {
            return back()->with('error', 'O selo final da jornada ainda não foi configurado.');
        }

        if (!($journeyReward['unlocked'] ?? false)) {
            return back()->with('error', 'Complete todas as etapas ativas antes de resgatar o selo.');
        }

        $setting = JourneyRewardSetting::query()->orderBy('id')->first();
        if (!$setting) {
            return back()->with('error', 'O selo final da jornada ainda não foi configurado.');
        }

        JourneyRewardClaim::query()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'journey_reward_setting_id' => $setting->id,
                'claimed_at' => now(),
            ]
        );

        return back()->with('success', 'Selo resgatado com sucesso.');
    }
}
