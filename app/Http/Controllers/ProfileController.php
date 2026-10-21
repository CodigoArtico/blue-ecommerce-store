<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\ImageManagerStatic as Image;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();
    
        if ($request->hasFile('img')) {
            try {
                // Obtener el archivo de imagen
                $image = $request->file('img');
        
                // Validar que el archivo es válido antes de continuar
                if (!$image->isValid()) {
                    throw new \Exception('El archivo de imagen no es válido.');
                }
        
                // Generar un nombre único para la imagen
                $originalName = preg_replace('/\s+/', '_', pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME));
                $name = time() . '_' . $originalName . '.webp';
                $imagePath = 'img_user/' . $name;
        
                // Convertir la imagen a WebP y guardarla en el almacenamiento público
                $webpImage = Image::make($image)->encode('webp', 90);
                Storage::disk('public')->put($imagePath, (string) $webpImage);
        
                // Verificar si existe una imagen previa
                if (!empty($user->img)) {
                    $previousImagePath = 'img_user/' . $user->img; // Ruta completa de la imagen previa
                    if (Storage::disk('public')->exists($previousImagePath)) {
                        // Eliminar la imagen previa
                        Storage::disk('public')->delete($previousImagePath);
                    }
                }
        
                // Guardar solo el nombre del archivo sin "img_user/"
                $data['img'] = $name; // Guardar solo el nombre del archivo
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al procesar la imagen: ' . $e->getMessage()], 500);
            }
        }
        
        // Manejo del correo electrónico
        if (isset($data['email'])) {
            $data['email'] = strtolower($data['email']);
        }
    
        // Actualizar datos del usuario
        $user->fill($data);
    
        // Procesar contraseña si está presente
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }
    
        // Invalidar la verificación del correo si se cambió
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
    
        // Guardar el usuario actualizado
        $user->save();
    
        return response()->json(['success' => true, 'message' => 'Perfil actualizado correctamente.']);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
