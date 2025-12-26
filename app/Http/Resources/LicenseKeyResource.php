<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LicenseKeyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'license_key' => $this->key,
            'customer_email' => $this->customer_email,
            'brand' => [
                'id' => $this->brand?->id,
                'name' => $this->brand?->name,
                'slug' => $this->brand?->slug,
            ],
            'licenses' => LicenseResource::collection($this->licenses ?? collect()),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
