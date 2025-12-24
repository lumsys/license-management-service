<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LicenseStatusResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'license_key'=>$this->key,
            'is_valid'=>$this->licenses->contains(fn($l)=>$l->isValid()),
            'entitlements'=>LicenseResource::collection($this->licenses)
        ];
    }
}
