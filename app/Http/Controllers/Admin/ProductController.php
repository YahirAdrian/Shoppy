<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();
        $productsByCategory = Product::with('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category_id');
        $currency = BusinessSetting::first()?->currency_symbol ?? '$';

        return view('admin.inventory.index', compact('categories', 'productsByCategory', 'currency'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products',
            'barcode' => 'nullable|string|max:255|unique:products',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'low_stock_alert' => 'nullable|numeric|min:0',
            'unit' => 'required|string|max:50',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'sku.required' => 'El SKU es obligatorio.',
            'sku.unique' => 'Este SKU ya está en uso.',
            'barcode.unique' => 'Este código de barras ya está en uso.',
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'cost_price.required' => 'El precio de costo es obligatorio.',
            'cost_price.min' => 'El precio de costo no puede ser negativo.',
            'selling_price.required' => 'El precio de venta es obligatorio.',
            'selling_price.min' => 'El precio de venta no puede ser negativo.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.min' => 'El stock no puede ser negativo.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.max' => 'La imagen no debe superar 2MB.',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        Product::create($validated);

        return redirect()->route('admin.inventory.index')->with('success', 'Producto creado exitosamente.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'low_stock_alert' => 'nullable|numeric|min:0',
            'unit' => 'required|string|max:50',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'sku.required' => 'El SKU es obligatorio.',
            'sku.unique' => 'Este SKU ya está en uso.',
            'barcode.unique' => 'Este código de barras ya está en uso.',
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'cost_price.required' => 'El precio de costo es obligatorio.',
            'selling_price.required' => 'El precio de venta es obligatorio.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.max' => 'La imagen no debe superar 2MB.',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                \Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $product->update($validated);

        return redirect()->route('admin.inventory.index')->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return redirect()->route('admin.inventory.index')->with('success', 'Producto eliminado exitosamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.inventory.index')->with('error', 'No se puede eliminar el producto porque tiene registros asociados.');
        }
    }

    public function adjustStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric',
            'note' => 'required|string|max:500',
        ], [
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.numeric' => 'La cantidad debe ser un número.',
            'note.required' => 'La nota es obligatoria.',
        ]);

        $product->update([
            'stock' => $product->stock + $validated['quantity'],
        ]);

        StockMovement::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'action' => 'adjustment',
            'quantity' => $validated['quantity'],
            'note' => $validated['note'],
        ]);

        return redirect()->route('admin.inventory.index')->with('success', 'Stock ajustado exitosamente.');
    }
}
