<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Collection;

/**
 * Enterprise Service for Kitchen Display System (KDS) Load Balancing,
 * SLA turnaround estimation, station-wise routing (Kitchen vs. Bar),
 * and dynamic preparation queue prioritization.
 */
class KitchenLoadBalancerService
{
    public const DEFAULT_PREPARATION_TIME_MINUTES = 12;
    public const STATION_KITCHEN = 'kitchen';
    public const STATION_BAR = 'bar';
    public const STATION_DESSERT = 'dessert';

    /**
     * Compute estimated preparation time for an incoming order ticket based on active load.
     *
     * @param Order $order The incoming order
     * @param int $activeQueueLength Current number of pending/cooking tickets in kitchen
     * @return array Turnaround metrics and ETA
     */
    public function calculateOrderTurnaround(Order $order, int $activeQueueLength = 0): array
    {
        $itemCount = $order->items ? $order->items->sum('quantity') : 1;
        $basePrepTime = self::DEFAULT_PREPARATION_TIME_MINUTES;

        // Dynamic complexity multiplier based on item volume
        $volumeOverhead = max(0, ($itemCount - 2) * 2.5);

        // Queue congestion latency: +3 mins per active backlog ticket
        $congestionDelay = $activeQueueLength * 3.0;

        $totalEstimatedMinutes = (int)ceil($basePrepTime + $volumeOverhead + $congestionDelay);

        $slaStatus = 'optimal';
        if ($totalEstimatedMinutes > 25) {
            $slaStatus = 'delayed';
        } elseif ($totalEstimatedMinutes > 15) {
            $slaStatus = 'moderate';
        }

        return [
            'order_id' => $order->id,
            'item_count' => $itemCount,
            'active_backlog_tickets' => $activeQueueLength,
            'estimated_minutes' => $totalEstimatedMinutes,
            'sla_status' => $slaStatus,
            'suggested_pickup_time' => now()->addMinutes($totalEstimatedMinutes)->toIso8601String(),
        ];
    }

    /**
     * Route order items to distinct kitchen stations (e.g. Hot Kitchen, Bar & Beverages, Bakery).
     *
     * @param Collection|array $orderItems List of order items
     * @return array Grouped items by station
     */
    public function routeItemsToStations($orderItems): array
    {
        $stationGroups = [
            self::STATION_KITCHEN => [],
            self::STATION_BAR => [],
            self::STATION_DESSERT => [],
        ];

        foreach ($orderItems as $item) {
            $category = strtolower($item->menuItem->category ?? $item['category'] ?? 'mains');

            if (str_contains($category, 'drink') || str_contains($category, 'bever') || str_contains($category, 'lassi') || str_contains($category, 'mocktail')) {
                $stationGroups[self::STATION_BAR][] = $item;
            } elseif (str_contains($category, 'dessert') || str_contains($category, 'sweet') || str_contains($category, 'ice cream')) {
                $stationGroups[self::STATION_DESSERT][] = $item;
            } else {
                $stationGroups[self::STATION_KITCHEN][] = $item;
            }
        }

        return $stationGroups;
    }

    /**
     * Prioritize ticket queue order (VIP, overdue SLA, or first-in-first-out).
     *
     * @param Collection $orders Active orders collection
     * @return Collection Sorted priority queue
     */
    public function prioritizeQueue(Collection $orders): Collection
    {
        return $orders->sortBy(function (Order $order) {
            $minutesElapsed = $order->created_at ? now()->diffInMinutes($order->created_at) : 0;
            $isUrgent = $minutesElapsed > 15 ? -100 : 0;
            return $isUrgent + ($order->id * 0.01);
        });
    }
}
