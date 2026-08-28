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
        $user = auth()->user();
        if ($user && $user->role !== 'superadmin' && (int)$user->restaurant_id !== (int)$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized tenant analytics access.'], 403);
        }

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
     * 📈 Comprehensive Authoritative Sales Analytics Report (Zero-Start + Date Filtering)
     */
    public function getSalesReport(Request $request, $restaurantId = null)
    {
        $user = auth()->user();
        $targetRestaurantId = $restaurantId ?: ($user ? $user->restaurant_id : ($request->query('restaurant_id') ?? $request->query('restaurantId')));

        if ($user && $user->role !== 'superadmin' && $user->restaurant_id && (int)$user->restaurant_id !== (int)$targetRestaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized tenant sales report access.'], 403);
        }

        if (!$targetRestaurantId) {
            return response()->json(['success' => false, 'message' => 'Missing restaurant ID'], 422);
        }

        $filter = $request->query('filter', 'today');
        $customDate = $request->query('date');

        // 1. Determine Date Range Filter
        $now = \Carbon\Carbon::now();
        $ordersQuery = Order::where('restaurant_id', $targetRestaurantId)
            ->where(function($q) {
                $q->where('payment_status', 'paid')
                  ->orWhere('status', 'completed');
            })
            ->whereNotIn('status', ['cancelled']);

        if ($filter === 'today') {
            $ordersQuery->whereDate('created_at', \Carbon\Carbon::today());
        } elseif ($filter === 'yesterday') {
            $ordersQuery->whereDate('created_at', \Carbon\Carbon::yesterday());
        } elseif ($filter === '7days') {
            $ordersQuery->whereDate('created_at', '>=', \Carbon\Carbon::today()->subDays(6)->startOfDay())
                        ->whereDate('created_at', '<=', \Carbon\Carbon::today()->endOfDay());
        } elseif ($filter === 'month') {
            $ordersQuery->whereYear('created_at', $now->year)
                        ->whereMonth('created_at', $now->month);
        } elseif ($filter === 'custom' && $customDate) {
            $ordersQuery->whereDate('created_at', $customDate);
        }

        $paidOrders = $ordersQuery->with(['items.menuItem', 'table'])->orderBy('created_at', 'desc')->get();

        // 2. Summary Metrics
        $grossRevenue = (float)$paidOrders->sum('total_amount');
        $totalPaidCount = $paidOrders->count();
        $taxCollected = round($grossRevenue * 0.05 / 1.05, 2);
        $avgTicket = $totalPaidCount > 0 ? round($grossRevenue / $totalPaidCount, 2) : 0.00;

        $upiCount = $paidOrders->filter(function($o) {
            $m = strtolower($o->payment_method ?? '');
            return str_contains($m, 'upi') || str_contains($m, 'online') || str_contains($m, 'gpay') || str_contains($m, 'digital');
        })->count();

        $upiShare = $totalPaidCount > 0 ? (int)round(($upiCount / $totalPaidCount) * 100) : 0;

        // 3. Past 7-Days Revenue Trajectory (Authoritative & Zero-Start)
        $refDate = ($filter === 'custom' && $customDate) ? \Carbon\Carbon::parse($customDate) : \Carbon\Carbon::today();
        $trajectoryLabels = [];
        $trajectoryData = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = (clone $refDate)->subDays($i);
            $trajectoryLabels[] = $day->format('D'); // Mon, Tue, etc.
            
            $dayTotal = (float) Order::where('restaurant_id', $targetRestaurantId)
                ->whereDate('created_at', $day->toDateString())
                ->where(function($q) {
                    $q->where('payment_status', 'paid')
                      ->orWhere('status', 'completed');
                })
                ->whereNotIn('status', ['cancelled'])
                ->sum('total_amount');

            $trajectoryData[] = round($dayTotal, 2);
        }

        // 4. Category Sales Distribution (Authoritative for selected period)
        $orderIds = $paidOrders->pluck('id')->toArray();
        $categorySales = [];

        if (!empty($orderIds)) {
            $catRecords = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->leftJoin('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
                ->whereIn('orders.id', $orderIds)
                ->select(DB::raw("COALESCE(menu_items.category, 'General') as category"), DB::raw('SUM(order_items.quantity * order_items.price) as total'))
                ->groupBy(DB::raw("COALESCE(menu_items.category, 'General')"))
                ->get();

            foreach ($catRecords as $rec) {
                $categorySales[$rec->category] = round((float)$rec->total, 2);
            }
        }

        $hasCategoryData = !empty($categorySales) && array_sum(array_values($categorySales)) > 0;

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'gross_revenue' => round($grossRevenue, 2),
                    'total_orders' => $totalPaidCount,
                    'tax_collected' => $taxCollected,
                    'avg_order_value' => $avgTicket,
                    'upi_share' => $upiShare,
                ],
                'revenue_trajectory' => [
                    'labels' => $trajectoryLabels,
                    'data' => $trajectoryData,
                    'total_trajectory_revenue' => array_sum($trajectoryData),
                ],
                'category_share' => [
                    'labels' => array_keys($categorySales),
                    'data' => array_values($categorySales),
                    'has_data' => $hasCategoryData,
                ],
                'settled_orders' => $paidOrders->map(function($o) {
                    return [
                        'id' => $o->id,
                        'table_id' => $o->table_id,
                        'table_number' => $o->table?->table_number ?? ('Table #' . $o->table_id),
                        'total_amount' => (float)$o->total_amount,
                        'tax_amount' => round((float)$o->total_amount * 0.05 / 1.05, 2),
                        'payment_method' => $o->payment_method ?? 'cash',
                        'payment_status' => $o->payment_status,
                        'created_at' => $o->created_at->toISOString(),
                        'created_at_time' => $o->created_at->format('H:i'),
                        'items' => $o->items->map(function($i) {
                            return [
                                'name' => $i->name,
                                'quantity' => (int)$i->quantity,
                                'price' => (float)$i->price,
                            ];
                        })
                    ];
                })
            ]
        ]);
    }

    /**
     * 📊 Menu Engineering Matrix (Stars, Plowhorses, Puzzles, Dogs)
     */
    public function getMenuEngineeringMatrix($restaurantId)
    {
        $user = auth()->user();
        if ($user && $user->role !== 'superadmin' && (int)$user->restaurant_id !== (int)$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized tenant matrix access.'], 403);
        }

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
        $user = auth()->user();
        if ($user && $user->role !== 'superadmin' && (int)$user->restaurant_id !== (int)$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized tenant forecast access.'], 403);
        }

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