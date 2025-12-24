<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LicenseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'product'=>$this->product->code,
            'status'=>$this->status,
            'expires_at'=>$this->expires_at->toDateString(),
            'seat_limit'=>$this->seat_limit,
            'remaining_seats'=>$this->remainingSeats(),
            '_links'=>[
                'self'=>route('licenses.status',$this->licenseKey->key),
                'activate'=>route('licenses.activate')
            ]
        ];
    }
}
