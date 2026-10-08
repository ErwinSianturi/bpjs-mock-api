<?php

use App\Http\Controllers\Api\BpjsMockController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Prefix: /api/v1/mock-bpjs and /api/hospitals
| Supports both GET and POST requests.
|
*/

// Endpoint umum mengambil daftar dan detail rumah sakit dari database
Route::get('/hospitals', [BpjsMockController::class, 'hospitals']);
Route::get('/hospitals/{id}', [BpjsMockController::class, 'showHospital']);

// Endpoint tools simulasi BPJS
Route::prefix('v1/mock-bpjs')->group(function () {
    Route::match(['get', 'post'], '/check-bpjs', [BpjsMockController::class, 'checkBpjs']);
    Route::match(['get', 'post'], '/referral-status', [BpjsMockController::class, 'referralStatus']);
    Route::match(['get', 'post'], '/hospitals', [BpjsMockController::class, 'hospitals']);
    Route::match(['get', 'post'], '/specialists', [BpjsMockController::class, 'specialists']);
    Route::match(['get', 'post'], '/search-web', [BpjsMockController::class, 'searchWeb']);
});
