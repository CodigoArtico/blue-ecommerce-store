<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use Illuminate\Http\Request;


class CarritoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carrito = Carrito::where('user_web_id', auth()->id())
            ->with('producto.categorias.caracteristica')  // Relación producto -> categoría -> característica
            ->get();

        return view('carrito.index', compact('carrito'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Verificar si el usuario está autenticado
        if (auth()->check()) {
            // Obtener el ID del usuario autenticado
            $userId = auth()->id();

            // Verificar si el producto ya está en el carrito
            $existingItem = Carrito::where('user_web_id', $userId)
                ->where('producto_id', $request->producto_id)
                ->first();

            if ($existingItem) {
                // Responder con un mensaje indicando que el producto ya está en el carrito
                return response()->json([
                    'success' => false,
                    'message' => 'El producto ya está en el carrito.'
                ]);
            }

            // Insertar el producto en el carrito
            Carrito::create([
                'user_web_id' => $userId,
                'producto_id' => $request->producto_id,
                'cantidad' => $request->cantidad,
            ]);

            // Obtener el número actualizado de productos en el carrito
            $cartCount = Carrito::where('user_web_id', $userId)->count();

            // Responder con un mensaje de éxito
            return response()->json([
                'success' => true,
                'message' => 'Producto añadido al carrito',
                'cartCount' => $cartCount,  // Devolver el nuevo count del carrito
            ]);
        } else {
            // Responder si el usuario no está autenticado
            return response()->json([
                'success' => false,
                'message' => 'Debes registrarte.'
            ]);
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
    public function update(Request $request, $id)
{
    $request->validate([
        'cantidad' => 'required|integer|min:1'
    ]);

    // Buscar el ítem del carrito
    $carritoItem = Carrito::findOrFail($id);

    // Actualizar la cantidad
    $carritoItem->cantidad = $request->input('cantidad');
    $carritoItem->save();

    // Obtener el total actualizado del carrito
    $total = Carrito::where('user_web_id', auth()->id())
                    ->with('producto')
                    ->get()
                    ->sum(function ($item) {
                        return $item->producto->precioVentaReciente() * $item->cantidad;
                    });

    // Responder con un mensaje de éxito y el nuevo total
    return response()->json([
        'success' => 'Cantidad actualizada correctamente.',
        'total' => number_format($total, 2), // Devolvemos el total con formato
    ]);
}


    /**
     * Remove the specified resource from storage.
     */
    // Eliminar un producto del carrito
    public function destroy($id)
    {
        // Buscar el producto en el carrito por su ID
        $carritoItem = Carrito::find($id);

        if (!$carritoItem) {
            return response()->json([
                'error' => 'Producto no encontrado.',
            ], 404);
        }

        // Eliminar el producto del carrito
        $carritoItem->delete();

        // Responder con un mensaje de éxito
        return response()->json([
            'success' => 'Producto eliminado del carrito.',
            'cartCount' => Carrito::where('user_web_id', auth()->id())->count(), // Contar los productos restantes
        ]);
    }
}
