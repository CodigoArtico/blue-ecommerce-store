<x-app-layout>
    @section('title', 'Carrito')

    @push('css')
    @endpush

    {{-- INCLUIR JS PARA ACTUALIZAR CARRITO, ELIMINAR Y MAS..  --}}
    @include('carrito.partials.js')

    <div class="container max-w-screen-xl mx-auto px-2 py-4">
        <h1 class="text-3xl font-semibold mb-0 text-center">Your Cart 🛒</h1>
        <p class="text-center text-gray-500 mb-2">Se cargaran los datos de tu perfil.</p>
        @if ($carrito->isEmpty())
            <p class="text-center text-gray-500">Tu carrito está vacío.</p>
            <!-- Contenedor centrado -->
            <div class="flex justify-center mt-4">
                <button onclick="mostrarPedidos()"
                    class="flex items-center bg-blue-600 hover:bg-blue-800 text-white py-2 px-6 rounded-lg shadow-lg transition duration-200">
                    <i class="fas fa-receipt mr-2"></i> <!-- Icono de factura -->
                    Ver Mis Pedidos
                </button>
            </div>
        @else
            <form action="{{ route('ordenes.store') }}" method="POST" id="delivery-form">
                @csrf
                {{-- Nombre --}}
                <div class="mb-4 flex flex-col items-center">
                    <label for="nombre" class="block text-gray-700 font-bold mb-1">Nombre:</label>
                    <input type="text" id="nombre" name="nombre"
                        class="w-1/2 px-3 py-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600"
                        placeholder="Ingrese su nombre" value="{{ Auth::check() ? Auth::user()->name : '' }}" required>
                </div>

                <!-- Teléfono -->
                <div class="mb-4 flex flex-col items-center">
                    <label for="telefono" class="block text-gray-700 font-bold mb-1">Teléfono:</label>
                    <input type="number" id="telefono" name="telefono"
                        class="w-1/2 px-3 py-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600"
                        placeholder="Ingrese su teléfono" value="{{ Auth::check() ? Auth::user()->telefono : '' }}"
                        required>
                </div>

                {{-- Dirección --}}
                <div class="mb-4 flex flex-col items-center">
                    <label for="direccion" class="block text-gray-700 font-bold mb-1">Dirección:</label>
                    <input type="text" id="direccion" name="direccion"
                        class="w-1/2 px-3 py-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600"
                        placeholder="Ingrese su dirección" value="{{ Auth::check() ? Auth::user()->direccion : '' }}"
                        required>
                </div>
                <!-- Monto total del pedido -->
                <div class="flex justify-center mb-2">
                    <p id="total-carrito" class="text-lg font-bold mr-2">
                        Total: S/
                        {{ number_format($carrito->sum(fn($item) => $item->producto->precioVentaReciente() * $item->cantidad), 2) }}
                    </p>
                </div>

                <!-- Botón para realizar la orden -->
                <div class="flex justify-center mb-4">
                    <button type="button" onclick="realizarOrden()"
                        class="bg-black hover:bg-gray-500 text-white py-1 px-4 rounded-lg shadow-lg flex items-center transition duration-200">
                        <i class="fa-solid fa-motorcycle fa-bounce mr-1"></i>Realizar Orden
                    </button>
                </div>

            </form>
            <!-- Productos en el carrito -->
            <div class="flex overflow-x-auto space-x-4 py-2">
                @foreach ($carrito as $item)
                    <div class="flex-none w-64 py-2">
                        <div class="border rounded-lg overflow-hidden shadow-lg">
                            <!-- Imagen del producto -->
                            @if ($item->producto->img_path)
                                <img src="{{ asset($pageWeb->url_sistem . '/storage/productos/' . $item->producto->img_path) }}"
                                    alt="{{ $item->producto->nombre }}" class="w-full h-48 object-cover">
                            @else
                                <img src="{{ asset($item->producto->img_url) }}" alt="{{ $item->producto->nombre }}"
                                    class="w-full h-48 object-cover">
                            @endif
                            <div class="p-4">
                                <!-- Nombre del producto -->
                                <h2 class="text-lg font-semibold">{{ \Illuminate\Support\Str::limit($item->producto->nombre, 15) }}</h2>
                                <p class="text-sm text-gray-500">
                                    @if ($item->producto->categorias->isNotEmpty())
                                        {{ $item->producto->categorias->first()->caracteristica->nombre }}
                                    @else
                                        Sin categoría
                                    @endif
                                </p>
                                <!-- Precio del producto -->
                                <p class="text-lg font-bold text-gray-700">S/
                                    {{ number_format($item->producto->precioVentaReciente(), 2) }}</p>

                                <!-- Fila de cantidad y botón de eliminar -->
                                <div class="flex items-center justify-between">
                                    <!-- Cantidad del producto -->
                                    <div class="flex items-center">
                                        <label for="cantidad_{{ $item->id }}" class="mr-2">Cant:</label>
                                        <input type="number" id="cantidad_{{ $item->id }}"
                                            name="cantidad[{{ $item->id }}]" value="{{ $item->cantidad }}"
                                            min="1" class="w-16 px-2 py-1 border rounded-lg text-center"
                                            inputmode="numeric"
                                            oninput="validarYActualizarCantidad({{ $item->id }})">
                                    </div>

                                    <!-- Botón para eliminar el producto del carrito -->
                                    <button onclick="eliminarProducto({{ $item->id }})"
                                        class="text-red-500 hover:text-red-700 ml-4">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>


        @endif
        {{-- incluimos el modal para mostrar las ordenes del usuario  --}}
        @include('components.modal_orders.modal')
    </div>

    @push('js')
    @endpush
</x-app-layout>
