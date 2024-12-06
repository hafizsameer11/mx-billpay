<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\BvnStatucRecorder;
use App\Models\Notification;
use App\Models\SocialMediaLinks;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function updateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 400);
        }
        $id = Auth::user()->id;
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }
        try {
            $user->email = $request->email;
            $user->save();
            return response()->json(['status' => 'success', 'message' => 'Email updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to update email', 'error' => $e->getMessage()], 500);
        }
    }
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'oldPassword' => 'required|string|min:8',
            'password' => 'required|string|min:8',
            'confirmPassword' => 'required|string|min:8|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 400);
        }
        $id = Auth::user()->id;
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found
                '], 404);
        }
        try {
            if (!Hash::check($request->oldPassword, $user->password)) {
                return response()->json(['status' => 'error', 'message' => 'Old password is
                        incorrect'], 400);
            }
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json(['status' => 'success', 'message' => 'Password updated
                        successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to update
                            password', 'error' => $e->getMessage()],  500);
        }
    }
    public function unreadNotifjications()
    {
        $userId = Auth::user()->id;
        //order by new first
        // $notifications = Notification::where('user_id', $userId)->orderBy('created_at', 'desc')->get(); fetch only 10
        $unreadNotifications = Notification::where('user_id', $userId)->where('read', 0)->orderBy('created_at', 'desc')->get();

        return response()->json(['status' => 'success', 'message' => 'Unread notifications', 'data' => $unreadNotifications], 200);
        // return $unreadNotifications;
    }
    public function checkUserStatus(){
        $userId = Auth::user()->id;
        $account=Account::where('user_id', $userId)->first();
        if($account){
            if($account->status=='PND'){

                return response()->json(['status' => 'pending'], 200);
            }else{
                $bvnStatus=BvnStatucRecorder::where('userId', $userId)->first();
                log::info($bvnStatus);
                if($bvnStatus){
                    $bvnStatus->status="checked";
                    $bvnStatus->save();
                }
                return response()->json(['status' => 'active'], 200);
            }

        }else{
            //account not found
            return response()->json(['status' => 'inactive'], status: 404);

        }
    }
    public function bvnStatusChecker(){

        $userId = Auth::user()->id;
        $bvnStatus=BvnStatucRecorder::where('userId', $userId)->first();

        return response()->json(['status' => $bvnStatus->status], 200);
    }

    //marking notification as read
    public function markAsRead(Request $request)
    {
        // Log::info('Notification ID:', $request[0]);
        // return response()->json($request->notificationId, 200);
        $notification = Notification::where('id', $request[0])->first();
        if (!$notification) {
            return response()->json(['status' => 'error', 'message' => 'Notification not found'], status: 404);
        }
        $notification->read = 1;

        $notification->save();
        // $notification=Notification::where('user_id', Auth::user()->id)->where('read', 0)->orderBy('created_at', 'desc')->get();
        return response()->json(['status' => 'success', 'message' => 'Notification marked as read'], 200);
        // return response()->json(['status' => 'success', 'message' => 'Notification marked as read'], 200);
    }
    public function markAllAsRead()
    {
        $userId = Auth::user()->id;
        $notifications = Notification::where('user_id', $userId)->update(['read' => 1]);
        return response()->json(['status' => 'success', 'message' => 'All notifications marked as read'], 200);
    }

    //set fcm
    public function setFcmToken(Request $request){
        $userId = Auth::user()->id;
        $fcmToken = $request->fcmToken;
        $user=User::where('id', $userId)->first();
        $user->fcmToken=$fcmToken;
        $user->save();
        return response()->json(['status' => 'success', 'message' => 'FCM token set successfully'], 200);

    }
    public function verifyUser(Request $request){
        $userId = Auth::user()->id;
        $passWord=$request->password;
        $user=User::where('id', $userId)->first();
        if(Hash::check($passWord, $user->password)){
            return response()->json(['status' => 'success', 'message' => 'User verified successfully'], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'User verification failed'], 200);
    }
    public function socialMedialinks(){
        $links=SocialMediaLinks::all();
        $links=$links->map(function($link){
            return [
                'id'=>$link->id,
                'title'=>$link->title,
                'link'=>$link->link,
                'icon'=>asset($link->icon)
            ];
        });
        return response()->json(['status' => 'success', 'data'=>$links], 200);
    }
public function deleteAccount(){
    $userId = Auth::user()->id;
    $user=User::where('id', $userId)->first();
    //delete from every table
    
    $user->delete();
    return response()->json(['status' => 'success', 'message' => 'Account deleted successfully'], 200);
}
}
