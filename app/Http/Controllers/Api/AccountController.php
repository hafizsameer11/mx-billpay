<?php

namespace App\Http\Controllers\Api;

use App\Events\AccountReleased;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\BvnConsent;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $this->accessToken = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI4MTUiLCJ0b2tlbklkIjoiYzVmOTA4OWMtODAyMS00ZWU3LThjNjYtNTMzMjEwZjQ0NjNkIiwiaWF0IjoxNzI5OTMyMzU2LCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.uIQKrplFvnc2ta7RMpwurkoK7guwIbYMBS00NopUxGwUlpP7TC1AqhM1_hns2NEQSw6scWABoeD2PLWpBkgPsA';
    }
    public function createIndividualAccount(Request $request)
    {
        // Validate the request parameters
        $validation = Validator::make($request->all(), [
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'dob' => 'required|string',
            'phone' => 'required|string',
            'bvn' => 'required|string',
            'profilePicture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validation->fails()) {
            $errorMessage = $validation->errors()->first();
            return response()->json(['message' => $errorMessage, 'errors' => $validation->errors(), 'status' => 'error']);
        }

        $userId = Auth::user()->id;
        $existingAccount = Account::where('user_id', $userId)
            ->orWhere('bvn', $request->bvn)
            ->first();

        if ($existingAccount) {
            return response()->json([
                'status' => 'error',
                'message' => 'User already has an account with us.',
                'data' => $existingAccount
            ], 409); // 409 Conflict status
        }

        // Handle profile picture upload if present
        $profilePicturePath = null;
        if ($request->hasFile('profilePicture')) {
            $profilePicture = $request->file('profilePicture');
            $fileName = uniqid() . '.' . $profilePicture->getClientOriginalExtension();
            $profilePicturePath = $profilePicture->storeAs('profile_pictures', $fileName, 'public');
        }

        // Create a new account
        $account = new Account();
        $account->user_id = $userId;
        $account->account_type = 'individual';
        $account->status = 'PND';
        $account->lastName = $request->lastName;
        $account->firstName = $request->firstName;
        $account->phone = $request->phone;
        $account->account_number = "000"; // Placeholder until successful creation
        $account->bvn = $request->bvn;
        $account->profile_picture = $profilePicturePath;
        $account->save();

        // Prepare for API call
        $accessToken = $this->accessToken;
        $apiEndpoint = 'https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/client/individual';
        $apiPayload = [
            'firstname' => $request->firstName,
            'lastname' => $request->lastName,
            'dob' => $request->dob,
            'phone' => $request->phone,
            'bvn' => $request->bvn,
        ];

        // Make the API request
        try {
            $response = Http::withHeaders(['AccessToken' => $accessToken])
                ->timeout(300)
                ->post($apiEndpoint, $apiPayload);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Connection error while creating account', ['error' => $e->getMessage()]);
            $account->delete();
            return response()->json([
                'status' => 'error',
                'message' => 'API connection error. Account not created.',
            ], 400);
        }

        // Handle the API response
        if ($response->successful()) {
            $responseData = $response->json();
            //log the account creation info in a seperate log file
            $logFile = storage_path('logs/account_creation.log');
            $accountCreationInfo = "Account created successfully for user $userId with BVN $request->bvn";
            file_put_contents($logFile, $accountCreationInfo . PHP_EOL, FILE_APPEND);
            switch ($responseData['status']) {
                case "00":
                    // Successful creation
                    $account->account_number = $responseData['data']['accountNo'];
                    $account->save();
                    return response()->json(['message' => 'Account created successfully', 'data' => $account], 200);

                case "01":
                    if (isset($responseData['data']['accountNo'])) {
                        $account->account_number = $responseData['data']['accountNo'];
                        $account->status = 'EXIST';
                        $account->save();

                        return response()->json([
                            'status' => 'success',
                            'message' => 'User already has an account, updated with existing account number.',
                            'data' => $account
                        ], 200);
                    } else {
                        $account->delete();
                        return response()->json([
                            'status' => 'error',
                            'message' => $responseData['message'],
                        ], 400);
                    }

                case "929":
                    $account->delete();
                    return response()->json([
                        'status' => 'error',
                        'message' => $responseData['message'] ?? 'Error creating client. Contact Admin.',
                    ], 400);

                case "199":
                    return response()->json([
                        'status' => 'error',
                        'message' => $responseData['message'],
                    ], 403);

                case "119":
                    $account->delete();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Not authorized to create clients.',
                    ], 403);

                default:
                    $account->delete();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Unexpected error occurred. Please try again.',
                        'response' => $responseData
                    ], 500);
            }
        } else {
            Log::error('API call failed', ['response' => $response->body()]);
            $account->delete();
            return response()->json([
                'status' => 'error',
                'message' => $response->json()['message'] ?? 'API call failed. Account not created.',
                'response' => $response->json() ?? 'No response from API'
            ], 400);
        }
    }




    public function createCorporateAccount(Request $request)
    {
        // Validate the incoming request data
        $userId = Auth::user()->id;
        $validation = Validator::make($request->all(), [
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'phone' => 'required|string',
            'rcNumber' => 'required|string',
            'companyName' => 'required|string',
            'incorporationDate' => 'required|string', // Ensure proper date format
            'bvn' => 'required|string',
            'profilePicture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Optional profile picture
        ]);

        if ($validation->fails()) {
            $errorMessage = $validation->errors()->first();
            return response()->json(['message' => $errorMessage, 'errors' => $validation->errors(), 'status' => 'error'], 422);
        }

        // Handle profile picture upload if present
        $profilePicturePath = null;
        if ($request->hasFile('profilePicture')) {
            $profilePicture = $request->file('profilePicture');
            $fileName = uniqid() . '.' . $profilePicture->getClientOriginalExtension();
            $profilePicturePath = $profilePicture->storeAs('profile_pictures', $fileName, 'public'); // Save the file
        }

        // Prepare the request for the external API
        $accessToken = $this->accessToken;
        $response = Http::withHeaders(['AccessToken' => $accessToken])
            ->timeout(300)
            ->post('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/client/corporate', [
                'rcNumber' => $request->rcNumber,
                'companyName' => $request->companyName,
                'incorporationDate' => $request->incorporationDate,
                'bvn' => $request->bvn,
            ]);

        $responseData = $response->json();
        if ($response->successful() && $responseData['status'] == "00") {

            $account = new Account();
            $account->user_id = $userId;
            $account->account_number = $responseData['data']['accountNo'];
            $account->account_type = 'corporate';
            $account->status = 'PND';
            $account->lastName = $request->lastName;
            $account->firstName = $request->firstName;
            $account->phone = $request->phone;
            $account->bvn = $request->bvn;
            $account->profile_picture = $profilePicturePath; // Save the image path
            $account->save();

            return response()->json(['message' => 'Corporate account created successfully', 'data' => $account], 201);
        } else {
            return response()->json(['status' => 'error', 'message' => $responseData['message'] ?? 'Failed to create corporate account'], 400);
        }
    }

    public function requestBvnConsent(Request $request)
    {

        $userId = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'bvn' => 'required|string',
            'type' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }
        $reference =  'mxPay-' . mt_rand(1000, 9999);

        $accessToken = $this->accessToken;

        $response = Http::withHeaders(['AccessToken' => $accessToken])
            ->get('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/bvn-consent', [
                'bvn' => $request->bvn,
                'type' => $request->type,
                'reference' => $reference,
            ]);
        $defaultResponse = [
            'status' => 'unknown', // Example default status
            'message' => 'No response received', // Example default message
        ];
        // Store the consent record in the database
        if ($response->successful()) {
            BvnConsent::create([
                'bvn' => $request->bvn,
                'type' => $request->type,
                'user_id' => $userId,
                'reference' => $reference,

            ]);

            $this->logApiCall('/bvn-consent', 'POST', $request->all(), $response->json());

            return $this->handleApiResponse($response);
        } else {
            $this->logApiCall('/bvn-consent', 'POST', $request->all(), $defaultResponse);
            return response()->json(['status' => 'error', 'message' => 'something went wrong'], 400);
        }
    }
    public function releaseAccount($accountNo, $userId)
    {
        $response = Http::withHeaders(['AccessToken' => $this->accessToken])
            ->post('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/client/release', [
                'accountNo' => $accountNo,
            ]);

        // Log the API call
        $this->logApiCall('/client/release', 'POST', ['accountNo' => $accountNo], $response->json());
        if ($response->successful()) {

            $account = Account::where('user_id', $userId)->first();
            $account->status = 'RELEASED';
            $account->save();
            $pusherRouteResponse = Http::get(route('test-pusher', ['userId' => $userId]));

            Log::info('Pusher Response:');
            Log::info('Account released successfully:', $response->json());

            return response()->json(['message' => 'Account released successfully', 'data' => $response->json()], 200);
        } else {
            $errorResponse = $response->json();
            Log::error('API Error Response:', $errorResponse);
            return response()->json(['error' => $errorResponse['message']], $response->status());
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
    public function handleBvnConsentWebhook(Request $request)
    {
        $validatedData = $request->validate([
            'status' => 'string',
            'message' => 'string',
            'data.bvn' => 'string',
            'data.status' => 'boolean',
            'data.reference' => 'string',
        ]);

        Log::info('BVN Consent Notification Received:', $validatedData);

        $account = Account::where('bvn', $validatedData['data']['bvn'] ?? null)->first();

        if ($account) {
            $releaseResponse = $this->releaseAccount($account->account_number, $account->user_id);
            Log::info('Account Released Response:', $releaseResponse->getData(true));
            return response()->json(['message' => 'Webhook received and processed successfully'], 200);
        } else {
            Log::error('No account found for BVN: ' . ($validatedData['data']['bvn'] ?? 'N/A'));
            return response()->json(['message' => 'Account not found'], 404);
        }
    }

    public function accountEnquiry(Request $request)
    {
        $userId = Auth::user()->id;

        $accountNumber = Account::where('user_id', $userId)->first();
        $accountNumber1 = $accountNumber->account_number;
        $response = Http::withHeaders(['AccessToken' => $this->accessToken])
            ->get('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/account/enquiry', [
                'accountNumber' => $accountNumber1
            ]);
        if ($response->successful()) {
            $accountData = $response->json()['data'];
            $accountStatus = $response->json()['status'];
            if ($accountStatus === '00') {
                $account = Account::where('user_id', $userId)->first();
                if ($account) {
                    $account->accountBalance = $accountData['accountBalance'];
                    $account->save();
                    if (is_null($account->accountId)) {
                        $account->accountId = $accountData['accountId'];
                        $account->client = $accountData['client'];
                        $account->clientId = $accountData['clientId'];
                        $account->savingsProductName = $accountData['savingsProductName'];
                        $account->save();
                    }
                    return response()->json($account, 200);
                }
                return response()->json(['message' => 'Account not found'], 404);
            } else {
                return response()->json(['error' => 'Invalid account status', 'status' => $accountStatus], 400);
            }
        } else {
            return response()->json(['error' => 'Failed to fetch account details', 'details' => $response->json()], $response->status());
        }
    }
}
