<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\CheckTransactionStatus;
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
use App\Services\NotificationService;

class BillPaymentController extends Controller
{
    //
    protected $accessToken;
    protected $baseUrl;
    protected $NotificationService;
    public function __construct(NotificationService $NotificationService)
    {
        $this->NotificationService = $NotificationService;
        $this->accessToken = config('access_token.live_token');
        $this->baseUrl = 'https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/billspaymentstore';
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
        // Fetch the category and provider
        $categories = BillerCategory::find($categoryId);
        $provider = BillProviders::find($providerId);

        // Check if the category or provider is null
        if (!$categories || !$provider) {
            return response()->json([
                'message' => 'Category or Provider not found',
                'data' => [],
            ], 404); // 404 Not Found
        }

        // Make the API request
        $response = Http::withHeaders(['AccessToken' => $this->accessToken])
            ->get('https://api-apps.vfdbank.systems/vtech-wallet/api/v1/billspaymentstore/billerItems', [
                'billerId' => $provider->billerId,
                'divisionId' => $provider->division,
                'productId' => $provider->product,
            ]);

        // Initialize items array
        $items = [];

        if ($response->successful()) {
            // Process the items from the API response
            $items = collect($response->json()['data']['paymentitems'])->map(function ($item, $index) use ($categories, $provider) {
                return [
                    'id' => $index + 1, // Use the loop index as the ID, starting from 1
                    'paymentitemname' => $item['paymentitemname'] ?? '',
                    'amount' => $item['amount'] ?? 0,
                    'percentageComission' => $categories->percentage_commission,
                    'fixedComission' => $categories->fixed_commission,
                    'category_id' => $categories->id,
                    // 'productId'=>$oo
                    'paymentCode' => $item['paymentCode'] ?? '',
                    'divisionId' => $item['division'] ?? '',
                    'productId' => $item['productId'] ?? '',
                    'billerId' => $provider->billerId ?? '',
                ];
            });
        }

        // Prepare the category and provider data for the response
        $categoriesData = [
            'id' => $categories->id,
            'category' => $categories->category,
            'icon' => asset($categories->logo),
            'iconColor' => $categories->backgroundColor,
        ];

        $providerData = [
            'id' => $provider->id,
            'title' => $provider->title,
        ];

        // If no items found
        if ($items->isEmpty()) {
            return response()->json([
                'message' => 'No items found for the provided criteria',
                'data' => [],
            ], 404); // 404 Not Found
        }

        // Successful response
        return response()->json([
            'message' => 'Items fetched successfully',
            'data' => [
                'category' => $categoriesData,
                'itemList' => $items,
                'provider' => $providerData,
            ],
        ], 200); // 200 OK
    }

    // public function fetchBillerItems($categoryId, $providerId)
    // {

    //     // $categoryId = $categoryId;

    //     $categories = BillerCategory::where('id', $categoryId)->first();
    //     $categories = [
    //         'id' => $categories->id,
    //         'category' => $categories->category,
    //         'icon' => asset($categories->logo),
    //         'iconColor' => $categories->backgroundColor
    //     ];
    //     $provider = BillProviders::where('id', $providerId)->first();
    //     // if()
    //     //if amount exists or not null than order by amount from lowert to higher amount will be of biller item


    //     $items = BillerItem::where('category_id', $categoryId)
    //         ->where('provider_name', $provider->title)
    //         ->orderByRaw('amount IS NULL, amount ASC') // NULLs at the end, then sort by amount
    //         ->get();
    //     $provider = [
    //         'id' => $provider->id,
    //         'title' => $provider->title,
    //     ];
    //     $items = $items->map(function ($item) {
    //         return [
    //             'id' => $item->id,
    //             'paymentitemname' => $item->paymentitemname,
    //             'amount' => $item->amount,
    //             'percentageComission' => $item->percentage_commission,
    //             'fixedComission' => $item->fixed_commission
    //         ];
    //     });
    //     if ($items->isEmpty()) {
    //         return response()->json([
    //             'message' => 'No items found for the provided criteria',
    //             'data' => [],
    //         ], 404); // 404 Not Found
    //     }
    //     return response()->json([
    //         'message' => 'Items fetched successfully',

    //         'data' => [
    //             'category' => $categories,
    //             'itemList' => $items,
    //             'provider' => $provider
    //         ],
    //     ], 200);
    // }


    public function validateCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customerId' => 'required|string',
            'id' => 'required|string',
            'divisionId' => 'required|string',
            'paymentItem' => 'required|string',
            'billerId' => 'required|string',
            'category_id' => 'required|string',
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
        $divisionId = $request->divisionId;
        $paymentItem = $request->paymentItem;
        $billerId = $request->billerId;

        Log::info('Validating customer for Biller ID: ' . $billerId, [
            'divisionId' => $divisionId,
            'paymentItem' => $paymentItem,
            'customerId' => $customerId,
            'billerId' => $billerId,
        ]);
        $category = BillerCategory::where('id', $request->category_id)->first();
        if ($category->category == 'Airtime' || $category->category == 'Data') {
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully validated customer',
                'data' => [],
            ], 200);
        } else {
            $response = Http::withHeaders([
                'AccessToken' => $this->accessToken,
            ])->get('https://api-apps.vfdbank.systems/vtech-wallet/api/v1/billspaymentstore/customervalidate', [
                'divisionId' => $divisionId,
                'paymentItem' => $paymentItem,
                'customerId' => $customerId,
                'billerId' => $billerId,
            ]);
            if ($response->successful()) {
                Log::info('Response from Validation Biller ID: ' . $billerId, ['response' => $response->json()]);

                // Safely extract relevant data
                $responseData = $response->json()['data']['responseData'] ??
                    $response->json()['data']['data'] ?? [];

                // Extract customer name from all possible structures
                $customerData = $responseData['customer'] ?? $responseData['user'] ?? [];
                $customerName = $customerData['customerName'] ?? $customerData['name'] ?? null;

                if ($customerName) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Successfully validated customer',
                        'data' => $response->json('data'),
                        'customerName' => $customerName,
                    ], 200);
                } else {
                    Log::warning('Validation failed for Biller ID: ' . $billerId, ['response' => $response->json()]);

                    return response()->json([
                        'status' => 'error',
                        'message' => $response->json()['data']['message'] ?? 'Customer does not exist',
                        'data' => $response->json('data'),
                    ], 400);
                }
            } else {
                Log::error('Validation failed for Biller ID: ' . $billerId, ['response' => $response->json()]);

                return response()->json([
                    'status' => 'error',
                    'message' => $response->json('message') ?? 'Customer does not exist',
                    'data' => $response->json('data'),
                ], 400);
            }
        }
    }

    public function payBills(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customerId'   => 'nullable|string',
            'amount'       => 'required|numeric',
            'billerItemId' => 'required',
            'paymentitemname' => 'required',
            'phoneNumber' => 'nullable',
            'division' => 'required',
            'paymentCode' => 'required',
            'productId' => 'required',
            'billerId' => 'required',
            'category_id' => 'nullable'

        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], 400);
        }
        $userId = Auth::user()->id;
        $wallet = Wallet::where('user_id', $userId)->orderBy('id', 'desc')->first();
        $customerId = $request->customerId;
        $billerItem = $request->billerItemId;
        $amount = $request->amount;
        $billerId = $request->billerId;


        $billerItem = BillerCategory::where('id', $request->category_id)->first();
        $fixedCommission = (float) $billerItem->fixed_commission; // Convert to float
        $percentageCommission = (float) $billerItem->percentage_commission; // Convert to float
        $totalAmount = $amount + $fixedCommission + ($amount * $percentageCommission / 100);
        if ($wallet->accountBalance < $totalAmount) {
            Log::info('Wallet in suffiecinet', ['wallet' => $wallet->accountBalance, 'amount' => $request->amount]);
            return response()->json([
                'status' => 'success',
                'message' => 'Insufficient balance',
                'data' => [
                    'error' => 'insufficient_error'
                ],
            ], 400);
        }
        Log::info('Pay bill payload: ', [$request->all()]);



        $category = BillerCategory::where('id', $request->category_id)->first();
        $paymentItem = $request->paymentCode;
        $productId = $request->productId;
        $division = $request->division;
        if ($category->category == 'Airtime' || $category->category == 'Data') {
            $customerId = $request->phoneNumber;
        } else {
            $customerId = $request->customerId;
        }
        $phoneNumber = $request->input('phoneNumber', null);
        $reference = 'mxPay-' . mt_rand(1000, 9999);


        $payload = [
            'customerId'   => $customerId,
            'amount'       => $amount,
            'division'     => $division,
            'paymentItem'  => $paymentItem,
            'productId'    => $productId,
            'billerId'     => $billerId,
            'reference'    => $reference,
            'phoneNumber'  => $phoneNumber,
        ];
        Log::info('Bill Payment Payload: ', [$payload]);
        $response = Http::withHeaders([
            'AccessToken' => $this->accessToken,  // Replace with actual token
        ])->post('https://api-apps.vfdbank.systems/vtech-wallet/api/v1/billspaymentstore/pay', $payload);
        Log::info('Bill Payment response: ', $response->json());
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
            $notification->message = "Bill payment of " . $amount . "NGN has been successful";
            $notification->user_id = $userId;
            $notification->icon = asset('notificationLogos/bill.png');
            $notification->iconColor = config('notification_colors.colors.Bill');
            $notification->save();
            $notificationTitle = "Bill Payment Successful";
            $notificationMessage = "Bill payment of " . $amount . " has been successful";
            $notificationResponse = $this->NotificationService->sendToUserById($userId, $notificationTitle, $notificationMessage);
            Log::info('Notification Response: ', $notificationResponse);
            $transaction->save();
            $token = '';
            if (isset($response->json()['data']['token'])) {
                $token = $response->json()['data']['token'];
            }

            BillPayment::create([
                'category_id' => $request->category_id,
                'billItemName' => $request->paymentitemname,
                'providerName' => $request->billerId,
                'biller_item_id' => 3,
                'user_id' => $userId,
                'refference' => $reference,
                'status' => 'success',
                'transaction_id' => $transaction->id,
                'customerId' => $customerId,
                'phoneNumber' => $phoneNumber,
                'amount' => $amount,
                'response' => json_encode($response->json()),
                'token' => $token,
                'totalAmount' => $totalAmount
            ]);

            Log::info('Bill Payment Response: ', $response->json());
            $wallet->accountBalance = $wallet->accountBalance - $totalAmount;
            $wallet->totalBillPayment = $wallet->totalBillPayment + $totalAmount;
            $wallet->save();
            $data = [
                'status' => 'success',
                'amount' => floatval($amount),
                'item' => $request->paymentitemname,
                'provider' => $request->billerId,
                'category' => $category->category,
                'transactionId' => $reference,
                'transactionDate' => now()->format('Y-m-d'),
                'token' => $token,
                'totalAmount' => $totalAmount
            ];
            return response()->json([
                'status' => 'success',
                'refference' => $reference,
                'message' => 'Successful payment',
                'data' => $data,
            ], 200);
        } else {
            //log into a seperate file seperately for now
            if ($response->json()['status'] == '09' && $response->json()['message'] == 'Transaction pending') {
                $transactionStatus = 'pending';
                $wallet->accountBalance = $wallet->accountBalance - $totalAmount;
                $wallet->totalBillPayment = $wallet->totalBillPayment + $totalAmount;
                $wallet->save();
                Log::info('Bill Payment Response: ', $response->json());
                $transaction = new Transaction();
                $transaction->user_id = $userId;
                $transaction->transaction_type = "Bill Payment";
                $transaction->status = $transactionStatus;
                $transaction->sign = 'negative';
                $transaction->amount = $amount;
                $transaction->save();
                BillPayment::create([
                    'category_id' => $request->category_id,
                    'providerName' => $request->billerId,
                    'billItemName' => $request->paymentitemname,
                    'biller_item_id' => $request->billerItemId,
                    'user_id' => $userId,
                    'refference' => $reference,
                    'status' => 'pending',
                    'transaction_id' => $transaction->id,
                    'customerId' => $customerId,
                    'phoneNumber' => $phoneNumber,
                    'amount' => $amount,
                    'response' => json_encode($response->json()),
                    'totalAmount' => $totalAmount
                ]);
                $data = [
                    'status' => 'pending',
                    'amount' => floatval(value: $amount),
                    'item' => $request->paymentitemname,
                    'provider' => $request->billerId,
                    'category' => $category->category,
                    'transactionId' => $reference,
                    'transactionDate' => now()->format('Y-m-d'),
                    'totalAmount' => $totalAmount
                ];
                return response()->json([
                    'status' => 'success',
                    'message' => $response->json('message'),
                    'data' => $data,
                ], 200);
            } else if ($response->json()['status'] == '99' && $response->json()['message'] == 'Not in the recent documentation') {
                $transactionStatus = 'pending';
                $wallet->accountBalance = $wallet->accountBalance - $totalAmount;
                $wallet->totalBillPayment = $wallet->totalBillPayment + $totalAmount;
                $wallet->save();
                Log::info('Bill Payment Response: ', $response->json());
                $transaction = new Transaction();
                $transaction->user_id = $userId;
                $transaction->transaction_type = "Bill Payment";
                $transaction->status = $transactionStatus;
                $transaction->sign = 'negative';
                $transaction->amount = $amount;
                $transaction->save();
                BillPayment::create([
                    'category_id' => $request->category_id,
                    'providerName' => $request->billerId,
                    'billItemName' => $request->paymentitemname,
                    'biller_item_id' => $request->billerItemId,
                    'user_id' => $userId,
                    'refference' => $reference,
                    'status' => 'pending',
                    'transaction_id' => $transaction->id,
                    'customerId' => $customerId,
                    'phoneNumber' => $phoneNumber,
                    'amount' => $amount,
                    'response' => json_encode($response->json()),
                    'totalAmount' => $totalAmount
                ]);
                $data = [
                    'status' => 'pending',
                    'amount' => floatval($amount),
                    'item' => $request->paymentitemname,
                    'provider' => $request->billerId,
                    'category' => $category->category,
                    'transactionId' => $reference,
                    'transactionDate' => now()->format('Y-m-d'),
                    'totalAmount' => $totalAmount
                ];
                return response()->json([
                    'status' => 'success',
                    'message' => $response->json('message'),
                    'data' => $data,
                ], 200);
            } else {
                $transactionStatus = 'failed';
            }
            Log::info('Bill Payment Response: ', $response->json());
            $transaction = new Transaction();
            $transaction->user_id = $userId;
            $transaction->transaction_type = "Bill Payment";
            $transaction->status = $transactionStatus;
            $transaction->sign = 'negative';
            $transaction->amount = $amount;
            $transaction->save();
            BillPayment::create([
                'category_id' => $request->category_id,
                'providerName' => $request->billerId,
                'billItemName' => $request->paymentitemname,
                'biller_item_id' => $request->billerItemId,
                'user_id' => $userId,
                'refference' => $reference,
                'status' => 'failed',
                'transaction_id' => $transaction->id,
                'customerId' => $customerId,
                'phoneNumber' => $phoneNumber,
                'amount' => $amount,
                'response' => json_encode($response->json()),
                'totalAmount' => $totalAmount
            ]);
            $data = [
                'status' => 'failed',

                'amount' => floatval($amount),
                'item' => $request->paymentitemname,
                'provider' => $request->billerId,
                'category' => $category->category,
                'transactionId' => $reference,
                'transactionDate' => now()->format('Y-m-d'),
                'totalAmount' => $totalAmount
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
    public function getBillerList($id)
    {
        $category = BillerCategory::where('id', $id)->first();
        $categoryname = $category->originalName;
    }

    public function verifyTransactionStatus($id)
    {
        $refferenceId = $id;
        //verify transaction exists
        $transaction = BillPayment::where('refference', $refferenceId)->first();
        if ($transaction) {
            $response = Http::withHeaders(['AccessToken' => $this->accessToken])
                ->get('https://api-apps.vfdbank.systems/vtech-wallet/api/v1/billspaymentstore/transactionStatus', [
                    'transactionId' => $refferenceId
                ]);
            if ($response->successful()) {
                Log::info('Transaction Status: ', $response->json());
                return response()->json([
                    'status' => 'success',
                    'data' => $response->json('data'),
                ], 200);
            } else {
                Log::info('Transaction Status: ', $response->json());
                return response()->json([
                    'status' => 'error',
                    'data' => $response->json('data'),
                ], 200);
            }
        }
    }

    public function testingTransactionStatus($id, $tid)
    {
        CheckTransactionStatus::dispatch($id, $tid, 0);
    }
}
