<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseKey extends Model
{
    use HasFactory;

    protected $fillable = ['brand_id', 'key', 'customer_email'];

    public function brand() { return $this->belongsTo(Brand::class); }
    public function licenses() { return $this->hasMany(License::class); }
}

