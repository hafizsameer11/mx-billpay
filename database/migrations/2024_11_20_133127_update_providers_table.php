<?php

use App\Models\Tier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    protected $tierId;
    // public function __construct()
    // {
    //     // $this->tierId = DB::table('tier')->max('id'); get first id first id
    //     $this->tierId = Tier::first()->id ?? 0;
    // }
    public function up(): void
    {
        Schema::table('bill_providers', function (Blueprint $table) {
            $table->unsignedBigInteger('biller_category_id');
            $table->foreign('biller_category_id')->references('id')->on('biller_categories')->onDelete('cascade');
        });
        Schema::table('accounts', function (Blueprint $table) {


            $table->unsignedBigInteger('tier_id')->default($this->tierId); // Set default value
            $table->foreign('tier_id')->references('id')->on('tiers')->onDelete('cascade'); // Add foreign key constraint

        });
        Schema::table('kyc_documents', function (Blueprint $table) {

            $table->string('title')->nullable();
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
