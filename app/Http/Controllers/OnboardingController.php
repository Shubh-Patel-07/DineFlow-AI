<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Table;
use App\Models\MenuItem;
use App\Models\User;
use App\Services\PlanLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $restaurant = Restaurant::find($user->restaurant_id);
        if (!$restaurant) {
            abort(404, 'Restaurant profile not found');
        }

        $tablesCount = Table::where('restaurant_id', $restaurant->id)->count();
        $menuItemsCount = MenuItem::where('restaurant_id', $restaurant->id)->count();
        $staffCount = User::where('restaurant_id', $restaurant->id)->count();
        $usageStats = PlanLimitService::getUsageStats($restaurant->id);

        return view('onboarding.index', compact('restaurant', 'user', 'tablesCount', 'menuItemsCount', 'staffCount', 'usageStats'));
    }

    public function complete(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->restaurant_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $restaurant = Restaurant::find($user->restaurant_id);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        $restaurant->onboarding_completed = true;
        $restaurant->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success'  => true,
                'message'  => 'Onboarding completed successfully!',
                'redirect' => '/dashboard'
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Onboarding completed! Welcome to your restaurant command center.');
    }

    public function getUsageStats(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->restaurant_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $stats = PlanLimitService::getUsageStats($user->restaurant_id);
        return response()->json(['success' => true, 'data' => $stats]);
    }
}
