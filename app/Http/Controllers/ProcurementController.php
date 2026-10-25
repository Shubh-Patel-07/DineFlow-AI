<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\GoodsReceivedNote;
use App\Models\SupplierPayment;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

class ProcurementController extends Controller
{
    /**
     * List suppliers with total orders and balance.
     */
    public function suppliers(Request $request)
    {
        $restaurantId = $request->header('X-Restaurant-ID') ?? auth()->user()?->restaurant_id ?? 1;

        $suppliers = Supplier::where('restaurant_id', $restaurantId)
            ->withCount('purchaseOrders')
            ->orderBy('name', 'asc')
            ->get();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['status' => 'success', 'data' => $suppliers]);
        }

        return view('admin.suppliers', compact('suppliers'));
    }

    /**
     * Store a new supplier.
     */
    public function storeSupplier(Request $request)
    {
        $restaurantId = $request->header('X-Restaurant-ID') ?? auth()->user()?->restaurant_id ?? 1;

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'company_name' => 'nullable|string|max:150',
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'gstin' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'payment_terms' => 'nullable|string|max:50',
            'bank_details' => 'nullable|string',
        ]);

        $validated['restaurant_id'] = $restaurantId;
        $supplier = Supplier::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Supplier added successfully',
            'data' => $supplier
        ], 201);
    }

    /**
     * List purchase orders with filter by status.
     */
    public function purchaseOrders(Request $request)
    {
        $restaurantId = $request->header('X-Restaurant-ID') ?? auth()->user()?->restaurant_id ?? 1;

        $query = PurchaseOrder::where('restaurant_id', $restaurantId)
            ->with(['supplier', 'items'])
            ->orderBy('id', 'desc');

        if ($request->filled('status')) {
            $query->where('status', strtoupper($request->status));
        }

        $orders = $query->paginate(20);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['status' => 'success', 'data' => $orders]);
        }

        return view('admin.purchase_orders', compact('orders'));
    }

    /**
     * Create a new Purchase Order (PO).
     */
    public function storePurchaseOrder(Request $request)
    {
        $restaurantId = $request->header('X-Restaurant-ID') ?? auth()->user()?->restaurant_id ?? 1;

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.ingredient_name' => 'required|string|max:150',
            'items.*.quantity_ordered' => 'required|numeric|min:0.1',
            'items.*.unit' => 'required|string|max:20',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated, $restaurantId, $request) {
            $poNumber = 'PO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $subtotal = 0;
            $taxAmount = 0;

            foreach ($validated['items'] as $item) {
                $lineSubtotal = $item['quantity_ordered'] * $item['unit_price'];
                $lineTax = $lineSubtotal * (($item['tax_rate'] ?? 0) / 100);
                $subtotal += $lineSubtotal;
                $taxAmount += $lineTax;
            }

            $totalAmount = $subtotal + $taxAmount;

            $po = PurchaseOrder::create([
                'restaurant_id' => $restaurantId,
                'supplier_id' => $validated['supplier_id'],
                'po_number' => $poNumber,
                'order_date' => $validated['order_date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'status' => 'SENT',
                'payment_status' => 'UNPAID',
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->user()?->name ?? 'Admin',
            ]);

            foreach ($validated['items'] as $item) {
                $lineSubtotal = $item['quantity_ordered'] * $item['unit_price'];
                $lineTax = $lineSubtotal * (($item['tax_rate'] ?? 0) / 100);

                $po->items()->create([
                    'ingredient_name' => $item['ingredient_name'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'quantity_received' => 0,
                    'unit' => $item['unit'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'total_price' => $lineSubtotal + $lineTax,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Purchase Order created: ' . $poNumber,
                'data' => $po->load(['supplier', 'items'])
            ], 201);
        });
    }

    /**
     * Receive goods against a PO (Generate Goods Received Note - GRN)
     * Automatically increments raw ingredient stocks in the inventories table!
     */
    public function receiveGoods(Request $request, $id)
    {
        $restaurantId = $request->header('X-Restaurant-ID') ?? auth()->user()?->restaurant_id ?? 1;
        $po = PurchaseOrder::with('items')->where('restaurant_id', $restaurantId)->findOrFail($id);

        $validated = $request->validate([
            'received_date' => 'required|date',
            'supplier_invoice_number' => 'nullable|string|max:100',
            'supplier_invoice_date' => 'nullable|date',
            'billed_amount' => 'nullable|numeric|min:0',
            'received_items' => 'required|array|min:1',
            'received_items.*.item_id' => 'required|exists:purchase_order_items,id',
            'received_items.*.quantity_received' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $po, $restaurantId) {
            $grnNumber = 'GRN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $grn = GoodsReceivedNote::create([
                'restaurant_id' => $restaurantId,
                'purchase_order_id' => $po->id,
                'grn_number' => $grnNumber,
                'received_date' => $validated['received_date'],
                'supplier_invoice_number' => $validated['supplier_invoice_number'] ?? null,
                'supplier_invoice_date' => $validated['supplier_invoice_date'] ?? null,
                'billed_amount' => $validated['billed_amount'] ?? $po->total_amount,
                'received_by' => auth()->user()?->name ?? 'Store Manager',
                'remarks' => $validated['remarks'] ?? null,
            ]);

            $allCompleted = true;

            foreach ($validated['received_items'] as $rec) {
                $poItem = PurchaseOrderItem::where('purchase_order_id', $po->id)->find($rec['item_id']);
                if ($poItem) {
                    $newQty = $poItem->quantity_received + $rec['quantity_received'];
                    $poItem->update(['quantity_received' => $newQty]);

                    if ($newQty < $poItem->quantity_ordered) {
                        $allCompleted = false;
                    }

                    // AUTO-SYNC TO INVENTORY: Update or create stock ingredient
                    $inventory = Inventory::firstOrCreate(
                        ['restaurant_id' => $restaurantId, 'name' => $poItem->ingredient_name],
                        ['quantity' => 0, 'unit' => $poItem->unit, 'threshold' => 10]
                    );

                    $inventory->increment('quantity', $rec['quantity_received']);
                }
            }

            $po->update([
                'status' => $allCompleted ? 'COMPLETED' : 'PARTIAL'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Goods received successfully under ' . $grnNumber . '. Inventory stock updated!',
                'grn' => $grn
            ]);
        });
    }
}
