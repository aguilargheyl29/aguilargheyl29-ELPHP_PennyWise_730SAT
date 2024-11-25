<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userEmail' => 'required|email|unique:users,userEmail',
            'userPassword' => 'required|min:6',
            'confirmPassword' => 'required|same:userPassword',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'userEmail' => $request->userEmail,
            'userPassword' => Hash::make($request->userPassword),
        ]);

        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }

    // Login a user
    public function login(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'userEmail' => 'required|email',
            'userPassword' => 'required|min:6',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Attempt to log the user in with provided email and password
        if (Auth::attempt(['userEmail' => $request->userEmail, 'userPassword' => $request->userPassword])) {
            $user = Auth::user();  // Get the authenticated user
    
            // Create a token for the authenticated user
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json(['message' => 'Login successful', 'token' => $token], 200);
        }
    
        // If authentication fails
        return response()->json(['error' => 'Invalid credentials'], 401);
    }
    // Update User Profile (Username, Fullname, Image)
    public function updateProfile(Request $request)
    {
        $user = Auth::user();  // Get the currently authenticated user

        // Validate the incoming request data
        $validated = $request->validate([
            'username' => 'sometimes|string|max:255',
            'fullname' => 'sometimes|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',  // Image validation
        ]);

        // Update username if provided
        if ($request->has('username')) {
            $user->username = $validated['username'];
        }

        // Update fullname if provided
        if ($request->has('fullname')) {
            $user->fullname = $validated['fullname'];
        }

        // Update image if provided
        if ($request->has('image')) {
            // Store the image and save the path
            $imagePath = $request->file('image')->store('images', 'public');
            $user->image = $imagePath;
        }

        // Save the updated user profile
        $user->save();

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user], 200);
    }
}
