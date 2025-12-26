<?php

namespace App\Services;


use Exception;
use App\Models\Activation;
use App\Models\LicenseKey;
use Log;
use App\Exceptions\NoAvailableSeatsException;
use App\Exceptions\LicenseNotValidException;

class LicenseActivationService
{

    
    public function activate(
    string $licenseKeyValue,
    string $productCode,
    string $instanceId
): Activation {
    $licenseKey = LicenseKey::with('licenses.product', 'licenses.activations')
        ->where('key', $licenseKeyValue)
        ->firstOrFail();

    $license = $licenseKey->licenses
        ->first(fn ($license) =>
            $license->product->code === $productCode &&
            $license->isValid()
        );

    if (!$license) {
            throw new LicenseNotValidException();
        }

        if ($license->remainingSeats() <= 0) {
            throw new NoAvailableSeatsException();
        }

    $activation = Activation::firstOrCreate(
        [
            'license_id' => $license->id,
            'instance_id' => $instanceId,
        ],
        [
            'activated_at' => now(),
        ]
    );

    
    if ($activation->wasRecentlyCreated) {
        Log::channel('licenses')->info('License activated', [
            'license_key' => $licenseKeyValue,
            'license_id' => $license->id,
            'product_code' => $productCode,
            'instance_id' => $instanceId,
            'remaining_seats' => $license->remainingSeats(),
        ]);
    }

    return $activation;
}

    public function deactivate(string $licenseKeyValue, string $instanceId): ?Activation
{
    $licenseKey = LicenseKey::with('licenses.activations')
        ->where('key', $licenseKeyValue)
        ->firstOrFail();

    $activation = $licenseKey->licenses
        ->flatMap(fn ($license) => $license->activations)
        ->firstWhere('instance_id', $instanceId);

    if (!$activation) {
        abort(404, 'Activation not found');
    }

    $activation->delete();

    Log::channel('licenses')->info('License deactivated', [
        'license_key' => $licenseKeyValue,
        'license_id' => $activation->license_id,
        'instance_id' => $instanceId,
    ]);

     return $activation;
}

}
