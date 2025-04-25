<?php
// routes/web.php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\QuizAttemptController;
use App\Http\Controllers\LanguageController;

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('quiz.index');
    }
    return redirect()->route('login');
});

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Language Switch
Route::get('language/{lang}', [LanguageController::class, 'switchLang'])->name('language.switch');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [ReportController::class, 'index'])->name('dashboard');
    // History per user
    Route::get('users/{user}/history', [AdminUserController::class, 'history'])->name('users.history');
    // Quiz Management
    Route::resource('quizzes', AdminQuizController::class);
    Route::resource('quizzes.questions', AdminQuestionController::class);

    // User Management
    Route::resource('users', AdminUserController::class);
    Route::post('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/quiz/{quiz}', [ReportController::class, 'quizReport'])->name('reports.quiz');
    Route::get('reports/attempt/{attempt}', [ReportController::class, 'attemptDetail'])->name('reports.attempt.detail');
    Route::get('reports/export/quiz/{quiz}', [ReportController::class, 'exportQuizResults'])->name('reports.export.quiz');
    Route::get('reports/export/attempt/{attempt}', [ReportController::class, 'exportSingleAttempt'])->name('reports.export.attempt');

    // Fix route bulk export - seharusnya di dalam group admin dan dengan parameter quiz
    Route::post('reports/export/bulk/{quiz}', [ReportController::class, 'exportBulk'])->name('reports.export.bulk');
    Route::get('quizzes/{quiz}/track', [AdminQuizController::class, 'track'])->name('quizzes.track');
    // Reset attempt route
    Route::post('quizzes/{quiz}/reset-attempt/{user}', [AdminQuizController::class, 'resetAttempt'])->name('quizzes.reset-attempt');
});

Route::middleware(['auth'])->group(function () {
    // Route untuk melihat daftar quiz
    Route::get('quiz', [QuizAttemptController::class, 'index'])->name('quiz.index');

    // Route untuk memulai quiz - tambahkan middleware di sini
    Route::get('quiz/{quiz}/start', [QuizAttemptController::class, 'start'])
        ->name('quiz.start')
        ->middleware('check_quiz_progress');

    // Route lainnya tetap sama
    Route::get('quiz/{attempt}/take', [QuizAttemptController::class, 'take'])->name('quiz.take');
    Route::post('quiz/{attempt}/submit', [QuizAttemptController::class, 'submit'])->name('quiz.submit');
    Route::get('quiz/{attempt}/result', [QuizAttemptController::class, 'result'])->name('quiz.result');
    Route::post('quiz/save-answer', [QuizAttemptController::class, 'saveAnswer'])->name('quiz.save-answer');
});
