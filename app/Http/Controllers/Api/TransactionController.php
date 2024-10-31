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
                        'status' => $transaction->transfer->status,
                        'to_client_name' => $transaction->transfer->to_client_name,
                        'from_client_name' => $transaction->transfer->from_client_name,
                        'amount' => $transaction->transfer->amount,
                        'response_message' => $transaction->transfer->response_message,

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
                ->with(['billpayment.billerItem']) // Eager load billpayment and their related billerItem
                ->get();
            if (!$billpayments) {
                return response()->json(['status' => 'error', 'message' => 'No bill payments found'], 404);
            }
            return response()->json(['status' => 'success', 'data' => $billpayments], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to retrieve bill payments', 'error' => $e->getMessage()], 500);
        }
    }
}
