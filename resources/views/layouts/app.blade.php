<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') — WARNET.OS</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:400,600,700|poppins:600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-paper font-sans text-ink antialiased selection:bg-acid-lime selection:text-ink">
    @auth
        @include('partials.navbar')
    @endauth

    <main class="mx-auto w-full max-w-360 px-4 py-7 sm:px-6 lg:px-10">
        @include('partials.alert')
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>