<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    public function sendNotification(Request $request, FirebaseNotificationService $firebaseNotificationService)
    {
        $user = User::where('email', 'hmstech08@gmail.com')->first();
        $fcmToken = $user->fcm_token;
        $title = "Test Notification";
        $body = "This is a test notification";
        $response = $firebaseNotificationService->sendNotification(
            $user->fcm_token,
            $title,
            $body,
            $request->get('data', [])
        );

        return response()->json(['message' => 'Notification sent', 'response' => $response]);
    }
}
