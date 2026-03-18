<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JourneyStep extends Model
{
    use HasFactory;

    public const COMPLETION_RULE_DOMAIN_CONFIGURED = 'domain_configured';
    public const COMPLETION_RULE_WHATSAPP_CONFIGURED = 'whatsapp_configured';
    public const COMPLETION_RULE_PRODUCT_ACTIVATED = 'product_activated';
    public const COMPLETION_RULE_FIRST_LEAD = 'first_lead';
    public const COMPLETION_RULE_FIRST_SALE = 'first_sale';
    public const COMPLETION_RULE_SALES_COUNT_AT_LEAST = 'sales_count_at_least';
    public const COMPLETION_RULE_SALES_TOTAL_AT_LEAST = 'sales_total_at_least';

    protected $fillable = [
        'title',
        'slug',
        'completion_rule',
        'xp',
        'sort_order',
        'is_active',
        'is_fixed',
        'goal_value',
        'description',
    ];

    protected $casts = [
        'xp' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'is_fixed' => 'boolean',
        'goal_value' => 'float',
    ];

    public function videos(): HasMany
    {
        return $this->hasMany(JourneyStepVideo::class)
            ->orderByDesc('is_primary')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public static function completionRuleOptions(): array
    {
        return [
            self::COMPLETION_RULE_DOMAIN_CONFIGURED => 'Domínio configurado',
            self::COMPLETION_RULE_WHATSAPP_CONFIGURED => 'WhatsApp configurado',
            self::COMPLETION_RULE_PRODUCT_ACTIVATED => 'Produto ativado',
            self::COMPLETION_RULE_FIRST_LEAD => 'Primeiro lead',
            self::COMPLETION_RULE_FIRST_SALE => 'Primeira venda',
            self::COMPLETION_RULE_SALES_COUNT_AT_LEAST => 'Número de vendas',
            self::COMPLETION_RULE_SALES_TOTAL_AT_LEAST => 'Total em vendas (R$)',
        ];
    }

    public static function fixedCompletionRules(): array
    {
        return [
            self::COMPLETION_RULE_DOMAIN_CONFIGURED,
            self::COMPLETION_RULE_WHATSAPP_CONFIGURED,
            self::COMPLETION_RULE_PRODUCT_ACTIVATED,
            self::COMPLETION_RULE_FIRST_LEAD,
            self::COMPLETION_RULE_FIRST_SALE,
        ];
    }

    public static function extraCompletionRules(): array
    {
        return [
            self::COMPLETION_RULE_SALES_COUNT_AT_LEAST,
            self::COMPLETION_RULE_SALES_TOTAL_AT_LEAST,
        ];
    }

    public static function isExtraCompletionRule(string $rule): bool
    {
        return in_array($rule, self::extraCompletionRules(), true);
    }

    public static function isFixedCompletionRule(string $rule): bool
    {
        return in_array($rule, self::fixedCompletionRules(), true);
    }
}
