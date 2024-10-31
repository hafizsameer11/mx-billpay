<?php

namespace App\Jobs;

use App\Models\Bank;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FetchBankListJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $accessToken;
    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $response = Http::withHeaders(['AccessToken' => $this->accessToken])
            ->get('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/bank');
        Log::info('Bank List API Response:', [
            'response' => $response->json()
        ]);
        if ($response->successful()) {
            $banks = $response->json()['data']['bank'];
            foreach ($banks as $bank) {
                if (isset($bank['logo'])) {
                    $logoUrl = $this->storeLogo($bank['logo'], $bank['code']);
                    $bank['logo'] = $logoUrl;
                }
                Bank::updateOrCreate(
                    ['code' => $bank['code']],
                    [
                        'name' => $bank['name'],
                        'logo' => $bank['logo']
                    ]
                );
            }
        }
    }

    private function storeLogo($base64Logo, $bankCode)
    {
        // Split the Base64 string to get the actual data
        $imageData = explode(',', $base64Logo);
        if (count($imageData) > 1) {
            $image = base64_decode(end($imageData)); // Decode the actual image data

            // Define the image path
            $fileName = 'bank_logos/' . $bankCode . '.png'; // Use bank code to name the file

            // Store the image in the public disk
            Storage::disk('public')->put($fileName, $image);

            // Return the full URL of the stored image
            return url( $fileName); // This generates the full URL
        }

        return null; // Handle the case where logo is not provided properly
    }
}
