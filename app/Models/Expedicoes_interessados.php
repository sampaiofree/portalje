<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Expedicoes;

class Expedicoes_interessados extends Model
{
    use HasFactory;

    // Definindo a tabela associada ao modelo (opcional, se o nome da tabela segue a convenção de pluralização)
    protected $table = 'expedicoes_interessados';

    // Definindo os atributos que podem ser preenchidos em massa
    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'expedicao_id',
        'observacoes',
    ];

    /**
     * Relacionamento com o modelo Expedicao.
     * Um interessado pertence a uma expedição.
     */
    public function expedicao()
    {
        return $this->belongsTo(Expedicao::class, 'expedicao_id');
    }
    
    
}
