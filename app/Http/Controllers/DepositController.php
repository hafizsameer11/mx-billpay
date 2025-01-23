<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $transactions = Transaction::where('transaction_type', 'Inward Credit')
            ->with(['user', 'user.account', 'transfer'])
            ->when($keyword, function ($query) use ($keyword) {
                $query->whereHas('user.account', function ($q) use ($keyword) {
                    $q->where('firstName', 'like', '%' . $keyword . '%')
                        ->orWhere('lastName', 'like', '%' . $keyword . '%');
                })
                    ->orWhereHas('user', function ($q) use ($keyword) {
                        $q->where('email', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('billerItem.category', function ($q) use ($keyword) {
                        $q->where('category', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('transfer', function ($q) use ($keyword) {
                        $q->where('reference', 'like', '%' . $keyword . '%');
                    });
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        $today = Carbon::today();
        $week = Carbon::today()->subDays(7);
        $month = Carbon::today()->subDays(30);
        $year = Carbon::today()->subDays(365);
        $todayCount = Transaction::where('transaction_type', 'Inward Credit')->where('created_at', '>=', $today)->sum('amount');
        $weekCount = Transaction::where('transaction_type', 'Inward Credit')->where('created_at', '>=', $week)->sum('amount');
        $monthCount = Transaction::where('transaction_type', 'Inward Credit')->where('created_at', '>=', $month)->sum('amount');
        $yearCount = Transaction::where('transaction_type', 'Inward Credit')->where('created_at', '>=', $year)->sum('amount');

        return view('Transactions.deposits', compact('transactions', 'todayCount', 'weekCount', 'monthCount', 'yearCount'));
    }

}
