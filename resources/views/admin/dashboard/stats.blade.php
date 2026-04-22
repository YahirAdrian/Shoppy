<section class="mt-8">
    <h2 class="text-lg font-bold text-stone-800">Estadísticas</h2>

    <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Line chart: ventas diarias últimos 30 días --}}
        <div class="lg:col-span-2 rounded-xl bg-white p-5 shadow">
            <h3 class="mb-4 text-sm font-semibold text-stone-600 uppercase">Ventas diarias — últimos 30 días</h3>
            <canvas id="salesChart" height="120"></canvas>
        </div>

        {{-- Doughnut chart: ingresos por categoría --}}
        <div class="rounded-xl bg-white p-5 shadow">
            <h3 class="mb-4 text-sm font-semibold text-stone-600 uppercase">Ingresos por categoría</h3>
            @if (count($categoryRevenueChart['data']) > 0)
                <canvas id="categoryChart" height="200"></canvas>
            @else
                <div class="flex h-40 items-center justify-center text-sm text-stone-400">
                    Sin datos de ventas por categoría.
                </div>
            @endif
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.initDashboardCharts(
            @js($dailySalesChart),
            @js($categoryRevenueChart),
            @js($currency)
        );
    });
</script>
