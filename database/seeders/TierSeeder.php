<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tiers')->insert([
            [
                'title' => 'Tier 1',
                'document_required' => 'Passport Photograph, Biodata, BVN or NIN',
                'description' => 'Low-level KYC with basic details.',
                'transaction_limit' => 50000,
                'daily_limit' => 50000,
                'balance_limit' => 300000,
            ],
            [
                'title' => 'Tier 2',
                'document_required' => 'Validated Government issued ID, Tier 1 documents',
                'description' => 'Medium-level KYC with validated ID.',
                'transaction_limit' => 200000,
                'daily_limit' => 200000,
                'balance_limit' => 500000,
            ],
            [
                'title' => 'Tier 3',
                'document_required' => 'Tier 2 documents and valid utility bill for address verification.',
                'description' => 'High-level KYC with complete documentation.',
                'transaction_limit' => 1000000,
                'daily_limit' => 1000000,
                'balance_limit' => 1000000,
            ],
        ]);
    }
}
