<?php

namespace App\Http\Controllers;

use App\Models\BillPayment;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $created_at = $request->input('created_at');

        $transactions = BillPayment::with(['user.account', 'transaction', 'category'])
            ->leftJoin('bill_providers', 'bill_payments.providerName', '=', 'bill_providers.billerId')
            ->select(
                'bill_payments.*',
                'bill_providers.billerId',
                'bill_providers.fixed_comission as provider_fixed_comission',
                'bill_providers.percentage_comission as provider_percentage_comission'
            )
            ->where('bill_payments.status', 'success')
            ->when($keyword, function ($query) use ($keyword) {
                $query->whereHas('user.account', function ($query) use ($keyword) {
                    $query->where('firstName', 'like', '%' . $keyword . '%');
                })->orwhereHas('user', function ($query) use ($keyword) {
                    $query->where('email', 'like', '%' . $keyword . '%');
                })
                    ->orWhereHas('billerItem.category', function ($query) use ($keyword) {
                        $query->where('category', 'like', '%' . $keyword . '%');
                    })
                    ->orWhere('bill_providers.name', 'like', '%' . $keyword . '%'); // Search in provider name as well
            })
            ->paginate(10);
// dd($transactions);
        return view('Transactions.revenue', compact('transactions'));
    }
}
