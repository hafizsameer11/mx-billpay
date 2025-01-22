<?php

namespace App\Http\Controllers;

use App\Models\BillPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $transactions = BillPayment::with(['user.account', 'transaction', 'category'])
            ->leftJoin('bill_providers', 'bill_payments.providerName', '=', 'bill_providers.billerId')
            ->select(
                'bill_payments.*',
                'bill_providers.billerId',
                'bill_providers.fixed_comission as provider_fixed_comission',
                'bill_providers.percentage_comission as provider_percentage_comission'
            )
            ->where('bill_payments.status', 'success')

            // Handle keyword search independently
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereHas('user.account', function ($q) use ($keyword) {
                        $q->where('firstName', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('user', function ($q) use ($keyword) {
                        $q->where('email', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('billerItem.category', function ($q) use ($keyword) {
                        $q->where('category', 'like', '%' . $keyword . '%');
                    })
                    ->orWhere('bill_providers.name', 'like', '%' . $keyword . '%');
                });
            })

            // Handle date range independently
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('bill_payments.created_at', [$startDate, $endDate]);
            })

            ->orderBy('bill_payments.created_at', 'desc')
            ->paginate(15);

        // Revenue calculations
        $dailyRevenue = $this->calculateRevenue(Carbon::today());
        $weeklyRevenue = $this->calculateRevenue(Carbon::now()->subWeek());
        $monthlyRevenue = $this->calculateRevenue(Carbon::now()->subMonth());
        $yearlyRevenue = $this->calculateRevenue(Carbon::now()->subYear());

        return view('Transactions.revenue', compact(
            'transactions',
            'dailyRevenue',
            'weeklyRevenue',
            'monthlyRevenue',
            'yearlyRevenue'
        ));
    }




    /**
     * Function to calculate revenue based on the given date range.
     */
    private function calculateRevenue($startDate)
    {
        $billPayments = BillPayment::where('bill_payments.created_at', '>=', $startDate)->leftJoin('bill_providers', 'bill_payments.providerName', '=', 'bill_providers.billerId')
            ->select(
                'bill_payments.*',
                'bill_providers.billerId',
                'bill_providers.fixed_comission as provider_fixed_comission',
                'bill_providers.percentage_comission as provider_percentage_comission'
            )->get();

        $totalRevenue = 0;
        foreach ($billPayments as $payment) {
            $commission = ($payment->amount * ($payment->provider_percentage_comission / 100)) + $payment->provider_fixed_comission;
            $totalRevenue += ($payment->amount - $payment->totalAmount) + $commission;
        }

        return $totalRevenue;
    }

}
