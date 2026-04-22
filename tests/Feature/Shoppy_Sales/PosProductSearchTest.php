<?php

namespace Tests\Feature\Shoppy_Sales;

use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosProductSearchTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        BusinessSetting::create([
            'business_name'   => 'Test Store',
            'currency_symbol' => '$',
            'low_stock'       => 5,
        ]);

        $this->seller = User::factory()->create([
            'role'      => 'seller',
            'is_active' => true,
        ]);

        $this->category = Category::create(['name' => 'Bebidas', 'is_active' => true]);
    }

    private function makeProduct(array $overrides = []): Product
    {
        static $i = 0;
        $i++;

        return Product::create(array_merge([
            'category_id'   => $this->category->id,
            'name'          => "Producto $i",
            'sku'           => "SKU-$i",
            'cost_price'    => 1.0,
            'selling_price' => 2.0,
            'stock'         => 20,
            'unit'          => 'pcs',
            'is_active'     => true,
        ], $overrides));
    }

    public function test_seller_can_search_by_partial_name(): void
    {
        $this->makeProduct(['name' => 'Café Molido', 'sku' => 'CAFE-001']);
        $this->makeProduct(['name' => 'Té Verde',    'sku' => 'TE-001']);

        $response = $this->actingAs($this->seller)
            ->getJson(route('pos.api.products', ['q' => 'Café']));

        $response->assertOk();
        $products = $response->json('products');
        $this->assertCount(1, $products);
        $this->assertEquals('Café Molido', $products[0]['name']);
    }

    public function test_seller_can_search_by_exact_barcode(): void
    {
        $coffee = $this->makeProduct(['name' => 'Café Molido', 'sku' => 'CAFE-001', 'barcode' => '7501234567890']);

        $response = $this->actingAs($this->seller)
            ->getJson(route('pos.api.products', ['q' => '7501234567890']));

        $response->assertOk();
        $products = $response->json('products');
        $this->assertCount(1, $products);
        $this->assertEquals($coffee->id, $products[0]['id']);
    }

    public function test_partial_name_returns_multiple_matches(): void
    {
        $this->makeProduct(['name' => 'Agua Mineral', 'sku' => 'AGUA-001']);
        $this->makeProduct(['name' => 'Agua de Coco', 'sku' => 'AGUA-002']);
        $this->makeProduct(['name' => 'Refresco Cola', 'sku' => 'REF-001']);

        $response = $this->actingAs($this->seller)
            ->getJson(route('pos.api.products', ['q' => 'Agua']));

        $response->assertOk();
        $this->assertCount(2, $response->json('products'));
    }

    public function test_inactive_products_are_excluded(): void
    {
        $this->makeProduct(['name' => 'Café Molido', 'sku' => 'CAFE-001', 'is_active' => false]);

        $response = $this->actingAs($this->seller)
            ->getJson(route('pos.api.products', ['q' => 'Café']));

        $response->assertOk();
        $this->assertEmpty($response->json('products'));
    }

    public function test_category_filter_returns_only_matching_category(): void
    {
        $other = Category::create(['name' => 'Snacks', 'is_active' => true]);

        $this->makeProduct(['name' => 'Bebida A', 'sku' => 'BEB-001', 'category_id' => $this->category->id]);
        $this->makeProduct(['name' => 'Snack B',  'sku' => 'SNA-001', 'category_id' => $other->id]);

        $response = $this->actingAs($this->seller)
            ->getJson(route('pos.api.products', ['category_id' => $this->category->id]));

        $response->assertOk();
        $products = $response->json('products');
        $this->assertCount(1, $products);
        $this->assertEquals('Bebida A', $products[0]['name']);
    }

    public function test_quick_search_without_page_returns_at_most_20_results(): void
    {
        for ($i = 1; $i <= 25; $i++) {
            Product::create([
                'category_id'   => $this->category->id,
                'name'          => "Producto Bulk $i",
                'sku'           => "SKU-BULK-$i",
                'cost_price'    => 1.0,
                'selling_price' => 2.0,
                'stock'         => 10,
                'unit'          => 'pcs',
                'is_active'     => true,
            ]);
        }

        $response = $this->actingAs($this->seller)
            ->getJson(route('pos.api.products'));

        $response->assertOk();
        $this->assertCount(20, $response->json('products'));
        $this->assertNull($response->json('meta'));
    }

    public function test_paginated_search_returns_meta_with_correct_totals(): void
    {
        for ($i = 1; $i <= 35; $i++) {
            Product::create([
                'category_id'   => $this->category->id,
                'name'          => "Producto Pag $i",
                'sku'           => "SKU-PG-$i",
                'cost_price'    => 1.0,
                'selling_price' => 2.0,
                'stock'         => 10,
                'unit'          => 'pcs',
                'is_active'     => true,
            ]);
        }

        $response = $this->actingAs($this->seller)
            ->getJson(route('pos.api.products', ['page' => 1]));

        $response->assertOk();
        $response->assertJsonStructure(['products', 'meta' => ['current_page', 'last_page', 'total']]);
        $this->assertEquals(1,  $response->json('meta.current_page'));
        $this->assertEquals(2,  $response->json('meta.last_page'));
        $this->assertEquals(35, $response->json('meta.total'));
        $this->assertCount(30, $response->json('products'));
    }

    public function test_paginated_second_page_returns_remaining_results(): void
    {
        for ($i = 1; $i <= 35; $i++) {
            Product::create([
                'category_id'   => $this->category->id,
                'name'          => "Producto P2 $i",
                'sku'           => "SKU-P2-$i",
                'cost_price'    => 1.0,
                'selling_price' => 2.0,
                'stock'         => 10,
                'unit'          => 'pcs',
                'is_active'     => true,
            ]);
        }

        $response = $this->actingAs($this->seller)
            ->getJson(route('pos.api.products', ['page' => 2]));

        $response->assertOk();
        $this->assertEquals(2, $response->json('meta.current_page'));
        $this->assertCount(5, $response->json('products')); // 35 - 30 = 5 on page 2
    }

    public function test_response_includes_product_own_low_stock_threshold(): void
    {
        $this->makeProduct(['name' => 'Café Molido', 'sku' => 'CAFE-001', 'low_stock_alert' => 3]);

        $response = $this->actingAs($this->seller)
            ->getJson(route('pos.api.products', ['q' => 'Café']));

        $response->assertOk();
        $this->assertEquals(3.0, $response->json('products.0.low_stock_threshold'));
    }

    public function test_response_falls_back_to_global_threshold_when_product_has_no_alert(): void
    {
        $this->makeProduct(['name' => 'Café Molido', 'sku' => 'CAFE-001', 'low_stock_alert' => null]);

        $response = $this->actingAs($this->seller)
            ->getJson(route('pos.api.products', ['q' => 'Café']));

        $response->assertOk();
        $this->assertEquals(5.0, $response->json('products.0.low_stock_threshold')); // global = 5
    }

    public function test_guest_cannot_access_product_search(): void
    {
        $this->getJson(route('pos.api.products'))->assertUnauthorized();
    }

    public function test_admin_cannot_access_product_search(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin)
            ->getJson(route('pos.api.products'))
            ->assertRedirect('/admin/dashboard');
    }
}
