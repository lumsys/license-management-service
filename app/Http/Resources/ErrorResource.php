<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ErrorResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'error' => $this->resource instanceof \Exception
                ? $this->resource->getMessage()
                : $this->resource,
            'code' => $this->resource instanceof \Exception
                ? $this->resource->getCode()
                : null,
        ];
    }
}
