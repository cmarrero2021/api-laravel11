<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciales inválidas.'], 401);
        }

        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Debes verificar tu correo electrónico antes de iniciar sesión.'], 403);
        }
        if ($user->requires_password_change) {
            return response()->json(['message' => 'Debes cambiar tu clave por primera vez. Dirígete a Olvidé mi clave','accion' =>1], 401);
        }
        if (!$user->active) {
            return response()->json(['message' => 'Usuario marcado como inactivo. Comunicate con el administrador del sistema','accion' =>2], 401);
        }

        $token = $user->createToken('auth_token', ['expires_at' => now()->addMinutes(60)])->plainTextToken;
        $user->tokens()->where('token', hash('sha256', explode('|', $token)[1]))
        ->update(['expires_at' => now()->addMinutes(60)]);
        return response()->json(['user' =>$user->toArray(),'access_token' => $token,]);
    }
    public function logout(Request $request) {
        $user = $request->user();

        if ($user) {
            $user->tokens()->delete();
            return response()->json(['message' => 'Sesión cerrada con éxito.'], 200);
        }

        return response()->json(['message' => 'Usuario no autenticado.'], 401);
    }
}
