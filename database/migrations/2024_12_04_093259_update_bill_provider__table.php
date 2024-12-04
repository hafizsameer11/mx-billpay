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
        Schema::table('bill_providers', function (Blueprint $table) {
            //
            $table->string('select_title')->nullable();
        });
        Schema::table('biller_categories', function (Blueprint $table) {
            //
            $table->string('select_title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bill_providers', function (Blueprint $table) {
          $table->dropColumn('select_title');
        });
    }
};
