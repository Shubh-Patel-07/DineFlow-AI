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

        // Seed 12 Tables (T-01 to T-12)
        for ($i = 1; $i <= 12; $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            Table::updateOrCreate(
                ['id' => $i],
                [
                    'restaurant_id' => '1',
                    'table_number' => 'T-' . $num,
                    'qr_code_url' => 'http://localhost:8000/menu?restaurantId=1&tableId=' . $i,
                    'status' => 'active'
                ]
            );
        }

        // Seed Menu Items (Matching POS frontend MENU array exactly)
        $menuItems = [
            ['id' => 1,  'name' => 'Paneer Butter Masala', 'cat' => 'Mains',     'price' => 240.00, 'veg' => true,  'image' => 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?w=500&auto=format&fit=crop&q=60'],
            ['id' => 2,  'name' => 'Chicken Tikka Masala',  'cat' => 'Mains',     'price' => 320.00, 'veg' => false, 'image' => 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=500&auto=format&fit=crop&q=60'],
            ['id' => 3,  'name' => 'Garlic Naan',           'cat' => 'Breads',    'price' => 60.00,  'veg' => true,  'image' => 'https://images.unsplash.com/photo-1585238342024-78d387f4a707?w=500&auto=format&fit=crop&q=60'],
            ['id' => 4,  'name' => 'Veg Spring Rolls',      'cat' => 'Starters',  'price' => 180.00, 'veg' => true,  'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=500&auto=format&fit=crop&q=60'],
            ['id' => 5,  'name' => 'Chicken Biryani',       'cat' => 'Rice',      'price' => 380.00, 'veg' => false, 'image' => 'https://images.unsplash.com/photo-1633945274405-b6c8069047b0?w=500&auto=format&fit=crop&q=60'],
            ['id' => 6,  'name' => 'Mango Lassi',           'cat' => 'Beverages', 'price' => 120.00, 'veg' => true,  'image' => 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=500&auto=format&fit=crop&q=60'],
            ['id' => 7,  'name' => 'Gulab Jamun',           'cat' => 'Desserts',  'price' => 80.00,  'veg' => true,  'image' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=500&auto=format&fit=crop&q=60'],
            ['id' => 8,  'name' => 'Dal Tadka',             'cat' => 'Mains',     'price' => 180.00, 'veg' => true,  'image' => 'https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=500&auto=format&fit=crop&q=60'],
            ['id' => 9,  'name' => 'Fish Fry',              'cat' => 'Starters',  'price' => 280.00, 'veg' => false, 'image' => 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=500&auto=format&fit=crop&q=60'],
            ['id' => 10, 'name' => 'Laccha Paratha',        'cat' => 'Breads',    'price' => 70.00,  'veg' => true,  'image' => 'https://images.unsplash.com/photo-1627834377411-8da5f4f09de8?w=500&auto=format&fit=crop&q=60'],
            ['id' => 11, 'name' => 'Veg Biryani',           'cat' => 'Rice',      'price' => 280.00, 'veg' => true,  'image' => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=500&auto=format&fit=crop&q=60'],
            ['id' => 12, 'name' => 'Cold Coffee',           'cat' => 'Beverages', 'price' => 140.00, 'veg' => true,  'image' => 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=500&auto=format&fit=crop&q=60'],
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
                    'is_veg' => $item['veg'],
                    'image' => $item['image']
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
