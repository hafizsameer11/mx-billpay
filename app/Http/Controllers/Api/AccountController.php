<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
            'profilePicture' => 'nullable|string', // Expecting base64 string
        ]);

        if ($validation->fails()) {
            $errorMessage = $validation->errors()->first();
            return response()->json(['message' => $errorMessage, 'errors' => $validation->errors(), 'status' => 'error']);
        }

        $profilePicturePath = null;
        if ($request->has('profilePicture')) {
            $imageData = $request->profilePicture;
            $imageParts = explode(";base64,", $imageData);
            $imageBase64 = base64_decode($imageParts[1]);
            $fileName = uniqid() . '.jpeg';
            $filePath = 'profile_pictures/' . $fileName;
            Storage::disk('public')->put($filePath, $imageBase64);
            $profilePicturePath = $filePath;
        }

        $accessToken = $this->accessToken;

        $response = Http::withHeaders(['AccessToken' => $accessToken])
            ->timeout(120)
            ->post('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/client/individual', [
                'firstname' => $request->firstName,
                'lastname' => $request->lastName,
                'dob' => $request->dob,
                'phone' => $request->phone,
                'bvn' => $request->bvn,
            ]);

        $this->logApiCall('/client/individual', 'POST', $request->all(), $response->json());

        if ($response) {
            $accountData = $response->json();
            $account = new Account();
            $account->user_id = $request->userId;
            $account->account_number = $accountData['data']['accountNo'];
            $account->account_type = 'individual';
            $account->status = 'PND';
            $account->lastName=$request->lastName;
            $account->firstName=$request->firstName;
            $account->phone=$request->phone;
            $account->bvn = $request->bvn;
            $account->profile_picture = $profilePicturePath; // Save the image path
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
    public function releaseAccount($accountNo)
    {
        $response = Http::withHeaders(['AccessToken' => $this->accessToken])
            ->post('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/client/release', [
                'accountNo' => $accountNo,
            ]);

        // Log the API call
        $this->logApiCall('/client/release', 'POST', ['accountNo' => $accountNo], $response->json());

        // Check if the response is successful
        if ($response->successful()) {
            // Log the success response
            Log::info('Account released successfully:', $response->json());

            // Trigger the Pusher event here
            // You can replace 'userId' with the actual user ID for notification
            // event(new AccountReleased($userId, 'Your account has been released.'));

            return response()->json(['message' => 'Account released successfully'], 200);
        } else {
            // Log the error response
            Log::error('API Error Response:', $response->json());

            return response()->json(['error' => $response->json()['message']], $response->status());
        }
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
            $releaseResponse = $this->releaseAccount($account->account_number);
            Log::info('Account Released Response:', $releaseResponse->json() ?? []);
            return response()->json(['message' => 'Webhook received and processed successfully'], 200);
        } else {
            Log::error('No account found for BVN: ' . ($validatedData['data']['bvn'] ?? 'N/A'));
            return response()->json(['message' => 'Account not found'], 404);
        }
    }
}
