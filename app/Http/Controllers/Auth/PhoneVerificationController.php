<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PhoneVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PhoneVerificationController extends Controller
{
    public function show(Request $request, PhoneVerificationService $phoneVerificationService): RedirectResponse|View
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if ($user->hasVerifiedPhone()) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-whatsapp', [
            'requestPhoneUrl' => $phoneVerificationService->requestUrl(),
            'requestPhoneNumber' => $phoneVerificationService->requestPhone(),
            'pendingPhone' => $user->telefone_pessoal_1_pending,
        ]);
    }

    public function send(Request $request, PhoneVerificationService $phoneVerificationService): RedirectResponse
    {
        $validated = $request->validate([
            'telefone_pessoal_1' => ['required', 'regex:/^[0-9]{11,14}$/'],
        ], [
            'telefone_pessoal_1.regex' => 'Digite o WhatsApp com DDI e apenas numeros.',
        ]);

        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $phone = preg_replace('/\D/', '', (string) $validated['telefone_pessoal_1']);

        $phoneVerificationService->start($user, $phone);

        return redirect()->route('phone.verification.notice')->with('status', 'phone-code-sent');
    }

    public function confirm(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->matchesPhoneVerificationCode((string) $request->input('code'))) {
            return back()->withErrors([
                'code' => 'Codigo invalido ou expirado.',
            ])->onlyInput('code');
        }

        $user->confirmPhoneVerification();

        return redirect()->route('dashboard')->with('status', 'phone-verified');
    }
}
