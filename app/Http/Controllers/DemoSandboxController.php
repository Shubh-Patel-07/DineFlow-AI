<?php

namespace App\Http\Controllers;

use App\Services\DemoSandboxService;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemoSandboxController extends Controller
{
    protected DemoSandboxService $sandboxService;

    public function __construct(DemoSandboxService $sandboxService)
    {
        $this->sandboxService = $sandboxService;
    }

    /**
     * Start a new isolated sandbox demo session
     */
    public function startDemo(Request $request)
    {
        // 1. If currently logged in as a real restaurant, do not wipe real restaurant data!
        if (Auth::check()) {
            $currentRest = Auth::user()->restaurant;
            if ($currentRest && $currentRest->is_demo && !$request->has('fresh')) {
                // Return to active demo session
                $target = $request->query('destination', '/dashboard');
                return redirect($target);
            }
            if ($currentRest && $currentRest->is_demo && $request->has('fresh')) {
                $this->sandboxService->destroySandbox($currentRest);
            }
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // 2. Provision a clean isolated demo sandbox
        $sandbox = $this->sandboxService->createSandbox();

        // 3. Log into this demo session
        Auth::login($sandbox['user']);
        $request->session()->regenerate();
        session([
            'is_demo' => true,
            'demo_session_id' => $sandbox['demo_session_id'],
            'demo_expires_at' => $sandbox['restaurant']->expires_at->toIso8601String(),
        ]);

        $destination = $request->query('destination', '/dashboard');
        if ($destination === 'pos') {
            return redirect('/pos')->with('info', '⚡ Welcome to the Live Demo POS Terminal Sandbox!');
        } elseif ($destination === 'kitchen') {
            return redirect('/kitchen')->with('info', '⚡ Welcome to the Live Demo Kitchen KDS Sandbox!');
        } elseif ($destination === 'qr') {
            return redirect("/customer/menu?restaurantId={$sandbox['restaurant']->id}&tableId=1");
        }

        return redirect('/dashboard')->with('info', '⚡ You are currently in an Isolated Demo Sandbox. Your data is temporary.');
    }

    /**
     * Reset the active demo sandbox to a fresh state
     */
    public function resetDemo(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->restaurant || !$user->restaurant->is_demo) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Not in a demo sandbox session.'], 403);
            }
            return redirect('/')->with('error', 'Unauthorized.');
        }

        $currentRest = $user->restaurant;
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $sandbox = $this->sandboxService->resetSandbox($currentRest);

        Auth::login($sandbox['user']);
        $request->session()->regenerate();
        session([
            'is_demo' => true,
            'demo_session_id' => $sandbox['demo_session_id'],
            'demo_expires_at' => $sandbox['restaurant']->expires_at->toIso8601String(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Demo sandbox reset successfully.',
                'demo_session_id' => $sandbox['demo_session_id']
            ]);
        }

        return redirect('/dashboard')->with('success', 'Demo sandbox reset to brand new state!');
    }

    /**
     * Exit Demo Sandbox & Clean up temporary records
     */
    public function exitDemo(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->restaurant && $user->restaurant->is_demo) {
            $this->sandboxService->destroySandbox($user->restaurant);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('info', 'Demo session ended. Temporary sandbox records cleared.');
    }
}
