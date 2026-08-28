<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle username/password login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username is required',
            'password.required' => 'Password is required',
        ]);

        // Find user by username or email
        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['login' => 'Invalid username or password']);
        }

        // Store username and role in session for RBAC
        if ($request->hasSession()) {
            session(['last_username' => $user->username, 'active_role' => $user->role ?? $user->username]);
            $request->session()->regenerate();
        }
        
        Auth::login($user, $request->boolean('remember'));

        $role = strtolower(trim($user->role ?? $user->username ?? ''));
        if ($role === 'superadmin') {
            return redirect('/superadmin');
        } elseif ($role === 'customer') {
            $restId = $user->restaurant_id;
            if (!$restId) {
                return redirect('/');
            }
            return redirect("/customer/menu?restaurantId={$restId}&tableId=1");
        } elseif ($role === 'kitchen' || $role === 'chef') {
            return redirect('/kitchen');
        } elseif ($role === 'admin' || $role === 'owner' || $role === 'manager') {
            return redirect('/dashboard');
        }

        return redirect('/pos')->with('success', 'Welcome to DineFlow AI POS!');
    }

    /**
     * Show PIN login form
     */
    public function showPinLogin()
    {
        $lastUsername = session('last_username');
        
        if (!$lastUsername) {
            return redirect()->route('login')->with('info', 'Please login with username and password first');
        }

        return view('auth.pin-login', ['username' => $lastUsername]);
    }

    /**
     * Handle PIN login (Tenant-Aware and Hash-Secure)
     */
    public function loginWithPin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'numeric_pin' => 'required|string|min:4|max:8',
        ], [
            'username.required' => 'Username or identifier is required',
            'numeric_pin.required' => 'Quick PIN is required',
        ]);

        $query = User::where(function ($q) use ($request) {
            $q->where('username', $request->username)
              ->orWhere('email', $request->username)
              ->orWhere('phone', $request->username);
        });

        if ($request->filled('restaurant_id')) {
            $query->where('restaurant_id', $request->restaurant_id);
        }

        $user = $query->first();

        if (!$user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Staff account not found.'], 404);
            }
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['numeric_pin' => 'User account not found.']);
        }

        if ($user->is_active === false) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'This staff account is deactivated.'], 403);
            }
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['numeric_pin' => 'This staff account has been deactivated. Please contact your administrator.']);
        }

        // Verify PIN securely using User::verifyPin (handles bcrypt & legacy)
        if (!$user->verifyPin($request->numeric_pin)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Invalid Quick PIN.'], 401);
            }
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['numeric_pin' => 'Invalid Quick PIN.']);
        }

        if ($request->hasSession()) {
            session([
                'last_username' => $user->username,
                'active_role' => $user->role ?? $user->username,
                'restaurant_id' => $user->restaurant_id,
            ]);
            $request->session()->regenerate();
        }

        Auth::login($user, remember: true);
        
        $role = strtolower(trim($user->role ?? $user->username ?? ''));
        $targetUrl = '/pos';
        if ($role === 'superadmin') {
            $targetUrl = '/superadmin';
        } elseif ($role === 'customer') {
            $restId = $user->restaurant_id;
            $targetUrl = $restId ? "/customer/menu?restaurantId={$restId}&tableId=1" : '/';
        } elseif ($role === 'kitchen' || $role === 'chef') {
            $targetUrl = '/kitchen';
        } elseif ($role === 'admin' || $role === 'owner' || $role === 'manager') {
            $targetUrl = '/dashboard';
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'redirect' => $targetUrl,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                    'restaurant_id' => $user->restaurant_id,
                ],
                'message' => 'Login successful!'
            ]);
        }

        return redirect($targetUrl)->with('success', 'Login successful!');
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
