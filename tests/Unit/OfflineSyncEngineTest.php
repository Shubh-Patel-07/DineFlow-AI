<?php

namespace Tests\Unit;

use App\Services\OfflineSyncQueueService;
use Tests\TestCase;

class OfflineSyncEngineTest extends TestCase
{
    private OfflineSyncQueueService $syncService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->syncService = new OfflineSyncQueueService();
    }

    /** @test */
    public function it_rejects_empty_sync_payloads(): void
    {
        $payload = [
            'client_order_id' => 'SYNC-1001',
            'restaurant_id' => '1',
            'table_id' => '2',
            'items' => [],
        ];

        $result = $this->syncService->syncIncomingOrder($payload);

        $this->assertFalse($result['success']);
        $this->assertEquals('No items provided in sync payload.', $result['message']);
    }
}
