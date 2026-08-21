<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $restaurantId = $request->query('restaurantId', 1);
        $coupons = Coupon::where('restaurant_id', $restaurantId)->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $coupons]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'           => 'required|string|unique:coupons,code',
            'name'           => 'required|string|max:255',
            'discount_type'  => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::create([
            'restaurant_id'    => $request->restaurant_id ?? 1,
            'code'             => strtoupper($request->code),
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
        $coupon = Coupon::find($id);
        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Coupon not found'], 404);
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
            'expires_at'       => 'nullable|date',
        ]);

        $coupon->fill($validated);
        $coupon->save();
        return response()->json(['success' => true, 'data' => $coupon]);
    }

    public function destroy($id)
    {
        $coupon = Coupon::find($id);
        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Coupon not found'], 404);
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

        $coupons = Coupon::where('is_active', true)
            ->where('is_auto_apply', true)
            ->where('restaurant_id', $request->restaurant_id ?? $request->input('restaurant_id', '1'))
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
            return response()->json(['success' => false, 'message' => 'No eligible auto-apply coupon found']);
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
     * Validate a coupon code at POS checkout
     */
    public function validateCoupon(Request $request)
    {
        $code = strtoupper($request->input('code'));
        $orderAmount = $request->input('order_amount', 0);
        $isNewCustomer = (bool)$request->input('is_new_customer', false);
        $orderType = $request->input('order_type', 'all');

        $coupon = Coupon::where('code', $code)
            ->where('is_active', true)
            ->where('restaurant_id', $request->restaurant_id ?? $request->input('restaurant_id', '1'))
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
