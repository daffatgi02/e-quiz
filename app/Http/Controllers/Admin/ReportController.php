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
        $quizzes = Quiz::withCount('attempts')
            ->with(['attempts' => function($query) {
                $query->select('quiz_id')
                    ->selectRaw('COUNT(*) as total')
                    ->selectRaw('AVG(score) as average_score')
                    ->selectRaw('MAX(score) as highest_score')
                    ->selectRaw('MIN(score) as lowest_score')
                    ->where('status', 'graded')
                    ->groupBy('quiz_id');
            }])
            ->latest()
            ->paginate(10);

        return view('admin.reports.index', compact('quizzes'));
    }

    public function quizReport(Quiz $quiz)
    {
        $attempts = $quiz->attempts()
            ->with(['user', 'answers.question'])
            ->orderBy('score', 'desc')
            ->get();

        $passingScore = 70;

        $statistics = [
            'total_attempts' => $attempts->count(),
            'completed' => $attempts->where('status', 'completed')->count(),
            'graded' => $attempts->where('status', 'graded')->count(),
            'average_score' => round($attempts->where('status', 'graded')->avg('score'), 2),
            'highest_score' => $attempts->where('status', 'graded')->max('score'),
            'lowest_score' => $attempts->where('status', 'graded')->min('score'),
            'passed' => $attempts->where('status', 'graded')->where('score', '>=', $passingScore)->count(),
            'failed' => $attempts->where('status', 'graded')->where('score', '<', $passingScore)->count(),
            'passing_rate' => $attempts->where('status', 'graded')->count() > 0
                ? round(($attempts->where('status', 'graded')->where('score', '>=', $passingScore)->count() / $attempts->where('status', 'graded')->count()) * 100, 2)
                : 0
        ];

        return view('admin.reports.quiz', compact('quiz', 'attempts', 'statistics', 'passingScore'));
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

        $callback = function () use ($attempts, $columns) {
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
        return Excel::download(new QuizResultsExport($quiz), 'quiz_' . $quiz->id . '_results.xlsx');
        return response()->stream($callback, 200, $headers);
    }
    public function attemptDetail(QuizAttempt $attempt)
    {
        $attempt->load(['quiz', 'user', 'answers.question.options', 'answers.questionOption']);
        return view('admin.reports.attempt-detail', compact('attempt'));
    }
    public function exportSingleAttempt(QuizAttempt $attempt)
    {
        $attempt->load(['quiz', 'user', 'answers.question.options', 'answers.questionOption']);
        return Excel::download(
            new QuizResultsExport($attempt->quiz, collect([$attempt])),
            'quiz_' . $attempt->quiz->id . '_user_' . $attempt->user->id . '_result.xlsx'
        );
    }

    public function exportBulk(Request $request, Quiz $quiz)
    {
        $attemptIds = $request->input('attempt_ids', []);
        $attempts = $quiz->attempts()
            ->whereIn('id', $attemptIds)
            ->with(['user', 'answers.question.options', 'answers.questionOption'])
            ->get();

        return Excel::download(
            new QuizResultsExport($quiz, $attempts),
            'quiz_' . $quiz->id . '_bulk_results.xlsx'
        );
    }
}
