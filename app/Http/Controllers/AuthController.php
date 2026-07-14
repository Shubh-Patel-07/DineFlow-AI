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

        // Find user by username
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['login' => 'Invalid username or password']);
        }

        // Store username in session for quick PIN login
        session(['last_username' => $user->username]);
        
        Auth::login($user, $request->boolean('remember'));
        return redirect()->route('pos.index')->with('success', 'Welcome to RestoPOS!');
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
     * Handle PIN login
     */
    public function loginWithPin(Request $request)
    {
        $request->validate([
            'username' => 'required|string|exists:users,username',
            'numeric_pin' => 'required|digits_between:4,6',
        ], [
            'username.required' => 'Username is required',
            'username.exists' => 'User not found',
            'numeric_pin.required' => 'PIN is required',
            'numeric_pin.digits_between' => 'PIN must be between 4-6 digits',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || $user->numeric_pin !== $request->numeric_pin) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['numeric_pin' => 'Invalid PIN']);
        }

        session(['last_username' => $user->username]);
        Auth::login($user, remember: true);
        
        return redirect()->route('pos.index')->with('success', 'Login successful!');
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
