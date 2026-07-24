<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|string',
            'table_number' => 'required|string',
        ]);

        $table = Table::create([
            'restaurant_id' => $request->restaurant_id,
            'table_number' => $request->table_number,
            'status' => 'active',
        ]);

        $localIP = gethostbyname(gethostname());
        $host = "http://" . $localIP . ":8000";
        $qrData = $host . "/customer/menu?restaurantId=" . $request->restaurant_id . "&tableId=" . $table->id;
        $table->qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrData);
        $table->save();

        return response()->json(['success' => true, 'data' => $table], 201);
    }

    public function getRestaurantTables($restaurantId)
    {
        $tables = Table::where('restaurant_id', $restaurantId)->get();
        
        $serverIp = request()->server('SERVER_ADDR') ?: gethostbyname(gethostname());
        if (!$serverIp || $serverIp === '127.0.0.1') {
            $serverIp = request()->ip();
        }

        // Map tables to include dynamic occupancy status and live dynamic QR code URL
        $tables = $tables->map(function($table) use ($serverIp) {
            // Dynamically replace any local IP address in qr_code_url with current active server IP
            if ($table->qr_code_url) {
                $table->qr_code_url = preg_replace('/(?:192\.168|10|172\.(?:1[6-9]|2\d|3[01]))\.[\d.]+|127\.0\.0\.1/', $serverIp, $table->qr_code_url);
            }

            $activeOrder = \App\Models\Order::where('table_id', $table->id)
                ->where('payment_status', 'pending')
                ->first();
            if ($activeOrder) {
                $table->status = 'occupied';
                $table->active_order_id = $activeOrder->id;
                $subtotal = $activeOrder->total_amount;
                $serviceCharge = $subtotal * 0.05;
                $gst = ($subtotal + $serviceCharge) * 0.18;
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
        $table = Table::find($id);

        if (!$table) {
            return response()->json(['success' => false, 'message' => 'Table not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $table]);
    }

    public function destroy($id)
    {
        $table = Table::find($id);

        if (!$table) {
            return response()->json(['success' => false, 'message' => 'Table not found'], 404);
        }

        // Prevent deleting a table if it has active unpaid orders
        $hasActiveOrder = \App\Models\Order::where('table_id', $table->id)
            ->where('payment_status', 'pending')
            ->exists();

        if ($hasActiveOrder) {
            return response()->json(['success' => false, 'message' => 'Cannot delete a table with an active unpaid order'], 400);
        }

        $table->delete();

        return response()->json(['success' => true, 'message' => 'Table deleted successfully']);
    }
}
