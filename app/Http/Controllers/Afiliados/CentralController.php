<?php

namespace App\Http\Controllers\Afiliados;

use App\Http\Controllers\Controller;
use App\Models\Curso;

class CentralController extends Controller
{
    public function index()
    {
        $cursos = Curso::query()
            ->where('publicado', true)
            ->where('permitir_afiliacao', true)
            ->orderBy('ordem')
            ->orderBy('id')
            ->get(['id', 'titulo', 'capa_horizontal', 'capa_quadrada', 'link_materiais', 'link_afiliacao']);

        return view('afiliados.central', compact('cursos'));
    }
}
