<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
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
                    'is_active' => $u->is_active !== false,
                    'created_at' => $u->created_at ? $u->created_at->toISOString() : null,
                ];
            })
        ]);
    }

    /**
     * Create a new staff member with Email + Password.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:admin,owner,manager,cashier,kitchen,waiter,staff',
            'phone' => 'nullable|string|max:20',
        ], [
            'email.unique' => 'This email address is already registered.',
            'password.confirmed' => 'Password confirmation does not match.',
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

        // Generate clean username from email or name
        $baseUser = explode('@', $validated['email'])[0];
        $username = Str::slug($baseUser);
        if (empty($username)) $username = 'staff';
        $uniqueUsername = $username . '_' . $restaurantId;
        $counter = 1;
        while (User::where('username', $uniqueUsername)->exists()) {
            $uniqueUsername = $username . '_' . $restaurantId . '_' . $counter++;
        }

        $staffUser = new User([
            'name' => $validated['name'],
            'username' => $uniqueUsername,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'] === 'owner' ? 'admin' : $validated['role'],
            'password' => Hash::make($validated['password']),
            'restaurant_id' => $restaurantId,
            'is_active' => true,
        ]);

        $staffUser->save();

        if (class_exists(AuditLog::class)) {
            try {
                AuditLog::record('staff_created', $restaurantId, 'user', $staffUser->id, [
                    'name' => $staffUser->name,
                    'email' => $staffUser->email,
                    'role' => $staffUser->role,
                ]);
            } catch (\Throwable $e) {}
        }

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
                'is_active' => true,
            ],
            'message' => "Staff member \"{$staffUser->name}\" created successfully. They can now login with {$staffUser->email}."
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
            'email' => "sometimes|required|email|max:255|unique:users,email,{$id}",
            'role' => 'sometimes|required|string|in:admin,owner,manager,cashier,kitchen,waiter,staff',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'sometimes|boolean',
        ]);

        if (isset($validated['name'])) $staffUser->name = $validated['name'];
        if (isset($validated['email'])) $staffUser->email = $validated['email'];
        if (isset($validated['role'])) $staffUser->role = $validated['role'] === 'owner' ? 'admin' : $validated['role'];
        if (isset($validated['phone'])) $staffUser->phone = $validated['phone'];
        if (isset($validated['is_active'])) $staffUser->is_active = (bool)$validated['is_active'];

        $staffUser->save();

        if (class_exists(AuditLog::class)) {
            try {
                AuditLog::record('staff_updated', $staffUser->restaurant_id, 'user', $staffUser->id, [
                    'name' => $staffUser->name,
                    'email' => $staffUser->email,
                    'role' => $staffUser->role,
                ]);
            } catch (\Throwable $e) {}
        }

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
                'is_active' => $staffUser->is_active !== false,
            ],
            'message' => 'Staff member updated successfully.'
        ]);
    }

    /**
     * Reset password for a staff member.
     */
    public function resetPassword(Request $request, $id)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.confirmed' => 'Password confirmation does not match.',
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

        if ($user && $user->role === 'manager' && in_array($staffUser->role, ['admin', 'owner'])) {
            return response()->json(['success' => false, 'message' => 'Managers cannot reset administrator passwords.'], 403);
        }

        $staffUser->password = Hash::make($validated['password']);
        $staffUser->save();

        if (class_exists(AuditLog::class)) {
            try {
                AuditLog::record('staff_password_reset', $staffUser->restaurant_id, 'user', $staffUser->id, [
                    'email' => $staffUser->email,
                ]);
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success' => true,
            'message' => "Password for {$staffUser->name} ({$staffUser->email}) has been reset successfully."
        ]);
    }

    /**
     * Toggle staff member active/inactive status.
     */
    public function toggleStatus($request = null, $id = null)
    {
        if ($id === null && $request !== null) {
            if (!is_object($request)) {
                $id = $request;
            } elseif ($request instanceof Request) {
                $id = $request->route('id') ?? $request->input('id');
            }
        }

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

        if (class_exists(AuditLog::class)) {
            try {
                AuditLog::record('staff_status_toggled', $staffUser->restaurant_id, 'user', $staffUser->id, [
                    'is_active' => $staffUser->is_active,
                ]);
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success' => true,
            'is_active' => $staffUser->is_active,
            'message' => "Staff member {$staffUser->name} has been {$statusText}."
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

        $deletedName = $staffUser->name;
        $deletedEmail = $staffUser->email;
        $rId = $staffUser->restaurant_id;

        $staffUser->delete();

        if (class_exists(AuditLog::class)) {
            try {
                AuditLog::record('staff_deleted', $rId, 'user', (int)$id, [
                    'name' => $deletedName,
                    'email' => $deletedEmail,
                ]);
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success' => true,
            'message' => "Staff member \"{$deletedName}\" removed successfully."
        ]);
    }
}