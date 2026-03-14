<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RootDomainCourseConfig extends Model
{
    use HasFactory;

    protected $table = 'root_domain_course_configs';

    protected $fillable = [
        'curso_id',
        'mostrar_curso',
        'formulario_pre_checkout',
        'modo_precos',
        'cupom_principal_id',
        'cupom_secundario_id',
        'usar_contador',
        'contador_minutos',
        'contador_acao',
        'contador_destino_oferta',
    ];

    protected $casts = [
        'mostrar_curso' => 'boolean',
        'formulario_pre_checkout' => 'boolean',
        'cupom_principal_id' => 'integer',
        'cupom_secundario_id' => 'integer',
        'usar_contador' => 'boolean',
        'contador_minutos' => 'integer',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id', 'id');
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
