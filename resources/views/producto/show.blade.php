<x-app-layout>
    @section('title', $producto->nombre)
    @section('meta_description', $producto->descripcion)
    @section('og_type', 'article')
    @section('og_title', $producto->nombre)
    @section('og_description', $producto->descripcion)
    @section('og_image', $producto->img_path ? asset($pageWeb->url_sistem . '/storage/productos/' . $producto->img_path)
        : $producto->img_url)
    @section('og_url', url()->current())

    @push('head')
        <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "Product",
                "name": "{{ $producto->nombre }}",
                "description": "{{ $producto->descripcion }}",
                "image": "{{ $producto->img_path ? asset('storage/productos/' . $producto->img_path) : $producto->img_url }}",
                "brand": {
                    "@type": "Brand",
                    "name": "{{ $producto->marca->nombre }}"
                },
                "sku": "{{ $producto->codigo }}",
                "offers": {
                    "@type": "Offer",
                    "url": "{{ url()->current() }}",
                    "priceCurrency": "S/",
                    "price": "{{ $producto->precioVentaReciente() }}",
                    "priceValidUntil": "{{ now()->addMonths(1)->toDateString() }}",
                    "itemCondition": "https://schema.org/NewCondition",
                    "availability": "https://schema.org/InStock"
                }
            }
        </script>
    @endpush

    @push('css')
        {{-- estilo de transicion de las imagenes  --}}
        <style>
            /* Transición suave de la opacidad de las imágenes */
            #product-image-container-{{ $producto->id }} .transition-opacity {
                transition: opacity 0.5s ease-in-out;
            }

            .categoria-cuadradito {
                display: inline-block;
                background-color: #f3f4f6;
                /* Color de fondo */
                padding: 5px 10px;
                /* Espaciado interno */
                margin: 1px;
                /* Separación entre los cuadraditos */
                border-radius: 2px;
                /* Bordes redondeados */
                border: 1px solid #ccc;
                /* Borde */
                font-size: 14px;
                /* Tamaño de fuente */
                text-align: center;
                /* Centrar texto */
            }

            /* Efecto al pasar el mouse */
            .categoria-cuadradito:hover {
                background-color: #e0e0e0;
                /* Cambio de fondo */
                border-color: #888;
                /* Cambio de borde */
                cursor: pointer;
                /* Hacerlo clickeable */
            }

            .medida-cuadradito {
                display: inline-block;
                background-color: #f3f4f6;
                /* Fondo color claro */
                padding: 5px 12px;
                /* Espaciado interno */
                margin: 1px;
                /* Separación entre los chips */
                border-radius: 20px;
                /* Bordes redondeados */
                border: 1px solid #ccc;
                /* Borde sutil */
                font-size: 14px;
                /* Tamaño de la fuente */
                font-weight: 600;
                /* Texto en negrita */
                text-transform: uppercase;
                /* Convertir el texto a mayúsculas */
                color: #333;
                /* Color del texto */
                transition: all 0.3s ease;
                /* Transición para hover */
            }

            /* Efecto al pasar el mouse */
            .medida-cuadradito:hover {
                background-color: #e0e0e0;
                /* Cambio de fondo */
                border-color: #888;
                /* Cambio de borde */
                cursor: pointer;
                /* Hacerlo clickeable */
            }
        </style>
    @endpush

    {{-- JS: FUNCIONES DE DELIVERY Y PROMEDIO DE VOTOS  --}}
    @include('producto.partials.js')

    <div class="container max-w-screen-xl mx-auto py-4">

        <!-- Detalle del producto -->
        <div class="flex flex-col lg:flex-row gap-2">
            <!-- Imagenes del producto -->
            <div class="flex-1" id="product-image-container-{{ $producto->id }}">
                <div class="relative mb-4 px-2">
                    <!-- Imagen principal o alternativa dependiendo de si existe img_path -->
                    @if ($producto->img_path)
                        <img loading="lazy" rel="preload" id="mainImage-{{ $producto->id }}"
                            src="{{ url('/imagen-con-logo/' . $producto->img_path) }}" alt="{{ $producto->nombre }}"
                            class="w-full h-[350px] sm:h-[450px] md:h-[600px] object-cover rounded-lg shadow-lg transition-opacity duration-500">
                    @else
                        <img loading="lazy" rel="preload" id="mainImage-{{ $producto->id }}"
                            src="{{ asset($producto->img_url) }}" alt="{{ $producto->nombre }}"
                            class="w-full h-[350px] sm:h-[450px] md:h-[600px] object-cover rounded-lg shadow-lg transition-opacity duration-500">
                    @endif

                    <!-- Imagen alternativa (solo si img_url existe) -->
                    @if ($producto->img_url && $producto->img_path)
                        <img loading="lazy" rel="preload" id="altImage-{{ $producto->id }}"
                            src="{{ asset($producto->img_url) }}" alt="{{ $producto->nombre }}"
                            class="h-[350px] sm:h-[450px] md:h-[600px] left-2 w-[calc(100%-16px)] object-cover rounded-lg shadow-lg absolute top-0 opacity-0 transition-opacity duration-500">

                        <!-- Icono de apuntar a la derecha -->
                        <button id="toggleRightImage-{{ $producto->id }}"
                            class="absolute top-1/2 right-2 transform -translate-y-1/2 text-3xl bg-opacity-70 hover:bg-opacity-100 rounded-full p-2 transition duration-300 ease-in-out">
                            <i class="fa-solid fa-circle-chevron-right fa-xl text-gray-800"></i>
                        </button>

                        <!-- Icono de apuntar a la izquierda -->
                        <button id="toggleLeftImage-{{ $producto->id }}"
                            class="absolute top-1/2 left-2 transform -translate-y-1/2 text-3xl bg-opacity-70 hover:bg-opacity-100 rounded-full p-2 transition duration-300 ease-in-out">
                            <i class="fa-solid fa-circle-chevron-left fa-xl text-gray-800"></i>
                        </button>
                    @endif
                </div>
            </div>

            <!-- JavaScript para alternar entre las imágenes -->
            <script>
                // Solo ejecuta si la img_url existe
                @if ($producto->img_url)
                    const toggleRightButton = document.getElementById('toggleRightImage-{{ $producto->id }}');
                    const toggleLeftButton = document.getElementById('toggleLeftImage-{{ $producto->id }}');
                    const mainImage = document.getElementById('mainImage-{{ $producto->id }}');
                    const altImage = document.getElementById('altImage-{{ $producto->id }}');

                    // Inicializa la visibilidad (la imagen alternativa comienza oculta)
                    altImage.style.opacity = "0";

                    // Función para alternar las imágenes
                    function toggleImages() {
                        if (altImage.style.opacity === "0") {
                            // Mostrar la imagen alternativa con una transición suave
                            altImage.style.opacity = "1";
                            mainImage.style.opacity = "0";
                        } else {
                            // Volver a la imagen principal con una transición suave
                            altImage.style.opacity = "0";
                            mainImage.style.opacity = "1";
                        }
                    }

                    // Asocia el evento de clic para el botón de la derecha
                    toggleRightButton.addEventListener('click', toggleImages);

                    // Asocia el evento de clic para el botón de la izquierda
                    toggleLeftButton.addEventListener('click', toggleImages);
                @endif
            </script>

            <!-- Detalles del producto -->
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-800 px-2">{{ $producto->nombre }}</h1>

                <!-- Precio -->
                <p class="text-xl font-semibold text-gray-950 py-2 px-2">S/
                    {{ $producto->precioVentaReciente() ?? '...' }}</p>
                <hr>
                <!-- Información del producto -->
                <div class="mt-2">
                    @php
                        // Cálculo del promedio de estrellas y votos
                        $promedio = $producto->calificaciones->avg('puntuacion') ?? 4;
                        $votos = $producto->calificaciones->count() > 0 ? $producto->calificaciones->count() : 12;
                        $stars = floor($promedio);
                        $halfStar = $promedio - $stars >= 0.5;
                        $emptyStars = 5 - $stars - ($halfStar ? 1 : 0);
                    @endphp

                    @auth
                        <p class="text-gray-950 mb-0 px-2"><strong>Calificación:</strong></p>

                        <!-- Estrellas para mostrar la calificación -->
                        <div class="flex items-center px-2">
                            <div id="rating-stars" class="flex text-yellow-500">
                                <!-- Estrellas llenas -->
                                @for ($i = 0; $i < $stars; $i++)
                                    <i class="fas fa-star cursor-pointer" data-star="{{ $i + 1 }}"></i>
                                @endfor

                                <!-- Media estrella si aplica -->
                                @if ($halfStar)
                                    <i class="fas fa-star-half-alt cursor-pointer" data-star="{{ $stars + 1 }}"></i>
                                @endif

                                <!-- Estrellas vacías -->
                                @for ($i = 0; $i < $emptyStars; $i++)
                                    <i class="far fa-star cursor-pointer"
                                        data-star="{{ $stars + ($halfStar ? 1 : 0) + $i + 1 }}"></i>
                                @endfor
                            </div>
                            <span id="rating-count" class="ml-2 text-gray-600">({{ $votos }} votos)</span>
                        </div>
                    @else
                        <p class="text-gray-950 mb-0 px-2"><strong>Calificación:</strong></p>

                        <!-- Estrellas solo lectura para mostrar la calificación -->
                        <div class="flex items-center px-2">
                            <div id="rating-stars" class="flex text-yellow-500">
                                <!-- Estrellas llenas -->
                                @for ($i = 0; $i < $stars; $i++)
                                    <i class="fas fa-star"></i>
                                @endfor

                                <!-- Media estrella si aplica -->
                                @if ($halfStar)
                                    <i class="fas fa-star-half-alt"></i>
                                @endif

                                <!-- Estrellas vacías -->
                                @for ($i = 0; $i < $emptyStars; $i++)
                                    <i class="far fa-star"></i>
                                @endfor
                            </div>
                            <span id="rating-count" class="ml-2 text-gray-600">({{ $votos }} votos)</span>
                        </div>
                    @endauth

                    <p class="text-gray-950 mb-2 px-2"><strong>Descrip:</strong> {{ $producto->descripcion }}</p>
                    <p class="text-gray-950 mb-2 px-2">
                        <strong>Med:</strong>
                        @foreach (explode(',', $producto->medida) as $medida)
                            <span class="medida-cuadradito">{{ $medida }}</span>
                        @endforeach
                    </p>
                    <p class="text-gray-950 mb-2 px-2"><strong>Cat:</strong>
                        @foreach ($producto->categorias->pluck('caracteristica.nombre') as $categoria)
                            <span class="categoria-cuadradito">{{ strtoupper($categoria) }}</span>
                        @endforeach
                    </p>
                    <p class="text-gray-950 mb-2 px-2"><strong>Pres:</strong>
                        {{ $producto->presentacione->caracteristica->nombre }}</p>
                    <div class="mb-2 px-4 py-2 ml-2 mr-4 rounded-lg shadow-md border"
                    :class="{
                        'border-green-400 bg-green-100 text-green-700': parseInt('{{ $producto->stock }}') > 10,
                        'border-yellow-400 bg-yellow-100 text-yellow-700': parseInt('{{ $producto->stock }}') > 5 && parseInt('{{ $producto->stock }}') <= 10,
                        'border-red-400 bg-red-100 text-red-700': parseInt('{{ $producto->stock }}') > 0 && parseInt('{{ $producto->stock }}') <= 5,
                        'border-gray-400 bg-gray-100 text-gray-600': parseInt('{{ $producto->stock }}') === 0
                      }">

                        <p class="flex items-center gap-2 text-lg font-semibold">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m-1-4h4m-2 0a9 9 0 1 1-2.83 17.17" />
                            </svg>
                            Stock: {{ $producto->stock }} unidades
                        </p>
                    </div>

                    <!-- Información adicional de envío -->
                    <div class="mt-2 ml-2 mr-4 p-3 shadow-md border bg-cyan-200 text-gray-800 rounded-lg text-center">
                        <p class="text-lg font-semibold">¡Envíos a todo el Perú!
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/cf/Flag_of_Peru.svg/1280px-Flag_of_Peru.svg.png"
                                alt="Bandera de Perú" class="inline-block h-4">
                        </p>
                        <p class="text-md font-semibold">¡Delivery en Lima contra entrega disponible! 🚚</p>
                    </div>

                </div>

                <!-- Selección de cantidad y botones -->
                <div class="mt-2 px-4">
                    <form action="{{ route('ordenes.store') }}" method="POST" id="delivery-form">
                        @csrf
                        <!-- Campos para el usuario -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="nombre" class="block text-gray-700 mb-1">Nombre:</label>
                                <input required type="text" id="nombre" name="nombre"
                                    placeholder="Ejem: Pedro Diaz"
                                    value="{{ Auth::check() ? Auth::user()->name : '' }}"
                                    class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600 transition duration-200">
                            </div>
                            <div>
                                <label for="telefono" class="block text-gray-700 mb-1">Tel/Cel:</label>
                                <input required type="number" id="telefono" name="telefono" placeholder="999-345-675"
                                    value="{{ Auth::check() ? Auth::user()->telefono : '' }}"value="{{ Auth::check() ? Auth::user()->telefono : '' }}"
                                    class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600 transition duration-200">
                            </div>
                            <div>
                                <label for="direccion" class="block text-gray-700 mb-1">Dirección:</label>
                                <input required type="text" name="direccion" id="direccion"
                                    placeholder="av. angeles #123"
                                    value="{{ Auth::check() ? Auth::user()->direccion : '' }}"
                                    class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600 transition duration-200">
                            </div>
                            <div>
                                <input type="hidden" name="productos[0][producto_id]" value="{{ $producto->id }}">
                                <input type="hidden" name="productos[0][precio]"
                                    value="{{ $producto->precioVentaReciente() }}">
                                <label for="cantidad" class="block text-gray-700 mb-1">Cantidad:</label>
                                {{-- max="{{ $producto->stock }} validar por stock  --}}
                                <input required type="number" id="cantidad" name="productos[0][cantidad]"
                                    value="1" min="1"
                                    class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600 transition duration-200">
                                <input type="hidden" id="nombre_producto" name="nombre_producto"
                                    value="{{ $producto->nombre }}">
                            </div>
                        </div>
                        <div class="mt-4 flex gap-3">
                            <div class="relative inline-block">
                                <!-- Texto en la parte superior derecha -->
                                <span
                                    class="bg-white rounded-lg font-semibold absolute top-0 right-0 text-xs text-gray-900"
                                    style="margin-top: -10px; margin-right: 5px; padding: 2px 4px;">
                                    Destacado<i class="ml-1 fa-solid fa-star" style="color: #FFD43B;"></i>
                                </span>
                                <button class="button-whatsapp">
                                    <a href="https://wa.me/{{ $pageWeb->telefono }}?text=Hola,%20quiero%20ordenar%20{{ urlencode($producto->nombre) }}%20(Código:%20{{ $producto->codigo }})%20Ver%20producto:%20{{ urlencode(url()->current()) }}"
                                        target="_blank"
                                        class="text-white py-2 px-4 rounded-lg shadow-lg flex items-center transition duration-200"
                                        style="background: linear-gradient(to right, #00d46a, #000000);"
                                        title="Ordenar por WhatsApp">
                                        <i class="fa-brands fa-whatsapp fa-bounce mr-1"></i>WhatsApp
                                    </a>
                                </button>
                            </div>
                            @if ($producto->precioVentaReciente())
                                <div class="relative inline-block">
                                    <!-- Texto en la parte superior derecha -->
                                    <span
                                        class="bg-cyan-300 rounded-lg font-semibold absolute top-0 right-0 text-xs text-gray-900"
                                        style="margin-top: -10px; margin-right: 5px; padding: 2px 4px;">
                                        Lima
                                    </span>

                                    <!-- Botón de delivery -->
                                    <button type="submit"
                                        class="text-white py-2 px-4 rounded-lg shadow-lg flex items-center transition duration-200"
                                        style="background: linear-gradient(to right, #818181, #000000);"
                                        title="delivery">
                                        <i class="fa-solid fa-motorcycle fa-flip mr-1"></i>Delivery
                                    </button>
                                </div>
                            @endif
                        </div>
                    </form>

                    <div class="mt-2 flex gap-4">
                        {{-- orden con boton de delivery --}}
                        <button id="ver-orden-btn"
                            class="text-white font-bold py-2 px-4 rounded-lg shadow-lg transition duration-300 transform hover:scale-105"
                            style="display: none; background: linear-gradient(to right, #818181, #000000);"
                            type="button">
                            <i class="fa-solid fa-tags fa-beat mr-1"></i>Ver Orden
                        </button>
                        @if (session('orden_id'))
                            {{-- orden con boton de compra online  --}}
                            <button
                                class="text-white font-bold py-2 px-4 rounded-lg shadow-lg transition duration-300 transform hover:scale-105"
                                style="background: linear-gradient(to right, #818181, #000000);" type="button"
                                onclick="mostrarOrden('{{ session('orden_id') }}')">
                                <!-- Asigna el ID aquí -->
                                <i class="fa-solid fa-tags fa-beat mr-1"></i>Ver Orden
                            </button>
                        @endif

                        @auth
                            <form id="addToCartForm-{{ $producto->id }}" class="add-to-cart-form"
                                data-producto-id="{{ $producto->id }}" method="POST">
                                @csrf
                                <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                <input type="hidden" name="cantidad" value="1">
                                <!-- Si el usuario está autenticado, botón para agregar al carrito -->
                                <button type="button"
                                    class="text-white py-2 px-4 rounded-lg shadow-lg flex items-center transition duration-200 add-to-cart-button"
                                    style="background: linear-gradient(to right, #8400ff, #000000);"
                                    title="Añadir al Carrito">
                                    <i class="fa-solid fa-cart-plus mr-1"></i>Añadir al Carrito
                                </button>
                            </form>
                        @else
                            <!-- Si el usuario no está autenticado -->
                            <div class="relative inline-block">
                                <button type="button"
                                    class="text-white py-2 px-4 rounded-lg shadow-lg flex items-center transition duration-200"
                                    style="background: linear-gradient(to right, #8400ff, #000000);"
                                    title="Añadir al Carrito" onclick="togglePopup()">
                                    <i class="fa-solid fa-cart-plus mr-1"></i>Añadir al Carrito
                                </button>
                            </div>
                        @endauth
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Otras sugerencias de productos -->
    <div class="container max-w-screen-xl mx-auto py-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 px-1">Productos Relacionados</h2>

        <!-- Para pantallas grandes (desktop) -->
        <div class="hidden md:grid md:grid-cols-3 lg:grid-cols-4 gap-6 px-2">
            @foreach ($productosRelacionados as $relacionado)
                <div class="border border-gray-200 rounded-lg p-3 hover:shadow-lg transition-shadow duration-300">
                    <a href="{{ route('producto.show', $relacionado->codigo) }}">
                        @if ($relacionado->img_path)
                            <img loading="lazy" rel="preload"
                                src="{{ url('/imagen-con-logo/' . $relacionado->img_path) }}"
                                alt="{{ $relacionado->nombre }}" class="h-56 w-full object-cover rounded-md mb-1">
                        @else
                            <img loading="lazy" rel="preload" src="{{ $relacionado->img_url }}"
                                alt="{{ $relacionado->nombre }}" class="h-56 w-full object-cover rounded-md mb-1">
                        @endif
                        <h3 class="text-lg font-semibold text-gray-800">
                            {{ \Illuminate\Support\Str::limit(strtoupper($relacionado->nombre), 25) }}</h3>
                        <p class="text-gray-600 font-semibold">S/ {{ $relacionado->precioVentaReciente() }}</p>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- Para pantallas móviles y tablets -->
        <div class="flex overflow-x-auto space-x-4 md:hidden px-2">
            @php $count = 0; @endphp
            @foreach ($productosRelacionados as $relacionado)
                @if ($count % 6 == 0 && $count > 0)
        </div>
        <div class="flex overflow-x-auto space-x-4 md:hidden px-2 py-3">
            @endif
            <div
                class="border border-gray-200 rounded-lg p-3 hover:shadow-lg transition-shadow duration-300 min-w-[250px]">
                <a href="{{ route('producto.show', $relacionado->codigo) }}">
                    @if ($relacionado->img_path)
                        <img loading="lazy" rel="preload"
                            src="{{ url('/imagen-con-logo/' . $relacionado->img_path) }}"
                            alt="{{ $relacionado->nombre }}" class="h-56 w-full object-cover rounded-md mb-1">
                    @else
                        <img loading="lazy" rel="preload" src="{{ $relacionado->img_url }}"
                            alt="{{ $relacionado->nombre }}" class="h-56 w-full object-cover rounded-md mb-1">
                    @endif
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ \Illuminate\Support\Str::limit(strtoupper($relacionado->nombre), 20) }}</h3>
                    <p class="text-gray-600 font-semibold">S/ {{ $relacionado->precioVentaReciente() }}</p>
                </a>
            </div>
            @php $count++; @endphp
            @endforeach
        </div>

    </div>

    @push('js')
    @endpush

</x-app-layout>
