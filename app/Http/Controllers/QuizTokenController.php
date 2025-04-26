<?php
// app/Http/Controllers/QuizTokenController.php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizTokenController extends Controller
{
    public function showTokenForm()
    {
        return view('quiz.token-form');
    }

    public function validateToken(Request $request)
    {
        $request->validate([
            'quiz_token' => 'required|string|regex:/^[A-Z]{4}-[A-Z]{4}$/'
        ], [
            'quiz_token.required' => 'Token harus diisi',
            'quiz_token.regex' => 'Format token tidak valid. Format yang benar: ABCD-EFGH'
        ]);

        $quiz = Quiz::where('quiz_token', $request->quiz_token)
            ->where('is_active', true)
            ->where('requires_token', true)
            ->first();

        if (!$quiz) {
            return back()->withErrors(['quiz_token' => 'Token tidak ditemukan atau quiz tidak aktif'])
                ->withInput();
        }

        if (!$quiz->isTokenValid()) {
            return back()->withErrors(['quiz_token' => 'Token sudah kadaluarsa. Silakan hubungi administrator.'])
                ->withInput();
        }

        // Cek apakah user sudah menggunakan token ini sebelumnya
        if ($quiz->tokenUsers()->where('user_id', auth()->id())->exists()) {
            return redirect()->route('quiz.index')
                ->with('info', 'Anda sudah menggunakan token ini sebelumnya. Quiz sudah tersedia di dashboard Anda.');
        }

        // Simpan ke pivot table
        $quiz->tokenUsers()->attach(auth()->id(), ['token_used_at' => now()]);

        return redirect()->route('quiz.index')
            ->with('success', 'Token berhasil divalidasi. Quiz "' . $quiz->title . '" sudah tersedia di dashboard Anda.');
    }
}
