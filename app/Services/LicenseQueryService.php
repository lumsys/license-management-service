<?php

namespace App\Services;

use App\Models\LicenseKey;
use Illuminate\Pagination\LengthAwarePaginator;

class LicenseQueryService
{

    public function getStatus(LicenseKey $licenseKey): array
{
    $licenses = $licenseKey->licenses ?? collect(); 

    return [
        'is_valid' => $licenses->contains(fn($license) => $license->isValid()),
        'licenses' => $licenses->map(fn($license) => [
            'product' => $license->product?->name, 
            'status' => $license->status,
            'expires_at' => $license->expires_at?->toISOString(),
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
