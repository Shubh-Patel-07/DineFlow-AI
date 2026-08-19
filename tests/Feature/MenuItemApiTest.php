<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use Tests\TestCase;

class MenuItemApiTest extends TestCase
{
    /** @test */
    public function it_returns_menu_items_list(): void
    {
        $response = $this->getJson('/api/menu?restaurantId=1');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data',
                 ]);
    }

    /** @test */
    public function it_filters_menu_items_by_restaurant_id(): void
    {
        $response = $this->getJson('/api/menu/restaurant/1');

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
    }
}
