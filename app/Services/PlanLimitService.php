<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\Table;
use App\Models\User;
use App\Models\MenuItem;
use App\Models\Order;
use Carbon\Carbon;

class PlanLimitService
{
    public static function getDefaultPlanLimits(string $planType = 'free'): array
    {
        if ($planType === 'pro' || $planType === 'enterprise') {
            return [
                'tables' => 100,
                'staff' => 50,
                'menu_items' => 1000,
                'monthly_orders' => 10000,
            ];
        }

        // Free Plan Defaults
        return [
            'tables' => 10,
            'staff' => 5,
            'menu_items' => 100,
            'monthly_orders' => 500,
        ];
    }

    public static function getLimits($restaurantId): array
    {
        $restaurant = Restaurant::find($restaurantId);
        if (!$restaurant) {
            return self::getDefaultPlanLimits('free');
        }

        $limits = $restaurant->plan_limits;
        if (!is_array($limits) || empty($limits)) {
            $limits = self::getDefaultPlanLimits($restaurant->plan_type ?? 'free');
        }

        return array_merge(self::getDefaultPlanLimits($restaurant->plan_type ?? 'free'), $limits);
    }

    public static function canCreateTable($restaurantId): array
    {
        $limits = self::getLimits($restaurantId);
        $maxTables = intval($limits['tables'] ?? 10);
        $currentTables = Table::where('restaurant_id', $restaurantId)->count();

        if ($currentTables >= $maxTables) {
            return [
                'allowed' => false,
                'current' => $currentTables,
                'limit' => $maxTables,
                'message' => "Free Plan limit reached (maximum {$maxTables} tables). Please upgrade your plan."
            ];
        }

        return [
            'allowed' => true,
            'current' => $currentTables,
            'limit' => $maxTables,
            'message' => 'OK'
        ];
    }

    public static function canCreateStaff($restaurantId): array
    {
        $limits = self::getLimits($restaurantId);
        $maxStaff = intval($limits['staff'] ?? 5);
        $currentStaff = User::where('restaurant_id', $restaurantId)
            ->whereIn('role', ['admin', 'manager', 'cashier', 'kitchen', 'waiter', 'staff'])
            ->count();

        if ($currentStaff >= $maxStaff) {
            return [
                'allowed' => false,
                'current' => $currentStaff,
                'limit' => $maxStaff,
                'message' => "Free Plan limit reached (maximum {$maxStaff} staff accounts). Please upgrade your plan."
            ];
        }

        return [
            'allowed' => true,
            'current' => $currentStaff,
            'limit' => $maxStaff,
            'message' => 'OK'
        ];
    }

    public static function canCreateMenuItem($restaurantId): array
    {
        $limits = self::getLimits($restaurantId);
        $maxItems = intval($limits['menu_items'] ?? 100);
        $currentItems = MenuItem::where('restaurant_id', $restaurantId)->count();

        if ($currentItems >= $maxItems) {
            return [
                'allowed' => false,
                'current' => $currentItems,
                'limit' => $maxItems,
                'message' => "Free Plan limit reached (maximum {$maxItems} menu items). Please upgrade your plan."
            ];
        }

        return [
            'allowed' => true,
            'current' => $currentItems,
            'limit' => $maxItems,
            'message' => 'OK'
        ];
    }

    public static function canCreateOrder($restaurantId): array
    {
        $limits = self::getLimits($restaurantId);
        $maxMonthlyOrders = intval($limits['monthly_orders'] ?? 500);
        $currentMonthOrders = Order::where('restaurant_id', $restaurantId)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        if ($currentMonthOrders >= $maxMonthlyOrders) {
            return [
                'allowed' => false,
                'current' => $currentMonthOrders,
                'limit' => $maxMonthlyOrders,
                'message' => "Monthly order limit reached ({$maxMonthlyOrders} orders/month on Free Plan). Please upgrade your plan."
            ];
        }

        return [
            'allowed' => true,
            'current' => $currentMonthOrders,
            'limit' => $maxMonthlyOrders,
            'message' => 'OK'
        ];
    }

    public static function getUsageStats($restaurantId): array
    {
        $limits = self::getLimits($restaurantId);
        $restaurant = Restaurant::find($restaurantId);

        $tablesCount = Table::where('restaurant_id', $restaurantId)->count();
        $staffCount = User::where('restaurant_id', $restaurantId)->whereIn('role', ['admin', 'manager', 'cashier', 'kitchen', 'waiter', 'staff'])->count();
        $menuItemsCount = MenuItem::where('restaurant_id', $restaurantId)->count();
        $monthlyOrdersCount = Order::where('restaurant_id', $restaurantId)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        return [
            'plan_type' => $restaurant?->plan_type ?? 'free',
            'subscription_status' => $restaurant?->subscription_status ?? 'trial',
            'trial_ends_at' => $restaurant?->trial_ends_at,
            'onboarding_completed' => (bool)($restaurant?->onboarding_completed ?? false),
            'usage' => [
                'tables' => [
                    'current' => $tablesCount,
                    'limit' => intval($limits['tables'] ?? 10),
                    'percentage' => round(($tablesCount / max(1, $limits['tables'] ?? 10)) * 100)
                ],
                'staff' => [
                    'current' => $staffCount,
                    'limit' => intval($limits['staff'] ?? 5),
                    'percentage' => round(($staffCount / max(1, $limits['staff'] ?? 5)) * 100)
                ],
                'menu_items' => [
                    'current' => $menuItemsCount,
                    'limit' => intval($limits['menu_items'] ?? 100),
                    'percentage' => round(($menuItemsCount / max(1, $limits['menu_items'] ?? 100)) * 100)
                ],
                'monthly_orders' => [
                    'current' => $monthlyOrdersCount,
                    'limit' => intval($limits['monthly_orders'] ?? 500),
                    'percentage' => round(($monthlyOrdersCount / max(1, $limits['monthly_orders'] ?? 500)) * 100)
                ],
            ]
        ];
    }
}
