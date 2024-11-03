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
        Schema::create('bvn_statuc_recorders', function (Blueprint $table) {
            $table->id();
            // $table->string('bvn');
            //userId
            $table->foreignId('userId')->constrained()->onDelete('cascade');
            //status enum checked unchecked
            $table->enum('status', ['checked', 'unchecked'])->default('unchecked');
            // $table->string('status');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bvn_statuc_recorders');
    }
};