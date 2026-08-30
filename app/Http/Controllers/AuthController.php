<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show primary login form (Email + Password)
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle email/username + password primary login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Email or Username is required.',
            'password.required' => 'Password is required.',
        ]);

        // Find user by email, username, or phone
        $identifier = trim($request->username);
        $user = User::where('email', $identifier)
            ->orWhere('username', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Invalid email/username or password.'], 401);
            }
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['login' => 'Invalid email or password.']);
        }

        // Check active status
        if ($user->is_active === false) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'This staff account has been deactivated. Please contact your restaurant administrator.'], 403);
            }
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['login' => 'This staff account has been deactivated. Please contact your restaurant administrator.']);
        }

        Auth::login($user, $request->boolean('remember'));

        // Store username and role in session for tenant RBAC
        if ($request->hasSession()) {
            session([
                'last_username' => $user->username,
                'active_role' => $user->role ?? $user->username,
                'restaurant_id' => $user->restaurant_id,
            ]);
            $request->session()->regenerate();
        }

        // Record Audit Log
        if (class_exists(AuditLog::class)) {
            try {
                AuditLog::record('staff_login', $user->restaurant_id, 'user', $user->id, [
                    'email' => $user->email,
                    'role' => $user->role,
                    'method' => 'password'
                ]);
            } catch (\Throwable $e) {
                // Non-blocking audit log
            }
        }

        $role = strtolower(trim($user->role ?? $user->username ?? ''));
        $redirectUrl = '/pos';

        if ($role === 'superadmin') {
            $redirectUrl = '/superadmin';
        } elseif ($role === 'customer') {
            $restId = $user->restaurant_id;
            $redirectUrl = $restId ? "/customer/menu?restaurantId={$restId}&tableId=1" : '/';
        } else {
            // For restaurant staff, check if restaurant is suspended or expired
            if ($user->restaurant_id) {
                $restaurant = \App\Models\Restaurant::find($user->restaurant_id);
                if ($restaurant) {
                    $status = strtolower(trim($restaurant->subscription_status ?? 'active'));
                    if (in_array($status, ['suspended', 'expired', 'cancelled'])) {
                        $redirectUrl = '/subscription/status';
                    } elseif ($role === 'kitchen' || $role === 'chef') {
                        $redirectUrl = '/kitchen';
                    } elseif ($role === 'admin' || $role === 'owner' || $role === 'manager') {
                        $redirectUrl = '/dashboard';
                    } else {
                        $redirectUrl = '/pos';
                    }
                }
            } elseif ($role === 'kitchen' || $role === 'chef') {
                $redirectUrl = '/kitchen';
            } elseif ($role === 'admin' || $role === 'owner' || $role === 'manager') {
                $redirectUrl = '/dashboard';
            }
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'redirect' => $redirectUrl,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'restaurant_id' => $user->restaurant_id,
                ],
                'message' => 'Logged in successfully.'
            ]);
        }

        return redirect($redirectUrl)->with('success', 'Welcome to DineFlow AI!');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'Logged out successfully');
    }

    /**
     * Switch user - go back to login
     */
    public function switchUser()
    {
        session()->forget('last_username');
        Auth::logout();
        
        return redirect()->route('login');
    }
}
