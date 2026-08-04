<?php

namespace Database\Factories;

use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MenuItem>
 */
class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Mains', 'Starters', 'Breads', 'Rice', 'Drinks', 'Desserts', 'Pizza', 'Burger'];
        $category = fake()->randomElement($categories);

        return [
            'restaurant_id' => '1',
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(10),
            'price' => fake()->randomFloat(2, 50, 600),
            'category' => $category,
            'image' => fake()->imageUrl(640, 480, 'food', true),
            'is_available' => fake()->boolean(90),
            'is_veg' => fake()->boolean(70),
        ];
    }

    /**
     * Indicate that the item is vegetarian.
     */
    public function vegetarian(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_veg' => true,
        ]);
    }

    /**
     * Indicate that the item is non-vegetarian.
     */
    public function nonVegetarian(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_veg' => false,
        ]);
    }

    /**
     * Indicate that the item is in stock.
     */
    public function inStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => true,
        ]);
    }

    /**
     * Indicate that the item is out of stock / sold out.
     */
    public function soldOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
        ]);
    }
}
