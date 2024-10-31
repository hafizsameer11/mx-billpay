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

            $transactions = Transaction::where('user_id', $user->id)->with(['user', 'transfer'])->get();

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
            $billpayments = Transaction::where('user_id', $user->id)->with(['user', 'billpayment'])->get();
            if (!$billpayments) {
                return response()->json(['status' => 'error', 'message' => 'No bill payments found'], 404);
            }
            return response()->json(['status' => 'success', 'data' => $billpayments], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to retrieve bill payments', 'error' => $e->getMessage()], 500);
        }
    }
}
