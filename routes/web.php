<?php

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

Route::get('/fetch-biller-categories', [BillerCategoryController::class, 'fetchCategories']);
Route::get('/pusher-log',function(){
return view('welcome');
}
);
