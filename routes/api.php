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
use App\Http\Controllers\Api\Security\PermissionController;
use App\Http\Controllers\Api\Security\RoleController;
use App\Http\Middleware\CheckPermissionOrAdmin;

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
    Route::middleware(CheckPermissionOrAdmin::class.':ver usuarios')->get('/users', [UserController::class, 'index'])->name('users.list');
    Route::middleware(CheckPermissionOrAdmin::class.':mostrar usuario')->get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::middleware(CheckPermissionOrAdmin::class.':crear usuarios')->post('/users', [UserController::class, 'store'])->name('users.create');
    Route::middleware(CheckPermissionOrAdmin::class.':actualizar usuarios')->post('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::middleware(CheckPermissionOrAdmin::class.':eliminar usuarios')->delete('/users/{id}', [UserController::class, 'destroy'])->name('users.delete');
    Route::middleware(CheckPermissionOrAdmin::class.':asignar rol usuarios')->post('/assign-roles', [UserController::class, 'assignRoles'])->name('users.assign-roles');
    Route::middleware(CheckPermissionOrAdmin::class.':remover rol usuarios')->delete('/remove-roles', [UserController::class, 'removeRoles'])->name('users.remove-roles');
    Route::middleware(CheckPermissionOrAdmin::class.':ver permisos')->get('/permissions', [PermissionController::class, 'index'])->name('permissions.list');
    Route::middleware(CheckPermissionOrAdmin::class.':mostrar permiso')->get('/permissions/{id}', [PermissionController::class, 'show'])->name('permissions.show');
    Route::middleware(CheckPermissionOrAdmin::class.':crear permisos')->post('/permissions', [PermissionController::class, 'store'])->name('permissions.create');
    Route::middleware(CheckPermissionOrAdmin::class.':actualizar permisos')->post('/permissions/{id}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::middleware(CheckPermissionOrAdmin::class.':eliminar permisos')->delete('/permissions/{id}', [PermissionController::class, 'destroy'])->name('permissions.delete');
    Route::middleware(CheckPermissionOrAdmin::class.':ver roles')->get('/roles', [RoleController::class, 'index'])->name('roles.list');
    Route::middleware(CheckPermissionOrAdmin::class.':mostrar rol')->get('/roles/{id}', [RoleController::class, 'show'])->name('roles.show');
    Route::middleware(CheckPermissionOrAdmin::class.':crear roles')->post('/roles', [RoleController::class, 'store'])->name('roles.create');
    Route::middleware(CheckPermissionOrAdmin::class.':actualizar roles')->post('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::middleware(CheckPermissionOrAdmin::class.':eliminar roles')->delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.delete');
    Route::middleware(CheckPermissionOrAdmin::class.':asignar permisos')->post('/roles/permissions/assign/{id}', [RoleController::class, 'assignPermissions'])->name('roles.permissions.assign');
    Route::middleware(CheckPermissionOrAdmin::class.':remover permisos')->delete('/roles/permissions/remove/{role}', [RoleController::class, 'removePermissions'])->name('roles.permissions.remove');

});

