<?php

use App\Http\Controllers\AdminAuthenticateController;
use App\Http\Controllers\Api\BillerProviderController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\ApiHandlingController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BillerCategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\faqController;
use App\Http\Controllers\SlideController;
use App\Http\Controllers\SmtpController;
use App\Http\Controllers\SocialMediaController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
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

// Route::get('/', function () {
//     return view('dashboard.index');
// });
Route::get('/clear-cache', function () {
    try {
        // Clear application cache
        Artisan::call('cache:clear');

        // Clear route cache
        Artisan::call('route:clear');

        // Clear config cache
        Artisan::call('config:clear');

        // Clear view cache
        Artisan::call('view:clear');

        // Optional: Rebuild config cache
        // Artisan::call('config:cache');

        return response()->json(['success' => true, 'message' => 'Caches cleared successfully!']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
});
//bill categories
Route::get('/bill-categories', [BillerCategoryController::class, 'index'])->name('category.index');
Route::get('/fetch-biller-categories', [BillerCategoryController::class, 'fetchCategories'])->name('category.fetch');
Route::get('/fetch-biller-item/{categoryName}', [BillerCategoryController::class, 'fetchBillerItemsForCategory'])->name('billitem.fetch');
Route::geT('/show-biller-items', [BillerCategoryController::class, 'showBillerItems'])->name('billeritem.show');
Route::post('item/add-commission', [BillerCategoryController::class, 'addCommission'])->name('item.addCommission');
Route::post('item/bulk-add-commission', [BillerCategoryController::class, 'bulkAddCommission'])->name('item.bulkAddCommission');

Route::get('/fetch-banks', [BankController::class, 'index']);


// admin auth routes
Route::get('/login', [AdminAuthenticateController::class, 'index'])->name('admin.login');
Route::post('/login', [AdminAuthenticateController::class, 'login'])->name('admin.authenticate');

// Route::middleware(['admin','auth'])->group(function () {
    // dashoard routes
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/logout', [AdminAuthenticateController::class, 'logout'])->name('admin.logout');


    // user routes
    Route::get('/fetch-users', [UserController::class, 'index'])->name('user.index');
    Route::get('/edit-user/{id}', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/update-user/{id}', [UserController::class, 'update'])->name('user.update');
    Route::get('/show-user/{id}', [UserController::class, 'show'])->name('user.show');

    // transaction routes
    Route::get('/all-transactions', [TransactionsController::class, 'index'])->name('all.transactions');
    Route::get('/payment-request', [TransactionsController::class, 'pendingPayments'])->name('pending.transactions');
    Route::get('/payment-log', [TransactionsController::class, 'completedPayments'])->name('completed.transactions');

    // bil
    Route::get('/billPayments-transactions', [TransactionsController::class, 'billPayments'])->name('billPayments.transactions');
    Route::get('/billPayments-status', [TransactionsController::class, 'billPaymentsFilter'])->name('billPayments.transactions.filter');
    Route::get('/pending-billPayments-transactions', [TransactionsController::class, 'pendingBillPayments'])->name('pending.billPayments.transactions');
    Route::get('/complete-billPayments-transactions', [TransactionsController::class, 'completeBillPayments'])->name('complete.billPayments.transactions');
    Route::get('/return-billPayments-transactions', [TransactionsController::class, 'returnBillPayments'])->name('return.billPayments.transactions');
    Route::get('/billPayments-transactions-show/{id}', [TransactionsController::class, 'billPaymentsShow'])->name('billPayments.transactions.show');

    // faqs
    Route::get('/faq-category', [faqController::class, 'index'])->name('faq.category');
    Route::post('/faq-category-store', [faqController::class, 'category'])->name('faq.category.store');
    Route::get('/faq-category-edit/{id}', [faqController::class, 'categoryEdit'])->name('faq.category.edit');
    Route::post('/faq-category-update/{id}', [faqController::class, 'categoryupdate'])->name('faq.category.update');
    Route::get('/faq-category-delete/{id}', [faqController::class, 'categoryDelete'])->name('faq.category.delete');

    // answers question
    Route::get('/faqs-add', [faqController::class, 'addFaqs'])->name('faq.addFaqs');
    Route::get('/faqs', [faqController::class, 'faqs'])->name('faq.show');
    Route::post('/faqs-store', [faqController::class, 'storeFaqs'])->name('faq.store');
    Route::get('/edit-faqs/{id}', [faqController::class, 'editFaqs'])->name('faq.edit');
    Route::put('/update-faqs/{id}', [faqController::class, 'updateFaq'])->name('faq.update');
    Route::get('/delete-faqs/{id}', [faqController::class, 'deleteFaqs'])->name('faq.delete');

    // access token api
    Route::get('access-token', [ApiHandlingController::class, 'AccessToken'])->name('AccessToken');
    Route::get('access-token-add', [ApiHandlingController::class, 'addToken'])->name('addToken');
    Route::post('access-token-store', [ApiHandlingController::class, 'storeToken'])->name('storeToken');
    Route::get('edit-access-token/{id}', [ApiHandlingController::class, 'editAccessToken'])->name('editAccessToken');
    Route::put('edit-access-token/{id}', [ApiHandlingController::class, 'updateToken'])->name('updateToken');
    Route::get('delete-access-token/{id}', [ApiHandlingController::class, 'deleteToken'])->name('deleteToken');


    // service provider api
    Route::get('/service-providers', [BillerProviderController::class, 'index'])->name('service.provider');
    Route::post('/service-providers-logo', [BillerProviderController::class, 'logoStore'])->name('service.provider.logo');


    // update
    Route::post('/category/store-or-update', [BillerCategoryController::class, 'storeOrUpdateCategoryTitle'])->name('category.storeOrUpdate');
    Route::post('/description/store-or-update', [BillerCategoryController::class, 'storeOrUpdateDescription'])->name('description.storeOrUpdate');
    Route::post('/selectTitle/store-or-update', [BillerCategoryController::class, 'storeOrUpdateSelectTitle'])->name('selectTitle.storeOrUpdate');

    Route::post('/provider/store-or-update', [BillerCategoryController::class, 'storeOrUpdateProvider'])->name('provider.storeOrUpdate');
    Route::post('/provider-description/store-or-update', [BillerCategoryController::class, 'storeOrUpdateProviderDescription'])->name('provider.description.storeOrUpdate');
    Route::post('/provider-select-title/store-or-update', [BillerCategoryController::class, 'storeOrUpdateProviderSelectTitle'])->name('provider.selectTitle.storeOrUpdate');

    Route::get('/social-media', [SocialMediaController::class, 'index'])->name('social.media.index');
    Route::get('/social-media-create', [SocialMediaController::class, 'create'])->name('social.media.create');
    Route::post('/social-media-store', [SocialMediaController::class, 'store'])->name('social.media.store');
    Route::get('/social-media-edit/{id}', [SocialMediaController::class, 'edit'])->name('social.media.edit');
    Route::put('/social-media-update/{id}', [SocialMediaController::class, 'update'])->name('social.media.update');
    Route::get('/social-media-delete/{id}', [SocialMediaController::class, 'delete'])->name('social.media.delete');
// });

// Route::get('/bill-categories', [BillerCategoryController::class, 'index'])->name('category.index');
// Route::get('/fetch-biller-categories', [BillerCategoryController::class, 'fetchCategories'])->name('category.fetch');
// Route::get('/fetch-biller-item/{categoryName}', [BillerCategoryController::class, 'fetchBillerItemsForCategory'])->name('billitem.fetch');
// Route::geT('/show-biller-items', [BillerCategoryController::class, 'showBillerItems'])->name('billeritem.show');
// Route::post('item/add-commission', [BillerCategoryController::class, 'addCommission'])->name('item.addCommission');
// Route::post('item/bulk-add-commission', [BillerCategoryController::class, 'bulkAddCommission'])->name('item.bulkAddCommission');

// Route::get('/fetch-banks', [BankController::class, 'index']);
// Route::get('/test-account-release', function () {
//     return view('account-release-test');
// });


//routes for smtp
Route::get('/set-smtp', [SmtpController::class, 'index'])->name('smtp.index');
Route::get('/add-smtp', [SmtpController::class, 'create'])->name('smtp.create');
Route::get('/edit-smtp/{id}', [SmtpController::class, 'edit'])->name('smtp.edit');

Route::post('/store-smtp', [SmtpController::class, 'store'])->name('smtp.store');
Route::post('/update-smtp', [SmtpController::class, 'update'])->name('smtp.update');
Route::get('/dipatchevent', [TransferController::class, 'dispatchevent']);
Route::resource('slides', SlideController::class);
use Pusher\Pusher;
Route::get('/chat',[ChatController::class,'index'])->name('chat.index');
Route::get('/chat/{id}', [ChatController::class, 'show'])->name('chat.show');
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
