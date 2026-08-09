<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CashierManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_cashier_login_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.staff.store'), [
            'name' => 'Ama Mensah',
            'number' => '0240000000',
            'email' => 'ama@example.com',
            'password' => 'cashier123',
            'password_confirmation' => 'cashier123',
            'is_active' => '1',
        ])->assertRedirect(route('admin.staff.index'));

        $cashier = User::where('email', 'ama@example.com')->firstOrFail();

        $this->assertSame('cashier', $cashier->role);
        $this->assertTrue($cashier->is_active);
        $this->assertTrue(Hash::check('cashier123', $cashier->password));
        $this->assertDatabaseHas('pos_staff', [
            'user_id' => $cashier->id,
            'number' => '0240000000',
        ]);

        $this->actingAs($admin)->get(route('admin.staff.index'))
            ->assertOk()
            ->assertSee('Ama Mensah')
            ->assertSee('Active');
    }

    public function test_inactive_cashier_cannot_log_in(): void
    {
        User::factory()->create([
            'email' => 'disabled@example.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'is_active' => false,
        ]);

        $this->post(route('login.submit'), [
            'email' => 'disabled@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_dashboard_and_orders_show_cashier_performance(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cashier = User::factory()->create([
            'name' => 'Kojo Cashier',
            'email' => 'kojo@example.com',
            'role' => 'cashier',
        ]);

        DB::table('pos_staff')->insert([
            'user_id' => $cashier->id,
            'name' => $cashier->name,
            'number' => '0200000000',
            'email' => $cashier->email,
            'pincode' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $productId = DB::table('pos_products')->insertGetId([
            'code' => 'PRD-TEST',
            'name' => 'Test Product',
            'description' => '',
            'price' => 25,
            'stock' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $orderId = DB::table('pos_orders')->insertGetId([
            'code' => 'ORD-TEST',
            'customer_name' => 'Walk-in',
            'cashier_user_id' => $cashier->id,
            'grand_total' => 50,
            'status' => 'paid',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pos_order_items')->insert([
            'order_id' => $orderId,
            'product_id' => $productId,
            'qty' => 2,
            'price' => 25,
            'total' => 50,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pos_payments')->insert([
            'order_id' => $orderId,
            'method' => 'Mobile Money',
            'amount' => 50,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Cashier Performance')
            ->assertSee('Kojo Cashier')
            ->assertSee('50.00');

        $this->actingAs($admin)->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee('Kojo Cashier')
            ->assertSee('Mobile Money')
            ->assertSee('2 items');
    }
}
