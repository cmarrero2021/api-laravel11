<?php
namespace App\Http\Controllers\Api\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Models\Role;
class RegisterController extends Controller
{
    public function register(Request $request) {
        $errors = [];
        if (empty($request->input('name'))) {
            $errors['name'][] = 'El nombre es obligatorio.';
        }
        if (empty($request->input('email'))) {
            $errors['email'][] = 'El correo electrónico es obligatorio.';
        } elseif (!filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Debe proporcionar un correo electrónico válido.';
        } elseif (User::where('email', $request->input('email'))->exists()) {
            $errors['email'][] = 'El correo electrónico ya está registrado.';
        }
        $password = $request->input('password');
        if (empty($password)) {
            $errors['password'][] = 'La contraseña es obligatoria.';
        }
        if (strlen($password) < 8) {
            $errors['password'][] = 'La contraseña debe tener al menos 8 caracteres.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors['password'][] = 'La contraseña debe contener al menos una letra mayúscula.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors['password'][] = 'La contraseña debe contener al menos una letra minúscula.';
        }
        if (!preg_match('/\d/', $password)) {
            $errors['password'][] = 'La contraseña debe contener al menos un número.';
        }
        if (!preg_match('/[|°#$%&\/\*\+]/', $password)) {
            $errors['password'][] = 'La contraseña debe contener al menos uno de los caracteres especiales: |°#$%&/*+.';
        }
        if (preg_match('/([a-zA-Z\d|°#$%&\/\*\+])\1/', $password)) {
            $errors['password'][] = 'La contraseña no debe tener caracteres repetidos consecutivos.';
        }
        if ($password !== $request->input('password_confirmation')) {
            $errors['password'][] = 'La confirmación de la contraseña no coincide.';
        }
        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => null,
            'requires_password_change' => false
        ]);
        $user->assignRole('usuario');
        $this->sendVerificationEmail($user);
        return response()->json(['message' => 'Usuario creado con éxito. Verifica tu correo electrónico para activar tu cuenta.'], 201);
    }
    private function generateVerificationLink($user)
    {
        $verificationUrl = URL::signedRoute('auth.verify-email', ['id' => $user->id]);
        return $verificationUrl;
    }

    private function sendVerificationEmail($user)
    {
        $verificationUrl = $this->generateVerificationLink($user);
        $mailData = [
            'nombre' => $user->name,
            'title' => 'Verifica tu correo electrónico',
            'url' => $verificationUrl,
        ];
        Mail::to($user->email)->send(new \App\Mail\VerifyEmail($mailData));
    }
}
