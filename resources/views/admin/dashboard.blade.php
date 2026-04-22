<x-layouts.admin title="Dashboard — Shoppy Adminer">

    {{-- Page header --}}
    <h1 class="text-2xl font-bold text-stone-800 uppercase">Dashboard</h1>
    <p class="mt-1 text-stone-500">Resumen del negocio y estadísticas</p>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="mt-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mt-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @include('admin.dashboard.summary-cards')

    @include('admin.dashboard.stats')

    @include('admin.dashboard.tasks')

</x-layouts.admin>
