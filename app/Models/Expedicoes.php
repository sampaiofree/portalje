<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expedicoes extends Model
{
    use HasFactory;

    protected $table = 'expedicoes';

    protected $fillable = [
        'id',
        'destino',
        'informacoes',
        'preco',
    ];
    
    
}
