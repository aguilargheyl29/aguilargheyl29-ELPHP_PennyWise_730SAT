<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/verify-email/{token}', [UserController::class, 'verifyEmail']);
