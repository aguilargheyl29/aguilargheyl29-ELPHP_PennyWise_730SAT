<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\EmailVerification;
use App\Mail\EmailVerificationMail;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Validate incoming request
            $validator = Validator::make($request->all(), [
                'userEmail' => 'required|email|unique:users,userEmail',  // Validate email
                'userPassword' => 'required|min:6',  // Password must be at least 6 characters
                'confirmPassword' => 'required|same:userPassword',  // Confirm password must match user password
            ]);
    
            // If validation fails, return errors
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            // Create the user - do not hash the password (store it as plain-text)
            $user = User::create([
                'userEmail' => $request->userEmail,
                'userPassword' => $request->userPassword,  // Store password as plain-text
            ]);
    
            // Log user creation for debugging
            \Log::info('User created successfully', ['user' => $user]);
    
            // Generate the email verification token
            $token = Str::random(60);  // Generate a random token
            \Log::info('Generated token', ['token' => $token]);
    
            // Create the email verification record
            $verification = EmailVerification::create([
                'user_id' => $user->userID,  // Ensure the user_id matches the user's userID
                'token' => $token,
                'expires_at' => Carbon::now()->addHours(24),  // Set expiration for token
            ]);
    
            // Log verification record creation
            \Log::info('Email verification record created', ['verification' => $verification]);
    
            // Prepare the verification URL
            $verificationUrl = url("/verify-email?token={$token}");
            \Log::info('Verification URL', ['verificationUrl' => $verificationUrl]);
    
            // Send verification email to the user
            Mail::to($user->userEmail)->send(new EmailVerificationMail($verificationUrl));
    
            // Return response to the user with success message and user info
            return response()->json([
                'message' => 'User registered successfully. A verification email has been sent to your email address.',
                'user' => $user,
            ], 201);
    
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error during registration', ['exception' => $e]);
    
            // Return a generic error response
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }
    

    // Verify Email
    public function verifyEmail(Request $request)
    {
        try {
            // Check if the token exists and is not expired
            $verification = EmailVerification::where('token', $request->token)
                                              ->where('expires_at', '>', Carbon::now())
                                              ->first();

            if ($verification) {
                // Find the user by user_id
                $user = User::find($verification->userID);
                if ($user) {
                    // Log before updating the email_verified_at field
                    \Log::info('Verifying email for user', ['user_id' => $user->userID]);

                    // Update email_verified_at field
                    $user->email_verified_at = Carbon::now();
                    
                    // Save the updated user
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

    // Sanitize inputs
    $email = strtolower(trim($request->userEmail));
    $password = trim($request->userPassword);

    // Log input for debugging
    \Log::info('Login attempt', ['email' => $email, 'password' => $password]);

    // Find the user by email (case insensitive)
    $user = User::whereRaw('LOWER(userEmail) = ?', [$email])->first();

    // Check if user was found
    if (!$user) {
        \Log::warning('User not found', ['email' => $email]);
        return response()->json(['error' => 'User not found.'], 404);
    }

    // Check password
    if (Hash::check($password, $user->userPassword)) {
        // Password is correct, generate the token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ], 200);
    } else {
        // Log the failed attempt for debugging
        \Log::warning('Invalid password attempt', ['email' => $email]);
        return response()->json(['error' => 'Invalid credentials.'], 401);
    }
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

    //forget password
    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,userEmail',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $email = $request->email;
        $token = Str::random(60);

        // Store the token in the password_resets table
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        // Send the reset email
        $resetLink = url("/reset-password?token={$token}&email={$email}");
        Mail::raw("Reset your password using this link: $resetLink", function ($message) use ($email) {
            $message->to($email)->subject('Password Reset Request');
        });

        return response()->json(['message' => 'Password reset link sent to your email.'], 200);
    }

    //reset password
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,userEmail',
            'token' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $email = $request->email;
        $token = $request->token;
        $newPassword = $request->new_password;

        // Verify token
        $passwordReset = DB::table('password_resets')->where('email', $email)->where('token', $token)->first();

        if (!$passwordReset || Carbon::parse($passwordReset->created_at)->addMinutes(60)->isPast()) {
            return response()->json(['error' => 'Invalid or expired token.'], 400);
        }

        // Update the user's password
        $user = User::where('userEmail', $email)->first();
        $user->userPassword = Hash::make($newPassword);
        $user->save();

        // Delete the password reset token
        DB::table('password_resets')->where('email', $email)->delete();

        return response()->json(['message' => 'Password reset successful.'], 200);
    }

}
