<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada — Shoppy</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('shoppy-logo.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Franklin:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-50 flex items-center justify-center px-4">

    <div class="w-full max-w-md text-center">

        {{-- Logo --}}
        <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-primary-700">
            <img src="{{ asset('shoppy-logo-white.svg') }}" alt="Shoppy" class="h-12 w-12">
        </div>

        {{-- 404 numeral --}}
        <p class="text-8xl font-extrabold text-stone-200 leading-none select-none">404</p>

        {{-- Heading --}}
        <h1 class="mt-4 text-2xl font-bold text-stone-800">Página no encontrada</h1>

        {{-- Message --}}
        <p class="mt-3 text-stone-600">
            La página que buscas no existe o fue movida.
        </p>

        {{-- Back link --}}
        <a href="/"
           class="mt-8 inline-flex items-center gap-2 rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Volver al inicio
        </a>

    </div>

</body>
</html>
