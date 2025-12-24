<?php

use App\Http\Controllers\API\{
    LicenseProvisionController,
    LicenseActivationController,
    LicenseStatusController,
    BrandLicenseQueryController
};

Route::middleware('brand.auth')->group(function () {
    Route::post('/brands/{brand}/licenses', [LicenseProvisionController::class, 'store']);
    Route::get('/brands/licenses', [BrandLicenseQueryController::class, 'index']);
    Route::patch('/licenses/{license}/status', [LicenseController::class, 'updateStatus']);
    Route::patch('/licenses/{license}/renew', [LicenseController::class, 'renew']);
});

Route::post('/licenses/activate', [LicenseActivationController::class, 'activate'])
    ->name('licenses.activate');

Route::get('/licenses/{key}', [LicenseStatusController::class, 'show'])
    ->name('licenses.status');
