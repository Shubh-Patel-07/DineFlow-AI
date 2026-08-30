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
        $supportEmail = 'billing@tendorabuy.in';
        $waGeneralMessage = urlencode("Hello Tendorabuy Team, I need assistance with my restaurant subscription for '{$restaurant->name}' (ID #{$restaurant->id}). Status: " . strtoupper($status));
        $whatsappUrl = "https://wa.me/917777938338?text={$waGeneralMessage}";

        $waProMessage = urlencode("Hello Tendorabuy Team, I would like to renew / upgrade to the PRO Plan (₹999/mo) for my restaurant '{$restaurant->name}' (ID #{$restaurant->id}). Please send payment link and activate.");
        $waEnterpriseMessage = urlencode("Hello Tendorabuy Team, I would like to enquire about the ENTERPRISE Plan for my restaurant '{$restaurant->name}' (ID #{$restaurant->id}). Please connect with sales.");

        $availablePlans = [
            'free' => [
                'name' => 'Starter Free',
                'tagline' => 'Essential tools for small cafés and quick food stalls',
                'price' => '₹0',
                'period' => '/ month',
                'badge' => 'Standard',
                'popular' => false,
                'tables' => 'Up to 10 Tables',
                'staff' => '5 Staff Logins',
                'orders' => '500 Orders / month',
                'menu_items' => '100 Menu Items',
                'features' => ['Digital QR Menu', 'Basic POS Billing', 'Standard Sales Summary'],
                'cta_text' => 'Choose Starter',
                'cta_url' => "https://wa.me/917777938338?text=" . urlencode("Hello Tendorabuy Team, please activate Starter Free Plan for '{$restaurant->name}' (ID #{$restaurant->id})"),
            ],
            'pro' => [
                'name' => 'Restaurant Pro',
                'tagline' => 'Complete digital command center for high-volume restaurants',
                'price' => '₹999',
                'period' => '/ month',
                'badge' => 'Most Popular',
                'popular' => true,
                'tables' => 'Up to 50 Tables',
                'staff' => '25 Staff Logins (Admin, Cashier, Chef, Waiter)',
                'orders' => '5,000 Orders / month',
                'menu_items' => '500 Menu Items',
                'features' => ['Fast POS Billing & Thermal Receipts', 'Live Kitchen Display (KDS)', 'Real-Time Inventory & Low-Stock Alerts', 'AI Menu Engineering & Demand Forecast', 'Multi-Language KOT & QR Menu'],
                'cta_text' => 'Renew / Upgrade to Pro',
                'cta_url' => "https://wa.me/917777938338?text={$waProMessage}",
            ],
            'enterprise' => [
                'name' => 'Enterprise & Chains',
                'tagline' => 'Custom multi-outlet architecture with priority dedicated support',
                'price' => 'Custom',
                'period' => 'Annual Billing',
                'badge' => 'Chains & Franchises',
                'popular' => false,
                'tables' => '200+ Tables',
                'staff' => '100+ Staff Accounts',
                'orders' => 'Unlimited Orders',
                'menu_items' => '2,000+ Menu Items',
                'features' => ['Multi-Branch Centralized Reporting', 'Custom POS & Payment Gateway Setup', 'Dedicated Account Manager & 24/7 SLA', 'Custom API & ERP Integrations'],
                'cta_text' => 'Contact Enterprise Sales',
                'cta_url' => "https://wa.me/917777938338?text={$waEnterpriseMessage}",
            ],
        ];

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
            'availablePlans' => $availablePlans,
        ]);
    }
}
