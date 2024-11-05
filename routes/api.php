<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\{
    RegisterController,
    LoginController,
    VerificationController,
    ForgotPasswordController
};
use App\Http\Controllers\Api\Users\UserController;
use App\Http\Controllers\Api\Security\PermisionController;
use App\Http\Controllers\Api\Security\RoleController;
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
Route::middleware(['auth:api','role:admin|supervisor'])->group(function () {
    Route::get('/usuarios', [UserController::class, 'index']);
    Route::get('/usuario/{id}', [UserController::class, 'show']);
    Route::post('/usuarios', [UserController::class, 'store']);
    Route::post('/usuarios/{id}', [UserController::class, 'update']);
    Route::delete('/usuarios/{id}', [UserController::class, 'destroy']);

    Route::get('/permissions', [PermisionController::class, 'index']);
    Route::get('/permissions/{id}', [PermisionController::class, 'show']);
    Route::post('/permissions', [PermisionController::class, 'store']);
    Route::post('/permissions/{id}', [PermisionController::class, 'update']);
    Route::delete('/permissions/{id}', [PermisionController::class, 'destroy']);

    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/roles/{id}', [RoleController::class, 'show']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::post('/roles/{id}', [RoleController::class, 'update']);
    Route::delete('/roles/{id}', [RoleController::class, 'destroy']);
    Route::post('/roles/{roleId}/permissions/assign', [RoleController::class, 'assignPermissions'])
    ->name('roles.permissions.assign');
    Route::delete('/roles/{roleId}/permissions/remove', [RoleController::class, 'removePermissions'])
        ->name('roles.permissions.remove');

});

