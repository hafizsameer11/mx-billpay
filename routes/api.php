<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillerProviderController;
use App\Http\Controllers\Api\BillPaymentController;
use App\Http\Controllers\Api\CooperateAccountRequestController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\TransferApiController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserDetailController;
use App\Http\Controllers\Api\VirtualAccountController;
use App\Http\Controllers\PinController;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('webhook/bvn-consent', [AccountController::class, 'handleBvnConsentWebhook']);

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login'])->name('login');

Route::post('auth/resend-otp', [AuthController::class, 'resendotp']);
Route::post('auth/forget-password', [AuthController::class, 'forgetPassword']);
Route::post('auth/reset-password-otp-verification', [AuthController::class, 'verifyResetPasswordOtp']);
Route::post('auth/reset-password', [AuthController::class, 'resetPassword']);
Route::post('auth/user-clear', [AuthController::class, 'tableclear']);
Route::get('/biller-categories-fetch', [BillPaymentController::class, 'fetchBillerCategories']);
Route::get('/biller-items-fetch/{categoryId}/{providerId}', [BillPaymentController::class, 'fetchBillerItems']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
Route::post('accounts/release', [AccountController::class, 'releaseAccount']);
Route::middleware('auth:sanctum')->group(function () {

    Route::post('user-details', [UserDetailController::class, 'detail']);
    Route::post('accounts/individual', [AccountController::class, 'createIndividualAccount']);
    Route::post('accounts/bvn-consent', [AccountController::class, 'requestBvnConsent']);
    Route::post('accountEnquiry', [AccountController::class, 'accountEnquiry']);
    Route::post('accounts/corporate', [AccountController::class, 'createCorporateAccount']);
    Route::post('auth/verify-email', [AuthController::class, 'verifyEmail']);
    //bill payment apis
Route::get('/balance',[VirtualAccountController::class,'balance']);
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
    Route::get('/get-transfer', [TransactionController::class, 'getTransactions']);
    Route::get('/get-billpayments', [TransactionController::class, 'getBillPayment']);
    Route::post('/update-email',[UserController::class,'updateEmail']);
    Route::post('/verify-user',[UserController::class,'verifyUser']);
    Route::post('/update-password',[UserController::class,'updatePassword']);
    Route::get('/check-user-status',[UserController::class,'checkUserStatus']);
    Route::get('/check-bvn-status',[UserController::class,'bvnStatusChecker']);
    Route::get('/unread-notifications',[UserController::class,'unreadNotifjications']);
    Route::get('edit-profile-details', [UserDetailController::class, 'editprofileDetail']);
    Route::get('/mark-all-read', [UserController::class, 'markAllAsRead']);
    Route::get('/transaction-details/{id}', [TransactionController::class, 'transactionDetails']);
    Route::post('/set-pin',[PinController::class,'setPin']);
    Route::post('/verify-pin',[PinController::class,'checkPin']);
    Route::post('/change-pin',[PinController::class,'changePin']);
    Route::get('/fund-account',[VirtualAccountController::class,'fundAccount']);
    Route::post('/set-fcm-token',[UserController::class,'setFcmToken']);
    //routes for statistics
    Route::get('/monthly-stats',[StatisticsController::class,'monthlyStats']);
    Route::get('/yearly-stats',[StatisticsController::class,'yearlyStats']);
    Route::get('/quarterly-stats',[StatisticsController::class,'quarterlyStats']);
    Route::get('/yty-stats',[StatisticsController::class,'ytyStats']);
    Route::get('/slides',[StatisticsController::class,'slides']);
    //Live chat apis
    Route::post('/messages',[MessageController::class,'index']);
    Route::post('/send-message',[MessageController::class,'store']);
});
Route::post('/inwardCreditNotification',[TransferApiController::class,'inwardCreditNotification']);
Route::get('/social-media-links',[UserController::class,'socialMedialinks']);
Route::get('/set-providers',[BillerProviderController::class,'setProviders']);
Route::get('/get-provider/{id}',[BillerProviderController::class,'getProviders']);
//Tranfser Routes
Route::post('/mark-as-read',[UserController::class,'markAsRead']);
Route::get('/getpooldetails', [TransferApiController::class, 'getPoolAccountDetails']);
Route::middleware('auth:sanctum')->post('/analytics', [AnalyticsController::class, 'getAnalyticsData']);
