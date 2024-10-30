<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\BillerCategoryController;
use App\Http\Controllers\TransferController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('dashboard.index');
});

//bill categories
Route::get('/bill-categories', [BillerCategoryController::class, 'index'])->name('category.index');
Route::get('/fetch-biller-categories', [BillerCategoryController::class, 'fetchCategories'])->name('category.fetch');
Route::get('/fetch-biller-item/{categoryName}', [BillerCategoryController::class, 'fetchBillerItemsForCategory'])->name('billitem.fetch');
Route::geT('/show-biller-items', [BillerCategoryController::class, 'showBillerItems'])->name('billeritem.show');
Route::post('item/add-commission', [BillerCategoryController::class, 'addCommission'])->name('item.addCommission');
Route::post('item/bulk-add-commission', [BillerCategoryController::class, 'bulkAddCommission'])->name('item.bulkAddCommission');

Route::get('/fetch-banks', [BankController::class, 'index']);
Route::get('/test-account-release', function () {
    return view('account-release-test');
});
Route::get('/dipatchevent', [TransferController::class, 'dispatchevent']);

use Pusher\Pusher;

Route::get('/test-pusher/{userId}', function ($userId) {
    $pusher = new Pusher(
        env('PUSHER_APP_KEY'),
        env('PUSHER_APP_SECRET'),
        env('PUSHER_APP_ID'),
        [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true,
        ]
    );

    $data = ['message' => 'Testing direct Pusher integration for user ' . $userId];
    $pusher->trigger("user.{$userId}", 'account.released', $data);

    return "Direct Pusher test triggered for user {$userId}";
})->name('test-pusher');
