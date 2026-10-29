<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XpRankingSnapshot extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'rows' => 'array',
        'totals' => 'array',
        'started_at' => 'immutable_datetime',
        'generated_at' => 'immutable_datetime',
    ];
}
