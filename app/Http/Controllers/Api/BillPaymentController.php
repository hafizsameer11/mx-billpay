<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BillerCategory;
use App\Models\BillerItem;
use App\Models\BillPayment;
use App\Models\BillProviders;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Container\RewindableGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BillPaymentController extends Controller
{
    //
    protected $accessToken;
    protected $baseUrl;
    public function __construct()
    {
        $this->accessToken = config('access_token.test_token');
        $this->baseUrl ='https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/billspaymentstore';
        // $this->accessToken = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI4MTUiLCJ0b2tlbklkIjoiZGE1YjM5ZDItMGE2MS00MGE5LTg2ZGYtNTFjNDE5NmU4MmMyIiwiaWF0IjoxNzMxOTIyNjMyLCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.D8lFZCna6PZNIXnmJt-Xwc2JJ9rYxNPv4x5yDwRnldGs6tZu8KAlCoXumVIcXuUrOvcEud0hSIkQ7hZUjsFh7Q';
    }
    public function fetchBillerCategories()
{
    // Fetch categories ordered by `order_id` in ascending order
    $categories = BillerCategory::orderBy('order_id', 'asc')->get();

    // Map the categories with the required fields
    $categories = $categories->map(function ($category) {
        return [
            'id' => $category->id,
            'category' => $category->category,
            'categoryTitle' => $category->category_title,
            'categoryDescription' => $category->category_description,
            'isCategory' => $category->isCategory,
            'icon' => asset($category->logo),
            'selectTitle' => $category->select_title,
            'iconColor' => $category->backgroundColor
        ];
    });

    // Return the response
    return response()->json([
        'message' => 'Categories fetched successfully',
        'data' => $categories
    ]);
}

    public function fetchBillerItems($categoryId, $providerId)
    {

        // $categoryId = $categoryId;

        $categories = BillerCategory::where('id', $categoryId)->first();
        $categories = [
            'id' => $categories->id,
            'category' => $categories->category,
            'icon' => asset($categories->logo),
            'iconColor' => $categories->backgroundColor
        ];
        $provider = BillProviders::where('id', $providerId)->first();
        // if()
        $items = BillerItem::where('category_id', $categoryId)->where('provider_name', $provider->title)->get();
        $provider = [
            'id' => $provider->id,
            'title' => $provider->title,
        ];
        $items = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'paymentitemname' => $item->paymentitemname,

                'percentageComission' => $item->percentage_commission
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
                'itemList' => $items,
                'provider' => $provider
            ],
        ], 200);
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
        // if()
        $category = BillerCategory::where('id', $billerItem->category_id)->first();
        if($category->category=='Airtime'){
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully validated customer',
                'data' => [],
            ], 200);
        }else{ $response = Http::withHeaders([
            'AccessToken' => $this->accessToken,  // Replace with actual token
        ])->get('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/billspaymentstore/customervalidate', [
            'divisionId' => $divisionId,
            'paymentItem' => $paymentItem,
            'customerId' => $customerId,
            'billerId' => $billerId,
        ]);
        if ($response->successful()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully validated customer',
                'data' => $response->json('data'),
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => $response->json('message') . ' Customer does not Exist', // Error message from the API
                'data' => $response->json('data'),
            ], 400);
        }

        }

    }

    public function payBills(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customerId'   => 'required|string',
            'amount'       => 'required|numeric',
            'billerItemId' => 'required',
            'phoneNumber' => 'nullable',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], 400);
        }
        $userId = Auth::user()->id;
        $wallet = Wallet::where('user_id', $userId)->orderBy('id', 'desc')->first();
        if ($wallet->accountBalance < $request->amount) {
            return response()->json([
                'status' => 'success',
                'message' => 'Insufficient balance',
                'data' => [],

            ], 400);
        }
        $customerId = $request->customerId;
        $billerItem = $request->billerItemId;
        $amount = $request->amount;
        $billerItem = BillerItem::where('id', $billerItem)->first();
        $billerId = $billerItem->billerId;
        $category=BillerCategory::where('id', $billerItem->category_id)->first();
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
        Log::info('Bill Payment Payload: ', $response->json());
        if ($response->successful() && $response->json('status') == '00') {
            $transaction = new Transaction();
            $transaction->user_id = $userId;
            $transaction->transaction_type = "Bill Payment";
            $transaction->status = 'completed';
            $transaction->sign = 'negative';
            $transaction->amount = $amount;
            $notification = new Notification();
            $notification->title = "Bill Payment Successful";
            $notification->type = "billPayment";
            $notification->message = "Bill payment of " . $amount . " has been successful";
            $notification->user_id = $userId;
            $notification->icon = asset('notificationLogos/bill.png');
            $notification->iconColor = config('notification_colors.colors.Bill');
            $notification->save();
            $transaction->save();
            BillPayment::create([
                'biller_item_id' => $request->billerItemId,
                'user_id' => $userId,
                'refference' => $reference,
                'status' => 'success',
                'transaction_id' => $transaction->id,
                'customerId' => $customerId,
                'phoneNumber' => $phoneNumber,
                'amount' => $amount,
                'response' => json_encode($response->json())
            ]);
            Log::info('Bill Payment Respo .;ppnse: ', $response->json());
            $wallet->accountBalance = $wallet->accountBalance - $amount;
            $wallet->totalBillPayment = $wallet->totalBillPayment + $amount;
            $wallet->save();
            $data=[
                'status'=>'success',
                'amount'=>floatval($amount),
                'item'=>$billerItem->billerId,
                'provider'=>$billerItem->provider_name,
                'category'=>$category->category,
                'transactionId'=>$reference,
                'transactionDate'=>now()->format('Y-m-d'),
            ];
            return response()->json([
                'status' => 'success',
                'refference' => $reference,
                'message' => 'Successful payment',
                'data' => $data,
            ], 200);
        } else {
            //log into a seperate file seperately for now

            Log::info('Bill Payment Response: ', $response->json());
            $transaction = new Transaction();
            $transaction->user_id = $userId;
            $transaction->transaction_type = "Bill Payment";
            $transaction->status = 'failed';
            $transaction->sign = 'negative';
            $transaction->amount = $amount;
            $transaction->save();
            BillPayment::create([
                'biller_item_id' => $request->billerItemId,
                'user_id' => $userId,
                'refference' => $reference,
                'status' => 'failed',
                'transaction_id' => $transaction->id,
                'customerId' => $customerId,
                'phoneNumber' => $phoneNumber,
                'amount' => $amount,
                'response' => json_encode($response->json())
            ]);
            $data=[
                'status'=>'success',
                'amount'=>floatval($amount),
                'item'=>$billerItem->billerId,
                'provider'=>$billerItem->provider_name,
                'category'=>$category->category,
                'transactionId'=>$reference,
                'transactionDate'=>now()->format('Y-m-d'),
            ];
            return response()->json([
                'status' => 'error',
                'message' => $response->json('message'),
                'data' => $data,
            ], 400);
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
