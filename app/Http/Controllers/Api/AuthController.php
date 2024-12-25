<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Notification as ModelsNotification;
use App\Models\PasswordReset;
use App\Models\Pin;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $accessToken;
    protected $NotificationService;
    public function __construct(NotificationService $NotificationService)


    {
        $this->NotificationService = $NotificationService;
        $this->accessToken = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI4MTUiLCJ0b2tlbklkIjoiZGE1YjM5ZDItMGE2MS00MGE5LTg2ZGYtNTFjNDE5NmU4MmMyIiwiaWF0IjoxNzMxOTIyNjMyLCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.D8lFZCna6PZNIXnmJt-Xwc2JJ9rYxNPv4x5yDwRnldGs6tZu8KAlCoXumVIcXuUrOvcEud0hSIkQ7hZUjsFh7Q';
    }
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
                'otp_verified' => false
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

        $user = User::where('email', $request->email)->with('account')->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid email or password.',
                'error' => 'The email or password entered is incorrect.',
                'status' => 'error'
            ], 401);
        }
        $account = $user->account;
        if (!$account) {
            return response()->json([
                'statuss' => 'pending',
                'message' => 'User Does Not Have An Account. Please Create One.',
                'user' => $user,
                'token' => $user->createToken('API Token')->plainTextToken,
            ]);
        }
        $pin = Pin::where('user_id', $user->id)->first();
        $has_pin = $pin ? true : false;
        if ($user->account->profile_picture == null) {
            $profilePictureUrl = '';
        } else {

            $profilePictureUrl = asset('storage/' . $user->account->profile_picture);
        }
        $notification = new ModelsNotification();
        $notification->user_id = $user->id;
        $notification->type = "login";
        $notification->title = "User Logged In";
        $notification->message = "User Logged In Successfully";
        $notification->icon = asset('notificationLogos/profile2.png');
        $notification->iconColor = config('notification_colors.colors.Account');
        $notification->save();
        $notificationTitle = "User Logged In";
        $notificationMessage = "User Logged In Successfully";
        $notificationResponse = $this->NotificationService->sendToUserById($user->id, $notificationTitle, $notificationMessage);
        Log::info("Notification sent to userId: $user->id", $notificationResponse);
        $wallet = Wallet::where('user_id', $user->id)->first();
        return response()->json([
            'message' => 'Login successful.',
            'user' => [
                'userId' => $user->id,
                'firstName' => $user->account->firstName,
                'lastName' => $user->account->lastName,
                'email' => $user->email,
                'phone' => $user->account->phone,
                'hasPin' => $has_pin,
                'accountNumber' => $wallet->accountNumber,
                'accountBalance' => $wallet->accountBalance,
                'created_at' => $account->created_at,
                'updated_at' => $account->updated_at,
                'totalIncome' => $wallet->totalIncome,
                'totalBillPayment' => $wallet->totalBillPayment,
                'profilePicture' => $profilePictureUrl
            ],
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
        $userId = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:4',
        ], [

            'otp.required' => 'Otp is required',
            'otp.digits' => 'Otp must be 4 digits',
        ]);
        // $request->validate();
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return response()->json(['message' => $errorMessage, 'errors' => $validator->errors(), 'status' => 'error'], 500);
        }
        $id = $userId;
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
            Mail::send('emails.otp_email', ['otp' => $otp], function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Your OTP Code');
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

            Mail::send('emails.password_reset', ['otp' => $otp], function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Your OTP Code');
            });
            return response()->json(['message' => 'Otp sent successfully', 'status' => 'success', 'user_id' => $user->id, 'otp' => $otp], 200);
        }
    }

    public function verifyResetPasswordOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required',
            'userId' => 'required'
        ], [
            'otp.required' => 'Otp is required',
            'userId.required' => 'User Id Required'
        ]);
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return response()->json(['message' => $errorMessage, 'error' => $validator->errors(), 'status' => 'error']);
        } else {
            $passwordReset = PasswordReset::where('otp', $request->otp)->where('user_id', $request->userId)->first();
            if (!$passwordReset) {
                return response()->json(['message' => 'Invalid otp', 'status' => 'error'], 404);
            } else {
                return response()->json(['message' => 'Otp is valid', 'status' => 'success', 'user_id' => $passwordReset->user_id], 200);
            }
        }
    }
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required|exists:users,id',
            'newPassword' => 'required|min:6',
            'confirmPassword' => 'required|same:newPassword'
        ], messages: [
            'userId.required' => 'User ID is required',
            'userId.exists' => 'User not found',
            'newPassword.required' => 'New password is required',
            'newPassword.min' => 'Password must be at least 6 characters long',
            'confirmPassword.required' => 'Please confirm your password',
            'confirmPassword.same' => 'Passwords do not match'
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return response()->json(['message' => $errorMessage, 'error' => $validator->errors(), 'status' => 'error'], 400);
        }
        $user = User::find($request->userId);
        if (!$user) {
            return response()->json(['message' => 'User not found', 'status' => 'error'], 404);
        }
        $user->password = bcrypt($request->newPassword);
        $user->save();
        PasswordReset::where('user_id', $request->userId)->delete();
        return response()->json(['message' => 'Password reset successfully', 'status' => 'success'], 200);
    }
    public function tableclear()
    {
        $table = DB::table('users');
        $table->truncate();
        return response()->json(['message' => 'Table cleared successfully', 'status' => 'success']);
    }
}
