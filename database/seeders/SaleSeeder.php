<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $sellers = DB::table('users')->where('role', 'seller')->pluck('id')->toArray();
        $products = DB::table('products')->get();

        $customers = [null, 'Juan Pérez', 'Ana García', 'Roberto Sánchez', 'Laura Martínez', 'Pedro Hernández', null, 'Sofía Díaz', null, null];
        $methods = ['cash', 'card'];

        $now = Carbon::now();

        for ($i = 0; $i < 45; $i++) {
            $saleDate = $now->copy()->subDays(rand(0, 30))->subHours(rand(0, 12))->subMinutes(rand(0, 59));
            $sellerId = $sellers[array_rand($sellers)];
            $customer = $customers[array_rand($customers)];
            $method = $methods[array_rand($methods)];

            // Pick 1-5 random products for this sale
            $itemCount = rand(1, 5);
            $saleProducts = $products->random($itemCount);

            $subtotal = 0;
            $items = [];

            foreach ($saleProducts as $product) {
                $qty = rand(1, 4);
                $itemDiscount = rand(0, 10) > 7 ? round($product->selling_price * 0.1, 2) : 0;
                $itemSubtotal = round(($product->selling_price * $qty) - $itemDiscount, 2);
                $subtotal += $itemSubtotal;

                $items[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->selling_price,
                    'quantity' => $qty,
                    'discount_amount' => $itemDiscount,
                    'subtotal' => $itemSubtotal,
                    'created_at' => $saleDate,
                    'updated_at' => $saleDate,
                ];
            }

            $saleDiscount = rand(0, 10) > 8 ? round($subtotal * 0.05, 2) : 0;
            $total = $subtotal - $saleDiscount;
            $amountTendered = $method === 'card' ? $total : ceil($total / 10) * 10;
            $change = round($amountTendered - $total, 2);

            $saleId = DB::table('sales')->insertGetId([
                'user_id' => $sellerId,
                'customer_name' => $customer,
                'subtotal' => $subtotal,
                'discount_amount' => $saleDiscount,
                'payment_method' => $method,
                'amount_tendered' => $amountTendered,
                'change_given' => $change,
                'note' => null,
                'created_at' => $saleDate,
                'updated_at' => $saleDate,
            ]);

            foreach ($items as &$item) {
                $item['sale_id'] = $saleId;
            }

            DB::table('sale_items')->insert($items);
        }
    }
}
