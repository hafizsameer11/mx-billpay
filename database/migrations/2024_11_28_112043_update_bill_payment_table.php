<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bill_payments', function (Blueprint $table) {
            //field forr storing json response from paystack
            $table->json('response')->nullable();
            $table->decimal('amount', 15, 2); // Amount of the transaction
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billpayments', function (Blueprint $table) {
            //
        });
    }
};
