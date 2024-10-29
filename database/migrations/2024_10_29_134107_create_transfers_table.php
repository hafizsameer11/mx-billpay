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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade'); // Foreign key referencing transactions table
            $table->string('from_account_number')->nullable(); // Sender's account number
            $table->string('to_account_number')->nullable(); // Receiver's account number
            $table->string('from_client_id')->nullable(); // Client ID of the sender
            $table->string('to_client_id')->nullable(); // Client ID of the receiver
            $table->string('status')->default('pending'); // Status of the transfer (e.g., pending, completed, failed)
            $table->string('to_client_name')->nullable();
            $table->string('from_client_name')->nullable();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade'); // Foreign key referencing transactions table
            $table->decimal('amount', 15, 2)->nullable(); // Amount of the transfer

            $table->text('response_message')->nullable(); // Message from the response, useful for errors

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
