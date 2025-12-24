<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $fillable = ['brand_id','key'];
    public function brand() { return $this->belongsTo(Brand::class); }
}
