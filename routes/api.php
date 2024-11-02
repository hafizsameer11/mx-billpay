<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillPaymentController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\TransferApiController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserDetailController;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('webhook/bvn-consent', [AccountController::class, 'handleBvnConsentWebhook']);

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login'])->name('login');

Route::post('auth/resend-otp', [AuthController::class, 'resendotp']);
Route::post('auth/forget-password', [AuthController::class, 'forgetPassword']);
Route::post('auth/reset-password-otp-verification', [AuthController::class, 'verifyResetPasswordOtp']);
Route::post('auth/reset-password', [AuthController::class, 'resetPassword']);
Route::post('auth/user-clear', [AuthController::class, 'tableclear']);
Route::get('/biller-categories-fetch', [BillPaymentController::class, 'fetchBillerCategories']);
Route::get('/biller-items-fetch/{id}', [BillPaymentController::class, 'fetchBillerItems']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
Route::post('accounts/release', [AccountController::class, 'releaseAccount']);
Route::middleware('auth:sanctum')->group(function () {

    Route::post('user-details', [UserDetailController::class, 'detail']);
    Route::post('accounts/individual', [AccountController::class, 'createIndividualAccount']);
    Route::post('accounts/bvn-consent', [AccountController::class, 'requestBvnConsent']);
    Route::post('accountEnquiry', [AccountController::class, 'accountEnquiry']);
    Route::post('client/corporate', [AccountController::class, 'createCor` porateAccount']);
    Route::post('auth/verify-email', [AuthController::class, 'verifyEmail']);
    //bill payment apis

    Route::post('/Validate-Customer', [BillPaymentController::class, 'validateCustomer']);
    Route::post('/payBills', [BillPaymentController::class, 'payBills']);
    Route::get('/transaction-Status', [BillPaymentController::class, 'transactionStatus']);
    Route::post('/recepient-details', [TransferApiController::class, 'beneficiaryEnquiry']);
    Route::post('/transfer', [TransferApiController::class, 'transferFunds']);
    Route::get('/biller-Item-details/{id}', [BillPaymentController::class, 'fetchbillerItemDetails']);
    Route::get('fetch-banks', [TransferController::class, 'fetchBanks']);
    //profile end points
    Route::post('update-profile', [UserDetailController::class, 'updateProfile']);
    Route::get('profile-detail', [UserDetailController::class, 'profileDetail']);
    // Route::get('/transaction-details',[]);
    Route::post('/get-transfer', [TransactionController::class, 'getTransactions']);
    Route::post('/get-billpayments', [TransactionController::class, 'getBillPayment']);
    Route::post('/update-email',[UserController::class,'updateEmail']);
    Route::post('/update-password',[UserController::class,'updatePassword']);
    Route::get('/unread-notifications',[UserController::class,'unreadNotifications']);

});
Route::post('/inwardCreditNotification',[TransferApiController::class,'inwardCreditNotification']);
//Tranfser Routes
Route::get('/getpooldetails', [TransferApiController::class, 'getPoolAccountDetails']);
