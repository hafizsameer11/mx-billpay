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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_title')->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('reference')->nullable();
            $table->decimal('amount', 15, 2)->nullable(); // Amount of the transaction
            $table->string('currency')->default('NGN');
            $table->string('from_account_number')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Foreign key referencing users table
            $table->string('to_account_title')->nullable();
            $table->string('from_account_title')->nullable();
            $table->string('to_account_number')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('transaction_date')->nullable(); // Timestamp of when the transaction was processed
            $table->text('response_message')->nullable(); // Message from the response, useful for errors
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
