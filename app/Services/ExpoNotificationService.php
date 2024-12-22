<?php
namespace App\Services;

use GuzzleHttp\Client;

class ExpoNotificationService
{
    protected $expoPushUrl = 'https://exp.host/--/api/v2/push/send';

    public function sendNotification($expoToken, $title, $body, $data = [])
    {
        if (!str_starts_with($expoToken, 'ExponentPushToken')) {
            throw new \Exception('Invalid Expo Push Token');
        }

        $payload = [
            'to' => $expoToken,
            'title' => $title,
            'body' => $body,
            'data' => $data, // Optional custom data
        ];

        $client = new Client();
        $response = $client->post($this->expoPushUrl, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);

        return json_decode($response->getBody(), true);
    }
}

