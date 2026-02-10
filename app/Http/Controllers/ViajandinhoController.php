<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Expedicoes; // 
use App\Models\ExpedicoesInteressado; // 

class ViajandinhoController extends Controller
{
    public function nova_expedicao()
    {
        // Cria um novo registro na tabela 'expedicoes'
        $expedicao = Expedicoes::create();

        // Obtém o ID do registro recém-criado
        $expedicao_id = $expedicao->id;

        // Redireciona para a rota especificada com o ID do novo registro
        return redirect()->route('editar_exedicao', ['id' => $expedicao_id]);
    }

    public function editar_exedicao($id) {
        $expedicao = Expedicoes::where('id', $id)->first();
        if($expedicao){
            return view('viajandinho.expedicao_editar', compact('expedicao'));
        }
    }

    public function editar_exedicao_alterar(Request $request) {
        
        $expedicao = Expedicoes::updateOrCreate(
            ['id' => $request->input('id')], // Condição para verificar a existência do registro (ID)
            [
                'destino' => $request->input('destino'),
                'informacoes' => $request->input('informacoes'),
                'preco' => $request->input('preco'),
            ] // Dados para criar ou atualizar
        );

        return redirect()->route('listar_exedicoes')->with('message', 'Expedição atualizada com sucesso!');
    }

    public function editar_exedicao_ativo(Request $request) {
        // Validação básica para garantir que os dados necessários estejam presentes
        $request->validate([
            'id' => 'required|exists:expedicoes,id', // Certifica-se de que o ID existe na tabela 'expedicoes'
            'ativo' => 'required|boolean', // Verifica se o campo 'ativo' é booleano (0 ou 1)
        ]);
    
        // Encontre o registro da expedição
        $expedicao = Expedicoes::findOrFail($request->input('id'));
    
        // Atualize o campo 'ativo' com o novo valor
        $expedicao->ativo = $request->input('ativo');
        $expedicao->save();
    
        return response()->json(['message' => 'Expedição Alterada com Sucesso!']);
    }
    
    

    public function listar_exedicoes()
    {
        $expedicao = Expedicoes::all();
        return view('viajandinho.expedicao_listar', compact('expedicao'));
    }

    public function listar_interessados()
    {
        $interessados = ExpedicoesInteressado::with('expedicao')->get();

        // echo "<pre>";
        // print_r($interessados);
        // echo "</pre>";

        return view('viajandinho.expedicao_interessados_listar', compact('interessados'));
    }
}
