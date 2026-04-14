<?php
use App\Http\Controllers\AdminCertificateVerificationController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CertificateVerificationController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonProgressController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/',         [HomeController::class, 'index'])->name('home');
Route::get('/login',    [GoogleAuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [GoogleAuthController::class, 'login'])->name('login.post');
Route::post('/locale',  [LocaleController::class, 'update'])->name('locale.update');

Route::get('/signup',   [GoogleAuthController::class, 'showSignupChoice'])->name('signup');
Route::get('/signup/learner',   [GoogleAuthController::class, 'showSignup'])->name('signup.learner');
Route::get('/signup/educator',  [GoogleAuthController::class, 'showEducatorSignup'])->name('signup.educator');
Route::post('/signup',  [GoogleAuthController::class, 'register'])->name('signup.post');

Route::get('/auth/google',          [GoogleAuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [GoogleAuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout',   [GoogleAuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');
    Route::get('/certificates/{certificate}', [CertificateController::class, 'show'])->name('certificates.show');
    Route::get('/certificate-verifications/{verification}/download', [CertificateVerificationController::class, 'download'])->name('certificate-verifications.download');

    Route::middleware('educator')->group(function () {
        Route::post('/certificate-verifications', [CertificateVerificationController::class, 'store'])->name('certificate-verifications.store');

        Route::get('lessons/create', [LessonController::class, 'create'])->name('lessons.create');

        // Rate-limited lesson creation and updates
        Route::middleware('throttle:lesson-create')->post('lessons', [LessonController::class, 'store'])->name('lessons.store');
    });

    Route::middleware('lesson-manager')->group(function () {
        Route::post('/lessons/check-title', [LessonController::class, 'checkTitle'])->name('lessons.check-title');
        Route::middleware('throttle:lesson-update')->post('lessons/autosave', [LessonController::class, 'autosave'])->name('lessons.autosave');
        Route::get('lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
        Route::middleware('throttle:lesson-update')->put('lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
        Route::middleware('throttle:lesson-update')->post('lessons/{lesson}/publish', [LessonController::class, 'publish'])->name('lessons.publish');
        Route::middleware('throttle:lesson-create')->delete('lessons/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');

        // Soft delete management routes
        Route::middleware('throttle:lesson-create')->post('lessons/{id}/restore', [LessonController::class, 'restore'])->name('lessons.restore');
        Route::middleware('throttle:lesson-create')->delete('lessons/{id}/force', [LessonController::class, 'forceDelete'])->name('lessons.force-delete');
    });

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/certificate-verifications', [AdminCertificateVerificationController::class, 'index'])->name('certificate-verifications.index');
        Route::patch('/certificate-verifications/{verification}/approve', [AdminCertificateVerificationController::class, 'approve'])->name('certificate-verifications.approve');
        Route::patch('/certificate-verifications/{verification}/reject', [AdminCertificateVerificationController::class, 'reject'])->name('certificate-verifications.reject');
    });

    // Exam grading
    Route::post('lessons/{lesson}/grade-exam', [LessonController::class, 'gradeExam'])->name('lessons.grade-exam');
    Route::middleware('throttle:lesson-read')->post('lessons/{lesson}/progress', [LessonProgressController::class, 'store'])->name('lessons.progress');
    Route::post('lessons/{lesson}/feedback', [LessonController::class, 'storeFeedback'])->name('lessons.feedback.store');
    Route::post('lessons/{lesson}/report', [LessonController::class, 'storeReport'])->name('lessons.report.store');
});

Route::get('lessons', [LessonController::class, 'index'])->name('lessons.index');
Route::get('lessons/{lesson}/documents/main/preview', [LessonController::class, 'previewMainDocument'])->name('lessons.documents.preview');
Route::get('lessons/{lesson}/documents/main/file', [LessonController::class, 'streamMainDocument'])->name('lessons.documents.stream');
Route::get('lessons/{lesson}/segments/{segment}/blocks/{block}/preview', [LessonController::class, 'previewBlockFile'])->name('lessons.block-files.preview');
Route::get('lessons/{lesson}/segments/{segment}/blocks/{block}/file', [LessonController::class, 'streamBlockFile'])->name('lessons.block-files.stream');
Route::get('lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
