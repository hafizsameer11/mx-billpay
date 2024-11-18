<?php

use App\Models\Tier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    protected $tierId ;
    public function __construct(){
        // $this->tierId = DB::table('tier')->max('id'); get first id first id
        $this->tierId=Tier::first()->id;
    }
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {


            $table->foreignId('tier_id')->constrained()->onDelete('cascade')->default($this->tierId);
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
