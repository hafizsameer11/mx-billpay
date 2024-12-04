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
        Schema::table('biller_categories', function (Blueprint $table) {
            $table->integer('order_id')->nullable();
            $table->string('category_title')->nullable();
            $table->string('category_description')->nullable();
        });
        Schema::table('bill_providers', function (Blueprint $table) {
            $table->string('provider_title')->nullable();
            $table->string('provider_description')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biller_categories', function (Blueprint $table) {
            //
        });
    }
};
