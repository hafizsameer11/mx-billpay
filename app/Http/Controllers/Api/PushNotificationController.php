<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ExpoNotificationService;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PushNotificationController extends Controller
{
    public function sendNotification(Request $request, FirebaseNotificationService $firebaseNotificationService)
    {
        // Fetch the user by email
        $user = User::where('email', 'hmstech08@gmail.com')->first();

        // Check if the user exists and has an FCM token
        if (!$user || !$user->fcmToken) {
            return response()->json(['message' => 'User or FCM token not found'], 404);
        }

        $fcmToken ='ekabu-2gQrCMnUTKZfl8Di:APA91bFPZLDRXeug-fgHGnAoefEBslWaK9eSi19yCpSW23pocyEsEw4sNND7VA9g22mZauq1XgXSOgk-vtg9jMAg1DMBnaBrjor4PlVQ5XB9a1xeklz42m8'; // Use the correct property for the FCM token
        $title = "Test Notification";
        $body = "This is a test notification";

        // Send notification
        try {
            $response = $firebaseNotificationService->sendNotification(
                $fcmToken, // Pass the correct FCM token
                $title,
                $body,
                $request->get('data', []) // Optional data payload
            );

            Log::info('Notification response:', $response);

            return response()->json(['message' => 'Notification sent successfully', 'response' => $response]);
        } catch (\Exception $e) {
            Log::error('Error sending notification: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to send notification', 'error' => $e->getMessage()], 500);
        }
    }
    // public function sendNotification(Request $request, ExpoNotificationService $expoNotificationService)
    // {
    //     $user = User::where('email', 'hmstech08@gmail.com')->first();
    //     if (!$user || !$user->fcmToken) {
    //         return response()->json(['message' => 'User or FCM token not found'], 404);
    //     }

    //     $expoToken = $user->fcmToken; // Expo token stored in your database
    //     $title = "Test Notification";
    //     $body = "This is a test notification";

    //     try {
    //         $response = $expoNotificationService->sendNotification(
    //             $expoToken,
    //             $title,
    //             $body,
    //         );

    //         return response()->json(['message' => 'Notification sent successfully', 'response' => $response]);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'Failed to send notification', 'error' => $e->getMessage()], 500);
    //     }
    // }
}
