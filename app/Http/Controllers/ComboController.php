<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Combo;

class ComboController extends Controller
{
    public function novo()
    {
        // Cria registro em branco com valores padrão
        $combo = new Combo();
        $combo->titulo             = '';
        $combo->headline           = '';
        $combo->descricao_curta    = '';
        $combo->url                = '';
        $combo->link_checkout      = '';
        $combo->preco_parcelado    = 0;
        $combo->preco              = 0;
        $combo->save();

        //echo "Olá"; exit;

        // Retorna a blade para criar/editar
        return view('adm.combos.criar_editar', compact('combo'));
    }

    public function editar(Request $request)
    {
        // Validação dos dados enviados
        $validated = $request->validate([
            'id'                     => 'required|exists:combos,id',
            'titulo'                 => 'nullable|string',
            'headline'               => 'nullable|string',
            'descricao_curta'        => 'nullable|string',
            'url'                    => 'nullable|string',
            'link_checkout'          => 'nullable|string',
            'link_checkout_completo' => 'nullable|string',
            'preco_parcelado'        => 'nullable|string',
            'preco'                  => 'nullable|string',
        ]);

        // Encontra o combo e atualiza os dados
        $combo = \App\Models\Combo::findOrFail($validated['id']);
        $combo->update($validated);

        // Redireciona de volta com mensagem de sucesso
        return $this->index();
    }

   // Lista os combos cadastrados
   public function index()
   {
       $combos = Combo::all();
       return view('adm.combos.index', compact('combos'));
   }

   // Carrega o formulário para editar um combo existente
   public function editarForm($id)
   {
       $combo = Combo::findOrFail($id);
       return view('adm.combos.criar_editar', compact('combo'));
   }

   public function formCursos($comboId)
    {
        $combo = \App\Models\Combo::findOrFail($comboId);
        $cursos = \App\Models\Curso::all();
        // IDs dos cursos já vinculados ao combo
        $selected = \App\Models\ComboCurso::where('id_combo', $combo->id)->pluck('id_curso')->toArray();
        return view('adm.combos.cursos', compact('combo', 'cursos', 'selected'));
    }

    public function salvarCursos(Request $request, $comboId)
    {
        $combo = \App\Models\Combo::findOrFail($comboId);
        $cursosSelecionados = $request->input('cursos', []);

        // Remove vínculos antigos
        \App\Models\ComboCurso::where('id_combo', $combo->id)->delete();

        // Cria novos vínculos
        foreach ($cursosSelecionados as $cursoId) {
            \App\Models\ComboCurso::create([
                'id_combo' => $combo->id,
                'id_curso' => $cursoId,
            ]);
        }

        return redirect()->route('combo.index')
                        ->with('success', 'Cursos atualizados!');
    }

    public function excluirCurso($comboId, $cursoId)
    {
        $combo = \App\Models\Combo::findOrFail($comboId);
        // Remove a relação usando o método detach do relacionamento many-to-many
        $combo->cursos()->detach($cursoId);

        return redirect()->back()->with('success', 'Curso removido do combo com sucesso!');
    }

    public function pagina($url)
    {
       // Busca o combo pelo campo "url", se não achar, retorna 404
        $combo = \App\Models\Combo::where('url', $url)->firstOrFail();

        // Para cada curso do combo, processa o conteúdo
        foreach ($combo->cursos as $curso) {
            if (!empty($curso->conteudo_principal)) {
                $curso->conteudo = $this->lista_conteudo($curso->conteudo_principal);
            } else {
                $curso->conteudo = [];
            }
        }

        // Direciona para a blade 'adm.combos.pagina_de_venda', passando o combo
        return view('adm.combos.pagina_de_venda', compact('combo'));
    }

    public function lista_conteudo($html){

        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        if(!$html){return null;}

        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $listItems = $dom->getElementsByTagName('li');

        $courses = [];
        $currentCourse = null;

        foreach ($listItems as $item) {
            $text = $item->textContent;
            $class = $item->getAttribute('class');

            if (strpos($class, 'ql-indent-1') === false) {
                // Novo curso
                if ($currentCourse) {
                    $courses[] = $currentCourse;
                }
                $currentCourse = ['title' => $text, 'topics' => []];
            } else {
                // Adiciona tópico ao curso atual
                $currentCourse['topics'][] = $text;
            }
        }

        // Adiciona o último curso, se existir
        if ($currentCourse) {
            $courses[] = $currentCourse;
        }

        return $courses;
    }

}
