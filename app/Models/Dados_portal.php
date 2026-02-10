<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Dados_portal extends Model
{
    use HasFactory;

    protected $table = 'portal_informacoes';

    protected $fillable = [
        'telefone_suporte_alunos',
        'telefone_suporte_afiliados',
        'endereco',
        'cnpj',
    ];
}
