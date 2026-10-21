<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        // Verificar si el usuario ya ha verificado su correo
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Enviar notificación de verificación de correo electrónico
        $request->user()->sendEmailVerificationNotification();

        // Redirigir de vuelta con un estado de enlace enviado
        return back()->with('status', 'verification-link-sent');
    }
}
