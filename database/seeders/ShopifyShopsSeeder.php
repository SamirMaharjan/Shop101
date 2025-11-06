<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShopifyShopsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('shops')->insert([
            'id' => 6,
            'shop_domain' => 'v6wcpk-9u.myshopify.com',
            'access_token' => 'dumy-shpat_4096b9327b-dumy-74589184c3f652307a66d6',
            'shop_name' => 'My Store',
            'shop_email' => 'samirmaharjan.second@gmail.com',
            'shop_owner' => 'Samir Maharjan',
            'currency' => 'NPR',
            'timezone' => 'Asia/Kathmandu',
            'last_sync_at' => null,
            'created_at' => '2025-11-06 06:53:01',
            'updated_at' => '2025-11-06 06:53:01',
        ]);
    }
}
