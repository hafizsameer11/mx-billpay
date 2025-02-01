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
        Schema::create('request_logs', function (Blueprint $table) {
            $table->id();
            $table->string('method'); // GET, POST, PUT, DELETE
            $table->string('url'); // The requested URL
            $table->json('request_data')->nullable(); // Request payload
            $table->string('ip_address'); // Client IP
            $table->string('user_agent')->nullable(); // User-Agent header
            $table->string('device_type')->nullable(); // Mobile or Desktop
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_logs');
    }
};
