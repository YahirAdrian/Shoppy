<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PosApiController extends Controller
{
    public function searchProducts(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));
        $categoryId = $request->query('category_id');
        $page = $request->query('page');

        $lowStockDefault = \App\Models\BusinessSetting::value('low_stock') ?? 5;

        $builder = Product::query()
            ->with('category:id,name')
            ->where('is_active', true)
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($inner) use ($query) {
                    $inner->where('barcode', $query)
                        ->orWhere('name', 'LIKE', '%' . $query . '%');
                });
            })
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->orderBy('name');

        $mapProduct = fn (Product $p) => [
            'id' => $p->id,
            'name' => $p->name,
            'barcode' => $p->barcode,
            'selling_price' => (float) $p->selling_price,
            'stock' => (float) $p->stock,
            'unit' => $p->unit,
            'category' => $p->category?->name,
            'category_id' => $p->category_id,
            'image' => $p->image ? asset('storage/' . $p->image) : null,
            'low_stock_threshold' => (float) ($p->low_stock_alert ?? $lowStockDefault),
        ];

        if ($page !== null) {
            $paginated = $builder->paginate(30);

            return response()->json([
                'products' => $paginated->getCollection()->map($mapProduct)->values(),
                'meta' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'total' => $paginated->total(),
                ],
            ]);
        }

        $products = $builder->limit(20)->get()->map($mapProduct);

        return response()->json(['products' => $products]);
    }

    public function storeSale(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash',
            'amount_tendered' => 'required|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
            'force_low_stock' => 'nullable|boolean',
        ], [
            'items.required' => 'La venta debe tener al menos un producto.',
            'items.min' => 'La venta debe tener al menos un producto.',
            'payment_method.in' => 'Método de pago no soportado.',
            'amount_tendered.min' => 'El monto recibido no puede ser negativo.',
        ]);

        $force = (bool) ($validated['force_low_stock'] ?? false);

        $sale = DB::transaction(function () use ($validated, $force) {
            $productIds = collect($validated['items'])->pluck('product_id')->unique();
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $lines = [];
            $stockIssues = [];
            $subtotal = 0;
            $totalDiscount = 0;

            foreach ($validated['items'] as $item) {
                $product = $products[$item['product_id']];
                $quantity = (float) $item['quantity'];
                $discount = (float) ($item['discount'] ?? 0);
                $unitPrice = (float) $product->selling_price;
                $lineSubtotal = max(0, $quantity * $unitPrice - $discount);

                if ($quantity > (float) $product->stock) {
                    $stockIssues[] = [
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'requested' => $quantity,
                        'available' => (float) $product->stock,
                    ];
                }

                $lines[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'subtotal' => $lineSubtotal,
                ];

                $subtotal += $quantity * $unitPrice;
                $totalDiscount += $discount;
            }

            if (! empty($stockIssues) && ! $force) {
                abort(response()->json([
                    'message' => 'Stock insuficiente para algunos productos.',
                    'stock_issues' => $stockIssues,
                ], 422));
            }

            $total = max(0, $subtotal - $totalDiscount);
            $tendered = (float) $validated['amount_tendered'];

            if ($tendered < $total) {
                abort(response()->json([
                    'message' => 'El monto recibido es menor al total.',
                ], 422));
            }

            $sale = Sale::create([
                'user_id' => auth()->id(),
                'customer_name' => $validated['customer_name'] ?? null,
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount,
                'payment_method' => $validated['payment_method'],
                'amount_tendered' => $tendered,
                'change_given' => $tendered - $total,
                'note' => $validated['note'] ?? null,
            ]);

            foreach ($lines as $line) {
                $product = $line['product'];

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'discount_amount' => $line['discount'],
                    'subtotal' => $line['subtotal'],
                ]);

                $product->decrement('stock', $line['quantity']);

                StockMovement::create([
                    'user_id' => auth()->id(),
                    'product_id' => $product->id,
                    'action' => 'sale',
                    'quantity' => -((int) round($line['quantity'])),
                    'note' => 'Venta #' . $sale->id,
                ]);
            }

            return $sale->load('items');
        });

        return response()->json([
            'sale' => [
                'id'         => $sale->id,
                'created_at' => $sale->created_at->toIso8601String(),
                'customer_name' => $sale->customer_name,
                'note' => $sale->note,
                'payment_method' => $sale->payment_method,
                'subtotal' => (float) $sale->subtotal,
                'discount_amount' => (float) $sale->discount_amount,
                'total' => (float) $sale->total,
                'amount_tendered' => (float) $sale->amount_tendered,
                'change_given' => (float) $sale->change_given,
                'items' => $sale->items->map(fn (SaleItem $i) => [
                    'product_id' => $i->product_id,
                    'product_name' => $i->product_name,
                    'quantity' => (float) $i->quantity,
                    'unit_price' => (float) $i->unit_price,
                    'discount_amount' => (float) $i->discount_amount,
                    'subtotal' => (float) $i->subtotal,
                ]),
            ],
        ], 201);
    }

    public function showSale(Sale $sale): JsonResponse
    {
        if ($sale->user_id !== auth()->id()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $sale->load('items');

        return response()->json([
            'sale' => [
                'id' => $sale->id,
                'created_at' => $sale->created_at->toIso8601String(),
                'customer_name' => $sale->customer_name,
                'note' => $sale->note,
                'payment_method' => $sale->payment_method,
                'subtotal' => (float) $sale->subtotal,
                'discount_amount' => (float) $sale->discount_amount,
                'total' => (float) $sale->total,
                'amount_tendered' => (float) $sale->amount_tendered,
                'change_given' => (float) $sale->change_given,
                'items' => $sale->items->map(fn (SaleItem $i) => [
                    'product_name' => $i->product_name,
                    'quantity' => (float) $i->quantity,
                    'unit_price' => (float) $i->unit_price,
                    'discount_amount' => (float) $i->discount_amount,
                    'subtotal' => (float) $i->subtotal,
                ]),
            ],
        ]);
    }

    public function deleteSale(Sale $sale, Request $request): JsonResponse
    {
        $adminToken = $request->header('X-Admin-Token');
        $sessionToken = session('pos_admin_token');
        $expiresAt = session('pos_admin_expires_at');

        if (! $adminToken || $adminToken !== $sessionToken || ! $expiresAt || now()->isAfter($expiresAt)) {
            return response()->json(['message' => 'Se requiere autorización de administrador.'], 403);
        }

        if ($sale->user_id !== auth()->id()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        DB::transaction(function () use ($sale) {
            $sale->load('items');

            foreach ($sale->items as $item) {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);

                StockMovement::create([
                    'user_id' => auth()->id(),
                    'product_id' => $item->product_id,
                    'action' => 'return',
                    'quantity' => (int) round($item->quantity),
                    'note' => 'Venta anulada #' . $sale->id,
                ]);
            }

            $sale->delete();
        });

        return response()->json(['message' => 'Venta eliminada.']);
    }

    public function adminAuth(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'El correo es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $admin = User::where('email', $validated['email'])
            ->where('role', 'admin')
            ->where('is_active', true)
            ->first();

        if (! $admin || ! Hash::check($validated['password'], $admin->password)) {
            return response()->json(['message' => 'Credenciales incorrectas.'], 401);
        }

        $token = Str::uuid()->toString();
        $expiresAt = now()->addMinutes(15);

        session([
            'pos_admin_token' => $token,
            'pos_admin_expires_at' => $expiresAt,
        ]);

        return response()->json([
            'token' => $token,
            'expires_at' => $expiresAt->toIso8601String(),
        ]);
    }
}
