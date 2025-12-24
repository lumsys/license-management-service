<?php

namespace App\Services;

use App\Models\Activation;
use App\Models\LicenseKey;

class LicenseActivationService
{
    public function activate(string $licenseKeyValue, string $productCode, string $instanceId): void
    {
        $licenseKey = LicenseKey::with('licenses.product', 'licenses.activations')
            ->where('key', $licenseKeyValue)
            ->firstOrFail();

        $license = $licenseKey->licenses
            ->first(fn ($license) =>
                $license->product->code === $productCode &&
                $license->isValid()
            );

        if (!$license) {
            abort(403, 'License not valid');
        }

        if ($license->remainingSeats() <= 0) {
            abort(403, 'No available seats');
        }

        Activation::firstOrCreate(
            [
                'license_id' => $license->id,
                'instance_id' => $instanceId,
            ],
            [
                'activated_at' => now(),
            ]
        );
    }

    public function deactivate(string $licenseKeyValue, string $instanceId): void
    {
        $licenseKey = LicenseKey::with('licenses.activations')
            ->where('key', $licenseKeyValue)
            ->firstOrFail();

        $activation = $licenseKey->licenses
            ->flatMap(fn ($license) => $license->activations)
            ->firstWhere('instance_id', $instanceId);

        if ($activation) {
            $activation->delete(); 
        } else {
            abort(404, 'Activation not found');
        }
    }
}
