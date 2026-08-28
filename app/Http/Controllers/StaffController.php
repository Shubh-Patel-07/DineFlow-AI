<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\PlanLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    /**
     * List all staff members for the authenticated restaurant tenant.
     */
    public function index(Request $request, $restaurantId = null)
    {
        $user = auth()->user();
        if ($user && $user->role !== 'superadmin' && $restaurantId && (int)$restaurantId !== (int)$user->restaurant_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized tenant staff access.'], 403);
        }

        $rId = ($user && $user->role === 'superadmin')
            ? ($restaurantId ?? $request->query('restaurant_id') ?? $user->restaurant_id)
            : ($user ? $user->restaurant_id : ($restaurantId ?? $request->query('restaurant_id')));

        if (!$rId) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $staffList = User::where('restaurant_id', $rId)
            ->whereIn('role', ['admin', 'owner', 'manager', 'cashier', 'kitchen', 'waiter', 'staff'])
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $staffList->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'username' => $u->username,
                    'email' => $u->email,
                    'phone' => $u->phone,
                    'role' => $u->role,
                    'restaurant_id' => $u->restaurant_id,
                    'has_pin' => !empty($u->numeric_pin),
                    'is_active' => $u->is_active !== false,
                    'created_at' => $u->created_at ? $u->created_at->toISOString() : null,
                ];
            })
        ]);
    }

    /**
     * Create a new staff member within the authenticated restaurant tenant.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|in:admin,owner,manager,cashier,kitchen,waiter,staff',
            'numeric_pin' => 'required|string|min:4|max:8|regex:/^[0-9]+$/',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ], [
            'numeric_pin.regex' => 'The Quick PIN must contain only digits (0-9).',
        ]);

        $user = auth()->user();
        $restaurantId = ($user && $user->role !== 'superadmin') ? $user->restaurant_id : ($request->input('restaurant_id') ?? $user?->restaurant_id);

        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Restaurant ID is required'], 422);
        }

        if ($user && $user->role === 'manager' && in_array($validated['role'], ['admin', 'owner'])) {
            return response()->json(['success' => false, 'message' => 'Managers cannot create administrator accounts.'], 403);
        }

        $limitCheck = PlanLimitService::canCreateStaff($restaurantId);
        if (!$limitCheck['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $limitCheck['message'],
                'limit_exceeded' => true,
                'details' => $limitCheck
            ], 422);
        }

        // Check PIN collision within the same restaurant tenant
        $existingStaff = User::where('restaurant_id', $restaurantId)->get();
        foreach ($existingStaff as $st) {
            if ($st->verifyPin($validated['numeric_pin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'This Quick PIN is already assigned to another staff member in your restaurant.'
                ], 422);
            }
        }

        // Generate unique username within tenant
        $slug = Str::slug($validated['name']);
        if (empty($slug)) $slug = 'staff';
        $username = $slug . '_' . $restaurantId . '_' . rand(100, 999);
        while (User::where('username', $username)->exists()) {
            $username = $slug . '_' . $restaurantId . '_' . rand(1000, 9999);
        }

        $email = $validated['email'] ?? ($username . '@dineflow.local');

        $staffUser = new User([
            'name' => $validated['name'],
            'username' => $username,
            'email' => $email,
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'] === 'owner' ? 'admin' : $validated['role'],
            'restaurant_id' => $restaurantId,
            'is_active' => true,
        ]);
        $staffUser->setPin($validated['numeric_pin']);
        $staffUser->save();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $staffUser->id,
                'name' => $staffUser->name,
                'username' => $staffUser->username,
                'email' => $staffUser->email,
                'phone' => $staffUser->phone,
                'role' => $staffUser->role,
                'restaurant_id' => $staffUser->restaurant_id,
                'has_pin' => true,
                'is_active' => true,
            ],
            'message' => "Staff member \"{$staffUser->name}\" created successfully."
        ], 201);
    }

    /**
     * Update an existing staff member (scoped to tenant).
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $query = User::query();
        if ($user && $user->role !== 'superadmin' && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $staffUser = $query->find($id);

        if (!$staffUser) {
            return response()->json(['success' => false, 'message' => 'Staff member not found in your restaurant'], 404);
        }

        if ($user && $user->role === 'manager' && in_array($staffUser->role, ['admin', 'owner'])) {
            return response()->json(['success' => false, 'message' => 'Managers cannot modify administrator accounts.'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'role' => 'sometimes|required|string|in:admin,owner,manager,cashier,kitchen,waiter,staff',
            'numeric_pin' => 'nullable|string|min:4|max:8|regex:/^[0-9]+$/',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        if (isset($validated['name'])) $staffUser->name = $validated['name'];
        if (isset($validated['role'])) $staffUser->role = $validated['role'] === 'owner' ? 'admin' : $validated['role'];
        if (isset($validated['phone'])) $staffUser->phone = $validated['phone'];
        if (isset($validated['email'])) $staffUser->email = $validated['email'];
        if (isset($validated['is_active'])) $staffUser->is_active = (bool)$validated['is_active'];

        if (!empty($validated['numeric_pin'])) {
            // Check collision with other staff in same restaurant
            $peers = User::where('restaurant_id', $staffUser->restaurant_id)
                ->where('id', '!=', $staffUser->id)
                ->get();
            foreach ($peers as $peer) {
                if ($peer->verifyPin($validated['numeric_pin'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This Quick PIN is already assigned to another staff member in your restaurant.'
                    ], 422);
                }
            }
            $staffUser->setPin($validated['numeric_pin']);
        }

        $staffUser->save();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $staffUser->id,
                'name' => $staffUser->name,
                'username' => $staffUser->username,
                'email' => $staffUser->email,
                'phone' => $staffUser->phone,
                'role' => $staffUser->role,
                'restaurant_id' => $staffUser->restaurant_id,
                'has_pin' => !empty($staffUser->numeric_pin),
                'is_active' => $staffUser->is_active !== false,
            ],
            'message' => 'Staff member updated successfully.'
        ]);
    }

    /**
     * Delete a staff member (scoped to tenant).
     */
    public function destroy($id)
    {
        $user = auth()->user();
        if ($user && (int)$user->id === (int)$id) {
            return response()->json(['success' => false, 'message' => 'You cannot delete your own active administrator account.'], 400);
        }

        $query = User::query();
        if ($user && $user->role !== 'superadmin' && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $staffUser = $query->find($id);

        if (!$staffUser) {
            return response()->json(['success' => false, 'message' => 'Staff member not found in your restaurant'], 404);
        }

        if ($user && $user->role === 'manager' && in_array($staffUser->role, ['admin', 'owner'])) {
            return response()->json(['success' => false, 'message' => 'Managers cannot delete administrator accounts.'], 403);
        }

        $staffUser->delete();

        return response()->json([
            'success' => true,
            'message' => 'Staff member removed successfully.'
        ]);
    }

    /**
     * Reset Quick PIN for a staff member.
     */
    public function resetPin(Request $request, $id)
    {
        $validated = $request->validate([
            'numeric_pin' => 'required|string|min:4|max:8|regex:/^[0-9]+$/',
        ]);

        $user = auth()->user();
        $query = User::query();
        if ($user && $user->role !== 'superadmin' && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $staffUser = $query->find($id);

        if (!$staffUser) {
            return response()->json(['success' => false, 'message' => 'Staff member not found in your restaurant'], 404);
        }

        // Check collision in tenant
        $peers = User::where('restaurant_id', $staffUser->restaurant_id)
            ->where('id', '!=', $staffUser->id)
            ->get();
        foreach ($peers as $peer) {
            if ($peer->verifyPin($validated['numeric_pin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'This Quick PIN is already assigned to another staff member.'
                ], 422);
            }
        }

        $staffUser->setPin($validated['numeric_pin']);
        $staffUser->save();

        return response()->json([
            'success' => true,
            'message' => "Quick PIN for {$staffUser->name} has been reset successfully."
        ]);
    }

    /**
     * Toggle staff member active/inactive status.
     */
    public function toggleStatus($id)
    {
        $user = auth()->user();
        if ($user && (int)$user->id === (int)$id) {
            return response()->json(['success' => false, 'message' => 'You cannot deactivate your own active account.'], 400);
        }

        $query = User::query();
        if ($user && $user->role !== 'superadmin' && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $staffUser = $query->find($id);

        if (!$staffUser) {
            return response()->json(['success' => false, 'message' => 'Staff member not found in your restaurant'], 404);
        }

        $staffUser->is_active = !($staffUser->is_active !== false);
        $staffUser->save();

        $statusText = $staffUser->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'success' => true,
            'is_active' => $staffUser->is_active,
            'message' => "Staff member {$staffUser->name} has been {$statusText}."
        ]);
    }

    /**
     * Verify staff PIN for POS quick staff switching (Tenant-Scoped).
     */
    public function verifyStaffPin(Request $request)
    {
        $request->validate([
            'numeric_pin' => 'required|string',
        ]);

        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : $request->input('restaurant_id');

        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Restaurant context required'], 403);
        }

        $staffMembers = User::where('restaurant_id', $restaurantId)
            ->where('is_active', '!=', false)
            ->get();

        $matchedStaff = null;
        foreach ($staffMembers as $st) {
            if ($st->verifyPin($request->numeric_pin)) {
                $matchedStaff = $st;
                break;
            }
        }

        if (!$matchedStaff) {
            return response()->json(['success' => false, 'message' => 'Invalid staff PIN for this restaurant.'], 401);
        }

        if ($request->hasSession()) {
            session(['active_role' => $matchedStaff->role, 'last_username' => $matchedStaff->username]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $matchedStaff->id,
                'name' => $matchedStaff->name,
                'role' => $matchedStaff->role,
                'username' => $matchedStaff->username,
            ],
            'message' => "Logged in as {$matchedStaff->name} ({$matchedStaff->role})"
        ]);
    }
}