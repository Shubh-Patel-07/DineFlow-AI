<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Restaurant;
use App\Models\AuditLog;
use App\Models\SubscriptionHistory;
use Carbon\Carbon;

class ProcessSubscriptionExpiryCommand extends Command
{
    protected $signature = 'subscriptions:process-expiry';
    protected $description = 'Evaluate restaurant subscription expiration, grace periods, and lifecycle states';

    public function handle(): int
    {
        $this->info('Starting SaaS subscription lifecycle evaluation...');
        $now = Carbon::now();
        $processed = 0;
        $expiringCount = 0;
        $graceCount = 0;
        $expiredCount = 0;
        $restoredCount = 0;

        $restaurants = Restaurant::where(function($q) {
            $q->whereNotNull('subscription_expires_at')
              ->orWhereNotNull('trial_ends_at');
        })->where('is_demo', false)->get();

        foreach ($restaurants as $restaurant) {
            $expiresAt = $restaurant->subscription_expires_at 
                ? Carbon::parse($restaurant->subscription_expires_at) 
                : ($restaurant->trial_ends_at ? Carbon::parse($restaurant->trial_ends_at) : null);

            if (!$expiresAt) {
                continue;
            }

            $graceDays = !is_null($restaurant->grace_period_days) ? intval($restaurant->grace_period_days) : 7;
            $graceEndsAt = (clone $expiresAt)->addDays($graceDays);
            $currentStatus = strtolower(trim($restaurant->subscription_status ?? 'active'));

            // Ignore explicitly suspended or cancelled restaurants
            if (in_array($currentStatus, ['suspended', 'cancelled'])) {
                continue;
            }

            // Case 0: Legitimate Future Expiry -> If status was expired/grace_period due to past date, restore to active
            if ($now->isBefore($expiresAt)) {
                if (in_array($currentStatus, ['expired', 'grace_period'])) {
                    $oldStatus = $currentStatus;
                    $restoredStatus = ($restaurant->trial_ends_at && !$restaurant->subscription_expires_at) ? 'trial' : 'active';
                    $restaurant->update([
                        'subscription_status' => $restoredStatus,
                        'suspension_reason' => null
                    ]);

                    SubscriptionHistory::record($restaurant, $restaurant->plan_type ?? 'free', $restoredStatus, [
                        'previous_status' => $oldStatus,
                        'reason' => 'Subscription validity extended to future date',
                        'changed_by' => null,
                    ]);

                    AuditLog::record('subscription_restored_auto', $restaurant->id, 'subscription', $restaurant->id, [
                        'previous_status' => $oldStatus,
                        'new_status' => $restoredStatus,
                        'expires_at' => $expiresAt->toDateTimeString(),
                    ]);

                    $this->info("Restaurant #{$restaurant->id} '{$restaurant->name}' RESTORED to {$restoredStatus} (Valid until {$expiresAt->toDateString()})");
                    $restoredCount++;
                } elseif ($now->diffInDays($expiresAt, false) <= 3) {
                    // Within 3 days of expiring -> Mark EXPIRING
                    if ($currentStatus === 'active' || $currentStatus === 'trial') {
                        $oldStatus = $currentStatus;
                        $restaurant->update(['subscription_status' => 'expiring']);

                        AuditLog::record('subscription_expiring_soon_cron', $restaurant->id, 'subscription', $restaurant->id, [
                            'previous_status' => $oldStatus,
                            'new_status' => 'expiring',
                            'days_remaining' => $now->diffInDays($expiresAt)
                        ]);

                        $this->info("Restaurant #{$restaurant->id} '{$restaurant->name}' marked EXPIRING SOON ({$expiresAt->toDateString()})");
                        $expiringCount++;
                    }
                }
            }
            // Case 1: Past grace period -> Mark EXPIRED
            elseif ($now->isAfter($graceEndsAt)) {
                if ($currentStatus !== 'expired') {
                    $oldStatus = $currentStatus;
                    $restaurant->update(['subscription_status' => 'expired']);

                    SubscriptionHistory::record($restaurant, $restaurant->plan_type ?? 'free', 'expired', [
                        'previous_status' => $oldStatus,
                        'reason' => 'Grace period exceeded without renewal',
                        'changed_by' => null, // Automated system
                    ]);

                    AuditLog::record('subscription_expired_cron', $restaurant->id, 'subscription', $restaurant->id, [
                        'previous_status' => $oldStatus,
                        'new_status' => 'expired',
                        'expires_at' => $expiresAt->toDateTimeString(),
                        'grace_ends_at' => $graceEndsAt->toDateTimeString()
                    ]);

                    $this->warn("Restaurant #{$restaurant->id} '{$restaurant->name}' marked EXPIRED (Grace ended {$graceEndsAt->toDateString()})");
                    $expiredCount++;
                }
            }
            // Case 2: Past expiry date but within grace period -> Mark GRACE_PERIOD
            elseif ($now->isAfter($expiresAt) && $now->isBefore($graceEndsAt)) {
                if ($currentStatus !== 'grace_period') {
                    $oldStatus = $currentStatus;
                    $restaurant->update(['subscription_status' => 'grace_period']);

                    SubscriptionHistory::record($restaurant, $restaurant->plan_type ?? 'free', 'grace_period', [
                        'previous_status' => $oldStatus,
                        'reason' => "Entered {$graceDays}-day grace period",
                        'changed_by' => null,
                    ]);

                    AuditLog::record('subscription_grace_period_cron', $restaurant->id, 'subscription', $restaurant->id, [
                        'previous_status' => $oldStatus,
                        'new_status' => 'grace_period',
                        'expires_at' => $expiresAt->toDateTimeString(),
                        'grace_ends_at' => $graceEndsAt->toDateTimeString()
                    ]);

                    $this->line("Restaurant #{$restaurant->id} '{$restaurant->name}' entered GRACE PERIOD until {$graceEndsAt->toDateString()}");
                    $graceCount++;
                }
            }

            $processed++;
        }

        $this->info("Lifecycle processing completed. Evaluated: {$processed} | Restored: {$restoredCount} | Expiring Soon: {$expiringCount} | Grace Period: {$graceCount} | Expired: {$expiredCount}");
        return Command::SUCCESS;
    }
}
