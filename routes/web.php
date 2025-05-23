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
use App\Http\Controllers\QuizTokenController;

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
    Route::get('users/search', [AdminUserController::class, 'search'])->name('users.search');
    // History per user
    Route::get('users/{user}/history', [AdminUserController::class, 'history'])->name('users.history');
    Route::get('quizzes/{quiz}/questions/export', [AdminQuestionController::class, 'exportQuestions'])->name('quizzes.questions.export');
    Route::get('quizzes/{quiz}/questions/import', [AdminQuestionController::class, 'showImportForm'])->name('quizzes.questions.import');
    Route::post('quizzes/{quiz}/questions/import', [AdminQuestionController::class, 'importQuestions'])->name('quizzes.questions.import.store');
    Route::get('questions/json-format', function () {
        return view('admin.questions.json-format');
    })->name('questions.json-format');

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
    Route::get('reports/export/training/{type}', [App\Http\Controllers\Admin\ReportController::class, 'exportTrainingReport'])
    ->name('reports.export.training');


    // Fix route bulk export - seharusnya di dalam group admin dan dengan parameter quiz
    Route::post('reports/export/bulk/{quiz}', [ReportController::class, 'exportBulk'])->name('reports.export.bulk');
    Route::get('quizzes/{quiz}/track', [AdminQuizController::class, 'track'])->name('quizzes.track');
    Route::post('quizzes/{quiz}/revoke-token/{user}', [AdminQuizController::class, 'revokeToken'])
        ->name('quizzes.revoke-token');
    // Reset attempt route
    Route::post('quizzes/{quiz}/reset-attempt/{user}', [AdminQuizController::class, 'resetAttempt'])->name('quizzes.reset-attempt');
    Route::post('quizzes/{quiz}/reset-all-attempts', [AdminQuizController::class, 'resetAllAttempts'])
    ->name('quizzes.reset-all-attempts');
    // Token Management
    Route::post('users/{user}/generate-token', [App\Http\Controllers\Admin\TokenController::class, 'generateToken'])->name('tokens.generate');
    Route::post('users/{user}/reset-pin', [App\Http\Controllers\Admin\TokenController::class, 'resetPin'])->name('tokens.reset-pin');
    Route::get('tokens/download', [App\Http\Controllers\Admin\TokenController::class, 'downloadTokens'])->name('tokens.download');
    Route::post('quizzes/{quiz}/regenerate-token', [AdminQuizController::class, 'regenerateToken'])
        ->name('quizzes.regenerate-token');
    Route::post('quizzes/{quiz}/kick-user/{attempt}', [AdminQuizController::class, 'kickUser'])
        ->name('quizzes.kick-user');
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
    Route::get('quiz/token', [QuizTokenController::class, 'showTokenForm'])->name('quiz.token.form');
    Route::post('quiz/token/validate', [QuizTokenController::class, 'validateToken'])->name('quiz.token.validate');
    Route::get('/quiz/check-attempt/{attempt}', [QuizAttemptController::class, 'checkStatus'])->name('quiz.check-status');
});

// Admin Login Routes - khusus untuk admin
Route::middleware('guest')->group(function () {
    // Token login routes
    Route::get('/', function () {
        return redirect()->route('token.login');
    });
    Route::get('token-login', [App\Http\Controllers\Auth\TokenLoginController::class, 'showLoginForm'])->name('token.login');
    Route::post('token-login', [App\Http\Controllers\Auth\TokenLoginController::class, 'login']);

    // PIN routes
    Route::get('set-pin', [App\Http\Controllers\Auth\TokenLoginController::class, 'showSetPinForm'])->name('token.set-pin');
    Route::post('set-pin', [App\Http\Controllers\Auth\TokenLoginController::class, 'setPin']);
    Route::get('verify-pin', [App\Http\Controllers\Auth\TokenLoginController::class, 'showVerifyPinForm'])->name('token.verify-pin');
    Route::post('verify-pin', [App\Http\Controllers\Auth\TokenLoginController::class, 'verifyPin']);

    // Admin login
    Route::get('admin-login', [App\Http\Controllers\Auth\LoginController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('admin-login', [App\Http\Controllers\Auth\LoginController::class, 'adminLogin'])->name('admin.login.submit');
});
