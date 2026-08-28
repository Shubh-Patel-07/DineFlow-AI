<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function getSalesAnalytics($restaurantId)
    {
        $totalOrders = Order::where('restaurant_id', $restaurantId)->count();
        $paidOrdersQuery = Order::where('restaurant_id', $restaurantId)->where('payment_status', 'paid');
        $totalSales = (float) $paidOrdersQuery->sum('total_amount');
        $paidOrdersCount = $paidOrdersQuery->count();
        
        $averageOrderValue = $paidOrdersCount > 0 ? round($totalSales / $paidOrdersCount, 2) : 0;

        $popularItems = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.restaurant_id', $restaurantId)
            ->where('orders.payment_status', 'paid')
            ->select('order_items.name', DB::raw('SUM(order_items.quantity) as salesCount'))
            ->groupBy('order_items.name')
            ->orderBy('salesCount', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'totalOrders' => $totalOrders,
                'totalSales' => round($totalSales, 2),
                'averageOrderValue' => $averageOrderValue,
                'popularItems' => $popularItems,
            ]
        ]);
    }

    /**
     * 📊 Menu Engineering Matrix (Stars, Plowhorses, Puzzles, Dogs)
     */
    public function getMenuEngineeringMatrix($restaurantId)
    {
        $menuItems = MenuItem::where('restaurant_id', $restaurantId)->get();
        if ($menuItems->isEmpty()) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $salesData = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.restaurant_id', $restaurantId)
            ->where('orders.payment_status', 'paid')
            ->select('order_items.name', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue'))
            ->groupBy('order_items.name')
            ->get()
            ->keyBy('name');

        $totalVolume = $salesData->sum('total_qty') ?: 1;
        $avgItemVolume = $totalVolume / max(1, $menuItems->count());

        $matrix = [];
        foreach ($menuItems as $item) {
            $sales = $salesData->get($item->name);
            $qtySold = $sales ? (int)$sales->total_qty : 0;
            $revenue = $sales ? (float)$sales->total_revenue : 0;

            $estimatedCost = round($item->price * 0.32, 2);
            $margin = round($item->price - $estimatedCost, 2);
            $marginPct = round(($margin / max(1, $item->price)) * 100, 1);

            $isHighProfit = $margin >= ($item->price * 0.60);
            $isHighPopular = $qtySold >= $avgItemVolume;

            if ($isHighProfit && $isHighPopular) {
                $category = 'Star';
                $action = 'Promote consistently & keep quality top-tier';
            } elseif (!$isHighProfit && $isHighPopular) {
                $category = 'Plowhorse';
                $action = 'Slightly increase price or reduce portion cost';
            } elseif ($isHighProfit && !$isHighPopular) {
                $category = 'Puzzle';
                $action = 'Feature in combo deals & promote with AI upsell';
            } else {
                $category = 'Dog';
                $action = 'Re-evaluate recipe or replace with popular dish';
            }

            $matrix[] = [
                'id'            => $item->id,
                'name'          => $item->name,
                'category_name' => $item->category,
                'price'         => (float)$item->price,
                'food_cost'     => $estimatedCost,
                'margin'        => $margin,
                'margin_pct'    => $marginPct,
                'qty_sold'      => $qtySold,
                'revenue'       => $revenue,
                'matrix_quad'   => $category,
                'action_hint'   => $action,
            ];
        }

        return response()->json(['success' => true, 'data' => $matrix]);
    }

    /**
     * 📈 AI Smart Demand & Raw Ingredient Forecasting
     */
    public function getDemandForecast($restaurantId)
    {
        $today = now();
        $isWeekendSoon = in_array($today->dayOfWeek, [5, 6, 0]);
        $demandMultiplier = $isWeekendSoon ? 1.45 : 1.15;

        $ingredients = Inventory::where('restaurant_id', $restaurantId)->get();

        $forecast = [];
        foreach ($ingredients as $item) {
            $dailyBurn = max(1, round($item->quantity * 0.18, 1));
            $predictedDemand3Days = round($dailyBurn * 3 * $demandMultiplier, 1);
            $remainingAfter3Days = round($item->quantity - $predictedDemand3Days, 1);
            $isCritical = $remainingAfter3Days <= ($item->low_stock_alert ?? 5);

            $forecast[] = [
                'id'                   => $item->id,
                'name'                 => $item->item_name,
                'current_stock'        => (float)$item->quantity,
                'unit'                 => $item->unit ?? 'kg',
                'predicted_3day_need'  => $predictedDemand3Days,
                'projected_balance'    => max(0, $remainingAfter3Days),
                'status'               => $isCritical ? 'restock_urgently' : 'optimal',
                'suggested_reorder'    => $isCritical ? max(10, round($predictedDemand3Days * 1.5)) : 0,
                'weekend_surge_active' => $isWeekendSoon
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'weekend_surge_factor' => $demandMultiplier,
                'forecast_horizon_days'=> 3,
                'items'                => $forecast
            ]
        ]);
    }
}