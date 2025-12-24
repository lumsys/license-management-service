<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LicenseResource extends JsonResource
{
    // public function toArray($request)
    // {
    //     return [
    //         'product'=>$this->product->code,
    //         'status'=>$this->status,
    //         'expires_at'=>$this->expires_at->toDateString(),
    //         'seat_limit'=>$this->seat_limit,
    //         'remaining_seats'=>$this->remainingSeats(),
    //         '_links'=>[
    //             'self'=>route('licenses.status',$this->licenseKey->key),
    //             'activate'=>route('licenses.activate')
    //         ]
    //     ];
    // }

    public function toArray($request): array
    {
        return [
            'license_key' => $this->key,
            'is_valid' => $this->licenses->contains(fn($license) => $license->isValid()),
            'licenses' => $this->licenses->map(fn($license) => [
                'product' => $license->product->name,
                'expires_at' => $license->expires_at?->toISOString(),
                'seat_limit' => $license->seat_limit,
                'remaining_seats' => $license->remainingSeats(),
                'status' => $license->status,
            ]),
        ];
    }
}
