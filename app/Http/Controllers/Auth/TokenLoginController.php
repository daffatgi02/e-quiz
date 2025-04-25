<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.token-login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'login_token' => 'required|string|size:8'
        ]);

        $user = User::where('login_token', strtoupper($validated['login_token']))->first();

        if (!$user) {
            return back()->withErrors(['login_token' => 'ID-Trainer tidak valid']);
        }

        if (!$user->is_active) {
            return back()->withErrors(['login_token' => 'Akun Anda tidak aktif']);
        }

        // Simpan user di session
        session(['pending_user_id' => $user->id]);

        // Jika PIN belum diset, redirect ke form set PIN
        if (!$user->pin_set) {
            return redirect()->route('token.set-pin');
        }

        // Jika PIN sudah diset, redirect ke form verifikasi PIN
        return redirect()->route('token.verify-pin');
    }

    public function showSetPinForm()
    {
        if (!session('pending_user_id')) {
            return redirect()->route('token.login');
        }

        $user = User::findOrFail(session('pending_user_id'));
        return view('auth.set-pin', compact('user'));
    }

    public function setPin(Request $request)
    {
        $validated = $request->validate([
            'pin' => 'required|string|size:6|confirmed',
        ]);

        if (!session('pending_user_id')) {
            return redirect()->route('token.login');
        }

        $user = User::findOrFail(session('pending_user_id'));
        $user->setPin($validated['pin']);

        session()->forget('pending_user_id');
        Auth::login($user);

        return redirect()->route('quiz.index');
    }

    public function showVerifyPinForm()
    {
        if (!session('pending_user_id')) {
            return redirect()->route('token.login');
        }

        $user = User::findOrFail(session('pending_user_id'));
        return view('auth.verify-pin', compact('user'));
    }

    public function verifyPin(Request $request)
    {
        $validated = $request->validate([
            'pin' => 'required|string|size:6'
        ]);

        if (!session('pending_user_id')) {
            return redirect()->route('token.login');
        }

        $user = User::findOrFail(session('pending_user_id'));

        if (!$user->verifyPin($validated['pin'])) {
            return back()->withErrors(['pin' => 'PIN tidak valid']);
        }

        session()->forget('pending_user_id');
        Auth::login($user);

        return redirect()->route('quiz.index');
    }
}
