<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BillerCategory;
use App\Models\BillerItem;
use App\Models\BillPayment;
use Illuminate\Container\RewindableGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class BillPaymentController extends Controller
{
    //
    protected $accessToken;
    public function __construct()
    {
        $this->accessToken = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI4MTUiLCJ0b2tlbklkIjoiYzVmOTA4OWMtODAyMS00ZWU3LThjNjYtNTMzMjEwZjQ0NjNkIiwiaWF0IjoxNzI5OTMyMzU2LCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.uIQKrplFvnc2ta7RMpwurkoK7guwIbYMBS00NopUxGwUlpP7TC1AqhM1_hns2NEQSw6scWABoeD2PLWpBkgPsA';
    }
    public function fetchBillerCategories()
    {
        $categories = BillerCategory::all();
        return response()->json([
            'message' => 'Categories fetched successfully',
            'data' => $categories
        ]);
    }
    public function fetchBillerItems($id)
    {
        // Fetching query parameters
        $categoryId = $id;
        $categories=BillerCategory::where('id',$categoryId)->first();
$categories=$categories->map(function($category){
return [
'id'=>$category->id,
'category'=>$category->category
];
});

        $items = BillerItem::where('category_id', $id)->get();

        $items = $items->map(function ($item) {
            return [

                'id' => $item->id,
                'paymentitemname' => $item->paymentitemname

            ];
        });
        if ($items->isEmpty()) {
            return response()->json([
                'message' => 'No items found for the provided criteria',
                'data' => [],
            ], 404); // 404 Not Found
        }
        return response()->json([
            'message' => 'Items fetched successfully',

            'data' => [
                'category' => $categories,
                'itemList'=>$items
            ],
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
        $id = $request->id;

        $billerItem = BillerItem::where('id', $id)->first();
        $divisionId = $billerItem->division;
        $paymentItem = $billerItem->paymentCode;
        $billerId = $billerItem->billerId;

        $response = Http::withHeaders([
            'AccessToken' => $this->accessToken,  // Replace with actual token
        ])->get('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/billspaymentstore/customervalidate', [
            'divisionId' => $divisionId,
            'paymentItem' => $paymentItem,
            'customerId' => $customerId,
            'billerId' => $billerId,
        ]);

        // Check if the API request was successful
        if ($response->successful()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully validated customer',
                'data' => $response->json('data'),
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => $response->json('message'), // Error message from the API
                'data' => $response->json('data'),
            ], $response->status());
        }
    }
    public function payBills(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customerId'   => 'required|string',
            'amount'       => 'required|numeric',
            'billerItemId' => 'required',
            'phoneNumber' => 'nullable',
            'userId' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], 400);
        }
        $customerId = $request->customerId;
        $billerItem = $request->billerItemId;
        $amount = $request->amount;
        $billerItem = BillerItem::where('id', $billerItem)->first();
        $billerId = $billerItem->billerId;
        $paymentItem = $billerItem->paymentCode;
        $productId = $billerItem->productId;
        $division = $billerItem->division;
        $phoneNumber = $request->input('phoneNumber', null); // Optional
        $reference = 'mxPay-' . mt_rand(1000, 9999);
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
        $response = Http::withHeaders([
            'AccessToken' => $this->accessToken,  // Replace with actual token
        ])->post('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/billspaymentstore/pay', $payload);
        if ($response->successful()) {
            BillPayment::create([

                'biller_item_id' => $request->billerItemId,
                'user_id' => $request->userId,
                'refference' => $reference,
                'status' => 'success',
                'customerId' => $customerId,
                'phoneNumber' => $phoneNumber,

            ]);
            return response()->json([
                'status' => 'success',
                'refference' => $reference,
                'message' => 'Successful payment',
                'data' => $response->json('data'),
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
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
    public function fetchbillerItemDetails($id)
    {

        // $validator = Validator::make($request->all(), [
        //     'billerItemId' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'message' => $validator->errors(),
        //         'data' => [],
        //     ], 400);
        // }
        $billerItemId = $id;
        $billerItem = BillerItem::where('id', $billerItemId)->first();
        if (!$billerItem) {
            return response()->json([
                'message' => 'No items found for the provided criteria',
                'data' => [],
            ], 404); // 404 Not Found
        }
        return response()->json([
            'message' => 'Items fetched successfully',
            'data' => $billerItem,
        ], 200); // 200 OK

    }
}
