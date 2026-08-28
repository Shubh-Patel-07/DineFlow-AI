<?php

namespace App\Http\Controllers;

use App\Models\DemoRequest;
use App\Mail\NewLeadNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ContactController extends Controller
{
    /**
     * Store a public contact / demo / enquiry form submission
     */
    public function submit(Request $request)
    {
        // 1. Honeypot check (anti-bot defense)
        if ($request->filled('website_hp')) {
            Log::info('Spam bot submission caught via honeypot.', ['ip' => $request->ip()]);
            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your enquiry has been received. Our Tendorabuy team will contact you shortly.'
            ], 200);
        }

        // 2. Strict Input Validation
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|min:2|max:255',
            'restaurant_name' => 'required|string|min:2|max:255',
            'phone'           => ['required', 'string', 'min:8', 'max:25', 'regex:/^[0-9+\s\-()]{8,25}$/'],
            'email'           => 'nullable|email|max:255',
            'city'            => 'nullable|string|max:255',
            'preferred_time'  => 'nullable|string|max:255',
            'message'         => 'nullable|string|max:3000',
            'source'          => 'nullable|string|max:100',
        ], [
            'name.required'            => 'Please provide your full name.',
            'restaurant_name.required' => 'Please provide your restaurant or business name.',
            'phone.required'           => 'Please provide a valid phone or WhatsApp number.',
            'phone.regex'              => 'Please enter a valid phone number format (8-15 digits).',
            'email.email'              => 'Please provide a valid email address.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors'  => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // 3. Duplicate Prevention (Debounce exact duplicate submissions within 60s)
        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
        $recentDuplicate = DemoRequest::where('phone', $validated['phone'])
            ->orWhere(function($q) use ($cleanPhone) {
                if (strlen($cleanPhone) >= 10) {
                    $q->where('phone', 'like', "%" . substr($cleanPhone, -10) . "%");
                }
            })
            ->where('created_at', '>=', Carbon::now()->subSeconds(60))
            ->first();

        if ($recentDuplicate) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thank you! Your enquiry has been received. Our Tendorabuy team will contact you shortly.',
                    'data'    => $recentDuplicate,
                    'duplicate' => true
                ], 200);
            }
            return redirect()->back()->with('success', 'Thank you! Your enquiry has been received.');
        }

        // 4. Save Lead to Database
        $lead = DemoRequest::create([
            'name'            => trim($validated['name']),
            'restaurant_name' => trim($validated['restaurant_name']),
            'phone'           => trim($validated['phone']),
            'email'           => !empty($validated['email']) ? trim($validated['email']) : null,
            'city'            => !empty($validated['city']) ? trim($validated['city']) : null,
            'preferred_time'  => !empty($validated['preferred_time']) ? trim($validated['preferred_time']) : null,
            'message'         => !empty($validated['message']) ? trim($validated['message']) : null,
            'source'          => !empty($validated['source']) ? trim($validated['source']) : 'website_form',
            'status'          => DemoRequest::STATUS_NEW,
        ]);

        // 5. Non-blocking Email Notification (Safe dispatch)
        $notificationEmail = env('CONTACT_NOTIFICATION_EMAIL');
        $emailSent = false;

        if ($notificationEmail) {
            try {
                Mail::to($notificationEmail)->send(new NewLeadNotification($lead));
                $emailSent = true;
                Log::info("Lead notification email sent to {$notificationEmail} for Lead #{$lead->id}");
            } catch (\Throwable $e) {
                Log::warning("Could not dispatch lead notification email: " . $e->getMessage(), [
                    'lead_id' => $lead->id,
                    'recipient' => $notificationEmail
                ]);
            }
        } else {
            Log::info("No CONTACT_NOTIFICATION_EMAIL configured in .env. Lead #{$lead->id} saved to database.");
        }

        // 6. Return Response
        $successMessage = 'Thank you! Your enquiry has been received. Our Tendorabuy team will contact you shortly.';

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success'    => true,
                'message'    => $successMessage,
                'email_sent' => $emailSent,
                'data'       => [
                    'id'              => $lead->id,
                    'name'            => $lead->name,
                    'restaurant_name' => $lead->restaurant_name,
                    'phone'           => $lead->phone,
                    'status'          => $lead->status,
                    'created_at'      => $lead->created_at->toISOString(),
                ]
            ], 201);
        }

        return redirect()->back()->with('success', $successMessage);
    }
}
