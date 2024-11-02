<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function getTransactions()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
            }

            $transactions = Transaction::where('user_id', $user->id)
                ->has('transfer')
                ->with('transfer')
                ->get()
                ->map(function ($transaction) {
                    return [
                        'transaction_id' => $transaction->id,
                        'amount' => $transaction->amount,
                        'user_id' => $transaction->user_id,
                        'transaction_type' => $transaction->transaction_type,
                        'transaction_date' => $transaction->created_at,
                        'sign' => $transaction->sign,
                        'status' => $transaction->status,
                        'from_account_number' => $transaction->transfer->from_account_number,
                        'to_account_number' => $transaction->transfer->to_account_number,
                        'from_client_id' => $transaction->transfer->from_client_id,
                        'to_client_id' => $transaction->transfer->to_client_id,
                        'to_client_name' => $transaction->transfer->to_client_name,
                        'from_client_name' => $transaction->transfer->from_client_name,
                        'response_message' => $transaction->transfer->response_message,
                        'type' => $transaction->transfer->transfer_type

                    ];
                });

            return response()->json(['status' => 'success', 'data' => $transactions], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to retrieve transactions', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch all bill payments made by the authenticated user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBillPayment()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
            }

            $billpayments = Transaction::where('user_id', $user->id)
                ->has('billpayment') // Only get transactions with non-null billpayments
                ->with([
                    'billpayment.billerItem.billerCategory' // Eager load billpayment, billerItem, and billerCategory
                ])
                ->get()
                ->map(function ($transaction) {
                    return [
                        'id' => $transaction->id,
                        'amount' => $transaction->amount,
                        'user_id' => $transaction->user_id,
                        'transaction_type' => $transaction->transaction_type,
                        'transaction_date' => $transaction->transaction_date,
                        'refference' => $transaction->billpayment->refference,
                        'customerId' => $transaction->billpayment->customerId,
                        'sign' => $transaction->sign,
                        'status' => $transaction->status,
                        'category' => $transaction->billpayment->billerItem->category->category,

                        'paymentitemname' => $transaction->billpayment->billerItem->paymentitemname,
                        'billerType' => $transaction->billpayment->billerItem->billerType,
                        'payDirectitemCode' => $transaction->billpayment->billerItem->payDirectitemCode,
                        'currencyCode' => $transaction->billpayment->billerItem->currencyCode,
                        'division' => $transaction->billpayment->billerItem->division,
                        'created_at' => $transaction->created_at,
                        'billerId' => $transaction->billpayment->billerItem->billerId,
                        'category_icon' => $transaction->billpayment->billerItem->category->logo,
                        'iconColor' => $transaction->billpayment->billerItem->category->backgroundColor
                        // 'billpayment' => [
                        //     'transaction_id' => $transaction->billpayment->id,
                        //     'user_id' => $transaction->billpayment->user_id,
                        //     'status' => $transaction->billpayment->status,


                        //     'billPaymenDate' => $transaction->billpayment->created_at,
                        //     'biller_item' => [
                        //         'id' => $transaction->billpayment->billerItem->id,

                        //         'paymentCode' => $transaction->billpayment->billerItem->paymentCode,
                        //         'productId' => $transaction->billpayment->billerItem->productId,
                        //         'paymentitemid' => $transaction->billpayment->billerItem->paymentitemid,
                        //         'currencySymbol' => $transaction->billpayment->billerItem->currencySymbol,
                        //         'isAmountFixed' => $transaction->billpayment->billerItem->isAmountFixed,
                        //         'itemFee' => $transaction->billpayment->billerItem->itemFee,
                        //         'itemCurrencySymbol' => $transaction->billpayment->billerItem->itemCurrencySymbol,
                        //         'pictureId' => $transaction->billpayment->billerItem->pictureId,

                        //         'fixed_commission' => $transaction->billpayment->billerItem->fixed_commission,
                        //         'percentage_commission' => $transaction->billpayment->billerItem->percentage_commission,
                        //         'created_at' => $transaction->billpayment->billerItem->created_at,
                        //         'updated_at' => $transaction->billpayment->billerItem->updated_at,

                        //     ],
                        // ],
                    ];
                });

            if ($billpayments->isEmpty()) {
                return response()->json(['status' => 'error', 'message' => 'No bill payments found'], 404);
            }

            return response()->json(['status' => 'success', 'data' => $billpayments], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to retrieve bill payments', 'error' => $e->getMessage()], 500);
        }
    }
}
