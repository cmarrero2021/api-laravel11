<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }
    protected $routeMiddleware = [
        'check.permission.or.admin' => \App\Http\Middleware\CheckPermissionOrAdmin::class,
        // Otros middleware de rutas...
    ];
    public function boot(): void
    {
        // Route::middleware('check.permission.or.admin' => \App\Http\Middleware\CheckPermissionOrAdmin::class);
    }
}
