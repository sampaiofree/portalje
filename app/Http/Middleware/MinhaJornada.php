<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\JourneyProgressService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class MinhaJornada
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $journeyProgressService = app(JourneyProgressService::class);

            try {
                $user = User::find(Auth::id());

                if ($user) {
                    $this->shareDashboardData($journeyProgressService->buildForUser($user));
                } else {
                    $this->shareDashboardData($journeyProgressService->defaultPayload());
                }
            } catch (Throwable $e) {
                report($e);
                $this->shareDashboardData($journeyProgressService->defaultPayload());
            }
        }

        return $next($request);
    }

    private function shareDashboardData(array $payload): void
    {
        foreach ($payload as $key => $value) {
            view()->share($key, $value);
        }
    }
}
