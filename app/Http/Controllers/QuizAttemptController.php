<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\UserAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class QuizAttemptController extends Controller
{
    public function index()
    {
        // Pertama, ambil semua attempt yang masih in_progress
        $inProgressAttempts = QuizAttempt::where('user_id', auth()->id())
            ->where('status', 'in_progress')
            ->with('quiz')
            ->get();

        // Cek timeout untuk setiap attempt
        foreach ($inProgressAttempts as $attempt) {
            $endTime = $attempt->started_at->addMinutes($attempt->quiz->duration);
            if (now()->gt($endTime)) {
                $attempt->update([
                    'status' => 'completed',
                    'completed_at' => now()
                ]);
            }
        }

        // Refresh data setelah update
        $inProgressAttempts = QuizAttempt::where('user_id', auth()->id())
            ->where('status', 'in_progress')
            ->with('quiz')
            ->get();

        // Quiz yang sedang aktif
        $activeQuizzes = Quiz::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                // Quiz tanpa token
                $query->where('requires_token', false)
                    // Atau quiz dengan token yang sudah digunakan user
                    ->orWhere(function ($q) {
                        $q->where('requires_token', true)
                            ->whereHas('tokenUsers', function ($qu) {
                                $qu->where('user_id', auth()->id());
                            });
                    });
            })
            ->get();

        // Quiz yang akan datang
        $upcomingQuizzes = Quiz::where('is_active', true)
            ->where('start_date', '>', now())
            ->orderBy('start_date', 'asc')
            ->get();

        $quizHistory = QuizAttempt::where('user_id', auth()->id())
            ->with('quiz')
            ->latest()
            ->paginate(10);

        return view('quizzes.index', compact('activeQuizzes', 'upcomingQuizzes', 'quizHistory', 'inProgressAttempts'));
    }

    public function start(Quiz $quiz)
    {
        // Cek apakah user sudah memiliki attempt yang sedang berjalan untuk quiz APAPUN
        $existingInProgressAttempt = QuizAttempt::where('user_id', auth()->id())
            ->where('status', 'in_progress')
            ->first();

        if ($existingInProgressAttempt) {
            return redirect()->route('quiz.index')
                ->with('error', __('quiz.must_complete_ongoing_quiz'));
        }

        // Cek apakah quiz single attempt dan sudah pernah dikerjakan
        if ($quiz->single_attempt) {
            $existingAttempt = $quiz->attempts()
                ->where('user_id', auth()->id())
                ->exists();

            if ($existingAttempt) {
                return redirect()->route('quiz.index')
                    ->with('error', __('quiz.already_attempted'));
            }
        }

        // Buat attempt baru
        $attempt = QuizAttempt::create([
            'user_id' => auth()->id(),
            'quiz_id' => $quiz->id,
            'started_at' => now(),
            'status' => 'in_progress'
        ]);

        return redirect()->route('quiz.take', $attempt);
    }

    public function take(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            return redirect()->route('quiz.index')
                ->with('error', __('quiz.invalid_attempt'));
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('quiz.index')
                ->with('error', __('quiz.attempt_already_completed'));
        }

        if ($attempt->kicked) {
            return redirect()->route('quiz.index')
                ->with('error', __('quiz.kicked_by_admin'));
        }

        $quiz = $attempt->quiz;
        $questions = $quiz->questions()->with('options')->get();

        // Load existing answers for this attempt
        $savedAnswers = $attempt->answers()->with(['question', 'questionOption'])->get();

        // Calculate remaining time
        $endTime = $attempt->started_at->addMinutes($quiz->duration);
        $remainingSeconds = now()->diffInSeconds($endTime, false);

        if ($remainingSeconds <= 0) {
            $this->submit($attempt);
            return redirect()->route('quiz.result', $attempt);
        }

        return view('quizzes.take', compact('attempt', 'quiz', 'questions', 'remainingSeconds', 'savedAnswers'));
    }

    // app/Http/Controllers/QuizAttemptController.php
    public function submit(QuizAttempt $attempt, Request $request)
    {
        if ($attempt->user_id !== auth()->id() || $attempt->status !== 'in_progress') {
            return redirect()->route('quiz.index')
                ->with('error', __('quiz.invalid_attempt'));
        }

        DB::transaction(function () use ($attempt, $request) {
            if ($request) {
                foreach ($request->input('answers', []) as $questionId => $answer) {
                    $question = $attempt->quiz->questions()->find($questionId);

                    if (!$question) continue;

                    $userAnswer = [
                        'quiz_attempt_id' => $attempt->id,
                        'question_id' => $questionId,
                    ];

                    if ($question->type === 'multiple_choice') {
                        $selectedOption = $question->options()->find($answer);
                        if ($selectedOption) {
                            $userAnswer['question_option_id'] = $selectedOption->id;
                            // Perbaikan di sini - seharusnya memeriksa apakah jawaban benar
                            $userAnswer['points_earned'] = $selectedOption->is_correct ? $question->points : 0;
                            $userAnswer['is_graded'] = true;
                        }
                    } else {
                        $userAnswer['essay_answer'] = $answer;
                        $userAnswer['is_graded'] = !$question->requires_manual_grading;
                        // Untuk essay yang tidak perlu dinilai manual, beri nilai penuh
                        if (!$question->requires_manual_grading) {
                            $userAnswer['points_earned'] = $question->points;
                        }
                    }

                    UserAnswer::create($userAnswer);
                }
            }

            // Hitung total skor untuk jawaban yang sudah dinilai
            $totalPoints = $attempt->answers()
                ->where('is_graded', true)
                ->sum('points_earned');

            // Update attempt dengan skor total
            $attempt->update([
                'completed_at' => now(),
                'status' => $attempt->answers()->where('is_graded', false)->exists() ? 'completed' : 'graded',
                'score' => $totalPoints
            ]);
        });

        return redirect()->route('quiz.result', $attempt);
    }
    public function checkStatus(QuizAttempt $attempt)
    {
        // Log untuk debug
        Log::info('Check status for attempt #' . $attempt->id . ', kicked = ' . ($attempt->kicked ? 'true' : 'false'));

        // Make sure to cast to boolean
        return response()->json([
            'kicked' => (bool)$attempt->kicked
        ]);
    }
    public function result(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            return redirect()->route('quiz.index')
                ->with('error', __('quiz.invalid_attempt'));
        }

        return view('quizzes.result', compact('attempt'));
    }

    public function saveAnswer(Request $request)
    {
        $validated = $request->validate([
            'attempt_id' => 'required|exists:quiz_attempts,id',
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required'
        ]);

        $attempt = QuizAttempt::findOrFail($validated['attempt_id']);

        if ($attempt->user_id !== auth()->id() || $attempt->status !== 'in_progress') {
            return response()->json(['error' => __('quiz.invalid_attempt')], 403);
        }

        $question = $attempt->quiz->questions()->findOrFail($validated['question_id']);

        $userAnswer = UserAnswer::updateOrCreate(
            [
                'quiz_attempt_id' => $attempt->id,
                'question_id' => $question->id,
            ],
            [
                'question_option_id' => $question->type === 'multiple_choice' ? $validated['answer'] : null,
                'essay_answer' => $question->type === 'essay' ? $validated['answer'] : null,
            ]
        );

        return response()->json(['success' => true]);
    }
}
