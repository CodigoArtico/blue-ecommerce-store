<?php

namespace App\Http\Controllers;

use App\Models\Calificacion;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CalificacionController extends Controller
{
    //
    public function calificarProducto(Request $request)
    {
        try {
            $validated = $request->validate([
                'producto_id' => 'required|exists:productos,id',
                'puntuacion' => 'required|integer|between:1,5',
            ]);

            // Verificar si el usuario está autenticado
            $userId = auth()->user() ? auth()->user()->id : null;

            // Verificar si el usuario ya ha calificado el producto
            $calificacion = Calificacion::where('producto_id', $validated['producto_id'])
                                         ->where('user_web_id', $userId)
                                         ->first();

            if ($calificacion) {
                // Si ya existe una calificación del usuario, actualizarla
                $calificacion->puntuacion = $validated['puntuacion'];
                $calificacion->save();
            } else {
                // Si no existe, crear una nueva
                Calificacion::create([
                    'producto_id' => $validated['producto_id'],
                    'user_web_id' => $userId,
                    'puntuacion' => $validated['puntuacion'],
                ]);
            }

            // Calcular el promedio y la cantidad de votos
            $producto = Producto::find($validated['producto_id']);
            $promedio = number_format($producto->calificaciones->avg('puntuacion'), 1);
            $votos = $producto->calificaciones->count();

            return response()->json([
                'calificacion' => 'Calificación añadida correctamente',
                'promedio' => $promedio,
                'votos' => $votos,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al calificar el producto: ' . $e->getMessage());
            return response()->json(['error' => 'Hubo un problema al procesar tu calificación.'], 500);
        }
    }
}
