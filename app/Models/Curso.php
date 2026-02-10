<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Curso extends Model
{
    use HasFactory;

    protected $table = 'curso';

    protected $fillable = [
        'id',
        'ordem',
        'codigo_id_hotmart',
        'url',
        'gratuito', 
        'publicado',
        'permitir_afiliacao',
        'mostrar_na_pagina',
        'categorias',
        'titulo',
        'headline',
        'beneficios_lista',
        'descricao_curta',
        'conteudo_principal',
        'conteudo_bonus',
        'capa_quadrada',
        'capa_vertical',
        'capa_horizontal',
        'nota_avaliacao',
        'numero_alunos',
        'areas_de_atuacao',
        'salario_maximo',
        'video_dentro_do_curso',
        'video_apresentacao',
        'link_checkout_basico',
        'link_checkout_completo',
        'codigo_afiliado_plano_basico',
        'codigo_afiliado_plano_completo',
        'preco_parcelado_basico',
        'preco_parcelado_completo',
        'preco_cheio_basico',
        'preco_cheio_completo',
        'horas_basico',
        'horas_completo',
        'link_materiais',
        'link_afiliacao',
        'link_area_membros',
        'professor_nome',
        'professor_biografia',
        'professor_foto',
    ];
    
    public function codigo_ref()
    {
        return $this->hasMany(Codigo_ref::class, 'curso_id', 'id');
    }

    public function aulas_demonstrativas()
    {
        return $this->hasMany(AulasDemonstrativa::class, 'id_curso', 'id');
    }

    // Um curso pode ter vários registros na tabela pivot (combo_cursos)
    public function comboCursos()
    {
        return $this->hasMany(ComboCurso::class, 'id_curso');
    }

    // Relação many-to-many com Combo via tabela pivot combo_cursos
    public function combos()
    {
        return $this->belongsToMany(Combo::class, 'combo_cursos', 'id_curso', 'id_combo');
    }
   
}
