<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\PageWeb;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener el primer registro de page_web con sus billboards asociados
        $pageWeb = PageWeb::with('billboards')->first();

        // Obtener las categorías con productos y características
        $categorias = Categoria::whereHas('caracteristica', function ($query) {
            $query->where('estado', 1); // Filtrar categorías con estado = 1 en caracteristicas
        })
            ->whereHas('productos', function ($query) {
                $query->where('estado', 1); // Filtrar categorías con productos con estado = 1
            })
            ->join('caracteristicas', 'caracteristicas.id', '=', 'categorias.caracteristica_id') // Unir la tabla 'caracteristicas'
            ->select('categorias.*', 'caracteristicas.destacado') // Seleccionar las columnas necesarias
            ->orderByDesc('caracteristicas.destacado') // Mantener destacadas arriba
            ->with(['productos' => function ($query) {
                $query->where('estado', 1) // Cargar solo productos con estado = 1
                    ->orderByDesc('destacado'); // Priorizar los productos con 'destacado' == 1
            }])
            ->get();

        // Separar las categorías destacadas de las no destacadas
        $categorias_destacadas = $categorias->filter(fn($categoria) => $categoria->destacado == 1);
        $categorias_no_destacadas = $categorias->filter(fn($categoria) => $categoria->destacado == 0);

        // Mezclar ambas listas aleatoriamente
        $categorias_destacadas = $categorias_destacadas->shuffle();
        $categorias_no_destacadas = $categorias_no_destacadas->shuffle();

        // Unir las categorías destacadas (mezcladas) con las no destacadas (también mezcladas)
        $categorias = $categorias_destacadas->merge($categorias_no_destacadas);

        // Inicializar arrays para URLs
        $externalImages = [];
        $localImages = [];

        // URL base para las imágenes locales
        $baseUrl = $pageWeb->url_sistem . '/storage/billboards/';

        // Verificar si hay billboards y recoger las URLs
        if ($pageWeb && $pageWeb->billboards) {
            foreach ($pageWeb->billboards as $billboard) {
                // Agregar la URL externa si tiene estado_url == 1
                if ($billboard->img_url && $billboard->estado_url == 1) {
                    $externalImages[] = $billboard->img_url; // Agregar la URL externa
                }

                // Agregar la imagen local si tiene estado_path == 1
                if ($billboard->img_path && $billboard->estado_path == 1) {
                    // Asegúrate de que solo se agregue la parte de la imagen
                    $localImages[] = $baseUrl . ltrim($billboard->img_path, '/'); // Agregar imagen local
                }
            }
        }

        // Pasar las variables separadas a la vista
        return view('dashboard.dashboard', compact('categorias', 'pageWeb', 'externalImages', 'localImages'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($codigo)
    {
        // Obtener el producto por su código
        $producto = Producto::where('codigo', $codigo)->firstOrFail();

        // Cargar la relación de calificaciones (si es que no se ha cargado previamente)
        $producto->load('calificaciones');

        // Calcular el promedio de las calificaciones y el número de votos
        $promedioCalificacion = $producto->calificaciones->avg('puntuacion') ?? 0; // Si no hay calificaciones, será 0
        $numeroVotos = $producto->calificaciones->count(); // Si no hay calificaciones, será 0

        // Inicializar la colección de productos relacionados
        $productosRelacionados = collect();

        // Verificar si el producto tiene categorías relacionadas
        if ($producto->categorias->isNotEmpty()) {
            // Obtener productos relacionados aleatorios usando las categorías del producto
            $productosRelacionados = Producto::whereHas('categorias', function ($query) use ($producto) {
                $query->whereIn('categorias.id', $producto->categorias->pluck('id'));
            })->where('id', '!=', $producto->id)->inRandomOrder()->take(12)->get();
        }

        // Si no se alcanzan los 12 productos, agregar productos aleatorios hasta completar
        if ($productosRelacionados->count() < 12) {
            $productosFaltantes = Producto::where('id', '!=', $producto->id)
                ->where('estado', 1) // Añadir esta condición para filtrar por estado == 1
                ->whereNotIn('id', $productosRelacionados->pluck('id'))
                ->inRandomOrder()
                ->take(12 - $productosRelacionados->count())
                ->get();

            // Combinar los productos relacionados con los aleatorios
            $productosRelacionados = $productosRelacionados->merge($productosFaltantes);
        }

        // Si no se encontraron productos relacionados, registrar mensaje para depuración (opcional)
        if ($productosRelacionados->isEmpty()) {
            Log::warning('No se encontraron suficientes productos relacionados ni productos aleatorios en la base de datos.');
        }

        // Pasar los datos a la vista
        return view('producto.show', compact('producto', 'productosRelacionados', 'promedioCalificacion', 'numeroVotos'));
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
