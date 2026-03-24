<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/user/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function showLoginForm(Request $request)
    {
        if ($redirect = $this->legacyLoginRedirect($request)) {
            return $redirect;
        }

        // Always issue a fresh CSRF token for the login form.
        $request->session()->regenerateToken();

        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->legacyLoginRedirect($request) ?? redirect('/login');
    }

    private function legacyLoginRedirect(Request $request): ?RedirectResponse
    {
        $host = preg_replace('/^www\./', '', strtolower(trim((string) $request->getHost())));

        if ($host !== 'jovemempreendedor.org') {
            return null;
        }

        $target = 'https://portalje.org/login';
        $query = $request->getQueryString();

        if (!empty($query)) {
            $target .= '?' . $query;
        }

        return redirect()->away($target);
    }
}
