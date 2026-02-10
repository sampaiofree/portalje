<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AulasDemonstrativa extends Model
{
    use HasFactory;

    // Definindo a tabela associada ao modelo
    protected $table = 'aulas_demonstrativas';

    // Definindo a chave primária, caso seja diferente de 'id'
    protected $primaryKey = 'id';

    // Definindo os campos que podem ser preenchidos em massa (mass assignment)
    protected $fillable = [
        'id_curso', 
        'aula_titulo', 
        'aula_id_youtube'
    ];

    // Desabilitando a criação automática dos campos 'created_at' e 'updated_at'
    public $timestamps = true;

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'id_curso', 'id');
    }
}