<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrentProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'code' => '6034000181142',
                'name' => 'Bel Aqua',
                'description' => '',
                'price' => 4.00,
                'stock' => 17,
                'image' => '42fb0e30-0400-440f-abb9-8260b8c23a28.png',
                'low_stock_threshold' => 5,
            ],
            [
                'code' => '90418723',
                'name' => 'Fanta Lemon',
                'description' => '',
                'price' => 7.00,
                'stock' => 23,
                'image' => '0e0a30b3-fd51-4aaa-a4e5-a64137de0de1.png',
                'low_stock_threshold' => 5,
            ],
            [
                'code' => '8851028000631',
                'name' => 'Vitamilk',
                'description' => '',
                'price' => 15.00,
                'stock' => 20,
                'image' => 'a78910e9-0a43-4a8a-a9e1-00d3b49f449f.png',
                'low_stock_threshold' => 5,
            ],
            [
                'code' => '90338052',
                'name' => 'Coke',
                'description' => '',
                'price' => 7.00,
                'stock' => 20,
                'image' => '89806c22-6ed1-409d-b7f4-10fd45a4df10.webp',
                'low_stock_threshold' => 5,
            ],
            [
                'code' => '6033000550859',
                'name' => 'Bel Aqua 1L',
                'description' => '',
                'price' => 5.00,
                'stock' => 20,
                'image' => 'ca4b654b-7610-46e7-bc3e-52c5945ed483.png',
                'low_stock_threshold' => 5,
            ],
            [
                'code' => '5031413917932',
                'name' => 'Pretty Fresh Mouth Spray',
                'description' => '',
                'price' => 30.00,
                'stock' => 20,
                'image' => 'bd280318-bb3a-4725-8229-f9c2b03a993a.webp',
                'low_stock_threshold' => 5,
            ],
            [
                'code' => '6034000351170',
                'name' => 'Bel Active',
                'description' => '',
                'price' => 7.00,
                'stock' => 20,
                'image' => '9004a8a8-ac2b-46e3-b897-e32d02760517.png',
                'low_stock_threshold' => 5,
            ],
            [
                'code' => '964458662343',
                'name' => 'Staples',
                'description' => '',
                'price' => 6.00,
                'stock' => 39,
                'image' => null,
                'low_stock_threshold' => 5,
            ],
        ];

        foreach ($products as $product) {
            DB::table('pos_products')->updateOrInsert(
                ['code' => $product['code']],
                array_merge($product, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}
