<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\BorrowController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function() {
    return response()->json(['message' => 'API is working']);
});

Route::get('/buat-user-tes', function() {
    $user = \App\Models\User::updateOrCreate(
        ['email' => 'admin@gmail.com'],
        [
            'name' => 'Admin Tes',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'phone_number' => '08123456789',
            'role' => 'admin'
        ]
    );
    return response()->json(['message' => 'User tes berhasil dibuat', 'user' => $user]);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/profile/update', [AuthController::class, 'updateProfile']);
    Route::delete('/user', [AuthController::class, 'deleteAccount']);

    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{book}', [BookController::class, 'show']);
    
    Route::post('/borrow', [BorrowController::class, 'borrow']);
    Route::get('/history', [BorrowController::class, 'history']);
    Route::post('/return/{borrowing}', [BorrowController::class, 'returnBook']);

    Route::post('/books/{book}/reviews', [ReviewController::class, 'store']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);

    // Admin only (simplified check)
    Route::middleware('can:admin-access')->group(function () {
        Route::post('/books', [BookController::class, 'store']);
        Route::put('/books/{book}', [BookController::class, 'update']);
        Route::delete('/books/{book}', [BookController::class, 'destroy']);
    });
});