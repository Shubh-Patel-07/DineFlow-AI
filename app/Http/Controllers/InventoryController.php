<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $restaurantId = $request->query('restaurantId', 1);

        $items = Inventory::where('restaurant_id', $restaurantId)->orderBy('item_name')->get();
        return response()->json(['success' => true, 'data' => $items]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'sometimes|integer',
            'item_name'     => 'required|string|max:255',
            'category'      => 'sometimes|string|max:100',
            'quantity'      => 'required|numeric|min:0',
            'unit'          => 'sometimes|string|max:50',
            'low_stock_alert' => 'sometimes|numeric|min:0',
            'cost_per_unit'   => 'sometimes|numeric|min:0',
        ]);

        $item = Inventory::create([
            'restaurant_id'   => $request->restaurant_id ?? 1,
            'item_name'       => $request->item_name,
            'category'        => $request->category ?? 'General',
            'quantity'        => $request->quantity,
            'unit'            => $request->unit ?? 'units',
            'low_stock_alert' => $request->low_stock_alert ?? 10,
            'cost_per_unit'   => $request->cost_per_unit ?? 0,
            'notes'           => $request->notes ?? null,
        ]);

        return response()->json(['success' => true, 'data' => $item], 201);
    }

    public function update(Request $request, $id)
    {
        $item = Inventory::find($id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Inventory item not found'], 404);
        }

        $request->validate([
            'quantity' => 'sometimes|numeric|min:0',
            'cost_per_unit' => 'sometimes|numeric|min:0',
        ]);

        $item->fill($request->only([
            'item_name', 'category', 'quantity', 'unit', 'low_stock_alert', 'cost_per_unit', 'notes'
        ]));
        $item->save();

        return response()->json(['success' => true, 'data' => $item]);
    }

    public function destroy($id)
    {
        $item = Inventory::find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item not found'], 404);
        }
        $item->delete();
        return response()->json(['success' => true, 'message' => 'Item deleted']);
    }
}
