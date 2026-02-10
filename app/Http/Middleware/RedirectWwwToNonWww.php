<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectWwwToNonWww
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Verifica se o domínio contém 'www'
        if (strpos($request->getHost(), 'www.') === 0) {
            // Remove o 'www.' e redireciona
            $nonWwwUrl = preg_replace('/^www\./', '', $request->fullUrl());

            return redirect($nonWwwUrl, 301);
        }

        return $next($request);
    }
}
