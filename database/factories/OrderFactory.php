<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 200, 2500);
        $tax = round($subtotal * 0.05, 2);
        $serviceCharge = round($subtotal * 0.05, 2);
        $total = $subtotal + $tax + $serviceCharge;

        return [
            'restaurant_id' => '1',
            'table_id' => (string)fake()->numberBetween(1, 12),
            'order_type' => fake()->randomElement(['dine_in', 'takeaway']),
            'status' => fake()->randomElement(['pending', 'preparing', 'ready', 'served', 'completed']),
            'payment_status' => fake()->randomElement(['unpaid', 'paid']),
            'payment_method' => fake()->randomElement(['cash', 'upi', 'card']),
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'service_charge' => $serviceCharge,
            'total_amount' => $total,
            'special_instructions' => fake()->optional()->sentence(),
            'created_at' => fake()->dateTimeBetween('-15 days', 'now'),
        ];
    }
}
