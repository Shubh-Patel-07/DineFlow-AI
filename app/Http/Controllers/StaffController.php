<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    public function index(Request $request, $restaurantId = null)
    {
        $rId = $restaurantId ?? $request->query('restaurant_id', '1');
        $staff = User::where('restaurant_id', $rId)
            ->whereIn('role', ['admin', 'manager', 'cashier', 'kitchen', 'waiter', 'staff'])
            ->orderBy('id', 'desc')
            ->get()
            ->makeVisible(['numeric_pin']);

        return response()->json([
            'success' => true,
            'data' => $staff
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'nullable|string',
            'name' => 'required|string|max:255',
            'role' => 'required|string|in:admin,manager,cashier,kitchen,waiter,staff',
            'numeric_pin' => 'required|string|min:4|max:8',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $restaurantId = $validated['restaurant_id'] ?? $request->input('restaurant_id', '1');

        $existingPin = User::where('restaurant_id', $restaurantId)
            ->where('numeric_pin', $validated['numeric_pin'])
            ->first();
        if ($existingPin) {
            return response()->json([
                'success' => false,
                'message' => 'This PIN is already assigned to another staff member in your restaurant.'
            ], 422);
        }

        $username = Str::slug($validated['name']) . rand(100, 999);
        $email = $validated['email'] ?? ($username . '@dineflow.local');

        $user = User::create([
            'name' => $validated['name'],
            'username' => $username,
            'email' => $email,
            'role' => $validated['role'],
            'numeric_pin' => $validated['numeric_pin'],
            'restaurant_id' => $restaurantId,
            'password' => Hash::make($validated['numeric_pin']),
        ]);

        return response()->json([
            'success' => true,
            'data' => $user->makeVisible(['numeric_pin']),
            'message' => 'Staff member created successfully'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Staff member not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'role' => 'sometimes|required|string|in:admin,manager,cashier,kitchen,waiter,staff',
            'numeric_pin' => 'sometimes|required|string|min:4|max:8',
        ]);

        if (isset($validated['numeric_pin'])) {
            $validated['password'] = Hash::make($validated['numeric_pin']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Staff member updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Staff member not found'], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Staff member deleted successfully'
        ]);
    }
}