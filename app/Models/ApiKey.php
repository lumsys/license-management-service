<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
     use HasFactory;

    protected $fillable = [
        'key',
        'brand_id',
        'role',
        'is_active',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}