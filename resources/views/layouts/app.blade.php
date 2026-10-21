<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Meta descripción general para SEO -->
    <meta name="description" content="@yield('meta_description', $pageWeb->descripcion)">
    <meta name="author" content="@yield('meta_author', 'Xobek')">

    <title>{{ $pageWeb->nombre }} - @yield('title')</title>

    <!-- Open Graph para redes sociales -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', 'Xobek - Moda, Tecnología, Accesorios y más')">
    <meta property="og:description" content="@yield('og_description', 'En ' . $pageWeb->nombre . ' ' . ($pageWeb->descripcion ?: 'encuentras todo lo que necesitas: moda, tecnología y accesorios. Compra online y recibe en casa. ¡Descúbrelo Ahora!'))">
    <meta property="og:image" content="@yield('og_image', isset($imgAccount) ? asset($imgAccount) : asset('images/banner2.png'))">
    <meta property="og:url" content="@yield('og_url', $pageWeb->url_web)">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', 'Xobek - Moda, Tecnología, Accesorios y más')">
    <meta name="twitter:description" content="@yield('twitter_description', 'En ' . $pageWeb->nombre . ' ' . ($pageWeb->descripcion ?: 'encuentras todo lo que necesitas: moda, tecnología y accesorios. Compra online y recibe en casa. ¡Descúbrelo Ahora!'))">
    <meta name="twitter:image" content="@yield('twitter_image', isset($imgAccount) ? asset($imgAccount) : asset('images/banner2.png'))">
    <meta name="twitter:site" content="@Xobek">

    <!-- Robots -->
    <meta name="robots" content="index, follow">

    <!-- Canonical -->
    <link rel="canonical" href="{{ $pageWeb->url_web }}">


    <!-- Fonts -->
    <script src="https://kit.fontawesome.com/16454e4b22.js" crossorigin="anonymous" defer></script>
    <!-- jQuery asíncrono -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 asíncrono -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    
    <link rel="stylesheet" href="{{ asset('build/assets/app-fy1h1wPh.css') }}">

    {{-- favicon --}}
    {{-- <link rel="shortcut icon" href="{{ asset('images/favicon/favicon.png') }}" type="image/x-icon"> --}}
    <link rel="shortcut icon" href="{{ isset($imgAccount) ? asset($imgAccount) : asset('images/favicon/favicon.png') }}"
        type="image/x-icon">
    @stack('css')

</head>

<body class="font-sans antialiased" translate="no">
    <div id="app">

        <div id="main">
            @include('layouts.navigation-header')


            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
            {{-- SONIDOS Y ALERTAS --}}
            @include('components.sound-alert.alert-view')
            @include('components.sound-alert.reproducir-sonido')

            {{-- --------------FUNCION AGREGAR PRODUCTO AL CARRITO----------------------- --}}
            @include('components.add-producto-cart.agregar-producto')

            {{-- MODAL ORDENES | USER AUTENTICADO | USER NO AUTENTICADO DESDE STORAGE  --}}
            @include('components.modal_orders.modal')

            {{-- MODAL 1 ORDEN | USER AUTENTICADO | USER NO AUTENTICADO  --}}
            @include('components.modal_orders.modaluserlibres')

            {{-- MODAL INICIAR SESION|CREAR CUENTA  --}}
            @include('components.modal-sesion.modal')

            {{-- SPINNER DE CARGA --}}
            @include('layouts.partials.spinner-carga')

        </div>
    </div>
    {{-- Footer  --}}
    <footer>
        @include('layouts.navigation-footer')
    </footer>

    {{-- aqui los scripts  --}}
    @stack('js')
</body>

</html>
