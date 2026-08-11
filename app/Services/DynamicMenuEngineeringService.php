<?php

namespace App\Services;

use App\Models\MenuItem;
use Illuminate\Support\Collection;

/**
 * Enterprise Service for Menu Engineering Matrix Categorization
 * (Stars, Plowhorses, Puzzles, Dogs) based on Popularity Index and Profit Margins.
 */
class DynamicMenuEngineeringService
{
    public const QUADRANT_STAR = 'Star';           // High Profitability + High Popularity
    public const QUADRANT_PLOWHORSE = 'Plowhorse'; // Low Profitability + High Popularity
    public const QUADRANT_PUZZLE = 'Puzzle';       // High Profitability + Low Popularity
    public const QUADRANT_DOG = 'Dog';             // Low Profitability + Low Popularity

    /**
     * Categorize menu items into the 4 BCG Menu Engineering Quadrants.
     *
     * @param array $itemsWithSales Array of items with 'name', 'price', 'cost', 'sales_count'
     * @return array Matrix quadrant groupings and actionable recommendations
     */
    public function generateMenuEngineeringMatrix(array $itemsWithSales): array
    {
        if (empty($itemsWithSales)) {
            return [
                'stars' => [],
                'plowhorses' => [],
                'puzzles' => [],
                'dogs' => [],
                'average_margin' => 0.00,
                'average_sales' => 0,
            ];
        }

        $totalSales = 0;
        $totalMargin = 0;
        $processed = [];

        foreach ($itemsWithSales as $item) {
            $price = (float)($item['price'] ?? 0);
            $cost = (float)($item['cost'] ?? ($price * 0.35)); // Default 35% food cost
            $margin = max(0, $price - $cost);
            $salesCount = (int)($item['sales_count'] ?? 0);

            $totalSales += $salesCount;
            $totalMargin += $margin;

            $processed[] = [
                'id' => $item['id'] ?? null,
                'name' => $item['name'] ?? 'Item',
                'category' => $item['category'] ?? 'Mains',
                'price' => $price,
                'cost' => $cost,
                'margin' => round($margin, 2),
                'sales_count' => $salesCount,
            ];
        }

        $count = count($processed);
        $avgSales = $count > 0 ? ($totalSales / $count) : 0;
        $avgMargin = $count > 0 ? ($totalMargin / $count) : 0;

        $stars = [];
        $plowhorses = [];
        $puzzles = [];
        $dogs = [];

        foreach ($processed as $item) {
            $isHighSales = $item['sales_count'] >= $avgSales;
            $isHighMargin = $item['margin'] >= $avgMargin;

            if ($isHighSales && $isHighMargin) {
                $item['quadrant'] = self::QUADRANT_STAR;
                $item['recommendation'] = 'Maintain strict recipe quality and feature prominently on QR menu home banner.';
                $stars[] = $item;
            } elseif ($isHighSales && !$isHighMargin) {
                $item['quadrant'] = self::QUADRANT_PLOWHORSE;
                $item['recommendation'] = 'Increase price slightly by 5-8% or reformulate ingredients to improve margin.';
                $plowhorses[] = $item;
            } elseif (!$isHighSales && $isHighMargin) {
                $item['quadrant'] = self::QUADRANT_PUZZLE;
                $item['recommendation'] = 'Reposition in Recommended section or pair with high-selling combos.';
                $puzzles[] = $item;
            } else {
                $item['quadrant'] = self::QUADRANT_DOG;
                $item['recommendation'] = 'Consider retiring item or replacing with high-demand seasonal alternative.';
                $dogs[] = $item;
            }
        }

        return [
            'stars' => $stars,
            'plowhorses' => $plowhorses,
            'puzzles' => $puzzles,
            'dogs' => $dogs,
            'average_margin' => round($avgMargin, 2),
            'average_sales_volume' => round($avgSales, 1),
            'total_items_analyzed' => $count,
        ];
    }
}
