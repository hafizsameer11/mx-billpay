<?php

namespace App\Http\Controllers\Api;

use App\Events\AccountReleased;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\BvnConsent;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
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
        $validation = Validator::make($request->all(), [
            'userId' => 'required|string',
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'dob' => 'required|string',
            'phone' => 'required|string',
            'bvn' => 'required|string',
            'profilePicture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Optional profi
        ]);

        if ($validation->fails()) {
            $errorMessage = $validation->errors()->first();
            return response()->json(data: ['message' => $errorMessage, 'errors' => $validation->errors(), 'status' => 'error']);
        }
        if ($request->hasFile('profilePicture')) {
            $profilePicture = $request->file('profilePicture'); // Laravel will handle the file object
            $fileName = uniqid() . '.' . $profilePicture->getClientOriginalExtension();
            $profilePicturePath = $profilePicture->storeAs('profile_pictures', $fileName, 'public'); // Save the file
        } else {
            $profilePicturePath = "NULL";
        }
        $accessToken = $this->accessToken;
        $response = Http::withHeaders(['AccessToken' => $accessToken])
            ->timeout(220)
            ->post('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/client/individual', [
                'firstname' => $request->firstName,
                'lastname' => $request->lastName,
                'dob' => $request->dob,
                'phone' => $request->phone,
                'bvn' => $request->bvn,
            ]);

        $responseData = $response->json();

        if ($response->successful()) {

            if ($responseData['status'] == "00") {
                $accountData = $response->json();
                $account = new Account();
                $account->user_id = $request->userId;
                $account->account_number = $accountData['data']['accountNo'];
                $account->account_type = 'individual';
                $account->status = 'PND';
                $account->lastName = $request->lastName;
                $account->firstName = $request->firstName;
                $account->phone = $request->phone;
                $account->bvn = $request->bvn;
                $account->profile_picture = $profilePicturePath; // Save the image path
                $account->save();
                return response()->json(['message' => 'Account created successfully', 'data' => $account], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => "Account Database Save failed", 'response' => $responseData]);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Went Wrong Please Try again','data'=>$responseData]);
        }
    }
    public function createCorporateAccount(Request $request)
    {
        // Validate the incoming request data
        $validation = Validator::make($request->all(), [
            'userId' => 'required|string',
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
            ->timeout(220)
            ->post('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/client/corporate', [
                'rcNumber' => $request->rcNumber,
                'companyName' => $request->companyName,
                'incorporationDate' => $request->incorporationDate,
                'bvn' => $request->bvn,
            ]);

        $responseData = $response->json();
        if ($response->successful() && $responseData['status'] == "00") {

            $account = new Account();
            $account->user_id = $request->userId;
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

        $validator = Validator::make($request->all(), [
            'bvn' => 'required|string',
            'type' => 'required|string',
            'userId' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }
        $reference = Str::uuid()->toString();

        $accessToken = $this->accessToken;

        $response = Http::withHeaders(['AccessToken' => $accessToken])
            ->get('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/bvn-consent', [
                'bvn' => $request->bvn,
                'type' => $request->type,
                'reference' => $reference,
            ]);  $defaultResponse = [
                'status' => 'unknown', // Example default status
                'message' => 'No response received', // Example default message
            ];
        // Store the consent record in the database
        if($response->successful()){
            BvnConsent::create([
                'bvn' => $request->bvn,
                'type' => $request->type,
                'user_id' => $request->userId,
                'reference' => $reference,

            ]);

            $this->logApiCall('/bvn-consent', 'POST', $request->all(), $response->json());

            return $this->handleApiResponse($response);
        }else{
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
            event(new AccountReleased($userId));
            $account = Account::where('user_id', $userId)->first();
            $account->status = 'RELEASED';
            $account->save();
            event(new AccountReleased($userId));

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
        $validate = Validator::make($request->all(), [
            'userId' => 'required'
        ], [
            'userId.required' => 'User ID is required',
        ]);
        if ($validate->fails()) {
            $errorMessage = $validate->errors()->first();
            return response()->json(['error' => $errorMessage], 422);
        }
        $accountNumber=Account::where('user_id',$request->userId)->first();
        $accountNumber1=$accountNumber->account_number;
        $response = Http::withHeaders(['AccessToken' => $this->accessToken])
            ->get('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/account/enquiry', [
                'accountNumber' => $accountNumber1
            ]);
        if ($response->successful()) {
            $accountData = $response->json()['data'];
            $accountStatus = $response->json()['status'];
            if ($accountStatus === '00') {
                $account = Account::where('user_id', $request->userId)->first();
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
