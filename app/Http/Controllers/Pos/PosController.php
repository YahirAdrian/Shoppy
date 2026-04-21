<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Sale;

class PosController extends Controller
{
    public function sale()
    {
        $settings = BusinessSetting::first();

        return view('pos.sale', [
            'currency' => $settings?->currency_symbol ?? '$',
            'business' => [
                'name' => $settings?->business_name ?? 'Shoppy',
                'address' => $settings?->address,
                'phone' => $settings?->phone,
                'email' => $settings?->email,
                'receipt_header' => $settings?->receipt_header,
                'receipt_footer' => $settings?->receipt_footer,
            ],
        ]);
    }

    public function search()
    {
        return view('pos.search', [
            'currency' => BusinessSetting::first()?->currency_symbol ?? '$',
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function status()
    {
        $settings = BusinessSetting::first();
        $userId = auth()->id();

        $sales = Sale::with('items')
            ->where('user_id', $userId)
            ->whereDate('created_at', today())
            ->orderByDesc('created_at')
            ->get();

        $totalSold = $sales->sum(fn ($s) => (float) $s->subtotal - (float) $s->discount_amount);
        $count = $sales->count();

        return view('pos.status', [
            'currency' => $settings?->currency_symbol ?? '$',
            'sellerName' => auth()->user()->name,
            'totalSales' => $count,
            'totalSold' => $totalSold,
            'avgTicket' => $count > 0 ? $totalSold / $count : 0,
            'salesData' => $sales->map(fn ($s) => [
                'id' => $s->id,
                'created_at' => $s->created_at->toIso8601String(),
                'subtotal' => (float) $s->subtotal,
                'discount_amount' => (float) $s->discount_amount,
                'total' => (float) $s->total,
                'payment_method' => $s->payment_method,
                'note' => $s->note,
            ]),
        ]);
    }
}
