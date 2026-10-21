<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Orden;
use App\Models\OrdenProducto;
use App\Models\Producto;
use App\Models\UserWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrdenController extends Controller
{
    // Función para obtener las órdenes de un usuario autenticado
    public function getOrders(Request $request)
    {
        $userId = auth()->id(); // Obtener el ID del usuario autenticado

        // Obtener las órdenes del usuario junto con los productos asociados a través de ordenProductos
        $orders = Orden::where('user_web_id', $userId)
            ->with('ordenProductos.producto') // Incluir productos a través de ordenProductos
            ->orderBy('created_at', 'desc') // Ordenar por fecha de creación en orden descendente
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ]);
    }

    // obtener 1 orden en especifico para user : autenticado y no autenticado
    public function mostrar($id)
    {
        try {
            $orden = Orden::with('ordenProductos.producto')->findOrFail($id);

            return response()->json([
                'success' => true,
                'orden' => $orden
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Orden no encontrada.'
            ]);
        }
    }

    // Función para devolver las órdenes de un usuario no autenticado
    public function getOrdersForUnauthenticatedUser(Request $request)
    {
        try {
            // Validar que se haya enviado un array de IDs de órdenes
            $request->validate([
                'orden_ids' => 'required|array',
                'orden_ids.*' => 'integer|exists:ordenes,id', // Validar que los IDs existan en la base de datos
            ]);

            // Obtener las órdenes correspondientes a los IDs proporcionados, ordenadas por fecha de creación
            $orders = Orden::with('ordenProductos.producto')
                ->whereIn('id', $request->orden_ids)
                ->orderBy('created_at', 'desc') // Ordenar por la fecha de creación (más recientes primero)
                ->get();

            // Verificar si no se encontraron órdenes
            if ($orders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron órdenes.'
                ], 404);
            }

            // Responder con las órdenes encontradas, ordenadas
            return response()->json([
                'success' => true,
                'orders' => $orders
            ]);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al recuperar las órdenes.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    // generar codigo unico para codigo_orden
    public function generarCodigoUnico()
    {
        do {
            // Genera un código alfanumérico de 10 caracteres
            $codigo_orden = Str::random(10);
        } while (Orden::where('codigo_orden', $codigo_orden)->exists());

        return $codigo_orden;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación para el formato JSON
        $request->validate([
            'productos' => 'required|array',
            'productos.*.producto_id' => 'required|integer|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'required|numeric',
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'carrito' => 'nullable|string' // Agregar validación para 'carrito'
        ]);

        DB::beginTransaction();

        try {
            // Verificar si el usuario está autenticado
            $user = auth()->user();

            if ($user) {
                // Verificar y actualizar teléfono y dirección si están vacíos
                if (empty($user->telefono)) {
                    $user->telefono = $request->telefono;
                }
                if (empty($user->direccion)) {
                    $user->direccion = $request->direccion;
                }
                $user->save();
            }

            // Obtener el ID del usuario autenticado o el primero en UserWeb
            $userId = $user ? $user->id : UserWeb::first()->id;

            // Generar el código único para la orden
            $codigo_orden = $this->generarCodigoUnico();

            // Crear la orden con el código único
            $orden = Orden::create([
                'user_web_id' => $userId,
                'estado' => 'pendiente',
                'codigo_orden' => $codigo_orden, // Asignar el código único
                'nombre' => $request->nombre,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'total' => 0, // Inicializar el total
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Manejo de productos y cálculo del total
            foreach ($request->productos as $productoData) {
                $producto = Producto::findOrFail($productoData['producto_id']);

                OrdenProducto::create([
                    'orden_id' => $orden->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $productoData['cantidad'],
                    'precio' => $productoData['precio'],
                ]);

                // Sumar al total
                $orden->total += $productoData['precio'] * $productoData['cantidad'];
            }

            // Guardar el total actualizado
            $orden->save();

            DB::commit();

            // Vaciar el carrito del usuario si 'carrito' es "yes"
            if ($request->carrito === 'yes') {
                Carrito::where('user_web_id', $userId)->delete();
            }

            // Retornar respuesta JSON
            return response()->json(['success-orden' => 'Orden creada con éxito.', 'orden_id' => $orden->id]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al crear la orden: ' . $e->getMessage());

            // Retornar respuesta JSON de error
            return response()->json(['error' => 'Ocurrió un error al crear la orden. Intenta de nuevo.'], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
