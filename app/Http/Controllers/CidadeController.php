<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cidades;

class CidadeController extends Controller
{
    public function buscarCidades()
    {
        echo "Aqui!";
        //$query = $request->get('query');
        //$cidades = Cidades::where('nome', 'LIKE', "%{$query}%")->limit(10)->get();

        //return response()->json($cidades);
    }

    public function processarCidades(Request $request)
    {
        $cidadesSelecionadas = $request->input('cidades'); // Retorna um array de IDs das cidades selecionadas
        // Processar as cidades selecionadas conforme necessário
    }
}
