<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    use HasFactory;

    protected $fillable = [
       'user_id', 'name', 'address', 'city', 'area', 'pincode', 'mobile_number', 'default', 'created_at', 'updated_at' 
    ];
}
