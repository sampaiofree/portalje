<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JourneyStepVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'journey_step_id',
        'title',
        'youtube_id',
        'support_html',
        'sort_order',
        'is_primary',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function step(): BelongsTo
    {
        return $this->belongsTo(JourneyStep::class, 'journey_step_id', 'id');
    }
}
