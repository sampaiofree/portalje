<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\PurchaseEvent;

class Codigo_ref extends Model
{
    use HasFactory;

    protected $table = 'codigo_ref';

    protected $fillable = [
        'codigo_ref',
        'curso_id',
        'user_id',
        'mostrar_curso',
        
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function purchaseEvent()
    {
        return $this->hasMany(Codigo_ref::class, 'affiliate_code','codigo_ref');
    }

    
}
