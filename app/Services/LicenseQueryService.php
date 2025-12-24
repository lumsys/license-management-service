<?php

namespace App\Services;

use App\Models\LicenseKey;

class LicenseQueryService
{
    public function getStatus(LicenseKey $licenseKey): array
    {
        return [
            'is_valid' => $licenseKey->licenses->contains(
                fn ($license) => $license->isValid()
            ),
            'licenses' => $licenseKey->licenses,
        ];
    }
}
