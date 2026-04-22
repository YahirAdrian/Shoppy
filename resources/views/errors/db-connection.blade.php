<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error de conexión — Shoppy</title>
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

        {{-- Error icon --}}
        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
            </svg>
        </div>

        {{-- Heading --}}
        <h1 class="text-2xl font-bold text-stone-800">Error de conexión</h1>

        {{-- Message --}}
        <p class="mt-3 text-stone-600">
            No se pudo conectar a la base de datos.<br>
            Intenta de nuevo en un momento.
        </p>
        <p class="mt-2 text-sm text-stone-400">
            Si el problema persiste, contacta al administrador del sistema.
        </p>

        {{-- Retry button --}}
        <button onclick="window.location.reload()"
                class="mt-8 inline-flex items-center gap-2 rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Reintentar
        </button>

    </div>

</body>
</html>
