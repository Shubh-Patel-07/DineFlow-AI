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

        // Seed Menu Items (Matching POS frontend MENU array exactly)
        $menuItems = [
            ['id' => 1,  'name' => 'Paneer Butter Masala', 'cat' => 'Mains',     'price' => 240.00, 'veg' => true],
            ['id' => 2,  'name' => 'Chicken Tikka Masala',  'cat' => 'Mains',     'price' => 320.00, 'veg' => false],
            ['id' => 3,  'name' => 'Garlic Naan',           'cat' => 'Breads',    'price' => 60.00,  'veg' => true],
            ['id' => 4,  'name' => 'Veg Spring Rolls',      'cat' => 'Starters',  'price' => 180.00, 'veg' => true],
            ['id' => 5,  'name' => 'Chicken Biryani',       'cat' => 'Rice',      'price' => 380.00, 'veg' => false],
            ['id' => 6,  'name' => 'Mango Lassi',           'cat' => 'Beverages', 'price' => 120.00, 'veg' => true],
            ['id' => 7,  'name' => 'Gulab Jamun',           'cat' => 'Desserts',  'price' => 80.00,  'veg' => true],
            ['id' => 8,  'name' => 'Dal Tadka',             'cat' => 'Mains',     'price' => 180.00, 'veg' => true],
            ['id' => 9,  'name' => 'Fish Fry',              'cat' => 'Starters',  'price' => 280.00, 'veg' => false],
            ['id' => 10, 'name' => 'Laccha Paratha',        'cat' => 'Breads',    'price' => 70.00,  'veg' => true],
            ['id' => 11, 'name' => 'Veg Biryani',           'cat' => 'Rice',      'price' => 280.00, 'veg' => true],
            ['id' => 12, 'name' => 'Cold Coffee',           'cat' => 'Beverages', 'price' => 140.00, 'veg' => true],
        ];

        foreach ($menuItems as $item) {
            MenuItem::updateOrCreate(
                ['id' => $item['id']],
                [
                    'restaurant_id' => '1',
                    'name' => $item['name'],
                    'description' => $item['name'] . ' served fresh.',
                    'price' => $item['price'],
                    'category' => $item['cat'],
                    'is_available' => true,
                    'is_veg' => $item['veg']
                ]
            );

            // Seed matching Inventory item with quantity 50
            Inventory::updateOrCreate(
                ['id' => $item['id']],
                [
                    'restaurant_id' => '1',
                    'name' => $item['name'],
                    'quantity' => 50,
                    'unit' => 'servings',
                    'threshold' => 5
                ]
            );
        }
    }
}
