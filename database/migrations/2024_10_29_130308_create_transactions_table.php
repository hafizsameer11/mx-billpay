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
            $table->decimal('amount', 15, 2); // Amount of the transaction
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign key referencing users table
            $table->string('transaction_type'); // e.g., 'bill_payme
            $table->timestamp('transaction_date')->nullable();
            $table->string('sign')->nullable();
            $table->string('status')->default('pending');
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
