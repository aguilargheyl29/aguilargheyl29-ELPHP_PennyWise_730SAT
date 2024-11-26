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
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'userEmail' => 'required|email|unique:users,userEmail',
            'userPassword' => 'required|min:6',
            'confirmPassword' => 'required|same:userPassword',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Create the user - setUserPasswordAttribute will automatically hash the password
        $user = User::create([
            'userEmail' => $request->userEmail,
            'userPassword' => $request->userPassword,  // Password will be hashed automatically
        ]);
    
        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }    
    
    public function login(Request $request)
    {
        \Log::info('Login attempt', $request->all()); // Log the request data for debugging
    
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'userEmail' => 'required|email',  // Ensure the email is valid
            'userPassword' => 'required|min:6',  // Ensure the password is provided
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Retrieve the user by email
        $user = User::where('userEmail', $request->userEmail)->first();
    
        // Check if the user exists
        if (!$user) {
            \Log::info('User not found', ['email' => $request->userEmail]);
            return response()->json(['error' => 'User not found'], 404);
        }
    
        \Log::info('User found', ['email' => $user->userEmail]);
    
        // Check if the password matches
        if (Hash::check($request->userPassword, $user->userPassword)) {
            \Log::info('User ID for tokenable_id', ['id' => $user->id]);
        
            $token = $user->createToken('auth_token')->plainTextToken;
        
            \Log::info('Token created', ['token' => $token]);
        
            return response()->json(['message' => 'Login successful', 'token' => $token, 'user' => $user], 200);
        }        
    }
    
    
    // // Update User Profile (Username, Fullname, Image)
    // public function updateProfile(Request $request)
    // {
    //     $user = Auth::user();  // Get the currently authenticated user

    //     // Validate the incoming request data
    //     $validated = $request->validate([
    //         'username' => 'sometimes|string|max:255',
    //         'fullname' => 'sometimes|string|max:255',
    //         'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',  // Image validation
    //     ]);

    //     // Update username if provided
    //     if ($request->has('username')) {
    //         $user->username = $validated['username'];
    //     }

    //     // Update fullname if provided
    //     if ($request->has('fullname')) {
    //         $user->fullname = $validated['fullname'];
    //     }

    //     // Update image if provided
    //     if ($request->has('image')) {
    //         // Store the image and save the path
    //         $imagePath = $request->file('image')->store('images', 'public');
    //         $user->image = $imagePath;
    //     }

    //     // Save the updated user profile
    //     $user->save();

    //     return response()->json(['message' => 'Profile updated successfully', 'user' => $user], 200);
    // }
}
