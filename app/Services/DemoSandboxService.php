<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\User;
use App\Models\Table;
use App\Models\MenuItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Expense;
use App\Models\Feedback;
use App\Models\WaiterCall;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DemoSandboxService
{
    /**
     * Create a completely isolated, temporary demo sandbox tenant
     */
    public function createSandbox(): array
    {
        $demoSessionId = 'demo_01_' . bin2hex(random_bytes(12));
        $ttlMinutes = (int) env('DEMO_TTL_MINUTES', 60);

        // 1. Create temporary Demo Restaurant Tenant
        $restaurant = Restaurant::create([
            'name' => 'Demo Sandbox Café',
            'address' => 'Virtual Sandbox Terminal',
            'phone' => '9999900000',
            'logo' => '/images/dineflow_logo.jpg',
            'subscription_plan' => 'pro',
            'subscription_status' => 'active',
            'onboarding_completed' => true,
            'is_demo' => true,
            'demo_session_id' => $demoSessionId,
            'expires_at' => Carbon::now()->addMinutes($ttlMinutes),
            'gst_rate' => 5.00,
            'service_charge' => 0.00,
        ]);

        // 2. Create isolated Demo User Account
        $username = 'demo_' . substr(bin2hex(random_bytes(4)), 0, 6);
        $user = User::create([
            'name' => 'Demo Administrator',
            'username' => $username,
            'email' => $username . '@sandbox.tendorabuy.in',
            'password' => Hash::make('demo1234'),
            'role' => 'admin',
            'numeric_pin' => '1234',
            'restaurant_id' => $restaurant->id,
        ]);

        // 3. Seed isolated sample Tables
        $tables = [
            ['table_number' => 'T-01', 'capacity' => 2, 'status' => 'available'],
            ['table_number' => 'T-02', 'capacity' => 4, 'status' => 'available'],
            ['table_number' => 'T-03', 'capacity' => 4, 'status' => 'available'],
            ['table_number' => 'T-04', 'capacity' => 6, 'status' => 'available'],
        ];
        foreach ($tables as $t) {
            Table::create([
                'restaurant_id' => $restaurant->id,
                'table_number' => $t['table_number'],
                'capacity' => $t['capacity'],
                'status' => $t['status'],
                'is_active' => true,
            ]);
        }

        // 4. Seed isolated sample Menu Items
        $menuItems = [
            [
                'name' => 'Paneer Tikka Platter',
                'category' => 'Starters',
                'price' => 260.00,
                'is_veg' => true,
                'is_available' => true,
                'description' => 'Char-grilled cottage cheese cubes with aromatic spices & mint chutney.',
            ],
            [
                'name' => 'Margherita Woodfire Pizza',
                'category' => 'Mains',
                'price' => 340.00,
                'is_veg' => true,
                'is_available' => true,
                'description' => 'Fresh buffalo mozzarella, basil leaves, and san marzano tomato sauce.',
            ],
            [
                'name' => 'Crispy Chicken Zinger Burger',
                'category' => 'Mains',
                'price' => 220.00,
                'is_veg' => false,
                'is_available' => true,
                'description' => 'Double-breaded golden crispy fillet with garlic mayo & cheese slice.',
            ],
            [
                'name' => 'Cold Brew Ice Cream Float',
                'category' => 'Beverages',
                'price' => 150.00,
                'is_veg' => true,
                'is_available' => true,
                'description' => 'Slow-dripped arabica coffee topped with rich vanilla bean gelato.',
            ],
            [
                'name' => 'Triple Belgian Chocolate Brownie',
                'category' => 'Desserts',
                'price' => 180.00,
                'is_veg' => true,
                'is_available' => true,
                'description' => 'Fudgy warm chocolate brownie drizzled with dark chocolate ganache.',
            ],
            [
                'name' => 'Peri Peri Crinkle Fries',
                'category' => 'Sides',
                'price' => 120.00,
                'is_veg' => true,
                'is_available' => true,
                'description' => 'Crisp potato wedges tossed in zesty African peri peri seasoning.',
            ],
        ];
        foreach ($menuItems as $m) {
            MenuItem::create([
                'restaurant_id' => $restaurant->id,
                'name' => $m['name'],
                'category' => $m['category'],
                'price' => $m['price'],
                'is_veg' => $m['is_veg'],
                'is_available' => $m['is_available'],
                'description' => $m['description'],
            ]);
        }

        // 5. Seed isolated sample Coupon
        Coupon::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Demo Welcome Discount (15% OFF)',
            'code' => 'DEMO15',
            'discount_type' => 'percentage',
            'discount_value' => 15.00,
            'min_order_amount' => 300.00,
            'max_discount' => 100.00,
            'is_active' => true,
            'expires_at' => Carbon::now()->addDays(30),
        ]);

        return [
            'restaurant' => $restaurant,
            'user' => $user,
            'demo_session_id' => $demoSessionId,
            'credentials' => [
                'username' => $username,
                'password' => 'demo1234',
                'pin' => '1234',
            ]
        ];
    }

    /**
     * Completely destroy all data of a single demo sandbox
     */
    public function destroySandbox(Restaurant $restaurant): void
    {
        if (!$restaurant->is_demo) {
            throw new \InvalidArgumentException("Cannot destroy non-demo production restaurant.");
        }

        $restaurantId = $restaurant->id;

        // Cascade delete all scoped demo records
        $orders = Order::where('restaurant_id', $restaurantId)->get();
        foreach ($orders as $o) {
            OrderItem::where('order_id', $o->id)->delete();
            $o->delete();
        }

        MenuItem::where('restaurant_id', $restaurantId)->delete();
        Table::where('restaurant_id', $restaurantId)->delete();
        Customer::where('restaurant_id', $restaurantId)->delete();
        Coupon::where('restaurant_id', $restaurantId)->delete();
        Inventory::where('restaurant_id', $restaurantId)->delete();
        Expense::where('restaurant_id', $restaurantId)->delete();
        Feedback::where('restaurant_id', $restaurantId)->delete();
        WaiterCall::where('restaurant_id', $restaurantId)->delete();
        User::where('restaurant_id', $restaurantId)->delete();

        $restaurant->delete();
    }

    /**
     * Reset an existing demo sandbox and re-seed it fresh
     */
    public function resetSandbox(Restaurant $restaurant): array
    {
        $this->destroySandbox($restaurant);
        return $this->createSandbox();
    }

    /**
     * Clean up all expired demo sandboxes from database
     */
    public function cleanupExpiredSandboxes(): int
    {
        $expiredRestaurants = Restaurant::where('is_demo', true)
            ->where(function ($q) {
                $q->where('expires_at', '<', Carbon::now())
                  ->orWhereNull('expires_at');
            })
            ->get();

        $count = 0;
        foreach ($expiredRestaurants as $r) {
            $this->destroySandbox($r);
            $count++;
        }

        return $count;
    }
}
