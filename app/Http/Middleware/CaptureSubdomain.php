<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CaptureSubdomain
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $parts = explode('.', $host);

        // Assumindo que seu domínio é algo como subdomain.portalje.org
        if (count($parts) > 2) {
            $subdomain = $parts[0];
            $request->attributes->set('subdomain', $subdomain);
        } else {
            $request->attributes->set('subdomain', null);
        }

        return $next($request);
    }
}
