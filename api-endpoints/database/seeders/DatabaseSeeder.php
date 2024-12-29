<?php

namespace Database\Seeders;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShoppingCart;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Product::factory(20)->create();

        ProductVariant::factory(200)->create();

        User::factory(15)->create();

        Order::factory(200)->create();

        ShoppingCart::factory(200)->create();

        OrderItem::factory(300)->create();
        
        CartItem::factory(300)->create();


    }
}
