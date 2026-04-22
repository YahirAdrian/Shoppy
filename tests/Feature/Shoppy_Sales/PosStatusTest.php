<?php

namespace Tests\Feature\Shoppy_Sales;

use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\PosSession;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PosStatusTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;
    private User $admin;
    private PosSession $session;
    private Category $category;
    private Product $coffee;

    protected function setUp(): void
    {
        parent::setUp();

        BusinessSetting::create([
            'business_name'   => 'Test Store',
            'currency_symbol' => '$',
            'low_stock'       => 5,
        ]);

        $this->admin = User::factory()->create([
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $this->seller = User::factory()->create([
            'role'      => 'seller',
            'is_active' => true,
        ]);

        $this->session = PosSession::create([
            'seller_id'    => $this->seller->id,
            'opening_cash' => 0.0,
            'current_cash' => 0.0,
            'status'       => 'active',
            'started_at'   => now(),
        ]);

        $this->category = Category::create(['name' => 'Bebidas', 'is_active' => true]);

        $this->coffee = Product::create([
            'category_id'   => $this->category->id,
            'name'          => 'Coffee',
            'sku'           => 'SKU-001',
            'cost_price'    => 1.5,
            'selling_price' => 3.5,
            'stock'         => 100,
            'unit'          => 'cup',
            'is_active'     => true,
        ]);
    }

    private function createSale(float $total, string $paymentMethod = 'cash'): Sale
    {
        $sale = Sale::create([
            'user_id'         => $this->seller->id,
            'pos_session_id'  => $this->session->id,
            'subtotal'        => $total,
            'discount_amount' => 0,
            'payment_method'  => $paymentMethod,
            'amount_tendered' => $total,
            'change_given'    => 0,
        ]);

        SaleItem::create([
            'sale_id'         => $sale->id,
            'product_id'      => $this->coffee->id,
            'product_name'    => $this->coffee->name,
            'unit_price'      => $total,
            'quantity'        => 1,
            'discount_amount' => 0,
            'subtotal'        => $total,
        ]);

        return $sale;
    }

    private function adminToken(): string
    {
        return Str::uuid()->toString();
    }

    // ── Status page stats ────────────────────────────────────────────────────

    public function test_status_page_stats_reflect_session_sales(): void
    {
        $this->createSale(10.0);
        $this->createSale(20.0);
        $this->createSale(30.0);

        $this->actingAs($this->seller)->get(route('pos.status'))
            ->assertOk()
            ->assertViewHas('totalSales', 3)
            ->assertViewHas('totalSold', 60.0)
            ->assertViewHas('avgTicket', 20.0);
    }

    public function test_status_page_with_no_session_shows_zero_stats(): void
    {
        $this->session->update(['status' => 'finished', 'finished_at' => now()]);

        $seller2 = User::factory()->create(['role' => 'seller', 'is_active' => true]);

        $this->actingAs($seller2)->get(route('pos.status'))
            ->assertOk()
            ->assertViewHas('totalSales', 0)
            ->assertViewHas('totalSold', 0.0)
            ->assertViewHas('avgTicket', 0.0);
    }

    public function test_stats_only_count_sales_from_current_session(): void
    {
        $seller2 = User::factory()->create(['role' => 'seller', 'is_active' => true]);
        $session2 = PosSession::create([
            'seller_id'    => $seller2->id,
            'opening_cash' => 0,
            'current_cash' => 0,
            'status'       => 'active',
            'started_at'   => now(),
        ]);

        // Seller 1: 2 sales
        $this->createSale(10.0);
        $this->createSale(20.0);

        // Seller 2: 1 sale in their own session
        Sale::create([
            'user_id'         => $seller2->id,
            'pos_session_id'  => $session2->id,
            'subtotal'        => 50.0,
            'discount_amount' => 0,
            'payment_method'  => 'cash',
            'amount_tendered' => 50.0,
            'change_given'    => 0,
        ]);

        $this->actingAs($this->seller)->get(route('pos.status'))
            ->assertOk()
            ->assertViewHas('totalSales', 2)
            ->assertViewHas('totalSold', 30.0);
    }

    // ── Admin auth ───────────────────────────────────────────────────────────

    public function test_admin_auth_valid_credentials_return_token(): void
    {
        $response = $this->actingAs($this->seller)
            ->postJson(route('pos.api.admin-auth'), [
                'email'    => $this->admin->email,
                'password' => 'password',
            ]);

        $response->assertOk();
        $response->assertJsonStructure(['token', 'expires_at']);
        $this->assertNotEmpty($response->json('token'));
    }

    public function test_admin_auth_wrong_password_returns_401(): void
    {
        $this->actingAs($this->seller)
            ->postJson(route('pos.api.admin-auth'), [
                'email'    => $this->admin->email,
                'password' => 'wrong-password',
            ])->assertStatus(401);
    }

    public function test_admin_auth_seller_credentials_return_401(): void
    {
        $this->actingAs($this->seller)
            ->postJson(route('pos.api.admin-auth'), [
                'email'    => $this->seller->email,
                'password' => 'password',
            ])->assertStatus(401);
    }

    // ── Delete sale ──────────────────────────────────────────────────────────

    public function test_delete_sale_without_token_returns_403(): void
    {
        $sale = $this->createSale(30.0);

        $this->actingAs($this->seller)
            ->deleteJson(route('pos.api.sales.destroy', $sale))
            ->assertStatus(403);
    }

    public function test_delete_sale_with_expired_token_returns_403(): void
    {
        $sale  = $this->createSale(30.0);
        $token = $this->adminToken();

        $this->actingAs($this->seller)
            ->withSession([
                'pos_admin_token'      => $token,
                'pos_admin_expires_at' => now()->subMinute(),
            ])
            ->deleteJson(route('pos.api.sales.destroy', $sale), [], ['X-Admin-Token' => $token])
            ->assertStatus(403);
    }

    public function test_delete_sale_restores_product_stock(): void
    {
        // Simulate stock already decremented by the sale
        $this->coffee->update(['stock' => 95]);

        $sale  = $this->createSale(3.5); // 1 unit of coffee in the SaleItem
        $token = $this->adminToken();

        $this->actingAs($this->seller)
            ->withSession([
                'pos_admin_token'      => $token,
                'pos_admin_expires_at' => now()->addMinutes(15),
            ])
            ->deleteJson(route('pos.api.sales.destroy', $sale), [], ['X-Admin-Token' => $token])
            ->assertOk();

        $this->assertEquals(96.0, (float) $this->coffee->fresh()->stock); // 95 + 1
    }

    public function test_delete_sale_creates_return_stock_movement(): void
    {
        $sale  = $this->createSale(3.5);
        $token = $this->adminToken();

        $this->actingAs($this->seller)
            ->withSession([
                'pos_admin_token'      => $token,
                'pos_admin_expires_at' => now()->addMinutes(15),
            ])
            ->deleteJson(route('pos.api.sales.destroy', $sale), [], ['X-Admin-Token' => $token])
            ->assertOk();

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->coffee->id,
            'action'     => 'return',
            'quantity'   => 1,
            'note'       => 'Venta anulada #' . $sale->id,
        ]);
    }

    public function test_delete_cash_sale_decrements_session_current_cash(): void
    {
        $this->session->update(['current_cash' => 200.0]);

        $sale  = $this->createSale(30.0, 'cash');
        $token = $this->adminToken();

        $this->actingAs($this->seller)
            ->withSession([
                'pos_admin_token'      => $token,
                'pos_admin_expires_at' => now()->addMinutes(15),
            ])
            ->deleteJson(route('pos.api.sales.destroy', $sale), [], ['X-Admin-Token' => $token])
            ->assertOk();

        $this->assertEquals(170.0, (float) $this->session->fresh()->current_cash);
    }

    public function test_seller_cannot_delete_another_sellers_sale(): void
    {
        $seller2 = User::factory()->create(['role' => 'seller', 'is_active' => true]);
        $session2 = PosSession::create([
            'seller_id'    => $seller2->id,
            'opening_cash' => 0,
            'current_cash' => 0,
            'status'       => 'active',
            'started_at'   => now(),
        ]);

        $sale = Sale::create([
            'user_id'         => $seller2->id,
            'pos_session_id'  => $session2->id,
            'subtotal'        => 20.0,
            'discount_amount' => 0,
            'payment_method'  => 'cash',
            'amount_tendered' => 20.0,
            'change_given'    => 0,
        ]);

        $token = $this->adminToken();

        // seller 1 tries to delete seller 2's sale
        $this->actingAs($this->seller)
            ->withSession([
                'pos_admin_token'      => $token,
                'pos_admin_expires_at' => now()->addMinutes(15),
            ])
            ->deleteJson(route('pos.api.sales.destroy', $sale), [], ['X-Admin-Token' => $token])
            ->assertStatus(403);
    }
}
