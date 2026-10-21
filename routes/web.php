<?php

use App\Http\Controllers\CalificacionController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\MercadoPagoController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketVentaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Http;

// RUTAS PUBLICAS---------------
//------------------------------------------------ruta inical de vista principal-------------------------------------------------------------
Route::get('/', [ProductoController::class, 'index'])->name('dashboard');

//------------------------------------------------ruta para realizar una orden----------------------------------------------------------------
Route::post('/ordenes', [OrdenController::class, 'store'])->name('ordenes.store');

//------------------------------------------------ruta para agregar al carrito usuario auth y no authentic-----------------------------------
Route::post('/carrito/store', [CarritoController::class, 'store'])->name('carrito.store');

//------------------------------------------------ruta para mostar el producto en especifico con sus relacionados-----------------------------
Route::get('/producto/{codigo}', [ProductoController::class, 'show'])->name('producto.show');

//-------------------------------------------------mostrar la orden de un usuario sin autenticar----------------------------------------------
Route::get('/orden/{id}', [OrdenController::class, 'mostrar'])->name('orden.mostrar');
//-------------------------------------------------mostrar las ordenes de un usuario sin autenticar----------------------------------------------
Route::post('/usuario/ordenes/no-autenticados', [OrdenController::class, 'getOrdersForUnauthenticatedUser'])->name('usuario.ordenes.no-autenticados');

// ------------------------------------------VENTA: TICKET------------------------------------------------------------------
Route::get('/venta/{identificador_web}/ticket', [TicketVentaController::class, 'abrirVentanaPDFTicket'])->name('venta.abrirVentanaPDFTicket');

// ---------------------RUTA PARA AÑADIR LOGO A LA IMAGEN----------------------------------

Route::get('/imagen-con-logo/{filename?}', function ($filename = null) {
    $pageWeb = \App\Models\PageWeb::first();
    $empresa = \App\Models\Empresa::first();

    // Si no se pasa filename o viene como "productos/default.png"
    if (empty($filename) || str_contains($filename, 'default')) {
        $imagePath = public_path('images/default/default.webp');
    } else {
        // Ruta local del producto
        $localPath = public_path('storage/productos/' . $filename);
        if (file_exists($localPath)) {
            $imagePath = $localPath;
        } else {
            // Intentar obtenerla desde el servidor remoto
            $imageUrl = "{$pageWeb->url_sistem}/storage/productos/{$filename}";
            $response = Http::get($imageUrl);

            if ($response->failed() || empty($response->body())) {
                // Si falla, usar imagen por defecto
                $imagePath = public_path('images/default/default.webp');
            } else {
                $tempPath = storage_path('app/temp_' . $filename);
                file_put_contents($tempPath, $response->body());
                $imagePath = $tempPath;
            }
        }
    }

    // Cargar imagen base
    if (!file_exists($imagePath)) {
        abort(404, 'No se encontró la imagen ni la imagen por defecto.');
    }

    $img = Image::make($imagePath);

    // Agregar logo si existe
    $logoPath = public_path('images/default/default.webp'); // Cambia si tienes un logo distinto
    if (file_exists($logoPath)) {
        $logo = Image::make($logoPath)
            ->resize(120, 60)
            ->opacity(80);
        $img->insert($logo, 'bottom-right', 10, 10);
    }

    return $img->response('jpg');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // ------------------------------------------------obtener ordenes del usuario autenticado ------------------------------------------------
    Route::get('/usuario/ordenes', [OrdenController::class, 'getOrders'])->name('usuario.ordenes');

    // ------------------------------------------------rutas para manejar el crud del carrito ------------------------------------------------
    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::patch('/carrito/{id}', [CarritoController::class, 'update'])->name('carrito.update');
    Route::delete('/carrito/{id}', [CarritoController::class, 'destroy'])->name('carrito.destroy');

    //------------------------------------------------Perfil ------------------------------------------------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //------------------------------------------------Calificacion ------------------------------------------------
    Route::post('/calificar-producto', [CalificacionController::class, 'calificarProducto'])->name('calificar.producto');
});

require __DIR__ . '/auth.php';
