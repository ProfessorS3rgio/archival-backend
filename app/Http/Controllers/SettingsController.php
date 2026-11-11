<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    /**
     * Show the settings page
     */
    public function index()
    {
        $setting = Setting::first();
        
        return Inertia::render('Settings/Tools', [
            'settings' => [
                'auto_fill' => $setting ? $setting->auto_fill : true,
            ]
        ]);
    }

    /**
     * Update the auto-fill setting
     */
    public function updateAutoFill(Request $request)
    {
        $request->validate([
            'auto_fill' => 'required|boolean',
        ]);

        if ($request->user()->role === 'staff') {
            // Document controls affect this staff session, never system settings.
            $request->session()->put('staff_auto_fill', $request->boolean('auto_fill'));
            return response()->json(['success' => true, 'auto_fill' => $request->boolean('auto_fill')]);
        }

        $setting = Setting::first();
        $oldValue = $setting ? $setting->auto_fill : null;

        Setting::setAutoFillEnabled($request->auto_fill);

        // Log the setting change
        LoggingService::logCustom(
            'update',
            'settings',
            $setting ? $setting->id : null,
            ['auto_fill' => $oldValue],
            ['auto_fill' => $request->auto_fill],
            "Updated auto-fill setting from " . ($oldValue ? 'enabled' : 'disabled') . " to " . ($request->auto_fill ? 'enabled' : 'disabled')
        );

        // If the request expects JSON (AJAX), return JSON to avoid redirecting
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'auto_fill' => (bool) $request->auto_fill]);
        }

        return back()->with('success', 'Auto-fill setting updated successfully.');
    }
}
