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

// Route::post('/auth/register', [App\Http\Controllers\Api\Auth\RegisterController::class, 'register'])->name('auth.register');
// Route::get('/auth/verify-email/{id}', [App\Http\Controllers\Api\Auth\VerificationController::class, 'verify'])->name('auth.verify-email');
// Route::post('/auth/login', [App\Http\Controllers\Api\Auth\LoginController::class, 'login'])->name('auth.login');
// Route::post('/auth/resend-verification-email', [App\Http\Controllers\Api\Auth\VerificationController::class, 'resendVerificationEmail'])->name('auth.resend-verification-email');
// Route::post('/auth/forgot-password', [App\Http\Controllers\Api\Auth\ForgotPasswordController::class, 'sendResetLink'])->name('auth.forgot-password');
// Route::post('/auth/reset-password', [App\Http\Controllers\Api\Auth\ForgotPasswordController::class, 'resetPassword'])->name('auth.reset-password');

// Route::get('/auth/verify-email/{id}', 'Api\Auth\VerificationController@verify')->name('auth.verify-email');
// Route::post('/auth/login', 'Api\Auth\LoginController@login');
// Route::post('/auth/resend-verification-email', 'Api\Auth\VerificationController@resendVerificationEmail');
// Route::post('/auth/forgot-password', 'Api\Auth\ForgotPasswordController@sendResetLink');
// Route::post('/auth/reset-password', 'Api\Auth\ForgotPasswordController@resetPassword');
