<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {

        $name = $request->input('name');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $created_at = $request->input('created_at');
        $updated_at = $request->input('updated_at');

        $users = User::with('account')
            ->when($name, function ($query) use ($name) {
                $query->whereHas('account', function ($query) use ($name) {
                    $query->where('firstName', 'like', '%' . $name . '%'); // Searching in account's firstName
                });
            })
            ->when($phone, function ($query) use ($phone) {
                $query->whereHas('account', function ($query) use ($phone) {
                    $query->where('phone', 'like', '%' . $phone . '%'); // Searching in account's phone
                });
            })
            ->when($email, function ($query) use ($email) {
                $query->where('email', 'like', '%' . $email . '%'); // Searching directly in users table's email
            })
            ->when($created_at, function ($query) use ($created_at) {
                $query->whereDate('created_at', $created_at);
            })
            ->when($updated_at, function ($query) use ($updated_at) {
                $query->whereDate('updated_at', $updated_at);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);


        return view('Users.index', compact('users'));
    }

    public function edit($id)
    {
        $user = User::where('id', $id)->with('account')->first();
        // dd($user);
        // dd($user->account->status);

        return view('Users.edit', compact('user'));
    }

    public function update($id, Request $request)
    {
        // Retrieve the user with the related account
        $user = User::where('id', $id)->with('account')->first();

        // Validate request data
        $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'status' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string|max:15',
        ]);

        $user->email = $request->email;
        $user->save();

        if ($user->account) {
            $user->account->firstName = $request->firstName;
            $user->account->lastName = $request->lastName;
            $user->account->status = $request->status;
            $user->account->phone = $request->phone;
            $user->account->save();
        }

        return redirect()->route('user.index')->with('success', 'User updated successfully.');
    }


    public function show(Request $request, $id)
    {
        $user = User::where('id', $id)->with('account', 'billPayment.category')->first();
        $wallet = Wallet::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();

        // Date Range Filters
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $transactionsQuery = Transaction::where('user_id', $id)
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->orderBy('created_at', 'desc');

        $transactions = $transactionsQuery->paginate(10);

        return view('Users.showUser', compact('user', 'transactions', 'wallet'));
    }

}
