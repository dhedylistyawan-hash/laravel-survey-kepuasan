<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController; // <--- pastikan baris ini ada
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\SurveyAnalysisController;
use App\Http\Controllers\BackupController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// ... existing code ...

Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1'); // 5 attempts per minute

// Logout dengan middleware auth untuk keamanan
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/', function () {
    return redirect()->route('login');
});

// Route untuk refresh CSRF token
Route::get('/refresh-csrf', function () {
    return response()->json(['csrf_token' => csrf_token()]);
})->middleware('web');

Route::middleware(['auth', 'admin', 'audit'])->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('questions', QuestionController::class);
    Route::resource('admin/surveys', App\Http\Controllers\Admin\SurveyController::class)->names([
        'index' => 'admin.surveys.index',
        'create' => 'admin.surveys.create',
        'store' => 'admin.surveys.store',
        'edit' => 'admin.surveys.edit',
        'update' => 'admin.surveys.update',
        'destroy' => 'admin.surveys.destroy',
        'show' => 'admin.surveys.show',
    ]);
    Route::get('/export/csv', [ExportController::class, 'exportCsv'])->name('export.csv');
    Route::get('/export/summary', [ExportController::class, 'exportSummary'])->name('export.summary');
    Route::get('/export/pdf-dashboard', [ExportController::class, 'exportPdfDashboard'])->name('export.pdf.dashboard');
    Route::get('/export/pdf-documentation', [ExportController::class, 'exportPdfDocumentation'])->name('export.pdf.documentation');
    Route::get('/export/excel', [ExportController::class, 'exportExcel'])->name('export.excel');
    Route::get('/survey-analysis', [SurveyAnalysisController::class, 'index'])->name('survey-analysis.index');
    Route::get('/survey-analysis/export', [SurveyAnalysisController::class, 'export'])->name('survey-analysis.export');
    Route::get('/survey-analysis/export-pdf', [SurveyAnalysisController::class, 'exportPdf'])->name('survey-analysis.export-pdf');
    Route::get('/reset-survey-data', [DashboardController::class, 'resetSurveyData'])->name('dashboard.reset-survey-data');
    Route::get('/admin/change-password', [\App\Http\Controllers\ProfileController::class, 'changePasswordForm'])->name('admin.change-password.form');
    Route::post('/admin/change-password', [\App\Http\Controllers\ProfileController::class, 'changePassword'])->name('admin.change-password');
    
    // Backup Management Routes
    Route::get('/admin/backup', [BackupController::class, 'index'])->name('admin.backup.index');
    Route::post('/admin/backup/create', [BackupController::class, 'create'])->name('admin.backup.create');
    Route::get('/admin/backup/download/{filename}', [BackupController::class, 'download'])->name('admin.backup.download');
    Route::delete('/admin/backup/delete/{filename}', [BackupController::class, 'delete'])->name('admin.backup.delete');
    Route::post('/admin/backup/cleanup', [BackupController::class, 'cleanup'])->name('admin.backup.cleanup');
    Route::get('/admin/backup/status', [BackupController::class, 'status'])->name('admin.backup.status');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/survey', [SurveyController::class, 'index'])->name('survey.index');
Route::post('/survey', [SurveyController::class, 'store'])->name('survey.store')->middleware('throttle:3,60'); // 3 submissions per hour
Route::get('/survey/guide', function() {


    
    return view('survey.guide');
})->name('survey.guide');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
