<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SimpleAdminController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminBookController;
use App\Http\Controllers\Admin\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ping', function() {
    return response()->json(['message' => 'Web is working', 'time' => now()]);
});

Route::get('/test-brevo', function (\App\Services\BrevoService $brevo) {
    $result = $brevo->sendEmail(
        'chanddwi780@gmail.com',
        'User Test',
        'Test Email Brevo API',
        '<h1>Berhasil!</h1><p>Ini adalah tes pengiriman email menggunakan Brevo API.</p>'
    );
    
    return $result ? "Email Brevo Berhasil Dikirim!" : "Gagal Mengirim Email Brevo.";
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