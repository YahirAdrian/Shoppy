<x-layouts.admin title="Dashboard — Shoppy Adminer">

    {{-- Page header --}}
    <h1 class="text-2xl font-bold text-stone-800 uppercase">Dashboard</h1>
    <p class="mt-1 text-stone-500">Resumen del negocio y estadísticas</p>

    @include('admin.dashboard.summary-cards')

    @include('admin.dashboard.stats')

    @include('admin.dashboard.tasks')

</x-layouts.admin>
