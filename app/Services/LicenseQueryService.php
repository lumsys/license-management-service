<?php

namespace App\Services;

use App\Models\LicenseKey;

class LicenseQueryService
{
    public function getStatus(LicenseKey $licenseKey): array
    {
        return [
            'is_valid' => $licenseKey->licenses->contains(fn ($license) => $license->isValid()),
            'licenses' => $licenseKey->licenses->map(fn ($license) => [
                'product' => $license->product->name,
                'status' => $license->status,
                'expires_at' => $license->expires_at,
                'seat_limit' => $license->seat_limit,
                'remaining_seats' => $license->remainingSeats(),
            ]),
        ];
    }

     public function listByEmail(
        string $email,
        ?int $brandId = null
    ): LengthAwarePaginator {
        $query = LicenseKey::with(['licenses.product', 'brand'])
            ->where('customer_email', $email);

        if ($brandId !== null) {
            $query->where('brand_id', $brandId);
        }

        return $query->paginate(10);
    }
}
