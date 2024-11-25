<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ExpenseDataController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BudgetDataController;

// Public Routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::apiResource('settings', SettingsController::class);
Route::apiResource('expenses', ExpenseDataController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('budgets', BudgetDataController::class);

//Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', function (Request $request) {
        return $request->user();
    });
    
    // Update user profile (username, fullname, image)
    Route::post('update-profile', [UserController::class, 'updateProfile']);

    // Get all users
    Route::get('users', [UserController::class, 'getUsers']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
