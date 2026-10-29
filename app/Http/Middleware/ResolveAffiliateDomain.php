<?php

namespace App\Http\Middleware;

use App\Services\AffiliateDomainResolver;
use Closure;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ResolveAffiliateDomain
{
    public function __construct(private AffiliateDomainResolver $resolver) {}

    public function handle(Request $request, Closure $next): Response
    {
        $host = $this->resolver->normalize($request->getHost());
        if (! $this->resolver->isOfficial($host) && ! $this->resolver->resolve($host)) {
            return redirect()->away('https://portalje.org/', 302)
                ->header('Cache-Control', 'no-store');
        }

        $response = $next($request);
        if (in_array($request->method(), ['GET', 'HEAD'], true) && $response->isRedirect()
            && $this->redirectsToCurrentUrl($request, $response->headers->get('Location'))) {
            Log::warning('Blocked a web redirect to the current URL.', [
                'host' => $host,
                'route' => $request->route()?->getName(),
                'status' => $response->getStatusCode(),
            ]);

            return response()->view('errors.500', [], 500)->header('Cache-Control', 'no-store');
        }

        return $response;
    }

    private function redirectsToCurrentUrl(Request $request, ?string $location): bool
    {
        if ($location === null) {
            return false;
        }

        $current = new Uri($request->getUri());
        $target = UriResolver::resolve($current, new Uri($location));
        $canonical = fn (Uri $uri) => [
            strtolower($uri->getScheme()),
            rtrim(strtolower($uri->getHost()), '.'),
            $uri->getPort(),
            $uri->getPath() ?: '/',
            $uri->getQuery(),
        ];

        return $canonical($current) === $canonical($target);
    }
}
