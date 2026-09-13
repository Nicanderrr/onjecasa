<?php

namespace Tests\Feature;

use App\Mail\LowStockAlertMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
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

    public function test_cashier_can_make_sale_without_clocking_in(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
            'is_active' => true,
        ]);

        $productId = DB::table('pos_products')->insertGetId([
            'code' => 'SALE-NOW',
            'name' => 'Immediate Sale Product',
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
                    ['product_id' => $productId, 'qty' => 2],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('pos_orders', [
            'customer_name' => 'Walk-in',
            'cashier_user_id' => $cashier->id,
            'grand_total' => 24,
            'status' => 'paid',
        ]);
        $this->assertDatabaseHas('pos_payments', [
            'method' => 'Cash',
            'amount' => 24,
        ]);
        $this->assertDatabaseHas('pos_products', [
            'id' => $productId,
            'stock' => 8,
        ]);
    }

    public function test_admin_gets_email_when_sale_creates_low_stock(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin',
            'is_active' => true,
        ]);
        $cashier = User::factory()->create([
            'role' => 'cashier',
            'is_active' => true,
        ]);

        $productId = DB::table('pos_products')->insertGetId([
            'code' => 'LOW-MAIL',
            'name' => 'Email Alert Product',
            'description' => '',
            'price' => 12,
            'stock' => 5,
            'low_stock_threshold' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($cashier)
            ->post(route('cashier.sales.store'), [
                'customer_name' => 'Walk-in',
                'payment_method' => 'Cash',
                'items' => [
                    ['product_id' => $productId, 'qty' => 2],
                ],
            ])
            ->assertRedirect();

        Mail::assertSent(LowStockAlertMail::class, fn (LowStockAlertMail $mail) => $mail->hasTo($admin->email));
        Mail::assertSentCount(1);

        $this->assertNotNull(DB::table('pos_products')->where('id', $productId)->value('low_stock_notified_at'));
    }

    public function test_cashier_sale_sends_whatsapp_receipt_when_number_is_supplied(): void
    {
        config([
            'services.whatsapp.access_token' => 'test-token',
            'services.whatsapp.phone_number_id' => '123456789',
            'services.whatsapp.api_version' => 'v20.0',
            'services.whatsapp.default_country_code' => '233',
        ]);

        Http::fake([
            'https://graph.facebook.com/v20.0/123456789/messages' => Http::response(['messages' => [['id' => 'wamid.test']]], 200),
        ]);

        $cashier = User::factory()->create([
            'role' => 'cashier',
            'is_active' => true,
        ]);

        $productId = DB::table('pos_products')->insertGetId([
            'code' => 'WA-001',
            'name' => 'WhatsApp Product',
            'description' => '',
            'price' => 15,
            'stock' => 10,
            'low_stock_threshold' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($cashier)
            ->post(route('cashier.sales.store'), [
                'customer_name' => 'Nana Buyer',
                'customer_whatsapp' => '0240000000',
                'payment_method' => 'Cash',
                'items' => [
                    ['product_id' => $productId, 'qty' => 2],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('pos_orders', [
            'customer_name' => 'Nana Buyer',
            'customer_whatsapp' => '0240000000',
            'grand_total' => 30,
        ]);

        Http::assertSent(function ($request) {
            $payload = $request->data();

            return $request->url() === 'https://graph.facebook.com/v20.0/123456789/messages'
                && $request->hasHeader('Authorization', 'Bearer test-token')
                && $payload['messaging_product'] === 'whatsapp'
                && $payload['to'] === '233240000000'
                && str_contains($payload['text']['body'], 'Receipt: ORD-')
                && str_contains($payload['text']['body'], 'WhatsApp Product x2 - 30.00');
        });
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
            ->assertSee('Low Stock Alerts')
            ->assertSee('Low Product')
            ->assertDontSee('Healthy Product')
            ->assertDontSee('Products at or below their alert threshold.');
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
