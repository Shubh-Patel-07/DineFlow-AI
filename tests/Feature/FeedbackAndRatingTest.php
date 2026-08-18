<?php

namespace Tests\Feature;

use Tests\TestCase;

class FeedbackAndRatingTest extends TestCase
{
    /** @test */
    public function customer_can_submit_review_with_rating(): void
    {
        $payload = [
            'restaurant_id' => '1',
            'customer_name' => 'Karan Dave',
            'rating' => 5,
            'comment' => 'Amazing food and super fast QR ordering!',
        ];

        $response = $this->postJson('/api/feedback', $payload);
        $response->assertStatus(201);
        $this->assertTrue($response->json('success'));
    }

    /** @test */
    public function admin_can_view_restaurant_feedback_list(): void
    {
        $response = $this->getJson('/api/feedback/restaurant/1');
        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response->json());
    }
}
