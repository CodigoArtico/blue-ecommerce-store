<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password view.
     */
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    /**
     * Confirm the user's password.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validar la contraseña utilizando el guard 'web' que ahora debería estar configurado para 'users_web'
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email, // Validar el correo del usuario autenticado
            'password' => $request->password, // Verificar que la contraseña coincida
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'), // Lanzar excepción si la contraseña no es válida
            ]);
        }

        // Almacenar la confirmación de la contraseña en la sesión
        $request->session()->put('auth.password_confirmed_at', time());

        // Redirigir al dashboard o a la página deseada
        return redirect()->intended(route('dashboard', absolute: false));
    }
}
