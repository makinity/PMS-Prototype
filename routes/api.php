<?php

use App\Http\Controllers\Api\PmsProviderController;
use App\Http\Middleware\EnsureValidPmsApiToken;
use Illuminate\Support\Facades\Route;

Route::prefix('pms/v1')
    ->middleware(EnsureValidPmsApiToken::class)
    ->group(function () {
        Route::get('/employees', [PmsProviderController::class, 'employees']);
        Route::get('/offices', [PmsProviderController::class, 'offices']);
        Route::get('/performance-periods', [PmsProviderController::class, 'performancePeriods']);
    });
