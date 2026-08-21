<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Feedback;
use App\Models\WaiterCall;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // ---- Customer CRUD ----

    public function index(Request $request)
    {
        $restaurantId = $request->query('restaurantId', 1);
        $customers = Customer::where('restaurant_id', $restaurantId)->orderBy('name')->get();
        return response()->json(['success' => true, 'data' => $customers]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'restaurant_id' => 'nullable',
            'phone'         => 'nullable|string|max:30',
            'email'         => 'nullable|email|max:255',
            'notes'         => 'nullable|string|max:1000',
        ]);

        $customer = Customer::create([
            'restaurant_id' => $request->restaurant_id ?? auth()->user()?->restaurant_id ?? 1,
            'name'          => $validated['name'],
            'phone'         => $validated['phone'] ?? null,
            'email'         => $validated['email'] ?? null,
            'notes'         => $validated['notes'] ?? null,
        ]);

        return response()->json(['success' => true, 'data' => $customer], 201);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found'], 404);
        }

        $validated = $request->validate([
            'name'           => 'sometimes|required|string|max:255',
            'phone'          => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:255',
            'loyalty_points' => 'nullable|integer|min:0',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $customer->fill($validated);
        $customer->save();
        return response()->json(['success' => true, 'data' => $customer]);
    }

    public function earnPoints(Request $request)
    {
        $phone = $request->input('phone');
        $name = $request->input('name');
        $earnedPoints = intval($request->input('earned_points', 0));
        $orderAmount = floatval($request->input('order_amount', 0));

        if ($earnedPoints <= 0) {
            return response()->json(['success' => false, 'message' => 'No points earned']);
        }

        $restaurantId = $request->input('restaurant_id', '1');
        $customer = null;
        if ($phone) {
            $customer = Customer::where('phone', $phone)->where('restaurant_id', $restaurantId)->first();
        }
        if (!$customer && $name) {
            $customer = Customer::where('name', $name)->where('restaurant_id', $restaurantId)->first();
        }

        if ($customer) {
            $customer->increment('loyalty_points', $earnedPoints);
            $customer->increment('total_spent', $orderAmount);
            $customer->increment('total_visits', 1);
            $customer->last_visit = now();
            $customer->save();

            return response()->json([
                'success' => true,
                'message' => "Earned +{$earnedPoints} points!",
                'data' => $customer
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Customer not found']);
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found'], 404);
        }
        $customer->delete();
        return response()->json(['success' => true, 'message' => 'Customer deleted']);
    }

    // ---- Feedback ----

    public function placeFeedback(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $restaurantId = $request->input('restaurant_id') ?? $request->input('restaurantId') ?? '1';
        $customerName = $request->input('customer_name') ?? $request->input('customerName') ?? 'Anonymous';
        $rating = $request->input('rating') ?? 5;
        $comment = $request->input('comment') ?? '';

        $feedback = Feedback::create([
            'restaurant_id' => (string) $restaurantId,
            'customer_name' => (string) $customerName,
            'rating'        => (int) $rating,
            'comment'       => (string) $comment,
        ]);

        return response()->json(['success' => true, 'data' => $feedback], 201);
    }

    public function getFeedbacks($restaurantId)
    {
        $feedbacks = Feedback::where('restaurant_id', $restaurantId)->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $feedbacks]);
    }

    // ---- Waiter Calls ----

    public function callWaiter(Request $request)
    {
        $restaurantId = $request->input('restaurant_id') ?? $request->input('restaurantId') ?? '1';
        $tableId = $request->input('table_id') ?? $request->input('tableId') ?? '1';

        $call = WaiterCall::create([
            'restaurant_id' => (string) $restaurantId,
            'table_id'      => (string) $tableId,
            'reason'        => $request->input('reason', 'General Assistance'),
            'status'        => 'pending',
        ]);

        return response()->json(['success' => true, 'data' => $call], 201);
    }

    public function getWaiterCalls($restaurantId)
    {
        $calls = WaiterCall::where('restaurant_id', $restaurantId)->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $calls]);
    }

    public function resolveWaiterCall(Request $request, $id)
    {
        $call = WaiterCall::find($id);

        if (!$call) {
            return response()->json(['success' => false, 'message' => 'Call request not found'], 404);
        }

        $call->status = 'resolved';
        $call->save();

        return response()->json(['success' => true, 'data' => $call]);
    }
}
