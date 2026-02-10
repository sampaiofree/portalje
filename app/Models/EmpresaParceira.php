<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaParceira extends Model
{
    use HasFactory;

    protected $table = 'empresas_parceiras';

    protected $fillable = [
        'nome_empresa',
        'nome_responsavel',
        'telefone_contato',
        'cidade',
        'estado',
        'informacoes_vagas',
    ];
}
