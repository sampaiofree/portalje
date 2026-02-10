<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RedirectController extends Controller
{
    /**
     * Redireciona para uma URL especificada na query string.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectWithUrl(Request $request)
    {
        // Obter o URL de redirecionamento da query string
        $url = $request->query('url');
        $iframe = $request->query('iframe');

        

        // Redirecionar para o URL especificado
        return view('redirect', compact('url', 'iframe'));
    }
}
