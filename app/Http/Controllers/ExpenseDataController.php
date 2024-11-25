<?php


namespace App\Http\Controllers;

use App\Models\ExpenseData;
use Illuminate\Http\Request;

class ExpenseDataController extends Controller
{
    // Retrieve all expenses
    public function index()
    {
        return ExpenseData::with(['user', 'category'])->get();
    }

    // Create a new expense
    public function store(Request $request)
    {
        $validated = $request->validate([
            'userID' => 'required|exists:users,userID',
            'categoryID' => 'required|exists:categories,categoryID',
            'expenseName' => 'required|string|max:255',
            'expenseAmount' => 'required|numeric',
            'expenseDescription' => 'nullable|string',
        ]);

        $expense = ExpenseData::create($validated);

        return response()->json(['message' => 'Expense created successfully', 'expense' => $expense], 201);
    }

    // Retrieve a single expense by ID
    public function show(ExpenseData $expenseData)
    {
        return $expenseData->load(['user', 'category']);
    }

    // Update an existing expense
    public function update(Request $request, ExpenseData $expenseData)
    {
        $validated = $request->validate([
            'expenseName' => 'required|string|max:255',
            'expenseAmount' => 'required|numeric',
            'expenseDescription' => 'nullable|string',
        ]);

        $expenseData->update($validated);

        return response()->json(['message' => 'Expense updated successfully', 'expense' => $expenseData]);
    }

    // Delete an expense
    public function destroy(ExpenseData $expenseData)
    {
        $expenseData->delete();

        return response()->json(['message' => 'Expense deleted successfully']);
    }
}
