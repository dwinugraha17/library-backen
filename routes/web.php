<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SimpleAdminController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminBookController;
use App\Http\Controllers\Admin\UserController;

Route::get('/', function () {
    return view('welcome');
});

// Simple Admin Routes
Route::prefix('simple-admin')->group(function () {
    Route::get('/login', [SimpleAdminController::class, 'loginForm'])->name('admin.login');
    Route::post('/login', [SimpleAdminController::class, 'login']);

    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [SimpleAdminController::class, 'dashboard'])->name('simple.dashboard');
        Route::post('/logout', [SimpleAdminController::class, 'logout'])->name('simple.logout');
    });
});


// Auth Routes
Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

// Admin Routes (Protected)
Route::middleware(['auth', 'can:admin-access'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminBookController::class, 'dashboard'])->name('dashboard');

    // Book CRUD
    Route::resource('books', AdminBookController::class);

    // User Management
    Route::resource('users', UserController::class);
});

Route::get('/debug-logs', function () {
    $logFile = storage_path('logs/laravel.log');
    if (!file_exists($logFile)) {
        return "Log file not found.";
    }
    
    $content = file_get_contents($logFile);
    // Ambil 100 baris terakhir agar tidak terlalu berat
    $lines = explode("\n", $content);
    $lastLines = array_slice($lines, -100);
    
    return "<pre>" . implode("\n", $lastLines) . "</pre>";
});
