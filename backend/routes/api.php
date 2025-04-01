<?php

use App\Http\Controllers\Api\V1\CustomersController;
use App\Http\Controllers\Api\V1\PaymentsController;
use App\Http\Controllers\Api\V1\ThankYouController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->name('api.')->group(function () {
    Route::apiResource('customers', CustomersController::class);

    Route::post('/payments/process', [PaymentsController::class, 'process'])->name('payments.process');

    Route::get('/thank-you/{payment}', [ThankYouController::class, 'show'])->name('thank-you.show');
});
