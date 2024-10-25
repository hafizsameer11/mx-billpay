<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\BillerCategoryController;
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
Route::get('/bill-categories',[BillerCategoryController::class,'index'])->name('category.index');
Route::get('/fetch-biller-categories', [BillerCategoryController::class, 'fetchCategories'])->name('category.fetch');
Route::get('/fetch-biller-item/{categoryName}',[BillerCategoryController::class,'fetchBillerItemsForCategory'])->name('billitem.fetch');
Route::geT('/show-biller-items',[BillerCategoryController::class,'showBillerItems'])->name('billeritem.show');
Route::post('item/add-commission', [BillerCategoryController::class, 'addCommission'])->name('item.addCommission');
Route::post('item/bulk-add-commission', [BillerCategoryController::class, 'bulkAddCommission'])->name('item.bulkAddCommission');

Route::get('/fetch-banks',[BankController::class,'index']);
