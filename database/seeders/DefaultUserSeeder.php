<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@mail.com'],
            [
                'name' => 'Super Admin',
                'role' => 'superadmin',
                'password' => Hash::make('password'),
                'pincode_hash' => null,
                'is_active' => true,
                'login_bypass_enabled' => false,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'System Admin',
                'role' => 'admin',
                'password' => Hash::make('password'),
                'pincode_hash' => null,
                'is_active' => true,
                'login_bypass_enabled' => false,
            ]
        );
    }
}
