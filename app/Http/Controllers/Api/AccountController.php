<?php

namespace App\Http\Controllers\Api;

use App\Events\AccountReleased;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\BvnConsent;
use App\Models\BvnStatucRecorder;
use App\Models\CooperateAccountRequest;
use App\Models\Profile;
use App\Models\Wallet;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
// use Str;

class AccountController extends Controller
{
    // Method to create an individual account
    protected $accessToken;
    public function __construct()
    {
        $this->accessToken = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI4MTUiLCJ0b2tlbklkIjoiZGE1YjM5ZDItMGE2MS00MGE5LTg2ZGYtNTFjNDE5NmU4MmMyIiwiaWF0IjoxNzMxOTIyNjMyLCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.D8lFZCna6PZNIXnmJt-Xwc2JJ9rYxNPv4x5yDwRnldGs6tZu8KAlCoXumVIcXuUrOvcEud0hSIkQ7hZUjsFh7Q';
    }
    public function createIndividualAccount(Request $request)
    {
        // Validate the request parameters
        $validation = Validator::make($request->all(), [
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'dob' => 'required|string',
            'phone' => 'required|string',
            'profilePicture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validation->fails()) {
            $errorMessage = $validation->errors()->first();
            return response()->json(['message' => $errorMessage, 'errors' => $validation->errors(), 'status' => 'error']);
        }

        $userId = Auth::user()->id;
        $existingAccount = Account::where('user_id', $userId)
            ->first();

        // if ($existingAccount) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'User already has an account with us.',
        //         'data' => $existingAccount
        //     ], 409); // 409 Conflict status
        // }

        // Handle profile picture upload if present
        $profilePicturePath = null;
        if ($request->hasFile('profilePicture')) {
            $profilePicture = $request->file('profilePicture');
            $fileName = uniqid() . '.' . $profilePicture->getClientOriginalExtension();
            $profilePicturePath = $profilePicture->storeAs('profile_pictures', $fileName, 'public');
        }
        $account = new Account();
        $account->user_id = $userId;
        $account->account_type = 'individual';
        $account->status = 'PND';
        $account->lastName = $request->lastName;
        $account->firstName = $request->firstName;
        $account->phone = $request->phone;
        $account->account_number = "mx-bill-pay". uniqid();
        $account->bvn = "000000";
        $account->profile_picture = $profilePicturePath;
        $account->accountBalance= "0.0";
        $account->save();
        if ($account) {
            $wallet = new Wallet();
            $wallet->user_id = $userId;
            $wallet->accountBalance = 0.0;
            $wallet->save();
            if ($wallet) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Account created successfully',
                    'data' => $account
                ], 201); // 201 Created status
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create wallet',
                    'data' => null
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create account',
                'data' => null
            ], 500);
        }
    }


    // Method to handle API responses
    private function handleApiResponse($response)
    {
        if ($response->successful()) {
            return response()->json($response->json(), 200);
        } else {
            return response()->json(['error' => $response->json()], $response->status());
        }
    }
    private function logApiCall($endpoint, $method, $requestData, $responseData)
    {
        Log::info("API Call: $method $endpoint", [
            'request' => $requestData,
            'response' => $responseData
        ]);
    }

}
