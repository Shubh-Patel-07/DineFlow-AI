<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $restaurantId = ($user && $user->role === 'superadmin')
            ? ($request->query('restaurantId') ?: $request->query('restaurant_id') ?: $user->restaurant_id)
            : ($user ? $user->restaurant_id : ($request->query('restaurantId') ?: $request->query('restaurant_id')));

        if (!$restaurantId) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $coupons = Coupon::where('restaurant_id', $restaurantId)->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $coupons]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? $request->input('restaurant_id'));
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Restaurant ID is required'], 422);
        }

        $request->validate([
            'code'           => 'required|string',
            'name'           => 'required|string|max:255',
            'discount_type'  => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
        ]);

        $code = strtoupper(trim($request->code));
        $existing = Coupon::where('restaurant_id', $restaurantId)->where('code', $code)->first();
        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Coupon code already exists in your restaurant.'], 422);
        }

        $coupon = Coupon::create([
            'restaurant_id'    => $restaurantId,
            'code'             => $code,
            'name'             => $request->name,
            'discount_type'    => $request->discount_type,
            'discount_value'   => $request->discount_value,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'max_discount'     => $request->max_discount ?? null,
            'usage_limit'      => $request->usage_limit ?? null,
            'is_active'        => $request->is_active ?? true,
            'is_auto_apply'    => $request->is_auto_apply ?? false,
            'first_time_only'  => $request->first_time_only ?? false,
            'category'         => $request->category ?? 'all',
            'order_type'       => $request->order_type ?? 'all',
            'expires_at'       => $request->expires_at ?? null,
        ]);

        return response()->json(['success' => true, 'data' => $coupon], 201);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $query = Coupon::query();
        if ($user && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $coupon = $query->find($id);

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Coupon not found in your restaurant'], 404);
        }

        $validated = $request->validate([
            'name'             => 'sometimes|required|string|max:255',
            'discount_type'    => 'sometimes|required|in:percentage,fixed',
            'discount_value'   => 'sometimes|required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount'     => 'nullable|numeric|min:0',
            'usage_limit'      => 'nullable|integer|min:1',
            'is_active'        => 'nullable|boolean',
            'is_auto_apply'    => 'nullable|boolean',
            'first_time_only'  => 'nullable|boolean',
            'category'         => 'nullable|string|max:100',
            'order_type'       => 'nullable|string',
            'expires_at'       => 'nullable',
        ]);

        if (array_key_exists('expires_at', $validated)) {
            $validated['expires_at'] = !empty($validated['expires_at']) ? date('Y-m-d H:i:s', strtotime($validated['expires_at'])) : null;
        }

        if (isset($validated['is_active']) && (bool)$validated['is_active']) {
            if ($coupon->expires_at && $coupon->expires_at->isPast() && !array_key_exists('expires_at', $validated)) {
                $coupon->expires_at = null;
            }
        }

        $coupon->fill($validated);
        $coupon->save();
        return response()->json(['success' => true, 'data' => $coupon]);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $query = Coupon::query();
        if ($user && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $coupon = $query->find($id);

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Coupon not found in your restaurant'], 404);
        }
        $coupon->delete();
        return response()->json(['success' => true, 'message' => 'Coupon deleted']);
    }

    /**
     * Get the best auto-apply coupon for POS / Customer Cart
     */
    public function getBestAutoApply(Request $request)
    {
        $orderAmount = floatval($request->input('order_amount', 0));
        $isNewCustomer = (bool)$request->input('is_new_customer', false);
        $orderType = $request->input('order_type', 'all');

        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? $request->input('restaurant_id'));
        if (!$restaurantId) {
            return response()->json(['success' => true, 'data' => null]);
        }

        $coupons = Coupon::where('is_active', true)
            ->where('is_auto_apply', true)
            ->where('restaurant_id', $restaurantId)
            ->get();

        $bestCoupon = null;
        $maxCalculatedDiscount = 0;

        foreach ($coupons as $coupon) {
            if ($coupon->expires_at && $coupon->expires_at->isPast()) continue;
            if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) continue;
            if ($orderAmount < $coupon->min_order_amount) continue;
            if ($coupon->first_time_only && !$isNewCustomer) continue;
            if ($coupon->order_type !== 'all' && $coupon->order_type !== $orderType) continue;

            $discount = 0;
            if ($coupon->discount_type === 'percentage') {
                $discount = ($orderAmount * $coupon->discount_value) / 100;
                if ($coupon->max_discount) {
                    $discount = min($discount, $coupon->max_discount);
                }
            } else {
                $discount = $coupon->discount_value;
            }

            if ($discount > $maxCalculatedDiscount) {
                $maxCalculatedDiscount = $discount;
                $bestCoupon = $coupon;
            }
        }

        if (!$bestCoupon) {
            return response()->json(['success' => true, 'data' => null]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'coupon' => $bestCoupon,
                'discount' => round($maxCalculatedDiscount, 2),
                'final_amount' => round(max(0, $orderAmount - $maxCalculatedDiscount), 2)
            ]
        ]);
    }

    /**
     * Validate a coupon code for POS / Customer Cart
     */
    public function validateCoupon(Request $request)
    {
        $code = strtoupper(trim($request->input('code', '')));
        $orderAmount = floatval($request->input('order_amount', 0));
        $isNewCustomer = (bool)$request->input('is_new_customer', false);
        $orderType = $request->input('order_type', 'all');

        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? $request->input('restaurant_id'));
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Restaurant ID is required to validate coupon'], 422);
        }

        $coupon = Coupon::where('code', $code)
            ->where('is_active', true)
            ->where('restaurant_id', $restaurantId)
            ->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Invalid or inactive coupon code'], 404);
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return response()->json(['success' => false, 'message' => 'Coupon has expired'], 400);
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return response()->json(['success' => false, 'message' => 'Coupon usage limit reached'], 400);
        }

        if ($coupon->first_time_only && !$isNewCustomer) {
            return response()->json(['success' => false, 'message' => 'Coupon is valid for new customers only'], 400);
        }

        if ($coupon->order_type !== 'all' && $coupon->order_type !== $orderType) {
            return response()->json(['success' => false, 'message' => "Coupon valid only for {$coupon->order_type} orders"], 400);
        }

        if ($orderAmount < $coupon->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => "Minimum order amount is ₹{$coupon->min_order_amount}"
            ], 400);
        }

        // Calculate discount
        if ($coupon->discount_type === 'percentage') {
            $discount = ($orderAmount * $coupon->discount_value) / 100;
            if ($coupon->max_discount) {
                $discount = min($discount, $coupon->max_discount);
            }
        } else {
            $discount = $coupon->discount_value;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'coupon'   => $coupon,
                'discount' => round($discount, 2),
                'final_amount' => round(max(0, $orderAmount - $discount), 2),
            ]
        ]);
    }
}
