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
        $this->accessToken = config('access_token.live_token');
        // $this->accessToken = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI4MTUiLCJ0b2tlbklkIjoiZGE1YjM5ZDItMGE2MS00MGE5LTg2ZGYtNTFjNDE5NmU4MmMyIiwiaWF0IjoxNzMxOTIyNjMyLCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.D8lFZCna6PZNIXnmJt-Xwc2JJ9rYxNPv4x5yDwRnldGs6tZu8KAlCoXumVIcXuUrOvcEud0hSIkQ7hZUjsFh7Q';
        $this->categoryId = $categoryId; // Set the category ID
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Fetch billers for the category
        $response = Http::withHeaders(['AccessToken' =>$this->accessToken])
            ->get('https://api-apps.vfdbank.systems/vtech-wallet/api/v1/billspaymentstore/billerlist', [
                'categoryName' => $this->categoryName
            ]);

        if ($response->successful()) {
            $billers = $response->json()['data'];

            Log::info('Fetched billers for category: ' . $this->categoryName, ['billers' => $billers]);

            foreach ($billers as $biller) {
                $this->getBillerItems($biller['id'], $biller['division'], $biller['product'],$biller['name']);
            }
        } else {
            Log::error('Failed to fetch billers for category: ' . $this->categoryName, ['response' => $response->json()]);
        }
    }
    private function getBillerItems($billerId, $divisionId, $productId,$billerName)
    {
        $response = Http::withHeaders(['AccessToken' => $this->accessToken])
            ->get('https://api-apps.vfdbank.systems/vtech-wallet/api/v1/billspaymentstore/billerItems', [
                'billerId' => $billerId,
                'divisionId' => $divisionId,
                'productId' => $productId,
            ]);

        // Log the response for debugging
        Log::info('Response from Biller Items API for Biller ID: ' . $billerId, ['response' => $response->json()]);

        if ($response->successful()) {
            $items = $response->json()['data']['paymentitems'];
            foreach ($items as $item) {
                Log::info('Item details: ' , [$item]);
                BillerItem::updateOrCreate(
                    [ 'paymentitemname' => $item['paymentitemname']??'N/A', 'category_id' => $this->categoryId],
                    [
                        'provider_name'=>$billerName ?? 'N/A',
                        'billerId'=>$billerId??'N/A',
                        'paymentitemid' => $item['paymentitemid']??'N/A',
                        
                        'paymentCode' => $item['paymentCode']??'N/A',
                        'productId' => $item['productId']??'N/A',
                        'currencySymbol' => $item['currencySymbol']??'N/A',
                        'isAmountFixed' => $item['isAmountFixed'] ? 1 : 0,
                        'itemFee' => $item['itemFee'],
                        'itemCurrencySymbol' => $item['itemCurrencySymbol'],
                        'pictureId' => $item['pictureId']??'N/A',
                        'billerType' => $item['billerType'],
                        'payDirectitemCode' => $item['payDirectitemCode'] ?? 'N/A',
                        'currencyCode' => $item['currencyCode'],
                        'division' => $item['division'],
                        'fixed_commission' => 0,
                        'percentage_commission' => 0,
                    ]
                );
            }
        } else {
            Log::error('Failed to fetch items for Biller ID: ' . $billerId, ['error' => $response->json()]);
        }
    }

}
