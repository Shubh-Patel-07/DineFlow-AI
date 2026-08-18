<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminDashboardAnalyticsTest extends TestCase
{
    /** @test */
    public function it_returns_admin_analytics_summary(): void
    {
        $response = $this->getJson('/api/analytics/sales/1');
        $response->assertStatus(200);
        $this->assertArrayHasKey('success', $response->json());
    }

    /** @test */
    public function it_fetches_category_sales_distribution(): void
    {
        $response = $this->getJson('/api/analytics/category-sales/1');
        $response->assertStatus(200);
        $this->assertArrayHasKey('success', $response->json());
    }

    /** @test */
    public function it_fetches_hourly_traffic_trends(): void
    {
        $response = $this->getJson('/api/analytics/hourly-traffic/1');
        $response->assertStatus(200);
    }
}
