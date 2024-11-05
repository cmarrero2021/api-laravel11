<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class CustomRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        // return $next($request);
        if (!auth()->check() || !$request->user()->hasAnyRole(explode('|', $roles))) {
            // Devolviendo una respuesta personalizada
            return response()->json([
                'message' => 'Acceso denegado: No tienes el rol adecuado para acceder a esta ruta.','accion' => 0
            ], 403); // Código de respuesta HTTP 403 (Forbidden)
        }

        return $next($request);
    }
}
