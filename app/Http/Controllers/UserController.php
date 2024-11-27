<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\EmailVerification;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;

class UserController extends Controller
{

    // Register a new user
    public function register(Request $request)
    {
        try {
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
                'userPassword' => $request->userPassword, // Hash the password
            ]);

            // Log user creation
            \Log::info('User created successfully', ['user' => $user]);

            // Generate the email verification token
            $token = Str::random(60);  // Generate a random token
            \Log::info('Generated token', ['token' => $token]);

            // Create the email verification record
            $verification = EmailVerification::create([
                'user_id' => $user->userID, // Ensure the user_id matches the user's userID
                'token' => $token,
                'expires_at' => Carbon::now()->addHours(24),
            ]);

            // Log verification record creation
            \Log::info('Email verification record created', ['verification' => $verification]);

            $verificationUrl = url("/verify-email?token={$token}");
            \Log::info('Verification URL', ['verificationUrl' => $verificationUrl]);
            Mail::to($user->userEmail)->send(new EmailVerificationMail($verificationUrl));
            
            return response()->json([
                'message' => 'User registered successfully. A verification email has been sent to your email address.',
                'user' => $user,
            ], 201);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error during registration', ['exception' => $e]);

            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }
    

    // Verify Email
    public function verifyEmail(Request $request)
    {
        try {
            $verification = EmailVerification::where('token', $request->token)
                                              ->where('expires_at', '>', Carbon::now())
                                              ->first();
    
            if ($verification) {
                $user = User::find($verification->user_id);
                if ($user) {
                    // Log before updating the email_verified_at field
                    \Log::info('Verifying email for user', ['user_id' => $user->userID]);
    
                    // Set email_verified_at field
                    $user->email_verified_at = Carbon::now();
                    
                    // Save the user after updating the email_verified_at field
                    $userSaved = $user->save();
    
                    // Log the result of the save operation
                    if ($userSaved) {
                        \Log::info('Email verified and updated successfully', [
                            'user_id' => $user->userID,
                            'email_verified_at' => $user->email_verified_at
                        ]);
                    } else {
                        \Log::error('Failed to save email verification', ['user_id' => $user->userID]);
                    }
    
                    // Delete the verification record after success
                    $verification->delete();
    
                    return response()->json(['message' => 'Email verified successfully.'], 200);
                } else {
                    return response()->json(['error' => 'User not found.'], 404);
                }
            } else {
                return response()->json(['error' => 'Invalid or expired token.'], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Error during email verification', ['exception' => $e]);
            return response()->json(['error' => 'An unexpected error occurred during email verification.'], 500);
        }
    }
      

    // Login user
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userEmail' => 'required|email',
            'userPassword' => 'required|min:6',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Trim and sanitize email input
        $email = strtolower(trim($request->userEmail));
    
        $user = User::whereRaw('LOWER(userEmail) = ?', [$email])->first();
    
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }
    
        // Log the user found
        \Log::info('User found for login', ['user_id' => $user->userID, 'userEmail' => $user->userEmail]);
    
        // Check if the user's email is verified
        if (!$user->email_verified_at) {
            return response()->json(['error' => 'Please verify your email address before logging in.'], 403);
        }
    
        // Log the password entered
        \Log::info('Password entered for login', ['userPassword' => $request->userPassword]);
    
        // Log the hashed password from the database
        \Log::info('Hashed password from database', ['user_id' => $user->userID, 'hashed_password' => $user->userPassword]);
    
        // Verify password
        if (Hash::check($request->userPassword, $user->userPassword)) {
            \Log::info('Login successful for user', ['user_id' => $user->userID]);
    
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user,
            ], 200);
        }
    
        \Log::info('Invalid credentials for user', ['user_id' => $user->userID]);
        return response()->json(['error' => 'Invalid credentials.'], 401);
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
