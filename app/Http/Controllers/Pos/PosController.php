<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Category;

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
        return view('pos.status', [
            'currency' => BusinessSetting::first()?->currency_symbol ?? '$',
        ]);
    }
}
