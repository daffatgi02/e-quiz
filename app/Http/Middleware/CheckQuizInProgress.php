<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\QuizAttempt;

class CheckQuizInProgress
{
    public function handle(Request $request, Closure $next)
    {
        // Cek SEMUA quiz yang sedang in_progress untuk user ini
        $inProgressAttempts = QuizAttempt::where('user_id', auth()->id())
            ->where('status', 'in_progress')
            ->with('quiz')
            ->get();

        if ($inProgressAttempts->isNotEmpty()) {
            // Cek setiap attempt
            foreach ($inProgressAttempts as $attempt) {
                $endTime = $attempt->started_at->addMinutes($attempt->quiz->duration);

                if (now()->lt($endTime)) {
                    // Jika masih ada waktu dan user mencoba memulai quiz APAPUN
                    if ($request->route()->getName() === 'quiz.start') {
                        return redirect()->route('quiz.index')
                            ->with('error', __('quiz.must_complete_ongoing_quiz'));
                    }
                } else {
                    // Jika waktu sudah habis, otomatis ubah status menjadi completed
                    $attempt->update([
                        'status' => 'completed',
                        'completed_at' => now()
                    ]);
                }
            }
        }

        return $next($request);
    }
}
