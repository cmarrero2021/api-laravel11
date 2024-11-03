<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\{
    RegisterController,
    LoginController,
    VerificationController,
    ForgotPasswordController
};
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/auth/register', [RegisterController::class, 'register'])->name('auth.register');
Route::get('/auth/verify-email/{id}', [VerificationController::class, 'verify'])->name('auth.verify-email');
Route::post('/auth/login', [LoginController::class, 'login'])->name('auth.login');
Route::post('/auth/resend-verification-email', [VerificationController::class, 'resendVerificationEmail'])->name('auth.resend-verification-email');
Route::post('/auth/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('auth.forgot-password');
Route::post('/auth/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('auth.reset-password');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/auth/logout', [LoginController::class, 'logout'])->name('auth.logout');
});
