<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request, $restaurantId = null)
    {
        $user = auth()->user();
        $rId = $user ? $user->restaurant_id : ($restaurantId ?? $request->query('restaurant_id'));
        if (!$rId) {
            return response()->json(['success' => true, 'data' => []]);
        }
        $expenses = Expense::where('restaurant_id', $rId)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $expenses
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'payment_method' => 'nullable|string|max:100',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : $request->input('restaurant_id');
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Restaurant ID is required'], 422);
        }

        $validated['restaurant_id'] = $restaurantId;
        $validated['category'] = $validated['category'] ?? 'Other';
        $validated['payment_method'] = $validated['payment_method'] ?? 'Cash';

        $expense = Expense::create($validated);

        return response()->json([
            'success' => true,
            'data' => $expense,
            'message' => 'Expense created successfully'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $query = Expense::query();
        if ($user && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $expense = $query->find($id);

        if (!$expense) {
            return response()->json(['success' => false, 'message' => 'Expense not found in your restaurant'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'payment_method' => 'nullable|string|max:100',
            'date' => 'sometimes|required|date',
            'notes' => 'nullable|string',
        ]);

        $expense->update($validated);

        return response()->json([
            'success' => true,
            'data' => $expense,
            'message' => 'Expense updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $query = Expense::query();
        if ($user && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $expense = $query->find($id);

        if (!$expense) {
            return response()->json(['success' => false, 'message' => 'Expense not found in your restaurant'], 404);
        }

        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully'
        ]);
    }
}
