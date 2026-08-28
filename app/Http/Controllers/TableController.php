<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $restaurantId = $user ? (string)$user->restaurant_id : (string)($request->restaurant_id ?? $request->restaurantId);

        $request->validate([
            'table_number' => 'required|string',
        ]);

        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Restaurant ID is required'], 422);
        }

        $limitCheck = \App\Services\PlanLimitService::canCreateTable($restaurantId);
        if (!$limitCheck['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $limitCheck['message'],
                'limit_exceeded' => true,
                'details' => $limitCheck
            ], 422);
        }

        $table = Table::create([
            'restaurant_id' => $restaurantId,
            'table_number' => $request->table_number,
            'status' => 'active',
        ]);

        $baseUrl = rtrim(config('app.url') ?: url('/'), '/');
        $qrData = $baseUrl . "/customer/menu?restaurantId=" . $restaurantId . "&tableId=" . $table->id;
        $table->qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrData);
        $table->save();

        return response()->json(['success' => true, 'data' => $table], 201);
    }

    public function getRestaurantTables($restaurantId)
    {
        $user = auth()->user();
        if ($user && $user->role !== 'superadmin' && $user->restaurant_id && strval($user->restaurant_id) !== strval($restaurantId)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access to another restaurant\'s tables.'], 403);
        }

        $tables = Table::where('restaurant_id', $restaurantId)->get();
        
        $baseUrl = rtrim(config('app.url') ?: url('/'), '/');
        $restaurant = \App\Models\Restaurant::find($restaurantId);
        $restaurantName = $restaurant ? $restaurant->name : 'DineFlow Restaurant';
        $gstRate = $restaurant ? (float)$restaurant->gst_rate / 100 : 0.05;
        $serviceRate = $restaurant ? (float)$restaurant->service_charge / 100 : 0.05;

        // Map tables to include dynamic occupancy status and live dynamic QR code URL
        $tables = $tables->map(function($table) use ($baseUrl, $restaurantId, $restaurantName, $gstRate, $serviceRate) {
            $table->qr_data_url = $baseUrl . "/customer/menu?restaurantId=" . $restaurantId . "&tableId=" . $table->id;
            $table->restaurant_name = $restaurantName;
            $table->qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($table->qr_data_url);

            $activeOrder = \App\Models\Order::where('table_id', $table->id)
                ->where('payment_status', 'pending')
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->latest()
                ->first();
            if ($activeOrder) {
                $table->status = 'occupied';
                $table->active_order_id = $activeOrder->id;
                $subtotal = $activeOrder->total_amount;
                $serviceCharge = $subtotal * $serviceRate;
                $gst = ($subtotal + $serviceCharge) * $gstRate;
                $table->active_order_amount = round($subtotal + $serviceCharge + $gst);
            } else {
                $table->status = 'available';
                $table->active_order_id = null;
                $activeOrder = null;
                $table->active_order_amount = 0;
            }
            return $table;
        });

        return response()->json(['success' => true, 'data' => $tables]);
    }

    public function show($id)
    {
        $user = auth()->user();
        $query = Table::query();
        if ($user && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $table = $query->find($id);

        if (!$table) {
            return response()->json(['success' => false, 'message' => 'Table not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $table]);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $query = Table::query();
        if ($user && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $table = $query->find($id);

        if (!$table) {
            return response()->json(['success' => false, 'message' => 'Table not found in your restaurant'], 404);
        }

        // Prevent deleting a table if it has active unpaid orders
        $hasActiveOrder = \App\Models\Order::where('table_id', $table->id)
            ->where('payment_status', 'pending')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->exists();

        if ($hasActiveOrder) {
            return response()->json(['success' => false, 'message' => 'Cannot delete a table with an active unpaid order'], 400);
        }

        $table->delete();

        return response()->json(['success' => true, 'message' => 'Table deleted successfully']);
    }
}
