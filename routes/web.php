<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-email', function () {
    try {
        Mail::raw('Halo, ini tes email dari UNILAM Library via Railway!', function ($message) {
            $message->to('dwinugraha17@gmail.com') // Ganti ke email tujuan test
                    ->subject('Test Email Railway');
        });
        return "Email berhasil dikirim! Cek inbox.";
    } catch (\Exception $e) {
        return "Gagal kirim email: " . $e->getMessage();
    }
});

Route::get('/ping', function() {
    return response()->json(['message' => 'Web is working', 'time' => now()]);
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
