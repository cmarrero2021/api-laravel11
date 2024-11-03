<?php
namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class ForgotPasswordController extends Controller
{
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'No existe un usuario con ese correo electrónico.'], 404);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Debes verificar tu correo electrónico antes de cambiar la contraseña.'], 403);
        }
        $token = Str::random(60);
        \DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now(),
            'expires_at' => now()->addMinutes(60),
        ]);

        $resetUrl = URL::signedRoute('auth.reset-password', [
            'token' => $token,
            'email' => $user->email,
        ]);

        $this->sendResetEmail($user, $resetUrl);

        return response()->json(['message' => 'Se ha enviado un correo con el enlace para cambiar la contraseña.'], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user ||!$user->password_reset_token ||!$user->password_reset_expires_at) {
            return response()->json(['message' => 'Token inválido o expirado.'], 401);
        }

        if ($user->password_reset_token!== $request->token || $user->password_reset_expires_at.now()) {
            return response()->json(['message' => 'Token inválido o expirado.'], 401);
        }

        $user->password = Hash::make($request->password);
        $user->password_reset_token = null;
        $user->password_reset_expires_at = null;
        $user->save();

        return response()->json(['message' => 'Contraseña cambiada con éxito.'], 200);
    }

    private function sendResetEmail($user, $resetUrl)
    {
        $mailData = [
            'title' => 'Restablecer Contraseña',
            'url' => $resetUrl,
        ];

        Mail::to($user->email)->send(new \App\Mail\ResetPassword($mailData));
    }
}
