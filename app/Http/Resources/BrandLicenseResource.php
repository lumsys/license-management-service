<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandLicenseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'brand'=>$this->brand->name,
            'license_key'=>$this->key,
            'customer_email'=>$this->customer_email,
            'licenses'=>LicenseResource::collection($this->licenses)
        ];
    }
}

