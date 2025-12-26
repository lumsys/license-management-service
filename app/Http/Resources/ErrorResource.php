<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Throwable;

class ErrorResource extends JsonResource
{
    public function toArray($request): array
    {
        if ($this->resource instanceof Throwable) {
            return [
                'error' => $this->resource->getMessage(),
                'code' => $this->resource->getCode() ?: 400,
               // 'trace' => $this->resource->getTraceAsString(),
            ];
        }

        return [
            'error' => $this->resource,
            'code' => null,
           // 'trace' => null,
        ];
    }
}
