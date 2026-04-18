<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PosApiController extends Controller
{
    public function searchProducts(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));
        $categoryId = $request->query('category_id');

        $products = Product::query()
            ->with('category:id,name')
            ->where('is_active', true)
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($inner) use ($query) {
                    $inner->where('barcode', $query)
                        ->orWhere('name', 'LIKE', '%' . $query . '%');
                });
            })
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(fn (Product $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'barcode' => $p->barcode,
                'selling_price' => (float) $p->selling_price,
                'stock' => (float) $p->stock,
                'unit' => $p->unit,
                'category' => $p->category?->name,
                'category_id' => $p->category_id,
                'image' => $p->image ? asset('storage/' . $p->image) : null,
            ]);

        return response()->json(['products' => $products]);
    }

    public function storeSale(Request $request): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
