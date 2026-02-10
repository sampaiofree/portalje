<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\PurchaseEvent;

class Lead extends Model 
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'whatsapp',
        'idade',
        'cidade',
        'escolaridade',
        'cursos_interesse',
        'curso_id',
        'curso',
        'aceita_estudar_online',
        'pode_pagar_inscricao',
        'perdeu_vaga',
        'motivacao',
        'compartilhar_dados',
        'melhor_horario',
        'preferencia_contato',
        'origem',
        'arquivar',
        'user_id',
        'evento_portal'
    ];

    

}
