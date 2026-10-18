<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\WastageLog;
use App\Models\Inventory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RecipeReportController extends Controller
{
    /**
     * Get all recipes with food costing analysis.
     */
    public function getRecipes(Request $request)
    {
        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? null);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized restaurant.'], 403);
        }

        $menuItems = MenuItem::where('restaurant_id', $restaurantId)
            ->with(['recipes.inventory'])
            ->get();

        $inventoryList = Inventory::where('restaurant_id', $restaurantId)->get();

        $recipeData = [];
        foreach ($menuItems as $item) {
            $rawCost = 0;
            $ingredients = [];

            foreach ($item->recipes as $r) {
                $costPerUnit = floatval($r->inventory->cost_per_unit ?? 0);
                $qty = floatval($r->quantity_required);
                $itemCost = $costPerUnit * $qty;
                $rawCost += $itemCost;

                $ingredients[] = [
                    'id' => $r->id,
                    'inventory_id' => $r->inventory_id,
                    'ingredient_name' => $r->inventory->item_name ?? 'Unknown',
                    'quantity_required' => $qty,
                    'unit' => $r->unit,
                    'cost_per_unit' => $costPerUnit,
                    'total_ingredient_cost' => round($itemCost, 2),
                ];
            }

            $sellingPrice = floatval($item->price);
            $grossProfit = max(0, $sellingPrice - $rawCost);
            $marginPct = $sellingPrice > 0 ? round(($grossProfit / $sellingPrice) * 100, 1) : 0;

            $recipeData[] = [
                'menu_item_id' => $item->id,
                'menu_item_name' => $item->name,
                'category' => $item->category,
                'selling_price' => $sellingPrice,
                'raw_food_cost' => round($rawCost, 2),
                'gross_profit' => round($grossProfit, 2),
                'margin_percentage' => $marginPct,
                'has_recipe' => count($ingredients) > 0,
                'ingredients' => $ingredients,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $recipeData,
            'inventory' => $inventoryList,
        ]);
    }

    /**
     * Save recipe ingredients for a menu item.
     */
    public function saveRecipe(Request $request)
    {
        $request->validate([
            'menu_item_id' => 'required',
            'ingredients' => 'required|array',
        ]);

        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? null);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized restaurant.'], 403);
        }

        $menuItemId = $request->menu_item_id;

        DB::transaction(function () use ($restaurantId, $menuItemId, $request) {
            // Delete old recipe mapping for this item
            Recipe::where('restaurant_id', $restaurantId)->where('menu_item_id', $menuItemId)->delete();

            foreach ($request->ingredients as $ing) {
                if (!empty($ing['inventory_id']) && floatval($ing['quantity_required'] ?? 0) > 0) {
                    Recipe::create([
                        'restaurant_id' => $restaurantId,
                        'menu_item_id' => $menuItemId,
                        'inventory_id' => $ing['inventory_id'],
                        'quantity_required' => floatval($ing['quantity_required']),
                        'unit' => $ing['unit'] ?? 'pcs',
                    ]);
                }
            }

            AuditLog::record(
                'recipe_updated',
                (int)$restaurantId,
                'menu_item',
                $menuItemId,
                ['ingredients_count' => count($request->ingredients)]
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'Recipe saved successfully! Raw materials will be auto-deducted on sale.',
        ]);
    }

    /**
     * Get Wastage logs.
     */
    public function getWastageLogs(Request $request)
    {
        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? null);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized restaurant.'], 403);
        }

        $logs = WastageLog::where('restaurant_id', $restaurantId)
            ->with('inventory')
            ->latest()
            ->take(50)
            ->get();

        $totalLoss = $logs->sum('cost_loss');

        return response()->json([
            'success' => true,
            'total_loss' => round($totalLoss, 2),
            'data' => $logs,
        ]);
    }

    /**
     * Record food wastage and deduct from inventory.
     */
    public function storeWastage(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required',
            'quantity' => 'required|numeric|min:0.01',
            'reason' => 'required|string',
        ]);

        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? null);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized restaurant.'], 403);
        }

        $inv = Inventory::where('restaurant_id', $restaurantId)->find($request->inventory_id);
        if (!$inv) {
            return response()->json(['success' => false, 'message' => 'Inventory item not found.'], 404);
        }

        $qty = floatval($request->quantity);
        $costLoss = $qty * floatval($inv->cost_per_unit ?: 0);

        // Deduct from inventory
        if ($inv->quantity >= $qty) {
            $inv->decrement('quantity', $qty);
        } else {
            $inv->update(['quantity' => 0]);
        }

        $log = WastageLog::create([
            'restaurant_id' => $restaurantId,
            'inventory_id' => $inv->id,
            'quantity' => $qty,
            'unit' => $request->unit ?: $inv->unit,
            'reason' => $request->reason,
            'cost_loss' => round($costLoss, 2),
            'recorded_by' => $user->name ?? 'Staff',
        ]);

        AuditLog::record(
            'wastage_recorded',
            (int)$restaurantId,
            'inventory',
            $inv->id,
            ['item' => $inv->item_name, 'qty' => $qty, 'loss' => $costLoss, 'reason' => $request->reason]
        );

        return response()->json([
            'success' => true,
            'message' => "Wastage recorded: {$qty} {$inv->unit} of '{$inv->item_name}' (Loss: ₹{$costLoss}).",
            'data' => $log,
        ]);
    }

    /**
     * Get GST & Tax Report (B2B & B2C GSTR-1 compliant).
     */
    public function getGstSummary(Request $request)
    {
        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? null);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized restaurant.'], 403);
        }

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $orders = Order::where('restaurant_id', $restaurantId)
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();

        $totalTaxable = 0;
        $totalCgst = 0;
        $totalSgst = 0;
        $totalGross = 0;
        $b2bCount = 0;
        $b2cCount = 0;

        $invoiceList = [];

        foreach ($orders as $ord) {
            $gross = floatval($ord->total_amount);
            // In standard Indian restaurant GST (5% composite tax included in gross), Taxable = Gross / 1.05
            $taxable = round($gross / 1.05, 2);
            $gstTotal = round($gross - $taxable, 2);
            $cgst = round($gstTotal / 2, 2);
            $sgst = round($gstTotal - $cgst, 2);

            $isB2b = !empty($ord->customer_gstin);
            if ($isB2b) {
                $b2bCount++;
            } else {
                $b2cCount++;
            }

            $totalTaxable += $taxable;
            $totalCgst += $cgst;
            $totalSgst += $sgst;
            $totalGross += $gross;

            $invoiceList[] = [
                'order_id' => $ord->id,
                'invoice_number' => 'INV-' . str_pad($ord->id, 5, '0', STR_PAD_LEFT),
                'date' => $ord->created_at->format('d/m/Y H:i'),
                'customer_name' => $ord->customer_name ?: 'Walk-in Guest',
                'customer_gstin' => $ord->customer_gstin ?: '-',
                'customer_company' => $ord->customer_company ?: '-',
                'invoice_type' => $isB2b ? 'B2B' : 'B2C',
                'taxable_amount' => $taxable,
                'cgst' => $cgst,
                'sgst' => $sgst,
                'total_tax' => $gstTotal,
                'total_amount' => $gross,
                'payment_method' => strtoupper($ord->payment_method ?: 'CASH'),
            ];
        }

        return response()->json([
            'success' => true,
            'summary' => [
                'period' => $startDate->format('d M Y') . ' to ' . $endDate->format('d M Y'),
                'total_invoices' => count($orders),
                'b2b_invoices' => $b2bCount,
                'b2c_invoices' => $b2cCount,
                'total_taxable_value' => round($totalTaxable, 2),
                'total_cgst' => round($totalCgst, 2),
                'total_sgst' => round($totalSgst, 2),
                'total_tax' => round($totalCgst + $totalSgst, 2),
                'total_gross_turnover' => round($totalGross, 2),
            ],
            'invoices' => $invoiceList,
        ]);
    }

    /**
     * One-click CSV Export for CA & GSTR-1 Filing.
     */
    public function exportGstCsv(Request $request)
    {
        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? null);
        if (!$restaurantId) {
            abort(403, 'Unauthorized restaurant.');
        }

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $orders = Order::where('restaurant_id', $restaurantId)
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();

        $csvFileName = 'GSTR1_Sales_Report_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$csvFileName}\"",
        ];

        $callback = function () use ($orders) {
            $handle = fopen('php://output', 'w');
            // GSTR-1 Headers
            fputcsv($handle, [
                'Invoice Number',
                'Invoice Date',
                'Invoice Type',
                'Customer GSTIN',
                'Legal Trade Name',
                'Customer Phone',
                'Place of Supply',
                'Taxable Value (INR)',
                'CGST Rate %',
                'CGST Amount (INR)',
                'SGST Rate %',
                'SGST Amount (INR)',
                'Total Tax (INR)',
                'Invoice Value (INR)',
                'Payment Mode',
            ]);

            foreach ($orders as $ord) {
                $gross = floatval($ord->total_amount);
                $taxable = round($gross / 1.05, 2);
                $gstTotal = round($gross - $taxable, 2);
                $cgst = round($gstTotal / 2, 2);
                $sgst = round($gstTotal - $cgst, 2);
                $isB2b = !empty($ord->customer_gstin);

                fputcsv($handle, [
                    'INV-' . str_pad($ord->id, 5, '0', STR_PAD_LEFT),
                    $ord->created_at->format('d-m-Y'),
                    $isB2b ? 'B2B' : 'B2C Large/Small',
                    $ord->customer_gstin ?: 'N/A',
                    $ord->customer_company ?: ($ord->customer_name ?: 'Consumer'),
                    $ord->customer_phone ?: 'N/A',
                    '24-Gujarat',
                    number_format($taxable, 2, '.', ''),
                    '2.5%',
                    number_format($cgst, 2, '.', ''),
                    '2.5%',
                    number_format($sgst, 2, '.', ''),
                    number_format($gstTotal, 2, '.', ''),
                    number_format($gross, 2, '.', ''),
                    strtoupper($ord->payment_method ?: 'CASH'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
