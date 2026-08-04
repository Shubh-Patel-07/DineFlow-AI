<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'restaurant_id' => '1',
            'name' => fake()->name(),
            'phone' => fake()->numerify('98########'),
            'email' => fake()->safeEmail(),
            'loyalty_points' => fake()->numberBetween(0, 500),
            'total_spent' => fake()->randomFloat(2, 500, 15000),
            'total_visits' => fake()->numberBetween(1, 20),
            'membership_tier' => fake()->randomElement(['Bronze', 'Silver', 'Gold', 'Platinum']),
            'last_visit_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
