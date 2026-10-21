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
    {{-- <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> --}}
    <script src="https://kit.fontawesome.com/16454e4b22.js" crossorigin="anonymous"></script>

    <!-- TailwindCSS asíncrono -->
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- favicon --}}
    {{-- <link rel="shortcut icon" href="{{ asset('images/favicon/favicon.png') }}" type="image/x-icon"> --}}
    <link rel="shortcut icon" href="{{ isset($imgAccount) ? asset($imgAccount) : asset('images/favicon/favicon.png') }}"
        type="image/x-icon">
    <!-- Scripts -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center sm:pt-0">
        {{-- <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-indigo-600" />
                </a>
            </div> --}}

        <div class="w-full sm:max-w-md px-6 shadow-lg overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>
</body>

</html>
