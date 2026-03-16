<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Middleware\EnsurePhoneIsVerified;
use App\Http\Middleware\MinhaJornada;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\RedirectWwwToNonWww;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'minha_jornada' => MinhaJornada::class,
            'check.admin' => CheckAdmin::class,
            'verified.phone' => EnsurePhoneIsVerified::class,
        ]);

        
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $handle419 = function (Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sessao expirada. Atualize a pagina e tente novamente.',
                ], 419);
            }

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->is('login')) {
                return redirect()
                    ->route('login')
                    ->with('error', 'Sua sessao expirou. Tente novamente.');
            }

            return redirect()
                ->to(url()->previous() ?: route('login'))
                ->with('error', 'Sua sessao expirou. Recarregamos para voce tentar de novo.');
        };

        $exceptions->render(function (TokenMismatchException $e, Request $request) use ($handle419) {
            return $handle419($request);
        });

        $exceptions->render(function (HttpException $e, Request $request) use ($handle419) {
            if ($e->getStatusCode() === 419) {
                return $handle419($request);
            }
        });
    })->create();
