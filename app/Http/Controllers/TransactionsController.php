<?php

namespace App\Http\Controllers;

use App\Models\BillPayment;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $account_number = $request->input('account_number');
        $bvn = $request->input('bvn');

        $transactions = Transaction::with(['user', 'account'])
            ->when($name, function ($query) use ($name) {
                $query->whereHas('account', function ($query) use ($name) {
                    $query->where('firstName', 'like', '%' . $name . '%');
                });
            })
            ->when($email, function ($query) use ($email) {
                $query->whereHas('user', function ($query) use ($email) {
                    $query->where('email', 'like', '%' . $email . '%');
                });
            })
            ->when($phone, function ($query) use ($phone) {
                $query->whereHas('account', function ($query) use ($phone) {
                    $query->where('phone', 'like', '%' . $phone . '%');
                });
            })
            ->when($account_number, function ($query) use ($account_number) {
                $query->whereHas('account', function ($query) use ($account_number) {
                    $query->where('account_number', 'like', '%' . $account_number . '%');
                });
            })
            ->when($bvn, function ($query) use ($bvn) {
                $query->whereHas('account', function ($query) use ($bvn) {
                    $query->where('bvn', 'like', '%' . $bvn . '%');
                });
            })
            ->paginate(10);
        return view('Transactions.index', compact('transactions'));
    }
    public function pendingPayments(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $account_number = $request->input('account_number');
        $bvn = $request->input('bvn');
        $transactions = Transaction::with(['user', 'account'])->where('status', 'pending')
            ->when($name, function ($query) use ($name) {
                $query->whereHas('account', function ($query) use ($name) {
                    $query->where('firstName', 'like', '%' . $name . '%');
                });
            })
            ->when($email, function ($query) use ($email) {
                $query->whereHas('user', function ($query) use ($email) {
                    $query->where('email', 'like', '%' . $email . '%');
                });
            })
            ->when($phone, function ($query) use ($phone) {
                $query->whereHas('account', function ($query) use ($phone) {
                    $query->where('phone', 'like', '%' . $phone . '%');
                });
            })
            ->when($account_number, function ($query) use ($account_number) {
                $query->whereHas('account', function ($query) use ($account_number) {
                    $query->where('account_number', 'like', '%' . $account_number . '%');
                });
            })
            ->when($bvn, function ($query) use ($bvn) {
                $query->whereHas('account', function ($query) use ($bvn) {
                    $query->where('bvn', 'like', '%' . $bvn . '%');
                });
            })
            ->paginate(10);
        return view('Transactions.paymentRequest', compact('transactions'));
    }
    public function completedPayments(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $account_number = $request->input('account_number');
        $bvn = $request->input('bvn');
        $transactions = Transaction::with(['user', 'account'])->where('status', 'completed')
            ->when($name, function ($query) use ($name) {
                $query->whereHas('account', function ($query) use ($name) {
                    $query->where('firstName', 'like', '%' . $name . '%');
                });
            })
            ->when($email, function ($query) use ($email) {
                $query->whereHas('user', function ($query) use ($email) {
                    $query->where('email', 'like', '%' . $email . '%');
                });
            })
            ->when($phone, function ($query) use ($phone) {
                $query->whereHas('account', function ($query) use ($phone) {
                    $query->where('phone', 'like', '%' . $phone . '%');
                });
            })
            ->when($account_number, function ($query) use ($account_number) {
                $query->whereHas('account', function ($query) use ($account_number) {
                    $query->where('account_number', 'like', '%' . $account_number . '%');
                });
            })
            ->when($bvn, function ($query) use ($bvn) {
                $query->whereHas('account', function ($query) use ($bvn) {
                    $query->where('bvn', 'like', '%' . $bvn . '%');
                });
            })
            ->paginate(10);
        return view('Transactions.paymentLog', compact('transactions'));
    }


    public function billPayments(Request $request)
    {
        $keyword = $request->input('keyword');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $transactions = BillPayment::with(['user.account', 'transaction', 'category'])
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereHas('user.account', function ($q) use ($keyword) {
                        $q->where('firstName', 'like', '%' . $keyword . '%')
                            ->orWhere('lastName', 'like', '%' . $keyword . '%');
                    })
                        ->orWhereHas('user', function ($q) use ($keyword) {
                            $q->where('email', 'like', '%' . $keyword . '%');
                        })
                        ->orWhereHas('category', function ($q) use ($keyword) {
                            $q->where('category', 'like', '%' . $keyword . '%');
                        })
                        ->orWhere('bill_payments.providerName', 'like', '%' . $keyword . '%')
                        ->orWhere('bill_payments.reference', 'like', '%' . $keyword . '%');
                });
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Calculate total deposits for different periods
        $todayCount = BillPayment::whereDate('created_at', Carbon::today())->sum('amount');
        $weekCount = BillPayment::where('created_at', '>=', Carbon::now()->subWeek())->sum('amount');
        $monthCount = BillPayment::where('created_at', '>=', Carbon::now()->subMonth())->sum('amount');
        $yearCount = BillPayment::where('created_at', '>=', Carbon::now()->subYear())->sum('amount');

        return view('Transactions.billPayments', compact(
            'transactions',
            'todayCount',
            'weekCount',
            'monthCount',
            'yearCount'
        ));
    }





    public function pendingBillPayments(Request $request)
    {
        $keyword = $request->input('keyword');
        $status = $request->input('status');

        $transactions = BillPayment::with(['account', 'transaction', 'billerItem.category'])
            ->where('status', 'pending')
            ->when($keyword, function ($query) use ($keyword) {
                $query->whereHas('account', function ($query) use ($keyword) {
                    $query->where('firstName', 'like', '%' . $keyword . '%');
                })
                    ->orWhereHas('billerItem.category', function ($query) use ($keyword) {
                        $query->where('category', 'like', '%' . $keyword . '%');
                    });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->paginate(10);

        return view('Transactions.pendingbillPayment', compact('transactions'));
    }

    public function completeBillPayments(Request $request)
    {
        $keyword = $request->input('keyword');
        $status = $request->input('status');

        $transactions = BillPayment::with(['account', 'transaction', 'billerItem.category'])
            ->where('status', 'success')
            ->when($keyword, function ($query) use ($keyword) {
                $query->whereHas('account', function ($query) use ($keyword) {
                    $query->where('firstName', 'like', '%' . $keyword . '%');
                })
                    ->orWhereHas('billerItem.category', function ($query) use ($keyword) {
                        $query->where('category', 'like', '%' . $keyword . '%');
                    });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->paginate(10);

        return view('Transactions.completedBillPayment', compact('transactions'));
    }
    public function returnBillPayments(Request $request)
    {
        $keyword = $request->input('keyword');
        $status = $request->input('status');

        $transactions = BillPayment::with(['account', 'transaction', 'billerItem.category'])
            ->where('status', 'return')
            ->when($keyword, function ($query) use ($keyword) {
                $query->whereHas('account', function ($query) use ($keyword) {
                    $query->where('firstName', 'like', '%' . $keyword . '%');
                })
                    ->orWhereHas('billerItem.category', function ($query) use ($keyword) {
                        $query->where('category', 'like', '%' . $keyword . '%');
                    });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->paginate(10);

        return view('Transactions.returnBillPayment', compact('transactions'));
    }


    public function billPaymentsShow($id)
    {
        $transactions = BillPayment::where('user_id', $id)->with(['user.account', 'transaction', 'billerItem.category'])->first();
        // dd($transactions);
        return view('Transactions.billPaymentsShow', compact('transactions'));
    }
}
