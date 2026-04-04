@props(['title' => 'Shoppy Adminer'])

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Shoppy Adminer' }}</title>
    <link rel="icon" type="image/x-icon" href="{{asset('shoppy-logo.svg')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Franklin:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100" x-data="{ sidebarOpen: false }">

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-30 bg-black/50 lg:hidden"
         @click="sidebarOpen = false"
         x-cloak>
    </div>

    {{-- Sidebar --}}
    <x-admin.sidebar />

    {{-- Main content --}}
    <div class="lg:pl-64">
        {{-- Mobile top bar --}}
        <header class="sticky top-0 z-20 flex items-center gap-4 bg-white px-4 py-3 shadow-sm lg:hidden">
            <button @click="sidebarOpen = true" class="text-stone-600 hover:text-stone-900">
                <img src="{{ asset('icons/menu.svg') }}" alt="Menú" class="h-6 w-6">
            </button>
            <span class="text-lg font-semibold text-stone-800">Shoppy Adminer</span>
        </header>

        {{-- Page content --}}
        <main class="p-6 lg:p-8">
            {{ $slot }}
        </main>
    </div>

</body>
</html>
