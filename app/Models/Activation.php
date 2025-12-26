<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activation extends Model
{

    use SoftDeletes;
    protected $fillable = ['license_id','instance_id','activated_at', 'deleted_at'];
    protected $casts = ['activated_at' => 'datetime'];
    public function license() { return $this->belongsTo(License::class); }
}
