<?php

namespace App\Jobs;

use App\Models\Bank;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class FetchBankListJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $accessToken;
    public function __construct($accessToken)
    {
        $this->accessToken=$accessToken;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $response = Http::withHeaders(['AccessToken' => $this->accessToken])
            ->get('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/bank');

        if ($response->successful()) {
            $banks = $response->json()['data'];

            foreach ($banks as $bank) {
                // Save each bank's details in the database
                Bank::updateOrCreate(
                    ['code' => $bank['code']], // Use bank code to identify duplicates
                    [
                        'name' => $bank['name'],
                        'logo' => $bank['logo']
                    ]
                );
            }
        }
    }
}
