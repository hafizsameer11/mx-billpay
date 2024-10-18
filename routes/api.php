<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
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
Route::post('auth/verify-email',[AuthController::class,'verifyEmail']);
Route::post('auth/resend-otp',[AuthController::class,'resendotp']);
Route::post('auth/forget-password',[AuthController::class,'forgetPassword']);
Route::post('auth/reset-password-otp-verification',[AuthController::class,'verifyResetPasswordOtp']);
Route::post('auth/reset-password',[AuthController::class,'resetPassword']);
Route::post('auth/user-clear',[AuthController::class,'tableclear']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
// Route::middleware('auth:sanctum')->group(function () {
    Route::post('accounts/individual', [AccountController::class, 'createIndividualAccount']);
    Route::post('accounts/bvn-consent', [AccountController::class, 'requestBvnConsent']);
    Route::post('accounts/release', [AccountController::class, 'releaseAccount']);
// });
