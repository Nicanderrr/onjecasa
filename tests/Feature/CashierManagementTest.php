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

        $this->post(route('otp.send'), [
            'email' => 'disabled@example.com',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_cashier_can_log_in_with_email_otp(): void
    {
        $cashier = User::factory()->create([
            'email' => 'otp-cashier@example.com',
            'role' => 'cashier',
            'is_active' => true,
        ]);

        $this->post(route('otp.send'), [
            'email' => $cashier->email,
        ])
            ->assertRedirect()
            ->assertSessionHas('show_user_otp_modal', true)
            ->assertSessionHas('pending_user_otp_user_id', $cashier->id);

        $otp = session('pending_user_otp');
        $this->assertNotEmpty($otp);

        $this->post(route('otp.verify'), [
            'otp' => $otp,
        ])->assertRedirect(route('cashier.pages.show', 'dashboard'));

        $this->assertAuthenticatedAs($cashier);
    }

    public function test_cashier_pages_render_for_cashier_users(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
            'is_active' => true,
        ]);

        foreach (['dashboard', 'products', 'orders', 'payments', 'receipts', 'orders-reports', 'payments-reports', 'settings'] as $page) {
            $this->actingAs($cashier)
                ->get(route('cashier.pages.show', $page))
                ->assertOk();
        }
    }

    public function test_cashier_can_start_and_end_shift(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
            'is_active' => true,
        ]);

        $this->actingAs($cashier)
            ->post(route('cashier.shifts.start'), ['opening_cash' => 25])
            ->assertRedirect();

        $this->assertDatabaseHas('pos_cashier_shifts', [
            'cashier_user_id' => $cashier->id,
            'opening_cash' => 25,
            'ended_at' => null,
        ]);

        $this->actingAs($cashier)
            ->get(route('cashier.pages.show', 'dashboard'))
            ->assertOk()
            ->assertSee('You are currently clocked in.');

        $this->actingAs($cashier)
            ->post(route('cashier.shifts.end'), [
                'closing_cash' => 40,
                'notes' => 'Balanced drawer',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('pos_cashier_shifts', [
            'cashier_user_id' => $cashier->id,
            'closing_cash' => 40,
            'notes' => 'Balanced drawer',
        ]);
    }

    public function test_cashier_must_start_shift_before_sale(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
            'is_active' => true,
        ]);

        $productId = DB::table('pos_products')->insertGetId([
            'code' => 'SHIFT-REQ',
            'name' => 'Shift Required Product',
            'description' => '',
            'price' => 12,
            'stock' => 10,
            'low_stock_threshold' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($cashier)
            ->post(route('cashier.sales.store'), [
                'customer_name' => 'Walk-in',
                'payment_method' => 'Cash',
                'items' => [
                    ['product_id' => $productId, 'qty' => 1],
                ],
            ])
            ->assertSessionHasErrors('shift');
    }

    public function test_admin_can_view_shift_history_with_sales_totals(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cashier = User::factory()->create([
            'name' => 'Shift Cashier',
            'role' => 'cashier',
            'is_active' => true,
        ]);

        $shiftId = DB::table('pos_cashier_shifts')->insertGetId([
            'cashier_user_id' => $cashier->id,
            'started_at' => now()->subHour(),
            'opening_cash' => 50,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pos_orders')->insert([
            'code' => 'SHIFT-ORDER',
            'customer_name' => 'Walk-in',
            'cashier_user_id' => $cashier->id,
            'grand_total' => 42,
            'status' => 'paid',
            'created_at' => now()->subMinutes(20),
            'updated_at' => now()->subMinutes(20),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.shifts.index'))
            ->assertOk()
            ->assertSee('Shift Cashier')
            ->assertSee('42.00')
            ->assertSee('Active');

        $this->assertDatabaseHas('pos_cashier_shifts', ['id' => $shiftId]);
    }

    public function test_cashier_dashboard_shows_low_stock_products_by_threshold(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
            'is_active' => true,
        ]);

        DB::table('pos_products')->insert([
            'code' => 'LOW-001',
            'name' => 'Low Product',
            'description' => '',
            'price' => 10,
            'stock' => 3,
            'low_stock_threshold' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pos_products')->insert([
            'code' => 'OK-001',
            'name' => 'Healthy Product',
            'description' => '',
            'price' => 10,
            'stock' => 6,
            'low_stock_threshold' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($cashier)
            ->get(route('cashier.pages.show', 'dashboard'))
            ->assertOk()
            ->assertSee('Low Product')
            ->assertDontSee('Healthy Product');
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
