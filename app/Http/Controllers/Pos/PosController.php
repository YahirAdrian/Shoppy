<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Category;

class PosController extends Controller
{
    public function sale()
    {
        return view('pos.sale', [
            'currency' => BusinessSetting::first()?->currency_symbol ?? '$',
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
