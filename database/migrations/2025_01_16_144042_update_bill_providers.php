<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bill_providers', function (Blueprint $table) {
            $table->double('fixed_comission')->default(0);
            $table->double('percentage_comission')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bill_providers', function (Blueprint $table) {
            //
        });
    }
};
