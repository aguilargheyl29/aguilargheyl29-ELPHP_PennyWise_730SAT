<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Retrieve all categories
    public function index()
    {
        return Category::all();
    }

    // Create a new category
    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoryName' => 'required|string|max:255|unique:categories,categoryName',
            'categoryDescription' => 'nullable|string',
        ]);

        $category = Category::create($validated);

        return response()->json(['message' => 'Category created successfully', 'category' => $category], 201);
    }

    // Retrieve a single category by ID
    public function show(Category $category)
    {
        return $category;
    }

    // Update an existing category
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'categoryIcon' => 'nullable|string|max:255',
            'categoryName' => 'required|string|max:255|unique:categories,categoryName,' . $category->categoryID,
            'categoryDescription' => 'nullable|string',
        ]);

        $category->update($validated);

        return response()->json(['message' => 'Category updated successfully', 'category' => $category]);
    }

    // Delete a category
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}