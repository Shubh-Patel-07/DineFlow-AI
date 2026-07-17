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

        $qrData = "http://localhost:8000/customer/menu?restaurantId=" . $request->restaurant_id . "&tableId=" . $table->id;
        $table->qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrData);
        $table->save();

        return response()->json(['success' => true, 'data' => $table], 201);
    }

    public function getRestaurantTables($restaurantId)
    {
        $tables = Table::where('restaurant_id', $restaurantId)->get();
        
        // Map tables to include dynamic occupancy status based on unpaid orders
        $tables = $tables->map(function($table) {
            $hasActiveOrder = \App\Models\Order::where('table_id', $table->id)
                ->where('payment_status', 'pending')
                ->exists();
            $table->status = $hasActiveOrder ? 'occupied' : 'available';
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
}
