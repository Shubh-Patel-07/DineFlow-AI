<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request, $restaurantId = null)
    {
        $user = auth()->user();
        $resId = $user ? $user->restaurant_id : ($restaurantId ?? $request->input('restaurant_id', '1'));
        $reservations = Reservation::where('restaurant_id', $resId)
            ->orderBy('reservation_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $reservations]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id'     => 'nullable',
            'customer_name'     => 'required|string|max:255',
            'customer_phone'    => 'required|string|max:25',
            'pax_count'         => 'required|integer|min:1|max:50',
            'reservation_date'  => 'required|date',
            'time_slot'         => 'required|string',
            'special_requests'  => 'nullable|string',
        ]);

        $user = auth()->user();
        $resId = $user ? $user->restaurant_id : ($validated['restaurant_id'] ?? $request->input('restaurant_id', '1'));

        $reservation = Reservation::create([
            'restaurant_id'    => $resId,
            'customer_name'    => $validated['customer_name'],
            'customer_phone'   => $validated['customer_phone'],
            'pax_count'        => $validated['pax_count'],
            'reservation_date' => $validated['reservation_date'],
            'time_slot'        => $validated['time_slot'],
            'status'           => 'pending',
            'special_requests' => $validated['special_requests'] ?? '',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Table reservation requested successfully',
            'data'    => $reservation
        ], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,seated,cancelled'
        ]);

        $user = auth()->user();
        $query = Reservation::query();
        if ($user && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $reservation = $query->find($id);

        if (!$reservation) {
            return response()->json(['success' => false, 'message' => 'Reservation not found in your restaurant'], 404);
        }

        $reservation->status = $request->status;
        $reservation->save();

        return response()->json([
            'success' => true,
            'message' => "Reservation status updated to {$request->status}",
            'data'    => $reservation
        ]);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $query = Reservation::query();
        if ($user && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $reservation = $query->find($id);

        if (!$reservation) {
            return response()->json(['success' => false, 'message' => 'Reservation not found in your restaurant'], 404);
        }

        $reservation->delete();
        return response()->json(['success' => true, 'message' => 'Reservation deleted successfully']);
    }
}