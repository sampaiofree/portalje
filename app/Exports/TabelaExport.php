<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class TabelaExport implements FromCollection
{
    protected $dados;

    // Recebe os dados no construtor
    public function __construct($dados)
    {
        $this->dados = $dados;
    }

    // Usa os dados fornecidos para a exportação
    public function collection()
    {
        return collect($this->dados);
    }
}
