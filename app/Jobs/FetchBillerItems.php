<?php

namespace App\Jobs;

use App\Models\BillerItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchBillerItems implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $categoryName;
    protected $accessToken;
    protected $categoryId; // Add category ID
    /**
     * Create a new job instance.
     */
    public function __construct($categoryName, $categoryId)
    {
        $this->categoryName = $categoryName;

        $this->accessToken = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiIzNjgiLCJ0b2tlbklkIjoiMjdiOGNmZjEtYjExNS00MGMzLWIwNDAtMDVjNmViMjljNTk3IiwiaWF0IjoxNzMxOTExNDIyLCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.T4dMWHEwST4OUskaQzl1W-dS1rDhFpi7vgPsCfEM58itWKDnbYabnXJnfis4Vr7mIDobzrCaA-UKq9oVjdo49g';
        $this->categoryId = $categoryId; // Set the category ID
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Fetch billers for the category
        $response = Http::withHeaders(['AccessToken' =>$this->accessToken])
            ->get('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/billspaymentstore/billerlist', [
                'categoryName' => $this->categoryName
            ]);

        if ($response->successful()) {
            $billers = $response->json()['data'];

            Log::info('Fetched billers for category: ' . $this->categoryName, ['billers' => $billers]);

            foreach ($billers as $biller) {
                $this->getBillerItems($biller['id'], $biller['division'], $biller['product']);
            }
        } else {
            Log::error('Failed to fetch billers for category: ' . $this->categoryName);
        }
    }
    private function getBillerItems($billerId, $divisionId, $productId)
    {
        $response = Http::withHeaders(['AccessToken' => $this->accessToken])
            ->get('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/billspaymentstore/billerItems', [
                'billerId' => $billerId,
                'divisionId' => $divisionId,
                'productId' => $productId,
            ]);

        // Log the response for debugging
        Log::info('Response from Biller Items API for Biller ID: ' . $billerId, ['response' => $response->json()]);

        if ($response->successful()) {
            $items = $response->json()['data']['paymentitems'];

            // Log the number of items fetched
            Log::info('Fetched items for Biller ID: ' . $billerId, ['items' => $items]);

            foreach ($items as $item) {
                BillerItem::updateOrCreate(
                    ['paymentitemid' => $item['paymentitemid'], 'category_id' => $this->categoryId],
                    [
                        'billerId'=>$billerId,
                        'paymentitemname' => $item['paymentitemname'],
                        'paymentCode' => $item['paymentCode'],
                        'productId' => $item['productId'],
                        'currencySymbol' => $item['currencySymbol'],
                        'isAmountFixed' => $item['isAmountFixed'] ? 1 : 0,
                        'itemFee' => $item['itemFee'],
                        'itemCurrencySymbol' => $item['itemCurrencySymbol'],
                        'pictureId' => $item['pictureId'],
                        'billerType' => $item['billerType'],
                        'payDirectitemCode' => $item['payDirectitemCode'],
                        'currencyCode' => $item['currencyCode'],
                        'division' => $item['division'],
                        'fixed_commission' => 0, // Default commission
                        'percentage_commission' => 0, // Default commission
                    ]
                );
            }
        } else {
            Log::error('Failed to fetch items for Biller ID: ' . $billerId, ['error' => $response->json()]);
        }
    }

}
