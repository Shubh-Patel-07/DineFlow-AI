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
        $user = auth()->user();
        $rId = $user ? $user->restaurant_id : ($restaurantId ?? $request->query('restaurant_id'));
        if (!$rId) {
            return response()->json(['success' => true, 'data' => []]);
        }
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
            'name' => 'required|string|max:255',
            'role' => 'required|string|in:admin,manager,cashier,kitchen,waiter,staff',
            'numeric_pin' => 'required|string|min:4|max:8',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : $request->input('restaurant_id');
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Restaurant ID is required'], 422);
        }

        $limitCheck = \App\Services\PlanLimitService::canCreateStaff($restaurantId);
        if (!$limitCheck['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $limitCheck['message'],
                'limit_exceeded' => true,
                'details' => $limitCheck
            ], 422);
        }

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

        $staffUser = User::create([
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
            'data' => $staffUser->makeVisible(['numeric_pin']),
            'message' => 'Staff member created successfully'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $query = User::query();
        if ($user && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $staffUser = $query->find($id);

        if (!$staffUser) {
            return response()->json(['success' => false, 'message' => 'Staff member not found in your restaurant'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'role' => 'sometimes|required|string|in:admin,manager,cashier,kitchen,waiter,staff',
            'numeric_pin' => 'sometimes|required|string|min:4|max:8',
        ]);

        if (isset($validated['numeric_pin'])) {
            $validated['password'] = Hash::make($validated['numeric_pin']);
        }

        $staffUser->update($validated);

        return response()->json([
            'success' => true,
            'data' => $staffUser,
            'message' => 'Staff member updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        if ($user && strval($user->id) === strval($id)) {
            return response()->json(['success' => false, 'message' => 'You cannot delete your own active administrator account.'], 400);
        }

        $query = User::query();
        if ($user && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $staffUser = $query->find($id);

        if (!$staffUser) {
            return response()->json(['success' => false, 'message' => 'Staff member not found in your restaurant'], 404);
        }

        $staffUser->delete();

        return response()->json([
            'success' => true,
            'message' => 'Staff member deleted successfully'
        ]);
    }
}