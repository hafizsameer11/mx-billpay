<?php

namespace App\Console\Commands;

use App\Models\BillerCategory;
use App\Models\BillProviders;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchBillers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:billers';
    protected $accessToken;
    public function __construct()
    {
        parent::__construct(); // Initialize the parent constructor
        $this->accessToken = config('access_token.live_token');
    }

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch billers for each category and store them in the database';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $categories = BillerCategory::all();
        foreach ($categories as $category) {
            $originalTitle = $category->originalTitle;
            if (empty($originalTitle)) {
                Log::warning("Skipping category with null or empty originalTitle: Category ID - {$category->id}");
                continue;
            }
            $response = Http::withHeaders(['AccessToken' => $this->accessToken])
                ->get('https://api-apps.vfdbank.systems/vtech-wallet/api/v1/billspaymentstore/billerlist', [
                    'categoryName' => $originalTitle,
                ]);
            if ($response->successful()) {
                $billers = $response->json()['data'];
                Log::info('Fetched billers for category: ' . $originalTitle, ['billers' => $billers]);
                foreach ($billers as $biller) {
                    $existingBiller = BillProviders::where('billerId', $biller['id'])->first();
                    if ($existingBiller) {
                        if (
                            $existingBiller->division !== $biller['division'] ||
                            $existingBiller->name !== $biller['name'] ||
                            $existingBiller->product !== $biller['product']
                        ) {
                            $existingBiller->update([
                                'title' => $biller['name'],
                                'division' => $biller['division'],
                                'name' => $biller['name'],
                                'billerId' => $biller['id'],
                                'product' => $biller['product'],
                            ]);
                            Log::info("Updated biller: {$biller['id']}", [
                                'name' => $biller['name'],
                                'division' => $biller['division'],
                            ]);
                        }
                    } else {
                        BillProviders::create([
                            'title' => $biller['name'],
                            'slug' => $biller['id'],
                            'biller_category_id' => $category->id,
                            'division' => $biller['division'],
                            'product' => $biller['product'], //
                            'name' => $biller['name'], //
                            'billerId' => $biller['id'],
                        ]);
                        Log::info("Added new biller: {$biller['id']}", [
                            'name' => $biller['name'],
                            'division' => $biller['division'],
                        ]);
                    }
                }
            } else {
                Log::error('Failed to fetch billers for category: ' . $originalTitle, [
                    'response' => $response->json(),
                ]);
            }
        }
        Log::info('Billers fetched and stored successfully.');
    }
}
