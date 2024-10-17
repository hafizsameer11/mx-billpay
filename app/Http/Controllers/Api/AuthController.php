<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'email' => 'required|string|email|unique:users',
            'password' => 'required|string', // Laravel uses 'confirmed' rule for password confirmation
            'confirmPassword' => 'required|string|same:password' // Custom confirmation field 'confirmPassword'
        ], [
            // Custom error messages for specific fields
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
                'errors' => $validator->errors(),
                'status' => 'error' // Detailed validation errors for developer
            ], 422);
        }
        $otp = mt_rand(1000, 9999);


        try {
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'otp' => $otp,
                'otp_verified' => null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unable to register user.',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
        Mail::send('emails.otp_email', ['otp' => $otp], function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Your OTP Code');
        });
        return response()->json([
            'message' => 'Registration successful. Please Check Your Email!',
            'user' => $user,
            'token' => $user->createToken('API Token')->plainTextToken,
            'user_id' => $user->id,
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
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
            'password.required' => 'Password is required.'
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();

            return response()->json([
                'message' => $errorMessage,
                'errors' => $validator->errors(),
                'status' => 'error'
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid email or password.',
                'error' => 'The email or password entered is incorrect.',
                'status' => 'error' // Detailed error for developer
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
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
            'otp' => 'required|digits:4',
        ], [
            'user_id.required' => 'User id is required',
            'user_id.string' => 'User id must be a string',
            'otp.required' => 'Otp is required',
            'otp.digits' => 'Otp must be 4 digits',
        ]);
        // $request->validate();
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return response()->json(['message' => $errorMessage, 'errors' => $validator->errors(), 'status' => 'error'], 500);
        }
        $id = $request->user_id;
        $user = User::where('id', $id)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found.', 'status' => 'error'], 404);
        }
        if ($user->otp === $request->otp) {
            $user->otp_verified = true;
            $user->otp = null;
            $user->save();
            return response()->json(['message' => 'Success. Otp Verification Completed', 'status' => 'success']);
        }
        return response()->json(['message' => 'Error', 'error' => 'Invalid OTP.', 'status' => 'error'], 400);
    }
    public function resendotp(Request $request)
    {
        // $request->validate();

        $validator = Validator::make($request->all(), [
            'email' => 'required'
        ], [
            'email.required' => 'Email is required'
        ]);
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return response()->json(['message' => $errorMessage, 'error' => $validator->errors(), 'status' => 'error'], 500);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found.', 'status' => 'error'], 404);
        } else {
            $otp = rand(1000, 9999);
            $user->otp = $otp;
            $user->save();
            Mail::send([], [], function ($message) use ($request, $otp) {
                $message->to($request->email)
                    ->subject('Your OTP Code')
                    ->setBody("Your OTP code is: $otp", 'text/html');
            });
            return response()->json(['message' => 'Otp sent successfully', 'status' => 'success'], 200);
        }
    }
    //forget password
    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',

        ], [
            'email.required' => 'Email is required',
        ]);
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return response()->json(['message' => $errorMessage, 'error' => $validator->errors(), 'status' => 'error']);
        }
        $otp = rand(1000, 9999);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found.', 'status' => 'error'], 404);
        } else {
            $passwordReset = new PasswordReset();
            $passwordReset->email = $request->email;
            $passwordReset->user_id = $user->id;
            $passwordReset->otp = $otp;
            $passwordReset->save();

            Mail::send([], [], function ($message) use ($request, $otp) {
                $message->to($request->email)
                    ->subject('Password Reset OTP Code')
                    ->setBody("Your Password Reset OTP code is: $otp", 'text/html');
            });
            return response()->json(['message' => 'Otp sent successfully', 'status' => 'success','user_id'=>$user->id], 200);
        }
    }

    public function verifyResetPasswordOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required',
            'user_id'=>'required'
        ], [
            'otp.required' => 'Otp is required',
            'user_id.required'=>'User Id Required'
        ]);
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return response()->json(['message' => $errorMessage, 'error' => $validator->errors(), 'status' => 'error']);
        } else {
            $passwordReset = PasswordReset::where('otp', $request->otp)->where('user_id',$request->user_id)->first();
            if (!$passwordReset) {
                return response()->json(['message' => 'Invalid otp', 'status' => 'error'], 404);
            } else {
                return response()->json(['message' => 'Otp is valid', 'status' => 'success','user_id'=>$passwordReset->user_id], 200);
            }
        }
    }
    public function resetPassword(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'new_password' => 'required|min:6',
        'confirm_password' => 'required|same:new_password'
    ], [
        'user_id.required' => 'User ID is required',
        'user_id.exists' => 'User not found',
        'new_password.required' => 'New password is required',
        'new_password.min' => 'Password must be at least 6 characters long',
        'confirm_password.required' => 'Please confirm your password',
        'confirm_password.same' => 'Passwords do not match'
    ]);

    if ($validator->fails()) {
        $errorMessage = $validator->errors()->first();
        return response()->json(['message' => $errorMessage, 'error' => $validator->errors(), 'status' => 'error'], 400);
    }
    $user = User::find($request->user_id);
    if (!$user) {
        return response()->json(['message' => 'User not found', 'status' => 'error'], 404);
    }
    $user->password = bcrypt($request->new_password);
    $user->save();
    PasswordReset::where('user_id', $request->user_id)->delete();
    return response()->json(['message' => 'Password reset successfully', 'status' => 'success'], 200);
}

}
