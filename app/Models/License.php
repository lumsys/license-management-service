<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{

    use HasFactory;

    protected $fillable = [
        'license_key_id','product_id','status','expires_at','seat_limit'
    ];

    protected $casts = ['expires_at' => 'date'];

    public function licenseKey() { return $this->belongsTo(LicenseKey::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function activations() { return $this->hasMany(Activation::class); }

    public function isValid(): bool
    {
        return $this->status === 'valid' && $this->expires_at->isFuture();
    }

    public function remainingSeats(): int
    {
        return $this->seat_limit - $this->activations()->count();
    }

}

