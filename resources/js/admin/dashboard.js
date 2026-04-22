const PALETTE = ['#6535b8', '#e88a10', '#16a34a', '#9b70ff', '#f5a623', '#3b82f6', '#ef4444', '#06b6d4'];

export default function initDashboardCharts(dailySalesChart, categoryRevenueChart, currency) {
    const salesEl    = document.getElementById('salesChart');
    const categoryEl = document.getElementById('categoryChart');

    if (salesEl) {
        new Chart(salesEl, {
            type: 'line',
            data: {
                labels: dailySalesChart.labels,
                datasets: [{
                    label: 'Ventas',
                    data: dailySalesChart.data,
                    borderColor: '#6535b8',
                    backgroundColor: 'rgba(101, 53, 184, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + currency + ' ' + Number(ctx.parsed.y).toLocaleString('es', { minimumFractionDigits: 2 }),
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => currency + ' ' + Number(v).toLocaleString('es', { minimumFractionDigits: 0 }),
                        },
                    },
                    x: {
                        ticks: { maxTicksLimit: 10 },
                    },
                },
            },
        });
    }

    if (categoryEl && categoryRevenueChart.labels.length > 0) {
        new Chart(categoryEl, {
            type: 'doughnut',
            data: {
                labels: categoryRevenueChart.labels,
                datasets: [{
                    data: categoryRevenueChart.data,
                    backgroundColor: PALETTE.slice(0, categoryRevenueChart.labels.length),
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + currency + ' ' + Number(ctx.parsed).toLocaleString('es', { minimumFractionDigits: 2 }),
                        },
                    },
                },
            },
        });
    }
}
