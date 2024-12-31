<?php

namespace Database\Seeders;

use App\Models\ShoppingCart;
use Illuminate\Database\Seeder;

class ShoppingCartsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ShoppingCart::factory(200)->create();
    }
}
