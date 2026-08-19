<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderProcessingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function customer_can_place_order_with_valid_items(): void
    {
        $payload = [
            'restaurant_id' => '1',
            'table_id' => '1',
            'payment_method' => 'cash',
            'special_instructions' => 'Extra onions',
            'items' => [
                [
                    'name' => 'Paneer Butter Masala',
                    'price' => 240.00,
                    'quantity' => 1,
                ],
                [
                    'name' => 'Garlic Naan',
                    'price' => 60.00,
                    'quantity' => 2,
                ],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'id',
                         'table_id',
                         'status',
                         'total_amount',
                     ],
                 ]);

        $this->assertTrue($response->json('success'));
    }

    /** @test */
    public function order_fails_when_items_list_is_empty(): void
    {
        $payload = [
            'restaurant_id' => '1',
            'table_id' => '1',
            'items' => [],
        ];

        $response = $this->postJson('/api/orders', $payload);
        $response->assertStatus(400);
    }
}
