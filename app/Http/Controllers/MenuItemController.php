<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public function index(Request $request)
    {
        $restaurantId = $request->query('restaurantId');
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Restaurant ID is required'], 400);
        }

        $items = MenuItem::where('restaurant_id', $restaurantId)->get();
        return response()->json(['success' => true, 'data' => $items]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|string',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'category' => 'required|string',
        ]);

        $item = MenuItem::create([
            'restaurant_id' => $request->restaurant_id,
            'name' => $request->name,
            'description' => $request->description ?? '',
            'price' => $request->price,
            'category' => $request->category,
            'image' => $request->image ?? '',
            'is_available' => $request->is_available ?? true,
            'is_veg' => $request->is_veg ?? true,
        ]);

        return response()->json(['success' => true, 'data' => $item], 201);
    }

    public function update(Request $request, $id)
    {
        $item = MenuItem::find($id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Menu item not found'], 404);
        }

        $item->update($request->only([
            'name', 'description', 'price', 'category', 'image', 'is_available', 'is_veg'
        ]));

        return response()->json(['success' => true, 'data' => $item]);
    }

    public function destroy($id)
    {
        $item = MenuItem::find($id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Menu item not found'], 404);
        }

        $item->delete();

        return response()->json(['success' => true, 'message' => 'Menu item deleted']);
    }
}
