<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $statistics = [
            'total_quizzes' => Quiz::count(),
            'total_users' => User::where('is_admin', false)->count(),
            'total_attempts' => QuizAttempt::count(),
            'average_score' => QuizAttempt::where('status', 'graded')->avg('score'),
            'pending_grading' => UserAnswer::where('is_graded', false)->count(),
        ];

        $recentAttempts = QuizAttempt::with(['user', 'quiz'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.reports.index', compact('statistics', 'recentAttempts'));
    }

    public function quizReport(Quiz $quiz)
    {
        $attempts = $quiz->attempts()
            ->with('user')
            ->orderBy('score', 'desc')
            ->get();

        $statistics = [
            'total_attempts' => $attempts->count(),
            'completed' => $attempts->where('status', 'completed')->count(),
            'graded' => $attempts->where('status', 'graded')->count(),
            'average_score' => $attempts->where('status', 'graded')->avg('score'),
            'highest_score' => $attempts->where('status', 'graded')->max('score'),
            'lowest_score' => $attempts->where('status', 'graded')->min('score'),
        ];

        return view('admin.reports.quiz', compact('quiz', 'attempts', 'statistics'));
    }

    public function userReport(User $user)
    {
        $attempts = $user->quizAttempts()
            ->with('quiz')
            ->latest()
            ->get();

        $statistics = [
            'total_attempts' => $attempts->count(),
            'completed' => $attempts->where('status', 'completed')->count(),
            'graded' => $attempts->where('status', 'graded')->count(),
            'average_score' => $attempts->where('status', 'graded')->avg('score'),
            'highest_score' => $attempts->where('status', 'graded')->max('score'),
            'lowest_score' => $attempts->where('status', 'graded')->min('score'),
        ];

        return view('admin.reports.user', compact('user', 'attempts', 'statistics'));
    }

    public function pendingGrading()
    {
        $pendingAnswers = UserAnswer::with(['quizAttempt.user', 'question'])
            ->where('is_graded', false)
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return view('admin.reports.pending-grading', compact('pendingAnswers'));
    }

    public function gradeAnswer(Request $request, UserAnswer $answer)
    {
        $validated = $request->validate([
            'points_earned' => 'required|numeric|min:0|max:' . $answer->question->points
        ]);

        $answer->update([
            'points_earned' => $validated['points_earned'],
            'is_graded' => true
        ]);

        // Check if all answers for this attempt are graded
        $attempt = $answer->quizAttempt;
        if (!$attempt->answers()->where('is_graded', false)->exists()) {
            $totalScore = $attempt->answers()->sum('points_earned');
            $attempt->update([
                'status' => 'graded',
                'score' => $totalScore
            ]);
        }

        return redirect()->back()
            ->with('success', __('quiz.answer_graded'));
    }

    public function exportQuizResults(Quiz $quiz)
    {
        $attempts = $quiz->attempts()
            ->with(['user', 'answers.question'])
            ->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=quiz_results_{$quiz->id}.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['User', 'NIK', 'Department', 'Position', 'Started At', 'Completed At', 'Score', 'Status'];

        $callback = function() use ($attempts, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($attempts as $attempt) {
                fputcsv($file, [
                    $attempt->user->name,
                    $attempt->user->nik,
                    $attempt->user->department,
                    $attempt->user->position,
                    $attempt->started_at->format('Y-m-d H:i:s'),
                    $attempt->completed_at?->format('Y-m-d H:i:s'),
                    $attempt->score,
                    $attempt->status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
