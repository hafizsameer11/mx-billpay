<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {

            $tierId = DB::table('tiers')->first();
            $tierId=$tierId->id;

            $table->foreignId('tier_id')->constrained()->onDelete('cascade')->default($tierId);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
//remove foreign key
$table->dropForeign('accounts_tier_id_foreign');
$table->dropColumn('tier_id');


        });
    }
};
