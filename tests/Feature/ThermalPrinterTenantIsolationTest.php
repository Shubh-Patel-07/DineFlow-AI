<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Table;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;

class ThermalPrinterTenantIsolationTest extends TestCase
{
    protected Restaurant $asopalav;
    protected Restaurant $restaurantA;
    protected User $asopalavUser;
    protected User $restAUser;
    protected Order $asopalavOrder;
    protected Order $orderA;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Asopalav
        $this->asopalav = Restaurant::firstOrCreate(
            ['name' => 'Asopalav'],
            [
                'address' => 'Gopi Avenue, Near Lili Vadi, Patan-Chansama Highway, Patan.',
                'phone' => '9558106440',
                'email' => 'asopalav@dineflow.in',
                'gstin' => '24ASOPL9876F1Z2',
                'is_demo' => false,
                'currency' => '₹',
                'tax_rate' => 5.0,
                'signature_name' => 'Pravinbhai Patel (Owner)'
            ]
        );
        $this->asopalav->update([
            'address' => 'Gopi Avenue, Near Lili Vadi, Patan-Chansama Highway, Patan.',
            'phone' => '9558106440',
            'gstin' => '24ASOPL9876F1Z2',
            'is_demo' => false,
            'signature_name' => 'Pravinbhai Patel (Owner)'
        ]);

        $this->asopalavUser = User::firstOrCreate(
            ['email' => 'asopalav@dineflow.in'],
            [
                'name' => 'Asopalav Restaurant Admin',
                'username' => 'asopalav_admin',
                'password' => bcrypt('Asopalav@2026'),
                'role' => 'admin',
                'restaurant_id' => $this->asopalav->id
            ]
        );
        $this->asopalavUser->update(['restaurant_id' => $this->asopalav->id, 'role' => 'admin']);

        $asopalavTable = Table::firstOrCreate(
            ['restaurant_id' => $this->asopalav->id, 'table_number' => 'T-05'],
            ['capacity' => 4, 'status' => 'available']
        );

        $asopalavItem1 = MenuItem::firstOrCreate(
            ['restaurant_id' => $this->asopalav->id, 'name' => 'Paneer Tikka Dry'],
            ['category' => 'STARTER', 'price' => 220, 'is_available' => true]
        );
        $asopalavItem2 = MenuItem::firstOrCreate(
            ['restaurant_id' => $this->asopalav->id, 'name' => 'Butter Roti'],
            ['category' => 'ROTI', 'price' => 30, 'is_available' => true]
        );
        $asopalavItem3 = MenuItem::firstOrCreate(
            ['restaurant_id' => $this->asopalav->id, 'name' => 'Cold Drinks'],
            ['category' => 'COLD DRINKS', 'price' => 40, 'is_available' => true]
        );

        $this->asopalavOrder = Order::create([
            'restaurant_id' => $this->asopalav->id,
            'table_id' => $asopalavTable->id,
            'total_amount' => 320,
            'status' => 'completed',
            'payment_status' => 'paid',
            'payment_method' => 'upi',
            'special_instructions' => 'Extra spicy and crispy'
        ]);

        OrderItem::create(['order_id' => $this->asopalavOrder->id, 'menu_item_id' => $asopalavItem1->id, 'name' => $asopalavItem1->name, 'price' => 220, 'quantity' => 1]);
        OrderItem::create(['order_id' => $this->asopalavOrder->id, 'menu_item_id' => $asopalavItem2->id, 'name' => $asopalavItem2->name, 'price' => 30, 'quantity' => 2]);
        OrderItem::create(['order_id' => $this->asopalavOrder->id, 'menu_item_id' => $asopalavItem3->id, 'name' => $asopalavItem3->name, 'price' => 40, 'quantity' => 1]);


        // 2. Restaurant A
        $this->restaurantA = Restaurant::firstOrCreate(
            ['name' => 'Royal Spice Bistro'],
            [
                'address' => 'Shop 4-6, Galaxy Plaza, Ahmedabad-Gandhinagar Highway, Ahmedabad.',
                'phone' => '9876500001',
                'email' => 'admin@royalspice.in',
                'gstin' => '24ROYAL1234A1Z1',
                'is_demo' => false,
                'currency' => '₹',
                'tax_rate' => 5.0,
                'signature_name' => 'Chef Vikram Patel (Owner)'
            ]
        );
        $this->restaurantA->update([
            'address' => 'Shop 4-6, Galaxy Plaza, Ahmedabad-Gandhinagar Highway, Ahmedabad.',
            'phone' => '9876500001',
            'gstin' => '24ROYAL1234A1Z1',
            'is_demo' => false,
            'signature_name' => 'Chef Vikram Patel (Owner)'
        ]);

        $this->restAUser = User::firstOrCreate(
            ['email' => 'royalspice@dineflow.in'],
            [
                'name' => 'Royal Spice Admin',
                'username' => 'royalspice_admin',
                'password' => bcrypt('RoyalSpice@2026'),
                'role' => 'admin',
                'restaurant_id' => $this->restaurantA->id
            ]
        );
        $this->restAUser->update(['restaurant_id' => $this->restaurantA->id, 'role' => 'admin']);

        $tableA = Table::firstOrCreate(
            ['restaurant_id' => $this->restaurantA->id, 'table_number' => 'T-01'],
            ['capacity' => 2, 'status' => 'available']
        );

        $itemA1 = MenuItem::firstOrCreate(
            ['restaurant_id' => $this->restaurantA->id, 'name' => 'Royal Veg Spring Roll'],
            ['category' => 'STARTERS', 'price' => 160, 'is_available' => true]
        );
        $itemA2 = MenuItem::firstOrCreate(
            ['restaurant_id' => $this->restaurantA->id, 'name' => 'Mango Lassi Special'],
            ['category' => 'BEVERAGES', 'price' => 95, 'is_available' => true]
        );

        $this->orderA = Order::create([
            'restaurant_id' => $this->restaurantA->id,
            'table_id' => $tableA->id,
            'total_amount' => 255,
            'status' => 'completed',
            'payment_status' => 'paid',
            'payment_method' => 'card',
            'special_instructions' => 'No sugar'
        ]);

        OrderItem::create(['order_id' => $this->orderA->id, 'menu_item_id' => $itemA1->id, 'name' => $itemA1->name, 'price' => 160, 'quantity' => 1]);
        OrderItem::create(['order_id' => $this->orderA->id, 'menu_item_id' => $itemA2->id, 'name' => $itemA2->name, 'price' => 95, 'quantity' => 1]);
    }

    /** @test */
    public function asopalav_thermal_receipt_renders_only_asopalav_details(): void
    {
        $response = $this->actingAs($this->asopalavUser)
            ->get("/orders/{$this->asopalavOrder->id}/receipt");

        $response->assertStatus(200);
        $html = $response->getContent();

        $this->assertStringContainsString('Asopalav', $html);
        $this->assertStringContainsString('Gopi Avenue, Near Lili Vadi', $html);
        $this->assertStringContainsString('9558106440', $html);
        $this->assertStringContainsString('24ASOPL9876F1Z2', $html);
        $this->assertStringContainsString('T-05', $html);
        $this->assertStringContainsString('Paneer Tikka Dry', $html);
        $this->assertStringContainsString('Butter Roti', $html);
        $this->assertStringContainsString('Cold Drinks', $html);

        // Zero leakage of Restaurant A
        $this->assertStringNotContainsString('Royal Spice Bistro', $html);
        $this->assertStringNotContainsString('Galaxy Plaza', $html);
        $this->assertStringNotContainsString('9876500001', $html);
        $this->assertStringNotContainsString('Royal Veg Spring Roll', $html);
    }

    /** @test */
    public function asopalav_kitchen_kot_renders_only_food_items_and_asopalav_header(): void
    {
        $response = $this->actingAs($this->asopalavUser)
            ->get("/orders/{$this->asopalavOrder->id}/receipt/kitchen");

        $response->assertStatus(200);
        $html = $response->getContent();

        $this->assertStringContainsString('Asopalav', $html);
        $this->assertStringContainsString('9558106440', $html);
        $this->assertStringContainsString('T-05', $html);
        $this->assertStringContainsString('Paneer Tikka Dry', $html);
        $this->assertStringContainsString('Butter Roti', $html);
        $this->assertStringContainsString('Extra spicy and crispy', $html);
        $this->assertStringContainsString('Asopalav - Kitchen Slip', $html);

        // Drinks must be excluded from Kitchen KOT
        $this->assertStringNotContainsString('Cold Drinks', $html);
    }

    /** @test */
    public function asopalav_bar_kot_renders_only_beverage_items(): void
    {
        $response = $this->actingAs($this->asopalavUser)
            ->get("/orders/{$this->asopalavOrder->id}/receipt/bar");

        $response->assertStatus(200);
        $html = $response->getContent();

        $this->assertStringContainsString('Asopalav', $html);
        $this->assertStringContainsString('Cold Drinks', $html);
        $this->assertStringContainsString('Asopalav - Bar Slip', $html);

        // Food items must be excluded from Bar KOT
        $this->assertStringNotContainsString('Butter Roti', $html);
        $this->assertStringNotContainsString('Paneer Tikka Dry', $html);
    }

    /** @test */
    public function asopalav_user_cannot_access_restaurant_a_thermal_receipt(): void
    {
        $response = $this->actingAs($this->asopalavUser)
            ->get("/orders/{$this->orderA->id}/receipt");

        $response->assertStatus(404);
    }

    /** @test */
    public function restaurant_a_thermal_receipt_renders_only_restaurant_a_details(): void
    {
        $response = $this->actingAs($this->restAUser)
            ->get("/orders/{$this->orderA->id}/receipt");

        $response->assertStatus(200);
        $html = $response->getContent();

        $this->assertStringContainsString('Royal Spice Bistro', $html);
        $this->assertStringContainsString('Galaxy Plaza', $html);
        $this->assertStringContainsString('9876500001', $html);
        $this->assertStringContainsString('24ROYAL1234A1Z1', $html);
        $this->assertStringContainsString('Royal Veg Spring Roll', $html);
        $this->assertStringContainsString('Mango Lassi Special', $html);

        // Zero leakage of Asopalav
        $this->assertStringNotContainsString('Asopalav', $html);
        $this->assertStringNotContainsString('Gopi Avenue', $html);
        $this->assertStringNotContainsString('9558106440', $html);
    }

    /** @test */
    public function restaurant_a_user_cannot_access_asopalav_thermal_receipt(): void
    {
        $response = $this->actingAs($this->restAUser)
            ->get("/orders/{$this->asopalavOrder->id}/receipt");

        $response->assertStatus(404);
    }
}
