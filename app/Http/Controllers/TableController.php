<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $restaurantId = $user ? (string)$user->restaurant_id : (string)($request->restaurant_id ?? $request->restaurantId);

        $request->validate([
            'table_number' => 'required|string',
        ]);

        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Restaurant ID is required'], 422);
        }

        $limitCheck = \App\Services\PlanLimitService::canCreateTable($restaurantId);
        if (!$limitCheck['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $limitCheck['message'],
                'limit_exceeded' => true,
                'details' => $limitCheck
            ], 422);
        }

        $table = Table::create([
            'restaurant_id' => $restaurantId,
            'table_number' => $request->table_number,
            'status' => 'active',
        ]);

        $baseUrl = $this->getPublicBaseUrl($request);
        $qrData = $baseUrl . "/customer/menu?restaurantId=" . $restaurantId . "&tableId=" . $table->id . "&restaurant_id=" . $restaurantId . "&table_id=" . $table->id;
        $table->qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrData);
        $table->save();

        return response()->json(['success' => true, 'data' => $table], 201);
    }

    public function getRestaurantTables($restaurantId)
    {
        $user = auth()->user();
        if ($user && $user->role !== 'superadmin' && $user->restaurant_id && strval($user->restaurant_id) !== strval($restaurantId)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access to another restaurant\'s tables.'], 403);
        }

        $tables = Table::where('restaurant_id', $restaurantId)->get();
        
        $baseUrl = $this->getPublicBaseUrl(request());
        $restaurant = \App\Models\Restaurant::find($restaurantId);
        $restaurantName = $restaurant ? $restaurant->name : 'DineFlow Restaurant';
        $gstRate = $restaurant ? (float)$restaurant->gst_rate / 100 : 0.05;
        $serviceRate = $restaurant ? (float)$restaurant->service_charge / 100 : 0.05;

        // Map tables to include dynamic occupancy status and live dynamic QR code URL
        $tables = $tables->map(function($table) use ($baseUrl, $restaurantId, $restaurantName, $gstRate, $serviceRate) {
            $table->qr_data_url = $baseUrl . "/customer/menu?restaurantId=" . $restaurantId . "&tableId=" . $table->id . "&restaurant_id=" . $restaurantId . "&table_id=" . $table->id;
            $table->restaurant_name = $restaurantName;
            $table->qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($table->qr_data_url);

            $activeOrder = \App\Models\Order::where('table_id', $table->id)
                ->where('payment_status', 'pending')
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->latest()
                ->first();
            if ($activeOrder) {
                $table->status = 'occupied';
                $table->active_order_id = $activeOrder->id;
                $subtotal = $activeOrder->total_amount;
                $serviceCharge = $subtotal * $serviceRate;
                $gst = ($subtotal + $serviceCharge) * $gstRate;
                $table->active_order_amount = round($subtotal + $serviceCharge + $gst);
            } else {
                $table->status = 'available';
                $table->active_order_id = null;
                $activeOrder = null;
                $table->active_order_amount = 0;
            }
            return $table;
        });

        return response()->json(['success' => true, 'data' => $tables]);
    }

    public function show($id)
    {
        $user = auth()->user();
        $query = Table::query();
        if ($user && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $table = $query->find($id);

        if (!$table) {
            return response()->json(['success' => false, 'message' => 'Table not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $table]);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $query = Table::query();
        if ($user && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $table = $query->find($id);

        if (!$table) {
            return response()->json(['success' => false, 'message' => 'Table not found in your restaurant'], 404);
        }

        // Prevent deleting a table if it has active unpaid orders
        $hasActiveOrder = \App\Models\Order::where('table_id', $table->id)
            ->where('payment_status', 'pending')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->exists();

        if ($hasActiveOrder) {
            return response()->json(['success' => false, 'message' => 'Cannot delete a table with an active unpaid order'], 400);
        }

        $table->delete();

        return response()->json(['success' => true, 'message' => 'Table deleted successfully']);
    }

    public function showA4QrPoster(Request $request, $tableId = null)
    {
        $user = auth()->user();
        $restaurantId = $user ? ($user->restaurant_id ?? null) : ($request->query('restaurant_id') ?? $request->query('restaurantId') ?? $request->input('restaurant_id') ?? $request->input('restaurantId') ?? null);
        if ($user && $user->role === 'superadmin' && ($request->query('restaurant_id') ?? $request->query('restaurantId') ?? $request->input('restaurant_id') ?? $request->input('restaurantId'))) {
            $restaurantId = $request->query('restaurant_id') ?? $request->query('restaurantId') ?? $request->input('restaurant_id') ?? $request->input('restaurantId');
        }

        $restaurant = null;
        if ($restaurantId) {
            $restaurant = \App\Models\Restaurant::find($restaurantId);
        }
        if (!$restaurant) {
            $restaurant = \App\Models\Restaurant::where('subscription_status', 'active')->first() ?: \App\Models\Restaurant::first();
        }
        if (!$restaurant) {
            abort(404, 'Restaurant not found');
        }

        $table = null;
        if ($tableId) {
            $table = Table::where('restaurant_id', $restaurant->id)
                ->where(function($q) use ($tableId) {
                    $q->where('id', $tableId)->orWhere('table_number', $tableId);
                })->first();
        }
        if (!$table) {
            $table = Table::where('restaurant_id', $restaurant->id)->first();
        }
        if (!$table) {
            $table = Table::firstOrCreate(
                ['restaurant_id' => $restaurant->id, 'table_number' => 'T-01'],
                ['status' => 'available']
            );
        }

        $tables = Table::where('restaurant_id', $restaurant->id)->orderBy('id')->get();
        $baseUrl = $this->getPublicBaseUrl($request);
        $isBatch = false;

        return view('pos.qr_poster_a4', compact('restaurant', 'table', 'tables', 'isBatch', 'baseUrl'));
    }

    public function showA4QrPosterBatch(Request $request)
    {
        $user = auth()->user();
        $restaurantId = $user ? ($user->restaurant_id ?? null) : ($request->query('restaurant_id') ?? $request->query('restaurantId') ?? $request->input('restaurant_id') ?? $request->input('restaurantId') ?? null);
        if ($user && $user->role === 'superadmin' && ($request->query('restaurant_id') ?? $request->query('restaurantId') ?? $request->input('restaurant_id') ?? $request->input('restaurantId'))) {
            $restaurantId = $request->query('restaurant_id') ?? $request->query('restaurantId') ?? $request->input('restaurant_id') ?? $request->input('restaurantId');
        }

        $restaurant = null;
        if ($restaurantId) {
            $restaurant = \App\Models\Restaurant::find($restaurantId);
        }
        if (!$restaurant) {
            $restaurant = \App\Models\Restaurant::where('subscription_status', 'active')->first() ?: \App\Models\Restaurant::first();
        }
        if (!$restaurant) {
            abort(404, 'Restaurant not found');
        }

        $tables = Table::where('restaurant_id', $restaurant->id)->orderBy('id')->get();
        if ($tables->isEmpty()) {
            $defaultTable = Table::firstOrCreate(
                ['restaurant_id' => $restaurant->id, 'table_number' => 'T-01'],
                ['status' => 'available']
            );
            $tables = collect([$defaultTable]);
        }

        $baseUrl = $this->getPublicBaseUrl($request);
        $isBatch = true;
        $table = $tables->first();

        return view('pos.qr_poster_a4', compact('restaurant', 'table', 'tables', 'isBatch', 'baseUrl'));
    }

    public function getPublicBaseUrl(?Request $request = null): string
    {
        $req = $request ?: request();

        if ($req) {
            $scheme = $req->header('x-forwarded-proto') ?: $req->getScheme();
            $host = $req->header('x-forwarded-host') ?: $req->getHttpHost();

            if (!empty($host) && !str_contains($host, 'localhost') && !str_contains($host, '127.0.0.1')) {
                return rtrim($scheme . '://' . $host, '/');
            }
        }

        $configuredUrl = rtrim(config('app.url') ?: '', '/');
        if (!empty($configuredUrl) && !str_contains($configuredUrl, 'localhost') && !str_contains($configuredUrl, '127.0.0.1')) {
            return $configuredUrl;
        }

        if ($req && method_exists($req, 'schemeAndHttpHost')) {
            return rtrim($req->schemeAndHttpHost(), '/');
        }

        return rtrim(config('app.url') ?: url('/'), '/');
    }

    public function shiftTable(Request $request)
    {
        $request->validate([
            'from_table_id' => 'required',
            'to_table_id' => 'required',
        ]);

        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? null);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized restaurant.'], 403);
        }

        $fromTable = Table::where('restaurant_id', $restaurantId)->find($request->from_table_id)
            ?: Table::where('restaurant_id', $restaurantId)->where('table_number', $request->from_table_id)->first();
        $toTable = Table::where('restaurant_id', $restaurantId)->find($request->to_table_id)
            ?: Table::where('restaurant_id', $restaurantId)->where('table_number', $request->to_table_id)->first();

        if (!$fromTable || !$toTable) {
            return response()->json(['success' => false, 'message' => 'Invalid source or destination table.'], 404);
        }

        if ($fromTable->id === $toTable->id) {
            return response()->json(['success' => false, 'message' => 'Source and destination tables must be different.'], 422);
        }

        // Check active order on destination
        $existingOrderOnTarget = \App\Models\Order::where('table_id', $toTable->id)
            ->where('restaurant_id', $restaurantId)
            ->where('payment_status', 'pending')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->first();

        if ($existingOrderOnTarget) {
            return response()->json(['success' => false, 'message' => 'Destination table is already occupied! Use Merge Table if you want to combine them.'], 400);
        }

        // Find active order on source table
        $sourceOrder = \App\Models\Order::where('table_id', $fromTable->id)
            ->where('restaurant_id', $restaurantId)
            ->where('payment_status', 'pending')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->latest()
            ->first();

        if (!$sourceOrder) {
            return response()->json(['success' => false, 'message' => 'No active order found on table ' . $fromTable->table_number], 404);
        }

        \DB::transaction(function() use ($sourceOrder, $fromTable, $toTable, $restaurantId, $user) {
            $sourceOrder->table_id = (string)$toTable->id;
            $sourceOrder->save();

            $fromTable->status = 'available';
            $fromTable->save();

            $toTable->status = 'occupied';
            $toTable->save();

            \App\Models\AuditLog::record(
                'table_shifted',
                (int)$restaurantId,
                'order',
                $sourceOrder->id,
                [
                    'from_table' => $fromTable->table_number,
                    'to_table' => $toTable->table_number,
                    'shifted_by' => $user ? $user->name : 'Staff'
                ]
            );
        });

        return response()->json([
            'success' => true,
            'message' => "Table shifted successfully from {$fromTable->table_number} to {$toTable->table_number}.",
            'from_table_id' => $fromTable->id,
            'to_table_id' => $toTable->id
        ]);
    }

    public function mergeTables(Request $request)
    {
        $request->validate([
            'source_table_id' => 'required',
            'target_table_id' => 'required',
        ]);

        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? null);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized restaurant.'], 403);
        }

        $sourceTable = Table::where('restaurant_id', $restaurantId)->find($request->source_table_id)
            ?: Table::where('restaurant_id', $restaurantId)->where('table_number', $request->source_table_id)->first();
        $targetTable = Table::where('restaurant_id', $restaurantId)->find($request->target_table_id)
            ?: Table::where('restaurant_id', $restaurantId)->where('table_number', $request->target_table_id)->first();

        if (!$sourceTable || !$targetTable) {
            return response()->json(['success' => false, 'message' => 'Invalid tables selected.'], 404);
        }

        if ($sourceTable->id === $targetTable->id) {
            return response()->json(['success' => false, 'message' => 'Cannot merge a table into itself.'], 422);
        }

        $sourceOrder = \App\Models\Order::where('table_id', $sourceTable->id)
            ->where('restaurant_id', $restaurantId)
            ->where('payment_status', 'pending')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->with('items')
            ->latest()
            ->first();

        $targetOrder = \App\Models\Order::where('table_id', $targetTable->id)
            ->where('restaurant_id', $restaurantId)
            ->where('payment_status', 'pending')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->with('items')
            ->latest()
            ->first();

        if (!$sourceOrder) {
            return response()->json(['success' => false, 'message' => 'No active order on table ' . $sourceTable->table_number], 404);
        }

        if (!$targetOrder) {
            $sourceOrder->table_id = (string)$targetTable->id;
            $sourceOrder->save();
            $sourceTable->status = 'available';
            $sourceTable->save();
            $targetTable->status = 'occupied';
            $targetTable->save();
            return response()->json([
                'success' => true,
                'message' => "Moved order from {$sourceTable->table_number} to {$targetTable->table_number}.",
            ]);
        }

        \DB::transaction(function() use ($sourceOrder, $targetOrder, $sourceTable, $targetTable, $restaurantId, $user) {
            foreach ($sourceOrder->items as $item) {
                $match = $targetOrder->items()
                    ->where('name', $item->name)
                    ->where('notes', $item->notes)
                    ->first();

                if ($match) {
                    $match->quantity += $item->quantity;
                    $match->save();
                    $item->delete();
                } else {
                    $item->order_id = $targetOrder->id;
                    $item->save();
                }
            }

            $targetOrder->load('items');
            $newTotal = $targetOrder->items->sum(fn($i) => $i->price * $i->quantity);
            $targetOrder->total_amount = $newTotal;
            $targetOrder->save();

            $sourceOrder->status = 'cancelled';
            $sourceOrder->special_instructions = ($sourceOrder->special_instructions ? $sourceOrder->special_instructions . ' | ' : '') . "Merged into Table {$targetTable->table_number} (Order #{$targetOrder->id})";
            $sourceOrder->save();

            $sourceTable->status = 'available';
            $sourceTable->save();

            \App\Models\AuditLog::record(
                'tables_merged',
                (int)$restaurantId,
                'order',
                $targetOrder->id,
                [
                    'source_table' => $sourceTable->table_number,
                    'target_table' => $targetTable->table_number,
                    'merged_by' => $user ? $user->name : 'Staff'
                ]
            );
        });

        return response()->json([
            'success' => true,
            'message' => "Successfully merged Table {$sourceTable->table_number} into Table {$targetTable->table_number}.",
            'target_order_id' => $targetOrder->id
        ]);
    }
}

