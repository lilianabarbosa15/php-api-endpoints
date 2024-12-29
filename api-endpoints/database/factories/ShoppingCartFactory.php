<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShoppingCart>
 */
class ShoppingCartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $usersIds = User::all()->pluck("id")->toArray();
        return [
            'user_id' => $this->faker->randomElement($usersIds),
            'status' => $this->faker->randomElement(['pending', 'completed', 'shipped', 'cancelled']),
        ];
    }
}
