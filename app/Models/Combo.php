<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Combo extends Model
{
    use HasFactory;

    // Define os campos que podem ser preenchidos via mass-assignment
    protected $fillable = [
        'titulo',
        'headline',
        'descricao_curta',
        'url',
        'link_checkout',
        'preco_parcelado',
        'preco'
    ];

    // Um combo pode ter vários registros na tabela pivot (combo_cursos)
    public function comboCursos()
    {
        return $this->hasMany(ComboCurso::class, 'id_combo');
    }

    // Relação many-to-many com Curso via tabela pivot combo_cursos
    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'combo_cursos', 'id_combo', 'id_curso');
    }
}
