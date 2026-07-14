<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\WaiterCall;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function placeFeedback(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $feedback = Feedback::create([
            'restaurant_id' => $request->restaurant_id,
            'customer_name' => $request->customer_name ?? 'Anonymous',
            'rating' => $request->rating,
            'comment' => $request->comment ?? '',
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
        $request->validate([
            'restaurant_id' => 'required|string',
            'table_id' => 'required|string',
        ]);

        $call = WaiterCall::create([
            'restaurant_id' => $request->restaurant_id,
            'table_id' => $request->table_id,
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
