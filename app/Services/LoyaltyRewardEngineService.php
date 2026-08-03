<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;

/**
 * Enterprise Service for Customer Loyalty Points Computation, Tier Progression
 * (Bronze -> Silver -> Gold -> Platinum), Cashback redemption rules, and Retention Scoring.
 */
class LoyaltyRewardEngineService
{
    public const EARN_RATE_DEFAULT = 0.05; // 5 points per ₹100 spent (5%)
    public const REDEMPTION_VALUE_INR = 1.0; // 1 Point = ₹1.00

    public const TIER_BRONZE = 'Bronze';
    public const TIER_SILVER = 'Silver';
    public const TIER_GOLD = 'Gold';
    public const TIER_PLATINUM = 'Platinum';

    /**
     * Compute reward points earned on completing a paid order.
     *
     * @param float $netSpent Net amount paid (excluding tax/discounts)
     * @param string $tier Customer's current membership tier
     * @return int Loyalty points earned
     */
    public function calculateEarnedPoints(float $netSpent, string $tier = self::TIER_BRONZE): int
    {
        $multiplier = match ($tier) {
            self::TIER_PLATINUM => 1.50, // 50% bonus points for VIPs
            self::TIER_GOLD => 1.25,     // 25% bonus points
            self::TIER_SILVER => 1.10,   // 10% bonus points
            default => 1.00,
        };

        $basePoints = ($netSpent * self::EARN_RATE_DEFAULT);
        return (int)floor($basePoints * $multiplier);
    }

    /**
     * Evaluate and upgrade customer membership tier based on lifetime spend.
     *
     * @param Customer $customer Target customer
     * @return array Tier evaluation result
     */
    public function evaluateTierProgression(Customer $customer): array
    {
        $lifetimeSpend = (float)$customer->total_spent;
        $currentTier = $customer->membership_tier ?? self::TIER_BRONZE;

        $newTier = match (true) {
            $lifetimeSpend >= 25000.00 => self::TIER_PLATINUM,
            $lifetimeSpend >= 10000.00 => self::TIER_GOLD,
            $lifetimeSpend >= 3000.00 => self::TIER_SILVER,
            default => self::TIER_BRONZE,
        };

        $upgraded = ($newTier !== $currentTier);

        if ($upgraded) {
            $customer->membership_tier = $newTier;
            $customer->save();
        }

        return [
            'customer_id' => $customer->id,
            'lifetime_spend' => $lifetimeSpend,
            'previous_tier' => $currentTier,
            'current_tier' => $newTier,
            'upgraded' => $upgraded,
        ];
    }

    /**
     * Calculate allowable point redemption on bill checkout.
     *
     * @param Customer $customer
     * @param float $billSubtotal
     * @param int $requestedPointsToRedeem
     * @return array Redemption breakdown
     */
    public function processRedemption(Customer $customer, float $billSubtotal, int $requestedPointsToRedeem): array
    {
        $availablePoints = (int)$customer->loyalty_points;
        $maxAllowedPoints = (int)min($availablePoints, floor($billSubtotal * 0.5)); // Max 50% bill paid via points
        $actualPoints = min($requestedPointsToRedeem, $maxAllowedPoints);
        $discountAmount = $actualPoints * self::REDEMPTION_VALUE_INR;

        return [
            'requested_points' => $requestedPointsToRedeem,
            'points_redeemed' => $actualPoints,
            'discount_amount' => $discountAmount,
            'remaining_points' => $availablePoints - $actualPoints,
        ];
    }
}
