<?php

namespace App\Console\Commands;

use App\Services\DemoSandboxService;
use Illuminate\Console\Command;

class CleanupDemoSandboxes extends Command
{
    protected $signature = 'demo:cleanup';
    protected $description = 'Purge expired temporary demo sandbox restaurants and their scoped records';

    public function handle(DemoSandboxService $sandboxService): int
    {
        $this->info("Scanning for expired demo sandboxes...");
        $purgedCount = $sandboxService->cleanupExpiredSandboxes();
        $this->info("Successfully purged {$purgedCount} expired demo sandbox tenants.");

        return Command::SUCCESS;
    }
}
