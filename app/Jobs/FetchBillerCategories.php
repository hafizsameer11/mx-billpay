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
        $accessToken = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI4MTUiLCJ0b2tlbklkIjoiNjJmOTFlYTItMTQ4NC00MTY1LTg0N2MtN2QxZmI1NzZlYmI3IiwiaWF0IjoxNzI5NTExNzI4LCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.pof77yeMdkhZyO8fQtaFmwT-3bq8fawbkcduxteAfGzP0U9HzaI-vdnrUok90oPvQ_PscdPD1vUPP4Ya5byITA';

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
