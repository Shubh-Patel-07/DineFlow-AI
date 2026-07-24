<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function getSalesAnalytics($restaurantId)
    {
        $totalOrders = Order::where('restaurant_id', $restaurantId)->count();
        $totalSales = Order::where('restaurant_id', $restaurantId)->where('payment_status', 'paid')->sum('total_amount');
        
        $averageOrderValue = $totalOrders > 0 ? round($totalSales / $totalOrders, 2) : 0;

        $popularItems = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.restaurant_id', $restaurantId)
            ->select('order_items.name', DB::raw('SUM(order_items.quantity) as salesCount'))
            ->groupBy('order_items.name')
            ->orderBy('salesCount', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'totalOrders' => $totalOrders,
                'totalSales' => (float)$totalSales,
                'averageOrderValue' => $averageOrderValue,
                'popularItems' => $popularItems,
            ]
        ]);
    }
}
