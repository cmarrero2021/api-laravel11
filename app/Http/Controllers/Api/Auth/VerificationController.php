<?php
namespace App\Http\Controllers\Api\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
class VerificationController extends Controller
{
    public function verify(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            return response()->json(['message' => 'Correo electrónico verificado con éxito.'], 200);
        }

        return response()->json(['message' => 'Este correo electrónico ya ha sido verificado.'], 400);
    }
    private function generateVerificationLink($user)
    {
        $verificationUrl = URL::signedRoute('auth.verify-email', ['id' => $user->id]);
        return $verificationUrl;
    }
    private function sendVerificationEmail($user)
    {
        $verificationUrl=$this->generateVerificationLink($user);
        $mailData = [
            'title' => 'Verifica tu correo electrónico',
            'url' => $verificationUrl,
        ];
        Mail::to($user->email)->send(new \App\Mail\VerifyEmail($mailData));
    }
    public function resendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => 'equired|string|email',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'No existe un usuario con ese correo electrónico.'], 404);
        }
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Este correo electrónico ya ha sido verificado.'], 400);
        }
        $verificationUrl = $this->generateVerificationLink($user);
        $this->sendVerificationEmail($user, $verificationUrl);
        return response()->json(['message' => 'Se ha reenviado el correo de verificación.'], 200);
    }
}
