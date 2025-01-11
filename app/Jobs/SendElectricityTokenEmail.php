<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendElectricityTokenEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $customerName;
    public $customerEmail;
    public $tokenNumber;

    /**
     * Create a new job instance.
     */
    public function __construct(string $customerName, string $customerEmail, string $tokenNumber)
    {
        $this->customerName = $customerName;
        $this->customerEmail = $customerEmail;
        $this->tokenNumber = $tokenNumber;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->customerEmail)->send(new ElectricityTokenMail($this->customerName, $this->tokenNumber));
    }
}
