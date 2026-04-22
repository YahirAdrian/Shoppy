<?php

namespace Tests\Feature\Shoppy_Sales;

use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\PosSession;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosSaleCreationTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;
    private PosSession $session;

    protected function setUp(): void
    {
        parent::setUp();

        BusinessSetting::create([
            'business_name'   => 'Test Store',
            'currency_symbol' => '$',
            'low_stock'       => 5,
            'receipt_header'  => 'Gracias por su compra',
            'receipt_footer'  => 'Vuelva pronto',
        ]);

        $category = Category::create(['name' => 'Beverages', 'is_active' => true]);

        Product::create([
            'category_id'   => $category->id,
            'name'          => 'Coffee',
            'sku'           => 'SKU-001',
            'barcode'       => '123456789',
            'cost_price'    => 1.5,
            'selling_price' => 3.5,
            'stock'         => 100,
            'unit'          => 'cup',
            'is_active'     => true,
        ]);

        Product::create([
            'category_id'   => $category->id,
            'name'          => 'Tea',
            'sku'           => 'SKU-002',
            'barcode'       => '987654321',
            'cost_price'    => 1.0,
            'selling_price' => 2.5,
            'stock'         => 50,
            'unit'          => 'cup',
            'is_active'     => true,
        ]);

        $this->seller = User::factory()->create([
            'role'      => 'seller',
            'is_active' => true,
        ]);

        $this->session = PosSession::create([
            'seller_id'    => $this->seller->id,
            'opening_cash' => 100.0,
            'current_cash' => 100.0,
            'status'       => 'active',
            'started_at'   => now(),
        ]);
    }

    public function test_seller_can_create_sale_with_single_item(): void
    {
        $coffee = Product::where('name', 'Coffee')->first();

        $response = $this->actingAs($this->seller)->postJson(route('pos.api.sales.store'), [
            'items'           => [['product_id' => $coffee->id, 'quantity' => 2, 'discount' => 0]],
            'payment_method'  => 'cash',
            'amount_tendered' => 10,
            'customer_name'   => null,
            'note'            => null,
            'force_low_stock' => false,
        ]);

        $response->assertCreated();
        $response->assertJsonStructure([
            'sale' => [
                'id', 'created_at', 'customer_name', 'note', 'payment_method',
                'subtotal', 'discount_amount', 'total', 'amount_tendered', 'change_given',
                'items' => [['product_id', 'product_name', 'quantity', 'unit_price', 'discount_amount', 'subtotal']],
            ],
        ]);

        $data = $response->json('sale');
        $this->assertEquals(7.0, $data['subtotal']);
        $this->assertEquals(7.0, $data['total']);
        $this->assertEquals(3.0, $data['change_given']);

        $this->assertDatabaseHas('sales', [
            'id'              => $data['id'],
            'user_id'         => $this->seller->id,
            'subtotal'        => 7.0,
            'discount_amount' => 0,
            'payment_method'  => 'cash',
            'amount_tendered' => 10,
        ]);

        $coffee->refresh();
        $this->assertEquals(98, $coffee->stock);
    }

    public function test_seller_can_create_sale_with_multiple_items(): void
    {
        $coffee = Product::where('name', 'Coffee')->first();
        $tea    = Product::where('name', 'Tea')->first();

        $response = $this->actingAs($this->seller)->postJson(route('pos.api.sales.store'), [
            'items' => [
                ['product_id' => $coffee->id, 'quantity' => 2, 'discount' => 0],
                ['product_id' => $tea->id,    'quantity' => 3, 'discount' => 0.5],
            ],
            'payment_method'  => 'cash',
            'amount_tendered' => 20,
            'customer_name'   => 'John Doe',
            'note'            => 'For delivery',
            'force_low_stock' => false,
        ]);

        $response->assertCreated();
        $data = $response->json('sale');

        $this->assertEquals(14.5, $data['subtotal']);  // (2*3.5) + (3*2.5)
        $this->assertEquals(0.5,  $data['discount_amount']);
        $this->assertEquals(14.0, $data['total']);
    }

    public function test_sale_includes_all_required_fields_for_receipt(): void
    {
        $coffee = Product::where('name', 'Coffee')->first();

        $response = $this->actingAs($this->seller)->postJson(route('pos.api.sales.store'), [
            'items'           => [['product_id' => $coffee->id, 'quantity' => 1, 'discount' => 0]],
            'payment_method'  => 'cash',
            'amount_tendered' => 5,
            'customer_name'   => 'Alice',
            'note'            => 'Gift',
            'force_low_stock' => false,
        ]);

        $response->assertCreated();
        $data = $response->json('sale');

        $this->assertNotNull($data['id']);
        $this->assertNotNull($data['created_at']);
        $this->assertEquals('Alice', $data['customer_name']);
        $this->assertEquals('Gift', $data['note']);
        $this->assertEquals('cash', $data['payment_method']);
        $this->assertEquals(3.5, $data['subtotal']);
        $this->assertEquals(0.0, $data['discount_amount']);
        $this->assertEquals(3.5, $data['total']);
        $this->assertEquals(5.0, $data['amount_tendered']);
        $this->assertEquals(1.5, $data['change_given']);
        $this->assertCount(1, $data['items']);
        $this->assertEquals('Coffee', $data['items'][0]['product_name']);
        $this->assertEquals(3.5,     $data['items'][0]['unit_price']);
    }

    public function test_insufficient_stock_returns_422_with_stock_issues(): void
    {
        $coffee = Product::where('name', 'Coffee')->first();

        $response = $this->actingAs($this->seller)->postJson(route('pos.api.sales.store'), [
            'items'           => [['product_id' => $coffee->id, 'quantity' => 150, 'discount' => 0]],
            'payment_method'  => 'cash',
            'amount_tendered' => 600,
            'force_low_stock' => false,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'stock_issues' => [['product_id', 'name', 'requested', 'available']],
        ]);

        $issues = $response->json('stock_issues');
        $this->assertCount(1, $issues);
        $this->assertEquals($coffee->id, $issues[0]['product_id']);
        $this->assertEquals(150, $issues[0]['requested']);
        $this->assertEquals(100, $issues[0]['available']);

        $coffee->refresh();
        $this->assertEquals(100, $coffee->stock);
    }

    public function test_force_low_stock_allows_oversell(): void
    {
        $coffee = Product::where('name', 'Coffee')->first();

        $this->actingAs($this->seller)->postJson(route('pos.api.sales.store'), [
            'items'           => [['product_id' => $coffee->id, 'quantity' => 150, 'discount' => 0]],
            'payment_method'  => 'cash',
            'amount_tendered' => 600,
            'force_low_stock' => true,
        ])->assertCreated();

        $coffee->refresh();
        $this->assertEquals(-50, $coffee->stock);
    }

    public function test_insufficient_tendered_amount_returns_422(): void
    {
        $coffee = Product::where('name', 'Coffee')->first();

        $this->actingAs($this->seller)->postJson(route('pos.api.sales.store'), [
            'items'           => [['product_id' => $coffee->id, 'quantity' => 2, 'discount' => 0]],
            'payment_method'  => 'cash',
            'amount_tendered' => 5, // total = 7, tendered < total
            'force_low_stock' => false,
        ])->assertStatus(422)->assertJsonFragment(['message' => 'El monto recibido es menor al total.']);
    }

    public function test_sale_page_passes_business_data_to_view(): void
    {
        $this->actingAs($this->seller)->get(route('pos.sale'))
            ->assertOk()
            ->assertViewHas('business', fn ($b) =>
                $b['name'] === 'Test Store'
                && $b['receipt_header'] === 'Gracias por su compra'
                && $b['receipt_footer'] === 'Vuelva pronto'
            )
            ->assertViewHas('currency', '$');
    }

    public function test_stock_movements_created_on_sale(): void
    {
        $coffee = Product::where('name', 'Coffee')->first();

        $response = $this->actingAs($this->seller)->postJson(route('pos.api.sales.store'), [
            'items'           => [['product_id' => $coffee->id, 'quantity' => 5, 'discount' => 0]],
            'payment_method'  => 'cash',
            'amount_tendered' => 30,
            'force_low_stock' => false,
        ])->assertCreated();

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $coffee->id,
            'user_id'    => $this->seller->id,
            'action'     => 'sale',
            'quantity'   => -5,
            'note'       => 'Venta #' . $response->json('sale.id'),
        ]);
    }

    public function test_cash_sale_increments_session_current_cash(): void
    {
        $coffee = Product::where('name', 'Coffee')->first();

        $this->actingAs($this->seller)->postJson(route('pos.api.sales.store'), [
            'items'           => [['product_id' => $coffee->id, 'quantity' => 2, 'discount' => 0]],
            'payment_method'  => 'cash',
            'amount_tendered' => 10,
            'force_low_stock' => false,
        ])->assertCreated();

        // opening 100 + sale total 7 = 107
        $this->assertEquals(107.0, (float) $this->session->fresh()->current_cash);
    }

    public function test_sale_without_active_session_returns_409(): void
    {
        $this->session->update(['status' => 'finished', 'finished_at' => now()]);

        $coffee = Product::where('name', 'Coffee')->first();

        $this->actingAs($this->seller)->postJson(route('pos.api.sales.store'), [
            'items'           => [['product_id' => $coffee->id, 'quantity' => 1, 'discount' => 0]],
            'payment_method'  => 'cash',
            'amount_tendered' => 10,
            'force_low_stock' => false,
        ])->assertStatus(409);
    }
}
