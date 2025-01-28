<?php

namespace App\Http\Controllers;

use App\Models\BillPayment;
use App\Models\Transaction;
use App\Models\User;
use Google\Service\Dfareporting\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Summary Counts
        $totalUsers = User::count();
        $totalBillpayments = BillPayment::count();
        $totalTransaction = Transaction::count();

        // Revenue Calculation
        $totalAmount = BillPayment::sum('amount'); // Total bill payment amounts
        $totalAmountPaid = BillPayment::sum('totalAmount'); // Total amount paid
        $totalCommission = 0;

        // Fetch transactions with commission details
        $transactions = BillPayment::with(['user.account', 'transaction', 'category'])
            ->leftJoin('bill_providers', 'bill_payments.providerName', '=', 'bill_providers.billerId')
            ->select(
                'bill_payments.*',
                'bill_providers.billerId',
                'bill_providers.fixed_comission as provider_fixed_comission',
                'bill_providers.percentage_comission as provider_percentage_comission'
            )
            ->where('bill_payments.status', 'success')
            ->get();

        // Calculate total commissions
        foreach ($transactions as $item) {
            $commission = ($item->amount * ($item->provider_percentage_comission / 100)) + $item->provider_fixed_comission;
            $totalCommission += $commission;
        }

        // Calculate total revenue
        $totalLastAmount =  $totalAmountPaid- $totalAmount ;
        $totalRevenue = $totalLastAmount + $totalCommission;

        // Bill Payments by Time Period
        $weeklyBillPayments = BillPayment::where('created_at', '>=', now()->subWeek())->count();
        $monthlyBillPayments = BillPayment::where('created_at', '>=', now()->subMonth())->count();
        $yearlyBillPayments = BillPayment::where('created_at', '>=', now()->subYear())->count();

        // Daily Revenue and Transactions for Chart
        $dailyData = BillPayment::select(
            DB::raw("SUM(amount) as total_amount"),
            DB::raw("SUM(totalAmount) as total_amount_paid"),
            DB::raw("DATE(bill_payments.created_at) as date")
        )
            ->where('bill_payments.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $dailyRevenue = collect();
        $dailyTransactions = collect();
        $chartCategories = collect();

        foreach ($dailyData as $day) {
            $dailyTransactionsData = BillPayment::whereDate('bill_payments.created_at', $day->date)
                ->leftJoin('bill_providers', 'bill_payments.providerName', '=', 'bill_providers.billerId')
                ->select(
                    'bill_payments.amount',
                    'bill_providers.fixed_comission as provider_fixed_comission',
                    'bill_providers.percentage_comission as provider_percentage_comission'
                )
                ->get();

            $dailyCommission = 0;
            foreach ($dailyTransactionsData as $transaction) {
                $commission = ($transaction->amount * ($transaction->provider_percentage_comission / 100)) + $transaction->provider_fixed_comission;
                $dailyCommission += $commission;
            }

            $dailyRevenue->push(($day->total_amount - $day->total_amount_paid) + $dailyCommission);
            $dailyTransactions->push($dailyTransactionsData->count()); // Count transactions per day
            $chartCategories->push($day->date);
        }

        // Latest Entries
        $latestBillPayments = BillPayment::with(['user', 'transaction'])->orderBy('created_at', 'desc')->take(5)->get();
        $latestUsers = User::with('account')->orderBy('created_at', 'desc')->take(5)->get();

        return view('dashboard.index', compact(
            'totalUsers',
            'totalBillpayments',
            'totalTransaction',
            'totalAmount',
            'totalAmountPaid',
            'totalCommission',
            'totalRevenue',
            'weeklyBillPayments',
            'monthlyBillPayments',
            'yearlyBillPayments',
            'dailyRevenue',
            'dailyTransactions',
            'chartCategories',
            'latestBillPayments',
            'latestUsers'
        ));
    }


}
