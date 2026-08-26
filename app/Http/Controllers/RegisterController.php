<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\User;
use App\Services\PlanLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegister()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $restaurant = $user->restaurant_id ? Restaurant::find($user->restaurant_id) : null;
            if ($restaurant && !$restaurant->onboarding_completed) {
                return redirect()->route('onboarding.index');
            }
            return redirect()->route('pos.index');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'restaurant_name' => 'required|string|max:255',
            'name'            => 'required|string|max:255',
            'email'           => 'required|string|email|max:255|unique:users,email',
            'phone'           => 'required|string|max:20',
            'password'        => 'required|string|min:6|confirmed',
            'terms'           => 'accepted',
        ], [
            'restaurant_name.required' => 'Please enter your restaurant or business name.',
            'name.required'            => 'Please enter your full owner name.',
            'email.required'           => 'Please enter a valid email address.',
            'email.email'              => 'The email format is invalid.',
            'email.unique'             => 'This email address is already registered. Please sign in.',
            'phone.required'           => 'Please enter your mobile phone number.',
            'password.required'        => 'Please create a secure password.',
            'password.min'             => 'Password must be at least 6 characters.',
            'password.confirmed'       => 'Password confirmation does not match.',
            'terms.accepted'           => 'You must agree to the Terms of Service & Privacy Policy to create an account.',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            // 1. Create Restaurant Tenant
            $restaurant = Restaurant::create([
                'name'                 => $validated['restaurant_name'],
                'address'              => 'Please update address in settings',
                'phone'                => $validated['phone'],
                'gstin'                => '',
                'gst_rate'             => 5.00,
                'service_charge'       => 5.00,
                'plan_type'            => 'free',
                'subscription_status'  => 'trial',
                'onboarding_completed' => false,
                'plan_limits'          => PlanLimitService::getDefaultPlanLimits('free'),
                'trial_ends_at'        => now()->addDays(14),
                'settings'             => [
                    'currency'               => 'INR',
                    'soundbox_lang'          => 'gu',
                    'printer_type'           => 'SYSTEM_DEFAULT',
                    'printer_name'           => '',
                    'auto_print_kot'         => false,
                    'receipt_footer'         => 'Thank you for dining with us!',
                    'point_earn_rate'        => '100',
                    'point_redeem_value'     => '1',
                    'min_redeem_points'      => '50',
                    'welcome_discount'       => '15',
                    'promo_banner_text'      => 'Welcome to ' . $validated['restaurant_name']
                ]
            ]);

            // 2. Generate clean username & random 4-digit PIN for owner
            $cleanSlug = Str::slug($validated['name']);
            if (empty($cleanSlug)) $cleanSlug = 'owner';
            $username = $cleanSlug . rand(100, 999);
            
            // Ensure unique username
            while (User::where('username', $username)->exists()) {
                $username = $cleanSlug . rand(1000, 9999);
            }

            $pin = (string) rand(1000, 9999);

            // 3. Create Admin Owner Account
            $user = User::create([
                'name'          => $validated['name'],
                'username'      => $username,
                'email'         => $validated['email'],
                'phone'         => $validated['phone'],
                'password'      => Hash::make($validated['password']),
                'numeric_pin'   => $pin,
                'role'          => 'admin',
                'restaurant_id' => $restaurant->id,
            ]);

            // 4. Auto Login & Session Setup
            Auth::login($user, remember: true);
            session([
                'last_username' => $user->username,
                'active_role'   => 'admin'
            ]);
            $request->session()->regenerate();

            if ($request->expectsJson()) {
                return response()->json([
                    'success'  => true,
                    'message'  => 'Restaurant account created successfully!',
                    'redirect' => '/onboarding',
                    'data'     => [
                        'restaurant_id' => $restaurant->id,
                        'user_id'       => $user->id,
                        'username'      => $user->username,
                        'pin'           => $pin,
                    ]
                ], 201);
            }

            return redirect()->route('onboarding.index')->with('success', "Welcome to DineFlow AI, {$user->name}! Let's set up your restaurant.");
        });
    }
}
