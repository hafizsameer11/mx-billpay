<?php

namespace App\Http\Controllers;

use App\Models\BillPayment;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    //
    public function getAnalyticsData(Request $request)
    {
        $userId = Auth::user()->id;

        // Validate the request to ensure the time period is valid
        $request->validate([
            'period' => 'required|string|in:month,quarter,year',
        ]);

        $period = $request->input('period');

        // Initialize totals
        $incomeTotal = 0;
        $expenseTotal = 0;
        $data = []; // To hold the formatted data

        // Get the start and end dates based on the selected period
        $now = now();
        switch ($period) {
            case 'month':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
            case 'quarter':
                $startDate = $now->copy()->startOfQuarter();
                $endDate = $now->copy()->endOfQuarter();
                break;
            case 'year':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                break;
            default:
                return response()->json(['status' => 'error', 'message' => 'Invalid period selected.'], 400);
        }

        // Fetch bill payments and filter by date range
        $billPayments = BillPayment::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Fetch transfers and filter by date range
        $transfers = Transfer::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Process bill payments and format data
        foreach ($billPayments as $payment) {
            $expenseTotal += $payment->amount; // Assuming bill payments are expenses
            $data[] = [
                'date' => $payment->created_at->format('Y-m-d'),
                'amount' => $payment->amount,
                'type' => 'Expense',
                'description' => 'Bill Payment: ' . $payment->description, // Customize as necessary
                'reference' => $payment->reference,
            ];
        }

        // Process transfers and format data
        foreach ($transfers as $transfer) {
            if ($transfer->sign === 'positive') {
                $incomeTotal += $transfer->amount; // Income from transfers
                $data[] = [
                    'date' => $transfer->created_at->format('Y-m-d'),
                    'amount' => $transfer->amount,
                    'type' => 'Income',
                    'description' => 'Transfer from: ' . $transfer->from_client_name,
                    'reference' => $transfer->reference,
                ];
            } else {
                $expenseTotal += $transfer->amount; // Expenses from transfers
                $data[] = [
                    'date' => $transfer->created_at->format('Y-m-d'),
                    'amount' => $transfer->amount,
                    'type' => 'Expense',
                    'description' => 'Transfer to: ' . $transfer->to_client_name,
                    'reference' => $transfer->reference,
                ];
            }
        }

        // Prepare the response data
        return response()->json([
            'status' => 'success',
            'message' => 'Analytics data retrieved successfully.',
            'data' => [
                'income' => $incomeTotal,
                'expense' => $expenseTotal,
                'transactions' => $data, // All transactions in the required format
            ],
        ], 200);
    }
}
