<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

Route::get('/', function () {
    return [
        'laravel-version' => app()->version(),
    ];
});

Route::prefix('transaction')
    ->group(function () {
        Route::get('/{transactionId}', \App\Http\Controllers\Api\v1\Transaction\GetTransactionController::class);
        Route::get('/{transactionId}/fee', \App\Http\Controllers\Api\v1\Transaction\FeeTransactionController::class);
        Route::get('/{transactionId}/fee-by-split',
            \App\Http\Controllers\Api\v1\Transaction\FeeBySplitTransactionController::class);
        Route::get('/{transactionId}/anticipation-fee',
            \App\Http\Controllers\Api\v1\Transaction\AnticipationFeeTransactionController::class);
        Route::post('/status-changed',
            \App\Http\Controllers\Api\v1\Transaction\StatusChangedTransactionController::class);
        Route::post('/credit-card',
            \App\Http\Controllers\Api\v1\Transaction\CreditCard\CreateTransactionCreditCardController::class);
        Route::post('/pix', \App\Http\Controllers\Api\v1\Transaction\Pix\CreateTransactionPixController::class);
        Route::post('/billet',
            \App\Http\Controllers\Api\v1\Transaction\Billet\CreateTransactionBilletController::class);
        Route::post('/{transactionId}/refund',
            \App\Http\Controllers\Api\v1\Transaction\RefundTransactionController::class);
    });

Route::post('recipients', \App\Http\Controllers\Api\v1\Recipient\CreateRecipientsController::class);

Route::put('recipients/{recipientId}/anticipation-settings',
    \App\Http\Controllers\Api\v1\Recipient\UpdateAnticipationSettingsController::class);

Route::prefix('balance/{recipientId}/{gateway}')
    ->group(function () {
        Route::get('/', \App\Http\Controllers\Api\v1\Balance\GetBalanceController::class);
        Route::post('/transfers', \App\Http\Controllers\Api\v1\Balance\BalanceTransfersController::class);
    });

Route::prefix('customer')
    ->group(function () {
        Route::post('/', \App\Http\Controllers\Api\v1\Customer\CreateController::class);
        Route::post('/associate-credit-card',
            \App\Http\Controllers\Api\v1\Customer\AssociateCreditCardController::class);
        Route::post('/get-by-email', \App\Http\Controllers\Api\v1\Customer\GetCustomerByEmailController::class);
    });
