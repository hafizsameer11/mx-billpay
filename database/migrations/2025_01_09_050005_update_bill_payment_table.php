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
        Schema::table('bill_payments', function (Blueprint $table) {
            $table->foreignId('category_id') // Creates an unsignedBigInteger and foreign key constraint
            ->constrained('biller_categories') // References the 'id' column on 'biller_categories'
            ->onDelete('cascade'); // Defines 'on delete cascade'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bill_payments', function (Blueprint $table) {
            Schema::table('bill_payments', function (Blueprint $table) {
                // $table->dropForeign(['category_id']); // Drops the foreign key
                $table->dropColumn('category_id'); // Drops the 'category_id' column
            });
        });
    }
};
