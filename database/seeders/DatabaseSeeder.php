<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'superadmin@mail.com',
        ], [
            'name' => 'Super Admin',
            'role' => 'superadmin',
            'password' => Hash::make('superadmin123'),
            'pincode_hash' => Hash::make('2222'),
            'is_active' => true,
        ]);

        User::updateOrCreate([
            'email' => 'admin@mail.com',
        ], [
            'name' => 'System Admin',
            'role' => 'admin',
            'password' => Hash::make('Enter2net'),
            'pincode_hash' => Hash::make('2222'),
        ]);

        User::updateOrCreate([
            'email' => 'cashier@mail.com',
        ], [
            'name' => 'Cashier James',
            'role' => 'cashier',
            'password' => Hash::make('cashier123'),
            'pincode_hash' => Hash::make('2222'),
        ]);

        if (DB::table('pos_products')->count() === 0) {
            DB::table('pos_products')->insert([
                ['code' => 'PRD-1001', 'name' => 'Vacumn Bottle', 'description' => 'Bottle', 'price' => 60, 'stock' => 33, 'created_at' => now(), 'updated_at' => now()],
                ['code' => 'PRD-1002', 'name' => 'Original Shirt', 'description' => 'Shirt', 'price' => 120, 'stock' => 2323, 'created_at' => now(), 'updated_at' => now()],
                ['code' => 'PRD-1003', 'name' => 'Pepsi', 'description' => 'Drink', 'price' => 20, 'stock' => 78, 'created_at' => now(), 'updated_at' => now()],
                ['code' => 'PRD-1004', 'name' => 'Series 7', 'description' => 'Watch', 'price' => 2500, 'stock' => 565, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}
