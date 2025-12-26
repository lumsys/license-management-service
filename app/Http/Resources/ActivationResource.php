<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivationResource extends JsonResource
{
     public function toArray(Request $request): array
    {
        return [
            'instance_id' => $this->instance_id,
            'activated_at' => $this->activated_at?->toISOString(),
            'status' => $this->status ? 'deactivated' : 'active',
        ];
    }
}
