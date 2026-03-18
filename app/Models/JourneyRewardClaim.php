<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JourneyRewardClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'journey_reward_setting_id',
        'claimed_at',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function setting(): BelongsTo
    {
        return $this->belongsTo(JourneyRewardSetting::class, 'journey_reward_setting_id', 'id');
    }
}
