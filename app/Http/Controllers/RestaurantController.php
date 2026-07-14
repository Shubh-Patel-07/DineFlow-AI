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
}
