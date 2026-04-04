<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $currency = BusinessSetting::first()?->currency_symbol ?? '$';
        $report = null;
        $filters = null;

        if ($request->has('period')) {
            $filters = $this->resolveFilters($request);
            $report = $this->buildReport($filters);
        }

        return view('admin.reports.index', compact('categories', 'currency', 'report', 'filters'));
    }

    private function resolveFilters(Request $request): array
    {
        $period = $request->input('period', 'today');
        $now = Carbon::now();

        switch ($period) {
            case 'today':
                $from = $now->copy()->startOfDay();
                $to = $now->copy()->endOfDay();
                $label = 'Hoy (' . $from->format('d/m/Y') . ')';
                break;
            case 'week':
                $from = $now->copy()->startOfWeek();
                $to = $now->copy()->endOfWeek();
                $label = 'Esta semana (' . $from->format('d/m') . ' – ' . $to->format('d/m/Y') . ')';
                break;
            case 'month':
                $from = $now->copy()->startOfMonth();
                $to = $now->copy()->endOfMonth();
                $label = 'Este mes (' . $from->format('M Y') . ')';
                break;
            case 'year':
                $from = $now->copy()->startOfYear();
                $to = $now->copy()->endOfYear();
                $label = 'Este año (' . $now->format('Y') . ')';
                break;
            case 'custom':
                $from = Carbon::parse($request->input('date_from'))->startOfDay();
                $to = Carbon::parse($request->input('date_to'))->endOfDay();
                $label = $from->format('d/m/Y') . ' – ' . $to->format('d/m/Y');
                break;
            default:
                $from = $now->copy()->startOfDay();
                $to = $now->copy()->endOfDay();
                $label = 'Hoy';
        }

        return [
            'period' => $period,
            'period_label' => $label,
            'from' => $from,
            'to' => $to,
            'category_id' => $request->input('category_id'),
            'product_ids' => $request->input('product_ids', []),
        ];
    }

    private function buildReport(array $filters): array
    {
        $query = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('sales.created_at', [$filters['from'], $filters['to']]);

        if (!empty($filters['category_id'])) {
            $query->where('products.category_id', $filters['category_id']);
        }

        if (!empty($filters['product_ids'])) {
            $query->whereIn('sale_items.product_id', $filters['product_ids']);
        }

        $rows = $query
            ->selectRaw('
                sale_items.product_id,
                sale_items.product_name,
                categories.name as category_name,
                SUM(sale_items.quantity) as total_quantity,
                SUM(sale_items.unit_price * sale_items.quantity) as total_revenue,
                SUM(sale_items.discount_amount) as total_discount,
                SUM(sale_items.subtotal) as total_net
            ')
            ->groupBy('sale_items.product_id', 'sale_items.product_name', 'categories.name')
            ->orderBy('categories.name')
            ->orderByDesc('total_net')
            ->get();

        $salesCount = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.created_at', [$filters['from'], $filters['to']])
            ->when(!empty($filters['category_id']), fn ($q) => $q->where('products.category_id', $filters['category_id']))
            ->when(!empty($filters['product_ids']), fn ($q) => $q->whereIn('sale_items.product_id', $filters['product_ids']))
            ->distinct('sale_items.sale_id')
            ->count('sale_items.sale_id');

        return [
            'rows' => $rows,
            'sales_count' => $salesCount,
            'totals' => [
                'quantity' => $rows->sum('total_quantity'),
                'revenue' => $rows->sum('total_revenue'),
                'discount' => $rows->sum('total_discount'),
                'net' => $rows->sum('total_net'),
            ],
        ];
    }
}
