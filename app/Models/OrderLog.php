<?php

namespace App\Models;

use App\Models\Admin\Tree;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    //This is Order Items table
    use HasFactory;
    protected $fillable = ['location_id','user_id','order_id', 'duration','tree_name','age','tree_id', 'quantity', 'price', 'created_at', 'updated_at', 'created_by', 'updated_by', 'trash', 'status'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    //Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function tree()
    {
        return $this->belongsTo(Tree::class, 'tree_id', 'id');
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'tree_id', 'id');
    }
}
