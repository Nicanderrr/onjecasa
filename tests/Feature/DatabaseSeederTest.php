<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CurrentProductSeeder;
use Database\Seeders\DefaultUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_user_seeder_creates_admin_accounts(): void
    {
        $this->seed(DefaultUserSeeder::class);

        $superadmin = User::where('email', 'superadmin@mail.com')->firstOrFail();
        $admin = User::where('email', 'admin@mail.com')->firstOrFail();

        $this->assertSame('superadmin', $superadmin->role);
        $this->assertSame('admin', $admin->role);
        $this->assertTrue($superadmin->is_active);
        $this->assertTrue($admin->is_active);
        $this->assertTrue(Hash::check('password', $superadmin->password));
        $this->assertTrue(Hash::check('password', $admin->password));
        $this->assertNull($superadmin->pincode_hash);
        $this->assertNull($admin->pincode_hash);
    }

    public function test_current_product_seeder_creates_current_catalog(): void
    {
        $this->seed(CurrentProductSeeder::class);

        $this->assertSame(8, DB::table('pos_products')->count());
        $this->assertDatabaseHas('pos_products', [
            'code' => '6034000181142',
            'name' => 'Bel Aqua',
            'price' => 4.00,
            'stock' => 17,
            'image' => '42fb0e30-0400-440f-abb9-8260b8c23a28.png',
            'low_stock_threshold' => 5,
        ]);
        $this->assertDatabaseHas('pos_products', [
            'code' => '964458662343',
            'name' => 'Staples',
            'price' => 6.00,
            'stock' => 39,
            'image' => null,
            'low_stock_threshold' => 5,
        ]);
    }
}
