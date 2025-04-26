<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\User;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::with('questions')->latest()->paginate(10);
        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('admin.quizzes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'duration' => 'required|integer|min:1',
            'question_type' => 'required|in:multiple_choice,essay,mixed',
            'single_attempt' => 'boolean',
            'is_active' => 'boolean',
            'requires_token' => 'boolean',
            'token_expires_at' => 'nullable|date|after:now',
        ]);

        $quiz = Quiz::create($validated);

        if ($request->requires_token) {
            $quiz->generateToken();
        }

        return redirect()->route('admin.quizzes.index')
            ->with('success', __('quiz.create_success'));
    }

    public function regenerateToken(Quiz $quiz)
    {
        if (!$quiz->requires_token) {
            return back()->with('error', 'Quiz ini tidak memerlukan token');
        }

        // Cek apakah ada user yang sedang mengerjakan
        $activeAttempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->exists();

        if ($activeAttempts) {
            return back()->with('error', 'Tidak dapat regenerate token karena ada peserta yang sedang mengerjakan quiz ini.');
        }

        try {
            $newToken = $quiz->generateToken();
            return back()->with('success', 'Token berhasil digenerate ulang: ' . $newToken);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate token: ' . $e->getMessage());
        }
    }

    public function edit(Quiz $quiz)
    {
        return view('admin.quizzes.edit', compact('quiz'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'duration' => 'required|integer|min:1',
            'question_type' => 'required|in:multiple_choice,essay,mixed',
            'single_attempt' => 'boolean',
            'is_active' => 'boolean',
            'requires_token' => 'boolean',
            'token_expires_at' => 'nullable|date|after:now',
        ];

        $validated = $request->validate($rules);

        // Cek apakah ada user yang sedang mengerjakan quiz ini
        $activeAttempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->exists();

        if ($activeAttempts && $quiz->requires_token) {
            return back()->with('error', 'Tidak dapat mengubah token karena ada peserta yang sedang mengerjakan quiz ini.');
        }

        try {
            DB::transaction(function () use ($quiz, $request, $validated) {
                // Handle token logic
                if ($request->requires_token) {
                    if (!$quiz->requires_token || !$quiz->quiz_token) {
                        // Generate token baru jika diaktifkan dari false ke true
                        $quiz->generateToken();
                    }
                } else {
                    // Hapus token jika dimatikan
                    $quiz->quiz_token = null;
                    $quiz->token_expires_at = null;
                }

                $quiz->update($validated);
            });

            return redirect()->route('admin.quizzes.index')
                ->with('success', __('quiz.update_success'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate quiz: ' . $e->getMessage());
        }
    }

    public function kickUser(Quiz $quiz, QuizAttempt $attempt)
    {
        if ($attempt->status !== 'in_progress') {
            return back()->with('error', 'Peserta tidak sedang mengerjakan quiz');
        }

        try {
            $attempt->update([
                'status' => 'completed',
                'completed_at' => now(),
                'kicked' => true // Tambahkan kolom ini di migration
            ]);

            // Di sini bisa menambahkan notifikasi real-time jika menggunakan websocket

            return back()->with('success', 'Peserta berhasil di-kick dari quiz');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal kick peserta: ' . $e->getMessage());
        }
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('admin.quizzes.index')
            ->with('success', __('quiz.delete_success'));
    }
    public function show(Quiz $quiz)
    {
        $quiz->load('questions.options');
        return view('admin.quizzes.show', compact('quiz'));
    }
    public function resetAttempt(Quiz $quiz, User $user)
    {
        QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->delete();

        return redirect()->back()
            ->with('success', __('quiz.attempt_reset_success'));
    }
    public function track(Quiz $quiz)
    {
        // Hanya ambil attempts yang sudah ada
        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->with('user')
            ->orderBy('started_at', 'desc')
            ->paginate(20);

        $statistics = [
            'in_progress' => $quiz->attempts()->where('status', 'in_progress')->count(),
            'completed' => $quiz->attempts()->where('status', 'completed')->count(),
            'graded' => $quiz->attempts()->where('status', 'graded')->count(),
            'total_participants' => $quiz->attempts()->count(),
        ];

        return view('admin.quizzes.track', compact('quiz', 'attempts', 'statistics'));
    }
}
