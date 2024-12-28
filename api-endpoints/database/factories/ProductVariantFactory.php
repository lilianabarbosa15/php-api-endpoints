<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        
        $productsIds = Product::all()->pluck("id")->toArray();
        return [
            'product_id'=> $this->faker->randomElement($productsIds),
            'color' => $this->faker->hexColor,  // Fake a hex color (e.g. #ff5733)
            'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL']),
            'stock_quantity' => $this->faker->numberBetween(0, 500),
        ];
    }
}
