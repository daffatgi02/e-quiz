<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     * Override default redirection based on user role
     */
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
}
