<?php

use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('form', [CheckoutController::class, 'form']);
Route::post('checkout', [CheckoutController::class, 'checkout'])->middleware(['service_token']);
Route::get('free', [CheckoutController::class, 'free'])->middleware(['service_token']);
Route::get('response/{response}', [CheckoutController::class, 'response']);
Route::get('success', [CheckoutController::class, 'success']);
Route::get('cancelled', [CheckoutController::class, 'cancelled']);
