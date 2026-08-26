<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public function index(Request $request, $restaurantId = null)
    {
        // For authenticated staff, enforce their own restaurant_id; for public QR, use the route/query param
        $user = auth()->user();
        if ($user && $user->restaurant_id) {
            $restaurantId = (string)$user->restaurant_id;
        } else {
            $restaurantId = (string)($restaurantId ?? $request->query('restaurantId') ?? $request->query('restaurant_id') ?? '1');
        }

        $items = MenuItem::where('restaurant_id', $restaurantId)->get();

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
        ]);

        $user = auth()->user();
        $restaurantId = $user ? (string)$user->restaurant_id : (string)($request->restaurant_id ?? $request->restaurantId);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Restaurant ID is required'], 422);
        }

        $item = MenuItem::create([
            'restaurant_id' => $restaurantId,
            'name' => $request->name,
            'description' => $request->description ?? '',
            'price' => $request->price,
            'category' => $request->category,
            'image' => $request->image ?? '',
            'is_available' => $request->has('is_available') ? filter_var($request->is_available, FILTER_VALIDATE_BOOLEAN) : true,
            'is_veg' => $request->has('is_veg') ? filter_var($request->is_veg, FILTER_VALIDATE_BOOLEAN) : true,
        ]);

        return response()->json(['success' => true, 'data' => $item], 201);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $userTenantId = $user ? $user->restaurant_id : null;

        $query = MenuItem::query();
        if ($userTenantId) {
            $query->where('restaurant_id', $userTenantId);
        }
        $item = $query->find($id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Menu item not found in your restaurant'], 404);
        }

        $request->validate([
            'price' => 'sometimes|numeric|min:0',
        ]);

        $data = $request->only([
            'name', 'description', 'price', 'category', 'image'
        ]);

        if ($request->has('is_available') || $request->has('isAvailable')) {
            $data['is_available'] = filter_var($request->input('is_available', $request->input('isAvailable')), FILTER_VALIDATE_BOOLEAN);
        }
        if ($request->has('is_veg') || $request->has('isVeg')) {
            $data['is_veg'] = filter_var($request->input('is_veg', $request->input('isVeg')), FILTER_VALIDATE_BOOLEAN);
        }

        $item->update($data);

        return response()->json(['success' => true, 'data' => $item]);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $userTenantId = $user ? $user->restaurant_id : null;

        $query = MenuItem::query();
        if ($userTenantId) {
            $query->where('restaurant_id', $userTenantId);
        }
        $item = $query->find($id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Menu item not found in your restaurant'], 404);
        }

        $item->delete();

        return response()->json(['success' => true, 'message' => 'Menu item deleted']);
    }
}

