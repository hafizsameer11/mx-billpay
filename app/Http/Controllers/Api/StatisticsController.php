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
        $data = $billPayments->map(function ($payment) {
            return [
                'name' => $payment->created_at->format('d'), // Day of the month
                'expense' => $payment->amount,
            ];
        });

        return response()->json(['status'=>'success','data' => $data], 200);
    }

    public function quarterlyStats()
    {
        $userId = Auth::user()->id;

        // Fetch Quarterly data
        $startOfQuarter = Carbon::now()->startOfQuarter();
        $endOfQuarter = Carbon::now()->endOfQuarter();

        $billPayments = BillPayment::where('user_id', $userId)
            ->whereBetween('created_at', [$startOfQuarter, $endOfQuarter])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Format the data
        $data = $billPayments->map(function ($payment) {
            return [
                'name' => $payment->created_at->format('d'), // Day of the month
                'expense' => $payment->amount,
            ];
        });

        return response()->json(['status'=>'success','data' => $data], 200);
    }

    public function yearlyStats()
    {
        $userId = Auth::user()->id;

        // Fetch Yearly data
        $billPayments = BillPayment::where('user_id', $userId)
            ->whereYear('created_at', Carbon::now()->year)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Format the data
        $data = $billPayments->map(function ($payment) {
            return [
                'name' => $payment->created_at->format('d'), // Day of the month
                'expense' => $payment->amount,
            ];
        });

        return response()->json(['status'=>'success','data' => $data], 200);
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
                'expense' => $payment->amount,
            ];
        });

        return response()->json(['status'=>'success','data' => $data], 200);
    }
}
