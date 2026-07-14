<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $restaurantId = $request->query('restaurantId');
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Restaurant ID is required'], 400);
        }

        $items = Inventory::where('restaurant_id', $restaurantId)->get();
        return response()->json(['success' => true, 'data' => $items]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|string',
            'name' => 'required|string',
            'quantity' => 'required|integer',
        ]);

        $item = Inventory::create([
            'restaurant_id' => $request->restaurant_id,
            'name' => $request->name,
            'quantity' => $request->quantity,
            'unit' => $request->unit ?? 'units',
            'threshold' => $request->threshold ?? 10,
        ]);

        return response()->json(['success' => true, 'data' => $item], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer',
        ]);

        $item = Inventory::find($id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Inventory item not found'], 404);
        }

        $item->quantity = $request->quantity;
        $item->save();

        return response()->json(['success' => true, 'data' => $item]);
    }
}
