<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserDetailController extends Controller
{
    //

    public function detail(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'userId' => 'required'
        ]);
        if ($validation->fails()) {
            $errorMessage = $validation->errors()->first();
            return response()->json(['message' => $errorMessage, 'status' =>
            'error'], 400);
        }
        $id = $request->userId;
        $user = User::where('id', $id)->with('account')->first();
        $userId = $user->id;
        $email = $user->email;
        // $firstName = $user->account->firstName;
        $account = Account::where('user_id', $id)->first();
        $firstName = $account->firstName;
        $lastName = $account->lastName;
        $profilePic = $account->profile_picture;
        $accountNo = $account->account_number;
        $balance = $account->accountBalance;
        if (!$user) {
            return response()->json(['message' => 'User not found', 'status' => 'error'], 404);
        } else {
            return response()->json([
                'message' => 'User found',
                'status' => 'success',
                'data' => [
                    'userId' => $userId,
                    'email' => $email,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'profilePic' => $profilePic,
                    'accountNo' => $accountNo,
                    'balance' => $balance,
                ]

            ], 200);
        }
    }
}
