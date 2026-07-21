<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\WaiterCall;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function placeFeedback(Request $request)
    {
        $restaurantId = $request->input('restaurant_id') ?? $request->input('restaurantId') ?? '1';
        $customerName = $request->input('customer_name') ?? $request->input('customerName') ?? 'Anonymous';
        $rating = $request->input('rating') ?? 5;
        $comment = $request->input('comment') ?? '';

        $feedback = Feedback::create([
            'restaurant_id' => (string) $restaurantId,
            'customer_name' => (string) $customerName,
            'rating' => (int) $rating,
            'comment' => (string) $comment,
        ]);

        return response()->json(['success' => true, 'data' => $feedback], 201);
    }

    public function getFeedbacks($restaurantId)
    {
        $feedbacks = Feedback::where('restaurant_id', $restaurantId)->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $feedbacks]);
    }

    public function callWaiter(Request $request)
    {
        $restaurantId = $request->input('restaurant_id') ?? $request->input('restaurantId') ?? '1';
        $tableId = $request->input('table_id') ?? $request->input('tableId') ?? '1';

        $call = WaiterCall::create([
            'restaurant_id' => (string) $restaurantId,
            'table_id' => (string) $tableId,
            'status' => 'pending',
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
