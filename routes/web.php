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

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Master Data Routes (Middleware di Controller)
Route::prefix('master')->name('master.')->group(function () {
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
Route::resource('letters', LetterController::class);

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
