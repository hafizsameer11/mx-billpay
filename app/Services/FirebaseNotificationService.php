<?php

namespace App\Services;

use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    protected $fcmUrl = 'https://fcm.googleapis.com/v1/projects/mx-bill-pay-5c87d/messages:send';
    protected $httpClient;

    public function __construct()
    {
        $credentialsPath = storage_path('app/json/file.json'); // Path to your Service Account key

        // Set environment variable for Google authentication
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $credentialsPath);

        $middleware = ApplicationDefaultCredentials::getMiddleware([
            'https://www.googleapis.com/auth/cloud-platform',
            'https://www.googleapis.com/auth/firebase.messaging',
        ]);
        Log::info($credentialsPath);

        $stack = HandlerStack::create();
        $stack->push($middleware);

        $this->httpClient = new Client(['handler' => $stack]);
    }

    public function sendNotification($fcmToken, $title, $body, $data = [])
    {
        $payload = [
            'message' => [
                'token' => $fcmToken, // The target device FCM token
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data, // Optional custom data payload
            ],
        ];

        // Prepare and send the request
        $request = new Request(
            'POST',
            $this->fcmUrl,
            ['Content-Type' => 'application/json'],
            json_encode($payload)
        );

        $response = $this->httpClient->send($request);

        return json_decode($response->getBody(), true);
    }
}
