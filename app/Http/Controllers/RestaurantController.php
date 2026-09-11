<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
        ]);

        $restaurant = Restaurant::create([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'logo' => $request->logo ?? '',
        ]);

        return response()->json(['success' => true, 'data' => $restaurant], 201);
    }
    public function show($id)
    {
        $restaurant = Restaurant::find($id);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        $data = $restaurant->toArray();
        $data['logo'] = $restaurant->getEffectiveLogoUrl() ?: ($restaurant->logo ?? null);
        $data['upi_qr'] = $restaurant->getEffectiveUpiQr();
        $data['upi_vpa'] = $restaurant->getEffectiveUpiVpa();
        $data['is_demo'] = $restaurant->isDemo();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getSettings($id)
    {
        $user = auth()->user();
        if ($user && $user->restaurant_id && strval($user->restaurant_id) !== strval($id) && strtolower(trim($user->role ?? '')) !== 'superadmin') {
            return response()->json(['success' => false, 'message' => 'Forbidden: You cannot access settings for another restaurant.'], 403);
        }

        $restaurant = Restaurant::find($id);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'address' => $restaurant->address,
                'phone' => $restaurant->phone,
                'logo' => $restaurant->getEffectiveLogoUrl(),
                'signature' => $restaurant->getEffectiveSignature(),
                'signature_name' => $restaurant->getEffectiveSignatureName(),
                'upi_qr' => $restaurant->getEffectiveUpiQr(),
                'upi_vpa' => $restaurant->getEffectiveUpiVpa(),
                'gstin' => $restaurant->gstin,
                'gst_rate' => $restaurant->gst_rate,
                'service_charge' => $restaurant->service_charge,
                'settings' => $restaurant->settings ?? []
            ]
        ]);
    }

    public function updateSettings(Request $request, $id)
    {
        $user = auth()->user();
        if ($user && $user->restaurant_id && strval($user->restaurant_id) !== strval($id) && strtolower(trim($user->role ?? '')) !== 'superadmin') {
            return response()->json(['success' => false, 'message' => 'Forbidden: You cannot modify settings for another restaurant.'], 403);
        }

        $restaurant = Restaurant::find($id);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'logo' => 'nullable|string|max:500',
            'signature' => 'nullable|string|max:500',
            'signature_name' => 'nullable|string|max:255',
            'upi_qr' => 'nullable|string|max:500',
            'upi_vpa' => 'nullable|string|max:100',
            'gstin' => 'nullable|string|max:50',
            'gst_rate' => 'nullable|numeric|min:0|max:100',
            'service_charge' => 'nullable|numeric|min:0|max:100',
            'settings' => 'nullable|array'
        ]);

        // Dual-persist in settings array for 100% backwards compatibility
        $currentSettings = $restaurant->settings ?? [];
        if ($request->has('logo')) $currentSettings['logo'] = $request->logo;
        if ($request->has('signature')) $currentSettings['signature'] = $request->signature;
        if ($request->has('signature_name')) $currentSettings['signature_name'] = $request->signature_name;
        if ($request->has('upi_qr')) $currentSettings['upi_qr'] = $request->upi_qr;
        if ($request->has('upi_vpa')) $currentSettings['upi_vpa'] = $request->upi_vpa;
        if ($request->has('settings') && is_array($request->settings)) {
            $currentSettings = array_merge($currentSettings, $request->settings);
        }
        $validated['settings'] = $currentSettings;

        // Schema-safe update: filter out any columns that may not exist in live DB
        try {
            $existingCols = \Illuminate\Support\Facades\Schema::getColumnListing('restaurants');
            $safeData = array_intersect_key($validated, array_flip($existingCols));
            $restaurant->update(array_filter($safeData, fn($val) => !is_null($val)));
        } catch (\Throwable $e) {
            // Fallback direct update
            $restaurant->update(array_filter($validated, fn($val) => !is_null($val)));
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'address' => $restaurant->address,
                'phone' => $restaurant->phone,
                'logo' => $restaurant->getEffectiveLogoUrl(),
                'signature' => $restaurant->getEffectiveSignature(),
                'signature_name' => $restaurant->getEffectiveSignatureName(),
                'upi_qr' => $restaurant->getEffectiveUpiQr(),
                'upi_vpa' => $restaurant->getEffectiveUpiVpa(),
                'gstin' => $restaurant->gstin,
                'gst_rate' => $restaurant->gst_rate,
                'service_charge' => $restaurant->service_charge,
                'settings' => $restaurant->settings ?? []
            ],
            'message' => 'Restaurant settings updated successfully'
        ]);
    }

    public function uploadImage(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'type' => 'nullable|string|in:logo,signature,upi_qr,general'
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $type = $request->input('type', 'general');
            $prefix = $type . '_' . ($user ? $user->restaurant_id . '_' : '');
            $filename = $prefix . \Illuminate\Support\Str::random(24) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads', $filename, 'public');

            return response()->json([
                'success' => true,
                'url' => '/storage/' . $path,
                'path' => $path
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'No image file uploaded'], 400);
    }
}
