<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activation extends Model
{
    protected $fillable = ['license_id','instance_id','activated_at'];
    protected $casts = ['activated_at' => 'datetime'];
    public function license() { return $this->belongsTo(License::class); }
}
