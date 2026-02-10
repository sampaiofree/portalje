<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComboCurso extends Model
{
    use HasFactory;

    protected $table = 'combo_cursos';

    protected $fillable = [
        'id_combo',
        'id_curso'
    ];

    // Relacionamento com o model Combo
    public function combo()
    {
        return $this->belongsTo(Combo::class, 'id_combo');
    }

    // Relacionamento com o model Curso
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'id_curso');
    }
}
