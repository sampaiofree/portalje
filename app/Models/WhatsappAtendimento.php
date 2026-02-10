<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappAtendimento extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_atendimento';

    protected $fillable = [
        'user_id',
        'whatsapp',
        'is_active',
        'last_lead_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_lead_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
