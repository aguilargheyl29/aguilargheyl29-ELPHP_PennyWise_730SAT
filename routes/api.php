<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ExpenseDataController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BudgetDataController;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/verify-email/{token}', [UserController::class, 'verifyEmail']); // Updated route

Route::apiResource('users', UserController::class);
Route::apiResource('settings', SettingsController::class);
Route::apiResource('expenses', ExpenseDataController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('budgets', BudgetDataController::class);

Route::post('/forgot-password', [UserController::class, 'forgetPassword'])->name('password.email');
Route::post('/reset-password', [UserController::class, 'resetPassword'])->name('password.update');

