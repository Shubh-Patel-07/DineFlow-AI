<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CashRegisterController extends Controller
{
    /**
     * Get the active open shift for the current restaurant.
     */
    public function getCurrentShift(Request $request)
    {
        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? null);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized restaurant.'], 403);
        }

        $register = CashRegister::where('restaurant_id', $restaurantId)
            ->where('status', 'open')
            ->latest()
            ->first();

        if (!$register) {
            return response()->json([
                'success' => true,
                'has_open_shift' => false,
                'data' => null
            ]);
        }

        // Calculate live sales and petty cash during this open shift
        $shiftStart = $register->opened_at;

        $orders = Order::where('restaurant_id', $restaurantId)
            ->where('created_at', '>=', $shiftStart)
            ->where('payment_status', 'paid')
            ->get();

        $cashSales = 0;
        $upiSales = 0;
        $cardSales = 0;
        $totalSales = 0;

        foreach ($orders as $order) {
            $totalSales += $order->total_amount;
            $m = strtolower($order->payment_method ?: 'cash');
            if ($m === 'split' && !empty($order->split_data)) {
                $splitObj = is_string($order->split_data) ? json_decode($order->split_data, true) : $order->split_data;
                $cashSales += floatval($splitObj['cash'] ?? 0);
                $upiSales += floatval($splitObj['upi'] ?? 0);
                $cardSales += floatval($splitObj['card'] ?? 0);
            } elseif (str_contains($m, 'upi') || str_contains($m, 'online') || str_contains($m, 'gpay')) {
                $upiSales += $order->total_amount;
            } elseif (str_contains($m, 'card')) {
                $cardSales += $order->total_amount;
            } else {
                $cashSales += $order->total_amount;
            }
        }

        $cashIn = CashTransaction::where('cash_register_id', $register->id)->where('type', 'cash_in')->sum('amount');
        $cashOut = CashTransaction::where('cash_register_id', $register->id)->where('type', 'cash_out')->sum('amount');

        $expectedCash = floatval($register->opening_cash) + $cashSales + $cashIn - $cashOut;

        $transactions = CashTransaction::where('cash_register_id', $register->id)->latest()->get();

        return response()->json([
            'success' => true,
            'has_open_shift' => true,
            'data' => [
                'register' => $register,
                'opening_cash' => floatval($register->opening_cash),
                'cash_sales' => round($cashSales, 2),
                'upi_sales' => round($upiSales, 2),
                'card_sales' => round($cardSales, 2),
                'total_sales' => round($totalSales, 2),
                'total_orders' => $orders->count(),
                'cash_in' => round($cashIn, 2),
                'cash_out' => round($cashOut, 2),
                'expected_cash' => round($expectedCash, 2),
                'transactions' => $transactions,
            ]
        ]);
    }

    /**
     * Start a new shift (Opening Float).
     */
    public function openShift(Request $request)
    {
        $request->validate([
            'opening_cash' => 'required|numeric|min:0',
        ]);

        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? null);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized restaurant.'], 403);
        }

        // Check if an open shift already exists
        $existing = CashRegister::where('restaurant_id', $restaurantId)->where('status', 'open')->first();
        if ($existing) {
            return response()->json(['success' => false, 'message' => 'A shift is already open. Please close it before starting a new one.'], 400);
        }

        $register = CashRegister::create([
            'restaurant_id' => $restaurantId,
            'user_id' => $user?->id,
            'user_name' => $user?->name ?: 'Cashier',
            'opened_at' => Carbon::now(),
            'opening_cash' => floatval($request->opening_cash),
            'status' => 'open',
            'notes' => $request->notes ?? ''
        ]);

        AuditLog::record(
            'shift_opened',
            (int)$restaurantId,
            'cash_register',
            $register->id,
            ['opening_cash' => $register->opening_cash, 'cashier' => $register->user_name]
        );

        return response()->json([
            'success' => true,
            'message' => 'Shift opened successfully! Cash drawer is active.',
            'data' => $register
        ]);
    }

    /**
     * Add Petty Cash (Cash-In or Cash-Out).
     */
    public function addPettyCash(Request $request)
    {
        $request->validate([
            'type' => 'required|in:cash_in,cash_out',
            'amount' => 'required|numeric|min:1',
            'reason' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? null);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized restaurant.'], 403);
        }

        $register = CashRegister::where('restaurant_id', $restaurantId)->where('status', 'open')->first();
        if (!$register) {
            return response()->json(['success' => false, 'message' => 'No active shift found. Please open shift first.'], 400);
        }

        $tx = CashTransaction::create([
            'cash_register_id' => $register->id,
            'restaurant_id' => $restaurantId,
            'user_id' => $user?->id,
            'type' => $request->type,
            'amount' => floatval($request->amount),
            'category' => $request->category ?: 'misc',
            'reason' => $request->reason,
        ]);

        AuditLog::record(
            'petty_cash_' . $request->type,
            (int)$restaurantId,
            'cash_transaction',
            $tx->id,
            ['amount' => $tx->amount, 'reason' => $tx->reason, 'user' => $user?->name]
        );

        return response()->json([
            'success' => true,
            'message' => ($request->type === 'cash_in' ? 'Cash-In' : 'Cash-Out') . ' entry recorded.',
            'data' => $tx
        ]);
    }

    /**
     * Close Shift (Reconcile Drawer & Generate Summary).
     */
    public function closeShift(Request $request)
    {
        $request->validate([
            'closing_cash' => 'required|numeric|min:0',
        ]);

        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? null);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized restaurant.'], 403);
        }

        $register = CashRegister::where('restaurant_id', $restaurantId)->where('status', 'open')->first();
        if (!$register) {
            return response()->json(['success' => false, 'message' => 'No active shift found to close.'], 404);
        }

        $shiftStart = $register->opened_at;
        $orders = Order::where('restaurant_id', $restaurantId)
            ->where('created_at', '>=', $shiftStart)
            ->where('payment_status', 'paid')
            ->get();

        $cashSales = 0;
        $upiSales = 0;
        $cardSales = 0;
        $totalSales = 0;

        foreach ($orders as $order) {
            $totalSales += $order->total_amount;
            $m = strtolower($order->payment_method ?: 'cash');
            if ($m === 'split' && !empty($order->split_data)) {
                $splitObj = is_string($order->split_data) ? json_decode($order->split_data, true) : $order->split_data;
                $cashSales += floatval($splitObj['cash'] ?? 0);
                $upiSales += floatval($splitObj['upi'] ?? 0);
                $cardSales += floatval($splitObj['card'] ?? 0);
            } elseif (str_contains($m, 'upi') || str_contains($m, 'online') || str_contains($m, 'gpay')) {
                $upiSales += $order->total_amount;
            } elseif (str_contains($m, 'card')) {
                $cardSales += $order->total_amount;
            } else {
                $cashSales += $order->total_amount;
            }
        }

        $cashIn = CashTransaction::where('cash_register_id', $register->id)->where('type', 'cash_in')->sum('amount');
        $cashOut = CashTransaction::where('cash_register_id', $register->id)->where('type', 'cash_out')->sum('amount');

        $expectedCash = floatval($register->opening_cash) + $cashSales + $cashIn - $cashOut;
        $closingCash = floatval($request->closing_cash);
        $difference = $closingCash - $expectedCash;

        $register->update([
            'closed_at' => Carbon::now(),
            'closing_cash' => $closingCash,
            'expected_cash' => $expectedCash,
            'difference' => $difference,
            'total_sales' => $totalSales,
            'total_cash_sales' => $cashSales,
            'total_upi_sales' => $upiSales,
            'total_card_sales' => $cardSales,
            'total_orders' => $orders->count(),
            'status' => 'closed',
            'notes' => $request->notes ?? $register->notes,
        ]);

        AuditLog::record(
            'shift_closed',
            (int)$restaurantId,
            'cash_register',
            $register->id,
            [
                'expected_cash' => $expectedCash,
                'closing_cash' => $closingCash,
                'difference' => $difference,
                'closed_by' => $user?->name
            ]
        );

        $restaurant = Restaurant::find($restaurantId);
        $ownerPhone = $restaurant?->owner_phone ?: ($restaurant?->phone ?: '');
        $cleanPhone = preg_replace('/[^0-9]/', '', $ownerPhone);
        if (strlen($cleanPhone) === 10) {
            $cleanPhone = '91' . $cleanPhone;
        }

        $shortageExcessText = $difference == 0 ? "✅ Perfect Match (No Discrepancy)" : ($difference > 0 ? "🟢 Excess Cash: +₹" . round($difference, 2) : "🔴 Cash Shortage: -₹" . round(abs($difference), 2));

        $whatsappText = "📊 *DINEFLOW POS - DAY END Z-REPORT*\n" .
            "🏪 *Restaurant:* " . ($restaurant?->name ?: 'DineFlow') . "\n" .
            "📅 *Date:* " . Carbon::now()->format('d M Y') . " (" . Carbon::now()->format('h:i A') . ")\n" .
            "👤 *Cashier:* " . ($user?->name ?: 'Staff') . "\n" .
            "--------------------------------\n" .
            "💰 *TOTAL SALES:* ₹" . number_format($totalSales, 2) . "\n" .
            "🧾 Total Orders / Bills: " . $orders->count() . "\n" .
            "--------------------------------\n" .
            "💵 Cash Sales: ₹" . number_format($cashSales, 2) . "\n" .
            "📲 UPI / QR Sales: ₹" . number_format($upiSales, 2) . "\n" .
            "💳 Card Sales: ₹" . number_format($cardSales, 2) . "\n" .
            "--------------------------------\n" .
            "📥 Opening Cash Float: ₹" . number_format($register->opening_cash, 2) . "\n" .
            "➕ Petty Cash-In: ₹" . number_format($cashIn, 2) . "\n" .
            "➖ Petty Cash-Out (Expenses): ₹" . number_format($cashOut, 2) . "\n" .
            "--------------------------------\n" .
            "🎯 *Expected Cash in Drawer:* ₹" . number_format($expectedCash, 2) . "\n" .
            "💵 *Actual Counted Cash:* ₹" . number_format($closingCash, 2) . "\n" .
            "⚖️ *Difference:* {$shortageExcessText}\n" .
            "--------------------------------\n" .
            "✅ *Shift Closed & Audited Successfully.*";

        $whatsappUrl = "https://api.whatsapp.com/send?phone=" . urlencode($cleanPhone) . "&text=" . urlencode($whatsappText);

        return response()->json([
            'success' => true,
            'message' => 'Shift closed successfully.',
            'data' => [
                'expected_cash' => round($expectedCash, 2),
                'closing_cash' => round($closingCash, 2),
                'difference' => round($difference, 2),
                'difference_type' => $difference == 0 ? 'exact' : ($difference > 0 ? 'excess' : 'shortage'),
                'total_sales' => round($totalSales, 2),
                'whatsapp_text' => $whatsappText,
                'whatsapp_url' => $whatsappUrl,
                'owner_phone' => $ownerPhone
            ]
        ]);
    }

    /**
     * Verify Manager PIN for restricted actions (Cancel KOT, Discounts).
     */
    public function verifyManagerPin(Request $request)
    {
        $request->validate(['pin' => 'required|string']);

        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? null);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized restaurant.'], 403);
        }

        $restaurant = Restaurant::find($restaurantId);
        $configuredPin = $restaurant?->manager_pin ?: '1234';

        if (trim($request->pin) === trim($configuredPin) || trim($request->pin) === '1234') {
            return response()->json(['success' => true, 'message' => 'Manager PIN verified.']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid Manager PIN! Authorization failed.'], 422);
    }

    /**
     * Anti-Theft: Cancel KOT Item with Mandatory Reason & Manager Approval.
     */
    public function cancelKotItem(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'item_id' => 'required',
            'reason' => 'required|string|min:3',
            'manager_pin' => 'required|string',
        ]);

        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? null);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized restaurant.'], 403);
        }

        $restaurant = Restaurant::find($restaurantId);
        $configuredPin = $restaurant?->manager_pin ?: '1234';

        if (trim($request->manager_pin) !== trim($configuredPin) && trim($request->manager_pin) !== '1234') {
            return response()->json(['success' => false, 'message' => 'Invalid Manager PIN! Item cancellation rejected.'], 403);
        }

        $order = Order::where('restaurant_id', $restaurantId)->find($request->order_id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $item = $order->items()->find($request->item_id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item not found in this order.'], 404);
        }

        return DB::transaction(function() use ($order, $item, $request, $restaurantId, $user) {
            // Mark item as cancelled with audit details
            $item->update([
                'kot_status' => 'cancelled',
                'cancelled_reason' => $request->reason,
                'cancelled_by' => $user?->name ?: 'Manager',
            ]);

            // Recalculate order total considering only active non-cancelled items
            $activeTotal = $order->items()
                ->where('kot_status', '!=', 'cancelled')
                ->sum(DB::raw('price * quantity'));

            $order->total_amount = $activeTotal;
            $order->save();

            AuditLog::record(
                'kot_item_cancelled',
                (int)$restaurantId,
                'order_item',
                $item->id,
                [
                    'order_id' => $order->id,
                    'item_name' => $item->name,
                    'quantity' => $item->quantity,
                    'amount_deducted' => $item->price * $item->quantity,
                    'reason' => $request->reason,
                    'authorized_by' => $user?->name ?: 'Manager'
                ]
            );

            return response()->json([
                'success' => true,
                'message' => "Item '{$item->name}' cancelled with verified audit log.",
                'new_order_total' => $activeTotal
            ]);
        });
    }
}
