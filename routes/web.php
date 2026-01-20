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

Route::get('/test-resend', function () {
    // Force route refresh
    try {
        Mail::raw('Halo! Ini adalah tes email menggunakan RESEND API dari UNILAM Library.', function ($message) {
            $message->to('chanddwi780@gmail.com') // Diubah ke email terdaftar Resend
                    ->subject('Test Resend Railway');
        });
        return "Resend Berhasil! Cek inbox Anda.";
    } catch (\Exception $e) {
        return "Resend Gagal: " . $e->getMessage();
    }
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