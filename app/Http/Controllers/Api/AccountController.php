<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    // Method to create an individual account
    protected $accessToken;
    public function __construct()
    {
        $this->accessToken = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI4MTUiLCJ0b2tlbklkIjoiZmIzNDczZjAtY2ZiNS00ZDQzLTk1Y2EtNWE2NjdlZTZmZjdkIiwiaWF0IjoxNzI4OTg5MDMwLCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.W_P0TOjlgGN3fgi0wzv7EVLDoRXa45EmhRQVj0-NEjaH5hxkCJaDLqTeC7f4snDE0BbG2GTYubYEthW0inCShg';
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
      
        if($validation->fails()){
            $errorMessage=$validation->errors()->first();
            return response()->json(['message'=>$errorMessage,'errors'=>$validation->errors(),'status'=>'error']);
        }
        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
        }
        $accessToken = $this->accessToken;
        $response = Http::withHeaders(['AccessToken' => $accessToken])
            ->timeout(120)
            ->post('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/client/individual', [
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'dob' => $request->dob,
                'phone' => $request->phone,
                'bvn' => $request->bvn,
            ]);
        $this->logApiCall('/client/individual', 'POST', $request->all(), $response->json());
        if ($response) {
            $accountData = $response->json();
            $account = new Account();
            $account->user_id = $request->user_id;
            $account->account_number = $accountData['data']['accountNo'];
            $account->account_type = 'individual';
            $account->status = 'PND';
            $account->bvn = $request->bvn;
            $account->profile_picture = $profilePicturePath;
            $account->save();
            return response()->json($account, 201);
        }

        return $this->handleApiResponse($response);
    }
    public function requestBvnConsent(Request $request)
    {
        $request->validate([
            'bvn' => 'required|string',
            'type' => 'required|string',
            'reference' => 'nullable|string|max:250',
        ]);

        $accessToken = $this->accessToken;

        $response = Http::withHeaders(['AccessToken' => $accessToken])
            ->get('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/bvn-consent', [
                'bvn' => $request->bvn,
                'type' => $request->type,
                'reference' => $request->reference,
            ]);

        // Log the API call
        $this->logApiCall('/bvn-consent', 'POST', $request->all(), $response->json());

        return $this->handleApiResponse($response);
    }
    public function releaseAccount(Request $request)
    {
        $request->validate(['accountNo' => 'required|string']);
        $accessToken = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI4MTUiLCJ0b2tlbklkIjoiMTM5ZDczODgtNjBkMC00YjQ5LTg5MjYtN2Y5YjQ1NDUzMzU4IiwiaWF0IjoxNzI4Njc2MTI1LCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.FYUuQ4uknYKBUOxlQAbOP7yKsgi-ftvzumzkIpi0AYE-oQuOtdzoS8le_uVML1ARw6RZ6Epdl28VOrV8KzQLZw';
        $response = Http::withHeaders(['AccessToken' => $accessToken])
            ->post('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/client/release', [
                'accountNo' => $request->accountNo,
            ]);
        $this->logApiCall('/client/release', 'POST', $request->all(), $response->json());

        return $this->handleApiResponse($response);
    }

    // Method to handle API responses
    private function handleApiResponse($response)
    {
        if ($response->successful()) {
            return response()->json($response->json(), 200);
        } else {
            return response()->json(['error' => $response->json()['message']], $response->status());
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
        // Validate incoming request
        $validatedData = $request->validate([
            'status' => 'string', // Made optional
            'message' => 'string', // Made optional
            'data.bvn' => 'string', // Made optional
            'data.status' => 'boolean', // Made optional
            'data.reference' => 'string', // Made optional
        ]);

        // Log the webhook notification
        Log::info('BVN Consent Notification Received:', $validatedData);

        // Retrieve the account number using the provided BVN
        $account = Account::where('bvn', $validatedData['data']['bvn'] ?? null)->first();

        if ($account) {
            $releaseResponse = $this->releaseAccount((object) ['accountNo' => $account->account_number]);
            Log::info('Account Released Response:', $releaseResponse);
            return response()->json(['message' => 'Webhook received and processed successfully'], 200);
        } else {
            Log::error('No account found for BVN: ' . ($validatedData['data']['bvn'] ?? 'N/A'));
            return response()->json(['message' => 'Account not found'], 404);
        }
    }
}
