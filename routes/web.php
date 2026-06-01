<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\Master\ClassificationLetterController;
use App\Http\Controllers\Master\LetterTypeController;
use App\Http\Controllers\Master\SignatoryController;
use App\Http\Controllers\Master\LetterPurposeController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\LetterImportController;
use App\Http\Controllers\Admin\ErrorLogController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ModuleSwitcherController;
use App\Http\Controllers\Master\EducationLevelController;
use App\Http\Controllers\LegalisirDashboardController;
use App\Http\Controllers\LegalizationController;
use App\Http\Controllers\LegalizationImportController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
// Fallback GET route untuk logout (jika CSRF expired)
Route::get('logout', [LoginController::class, 'logout'])->name('logout.get');

// Module Switcher (Landing Page)
Route::get('/', [ModuleSwitcherController::class, 'index'])->name('switcher');

// Dashboard Surat
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Route Legalisir
Route::prefix('legalisir')->name('legalizations.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [LegalisirDashboardController::class, 'index'])->name('dashboard');
});
Route::get('legalizations/import/template', [LegalizationImportController::class, 'template'])->name('legalizations.import.template')->middleware(['auth', 'role:admin,operator']);
Route::post('legalizations/import', [LegalizationImportController::class, 'import'])->name('legalizations.import')->middleware(['auth', 'role:admin,operator']);
Route::resource('legalizations', LegalizationController::class);

// Master Data Routes (Middleware di Controller)
Route::prefix('master')->name('master.')->group(function () {
    Route::resource('education-levels', EducationLevelController::class)->except(['show']);
    Route::resource('classification-letters', ClassificationLetterController::class);
    Route::get('classification-letters-template/download', [ClassificationLetterController::class, 'downloadTemplate'])->name('classification-letters.download-template');
    Route::post('classification-letters-import', [ClassificationLetterController::class, 'import'])->name('classification-letters.import');
    
    Route::resource('letter-types', LetterTypeController::class);
    Route::post('letter-types/{letterType}/toggle-requires-purpose', [LetterTypeController::class, 'toggleRequiresPurpose'])->name('letter-types.toggle-requires-purpose');
    
    Route::resource('signatories', SignatoryController::class);
    Route::resource('letter-purposes', LetterPurposeController::class);
    Route::resource('users', UserController::class);
});

// Letter Routes (Middleware di Controller)
Route::get('letters/import/template', [LetterImportController::class, 'template'])->name('letters.import.template')->middleware(['auth', 'role:admin,operator']);
Route::post('letters/import', [LetterImportController::class, 'import'])->name('letters.import')->middleware(['auth', 'role:admin,operator']);
Route::resource('letters', LetterController::class);

// Profile Routes (All authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('error-logs', [ErrorLogController::class, 'index'])->name('error-logs.index');
    Route::get('error-logs/{errorId}', [ErrorLogController::class, 'show'])->name('error-logs.show');
    Route::get('error-logs-statistics', [ErrorLogController::class, 'statistics'])->name('error-logs.statistics');
    
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/{id}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
});

// Debug route untuk testing badge
Route::get('/debug-badge', function () {
    $letters = \App\Models\Letter::with(['creator', 'viewedByUsers'])
        ->active()
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    $currentUser = auth()->user();
    
    return view('debug-badge', compact('letters', 'currentUser'));
})->middleware('auth')->name('debug.badge');
