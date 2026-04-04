<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Sale;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('user')
            ->withCount('items')
            ->orderByDesc('created_at')
            ->paginate(30);

        $currency = BusinessSetting::first()?->currency_symbol ?? '$';

        return view('admin.sales.index', compact('sales', 'currency'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['user', 'items']);
        $currency = BusinessSetting::first()?->currency_symbol ?? '$';

        return response()->json([
            'sale' => $sale,
            'currency' => $currency,
        ]);
    }
}
