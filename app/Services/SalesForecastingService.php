<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Collection;

/**
 * Advanced Predictive Service for Restaurant Revenue Forecasting, Peak Hours Analysis,
 * Moving Average Sales Trajectories, and Stock Reorder Recommendations.
 */
class SalesForecastingService
{
    /**
     * Compute 7-day Simple Moving Average (SMA) and predict next day revenue.
     *
     * @param array $historicalDailyRevenues Array of past daily revenue numbers
     * @return array Forecasted revenue and confidence metrics
     */
    public function compute7DayRevenueForecast(array $historicalDailyRevenues): array
    {
        $count = count($historicalDailyRevenues);
        if ($count === 0) {
            return [
                'forecasted_revenue' => 0.00,
                'moving_average' => 0.00,
                'trend' => 'neutral',
                'confidence_percentage' => 0,
            ];
        }

        $slice = array_slice($historicalDailyRevenues, -7);
        $movingAvg = array_sum($slice) / count($slice);

        // Linear trend coefficient
        $trend = 'steady';
        if ($count >= 2) {
            $last = end($historicalDailyRevenues);
            $prev = $historicalDailyRevenues[$count - 2];
            if ($last > $prev * 1.05) {
                $trend = 'upward';
            } elseif ($last < $prev * 0.95) {
                $trend = 'downward';
            }
        }

        $forecast = round($movingAvg * ($trend === 'upward' ? 1.04 : ($trend === 'downward' ? 0.96 : 1.0)), 2);

        return [
            'forecasted_revenue' => $forecast,
            'moving_average' => round($movingAvg, 2),
            'trend' => $trend,
            'confidence_percentage' => min(95, 60 + ($count * 5)),
            'historical_days_evaluated' => $count,
        ];
    }

    /**
     * Identify busiest dining peak hours from orders historical timestamps.
     *
     * @param Collection $orders Orders collection
     * @return array Hourly traffic heatmap breakdown
     */
    public function analyzePeakHours(Collection $orders): array
    {
        $hourCounts = array_fill(0, 24, 0);

        foreach ($orders as $order) {
            if ($order->created_at) {
                $hour = (int)$order->created_at->format('H');
                $hourCounts[$hour]++;
            }
        }

        arsort($hourCounts);
        $peakHours = array_slice($hourCounts, 0, 3, true);

        return [
            'peak_rush_hours' => array_keys($peakHours),
            'full_24h_distribution' => $hourCounts,
        ];
    }
}
