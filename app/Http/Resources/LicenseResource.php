<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LicenseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'product' => $this->product?->name,
            'expires_at' => $this->expires_at?->toISOString(),
            'seat_limit' => $this->seat_limit,
            'remaining_seats' => $this->remainingSeats(),
            'status' => $this->status,
            'is_valid' => $this->isValid(),
        ];
    }
}
