<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Feedback;
use App\Models\Inventory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ComprehensiveDemoSeeder extends Seeder
{
    /**
     * Run the comprehensive demo database seeds.
     */
    public function run(): void
    {
        // 1. Core Restaurant Profile
        $restaurant = Restaurant::updateOrCreate(
            ['id' => '1'],
            [
                'name' => 'DineFlow Gourmet Restaurant',
                'tagline' => 'Smart QR Table Ordering & Premium Dining',
                'description' => 'Fine dining restaurant offering modern Indian, Italian, and Continental fusion delicacies.',
                'address' => 'Station Road, Near University Circle, Patan, Gujarat - 384265',
                'phone' => '+91 98765 43210',
                'email' => 'contact@dineflow.ai',
                'gst_number' => '24AAACD1234F1Z5',
                'currency_symbol' => '₹',
                'currency_code' => 'INR',
                'tax_percentage' => 5.0,
                'service_charge_percentage' => 5.0,
                'is_active' => true,
            ]
        );

        // 2. Staff & Admin Accounts
        $users = [
            [
                'name' => 'Admin Manager',
                'email' => 'admin@dineflow.ai',
                'password' => Hash::make('admin123'),
                'pin' => '1234',
                'role' => 'admin',
                'restaurant_id' => '1',
            ],
            [
                'name' => 'Head Cashier',
                'email' => 'cashier@dineflow.ai',
                'password' => Hash::make('cashier123'),
                'pin' => '5678',
                'role' => 'cashier',
                'restaurant_id' => '1',
            ],
            [
                'name' => 'Executive Chef',
                'email' => 'kitchen@dineflow.ai',
                'password' => Hash::make('chef123'),
                'pin' => '9999',
                'role' => 'kitchen',
                'restaurant_id' => '1',
            ],
            [
                'name' => 'Senior Waiter',
                'email' => 'waiter@dineflow.ai',
                'password' => Hash::make('waiter123'),
                'pin' => '1111',
                'role' => 'waiter',
                'restaurant_id' => '1',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['email' => $userData['email']], $userData);
        }

        // 3. Dining Tables Setup (T-01 to T-12)
        $sections = ['Main Dining Hall', 'AC Family Section', 'Garden Terrace', 'VIP Lounge'];
        for ($i = 1; $i <= 12; $i++) {
            $section = $sections[($i - 1) % count($sections)];
            $capacity = ($i % 3 === 0) ? 6 : (($i % 2 === 0) ? 4 : 2);
            Table::updateOrCreate(
                ['id' => (string)$i, 'restaurant_id' => '1'],
                [
                    'table_number' => 'T-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'capacity' => $capacity,
                    'section' => $section,
                    'status' => ($i === 1 || $i === 3) ? 'occupied' : 'available',
                    'qr_code_url' => "/frontend/customer/menu.html?restaurantId=1&tableId={$i}",
                ]
            );
        }

        // 4. Coupons & Promo Deals
        $coupons = [
            ['code' => 'DINE10', 'discount_type' => 'percentage', 'discount_value' => 10, 'min_order_amount' => 300, 'max_discount' => 100, 'is_active' => true],
            ['code' => 'FEAST20', 'discount_type' => 'percentage', 'discount_value' => 20, 'min_order_amount' => 800, 'max_discount' => 250, 'is_active' => true],
            ['code' => 'FLAT50', 'discount_type' => 'fixed', 'discount_value' => 50, 'min_order_amount' => 400, 'max_discount' => 50, 'is_active' => true],
            ['code' => 'WELCOME100', 'discount_type' => 'fixed', 'discount_value' => 100, 'min_order_amount' => 1000, 'max_discount' => 100, 'is_active' => true],
        ];
        foreach ($coupons as $couponData) {
            Coupon::updateOrCreate(['code' => $couponData['code'], 'restaurant_id' => '1'], $couponData);
        }

        // 5. Raw Material & Inventory Tracking
        $inventoryItems = [
            ['item_name' => 'Amul Butter', 'category' => 'Dairy', 'current_stock' => 24.5, 'unit' => 'kg', 'min_threshold' => 5.0, 'unit_cost' => 520.00],
            ['item_name' => 'Paneer (Cottage Cheese)', 'category' => 'Dairy', 'current_stock' => 18.0, 'unit' => 'kg', 'min_threshold' => 4.0, 'unit_cost' => 360.00],
            ['item_name' => 'Basmati Rice (Daawat)', 'category' => 'Grains', 'current_stock' => 85.0, 'unit' => 'kg', 'min_threshold' => 20.0, 'unit_cost' => 110.00],
            ['item_name' => 'Refined Cooking Oil', 'category' => 'Oil', 'current_stock' => 45.0, 'unit' => 'Litre', 'min_threshold' => 10.0, 'unit_cost' => 140.00],
            ['item_name' => 'Fresh Tomatoes', 'category' => 'Vegetables', 'current_stock' => 32.0, 'unit' => 'kg', 'min_threshold' => 8.0, 'unit_cost' => 40.00],
            ['item_name' => 'Onions', 'category' => 'Vegetables', 'current_stock' => 50.0, 'unit' => 'kg', 'min_threshold' => 12.0, 'unit_cost' => 35.00],
            ['item_name' => 'Alphonso Mango Pulp', 'category' => 'Beverages', 'current_stock' => 15.0, 'unit' => 'Can', 'min_threshold' => 3.0, 'unit_cost' => 280.00],
            ['item_name' => 'Mozzarella Cheese Blend', 'category' => 'Dairy', 'current_stock' => 12.0, 'unit' => 'kg', 'min_threshold' => 3.5, 'unit_cost' => 480.00],
        ];
        foreach ($inventoryItems as $inv) {
            Inventory::updateOrCreate(['item_name' => $inv['item_name'], 'restaurant_id' => '1'], $inv);
        }

        // 6. Registered Customers & Loyalty Points
        $customers = [
            ['name' => 'Rahul Sharma', 'phone' => '9825012345', 'email' => 'rahul.s@example.com', 'loyalty_points' => 320, 'total_spent' => 4850.00, 'total_visits' => 7],
            ['name' => 'Pooja Patel', 'phone' => '9909054321', 'email' => 'pooja.patel@example.com', 'loyalty_points' => 580, 'total_spent' => 8200.00, 'total_visits' => 12],
            ['name' => 'Amit Verma', 'phone' => '9712398765', 'email' => 'amit.v@example.com', 'loyalty_points' => 150, 'total_spent' => 2100.00, 'total_visits' => 3],
            ['name' => 'Deepak Joshi', 'phone' => '9898011223', 'email' => 'deepak.j@example.com', 'loyalty_points' => 440, 'total_spent' => 6400.00, 'total_visits' => 9],
        ];
        foreach ($customers as $cust) {
            Customer::updateOrCreate(['phone' => $cust['phone'], 'restaurant_id' => '1'], $cust);
        }
    }
}
