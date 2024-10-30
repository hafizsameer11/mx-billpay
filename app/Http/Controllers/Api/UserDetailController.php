<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserDetailController extends Controller
{
    //

    public function detail(Request $request)
    {

        $id = Auth::user()->id;;
        $user = User::where('id', $id)->with('account')->first();
        $userId = $user->id;
        $email = $user->email;
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
    public function updateProfile(Request $request)
    {
        $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'nickName' => 'nullable|string|max:255',
            'gender' => 'nullable|string|in:male,female,other',
            'occupation' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'profilePicture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image
        ]);

        $userId = Auth::user()->id;

        $account = Account::where('user_id', $userId)->first();

        if (!$account) {
            return response()->json(['message' => 'Account not found.'], 404);
        }

        $account->firstName = $request->firstName;
        $account->lastName = $request->lastName;
        $account->phone = $request->phone;

        if ($request->filled('nickName')) {
            $account->nickName = $request->nickName;
        }
        if ($request->filled('gender')) {
            $account->gender = $request->gender;
        }
        if ($request->filled('occupation')) {
            $account->occupation = $request->occupation;
        }
        if ($request->filled('dob')) {
            $account->dob = $request->dob;
        }
        if ($request->hasFile('profilePicture')) {
            $profilePicture = $request->file('profilePicture');
            $fileName = uniqid() . '.' . $profilePicture->getClientOriginalExtension();
            $profilePicturePath = $profilePicture->storeAs('profile_pictures', $fileName, 'public');
            $account->profile_picture = $profilePicturePath; // Update the profile picture path
        }
        $account->save();
        return response()->json(['message' => 'Profile updated successfully.', 'data' => $account], 200);
    }
    public function profileDetail(){
        $userId = Auth::user()->id;
        $account = Account::where('user_id', $userId)->first();
        return response()->json(['success'=> true,'data' => $account], 200);
    }
}
