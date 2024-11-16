<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CooperateAccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CooperateAccountRequestController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'companyName' => 'required|string|max:255',
            'companyAddress' => 'required|string|max:255',
            'rcNumber' => 'required|string|max:100',
            'cacCertificate' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'businessAddressVerification' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'directorIdVerification' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'directorNiNnumber' => 'nullable|string|max:20',
            'directorBvnNumber' => 'nullable|string|max:20',
            'directorDob' => 'nullable|date',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 400);
        }
        $cacCertificatePath = $request->hasFile('cacCertificate')
            ? $request->file('cacCertificate')->store('uploads/cac_certificates', 'public')
            : null;

        $businessAddressPath = $request->hasFile('businessAddressVerification')
            ? $request->file('businessAddressVerification')->store('uploads/business_verifications', 'public')
            : null;

        $directorIdPath = $request->hasFile('directorIdVerification')
            ? $request->file('directorIdVerification')->store('uploads/director_ids', 'public')
            : null;
        $companyDetail = CooperateAccountRequest::create([
            'companyName' => $request->input('companyName'),
            'companyAddress' => $request->input('companyAddress'),
            'rcNumber' => $request->input('rcNumber'),
            'cacCertificate' => $cacCertificatePath,
            'businessAddressVerification' => $businessAddressPath,
            'directorIdVerification' => $directorIdPath,
            'directorNiNnumber' => $request->input('directorNiNnumber'),
            'directorBvnNumber' => $request->input('directorBvnNumber'),
            'directorDob' => $request->input('directorDob'),
        ]);
        //check if data is added in database and return response
        if ($companyDetail) {
            return response()->json([
                'status' => 'success',
                'message' => 'Your Request is Submitted',
                'data' => $companyDetail,
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add company details',
            ], 400);
        }
    }
}
