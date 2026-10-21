<div class="container mx-auto p-2">
    <div class="relative hidden sm:block">
        <!-- Botón de flecha izquierda -->
        <button title="mover a la izquierda" class="arrow-button left" onclick="handleArrowClick('left')">
            <i class="fa fa-chevron-left"></i>
        </button>

        <!-- Contenedor de categorías (Pantallas grandes) -->
        <div class="category-wrapper custom-scroll py-2 hidden sm:flex">
            @foreach ($categorias as $categoria)
                <button id="btn-{{ $categoria->id }}" onclick="filterCategoryItems('{{ $categoria->id }}')"
                    class="bg-black text-white text-sm px-6 py-1 rounded-lg shadow-md hover:bg-gray-900 transition transform hover:scale-105 font-semibold {{ $loop->first ? 'active-button highlight-shadow' : '' }} category-button">
                    <i class="fa-solid fa-tags mr-1"></i>{{ strtoupper($categoria->caracteristica->nombre) }}
                </button>
            @endforeach
        </div>

        <!-- Botón de flecha derecha -->
        <button title="mover a la derecha" class="arrow-button right" onclick="handleArrowClick('right')">
            <i class="fa fa-chevron-right"></i>
        </button>
    </div>
    <!-- Sección de productos para computadoras -->
    <div id="productsContainer" class="hidden sm:block py-4">
        @foreach ($categorias as $categoria)
            <div id="category-{{ $categoria->id }}"
                class="category-section mb-8 max-w-screen-xl mx-auto {{ $loop->first ? '' : 'hidden' }}">
                <div class="hidden sm:grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ($categoria->productos as $producto)
                        <a href="{{ route('producto.show', ['codigo' => $producto->codigo]) }}">
                            <div class="h-96 relative group card bg-white shadow-lg rounded-lg p-4 overflow-hidden">
                                <!-- Imagen del producto -->
                                @if ($producto->img_path)
                                    <img loading="lazy" rel="preload" src="{{ url('/imagen-con-logo/' . $producto->img_path) }}"
                                        alt="{{ $producto->nombre }}"
                                        class="h-64 w-full object-cover mb-4 transition-transform duration-300 group-hover:scale-105 rounded-md">
                                @else
                                    <img loading="lazy" rel="preload" src="{{ $producto->img_url ?? asset('path_to_default_image/default.jpg') }}"
                                        alt="{{ $producto->nombre }}"
                                        class="h-64 w-full object-cover mb-4 transition-transform duration-300 group-hover:scale-105 rounded-md">
                                @endif
                                <!-- Nombre y detalles del producto -->
                                <h3 class="text-gray-950 text-lg font-semibold">
                                    {{ \Illuminate\Support\Str::limit($producto->nombre, 22) }}
                                </h3>
                                <p class="text-sm text-gray-500">
                                    {{ \Illuminate\Support\Str::limit(strtoupper($categoria->caracteristica->nombre), 25) }}
                                </p>
                                <p class="text-gray-800 font-medium text-lg">S/{{ $producto->precioVentaReciente() }}
                                </p>
                                <!-- Iconos (expandir y carrito) -->
                                <div
                                    class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center space-x-4">
                                    <!-- Botón de expandir -->
                                    <button title="abrir detalles del producto"
                                        onclick="event.preventDefault(); openModal(
                                            '{{ $producto->nombre }}', 
                                            '{{ $producto->img_path }}',  <!-- img_path local -->
                                            '{{ $producto->img_url }}',   <!-- img_url externa -->
                                            '{{ $producto->descripcion }}', 
                                            '{{ $producto->precioVentaReciente() }}', 
                                            '{{ $categoria->caracteristica->nombre }}', 
                                            '{{ $producto->codigo }}')"
                                        class="bg-white p-2 rounded-full text-gray-700 hover:text-gray-900 transition">
                                        <i class="fa-solid fa-arrows-up-down-left-right fa-beat"
                                            style="color: #000000;"></i>
                                    </button>

                                    <!-- Botón de agregar al carrito -->
                                    <form id="addToCartForm-{{ $producto->id }}" class="add-to-cart-form"
                                        data-producto-id="{{ $producto->id }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                        <input type="hidden" name="cantidad" value="1">
                                        <button title="añadir al carrito" type="button"
                                            class="bg-white p-2 rounded-full text-gray-700 hover:text-gray-900 transition add-to-cart-button">
                                            <i class="fa-solid fa-cart-plus fa-bounce" style="color: #000000;"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    <script>
        let scrollInterval; // Intervalo para el desplazamiento automático
        let userInteracting = false; // Para detectar si el usuario está interactuando

        function cloneCategoryItems() {
            const categoriesWrapper = document.querySelector('.category-wrapper');
            const categoryItems = categoriesWrapper.children;

            // Clonar los primeros y últimos 5 elementos
            const startClones = [...categoryItems].slice(0, 5).map(item => {
                const clone = item.cloneNode(true);
                clone.setAttribute('data-clone', 'true'); // Marcar como clon
                clone.removeAttribute('id'); // Eliminar ID para evitar conflictos
                return clone;
            });

            const endClones = [...categoryItems].slice(-5).map(item => {
                const clone = item.cloneNode(true);
                clone.setAttribute('data-clone', 'true'); // Marcar como clon
                clone.removeAttribute('id'); // Eliminar ID para evitar conflictos
                return clone;
            });

            // Insertar clones al contenedor
            startClones.forEach(clone => categoriesWrapper.appendChild(clone));
            endClones.reverse().forEach(clone => categoriesWrapper.insertBefore(clone, categoriesWrapper.firstChild));

            // Ajustar el scroll para que inicie en la posición inicial
            categoriesWrapper.scrollLeft = categoriesWrapper.offsetWidth;
        }

        function startScrolling() {
            const categoriesWrapper = document.querySelector('.category-wrapper');
            scrollInterval = setInterval(() => {
                if (!userInteracting) {
                    categoriesWrapper.scrollBy({
                        left: 1,
                        behavior: 'smooth'
                    });

                    // Reiniciar al centro al llegar a los extremos
                    if (categoriesWrapper.scrollLeft <= 0) {
                        categoriesWrapper.scrollLeft = categoriesWrapper.scrollWidth / 3;
                    } else if (categoriesWrapper.scrollLeft >= categoriesWrapper.scrollWidth - categoriesWrapper
                        .offsetWidth) {
                        categoriesWrapper.scrollLeft = categoriesWrapper.scrollWidth / 3;
                    }
                }
            }, 30); // Velocidad del desplazamiento
        }

        function stopScrolling() {
            clearInterval(scrollInterval);
        }

        function handleArrowClick(direction) {
            const categoriesWrapper = document.querySelector('.category-wrapper');
            const scrollDistance = categoriesWrapper.offsetWidth / 2; // Desplazamiento de la mitad del ancho visible
            userInteracting = true;

            stopScrolling();

            if (direction === 'left') {
                categoriesWrapper.scrollBy({
                    left: -scrollDistance,
                    behavior: 'smooth'
                });
            } else if (direction === 'right') {
                categoriesWrapper.scrollBy({
                    left: scrollDistance,
                    behavior: 'smooth'
                });
            }

            // Ajustar el scroll al centro después del desplazamiento
            setTimeout(() => {
                if (categoriesWrapper.scrollLeft <= 0) {
                    categoriesWrapper.scrollLeft = categoriesWrapper.scrollWidth / 3;
                } else if (categoriesWrapper.scrollLeft >= categoriesWrapper.scrollWidth - categoriesWrapper
                    .offsetWidth) {
                    categoriesWrapper.scrollLeft = categoriesWrapper.scrollWidth / 3;
                }
            }, 500);

            // Reanudar el desplazamiento automático después de 2 segundos
            setTimeout(() => {
                userInteracting = false;
                startScrolling();
            }, 2000);
        }

        document.addEventListener('DOMContentLoaded', () => {
            cloneCategoryItems(); // Clonar categorías para el efecto infinito
            startScrolling(); // Iniciar el desplazamiento automático al cargar
        });

        function filterCategoryItems(categoryId) {
            // Ocultar todas las secciones de categorías
            const allSections = document.querySelectorAll('.category-section');
            allSections.forEach(section => {
                section.classList.add('hidden');
            });

            // Mostrar la categoría seleccionada
            const selectedSection = document.getElementById(`category-${categoryId}`);
            if (selectedSection) {
                selectedSection.classList.remove('hidden');
            }

            // Seleccionar todos los botones de categoría y limpiar los estilos
            const categoryButtons = document.querySelectorAll('.category-wrapper > .category-button');
            categoryButtons.forEach(button => {
                button.classList.remove('active-button', 'highlight-shadow'); // Limpiar estilos
            });

            // Aplicar los estilos al botón correspondiente
            const selectedButton = document.querySelector(`.category-wrapper > #btn-${categoryId}`);
            if (selectedButton) {
                selectedButton.classList.add('active-button', 'highlight-shadow');
            }
        }
    </script>
    <style>
        /* Botones de flechas */
        .arrow-button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            z-index: 10;
        }

        .arrow-button.left {
            left: 10px;
        }

        .arrow-button.right {
            right: 10px;
        }

        /* Contenedor de categorías */
        .category-wrapper {
            display: flex;
            overflow: hidden;
            white-space: nowrap;
            position: relative;
        }

        .category-wrapper button {
            flex-shrink: 0;
            margin: 0 5px;
        }

        /* Estilos para el botón activo */
        .active-button {
            background: linear-gradient(to right, #00bcd4, #000000);
        }

        .highlight-shadow {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);
        }

        .hover\:active-button:hover {
            background-color: #000000;
        }

        .hover\:scale-105:hover {
            transform: scale(1.05);
        }
    </style>


    <!-- Aviso para deslizar -->
    <div class="sm:hidden bg-gradient-to-r from-[#00bcd4] to-[#ffffff] text-center py-2 font-semibold text-sm rounded-t-md shadow-md mb-2">
        <span><i class="fa-solid fa-angles-left mr-2"></i>Desliza para Explorar más Categorias<i class="fa-solid fa-angles-right ml-2"></i></span>
    </div>
    <!-- Diseño horizontal para pantallas pequeñas -->
    <div class="container mx-auto block sm:hidden">
        <!-- Botones de Filtro -->
        <div class="flex overflow-x-auto pb-2 mb-1">
            @foreach ($categorias as $categoria)
                <button
                    class="btn-filter {{ $loop->first ? 'active' : '' }} bg-gray-200 text-xs-extra text-gray-700 font-semibold py-2 rounded-full shadow-md transition-all duration-300"
                    data-tipo="{{ $categoria->id }}">
                    <i class="fa-solid fa-tags mr-1"></i>{{ strtoupper($categoria->caracteristica->nombre) }}
                </button>
            @endforeach
        </div>

        <!-- Productos -->
        <div class="productos-lista">
            @foreach ($categorias as $categoria)
                <div class="productos-container hidden" data-tipo="{{ $categoria->id }}">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @foreach ($categoria->productos as $producto)
                            <div
                                class="border border-gray-200 bg-white rounded-lg p-3 hover:shadow-lg transition-shadow duration-300">
                                <a href="{{ route('producto.show', ['codigo' => $producto->codigo]) }}">
                                    <!-- Imagen del producto -->
                                    @if ($producto->img_path)
                                        <img loading="lazy" rel="preload" src="{{ url('/imagen-con-logo/' . $producto->img_path) }}"
                                            alt="{{ $producto->nombre }}"
                                            class="h-36 w-full object-cover mb-4 rounded-md">
                                    @else
                                        <img loading="lazy" rel="preload" src="{{ $producto->img_url ?? asset('path_to_default_image/default.jpg') }}"
                                            alt="{{ $producto->nombre }}"
                                            class="h-36 w-full object-cover mb-4 rounded-md">
                                    @endif
                                    <h3 class="text-lg font-semibold text-gray-800">
                                        {{ \Illuminate\Support\Str::limit($producto->nombre, 25) }}
                                    </h3>
                                    <p class="text-gray-600 font-semibold">
                                        S/ {{ number_format($producto->precioVentaReciente(), 2) }}
                                    </p>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filtros = document.querySelectorAll('.btn-filter');
            const contenedores = document.querySelectorAll('.productos-container');

            // Activar el primer filtro y mostrar su contenido
            filtros[0].classList.add('active');
            contenedores[0].classList.remove('hidden');

            filtros.forEach(filtro => {
                filtro.addEventListener('click', () => {
                    const tipoSeleccionado = filtro.getAttribute('data-tipo');

                    // Quitar la clase activa de todos los filtros y aplicarla solo al seleccionado
                    filtros.forEach(btn => btn.classList.remove('active'));
                    filtro.classList.add('active');

                    // Ocultar todos los productos y mostrar solo los de la categoría seleccionada
                    contenedores.forEach(container => {
                        container.classList.add('hidden');
                        if (container.getAttribute('data-tipo') === tipoSeleccionado) {
                            container.classList.remove('hidden');
                        }
                    });
                });
            });
        });
    </script>
    <style>
        /* Estilos de los botones de filtro */
        .btn-filter {
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        /* Estilo base */
        .btn-filter {
            background-color: #f3f4f6;
            color: #4b5563;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 1rem;
            font-weight: 600;
            margin-right: 10px;
        }

        /* Botón activo */
        .btn-filter.active {
            background: linear-gradient(to right, #00bcd4, #000000);
            color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .text-xs-extra {
            font-size: 0.80rem;
            /* Tamaño de fuente más pequeño que text-xs */
            line-height: 1rem;
            /* Ajusta la altura de línea si es necesario */
        }

        /* Productos ocultos
        .hidden {
            display: none;
        } */
    </style>


    <!-- Modal para expandir producto -->
    <div id="productModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-2xl w-full">
            <div class="modal-content">
                <!-- Imagen del producto -->
                <img id="modalProductImage" src="" alt="" class="h-64 object-cover">

                <!-- Detalles del producto -->
                <div class="modal-details">
                    <div class="flex justify-between items-center">
                        <h3></h3> <!-- Manteneer la X al margen -->
                        <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <h3 id="modalProductName"></h3>
                    <p id="modalProductPrice" class="text-gray-800 font-semibold text-xl"></p>
                    <hr>
                    <p id="modalCategory" class="text-gray-700 font-semibold text-lg py-1"></p>
                    <p id="modalProductDescription" class="text-gray-600"></p>

                    <a id="pedidoButton"
                        class="bg-black hover:bg-yellow-900 text-white py-2 px-3 rounded flex items-center w-24 justify-center">
                        <i class="fa-solid fa-motorcycle"></i>| Pedir
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script>
        // No es necesario baseUrl porque ya estamos accediendo a la ruta directamente desde la URL en Laravel
        function openModal(nombre, imgPath, imgUrl, descripcion, precio, categoria, codigo) {
            document.getElementById('modalProductName').textContent = nombre;
    
            // Verifica si imgPath existe, si no usa imgUrl (si imgUrl está vacío, usa la imagen predeterminada)
            const imageUrl = imgPath ? 
                '/imagen-con-logo/' + imgPath : // Solo concatenamos la ruta directa sin baseUrl
                (imgUrl || '/path_to_default_image/default.jpg'); // Usa la ruta predeterminada si no hay imgPath
    
            document.getElementById('modalProductImage').src = imageUrl; // Asigna la imagen al modal
    
            document.getElementById('modalProductDescription').textContent = descripcion;
            document.getElementById('modalCategory').textContent = categoria;
            document.getElementById('modalProductPrice').textContent = "S/ " + precio;
    
            // Establecer el href del botón de pedido
            document.getElementById('pedidoButton').setAttribute('href', `{{ url('/producto/') }}/${codigo}`);
    
            document.getElementById('productModal').classList.remove('hidden');
        }
    
        function closeModal() {
            document.getElementById('productModal').classList.add('hidden');
        }
    </script>
    
    <style>
        #productModal .modal-content {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            /* Espacio entre la imagen y los datos */
        }

        #productModal img {
            width: 40%;
            /* Ajusta el ancho de la imagen */
            border-radius: 8px;
            /* Bordes redondeados */
            border: 1px solid #ddd;
            /* Borde de la imagen */
        }

        #productModal .modal-details {
            width: 60%;
            /* Ajusta el ancho de los detalles */
        }

        #productModal h3 {
            font-size: 1.75rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        #productModal p {
            margin-bottom: 10px;
        }
    </style>
</div>
