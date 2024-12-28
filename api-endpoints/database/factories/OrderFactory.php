<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'total_amount' => $this->faker->numberBetween(1, 100),
            'order_status' => $this->faker->randomElement(['pending', 'completed', 'shipped', 'cancelled']),
            'payment_method' => $this->faker->randomElement(['Visa', 'MasterCard', 'Discover', 'UnionPay', 'Diners Club', 'JCB', 'AMEX']),
            'shipping_address' => $this->faker->address,
        ];
    }
}
