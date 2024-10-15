<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullName' => 'required|string', // Changed 'name' to 'fullName'
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string', // Laravel uses 'confirmed' rule for password confirmation
            'confirmPassword' => 'required|string|same:password' // Custom confirmation field 'confirmPassword'
        ], [
            // Custom error messages for specific fields
            'fullName.required' => 'Full name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Password is required.',
            'confirmPassword.required' => 'Password confirmation is required.',
            'confirmPassword.same' => 'Password confirmation does not match.'
        ]);

        if ($validator->fails()) {
            // Return the first validation error message
            $errorMessage = $validator->errors()->first();

            return response()->json([
                'message' => $errorMessage, // Display the first validation error
                'errors' => $validator->errors() // Detailed validation errors for developer
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->fullName, // Changed to 'fullName'
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unable to register user.', // User-friendly message
                'error' => $e->getMessage() // Detailed error for developer
            ], 500);
        }

        return response()->json([
            'message' => 'Registration successful.',
            'user' => $user,
            'token' => $user->createToken('API Token')->plainTextToken,
            'status' => 'success'
        ], 201);
    }

    // Login method
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ], [
            // Custom error messages for specific fields
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
            'password.required' => 'Password is required.'
        ]);

        if ($validator->fails()) {
            // Return the first validation error message
            $errorMessage = $validator->errors()->first();

            return response()->json([
                'message' => $errorMessage, // Display the first validation error
                'errors' => $validator->errors() // Detailed validation errors for developer
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid email or password.', // Specific error for user
                'error' => 'The email or password entered is incorrect.' // Detailed error for developer
            ], 401);
        }

        return response()->json([
            'message' => 'Login successful.',
            'user' => $user,
            'token' => $user->createToken('API Token')->plainTextToken,
            'status' => 'success'
        ], 200);
    }

    // Logout method
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to log out.',
                'error' => $e->getMessage() // Detailed error for developer
            ], 500);
        }

        return response()->json([
            'message' => 'Successfully logged out.',
            'status' => 'success'
        ], 200);
    }

}
