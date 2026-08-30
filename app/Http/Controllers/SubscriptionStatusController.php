<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SubscriptionStatusController extends Controller
{
    /**
     * Display the professional subscription and suspension status page.
     */
    public function show(Request $request)
    {
        $user = Auth::user() ?? $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        if (strtolower(trim($user->role ?? '')) === 'superadmin') {
            return redirect()->route('superadmin.dashboard');
        }

        if (!$user->restaurant_id) {
            return redirect()->route('login');
        }

        $restaurant = Restaurant::find($user->restaurant_id);
        if (!$restaurant) {
            abort(404, 'Restaurant profile not found');
        }

        $status = strtolower(trim($restaurant->subscription_status ?? 'active'));
        $effectiveExpiry = $restaurant->subscription_expires_at 
            ? Carbon::parse($restaurant->subscription_expires_at) 
            : ($restaurant->trial_ends_at ? Carbon::parse($restaurant->trial_ends_at) : null);

        $graceDays = !is_null($restaurant->grace_period_days) ? intval($restaurant->grace_period_days) : 7;
        $graceEndsAt = $effectiveExpiry ? (clone $effectiveExpiry)->addDays($graceDays) : null;
        $now = Carbon::now();

        // If restaurant is fully active and not expired, redirect to their home
        if ($status === 'active' || $status === 'trial') {
            if ($effectiveExpiry && $now->isAfter($effectiveExpiry)) {
                // Let it evaluate status below
            } else {
                return redirect()->route('dashboard');
            }
        }

        $daysRemainingGrace = 0;
        if ($graceEndsAt && $now->isBefore($graceEndsAt)) {
            $daysRemainingGrace = max(0, $now->diffInDays($graceEndsAt, false));
        }

        // Resolve customer-facing message
        if ($status === 'suspended') {
            $customerMessage = $restaurant->getCustomerFacingSuspensionMessage();
        } elseif ($status === 'grace_period') {
            $customerMessage = $restaurant->getCustomerFacingGracePeriodMessage();
        } else {
            $customerMessage = $restaurant->getCustomerFacingExpiryMessage();
        }

        $supportPhone = '+91 7777938338';
        $supportEmail = 'support@tendorabuy.in';
        $waMessage = urlencode("Hello Tendorabuy Support, I need assistance with my restaurant account (#{$restaurant->id} - {$restaurant->name}). Current Status: " . strtoupper($status));
        $whatsappUrl = "https://wa.me/917777938338?text={$waMessage}";

        return view('subscription.status', [
            'restaurant' => $restaurant,
            'user' => $user,
            'status' => strtoupper($status),
            'statusRaw' => $status,
            'effectiveExpiry' => $effectiveExpiry,
            'graceEndsAt' => $graceEndsAt,
            'daysRemainingGrace' => $daysRemainingGrace,
            'customerMessage' => $customerMessage,
            'suspensionReason' => $restaurant->suspension_reason,
            'suspendedAt' => $restaurant->suspended_at ?? $restaurant->updated_at,
            'supportPhone' => $supportPhone,
            'supportEmail' => $supportEmail,
            'whatsappUrl' => $whatsappUrl,
        ]);
    }
}
