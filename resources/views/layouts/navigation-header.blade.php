<style>
    /* estilos */
    .btn-web-link {
        background: linear-gradient(to right, #00bcd4, #000000);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        border-radius: 8px;
        /* Bordes redondeados para un diseño más moderno */
        padding: 8px 18px;
        /* Tamaño optimizado para un aspecto más profesional */
        font-size: 16px;
        /* Tamaño de fuente ajustado */
        transition: background 0.3s ease, box-shadow 0.3s ease;
        /* Transiciones suaves */
        text-decoration: none;
        /* Elimina el subrayado del enlace */
        display: inline-flex;
        /* Para asegurar que el icono y el texto estén alineados */
        align-items: center;
        justify-content: center;
    }

    .btn-web-link:hover {
        background: linear-gradient(to right, #0097a7, #222222);
        /* Efecto hover con gradiente más oscuro */
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4);
        /* Aumenta la sombra en hover */
    }
</style>

<nav class="bg-white text-gray-950 shadow-md">
    <div class="container mx-auto max-w-7xl  px-4 py-6 flex items-center justify-between">
        <!-- Logo -->
        <div class="text-3xl font-bold">
            <a href="{{ route('dashboard') }}"
                class="text-gray-950 hover:text-gray-700 transition-all duration-300 ease-in-out transform hover:scale-105">{{ $pageWeb->nombre ?? 'Nombre de Página' }}</a>
        </div>

        <!-- Buscador -->
        <div class="hidden lg:flex flex-1 mx-8 relative">
            <input disabled type="text" placeholder="Buscar..."
                class="w-full p-2 pl-12 border border-gray-300 rounded-md bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-950 transition duration-300" />
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
        </div>
        {{-- carrito  --}}
        @auth
            <div class="space-x-6 px-3 relative">
                <a title="carrito" href="{{ route('carrito.index') }}" class="text-gray-950 hover:text-gray-700 text-2xl">
                    <i class="fa-solid fa-cart-arrow-down fa-flip"></i>

                    <!-- Mostrar el puntito con la cantidad -->
                    @if ($cartCount > 0)
                        <span id="cart-count"
                            class="absolute top-0 right-0 inline-block w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full text-center">
                            {{ $cartCount }}
                        </span>
                    @else
                        <span id="cart-count"
                            class="hidden absolute top-0 right-0 inline-block w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full text-center"></span>
                        <!-- Ocultar el puntito si el carrito está vacío -->
                    @endif
                </a>
            </div>

            <div class="flex justify-center space-x-4 px-1">
                <button title="mostrar pedidos" onclick="mostrarPedidos()"
                    class="text-gray-950 hover:text-gray-700 text-2xl"
                    title="mis ordenes">
                    <i class="fas fa-receipt mr-2"></i> <!-- Icono de factura -->
                </button>
            </div>
        @else
            <div class="space-x-4 px-3 relative">
                <button title="carrito" class="text-gray-950 hover:text-gray-700 text-2xl" onclick="togglePopup()">
                    <i class="fa-solid fa-cart-arrow-down"></i>
                </button>
            </div>
            {{-- ordenes de usuario no autenticado, con ids de storage  --}}
            <div class="flex justify-center space-x-4 px-1">
                <button title="pedidos" class="text-gray-950 hover:text-gray-700 text-2xl" onclick="mostrarPedidosUserNoAutenticados()">
                    <i class="fas fa-receipt"></i> <!-- Icono de factura -->
                </button>
            </div>
        @endauth

        <!-- Menú móvil (desplegable) -->
        <div id="mobile-menu"
            class="lg:hidden bg-gray-300 rounded-full text-gray-950 border-t border-gray-500 shadow-lg">
            <div class="container mx-auto px-1 py-1 space-y-1 font-semibold">
                @auth
                    <!-- Desplegable del perfil -->
                    <div class="relative">
                        <button id="menuButtonMobile"
                            class="flex items-center text-gray-950 hover:text-indigo-700 text-xl font-medium">
                            <img src="{{ isset(Auth::user()->img) && Auth::user()->img ? asset('storage/img_user/' . Auth::user()->img) : asset('images/user.png') }}"
                                alt="Imagen de perfil" class="w-8 h-8 rounded-full object-cover">
                        </button>
                        <!-- El menú desplegable -->
                        <div id="menuDropdownMobile"
                            class="absolute -left-24 mt-2 w-36 bg-white border border-gray-200 rounded-md shadow-lg py-1 z-50 break-words"
                            style="display: none;">
                            <a href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 text-sm text-gray-900 hover:bg-gray-100">
                                <i class="fa-solid fa-user-edit mr-2"></i> Perfil
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-900 hover:bg-gray-100">
                                    <i class="fa-solid fa-sign-out-alt mr-2"></i> Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Desplegable del perfil user no autentic -->
                    <div class="relative">
                        <button id="menuButtonMobile"
                            class="flex items-center text-gray-950 hover:text-indigo-700 text-xl font-medium">
                            <img src="{{ asset('images/user.png') }}" alt="Imagen de perfil"
                                class="w-8 h-8 rounded-full object-cover">
                        </button>
                        <!-- El menú desplegable -->
                        <div id="menuDropdownMobile"
                            class="absolute -left-36 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg py-1 z-50 break-words"
                            style="display: none;">
                            <a href="{{ route('login') }}" class="block px-4 py-2 text-sm text-gray-900 hover:bg-gray-100">
                                <i class="fa-solid fa-user-lock mr-2"></i> Iniciar sesión
                            </a>
                            <!--<a href="{{ config('services.telegram.url', env('TELEGRAM_URL', 'https://t.me/codigoartico')) }}"-->
                            <!--    target="_blank" rel="noopener noreferrer"-->
                            <!--    class="block w-full text-left px-4 py-2 text-sm text-gray-900 hover:bg-gray-100">-->
                            <!--    <i class="fa-solid fa-envelope mr-2"></i> Quiero esta tienda-->
                            <!--</a>-->
                        </div>
                    </div>
                @endauth
            </div>
        </div>

        <!-- Menú para pantallas grandes -->
        <div class="hidden lg:flex items-center space-x-6">
            <!--<a href="{{ config('services.telegram.url', env('TELEGRAM_URL', 'https://t.me/codigoartico')) }}"-->
            <!--    target="_blank" rel="noopener noreferrer" class="btn-web-link text-sm">-->
            <!--    <i class="fa-solid fa-shop-lock mr-2"></i> Quiero esta tienda-->
            <!--</a>-->
            @if (Auth::check())
                <div class="relative inline-block text-left">
                    <!-- Botón que despliega el menú -->
                    <button id="menuButtonDesktop"
                        class="flex items-center text-gray-950 hover:text-indigo-700 text-xl font-medium">
                        <div class="bg-gray-300 rounded-full border-t border-gray-500 mr-2">
                            <div class="px-1 py-1">
                                <img src="{{ isset(Auth::user()->img) && Auth::user()->img ? asset('storage/img_user/' . Auth::user()->img) : asset('images/user.png') }}"
                                    alt="Imagen de perfil" class="w-8 h-8 rounded-full object-cover">
                            </div>
                        </div>
                        <h3 class="text-lg">Hola, {{ substr(Auth::user()->name, 0, 5) . '...' }}</h3>
                        <svg class="ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Menú desplegable -->
                    <div id="menuDropdownDesktop"
                        class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-md shadow-lg py-1 z-50"
                        style="display: none;">
                        <a href="{{ route('profile.edit') }}"
                            class="block px-4 py-2 text-sm text-gray-900 hover:bg-gray-100">
                            <i class="fa-solid fa-user-edit mr-2"></i> Perfil
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="block w-full">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-900 hover:bg-gray-100">
                                <i class="fa-solid fa-sign-out-alt mr-2"></i> Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="relative inline-block text-left">
                    <button type="button" onclick="togglePopup()"
                        class="flex items-center text-gray-950 hover:text-indigo-700 text-xl font-medium">
                        <div class="bg-gray-300 rounded-full border-t border-gray-500 mr-2">
                            <div class="px-1 py-1">
                                <img src="{{ asset('images/user.png') }}" alt="Imagen de perfil"
                                    class="w-8 h-8 rounded-full object-cover">
                            </div>
                        </div>
                    </button>
                </div>
            @endif
        </div>
        <!-- JavaScript para controlar la apertura y cierre del menú -->
        <script>
            // Menú móvil
            const menuButtonMobile = document.getElementById('menuButtonMobile');
            const menuDropdownMobile = document.getElementById('menuDropdownMobile');

            // Menú para pantallas grandes (usuario autenticado y no autenticado)
            const menuButtonDesktop = document.getElementById('menuButtonDesktop');
            const menuDropdownDesktop = document.getElementById('menuDropdownDesktop');

            // Función para abrir y cerrar menús móviles y de escritorio
            function toggleDropdown(button, dropdown) {
                button.addEventListener('click', function(event) {
                    // Prevenir propagación del click para evitar cerrarse por clic en el documento
                    event.stopPropagation();

                    // Cambiar estilo de visualización del menú
                    if (dropdown.style.display === 'none' || dropdown.style.display === '') {
                        dropdown.style.display = 'block'; // Muestra el menú
                    } else {
                        dropdown.style.display = 'none'; // Oculta el menú
                    }
                });
            }

            // Función para cerrar menús si se hace clic fuera de ellos
            function closeDropdowns(event) {
                // Si se hace clic fuera del botón o menú móvil
                if (menuButtonMobile && !menuButtonMobile.contains(event.target) && !menuDropdownMobile.contains(event
                        .target)) {
                    menuDropdownMobile.style.display = 'none';
                }

                // Si se hace clic fuera del botón o menú de escritorio
                if (menuButtonDesktop && !menuButtonDesktop.contains(event.target) && !menuDropdownDesktop.contains(event
                        .target)) {
                    menuDropdownDesktop.style.display = 'none';
                }
            }

            // Inicializar menús
            if (menuButtonMobile && menuDropdownMobile) {
                toggleDropdown(menuButtonMobile, menuDropdownMobile);
            }

            if (menuButtonDesktop && menuDropdownDesktop) {
                toggleDropdown(menuButtonDesktop, menuDropdownDesktop);
            }

            // Agregar un listener para cerrar los menús al hacer clic fuera de ellos
            document.addEventListener('click', closeDropdowns);
        </script>
    </div>

</nav>
