<?php

namespace App\Services;

use App\Models\JourneyStep;

class JourneyStepEvaluator
{
    public function isComplete(JourneyStep $step, array $metrics): bool
    {
        $rule = (string) $step->completion_rule;

        if (in_array($rule, [JourneyStep::COMPLETION_RULE_SALES_COUNT_AT_LEAST, JourneyStep::COMPLETION_RULE_SALES_TOTAL_AT_LEAST], true)) {
            $goal = (float) ($step->goal_value ?? 0);
            $value = $rule === JourneyStep::COMPLETION_RULE_SALES_COUNT_AT_LEAST
                ? (int) ($metrics[$rule] ?? 0)
                : (float) ($metrics[$rule] ?? 0);

            return $goal > 0 && $value >= $goal;
        }

        return (bool) ($metrics[$rule] ?? false);
    }
}
