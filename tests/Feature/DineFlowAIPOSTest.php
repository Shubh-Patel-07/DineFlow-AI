<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Table;
use App\Models\MenuItem;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\WaiterCall;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DineFlowAIPOSTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_dineflow_pos_backend_integration_flow()
    {
        // 1. Create and authenticate Admin User
        $admin = User::create([
            'name' => 'Admin Boss',
            'username' => 'admin_boss',
            'email' => 'admin@boss.com',
            'password' => bcrypt('password'),
            'numeric_pin' => '1234',
            'role' => 'admin',
            'restaurant_id' => '1',
        ]);

        $this->actingAs($admin);

        // 2. Create Restaurant
        $resRest = $this->postJson('/api/restaurants', [
            'name' => 'Gourmet DineFlow AI POS House',
            'address' => '123 Food Street',
            'phone' => '555-0100',
        ]);
        $resRest->assertStatus(201);
        $restaurantId = $resRest->json('data.id');
        $this->assertNotEmpty($restaurantId);

        // 3. Create Table (with QR Code URL)
        $resTab = $this->postJson('/api/tables', [
            'restaurant_id' => (string)$restaurantId,
            'table_number' => 'Table 7',
        ]);
        $resTab->assertStatus(201);
        $tableId = $resTab->json('data.id');
        $this->assertNotEmpty($resTab->json('data.qr_code_url'));

        // 4. Pre-stock Inventory
        $resInv1 = $this->postJson('/api/inventory', [
            'restaurant_id' => (string)$restaurantId,
            'name' => 'Double Cheese Burger',
            'quantity' => 10,
        ]);
        $resInv1->assertStatus(201);
        $invId1 = $resInv1->json('data.id');

        $resInv2 = $this->postJson('/api/inventory', [
            'restaurant_id' => (string)$restaurantId,
            'name' => 'Cheesy Garlic Bread',
            'quantity' => 10,
        ]);
        $resInv2->assertStatus(201);
        $invId2 = $resInv2->json('data.id');

        // 5. Add Menu Items
        $resMenu1 = $this->postJson('/api/menu', [
            'restaurant_id' => (string)$restaurantId,
            'name' => 'Double Cheese Burger',
            'price' => 15.00,
            'category' => 'Burgers',
        ]);
        $resMenu1->assertStatus(201);
        $itemId1 = $resMenu1->json('data.id');

        $resMenu2 = $this->postJson('/api/menu', [
            'restaurant_id' => (string)$restaurantId,
            'name' => 'Cheesy Garlic Bread',
            'price' => 8.00,
            'category' => 'Starters',
        ]);
        $resMenu2->assertStatus(201);
        $itemId2 = $resMenu2->json('data.id');

        // 6. Place Order (Should trigger auto-deduction)
        // 2 burgers ($15 each) + 3 garlic bread ($8 each) = 30 + 24 = $54
        $resOrder = $this->postJson('/api/orders', [
            'restaurant_id' => (string)$restaurantId,
            'table_id' => (string)$tableId,
            'items' => [
                ['menu_item_id' => $itemId1, 'quantity' => 2],
                ['menu_item_id' => $itemId2, 'quantity' => 3],
            ]
        ]);
        $resOrder->assertStatus(201);
        $orderId = $resOrder->json('data.id');
        $this->assertEquals(54.00, $resOrder->json('data.total_amount'));

        // 7. Verify Inventory stock deduction (10 - 2 = 8, 10 - 3 = 7)
        $this->assertEquals(8, Inventory::find($invId1)->quantity);
        $this->assertEquals(7, Inventory::find($invId2)->quantity);

        // 8. Update Order Status (Kitchen)
        $resStatus = $this->putJson("/api/orders/{$orderId}/status", [
            'status' => 'preparing',
        ]);
        $resStatus->assertStatus(200);
        $this->assertEquals('preparing', $resStatus->json('data.status'));

        // 9. Pay Bill (Payment check)
        $resPay = $this->postJson("/api/orders/{$orderId}/pay", [
            'payment_method' => 'online',
        ]);
        $resPay->assertStatus(200);
        $this->assertEquals('paid', $resPay->json('data.payment_status'));

        // 10. Fetch Sales Analytics
        $resAnalytics = $this->getJson("/api/analytics/restaurant/{$restaurantId}");
        $resAnalytics->assertStatus(200);
        $this->assertEquals(54.00, $resAnalytics->json('data.totalSales'));
        $this->assertEquals(1, $resAnalytics->json('data.totalOrders'));

        // 11. Customer feedback & Waiter calls
        $resFeed = $this->postJson('/api/feedback', [
            'restaurant_id' => (string)$restaurantId,
            'rating' => 5,
            'comment' => 'Awesome service!',
        ]);
        $resFeed->assertStatus(201);

        $resCall = $this->postJson('/api/waiter-calls', [
            'restaurant_id' => (string)$restaurantId,
            'table_id' => (string)$tableId,
        ]);
        $resCall->assertStatus(201);
        $callId = $resCall->json('data.id');

        // Resolve waiter call
        $resResolve = $this->putJson("/api/waiter-calls/{$callId}/resolve");
        $resResolve->assertStatus(200);
        $this->assertEquals('resolved', $resResolve->json('data.status'));

        // 12. Verify Role Security Blocks
        $customer = User::create([
            'name' => 'Bob Customer',
            'username' => 'bob_cust',
            'email' => 'bob@cust.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $this->actingAs($customer);

        // Customer attempts to fetch admin stats (expect 403)
        $resBlock = $this->getJson("/api/analytics/restaurant/{$restaurantId}");
        $resBlock->assertStatus(403);
    }
}
