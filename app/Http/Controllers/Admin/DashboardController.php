<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\PosSession;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function index()
    {
        $currency        = BusinessSetting::value('currency_symbol') ?? '$';
        $lowStockDefault = (float) (BusinessSetting::value('low_stock') ?? 5);

        // --- Summary: Ventas ---
        $now        = now();
        $salesStats = [
            'daily'   => $this->salesForPeriod($now->copy()->startOfDay(), $now->copy()->endOfDay()),
            'weekly'  => $this->salesForPeriod($now->copy()->startOfWeek(), $now->copy()->endOfWeek()),
            'monthly' => $this->salesForPeriod($now->copy()->startOfMonth(), $now->copy()->endOfMonth()),
        ];

        $recentSales = Sale::latest()
            ->limit(3)
            ->get()
            ->map(fn (Sale $s) => [
                'id'    => $s->id,
                'total' => round((float) $s->subtotal - (float) $s->discount_amount, 2),
            ]);

        // --- Summary: Productos ---
        $topProducts = SaleItem::selectRaw('product_name, SUM(quantity) as total_qty')
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(3)
            ->get();

        $lowStockProducts = Product::where('is_active', true)
            ->whereRaw('stock <= COALESCE(low_stock_alert, ?)', [$lowStockDefault])
            ->orderBy('stock')
            ->limit(3)
            ->get(['name', 'stock']);

        // --- Summary: Vendedores ---
        $sellers = User::where('role', 'seller')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function (User $seller) {
                $todaySales = Sale::whereDate('created_at', today())
                    ->where('user_id', $seller->id)
                    ->count();

                $session = PosSession::where('seller_id', $seller->id)
                    ->where('status', 'active')
                    ->first();

                return [
                    'id'          => $seller->id,
                    'name'        => $seller->name,
                    'today_sales' => $todaySales,
                    'session'     => $session ? [
                        'id'           => $session->id,
                        'current_cash' => (float) $session->current_cash,
                        'can_end'      => $session->canEnd(),
                    ] : null,
                ];
            });

        // --- Chart: Ventas diarias (últimos 30 días) ---
        $monthsEs = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $chartStart = now()->subDays(29)->startOfDay();
        $chartEnd   = now()->endOfDay();

        $rawDailyRevenue = Sale::selectRaw('DATE(created_at) as day, SUM(subtotal - discount_amount) as revenue')
            ->whereBetween('created_at', [$chartStart, $chartEnd])
            ->groupBy('day')
            ->pluck('revenue', 'day');

        $dailyLabels = [];
        $dailyData   = [];
        for ($i = 29; $i >= 0; $i--) {
            $date          = now()->subDays($i);
            $key           = $date->format('Y-m-d');
            $dailyLabels[] = $date->format('j') . ' ' . $monthsEs[(int) $date->format('n')];
            $dailyData[]   = round((float) ($rawDailyRevenue[$key] ?? 0), 2);
        }

        $dailySalesChart = ['labels' => $dailyLabels, 'data' => $dailyData];

        // --- Chart: Ingresos por categoría ---
        $categoryRevenue = SaleItem::join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw('categories.name as category_name, SUM(sale_items.subtotal) as revenue')
            ->groupBy('categories.name')
            ->orderByDesc('revenue')
            ->get();

        $categoryRevenueChart = [
            'labels' => $categoryRevenue->pluck('category_name')->toArray(),
            'data'   => $categoryRevenue->map(fn ($r) => round((float) $r->revenue, 2))->toArray(),
        ];

        // --- Tasks ---
        $pendingTasks = Task::where('is_completed', false)
            ->whereNotNull('due_date')
            ->where('due_date', '<=', today())
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        $upcomingTasks = Task::where('is_completed', false)
            ->whereNotNull('due_date')
            ->where('due_date', '>', today())
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'currency',
            'salesStats',
            'recentSales',
            'topProducts',
            'lowStockProducts',
            'sellers',
            'dailySalesChart',
            'categoryRevenueChart',
            'pendingTasks',
            'upcomingTasks',
        ));
    }

    public function endSession(PosSession $session): RedirectResponse
    {
        if ($session->status !== 'active') {
            return back()->with('error', 'La sesión no está activa.');
        }

        if (! $session->canEnd()) {
            return back()->with('error', 'El vendedor tiene efectivo en caja. Debe retirarlo desde el POS antes de terminar la sesión.');
        }

        $session->update([
            'status'      => 'finished',
            'finished_at' => now(),
        ]);

        return back()->with('success', 'Turno terminado correctamente.');
    }

    private function salesForPeriod(Carbon $start, Carbon $end): array
    {
        $row = Sale::whereBetween('created_at', [$start, $end])
            ->selectRaw('COUNT(*) as count, SUM(subtotal - discount_amount) as revenue')
            ->first();

        return [
            'count'   => (int) ($row->count ?? 0),
            'revenue' => round((float) ($row->revenue ?? 0), 2),
        ];
    }
}
