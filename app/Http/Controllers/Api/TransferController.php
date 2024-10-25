<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function fetchBanks()
    {
        $banks = Bank::all()->makeHidden(['created_at', 'updated_at']); // Exclude timestamps
        return response()->json(['status' => 'success', 'data' => $banks]);
    }
}
