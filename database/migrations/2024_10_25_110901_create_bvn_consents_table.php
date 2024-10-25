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
        Schema::create('bvn_consents', function (Blueprint $table) {
            $table->id();
            $table->string('bvn')->nullable(); // Bank Verification Number
            $table->string('type')->nullable(); // Type of request
            $table->string('user_id')->nullable(); // User ID
            $table->string('reference')->nullable(); // Reference generated for the request
            $table->json('response')->nullable(); // Store the response from the API
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bvn_consents');
    }
};
