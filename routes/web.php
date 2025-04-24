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

    // Quiz Management
    Route::resource('quizzes', AdminQuizController::class);
    Route::resource('quizzes.questions', AdminQuestionController::class);

    // User Management
    Route::resource('users', AdminUserController::class);
    Route::post('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/quiz/{quiz}', [ReportController::class, 'quizReport'])->name('reports.quiz');
    Route::get('reports/user/{user}', [ReportController::class, 'userReport'])->name('reports.user');
    Route::get('reports/pending-grading', [ReportController::class, 'pendingGrading'])->name('reports.pending-grading');
    Route::post('reports/grade/{answer}', [ReportController::class, 'gradeAnswer'])->name('reports.grade');
    Route::get('reports/export/quiz/{quiz}', [ReportController::class, 'exportQuizResults'])->name('reports.export.quiz');
});

// User Quiz Routes
Route::middleware(['auth'])->group(function () {
    Route::get('quiz', [QuizAttemptController::class, 'index'])->name('quiz.index');
    Route::get('quiz/{quiz}/start', [QuizAttemptController::class, 'start'])->name('quiz.start');
    Route::get('quiz/{attempt}/take', [QuizAttemptController::class, 'take'])->name('quiz.take');
    Route::post('quiz/{attempt}/submit', [QuizAttemptController::class, 'submit'])->name('quiz.submit');
    Route::get('quiz/{attempt}/result', [QuizAttemptController::class, 'result'])->name('quiz.result');
    Route::post('quiz/save-answer', [QuizAttemptController::class, 'saveAnswer'])->name('quiz.save-answer');
});
