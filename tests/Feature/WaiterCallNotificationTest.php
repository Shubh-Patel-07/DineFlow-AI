<?php

namespace Tests\Feature;

use App\Models\WaiterCall;
use Tests\TestCase;

class WaiterCallNotificationTest extends TestCase
{
    /** @test */
    public function customer_can_call_waiter_for_table(): void
    {
        $payload = [
            'restaurant_id' => '1',
            'table_id' => '2',
            'request_type' => 'bill',
        ];

        $response = $this->postJson('/api/waiter-calls', $payload);
        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
    }

    /** @test */
    public function staff_can_fetch_active_waiter_calls(): void
    {
        $response = $this->getJson('/api/waiter-calls/restaurant/1');
        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response->json());
    }
}
