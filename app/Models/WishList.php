<?php

namespace App\Models;

use App\Models\Admin\Tree;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishList extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'tree_id', 'product_type', 'created_at', 'updated_at', 'created_by', 'updated_by', 'trash', 'status'];
     /*
     * Relations
     */
    
    /**
     * Relationship with Tree model when product_type = 1.
     */
    public function tree()
    {
        return $this->belongsTo(Tree::class, 'tree_id');
    }

    /**
     * Relationship with Product model when product_type = 2.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'tree_id');
    }
}
