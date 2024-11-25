<?php

namespace App\Http\Controllers;

use App\Models\BudgetData;
use Illuminate\Http\Request;

class BudgetDataController extends Controller
{
    // Retrieve all budgets
    public function index()
    {
        return BudgetData::with(['user', 'category'])->get();
    }

    // Create a new budget
    public function store(Request $request)
    {
        $validated = $request->validate([
            'userID' => 'required|exists:users,userID',
            'categoryID' => 'required|exists:categories,categoryID',
            'budgetLimit' => 'required|numeric',
            'budgetNotes' => 'nullable|string',
        ]);

        $budget = BudgetData::create($validated);

        return response()->json(['message' => 'Budget created successfully', 'budget' => $budget], 201);
    }

    // Retrieve a single budget by ID
    public function show(BudgetData $budgetData)
    {
        return $budgetData->load(['user', 'category']);
    }

    // Update an existing budget
    public function update(Request $request, BudgetData $budgetData)
    {
        $validated = $request->validate([
            'budgetLimit' => 'required|numeric',
            'budgetNotes' => 'nullable|string',
        ]);

        $budgetData->update($validated);

        return response()->json(['message' => 'Budget updated successfully', 'budget' => $budgetData]);
    }

    // Delete a budget
    public function destroy(BudgetData $budgetData)
    {
        $budgetData->delete();

        return response()->json(['message' => 'Budget deleted successfully']);
    }
}namespace App\Http\Controllers;

use App\Models\BudgetData;
use Illuminate\Http\Request;

class BudgetDataController extends Controller
{
    // Retrieve all budgets
    public function index()
    {
        return BudgetData::with(['user', 'category'])->get();
    }

    // Create a new budget
    public function store(Request $request)
    {
        $validated = $request->validate([
            'userID' => 'required|exists:users,userID',
            'categoryID' => 'required|exists:categories,categoryID',
            'budgetLimit' => 'required|numeric',
            'budgetNotes' => 'nullable|string',
        ]);

        $budget = BudgetData::create($validated);

        return response()->json(['message' => 'Budget created successfully', 'budget' => $budget], 201);
    }

    // Retrieve a single budget by ID
    public function show(BudgetData $budgetData)
    {
        return $budgetData->load(['user', 'category']);
    }

    // Update an existing budget
    public function update(Request $request, BudgetData $budgetData)
    {
        $validated = $request->validate([
            'budgetLimit' => 'required|numeric',
            'budgetNotes' => 'nullable|string',
        ]);

        $budgetData->update($validated);

        return response()->json(['message' => 'Budget updated successfully', 'budget' => $budgetData]);
    }

    // Delete a budget
    public function destroy(BudgetData $budgetData)
    {
        $budgetData->delete();

        return response()->json(['message' => 'Budget deleted successfully']);
    }
}