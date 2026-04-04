<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;

class BusinessSettingController extends Controller
{
    public function edit()
    {
        $settings = BusinessSetting::firstOrFail();

        return view('admin.business.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = BusinessSetting::firstOrFail();

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'currency_symbol' => 'required|string|max:10',
            'low_stock' => 'required|integer|min:1',
            'receipt_header' => 'nullable|string',
            'receipt_footer' => 'nullable|string',
        ], [
            'business_name.required' => 'El nombre del negocio es obligatorio.',
            'business_name.max' => 'El nombre no debe superar 255 caracteres.',
            'logo.image' => 'El archivo debe ser una imagen.',
            'logo.max' => 'La imagen no debe superar 2MB.',
            'email.email' => 'El correo electrónico no es válido.',
            'currency_symbol.required' => 'El símbolo de moneda es obligatorio.',
            'low_stock.required' => 'El umbral de stock bajo es obligatorio.',
            'low_stock.integer' => 'El umbral de stock bajo debe ser un número entero.',
            'low_stock.min' => 'El umbral de stock bajo debe ser al menos 1.',
        ]);

        if ($request->hasFile('logo')) {
            if ($settings->logo) {
                \Storage::disk('public')->delete($settings->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        } elseif ($request->boolean('remove_logo')) {
            if ($settings->logo) {
                \Storage::disk('public')->delete($settings->logo);
            }
            $validated['logo'] = null;
        }

        $settings->update($validated);

        return redirect()->route('admin.business.edit')->with('success', 'Configuración del negocio actualizada exitosamente.');
    }
}
