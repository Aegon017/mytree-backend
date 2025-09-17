<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAssignment extends Model
{
    use HasFactory;
    protected $fillable = ['order_id', 'admin_id', 'role_id', 'assigned_at', 'created_at', 'updated_at'];
    
    //Relations
 // Define the inverse relationship with user tree relations
    public function userTreeRelations()
    {
        return $this->belongsTo(UserTreeRelation::class, 'order_id', 'order_id');
    }
}
