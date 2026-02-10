<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Curso;
use App\Models\PurchaseEvent;
use App\Models\Cidades;

class ConsultarController extends Controller
{
    public function consultarAluno($nomeCampo, $conteudoCampo)
    {
        if($conteudoCampo=="03125257107"){
            echo "Aluno do curdo de Auxiliar Veterinário";
            die();
        }
        if ($nomeCampo == "cpf") $nomeCampo = "buyer_document";
        if ($nomeCampo == "email") $nomeCampo = "buyer_email";
        if ($nomeCampo == "telefone") $nomeCampo = "buyer_checkout_phone";

        $alunos = PurchaseEvent::where($nomeCampo, 'like', '%' . $conteudoCampo . '%')->get();

        $alunos = PurchaseEvent::where($nomeCampo, $conteudoCampo)->get();

        // 🔹 Retornar os resultados formatados em Markdown
        if ($alunos->isEmpty()) {
            return "### ❌ Nenhum aluno encontrado para **{$nomeCampo} = {$conteudoCampo}**";
        }

        $markdown = "## 🔍 Resultado da consulta de alunos\n\n";
        foreach ($alunos as $aluno) {
            $markdown .= "**Nome:** {$aluno->buyer_name}\n";
            $markdown .= "- **Email:** {$aluno->buyer_email}\n";
            $markdown .= "- **Telefone:** {$aluno->buyer_checkout_phone}\n";
            $markdown .= "- **Documento:** {$aluno->buyer_document}\n";
            $markdown .= "- **Curso:** {$aluno->product_name}\n";
            $markdown .= "- **Status - Transação:** {$aluno->purchase_status}\n";
            $markdown .= "- **Data da transação:** {$aluno->purchase_date}\n\n";
            $markdown .= "---\n\n";
        }

        return $markdown;
    }

    public function consultarCurso($id = null)
    {
        $query = Curso::query();

        if ($id) {
            $query->where('id', $id);
        } else {
            $query->where(function ($q) {
                $q->where('publicado', '1')
                  ->orWhere('publicado', 'on');
            });
        }

        $cursos = $query->get();

        if ($cursos->isEmpty()) {
            return "### ❌ Nenhum curso encontrado.";
        }

        $markdown = "## 🎓 Lista de Cursos\n\n";

        foreach ($cursos as $curso) {
            $markdown .= "### {$curso->titulo}\n";
            $markdown .= "- **ID:** {$curso->id}\n";
            $markdown .= "- **Professor:** {$curso->professor_nome}\n";
            $markdown .= "- **Carga horária:** {$curso->horas_completo} horas\n";
            $markdown .= "- **Preço:** {$curso->preco_parcelado_completo} (ou {$curso->preco_cheio_completo} à vista no PIX)\n";
            $markdown .= "- **Avaliação:** {$curso->nota_avaliacao}\n";
            $markdown .= "- **Área de atuação:** {$curso->areas_de_atuacao}\n";
            $markdown .= "- **Página do curso:** https://jovemempreendedor.org/{$curso->url}\n";
            $markdown .= "- **Aulas gratuitas:** https://jovemempreendedor.org/{$curso->url}?g=1\n";
            $markdown .= "- **Pagamento:** {$curso->link_checkout_completo}\n";
            $markdown .= "- **Área de Membros:** {$curso->link_area_membros}\n";
            $markdown .= "- **Vídeo de apresentação:** (https://youtube.com/watch?v={$curso->video_apresentacao}\n";
            $markdown .= "- **Por dentro do curso:** https://youtube.com/watch?v={$curso->video_dentro_do_curso}\n\n";
            $markdown .= "---\n\n";
        }

        return $markdown;
    }

    public function gancho()
    {
        $query = Curso::query();

        

        $cursos = $query->get();

        if ($cursos->isEmpty()) {
            return "### ❌ Nenhum curso encontrado.";
        }

        $markdown = "";

        foreach ($cursos as $curso) {
            $markdown .= "O que faz um {$curso->titulo}, qual o salário médio no Brasil e onde esse profissional pode trabalhar  <br>";
        }

        echo "<pre>";
        print_r($markdown);
        echo "</pre>";
    }
   public function consultarNovaCidade(Request $request){
    
    $dados = $request->input('cidades_existentes', []);

    //return response()->json($dados);

    $query = Cidades::query();;

    foreach ($dados as $estado => $cidades) {
        $query->orWhere(function ($q) use ($estado, $cidades) {
            $q->where('estado', $estado)
              ->whereNotIn('cidade', $cidades);
        });
    }

    $proximaCidade = $query->orderBy('estado')->orderBy('cidade')->first();

    $return = $proximaCidade;

    return response()->json($return);

   }
} 
