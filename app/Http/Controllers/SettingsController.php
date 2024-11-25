<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    // Retrieve all settings
    public function index()
    {
        return Setting::with(['user', 'category'])->get();
    }

    // Create a new setting
    public function store(Request $request)
    {
        $validated = $request->validate([
            'userID' => 'required|exists:users,userID',
            'categoryID' => 'required|exists:categories,categoryID',
            'budgetPerCategory' => 'nullable|numeric',
            'budgetPerExpense' => 'nullable|numeric',
        ]);

        $setting = Setting::create($validated);

        return response()->json(['message' => 'Setting created successfully', 'setting' => $setting], 201);
    }

    // Retrieve a single setting by ID
    public function show(Setting $setting)
    {
        return $setting->load(['user', 'category']);
    }

    // Update an existing setting
    public function update(Request $request, Setting $setting)
    {
        $validated = $request->validate([
            'budgetPerCategory' => 'nullable|numeric',
            'budgetPerExpense' => 'nullable|numeric',
        ]);

        $setting->update($validated);

        return response()->json(['message' => 'Setting updated successfully', 'setting' => $setting]);
    }

    // Delete a setting
    public function destroy(Setting $setting)
    {
        $setting->delete();

        return response()->json(['message' => 'Setting deleted successfully']);
    }
}
