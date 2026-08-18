<?php

namespace Tests\Feature;

use Tests\TestCase;

class TableOccupancyLifecycleTest extends TestCase
{
    /** @test */
    public function it_fetches_all_tables_with_occupancy_status(): void
    {
        $response = $this->getJson('/api/tables/restaurant/1');
        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response->json());
    }

    /** @test */
    public function it_returns_individual_table_details(): void
    {
        $response = $this->getJson('/api/tables/1');
        $response->assertStatus(200);
    }
}
