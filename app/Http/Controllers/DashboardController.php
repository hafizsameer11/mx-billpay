<?php

namespace App\Http\Controllers;

use App\Models\BillPayment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalBillpayments = BillPayment::count();
        $totalTransaction = Transaction::count();
        $transactionCount = Transaction::with(['user', 'account'])
        ->latest() 
        ->take(5)  
        ->get();
        $UsersCount = User::with('account')
        ->latest() 
        ->take(5)  
        ->get();
        $weeklyBillPayments = Transaction::where('transaction_type', 'Bill Payment')
        ->where('created_at', '>=', now()->subWeek())
        ->count();
    
    $monthlyBillPayments = Transaction::where('transaction_type', 'Bill Payment')
        ->where('created_at', '>=', now()->subMonth())
        ->count();
    
    $yearlyBillPayments = Transaction::where('transaction_type', 'Bill Payment')
        ->where('created_at', '>=', now()->subYear())
        ->count();
    
    $totalRevenue = Transaction::where('transaction_type', 'Bill Payment')->sum('amount');
    
        // dd($UsersCount);
    


        $transactions = Transaction::select(DB::raw("SUM(amount) as total_amount"), DB::raw("DATE(created_at) as date"))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Prepare data for the chart
        $revenueChartData = $transactions->pluck('total_amount');
        $revenueChartCategories = $transactions->pluck('date');
        return view('dashboard.index', compact('totalUsers', 'totalBillpayments', 'totalTransaction', 'revenueChartData', 'revenueChartCategories','transactionCount','UsersCount','monthlyBillPayments','weeklyBillPayments','yearlyBillPayments','totalRevenue'));
    }


}
