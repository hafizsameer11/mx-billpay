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

        Schema::create('biller_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('biller_categories')->onDelete('cascade'); // Foreign key to categories
            $table->string('paymentitemname');
            $table->string('paymentCode')->nullable();
            $table->string('productId')->nullable();
            $table->string('paymentitemid')->nullable();
            $table->string('currencySymbol')->default('NGN');
            $table->tinyInteger('isAmountFixed')->default(0); // Change this line to tinyInteger
            $table->decimal('itemFee', 10, 2)->default(0);
            $table->string('itemCurrencySymbol')->default('NGN');
            $table->string('pictureId')->nullable();
            $table->string('billerType')->nullable();
            $table->string('payDirectitemCode')->nullable();
            $table->string('currencyCode')->nullable();
            $table->string('division')->nullable(); // Added division field
            // Add percentage and fixed commission fields
            $table->decimal('fixed_commission', 10, 2)->default(0);
            $table->decimal('percentage_commission', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biller_items');
    }
};
