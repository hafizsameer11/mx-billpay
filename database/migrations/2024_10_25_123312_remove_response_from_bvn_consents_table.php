<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('bvn_consents', function (Blueprint $table) {
            $table->dropColumn('response'); // Remove the response column
        });
    }

    public function down()
    {
        Schema::table('bvn_consents', function (Blueprint $table) {
            $table->json('response')->nullable(); // Add the response column back in case of rollback
        });
    }
};
