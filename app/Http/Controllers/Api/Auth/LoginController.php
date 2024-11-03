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

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['user' =>$user->toArray(),'access_token' => $token,]);
    }
}
