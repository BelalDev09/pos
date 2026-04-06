<?php

// File: app/Http/Controllers/Web/Settings/SettingsController.php

namespace App\Http\Controllers\Web\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('settings.index');
    }

    public function update(Request $request)
    {
        // Settings update logic — stub
        return back()->with('success', 'Settings updated.');
    }
}
