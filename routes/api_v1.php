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
    Route::patch('/licenses/{license}/status', [LicenseProvisionController::class, 'updateStatus']);
    Route::patch('/licenses/{license}/renew', [LicenseProvisionController::class, 'renew']);
});

Route::post('/licenses/activate', [LicenseActivationController::class, 'activate']);
Route::post('/licenses/deactivate', [LicenseActivationController::class, 'deactivate']);

Route::get('/licenses/{key}', [LicenseStatusController::class, 'show']);
