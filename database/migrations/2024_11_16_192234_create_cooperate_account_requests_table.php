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
        Schema::create('cooperate_account_requests', function (Blueprint $table) {
            $table->id();
            $table->string('companyName'); // Name of the company
            $table->string('companyAddress'); // Address of the company
            $table->string('rcNumber'); // RC Number
            $table->string('cacCertificate')->nullable(); // Path to CAC certificate file
            $table->string('businessAddressVerification')->nullable(); // File path for bank statement or utility bill
            $table->string('directorIdVerification')->nullable(); // Path to director's ID document
            $table->string('directorNiNnumber')->nullable(); // Director's NIN number
            $table->string('directorBvnNumber')->nullable(); // Director's BVN number
            $table->date('directorDob')->nullable(); // Director's Date of Birth
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cooperate_account_requests');
    }
};
