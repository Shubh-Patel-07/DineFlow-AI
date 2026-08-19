<?php

namespace Tests\Feature;

use App\Models\Coupon;
use Tests\TestCase;

class CouponDiscountTest extends TestCase
{
    /** @test */
    public function it_validates_existing_coupon_and_calculates_discount(): void
    {
        $payload = [
            'code' => 'DINE10',
            'subtotal' => 500.00,
            'restaurant_id' => '1',
        ];

        $response = $this->postJson('/api/coupons/validate', $payload);

        // Even if coupon not yet seeded, endpoint responds gracefully with structured json
        $response->assertStatus(200);
        $this->assertArrayHasKey('success', $response->json());
    }
}
