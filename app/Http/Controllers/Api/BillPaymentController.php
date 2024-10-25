<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BillerCategory;
use App\Models\BillerItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class BillPaymentController extends Controller
{
    //
    protected $accessToken;
    public function __construct()
    {
        $this->accessToken = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI4MTUiLCJ0b2tlbklkIjoiNjJmOTFlYTItMTQ4NC00MTY1LTg0N2MtN2QxZmI1NzZlYmI3IiwiaWF0IjoxNzI5NTExNzI4LCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.pof77yeMdkhZyO8fQtaFmwT-3bq8fawbkcduxteAfGzP0U9HzaI-vdnrUok90oPvQ_PscdPD1vUPP4Ya5byITA';
    }
    public function fetchBillerCategories()
    {
        $categories = BillerCategory::all();
        return response()->json([
            'message' => 'Categories fetched successfully',
            'data' => $categories
        ]);
    }
    public function fetchBillerItems(Request $request)
    {
        // Fetching query parameters
        $categoryId = $request->query('category_id');
        $division = $request->query('division');
        $productId = $request->query('productId');

        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'division' => 'required',
            'productId' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'data' => [],
            ], 400);
        }
        // Fetching the items based on the parameters
        $items = BillerItem::where('category_id', $categoryId)
            ->where('division', $division)
            ->where('productId', $productId)
            ->get();

        // Check if any items were found
        if ($items->isEmpty()) {
            return response()->json([
                'message' => 'No items found for the provided criteria',
                'data' => [],
            ], 404); // 404 Not Found
        }

        // Success response with found items
        return response()->json([
            'message' => 'Items fetched successfully',
            'data' => $items,
        ], 200); // 200 OK
    }
    public function validateCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customerId' => 'required|string',
            'id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'data' => [],
            ], 400);
        }

        $customerId = $request->input('customerId');
        $id = $request->input('id');

        $billerItem = BillerItem::where('id', $id)->first();
        $divisionId = $billerItem->division;
        $paymentItem = $billerItem->paymentCode;

        // Make GET request to validate customer using Http::get() and passing query parameters
        $response = Http::withHeaders([
            'AccessToken' => $this->accessToken,  // Replace with actual token
        ])->get('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/billspaymentstore/customervalidate', [
            'divisionId' => $divisionId,
            'paymentItem' => $paymentItem,
            'customerId' => $customerId,
            // 'billerId' => $billerId,
        ]);

        // Check if the API request was successful
        if ($response->successful()) {
            return response()->json([
                'message' => 'Successfully validated customer',
                'data' => $response->json('data'),
            ], 200);
        } else {
            // If the API request failed, return the error message and data
            return response()->json([
                'message' => $response->json('message'), // Error message from the API
                'data' => $response->json('data'),
            ], $response->status());
        }
    }
    //

    public function payBills(Request $request)
    {
        // Validate the request to ensure required parameters are present
        $validator = Validator::make($request->all(), [
            'customerId'   => 'required|string',
            'amount'       => 'required|numeric',
            'division'     => 'required|string',
            'paymentItem'  => 'required|string',
            'productId'    => 'required|string',
            'billerId'     => 'required|string',
            // 'reference'    => 'required|string',
            'phoneNumber'  => 'nullable|string',
        ]);

        // If validation fails, return an error response
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], 400);
        }

        // Collect required parameters
        $customerId = $request->input('customerId');
        $amount = $request->input('amount');
        $division = $request->input('division');
        $paymentItem = $request->input('paymentItem');
        $productId = $request->input('productId');
        $billerId = $request->input('billerId');
        $phoneNumber = $request->input('phoneNumber', null); // Optional

        $reference = 'mxPay-' . mt_rand(1000, 9999);

        // Prepare request payload
        $payload = [
            'customerId'   => $customerId,
            'amount'       => $amount,
            'division'     => $division,
            'paymentItem'  => $paymentItem,
            'productId'    => $productId,
            'billerId'     => $billerId,
            'reference'    => $reference,
            'phoneNumber'  => $phoneNumber,  // Optional
        ];

        // Make POST request to pay the bill
        $response = Http::withHeaders([
            'AccessToken' => $this->accessToken,  // Replace with actual token
        ])->post('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/billspaymentstore/pay', $payload);

        // Check if the API request was successful
        if ($response->successful()) {
            return response()->json([
                'message' => 'Successful payment',
                'data' => $response->json('data'),
            ], 200);
        } else {
            // If the API request failed, return the error message and data
            return response()->json([
                'message' => $response->json('message'), // Error message from the API
                'data' => $response->json('data'),
            ], $response->status());
        }
    }

    public function transactionStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transactionId' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'data' => [],
            ], 400);
        }

        $transactionId = $request->input('transactionId');


        // Make GET request to validate customer using Http::get() and passing query parameters
        $response = Http::withHeaders([
            'AccessToken' => $this->accessToken,  // Replace with actual token
        ])->get('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/billspaymentstore/transactionStatus', [
            'transactionId' => $transactionId,
        ]);

        // Check if the API request was successful
        if ($response->successful()) {
            return response()->json([
                'message' => 'Successful Transaction Retrieval',
                'data' => $response->json('data'),
            ], 200);
        } else {
            // If the API request failed, return the error message and data
            return response()->json([
                'message' => $response->json('message'), // Error message from the API
                'data' => $response->json('data'),
            ], $response->status());
        }
    }
}
