<?php

namespace Database\Seeders;


use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory(15)->create();

        $this->call([
            ProductsTableSeeder::class,
            ProductVariantsTableSeeder::class,
            OrdersTableSeeder::class,
            ShoppingCartsTableSeeder::class,
            OrderItemsTableSeeder::class,
            CartItemsTableSeeder::class,
        ]);


    }
}
