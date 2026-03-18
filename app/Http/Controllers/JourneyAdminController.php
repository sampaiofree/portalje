<?php

namespace App\Http\Controllers;

use App\Models\JourneyRewardSetting;
use App\Models\JourneyStep;
use App\Models\JourneyStepVideo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class JourneyAdminController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (!$this->journeyTablesAvailable()) {
            return redirect()
                ->route('dashboard_adm')
                ->with('error', 'As tabelas da jornada ainda não foram criadas.');
        }

        $steps = JourneyStep::query()
            ->with(['videos'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('dashboard.admin.jornada', [
            'steps' => $steps,
            'completionRules' => JourneyStep::completionRuleOptions(),
            'extraCompletionRules' => array_values(JourneyStep::extraCompletionRules()),
            'rewardSetting' => JourneyRewardSetting::query()->orderBy('id')->first()
                ?? JourneyRewardSetting::make(JourneyRewardSetting::defaults()),
        ]);
    }

    public function storeStep(Request $request): RedirectResponse
    {
        if (!$this->journeyTablesAvailable()) {
            return redirect()->route('dashboard_adm')->with('error', 'As tabelas da jornada ainda não foram criadas.');
        }

        $data = $this->validatedStepData($request);
        $data['slug'] = $this->makeUniqueSlug($data['title']);

        JourneyStep::create($data);

        return redirect()
            ->route('admin.journey.index')
            ->with('success', 'Etapa criada com sucesso.');
    }

    public function updateStep(Request $request, JourneyStep $step): RedirectResponse
    {
        if (!$this->journeyTablesAvailable()) {
            return redirect()->route('dashboard_adm')->with('error', 'As tabelas da jornada ainda não foram criadas.');
        }

        $data = $this->validatedStepData($request, $step);
        $data['slug'] = $this->makeUniqueSlug($data['title'], $step);

        $step->update($data);

        return redirect()
            ->route('admin.journey.index')
            ->with('success', 'Etapa atualizada com sucesso.')
            ->with('journey_manage_step', $step->id);
    }

    public function destroyStep(JourneyStep $step): RedirectResponse
    {
        if (!$this->journeyTablesAvailable()) {
            return redirect()->route('dashboard_adm')->with('error', 'As tabelas da jornada ainda não foram criadas.');
        }

        if ($step->is_fixed) {
            return redirect()
                ->route('admin.journey.index')
                ->with('error', 'As etapas base da jornada não podem ser removidas.');
        }

        $step->delete();

        return redirect()
            ->route('admin.journey.index')
            ->with('success', 'Etapa excluída com sucesso.');
    }

    public function storeVideo(Request $request, JourneyStep $step): RedirectResponse
    {
        if (!$this->journeyTablesAvailable()) {
            return redirect()->route('dashboard_adm')->with('error', 'As tabelas da jornada ainda não foram criadas.');
        }

        $data = $this->validatedVideoData($request);
        $data['journey_step_id'] = $step->id;

        DB::transaction(function () use ($step, $data): void {
            if ($data['is_primary']) {
                JourneyStepVideo::query()
                    ->where('journey_step_id', $step->id)
                    ->update(['is_primary' => false]);
            }

            JourneyStepVideo::create($data);
        });

        return redirect()
            ->route('admin.journey.index')
            ->with('success', 'Aula criada com sucesso.')
            ->with('journey_manage_step', $step->id);
    }

    public function updateVideo(Request $request, JourneyStepVideo $video): RedirectResponse
    {
        if (!$this->journeyTablesAvailable()) {
            return redirect()->route('dashboard_adm')->with('error', 'As tabelas da jornada ainda não foram criadas.');
        }

        $data = $this->validatedVideoData($request);

        DB::transaction(function () use ($video, $data): void {
            if ($data['is_primary']) {
                JourneyStepVideo::query()
                    ->where('journey_step_id', $video->journey_step_id)
                    ->update(['is_primary' => false]);
            }

            $video->update($data);
        });

        return redirect()
            ->route('admin.journey.index')
            ->with('success', 'Aula atualizada com sucesso.')
            ->with('journey_manage_step', $video->journey_step_id);
    }

    public function destroyVideo(JourneyStepVideo $video): RedirectResponse
    {
        if (!$this->journeyTablesAvailable()) {
            return redirect()->route('dashboard_adm')->with('error', 'As tabelas da jornada ainda não foram criadas.');
        }

        $stepId = $video->journey_step_id;
        $video->delete();

        return redirect()
            ->route('admin.journey.index')
            ->with('success', 'Aula excluída com sucesso.')
            ->with('journey_manage_step', $stepId);
    }

    public function updateReward(Request $request): RedirectResponse
    {
        if (!$this->journeyTablesAvailable()) {
            return redirect()->route('dashboard_adm')->with('error', 'As tabelas da jornada ainda não foram criadas.');
        }

        $data = $this->validatedRewardData($request);
        $setting = JourneyRewardSetting::query()->orderBy('id')->first();

        if ($setting) {
            $setting->update($data);
        } else {
            JourneyRewardSetting::create($data);
        }

        return redirect()
            ->route('admin.journey.index')
            ->with('success', 'Prêmio final da jornada atualizado com sucesso.');
    }

    private function validatedStepData(Request $request, ?JourneyStep $step = null): array
    {
        $isEditing = $step !== null;
        $isFixedStep = $isEditing && (bool) $step->is_fixed;

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'completion_rule' => ['required', Rule::in(array_keys(JourneyStep::completionRuleOptions()))],
            'xp' => ['required', 'integer', 'min:0'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'goal_value' => ['nullable', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['title'] = trim($data['title']);
        $data['description'] = trim((string) ($data['description'] ?? ''));
        $data['is_active'] = $request->boolean('is_active');
        $data['goal_value'] = JourneyStep::isExtraCompletionRule($data['completion_rule'])
            ? (float) ($data['goal_value'] ?? 0)
            : null;

        if (!$isEditing && !JourneyStep::isExtraCompletionRule($data['completion_rule'])) {
            throw ValidationException::withMessages([
                'completion_rule' => 'Novas etapas só podem usar gatilhos de número de vendas ou total em vendas.',
            ]);
        }

        if (!$isEditing && (int) $data['sort_order'] < 6) {
            throw ValidationException::withMessages([
                'sort_order' => 'Etapas extras devem usar ordem igual ou maior que 6.',
            ]);
        }

        if ($isFixedStep) {
            if ($data['completion_rule'] !== $step->completion_rule) {
                throw ValidationException::withMessages([
                    'completion_rule' => 'O gatilho das etapas base não pode ser alterado.',
                ]);
            }

            if ((int) $data['sort_order'] !== (int) $step->sort_order) {
                throw ValidationException::withMessages([
                    'sort_order' => 'A ordem das etapas base é fixa e não pode ser alterada.',
                ]);
            }

            if (!$data['is_active']) {
                throw ValidationException::withMessages([
                    'is_active' => 'As etapas base da jornada não podem ser desativadas.',
                ]);
            }

            $data['is_fixed'] = true;
            $data['is_active'] = true;
            $data['goal_value'] = null;

            return $data;
        }

        if (!JourneyStep::isExtraCompletionRule($data['completion_rule'])) {
            throw ValidationException::withMessages([
                'completion_rule' => 'Etapas extras só podem usar os gatilhos de vendas ou faturamento.',
            ]);
        }

        if ((int) $data['sort_order'] < 6) {
            throw ValidationException::withMessages([
                'sort_order' => 'Etapas extras devem usar ordem igual ou maior que 6.',
            ]);
        }

        if ($data['goal_value'] <= 0) {
            throw ValidationException::withMessages([
                'goal_value' => 'Informe uma meta válida para a etapa extra.',
            ]);
        }

        if ($data['completion_rule'] === JourneyStep::COMPLETION_RULE_SALES_COUNT_AT_LEAST) {
            $goalInteger = (int) round($data['goal_value']);
            if (abs($data['goal_value'] - $goalInteger) > 0.000001) {
                throw ValidationException::withMessages([
                    'goal_value' => 'Para número de vendas, a meta deve ser um valor inteiro.',
                ]);
            }

            $data['goal_value'] = (float) $goalInteger;
        } else {
            $data['goal_value'] = round($data['goal_value'], 2);
        }

        $exactDuplicate = JourneyStep::query()
            ->where('is_fixed', false)
            ->where('completion_rule', $data['completion_rule'])
            ->where('goal_value', $data['goal_value'])
            ->when($step, fn ($query) => $query->where('id', '!=', $step->id))
            ->exists();

        if ($exactDuplicate) {
            throw ValidationException::withMessages([
                'goal_value' => 'Já existe uma etapa extra com esse gatilho e essa meta.',
            ]);
        }

        $data['is_fixed'] = false;

        return $data;
    }

    private function validatedVideoData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'youtube_id' => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9_-]{6,20}$/'],
            'support_html' => ['nullable', 'string'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_primary' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['title'] = trim($data['title']);
        $data['youtube_id'] = trim($data['youtube_id']);
        $data['support_html'] = trim((string) ($data['support_html'] ?? ''));
        $data['is_primary'] = $request->boolean('is_primary');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function validatedRewardData(Request $request): array
    {
        $data = $request->validate([
            'badge_name' => ['required', 'string', 'max:255'],
            'badge_icon' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9:_-]+$/'],
            'pre_claim_text' => ['nullable', 'string'],
            'post_claim_text' => ['nullable', 'string'],
            'claim_button_label' => ['required', 'string', 'max:100'],
        ]);

        $data['badge_name'] = trim($data['badge_name']);
        $data['badge_icon'] = trim($data['badge_icon']);
        $data['pre_claim_text'] = trim((string) ($data['pre_claim_text'] ?? ''));
        $data['post_claim_text'] = trim((string) ($data['post_claim_text'] ?? ''));
        $data['claim_button_label'] = trim($data['claim_button_label']);

        return $data;
    }

    private function makeUniqueSlug(string $title, ?JourneyStep $step = null): string
    {
        $baseSlug = Str::slug($title);
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'etapa-jornada';
        $slug = $baseSlug;
        $index = 2;

        while (
            JourneyStep::query()
                ->where('slug', $slug)
                ->when($step, fn ($query) => $query->where('id', '!=', $step->id))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $index;
            $index++;
        }

        return $slug;
    }

    private function journeyTablesAvailable(): bool
    {
        return Schema::hasTable('journey_steps')
            && Schema::hasTable('journey_step_videos')
            && Schema::hasTable('journey_reward_settings')
            && Schema::hasTable('journey_reward_claims');
    }
}
