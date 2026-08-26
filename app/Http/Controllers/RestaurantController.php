<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
        ]);

        $restaurant = Restaurant::create([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'logo' => $request->logo ?? '',
        ]);

        return response()->json(['success' => true, 'data' => $restaurant], 201);
    }

    public function show($id)
    {
        $restaurant = Restaurant::find($id);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $restaurant]);
    }

    public function getSettings($id)
    {
        $user = auth()->user();
        if ($user && $user->restaurant_id && strval($user->restaurant_id) !== strval($id) && strtolower(trim($user->role ?? '')) !== 'superadmin') {
            return response()->json(['success' => false, 'message' => 'Forbidden: You cannot access settings for another restaurant.'], 403);
        }

        $restaurant = Restaurant::find($id);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $restaurant->name,
                'address' => $restaurant->address,
                'phone' => $restaurant->phone,
                'logo' => $restaurant->logo,
                'gstin' => $restaurant->gstin,
                'gst_rate' => $restaurant->gst_rate,
                'service_charge' => $restaurant->service_charge,
                'settings' => $restaurant->settings ?? []
            ]
        ]);
    }

    public function updateSettings(Request $request, $id)
    {
        $user = auth()->user();
        if ($user && $user->restaurant_id && strval($user->restaurant_id) !== strval($id) && strtolower(trim($user->role ?? '')) !== 'superadmin') {
            return response()->json(['success' => false, 'message' => 'Forbidden: You cannot modify settings for another restaurant.'], 403);
        }

        $restaurant = Restaurant::find($id);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'gstin' => 'nullable|string|max:50',
            'gst_rate' => 'nullable|numeric|min:0|max:100',
            'service_charge' => 'nullable|numeric|min:0|max:100',
            'settings' => 'nullable|array'
        ]);

        $restaurant->update(array_filter($validated, fn($val) => !is_null($val)));

        return response()->json([
            'success' => true,
            'data' => $restaurant,
            'message' => 'Restaurant settings updated successfully'
        ]);
    }
}
