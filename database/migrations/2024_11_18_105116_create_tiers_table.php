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
        Schema::create('tiers', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Tier name (e.g., "Tier 1", "Tier 2", "Tier 3")
            $table->text('document_required')->nullable(); // Optional documents required for this tier
            $table->text('description')->nullable(); // Optional description of the tier
            $table->decimal('transaction_limit', 15, 2)->default(50000); // Tier's transaction limit
            $table->decimal('daily_limit', 15, 2)->default(50000); // Tier's daily transaction limit
            $table->decimal('balance_limit', 15, 2)->default(300000); // Tier's balance limit
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiers');
    }
};
