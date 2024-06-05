<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class Contrasenia extends Controller
{
    public function requestForm()
    {
        return view('auth.passwords.email'); // Debes crear esta vista
    }

    // Enviar el enlace de restablecimiento de contraseña al correo electrónico
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    // Mostrar el formulario para restablecer la contraseña
    public function showResetForm($token)
    {
        return view('auth.passwords.reset', ['token' => $token]); // Debes crear esta vista
    }

    // Manejar el restablecimiento de la contraseña
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}
