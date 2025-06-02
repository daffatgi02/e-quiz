<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::withCount('attempts')
            ->with(['attempts' => function ($query) {
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

    public function exportQuizResults(Request $request, Quiz $quiz)
    {
        $lang = $request->get('lang', 'id'); // Default ke bahasa Indonesia
        app()->setLocale($lang);

        $attempts = $quiz->attempts()
            ->with(['user', 'answers.question.options', 'answers.questionOption'])
            ->get();

        $pdf = PDF::loadView('admin.reports.quiz-results-pdf', [
            'quiz' => $quiz,
            'attempts' => $attempts,
            'lang' => $lang
        ]);

        return $pdf->download('quiz_' . $quiz->id . '_results_' . $lang . '.pdf');
    }

    public function attemptDetail(QuizAttempt $attempt)
    {
        $attempt->load(['quiz', 'user', 'answers.question.options', 'answers.questionOption']);
        return view('admin.reports.attempt-detail', compact('attempt'));
    }

    public function exportSingleAttempt(Request $request, QuizAttempt $attempt)
    {
        $lang = $request->get('lang', 'id');
        app()->setLocale($lang);

        $attempt->load(['quiz', 'user', 'answers.question.options', 'answers.questionOption']);

        $pdf = PDF::loadView('admin.reports.attempt-detail-pdf', [
            'attempt' => $attempt,
            'lang' => $lang
        ]);

        return $pdf->download('quiz_' . $attempt->quiz->id . '_user_' . $attempt->user->id . '_result_' . $lang . '.pdf');
    }

    public function exportBulk(Request $request, Quiz $quiz)
    {
        $attemptIds = $request->input('attempt_ids', []);
        $attempts = $quiz->attempts()
            ->whereIn('id', $attemptIds)
            ->with(['user', 'answers.question.options', 'answers.questionOption'])
            ->get();

        $pdf = PDF::loadView('admin.reports.quiz-results-pdf', [
            'quiz' => $quiz,
            'attempts' => $attempts
        ]);

        return $pdf->download('quiz_' . $quiz->id . '_bulk_results.pdf');
    }

    // NEW METHOD - Show comparison form
    public function showComparisonForm()
    {
        $quizzes = Quiz::orderBy('title', 'asc')->get();
        return view('admin.reports.comparison-form', compact('quizzes'));
    }

    // UPDATED METHOD - Dynamic comparison export
    public function exportTrainingReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'pre_test_quiz_id' => 'required|exists:quizzes,id',
                'post_test_quiz_id' => 'required|exists:quizzes,id|different:pre_test_quiz_id',
                'title' => 'required|string|max:255',
                'lang' => 'sometimes|in:id,en'
            ], [
                'post_test_quiz_id.different' => 'Pre Test dan Post Test tidak boleh sama.',
                'pre_test_quiz_id.required' => 'Pre Test quiz harus dipilih.',
                'post_test_quiz_id.required' => 'Post Test quiz harus dipilih.',
                'title.required' => 'Judul training harus diisi.'
            ]);

            $lang = $validated['lang'] ?? 'id';
            app()->setLocale($lang);

            $preTest = Quiz::findOrFail($validated['pre_test_quiz_id']);
            $postTest = Quiz::findOrFail($validated['post_test_quiz_id']);
            $title = $validated['title'];

            // Debug: Log quiz information
            \Log::info('Pre Test Quiz: ' . $preTest->title . ' (ID: ' . $preTest->id . ')');
            \Log::info('Post Test Quiz: ' . $postTest->title . ' (ID: ' . $postTest->id . ')');

            // Format tanggal dengan aman
            $preTestDate = $preTest->start_date ? $preTest->start_date->format('d F Y') : __('general.not_available');
            $postTestDate = $postTest->start_date ? $postTest->start_date->format('d F Y') : __('general.not_available');
            $exportDate = now()->format('d F Y');

            // Dapatkan semua user yang telah mengambil pre-test atau post-test
            $preTestUserIds = QuizAttempt::where('quiz_id', $preTest->id)
                ->whereIn('status', ['completed', 'graded']) // Include both completed and graded
                ->pluck('user_id')
                ->unique();

            $postTestUserIds = QuizAttempt::where('quiz_id', $postTest->id)
                ->whereIn('status', ['completed', 'graded']) // Include both completed and graded
                ->pluck('user_id')
                ->unique();

            // Debug: Log user counts
            \Log::info('Pre Test Users: ' . $preTestUserIds->count());
            \Log::info('Post Test Users: ' . $postTestUserIds->count());

            $userIds = $preTestUserIds->merge($postTestUserIds)->unique();
            \Log::info('Total Unique Users: ' . $userIds->count());

            // Dapatkan data user
            $users = User::whereIn('id', $userIds)->orderBy('name')->get();

            // Debug: Log users found
            \Log::info('Users found: ' . $users->count());

            // Siapkan data untuk report
            $reportData = [];

            foreach ($users as $index => $user) {
                // Get best attempt for pre-test
                $preTestAttempt = QuizAttempt::where('quiz_id', $preTest->id)
                    ->where('user_id', $user->id)
                    ->whereIn('status', ['completed', 'graded'])
                    ->orderBy('score', 'desc') // Ambil yang tertinggi
                    ->orderBy('completed_at', 'desc') // Jika score sama, ambil yang terbaru
                    ->first();

                // Get best attempt for post-test
                $postTestAttempt = QuizAttempt::where('quiz_id', $postTest->id)
                    ->where('user_id', $user->id)
                    ->whereIn('status', ['completed', 'graded'])
                    ->orderBy('score', 'desc') // Ambil yang tertinggi
                    ->orderBy('completed_at', 'desc') // Jika score sama, ambil yang terbaru
                    ->first();

                $preTestScore = $preTestAttempt ? $preTestAttempt->score : '-';
                $postTestScore = $postTestAttempt ? $postTestAttempt->score : '-';

                // Debug specific user
                if ($index < 3) { // Log first 3 users for debugging
                    \Log::info("User: {$user->name} - Pre: {$preTestScore} - Post: {$postTestScore}");
                }

                // Tentukan passing threshold berdasarkan posisi
                $position = strtoupper($user->position ?? '');
                $isLeadOrSupervisor = str_contains($position, 'LEAD') ||
                    str_contains($position, 'SUPERVISOR') ||
                    str_contains($position, 'SPV') ||
                    str_contains($position, 'MANAGER');

                $passingScore = $isLeadOrSupervisor ? 80 : 70;

                // Tentukan keterangan (hanya untuk post test)
                $keterangan = '-';
                if ($postTestScore !== '-' && is_numeric($postTestScore)) {
                    $keterangan = $postTestScore >= $passingScore ?
                        ($lang == 'en' ? 'PASSED' : 'LULUS') : ($lang == 'en' ? 'FAILED' : 'TIDAK LULUS');
                }

                $reportData[] = [
                    'no' => $index + 1,
                    'nik' => $user->nik ?? '-',
                    'name' => $user->name,
                    'position' => $user->position ?? '-',
                    'department' => $user->department ?? '-',
                    'company' => $user->perusahaan ?? '-',
                    'pre_test_score' => $preTestScore,
                    'post_test_score' => $postTestScore,
                    'keterangan' => $keterangan,
                    'passing_score' => $passingScore
                ];
            }

            // Debug: Log report data count
            \Log::info('Report data count: ' . count($reportData));

            // If no data found, create a sample entry for debugging
            if (empty($reportData)) {
                \Log::warning('No data found for report');

                // Check if there are any quiz attempts at all
                $allPreAttempts = QuizAttempt::where('quiz_id', $preTest->id)->count();
                $allPostAttempts = QuizAttempt::where('quiz_id', $postTest->id)->count();

                \Log::info("All Pre Test attempts: $allPreAttempts");
                \Log::info("All Post Test attempts: $allPostAttempts");

                return redirect()->back()->with(
                    'error',
                    'Tidak ada data yang ditemukan untuk quiz yang dipilih. ' .
                        "Pre Test attempts: $allPreAttempts, Post Test attempts: $allPostAttempts"
                );
            }

            // Load view untuk PDF
            $pdf = PDF::loadView('admin.reports.training-pdf', [
                'title' => $title,
                'reportData' => $reportData,
                'preTestDate' => $preTestDate,
                'postTestDate' => $postTestDate,
                'exportDate' => $exportDate,
                'preTestTitle' => $preTest->title,
                'postTestTitle' => $postTest->title,
                'lang' => $lang,
                'totalUsers' => count($reportData)
            ]);

            // Set paper size and orientation
            $pdf->setPaper('A4', 'landscape');

            // Generate PDF filename dengan tanggal ekspor
            $filename = 'laporan_training_' . Str::slug($title) . '_' . date('Ymd_His') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Error generating training report: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // LEGACY METHOD - Keep for backward compatibility
    public function exportTrainingReportLegacy(Request $request, $type)
    {
        try {
            // Tentukan quiz berdasarkan tipe
            if ($type == 'sanitasi') {
                $preTest = Quiz::where('title', 'like', '%Pre Test Sanitasi%')->first();
                $postTest = Quiz::where('title', 'like', '%Post Test Sanitasi%')->first();
                $title = 'Sanitasi / Hygiene';
            } elseif ($type == 'halal') {
                $preTest = Quiz::where('title', 'like', '%Pre Test Training Halal%')->first();
                $postTest = Quiz::where('title', 'like', '%Post Test Training Halal%')->first();
                $title = 'Training Halal';
            } else {
                return redirect()->back()->with('error', __('general.invalid_report_type'));
            }

            if (!$preTest || !$postTest) {
                return redirect()->back()->with('error', __('general.quiz_not_found'));
            }

            // Use the new dynamic method
            return $this->exportTrainingReport(new Request([
                'pre_test_quiz_id' => $preTest->id,
                'post_test_quiz_id' => $postTest->id,
                'title' => $title,
                'lang' => $request->get('lang', 'id')
            ]));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('general.error_occurred') . ': ' . $e->getMessage());
        }
    }
}
