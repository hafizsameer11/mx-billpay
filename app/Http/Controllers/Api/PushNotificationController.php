<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PushNotificationController extends Controller
{
    public function sendNotification(Request $request, FirebaseNotificationService $firebaseNotificationService)
    {
        $user = User::where('email', 'hmstech08@gmail.com')->first();
        $fcmToken = $user->fcm_token;
        $title = "Test Notification";
        $body = "This is a test notification";
        $response = $firebaseNotificationService->sendNotification(
            $user->fcmToken,
            $title,
            $body,
            $request->get('data', [])
        );
        Log::info($response);

        return response()->json(['message' => 'Notification sent', 'response' => $response]);
    }
}
