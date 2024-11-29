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
        Schema::create('free_funds', function (Blueprint $table) {
            $table->id();
            $table->string('refference')->nullable();
            $table->string('amount')->nullable();
            $table->string('accountNumber')->nullable();
            $table->string('originatorAccount')->nullable();
            $table->string('originatorAccountNumber')->nullable();
            $table->string('originatorBank')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('free_funds');
    }
};
