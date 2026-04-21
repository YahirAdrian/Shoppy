<?php

namespace Tests\Feature\Shoppy_Sales;

use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleCreation extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        BusinessSetting::create([
            'business_name' => 'Test Store',
            'currency_symbol' => '$',
            'low_stock' => 5,
            'receipt_header' => 'Gracias por su compra',
            'receipt_footer' => 'Vuelva pronto',
        ]);

        $category = Category::create([
            'name' => 'Beverages',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Coffee',
            'sku' => 'SKU-001',
            'barcode' => '123456789',
            'cost_price' => 1.5,
            'selling_price' => 3.5,
            'stock' => 100,
            'unit' => 'cup',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Tea',
            'sku' => 'SKU-002',
            'barcode' => '987654321',
            'cost_price' => 1.0,
            'selling_price' => 2.5,
            'stock' => 50,
            'unit' => 'cup',
            'is_active' => true,
        ]);
    }

    private function seller(): User
    {
        return User::factory()->create([
            'name' => 'Seller One',
            'email' => 'seller@test.local',
            'role' => 'seller',
            'is_active' => true,
        ]);
    }

    public function test_seller_can_create_sale_with_single_item(): void
    {
        $seller = $this->seller();
        $coffee = Product::where('name', 'Coffee')->first();

        $response = $this->actingAs($seller)->postJson(route('pos.api.sales.store'), [
            'items' => [
                [
                    'product_id' => $coffee->id,
                    'quantity' => 2,
                    'discount' => 0,
                ],
            ],
            'payment_method' => 'cash',
            'amount_tendered' => 10,
            'customer_name' => null,
            'note' => null,
            'force_low_stock' => false,
        ]);

        $response->assertCreated();
        $response->assertJsonStructure([
            'sale' => [
                'id',
                'created_at',
                'customer_name',
                'note',
                'payment_method',
                'subtotal',
                'discount_amount',
                'total',
                'amount_tendered',
                'change_given',
                'items' => [
                    [
                        'product_id',
                        'product_name',
                        'quantity',
                        'unit_price',
                        'discount_amount',
                        'subtotal',
                    ],
                ],
            ],
        ]);

        $data = $response->json();
        $this->assertEquals(2 * 3.5, $data['sale']['subtotal']);
        $this->assertEquals(2 * 3.5, $data['sale']['total']);
        $this->assertEquals(10 - (2 * 3.5), $data['sale']['change_given']);

        $this->assertDatabaseHas('sales', [
            'id' => $data['sale']['id'],
            'user_id' => $seller->id,
            'subtotal' => 2 * 3.5,
            'discount_amount' => 0,
            'payment_method' => 'cash',
            'amount_tendered' => 10,
        ]);

        $coffee->refresh();
        $this->assertEquals(100 - 2, $coffee->stock);
    }

    public function test_seller_can_create_sale_with_multiple_items(): void
    {
        $seller = $this->seller();
        $coffee = Product::where('name', 'Coffee')->first();
        $tea = Product::where('name', 'Tea')->first();

        $response = $this->actingAs($seller)->postJson(route('pos.api.sales.store'), [
            'items' => [
                [
                    'product_id' => $coffee->id,
                    'quantity' => 2,
                    'discount' => 0,
                ],
                [
                    'product_id' => $tea->id,
                    'quantity' => 3,
                    'discount' => 0.5,
                ],
            ],
            'payment_method' => 'cash',
            'amount_tendered' => 20,
            'customer_name' => 'John Doe',
            'note' => 'For delivery',
            'force_low_stock' => false,
        ]);

        $response->assertCreated();
        $data = $response->json();

        $subtotal = (2 * 3.5) + (3 * 2.5); // 7 + 7.5 = 14.5
        $discount = 0.5; // from tea
        $total = $subtotal - $discount; // 14

        $this->assertEquals($subtotal, $data['sale']['subtotal']);
        $this->assertEquals($discount, $data['sale']['discount_amount']);
        $this->assertEquals($total, $data['sale']['total']);
        $this->assertEquals($subtotal, (2 * 3.5) + (3 * 2.5));
    }

    public function test_sale_includes_all_required_fields_for_receipt(): void
    {
        $seller = $this->seller();
        $coffee = Product::where('name', 'Coffee')->first();

        $response = $this->actingAs($seller)->postJson(route('pos.api.sales.store'), [
            'items' => [
                [
                    'product_id' => $coffee->id,
                    'quantity' => 1,
                    'discount' => 0,
                ],
            ],
            'payment_method' => 'cash',
            'amount_tendered' => 5,
            'customer_name' => 'Alice',
            'note' => 'Gift',
            'force_low_stock' => false,
        ]);

        $response->assertCreated();
        $data = $response->json()['sale'];

        $this->assertNotNull($data['id']);
        $this->assertNotNull($data['created_at']);
        $this->assertEquals('Alice', $data['customer_name']);
        $this->assertEquals('Gift', $data['note']);
        $this->assertEquals('cash', $data['payment_method']);
        $this->assertGreaterThan(0, $data['subtotal']);
        $this->assertGreaterThanOrEqual(0, $data['discount_amount']);
        $this->assertGreaterThan(0, $data['total']);
        $this->assertEquals(5, $data['amount_tendered']);
        $this->assertGreaterThanOrEqual(0, $data['change_given']);

        $this->assertIsArray($data['items']);
        $this->assertCount(1, $data['items']);
        $item = $data['items'][0];
        $this->assertEquals('Coffee', $item['product_name']);
        $this->assertEquals(1, $item['quantity']);
        $this->assertEquals(3.5, $item['unit_price']);
    }

    public function test_insufficient_stock_returns_422_with_stock_issues(): void
    {
        $seller = $this->seller();
        $coffee = Product::where('name', 'Coffee')->first();

        $response = $this->actingAs($seller)->postJson(route('pos.api.sales.store'), [
            'items' => [
                [
                    'product_id' => $coffee->id,
                    'quantity' => 150, // More than available (100)
                    'discount' => 0,
                ],
            ],
            'payment_method' => 'cash',
            'amount_tendered' => 500,
            'force_low_stock' => false,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'stock_issues' => [
                [
                    'product_id',
                    'name',
                    'requested',
                    'available',
                ],
            ],
        ]);

        $data = $response->json();
        $this->assertCount(1, $data['stock_issues']);
        $this->assertEquals($coffee->id, $data['stock_issues'][0]['product_id']);
        $this->assertEquals(150, $data['stock_issues'][0]['requested']);
        $this->assertEquals(100, $data['stock_issues'][0]['available']);

        $coffee->refresh();
        $this->assertEquals(100, $coffee->stock); // Stock unchanged
    }

    public function test_force_low_stock_allows_oversell(): void
    {
        $seller = $this->seller();
        $coffee = Product::where('name', 'Coffee')->first();

        $response = $this->actingAs($seller)->postJson(route('pos.api.sales.store'), [
            'items' => [
                [
                    'product_id' => $coffee->id,
                    'quantity' => 150,
                    'discount' => 0,
                ],
            ],
            'payment_method' => 'cash',
            'amount_tendered' => 600, // 150 * 3.5 = 525
            'force_low_stock' => true,
        ]);

        $response->assertCreated();
        $coffee->refresh();
        $this->assertEquals(100 - 150, $coffee->stock);
    }

    public function test_insufficient_tendered_amount_returns_422(): void
    {
        $seller = $this->seller();
        $coffee = Product::where('name', 'Coffee')->first();

        $response = $this->actingAs($seller)->postJson(route('pos.api.sales.store'), [
            'items' => [
                [
                    'product_id' => $coffee->id,
                    'quantity' => 2,
                    'discount' => 0,
                ],
            ],
            'payment_method' => 'cash',
            'amount_tendered' => 5, // 2 * 3.5 = 7, but only 5 tendered
            'force_low_stock' => false,
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => 'El monto recibido es menor al total.',
        ]);
    }

    public function test_sale_page_displays_business_name_and_receipt_text(): void
    {
        $seller = $this->seller();

        $response = $this->actingAs($seller)->get(route('pos.sale'));

        $response->assertOk();
        $response->assertViewHas('business', function ($business) {
            return $business['name'] === 'Test Store' &&
                   $business['receipt_header'] === 'Gracias por su compra' &&
                   $business['receipt_footer'] === 'Vuelva pronto';
        });
        $response->assertViewHas('currency', '$');
    }

    public function test_stock_movements_created_on_sale(): void
    {
        $seller = $this->seller();
        $coffee = Product::where('name', 'Coffee')->first();

        $response = $this->actingAs($seller)->postJson(route('pos.api.sales.store'), [
            'items' => [
                [
                    'product_id' => $coffee->id,
                    'quantity' => 5,
                    'discount' => 0,
                ],
            ],
            'payment_method' => 'cash',
            'amount_tendered' => 30,
            'force_low_stock' => false,
        ]);

        $response->assertCreated();
        $saleId = $response->json()['sale']['id'];

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $coffee->id,
            'user_id' => $seller->id,
            'action' => 'sale',
            'quantity' => -5,
            'note' => 'Venta #' . $saleId,
        ]);
    }
}
