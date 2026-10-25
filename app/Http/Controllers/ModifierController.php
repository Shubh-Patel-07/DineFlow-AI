<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ModifierGroup;
use App\Models\ModifierOption;
use App\Models\MenuItem;

class ModifierController extends Controller
{
    /**
     * Get all modifier groups for the restaurant.
     */
    public function index(Request $request)
    {
        $restaurantId = $request->header('X-Restaurant-ID') ?? auth()->user()?->restaurant_id ?? 1;

        $groups = ModifierGroup::where('restaurant_id', $restaurantId)
            ->with('options')
            ->orderBy('sort_order', 'asc')
            ->get();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['status' => 'success', 'data' => $groups]);
        }

        return view('admin.modifiers', compact('groups'));
    }

    /**
     * Create a new modifier group with options.
     */
    public function store(Request $request)
    {
        $restaurantId = $request->header('X-Restaurant-ID') ?? auth()->user()?->restaurant_id ?? 1;

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'selection_type' => 'required|in:single,multiple',
            'min_selection' => 'nullable|integer|min:0',
            'max_selection' => 'nullable|integer|min:1',
            'is_required' => 'nullable|boolean',
            'options' => 'required|array|min:1',
            'options.*.name' => 'required|string|max:100',
            'options.*.price' => 'required|numeric|min:0',
            'options.*.is_veg' => 'nullable|boolean',
            'options.*.is_default' => 'nullable|boolean',
        ]);

        $group = ModifierGroup::create([
            'restaurant_id' => $restaurantId,
            'name' => $validated['name'],
            'selection_type' => $validated['selection_type'],
            'min_selection' => $validated['min_selection'] ?? ($validated['selection_type'] === 'single' ? 1 : 0),
            'max_selection' => $validated['max_selection'] ?? 1,
            'is_required' => $request->boolean('is_required'),
        ]);

        foreach ($validated['options'] as $opt) {
            $group->options()->create([
                'name' => $opt['name'],
                'price' => $opt['price'],
                'is_veg' => $opt['is_veg'] ?? true,
                'is_default' => $opt['is_default'] ?? false,
                'is_available' => true,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Modifier group created successfully.',
            'data' => $group->load('options')
        ], 201);
    }

    /**
     * Fetch modifiers for a specific menu item (used by POS and Customer Menu).
     */
    public function getItemModifiers($id)
    {
        $item = MenuItem::with(['modifierGroups.options'])->find($id);

        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Menu item not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'item' => [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'has_modifiers' => (bool)$item->has_modifiers,
                'is_combo' => (bool)$item->is_combo,
            ],
            'modifier_groups' => $item->modifierGroups
        ]);
    }

    /**
     * Attach modifier groups to a menu item.
     */
    public function assignToItem(Request $request)
    {
        $validated = $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'modifier_group_ids' => 'present|array',
            'modifier_group_ids.*' => 'exists:modifier_groups,id'
        ]);

        $item = MenuItem::findOrFail($validated['menu_item_id']);
        $item->modifierGroups()->sync($validated['modifier_group_ids']);
        $item->update(['has_modifiers' => count($validated['modifier_group_ids']) > 0]);

        return response()->json([
            'status' => 'success',
            'message' => 'Modifiers updated for ' . $item->name
        ]);
    }
}
