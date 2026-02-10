<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'api/receber/users', // Excluir a rota da verificação de CSRF
        'receber/users',     // Caso a rota esteja em web.php
        '/api/receive-data',     // Caso a rota esteja em web.php
    ];
}
