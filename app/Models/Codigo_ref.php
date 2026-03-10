<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cupom;
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
        'formulario_pre_checkout',
        'modo_precos',
        'cupom_principal_id',
        'cupom_secundario_id',
        
    ];

    protected $casts = [
        'mostrar_curso' => 'boolean',
        'formulario_pre_checkout' => 'boolean',
        'cupom_principal_id' => 'integer',
        'cupom_secundario_id' => 'integer',
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

    public function cupomPrincipal()
    {
        return $this->belongsTo(Cupom::class, 'cupom_principal_id', 'id');
    }

    public function cupomSecundario()
    {
        return $this->belongsTo(Cupom::class, 'cupom_secundario_id', 'id');
    }

    
}
