<?php

use Illuminate\Support\Facades\Route;

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
    return response()->json(['status' => 200, 'message' => 'Gateway OK']);
});

Route::get('/health', function () {
    return response()->json([
        'queue_size' => rescue(fn () => Queue::size(), 0),
        'external_communication' => rescue(function () {
            (new \GuzzleHttp\Client())->get('https://www.google.com');

            return true;
        }, false),
        'database' => rescue(fn () => (bool) DB::select('SELECT 1'), false),
    ]);
});
