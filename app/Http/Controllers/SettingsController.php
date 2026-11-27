<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class SettingsController extends Controller
{
    /**
     * Show the settings page
     */
    public function index()
    {
        return Inertia::render('Settings/Tools');
    }
}
