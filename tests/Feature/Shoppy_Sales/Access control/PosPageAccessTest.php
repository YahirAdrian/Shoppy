<?php

namespace Tests\Feature\Shoppy_Adminer\AccessControl;

use App\Models\BusinessSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosPageAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        BusinessSetting::create([
            'business_name' => 'Test Store',
            'currency_symbol' => '$',
            'low_stock' => 5,
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

    private function admin(): User
    {
        return User::factory()->create([
            'name' => 'Admin One',
            'email' => 'admin@test.local',
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/pos/venta')->assertRedirect('/login');
        $this->get('/pos/buscar')->assertRedirect('/login');
        $this->get('/pos/estado')->assertRedirect('/login');
    }

    public function test_admin_cannot_access_pos_pages(): void
    {
        $this->actingAs($this->admin())
            ->get('/pos/venta')
            ->assertRedirect('/admin/dashboard');
    }

    public function test_seller_can_access_sale_page(): void
    {
        $this->actingAs($this->seller())
            ->get('/pos/venta')
            ->assertOk()
            ->assertSee('Venta');
    }

    public function test_seller_can_access_search_page(): void
    {
        $this->actingAs($this->seller())
            ->get('/pos/buscar')
            ->assertOk()
            ->assertSee('Buscar productos');
    }

    public function test_seller_can_access_status_page(): void
    {
        $this->actingAs($this->seller())
            ->get('/pos/estado')
            ->assertOk()
            ->assertSee('Estado de sesión');
    }

    public function test_pos_root_redirects_to_sale(): void
    {
        $this->actingAs($this->seller())
            ->get('/pos')
            ->assertRedirect('/pos/venta');
    }

    public function test_sidebar_renders_three_nav_links(): void
    {
        $response = $this->actingAs($this->seller())->get('/pos/venta');

        $response->assertOk();
        $response->assertSee('/pos/venta', false);
        $response->assertSee('/pos/buscar', false);
        $response->assertSee('/pos/estado', false);
    }

    public function test_seller_login_redirects_to_pos(): void
    {
        $this->seller();

        $this->post('/login', [
            'email' => 'seller@test.local',
            'password' => 'password',
        ])->assertRedirect('/pos');
    }
}
