<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use App\Models\Wallet;
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
        $wallet=Wallet::where('user_id', $id)->first();
        $email = $user->email;
        $account = Account::where('user_id', $id)->first();
        $firstName = $account->firstName;
        $lastName = $account->lastName;
        $profilePic = $account->profile_picture;
        $accountNo = $wallet->accountNumber;
        $balance = $wallet->accountBalance;
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
            'firstName' => 'nullable|string|max:255',
            'lastName' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'nickName' => 'nullable|string|max:255',
            'gender' => 'nullable|string|in:male,female,other',
            'occupation' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'profilePicture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $userId = Auth::user()->id;

        $account = Account::where('user_id', $userId)->first();

        if (!$account) {
            return response()->json(['message' => 'Account not found.'], 404);
        }
        if($request->filled('firstName')){
            $account->firstName = $request->firstName;
        }
        if($request->filled('lastName')){
            $account->lastName = $request->lastName;
        }
        if($request->filled('phone')){
            $account->phone = $request->phone;
            }
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
        $account = Account::where('user_id', $userId)->with('user')->first();
        if(!$account){
            $user=User::where('id', $userId)->first();
            return response()->json(['status'=>'success','message' => 'Account not found.','user'=>$user], 404);
            }
        return response()->json(['status'=> 'success','data' => $account,], 200);
    }
    public function editprofileDetail()
{
    $userId = Auth::user()->id;
    $account = Account::where('user_id', $userId)->with('user')->first();

    if (!$account) {
        $user = User::where('id', $userId)->first();
        return response()->json([
            'status' => 'success',
            'message' => 'Account not found.',
            'user' => [
                'firstName' => $user->firstName ?? null,
                'lastName' => $user->lastName ?? null,
                'dob' => $user->dob ?? null,
                'occupation' => $user->occupation ?? null,
                'gender' => $user->gender ?? null,

            ]
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'data' => [
            'firstName' => $account->firstName,
            'lastName' => $account->lastName,
            'dob' => $account->dob,
            'occupation' => $account->occupation,
            'gender' => $account->gender,
            'email' => $account->user->email ?? null, // from related User model
            'phone'=> $account->phone ?? null,
            'profilPicture'=>asset( 'storage/'.$account->profile_picture) ?? null,
            // Add any other fields you want to include
        ]
    ], 200);
}

}
