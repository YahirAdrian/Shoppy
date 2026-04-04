<section class="mt-8">
    <h2 class="text-lg font-bold text-stone-800">Estadísticas</h2>

    <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Line chart: ventas mensuales --}}
        <div class="lg:col-span-2 rounded-xl bg-white p-5 shadow">
            <h3 class="mb-4 text-sm font-semibold text-stone-600 uppercase">Ventas mensuales</h3>
            <canvas id="salesChart" height="120"></canvas>
        </div>

        {{-- Pie chart: ingresos por categoría --}}
        <div class="rounded-xl bg-white p-5 shadow">
            <h3 class="mb-4 text-sm font-semibold text-stone-600 uppercase">Ingresos por categoría</h3>
            <canvas id="categoryChart" height="200"></canvas>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Ventas mensuales — line chart
        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                datasets: [
                    {
                        label: 'Ventas ($)',
                        data: [8200, 9100, 7800, 12480, 10300, 11500, 9800, 13200, 14100, 12900, 15600, 16800],
                        borderColor: '#6535b8',
                        backgroundColor: 'rgba(101, 53, 184, 0.1)',
                        fill: true,
                        tension: 0.3,
                    },
                    {
                        label: 'Gastos ($)',
                        data: [5200, 5800, 5100, 6300, 5900, 6100, 5700, 7200, 6800, 6500, 7400, 7800],
                        borderColor: '#e88a10',
                        backgroundColor: 'rgba(232, 138, 16, 0.1)',
                        fill: true,
                        tension: 0.3,
                    },
                    {
                        label: 'Ganancia ($)',
                        data: [3000, 3300, 2700, 6180, 4400, 5400, 4100, 6000, 7300, 6400, 8200, 9000],
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22, 163, 74, 0.1)',
                        fill: true,
                        tension: 0.3,
                    },
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString() } }
                }
            }
        });

        // Ingresos por categoría — pie chart
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: ['Bebidas', 'Abarrotes', 'Lácteos', 'Snacks', 'Limpieza'],
                datasets: [{
                    data: [4200, 3800, 2100, 1500, 880],
                    backgroundColor: ['#6535b8', '#e88a10', '#16a34a', '#9b70ff', '#f5a623'],
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    });
</script>
