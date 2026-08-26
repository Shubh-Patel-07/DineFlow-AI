<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request, $restaurantId = null)
    {
        $rId = $restaurantId ?? $request->query('restaurant_id', '1');
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
            'restaurant_id' => 'nullable|string',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'payment_method' => 'nullable|string|max:100',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['restaurant_id'] = $validated['restaurant_id'] ?? $request->input('restaurant_id', '1');
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
        $expense = Expense::find($id);
        if (!$expense) {
            return response()->json(['success' => false, 'message' => 'Expense not found'], 404);
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
        $expense = Expense::find($id);
        if (!$expense) {
            return response()->json(['success' => false, 'message' => 'Expense not found'], 404);
        }

        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully'
        ]);
    }
}
