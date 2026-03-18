<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JourneyRewardSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'badge_name',
        'badge_icon',
        'pre_claim_text',
        'post_claim_text',
        'claim_button_label',
    ];

    public static function defaults(): array
    {
        return [
            'badge_name' => 'Afiliado Ativado',
            'badge_icon' => 'ri-shield-star-line',
            'pre_claim_text' => 'Parabéns. Você concluiu as 5 etapas da jornada e já pode resgatar o seu selo especial.',
            'post_claim_text' => 'Seu selo foi resgatado e agora faz parte da sua jornada de forma permanente.',
            'claim_button_label' => 'Resgatar selo',
        ];
    }

    public function claims(): HasMany
    {
        return $this->hasMany(JourneyRewardClaim::class, 'journey_reward_setting_id', 'id');
    }
}
