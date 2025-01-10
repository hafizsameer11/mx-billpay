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
        Schema::create('log_responses', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('null');
            $table->enum('log_type', ['error', 'billpayment', 'validation', 'transaction', 'response'])->default('response');
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_responses');
    }
};
