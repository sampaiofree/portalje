<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortenedUrl extends Model
{
    use HasFactory;

    // Definindo os campos que podem ser preenchidos em massa
    protected $fillable = [
        'user_id',
        'dominio',
        'slug',
        'url_longa',
        'click_count',
    ];

    /**
     * Relacionamento com o modelo User.
     * Um ShortenedUrl pertence a um User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
