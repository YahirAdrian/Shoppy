<?php

namespace Tests\Feature\Shoppy_Sales;

use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\PosSession;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SellerSessionTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;
    private User $admin;

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

        $this->admin = User::factory()->create([
            'role'      => 'admin',
            'is_active' => true,
        ]);
    }

    private function createActiveSession(float $cash = 100.0): PosSession
    {
        return PosSession::create([
            'seller_id'    => $this->seller->id,
            'opening_cash' => $cash,
            'current_cash' => $cash,
            'status'       => 'active',
            'started_at'   => now(),
        ]);
    }

    private function adminToken(): string
    {
        return Str::uuid()->toString();
    }

    // ── Start session ────────────────────────────────────────────────────────

    public function test_seller_can_start_new_session(): void
    {
        $response = $this->actingAs($this->seller)
            ->postJson(route('pos.api.sessions.store'), ['opening_cash' => 100]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['session' => ['id', 'opening_cash', 'current_cash', 'status', 'started_at']]);

        $session = $response->json('session');
        $this->assertEquals(100.0, $session['opening_cash']);
        $this->assertEquals(100.0, $session['current_cash']);
        $this->assertEquals('active', $session['status']);

        $this->assertDatabaseHas('pos_sessions', [
            'seller_id' => $this->seller->id,
            'status'    => 'active',
        ]);
    }

    public function test_starting_session_when_one_already_exists_returns_409(): void
    {
        $this->createActiveSession();

        $this->actingAs($this->seller)
            ->postJson(route('pos.api.sessions.store'), ['opening_cash' => 50])
            ->assertStatus(409);
    }

    public function test_opening_cash_cannot_be_negative(): void
    {
        $this->actingAs($this->seller)
            ->postJson(route('pos.api.sessions.store'), ['opening_cash' => -10])
            ->assertStatus(422);
    }

    // ── Current session ──────────────────────────────────────────────────────

    public function test_current_session_returns_active_session_data(): void
    {
        $this->createActiveSession(200.0);

        $response = $this->actingAs($this->seller)
            ->getJson(route('pos.api.sessions.current'));

        $response->assertOk();
        $response->assertJsonPath('session.status', 'active');
        $this->assertEquals(200.0, $response->json('session.opening_cash'));
    }

    public function test_current_session_returns_404_when_no_session_exists(): void
    {
        $this->actingAs($this->seller)
            ->getJson(route('pos.api.sessions.current'))
            ->assertStatus(404);
    }

    // ── Withdraw ─────────────────────────────────────────────────────────────

    public function test_withdraw_decrements_session_current_cash(): void
    {
        $session = $this->createActiveSession(100.0);

        $response = $this->actingAs($this->seller)
            ->patchJson(route('pos.api.sessions.withdraw'), ['amount' => 40]);

        $response->assertOk();
        $this->assertEquals(60.0, $response->json('session.current_cash'));
        $this->assertEquals(60.0, (float) $session->fresh()->current_cash);
    }

    public function test_withdraw_with_zero_amount_returns_422(): void
    {
        $this->createActiveSession();

        $this->actingAs($this->seller)
            ->patchJson(route('pos.api.sessions.withdraw'), ['amount' => 0])
            ->assertStatus(422);
    }

    public function test_withdraw_without_active_session_returns_404(): void
    {
        $this->actingAs($this->seller)
            ->patchJson(route('pos.api.sessions.withdraw'), ['amount' => 10])
            ->assertStatus(404);
    }

    // ── End session ──────────────────────────────────────────────────────────

    public function test_end_session_without_admin_token_returns_403(): void
    {
        $this->createActiveSession(0.0);

        $this->actingAs($this->seller)
            ->patchJson(route('pos.api.sessions.end'))
            ->assertStatus(403);
    }

    public function test_end_session_with_cash_remaining_returns_422(): void
    {
        $this->createActiveSession(50.0);
        $token = $this->adminToken();

        $this->actingAs($this->seller)
            ->withSession([
                'pos_admin_token'      => $token,
                'pos_admin_expires_at' => now()->addMinutes(15),
            ])
            ->patchJson(route('pos.api.sessions.end'), [], ['X-Admin-Token' => $token])
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'Debe retirar todo el efectivo antes de terminar la sesión.']);
    }

    public function test_end_session_when_cash_is_zero_marks_it_finished(): void
    {
        $session = $this->createActiveSession(0.0);
        $token   = $this->adminToken();

        $this->actingAs($this->seller)
            ->withSession([
                'pos_admin_token'      => $token,
                'pos_admin_expires_at' => now()->addMinutes(15),
            ])
            ->patchJson(route('pos.api.sessions.end'), [], ['X-Admin-Token' => $token])
            ->assertOk();

        $session->refresh();
        $this->assertEquals('finished', $session->status);
        $this->assertNotNull($session->finished_at);
    }

    public function test_end_session_with_expired_token_returns_403(): void
    {
        $this->createActiveSession(0.0);
        $token = $this->adminToken();

        $this->actingAs($this->seller)
            ->withSession([
                'pos_admin_token'      => $token,
                'pos_admin_expires_at' => now()->subMinute(),
            ])
            ->patchJson(route('pos.api.sessions.end'), [], ['X-Admin-Token' => $token])
            ->assertStatus(403);
    }

    // ── Login redirect ───────────────────────────────────────────────────────

    public function test_login_redirects_to_start_session_when_no_active_session(): void
    {
        $seller = User::factory()->create([
            'email'     => 'seller.login@test.local',
            'role'      => 'seller',
            'is_active' => true,
        ]);

        $this->post('/login', ['email' => $seller->email, 'password' => 'password'])
            ->assertRedirect(route('pos.session.start'));
    }

    public function test_login_redirects_to_sale_page_when_session_exists(): void
    {
        $seller = User::factory()->create([
            'email'     => 'seller.active@test.local',
            'role'      => 'seller',
            'is_active' => true,
        ]);

        PosSession::create([
            'seller_id'    => $seller->id,
            'opening_cash' => 50.0,
            'current_cash' => 50.0,
            'status'       => 'active',
            'started_at'   => now(),
        ]);

        $this->post('/login', ['email' => $seller->email, 'password' => 'password'])
            ->assertRedirect('/pos/venta');
    }
}
