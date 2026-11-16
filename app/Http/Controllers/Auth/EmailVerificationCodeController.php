<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationCodeController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        if (!$user->matchesEmailVerificationCode((string) $request->input('code'))) {
            return back()->withErrors([
                'code' => 'Codigo invalido ou expirado.',
            ])->onlyInput('code');
        }

        $user->markEmailAsVerified();
        $user->clearEmailVerificationCode();

        event(new Verified($user));

        return redirect()->route('dashboard')->with('status', 'email-verified');
    }
}
