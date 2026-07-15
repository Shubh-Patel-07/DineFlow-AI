<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\Table;
use App\Models\MenuItem;
use App\Models\Inventory;

class DineFlowAIPOSSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Restaurant
        Restaurant::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Ultimate DineFlow AI POS House',
                'address' => '456 Gourmet Boulevard',
                'phone' => '555-0199',
                'logo' => '',
            ]
        );

        // Seed Tables
        Table::updateOrCreate(
            ['id' => 1],
            [
                'restaurant_id' => '1',
                'table_number' => 'VIP 1',
                'qr_code_url' => 'data:image/png;base64,iVBORw0KGgoAAAANS...',
                'status' => 'active'
            ]
        );
        Table::updateOrCreate(
            ['id' => 2],
            [
                'restaurant_id' => '1',
                'table_number' => 'Table 2',
                'qr_code_url' => 'data:image/png;base64,iVBORw0KGgoAAAANS...',
                'status' => 'active'
            ]
        );

        // Seed Menu Items
        MenuItem::updateOrCreate(
            ['id' => 1],
            [
                'restaurant_id' => '1',
                'name' => 'Ultimate Burger',
                'description' => 'Premium beef patty with cheddar cheese and onions',
                'price' => 12.00,
                'category' => 'Burgers',
                'is_available' => true,
                'is_veg' => false
            ]
        );
        MenuItem::updateOrCreate(
            ['id' => 2],
            [
                'restaurant_id' => '1',
                'name' => 'Cheesy Garlic Crust',
                'description' => 'Crispy garlic bread loaded with mozzarella',
                'price' => 6.00,
                'category' => 'Starters',
                'is_available' => true,
                'is_veg' => true
            ]
        );
        MenuItem::updateOrCreate(
            ['id' => 3],
            [
                'restaurant_id' => '1',
                'name' => 'Mocktail Breeze',
                'description' => 'Refreshing citrus lime mocktail with mint',
                'price' => 4.50,
                'category' => 'Drinks',
                'is_available' => true,
                'is_veg' => true
            ]
        );

        // Seed Inventory
        Inventory::updateOrCreate(
            ['id' => 1],
            [
                'restaurant_id' => '1',
                'name' => 'Ultimate Burger',
                'quantity' => 50,
                'unit' => 'units',
                'threshold' => 5
            ]
        );
        Inventory::updateOrCreate(
            ['id' => 2],
            [
                'restaurant_id' => '1',
                'name' => 'Cheesy Garlic Crust',
                'quantity' => 30,
                'unit' => 'units',
                'threshold' => 5
            ]
        );
        Inventory::updateOrCreate(
            ['id' => 3],
            [
                'restaurant_id' => '1',
                'name' => 'Mocktail Breeze',
                'quantity' => 100,
                'unit' => 'units',
                'threshold' => 10
            ]
        );
    }
}
