<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'name' => $this->faker->sentence(4, true),
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 10, 700),
            'other_attributes' => json_encode([                
                'material' => $this->faker->randomElement(['cotton', 'polyester', 'wool']),
                'pattern' => $this->faker->randomElement(['solid', 'striped', 'plaid']),
                'brand' => $this->faker->randomElement(['CHANEL', 'PRADA', 'GUCCI','VERSACE','LOUIS VUITTON']),
                'care_instructions' => $this->faker->sentence,
                'collection' => $this->faker->randomElement(['summer', 'winter', 'autumn']),
                'gender' => $this->faker->randomElement(['unisex', 'woman', 'men']),
            ]),
            
        ];
    }
}
