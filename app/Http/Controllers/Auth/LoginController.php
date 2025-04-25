<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected function redirectTo()
    {
        if (auth()->user()->is_admin) {
            return '/admin/dashboard';
        }

        return '/quiz';
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // Show admin login form
    public function showAdminLoginForm()
    {
        return view('auth.admin-login');
    }

    // Admin login handler
    public function adminLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($this->attemptLogin($request)) {
            $user = auth()->user();

            // Pastikan hanya admin yang bisa login
            if (!$user->is_admin) {
                auth()->logout();
                return back()->withErrors(['email' => 'Anda bukan admin. Silakan gunakan login token.']);
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['email' => 'Kredensial tidak cocok']);
    }
}
