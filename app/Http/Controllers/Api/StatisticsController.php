<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BillPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatisticsController extends Controller
{
    public function ytyStats()
    {
        $userId = Auth::user()->id;

        // Fetch Year-To-Date (YTD) data
        $startOfYear = Carbon::now()->startOfYear();
        $today = Carbon::now();

        $billPayments = BillPayment::where('user_id', $userId)
            ->whereBetween('created_at', [$startOfYear, $today])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Format the data
        //check if amount is zero or null

        $data = $billPayments->map(function ($payment) {
            return [
                'name' => $payment->created_at->format('d'), // Day of the month
                'expense' =>floatval($payment->amount)??0,
            ];
        });

        return response()->json(['status' => 'success', 'data' => $data], 200);
    }

    public function quarterlyStats()
    {
        $userId = Auth::user()->id;

        // Get the start and end dates of the current quarter
        $startOfQuarter = Carbon::now()->startOfQuarter();
        $endOfQuarter = Carbon::now()->endOfQuarter();

        // Fetch Quarterly data grouped by month
        $billPayments = BillPayment::where('user_id', $userId)
            ->whereBetween('created_at', [$startOfQuarter, $endOfQuarter])
            ->get()
            ->groupBy(function ($payment) {
                return $payment->created_at->format('F'); // Group by month name
            });

        // Format the data
        $data = $billPayments->map(function ($payments, $month) {
            return [
                'name' => $month, // Month name
                'expense' =>floatval( $payments->sum('amount'))??0, // Sum of expenses for the month
            ];
        })->values(); // Re-index the collection

        return response()->json(['status' => 'success', 'data' => $data], 200);
    }


    public function yearlyStats()
    {
        $userId = Auth::user()->id;

        // Fetch Yearly data grouped by month
        $billPayments = BillPayment::where('user_id', $userId)
            ->whereYear('created_at', Carbon::now()->year)
            ->get()
            ->groupBy(function ($payment) {
                return $payment->created_at->format('F'); // Group by month name
            })
            ->sortKeysDesc() // Sort months in descending order
            ->take(5); // Take the latest 5 months

        // Format the data
        $data = $billPayments->map(function ($payments, $month) {
            return [
                'name' => $month, // Month name
                'expense' =>floatval( $payments->sum('amount'))??0, // Sum of expenses for the month
            ];
        })->values(); // Re-index the collection

        return response()->json(['status' => 'success', 'data' => $data], 200);
    }

    public function monthlyStats()
{
    $userId = Auth::user()->id;

    // Fetch Monthly data
    $billPayments = BillPayment::where('user_id', $userId)
        ->whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

    // Format the data
    $data = $billPayments->map(function ($payment) {
        return [
            'name' => $payment->created_at->format('d'), // Day of the month
            'expense' => floatval($payment->amount) ?? 0,
        ];
    });

    // Add dummy data if $data is empty
    if ($data->isEmpty()) {
        $data = collect([
            ['name' => '29', 'expense' => 10],

        ]);
    }

    return response()->json(['status' => 'success', 'data' => $data], 200);
}

}
