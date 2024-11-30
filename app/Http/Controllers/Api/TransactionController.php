<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BillPayment;
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
                ->with([
                    'billpayment.billerItem.category', // For bill payment
                    'transfer'
                ])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($transaction) {
                    if ($transaction->billpayment) {
                        return [
                            'amount' => $transaction->amount,
                            'type' => 'Bill Payment',
                            'category' => $transaction->billpayment->billerItem->category->category,
                            'item' => $transaction->billpayment->billerItem->paymentitemname,
                        ];
                    }

                    if ($transaction->transfer) {
                        return [
                            'amount' => $transaction->amount,
                            'type' => 'Fund Transfer',
                            'category' => 'Fund',
                            'item' => 'Incoming Fund',
                        ];
                    }
                    return null; // Skip transactions that are neither bill payments nor transfers
                })
                ->filter(); // Remove null entries

            if ($transactions->isEmpty()) {
                return response()->json(['status' => 'error', 'message' => 'No transactions found'], 404);
            }

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
                    'billpayment.billerItem.category' // Eager load billpayment, billerItem, and billerCategory
                ])->orderBy('created_at', 'desc')
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
                        'category_icon' => asset($transaction->billpayment->billerItem->category->logo),
                        'iconColor' => $transaction->billpayment->billerItem->category->backgroundColor
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
    public function transactionDetails($id)
    {

        $transaction = Transaction::where('id', $id)->has('transfer')->with('transfer')->first();

        if (!$transaction) {
            return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
        } else {
            $response = [
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'transaction_date' => $transaction->created_at,
                'status' => $transaction->status,
                'accountNumber' => $transaction->transfer->from_account_number,
                'toAccountNumber' => $transaction->transfer->to_account_number,
                'response_message' => $transaction->transfer->response_message,
                'type' => $transaction->transfer->transfer_type,
                'refference' => $transaction->transfer->reference

            ];

            return response()->json(['status' => 'success', 'data' => $response], 200);
        }
    }
    public function billPaymentDetails($id)
    {
        $transaction = Transaction::where('id', $id)->has('billpayment') // Only get transactions with non-null billpayments
            ->with([
                'billpayment.billerItem.category'
            ])->first();
        if (!$transaction) {
            return response()->json(['status' => 'error', 'message' => 'Bill payment not
                found'], 404);
        } else {
            $response = [
                'id' => $transaction->id,
                'amount' => $transaction->amount,
                'transaction_date' => $transaction->transaction_date,
                'refference' => $transaction->billpayment->refference,
                'customerId' => $transaction->billpayment->customerId,
                'status' => $transaction->status,
                'category' => $transaction->billpayment->billerItem->category->category,
                'paymentitemname' => $transaction->billpayment->billerItem->paymentitemname,
                'billerType' => $transaction->billpayment->billerItem->billerType,
            ];
            return response()->json(
                ['status' => 'success', 'data' => $response],
                200
            );
        }
    }
}
