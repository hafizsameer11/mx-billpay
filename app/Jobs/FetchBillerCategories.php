<?php

namespace App\Jobs;

use App\Events\BillerCategoriesFetched;
use App\Models\BillerCategory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchBillerCategories implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $accessToken = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI4MTUiLCJ0b2tlbklkIjoiZGE1YjM5ZDItMGE2MS00MGE5LTg2ZGYtNTFjNDE5NmU4MmMyIiwiaWF0IjoxNzMxOTIyNjMyLCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.D8lFZCna6PZNIXnmJt-Xwc2JJ9rYxNPv4x5yDwRnldGs6tZu8KAlCoXumVIcXuUrOvcEud0hSIkQ7hZUjsFh7Q';

        $response = Http::withHeaders(['AccessToken' => $accessToken])
            ->get('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/billspaymentstore/billercategory');
        if ($response->successful()) {
            $categories = $response->json()['data'];
            foreach ($categories as $category) {
                BillerCategory::updateOrCreate(
                    ['category' => $category['category']],
                    ['category' => $category['category']]
                );
            }
            broadcast(new BillerCategoriesFetched($categories));
        } else {
            Log::error('Failed to fetch categories', ['response' => $response->json()]);
        }
    }
}
